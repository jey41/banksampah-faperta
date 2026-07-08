<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\TrashPrice;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\TransactionService;
use Exception;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class QASimulateTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qa:simulate-transactions {--cleanup : Clean up simulated data after simulation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate 10 users performing transactions (deposits and withdrawals) for QA testing';

    /**
     * Execute the console command.
     */
    public function handle(TransactionService $transactionService)
    {
        $this->info('======================================================');
        $this->info('       STARTING QA TRANSACTION SIMULATION             ');
        $this->info('======================================================');

        $faker = Faker::create('id_ID');

        // 1. Get or Create Admin
        $admin = User::where('role', 'super_admin')->first();
        if (! $admin) {
            $admin = User::create([
                'name' => 'Admin QA',
                'email' => 'admin.qa@bsfpunmul.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'verified',
                'phone' => '081122334455',
                'address' => 'QA Lab',
                'saldo' => 0,
                'account_no' => 'BS-QAADM',
            ]);
            $this->comment('Created a new QA Admin account.');
        } else {
            $this->comment("Using existing Admin: {$admin->name} ({$admin->email})");
        }

        // Check if there are Trash Prices in the database
        $trashPrices = TrashPrice::all();
        if ($trashPrices->isEmpty()) {
            $this->error('Error: Tidak ada data kategori sampah di database. Silakan jalankan seeder terlebih dahulu: php artisan db:seed');

            return 1;
        }

        // 2. Create 10 QA Users
        $this->info("\n--- PHASE 1: Creating 10 QA Users ---");
        $users = [];
        $userIdsToDelete = [];

        for ($i = 1; $i <= 10; $i++) {
            $name = 'QA User '.$i.' - '.$faker->firstName;
            $email = "qa.user{$i}@example.com";

            // Check if user already exists
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'name' => $name,
                    'status' => 'verified',
                    'saldo' => 0,
                ]);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'nasabah',
                    'status' => 'verified',
                    'phone' => '089'.$faker->numerify('########'),
                    'address' => $faker->address,
                    'saldo' => 0,
                    'account_no' => 'BS-QA'.str_pad($i, 4, '0', STR_PAD_LEFT),
                ]);
            }
            $users[] = $user;
            $userIdsToDelete[] = $user->id;
            $this->line("- [User {$i}] Created: {$user->name} (Account: {$user->account_no})");
        }

        // 3. Simulate Deposits
        $this->info("\n--- PHASE 2: Simulating Deposit Submissions (Pending Status) ---");
        $deposits = [];
        $depositIdsToDelete = [];

        foreach ($users as $index => $user) {
            $itemCount = rand(1, 3);
            $itemsToCreate = [];
            $totalPrice = 0;
            $weightTotal = 0;

            // Prepare some random items
            $selectedTrash = $trashPrices->random($itemCount);

            // Generate pending deposit request
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'total_price' => 0, // Calculated on approval
                'weight_total' => 0, // Calculated on approval
                'status' => 'pending',
                'notes' => 'QA Auto Deposit Sim',
            ]);
            $depositIdsToDelete[] = $deposit->id;

            foreach ($selectedTrash as $trash) {
                $weight = round(rand(10, 100) / 10, 2); // 1.0 to 10.0
                $estimatedPrice = $weight * $trash->price_buy;

                $item = DepositItem::create([
                    'deposit_id' => $deposit->id,
                    'trash_price_id' => $trash->id,
                    'weight' => $weight,
                    'price_per_unit' => $trash->price_buy,
                    'total_price' => $estimatedPrice,
                ]);

                $weightTotal += $weight;
                $totalPrice += $estimatedPrice;
                $itemsToCreate[] = [
                    'id' => $item->id,
                    'weight' => $weight,
                ];
            }

            // Save estimated values
            $deposit->update([
                'total_price' => $totalPrice,
                'weight_total' => $weightTotal,
            ]);

            $deposits[] = [
                'deposit' => $deposit,
                'items' => $itemsToCreate,
                'user_index' => $index,
            ];

            $this->line("- [Deposit] User '{$user->name}' submitted deposit request: {$weightTotal} {$selectedTrash->first()->unit} (Estimated Rp ".number_format($totalPrice, 0, ',', '.').')');
        }

        // 4. Admin Approves & Weighs Deposits
        $this->info("\n--- PHASE 3: Admin Approving & Timbang Deposits via TransactionService ---");
        foreach ($deposits as $dData) {
            $deposit = $dData['deposit'];
            $items = $dData['items'];
            $user = $users[$dData['user_index']];

            // Simulate slight changes in weight during physical weighing by admin (e.g. +- 5%)
            $realItems = [];
            foreach ($items as $item) {
                $weightDiff = (rand(-5, 10) / 100); // -5% to +10%
                $realWeight = max(0.5, round($item['weight'] * (1 + $weightDiff), 2));
                $realItems[] = [
                    'id' => $item['id'],
                    'weight' => $realWeight,
                ];
            }

            // Call Service
            $transactionService->approveDeposit($deposit, $realItems, $admin->id);
            $deposit->refresh();
            $user->refresh();

            $this->line("- [Approved] Deposit #{$deposit->id} for '{$user->name}': Real price Rp ".number_format($deposit->total_price, 0, ',', '.').'. Saldo Sekarang: Rp '.number_format($user->saldo, 0, ',', '.'));
        }

        // 5. Simulate Withdrawals (Both valid and invalid)
        $this->info("\n--- PHASE 4: Simulating Withdrawal Submissions ---");
        $withdrawals = [];
        $withdrawalIdsToDelete = [];

        foreach ($users as $index => $user) {
            // User tries to withdraw a valid amount (e.g. 50% of balance)
            $validAmount = (int) ($user->saldo * 0.5);
            // Ensure it's at least 10,000 (minimum)
            $validAmount = max(10000, $validAmount);

            // Only simulate if user has enough balance (including potential 2500 admin fee)
            if ($user->saldo >= ($validAmount + 2500)) {
                $withdrawalValid = Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $validAmount,
                    'bank_name' => 'Bank Mandiri',
                    'account_number' => '123456789',
                    'account_name' => $user->name,
                    'status' => 'pending',
                    'notes' => 'QA Valid Withdrawal',
                ]);
                $withdrawalIdsToDelete[] = $withdrawalValid->id;
                $withdrawals[] = [
                    'withdrawal' => $withdrawalValid,
                    'user_index' => $index,
                    'type' => 'valid',
                ];
                $this->line("- [Withdraw Request] User '{$user->name}' requested valid withdrawal of Rp ".number_format($validAmount, 0, ',', '.'));
            }

            // User 3 and 7 will also attempt to withdraw an invalid amount (e.g. 150% of balance) to test bounds
            if ($index === 2 || $index === 6) {
                $invalidAmount = (int) ($user->saldo * 1.5);
                $withdrawalInvalid = Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => $invalidAmount,
                    'bank_name' => 'GOPAY',
                    'account_number' => '089999999',
                    'account_name' => $user->name,
                    'status' => 'pending',
                    'notes' => 'QA Invalid Withdrawal (Insufficient Balance)',
                ]);
                $withdrawalIdsToDelete[] = $withdrawalInvalid->id;
                $withdrawals[] = [
                    'withdrawal' => $withdrawalInvalid,
                    'user_index' => $index,
                    'type' => 'invalid',
                ];
                $this->warn("- [Withdraw Request] User '{$user->name}' requested invalid withdrawal of Rp ".number_format($invalidAmount, 0, ',', '.').' (Overlimit)');
            }
        }

        // 6. Admin Process Withdrawals
        $this->info("\n--- PHASE 5: Admin Processing Withdrawals via TransactionService ---");
        foreach ($withdrawals as $wData) {
            $withdrawal = $wData['withdrawal'];
            $user = $users[$wData['user_index']];
            $type = $wData['type'];

            if ($type === 'valid') {
                // Should pass
                $transactionService->approveWithdrawal($withdrawal, $admin->id);
                $user->refresh();
                $this->line("- [Approved] Withdrawal #{$withdrawal->id} of Rp ".number_format($withdrawal->amount, 0, ',', '.')." for '{$user->name}'. Saldo Sekarang: Rp ".number_format($user->saldo, 0, ',', '.'));
            } else {
                // Should fail due to validation
                try {
                    $transactionService->approveWithdrawal($withdrawal, $admin->id);
                    $this->error("- [FAIL] Withdrawal #{$withdrawal->id} of Rp ".number_format($withdrawal->amount, 0, ',', '.')." for '{$user->name}' was APPROVED but should have failed!");
                } catch (Exception $e) {
                    $transactionService->rejectWithdrawal($withdrawal, $admin->id);
                    $this->warn("- [Rejected Expectedly] Withdrawal #{$withdrawal->id} for '{$user->name}' failed: ".$e->getMessage());
                }
            }
        }

        // 7. Verify Data Integrity & Summary Table
        $this->info("\n--- PHASE 6: Data Integrity Validation & Summary ---");

        $tableData = [];
        $passCount = 0;

        foreach ($users as $index => $user) {
            $user->refresh();

            // Calculate manual check: Approved deposits - Approved withdrawals
            $totalDep = Deposit::where('user_id', $user->id)->where('status', 'approved')->sum('total_price');
            $totalWith = Withdrawal::where('user_id', $user->id)->where('status', 'approved')->sum('amount');
            $totalFee = Withdrawal::where('user_id', $user->id)->where('status', 'approved')->sum('admin_fee');
            $expectedSaldo = $totalDep - $totalWith - $totalFee;

            $status = ($user->saldo == $expectedSaldo) ? 'PASSED' : 'FAILED';
            if ($status === 'PASSED') {
                $passCount++;
            }

            $tableData[] = [
                $user->account_no,
                $user->name,
                'Rp '.number_format($totalDep, 0, ',', '.'),
                'Rp '.number_format($totalWith, 0, ',', '.'),
                'Rp '.number_format($user->saldo, 0, ',', '.'),
                $status,
            ];
        }

        $headers = ['Account No', 'User Name', 'Total Deposits', 'Total Withdrawals', 'Current Saldo', 'Audit Status'];
        $this->table($headers, $tableData);

        if ($passCount === 10) {
            $this->info("\n✅ AUDIT RESULT: 10/10 Users PASSED the balance integrity audit!");
        } else {
            $this->error("\n❌ AUDIT RESULT: ".(10 - $passCount).' Users FAILED the balance integrity audit!');
        }

        // 8. Cleanup if requested
        if ($this->option('cleanup')) {
            $this->info("\n--- Cleaning up simulated QA data ---");

            // Delete withdrawal records
            Withdrawal::whereIn('id', $withdrawalIdsToDelete)->delete();
            // Delete deposit items and deposits
            DepositItem::whereIn('deposit_id', $depositIdsToDelete)->delete();
            Deposit::whereIn('id', $depositIdsToDelete)->delete();
            // Delete users
            User::whereIn('id', $userIdsToDelete)->delete();

            $this->info('Simulated data successfully cleaned up.');
        } else {
            $this->comment("\nNote: Data pengujian tetap disimpan di database agar bisa Anda verifikasi di aplikasi/Filament.");
            $this->comment('Gunakan opsi --cleanup jika ingin menghapusnya kembali: php artisan qa:simulate-transactions --cleanup');
        }

        $this->info("\n======================================================");
        $this->info('                 SIMULATION COMPLETED                 ');
        $this->info('======================================================');
    }
}
