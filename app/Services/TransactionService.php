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
            $donationText = $deposit->is_donation ? ' (Sebagai Sedekah/Donasi)' : '';
            ActivityLog::create([
                'user_id' => $validatorId,
                'action' => 'approve_deposit',
                'description' => "{$validator->name} menyetujui setoran #{$deposit->id} milik nasabah {$nasabah->name}{$donationText} dengan total berat {$weightTotal} kg/L dan total nilai Rp " . number_format($totalPrice, 0, ',', '.'),
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

            if ($nasabah->saldo < $withdrawal->amount) {
                throw new Exception('Saldo nasabah tidak mencukupi untuk melakukan penarikan ini.');
            }

            $balanceBefore = $nasabah->saldo;
            // Deduct balance
            $nasabah->saldo -= $withdrawal->amount;
            $nasabah->save();
            $balanceAfter = $nasabah->saldo;

            // Update withdrawal details
            $withdrawal->status = 'approved';
            $withdrawal->validated_by = $validatorId;
            $withdrawal->save();

            // Record Ledger Mutation (debit)
            Mutation::create([
                'user_id' => $nasabah->id,
                'type' => 'debit',
                'amount' => $withdrawal->amount,
                'sourceable_id' => $withdrawal->id,
                'sourceable_type' => Withdrawal::class,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);

            // Record Activity Log
            $validator = User::find($validatorId);
            ActivityLog::create([
                'user_id' => $validatorId,
                'action' => 'approve_withdrawal',
                'description' => "{$validator->name} menyetujui penarikan saldo #{$withdrawal->id} milik nasabah {$nasabah->name} sebesar Rp " . number_format($withdrawal->amount, 0, ',', '.'),
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
}
