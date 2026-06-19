<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\Mutation;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    /**
     * Approve and weigh a pending deposit.
     */
    public function approveDeposit(Deposit $deposit, array $itemsData, int $validatorId): void
    {
        DB::transaction(function () use ($deposit, $itemsData, $validatorId) {
            // Lock the deposit record to prevent concurrent updates
            $deposit = Deposit::where('id', $deposit->id)->lockForUpdate()->firstOrFail();

            if ($deposit->status !== 'pending') {
                throw new Exception('Deposit ini sudah tidak berstatus pending.');
            }

            $weightTotal = 0;
            $totalPrice = 0;

            foreach ($itemsData as $itemData) {
                $item = $deposit->items()->where('id', $itemData['id'])->first();
                if ($item) {
                    $item->weight = $itemData['weight'];
                    // Recalculate item total price
                    $item->total_price = $item->weight * $item->price_per_unit;
                    $item->save();

                    $weightTotal += $item->weight;
                    $totalPrice += $item->total_price;
                }
            }

            // Update deposit
            $deposit->weight_total = $weightTotal;
            $deposit->total_price = $totalPrice;
            $deposit->status = 'approved';
            $deposit->validated_by = $validatorId;
            $deposit->save();

            $nasabah = User::where('id', $deposit->user_id)->lockForUpdate()->firstOrFail();
            $balanceBefore = $nasabah->saldo;
            $balanceAfter = $balanceBefore;

            // If it is NOT a donation, we credit the user's saldo
            if (!$deposit->is_donation) {
                $nasabah->saldo += $totalPrice;
                $nasabah->save();
                $balanceAfter = $nasabah->saldo;

                // Record Ledger Mutation (only if balance actually changes or is credited)
                Mutation::create([
                    'user_id' => $nasabah->id,
                    'type' => 'kredit',
                    'amount' => $totalPrice,
                    'sourceable_id' => $deposit->id,
                    'sourceable_type' => Deposit::class,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                ]);
            }

            // Record Activity Log (Audit Trail)
            $validator = User::find($validatorId);
            $donationText = $deposit->is_donation ? ' (Sebagai Donasi)' : '';
            $categoryText = $deposit->donation_category 
                ? ' - Kategori: ' . ($deposit->donation_category === 'umum' ? 'Sampah Umum' : 'Sampah Donasi') 
                : '';
            ActivityLog::create([
                'user_id' => $validatorId,
                'action' => 'approve_deposit',
                'description' => "{$validator->name} menyetujui setoran #{$deposit->id} milik nasabah {$nasabah->name}{$donationText}{$categoryText} dengan total berat {$weightTotal} kg/L dan total nilai Rp " . number_format($totalPrice, 0, ',', '.'),
            ]);
        });
    }

    /**
     * Reject a pending deposit.
     */
    public function rejectDeposit(Deposit $deposit, int $validatorId): void
    {
        DB::transaction(function () use ($deposit, $validatorId) {
            $deposit = Deposit::where('id', $deposit->id)->lockForUpdate()->firstOrFail();

            if ($deposit->status === 'approved') {
                // If it was already approved (rollback scenario), deduct the balance if it wasn't a donation
                if (!$deposit->is_donation) {
                    $nasabah = User::where('id', $deposit->user_id)->lockForUpdate()->firstOrFail();
                    $balanceBefore = $nasabah->saldo;
                    $nasabah->saldo -= $deposit->total_price;
                    $nasabah->save();
                    $balanceAfter = $nasabah->saldo;

                    // Record Ledger Mutation rollback (debit)
                    Mutation::create([
                        'user_id' => $nasabah->id,
                        'type' => 'debit',
                        'amount' => $deposit->total_price,
                        'sourceable_id' => $deposit->id,
                        'sourceable_type' => Deposit::class,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $balanceAfter,
                    ]);
                }
            }

            $deposit->status = 'rejected';
            $deposit->validated_by = $validatorId;
            $deposit->save();

            // Record Activity Log
            $validator = User::find($validatorId);
            $nasabah = $deposit->user;
            ActivityLog::create([
                'user_id' => $validatorId,
                'action' => 'reject_deposit',
                'description' => "{$validator->name} menolak setoran #{$deposit->id} milik nasabah {$nasabah->name}",
            ]);
        });
    }

    /**
     * Approve a pending withdrawal.
     * Business rules:
     * - Admin fee Rp2.500 for non-BTN transfers
     * - No fee for cash (tunai) or BTN transfers
     */
    public function approveWithdrawal(Withdrawal $withdrawal, int $validatorId): void
    {
        DB::transaction(function () use ($withdrawal, $validatorId) {
            $withdrawal = Withdrawal::where('id', $withdrawal->id)->lockForUpdate()->firstOrFail();

            if ($withdrawal->status !== 'pending') {
                throw new Exception('Penarikan ini sudah tidak berstatus pending.');
            }

            // Lock the user record to prevent race conditions on balance deduction
            $nasabah = User::where('id', $withdrawal->user_id)->lockForUpdate()->firstOrFail();

            // Calculate admin fee
            $adminFee = 0;
            if ($withdrawal->withdrawal_method === 'transfer_bank') {
                $bankName = strtolower($withdrawal->bank_name);
                if (!str_contains($bankName, 'btn') && $withdrawal->bank_type !== 'btn') {
                    $adminFee = 2500; // Rp 2.500 for non-BTN
                }
            }
            // No fee for cash (tunai) or BTN transfer

            $totalDeduction = $withdrawal->amount + $adminFee;

            if ($nasabah->saldo < $totalDeduction) {
                throw new Exception('Saldo nasabah tidak mencukupi untuk melakukan penarikan ini. Total pemotongan: Rp ' . number_format($totalDeduction, 0, ',', '.'));
            }

            $balanceBefore = $nasabah->saldo;
            // Deduct balance (amount + admin fee)
            $nasabah->saldo -= $totalDeduction;
            $nasabah->save();
            $balanceAfter = $nasabah->saldo;

            // Update withdrawal details
            $withdrawal->admin_fee = $adminFee;
            $withdrawal->status = 'approved';
            $withdrawal->validated_by = $validatorId;
            $withdrawal->save();

            // Create withdrawal history record
            $withdrawal->history()->create([
                'status' => 'approved',
                'processed_by' => $validatorId,
                'processed_at' => now(),
                'notes' => $adminFee > 0 ? 'Biaya admin Rp 2.500 (bank non-BTN)' : 'Tanpa biaya admin',
            ]);

            // Record Ledger Mutation (debit)
            Mutation::create([
                'user_id' => $nasabah->id,
                'type' => 'debit',
                'amount' => $totalDeduction,
                'sourceable_id' => $withdrawal->id,
                'sourceable_type' => Withdrawal::class,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);

            // Record Activity Log
            $validator = User::find($validatorId);
            $methodLabel = $withdrawal->withdrawal_method === 'tunai' ? 'Tunai' : 'Transfer Bank';
            ActivityLog::create([
                'user_id' => $validatorId,
                'action' => 'approve_withdrawal',
                'description' => "{$validator->name} menyetujui penarikan saldo #{$withdrawal->id} milik nasabah {$nasabah->name} sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.') . " (metode: {$methodLabel})" . ($adminFee > 0 ? " dengan biaya admin Rp 2.500" : ""),
            ]);
        });
    }

    /**
     * Reject a pending withdrawal.
     */
    public function rejectWithdrawal(Withdrawal $withdrawal, int $validatorId): void
    {
        DB::transaction(function () use ($withdrawal, $validatorId) {
            $withdrawal = Withdrawal::where('id', $withdrawal->id)->lockForUpdate()->firstOrFail();

            if ($withdrawal->status !== 'pending') {
                throw new Exception('Penarikan ini sudah tidak berstatus pending.');
            }

            $withdrawal->status = 'rejected';
            $withdrawal->validated_by = $validatorId;
            $withdrawal->save();

            // Create withdrawal history record
            $withdrawal->history()->create([
                'status' => 'rejected',
                'processed_by' => $validatorId,
                'processed_at' => now(),
            ]);

            // Record Activity Log
            $validator = User::find($validatorId);
            $nasabah = $withdrawal->user;
            ActivityLog::create([
                'user_id' => $validatorId,
                'action' => 'reject_withdrawal',
                'description' => "{$validator->name} menolak penarikan saldo #{$withdrawal->id} milik nasabah {$nasabah->name} sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.'),
            ]);
        });
    }

    /**
     * Validate operational hours (08:00-16:00).
     */
    public static function isWithinOperationalHours(): bool
    {
        $now = now();
        $hour = (int) $now->format('G');
        $minute = (int) $now->format('i');
        $totalMinutes = ($hour * 60) + $minute;

        $startMinutes = 8 * 60; // 08:00
        $endMinutes = 16 * 60;  // 16:00

        return $totalMinutes >= $startMinutes && $totalMinutes < $endMinutes;
    }

    /**
     * Check if withdrawal can be submitted based on H-2 SLA.
     * Submission must be at least 2 days before the requested processing date.
     */
    public static function validateSla(\DateTime $requestedDate = null): bool
    {
        $requestedDate = $requestedDate ?? now()->addDays(2);
        $minAllowedDate = now()->addDays(2)->startOfDay();

        return $requestedDate >= $minAllowedDate;
    }
}
