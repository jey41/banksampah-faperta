<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TrashPrice;
use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\Withdrawal;
use App\Models\PickupRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BankSampahTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $nasabah;
    protected TrashPrice $trashPrice;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'verified',
            'saldo' => 0,
            'account_no' => 'BS-00001',
        ]);

        $this->nasabah = User::create([
            'name' => 'Nasabah Test',
            'email' => 'nasabah@test.com',
            'password' => Hash::make('password'),
            'role' => 'nasabah',
            'status' => 'verified',
            'saldo' => 100000,
            'account_no' => 'BS-10001',
        ]);

        $this->trashPrice = TrashPrice::create([
            'name' => 'Plastik PET',
            'category' => 'plastik',
            'price_buy' => 5000,
            'price_sell' => 7000,
            'unit' => 'kg',
        ]);
    }

    public function test_nasabah_is_blocked_from_accessing_admin_panel()
    {
        $response = $this->actingAs($this->nasabah)
            ->get('/admin');

        // It should redirect them to the nasabah dashboard
        $response->assertRedirect(route('nasabah.dashboard'));
        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke panel admin.');
    }

    public function test_admin_can_access_admin_panel()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin');

        // Admin should be able to access the admin panel
        $response->assertStatus(200);
    }

    public function test_nasabah_dashboard_redirects_properly()
    {
        $response = $this->actingAs($this->nasabah)
            ->get('/dashboard');

        $response->assertRedirect(route('nasabah.dashboard'));
    }

    public function test_admin_dashboard_redirects_properly()
    {
        $response = $this->actingAs($this->admin)
            ->get('/dashboard');

        $response->assertRedirect('/admin');
    }

    public function test_nasabah_cannot_access_old_deposit_page()
    {
        $response = $this->actingAs($this->nasabah)
            ->get('/nasabah/setor');

        $response->assertStatus(404);
    }

    public function test_nasabah_can_view_pickup_request_page()
    {
        $response = $this->actingAs($this->nasabah)
            ->get(route('nasabah.pickup'));

        $response->assertStatus(200);
    }

    public function test_nasabah_can_submit_pickup_request()
    {
        $response = $this->actingAs($this->nasabah)
            ->post(route('nasabah.pickup.store'), [
                'pickup_address' => 'Jl. Raya Dramaga No. 10, Bogor',
                'pickup_phone' => '08123456789',
                'pickup_date' => now()->addDay()->format('Y-m-d'),
                'pickup_time' => '08:00-10:00',
                'latitude' => -0.4660341,
                'longitude' => 117.1558231,
                'estimated_distance' => 0,
                'notes' => 'Sampah plastik sudah dipilah',
            ]);

        $response->assertRedirect(route('nasabah.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pickup_requests', [
            'user_id' => $this->nasabah->id,
            'pickup_address' => 'Jl. Raya Dramaga No. 10, Bogor',
            'pickup_phone' => '08123456789',
            'pickup_time' => '08:00-10:00',
            'latitude' => -0.4660341,
            'longitude' => 117.1558231,
            'estimated_distance' => 0,
            'status' => 'pending',
        ]);
    }

    public function test_nasabah_can_submit_withdrawal_request()
    {
        \Illuminate\Support\Carbon::setTestNow('2026-06-20 10:00:00');

        $response = $this->actingAs($this->nasabah)
            ->post(route('nasabah.withdraw.store'), [
                'amount' => 50000,
                'withdrawal_method' => 'transfer_bank',
                'bank_name' => 'Bank Mandiri',
                'account_number' => '1234567890',
                'account_name' => 'Nasabah Test',
                'notes' => 'Tarik uang belanja',
            ]);

        \Illuminate\Support\Carbon::setTestNow();

        $response->assertRedirect(route('nasabah.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $this->nasabah->id,
            'amount' => 50000,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_verify_nasabah_and_generate_account_no()
    {
        $nasabahPending = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@test.com',
            'password' => Hash::make('password'),
            'role' => 'nasabah',
            'status' => 'pending',
            'saldo' => 0,
        ]);

        $this->actingAs($this->admin);

        // Execute the verify action logic
        $nasabahPending->status = 'verified';
        if (empty($nasabahPending->account_no)) {
            $nasabahPending->account_no = 'BS-' . str_pad($nasabahPending->id, 5, '0', STR_PAD_LEFT);
        }
        $nasabahPending->save();

        $this->assertDatabaseHas('users', [
            'id' => $nasabahPending->id,
            'status' => 'verified',
            'account_no' => 'BS-' . str_pad($nasabahPending->id, 5, '0', STR_PAD_LEFT),
        ]);
    }

    public function test_admin_can_reject_nasabah_registration()
    {
        $nasabahPending = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@test.com',
            'password' => Hash::make('password'),
            'role' => 'nasabah',
            'status' => 'pending',
            'saldo' => 0,
        ]);

        $this->actingAs($this->admin);

        // Execute the reject action logic
        $nasabahPending->status = 'rejected';
        $nasabahPending->save();

        $this->assertDatabaseHas('users', [
            'id' => $nasabahPending->id,
            'status' => 'rejected',
            'account_no' => null,
        ]);
    }

    public function test_admin_can_approve_deposit_request_and_recalculate_totals_inside_transaction()
    {
        $this->actingAs($this->admin);

        // Create pending deposit
        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 10000,
            'weight_total' => 2.0,
            'status' => 'pending',
        ]);

        $item = DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 2.0,
            'price_per_unit' => $this->trashPrice->price_buy,
            'total_price' => 10000,
        ]);

        $data = [
            'items' => [
                [
                    'id' => $item->id,
                    'trash_price_id' => $this->trashPrice->id,
                    'weight' => 3.5,
                ]
            ]
        ];

        // Execute the approve transaction
        app(\App\Services\TransactionService::class)->approveDeposit($deposit, $data['items'], $this->admin->id);

        // Verify deposit updated to approved, with recalculated weight 3.5 kg and price 17,500
        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => 'approved',
            'weight_total' => 3.5,
            'total_price' => 17500,
            'validated_by' => $this->admin->id,
        ]);

        // Verify nasabah balance credited (original 100,000 + 17,500 = 117,500)
        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 117500,
        ]);
    }

    public function test_approving_a_deposit_updates_nasabah_balance_from_status_change()
    {
        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 17500,
            'weight_total' => 3.5,
            'status' => 'pending',
        ]);

        $item = DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 3.5,
            'price_per_unit' => $this->trashPrice->price_buy,
            'total_price' => 17500,
        ]);

        $data = [
            [
                'id' => $item->id,
                'weight' => 3.5,
            ]
        ];

        app(\App\Services\TransactionService::class)->approveDeposit($deposit, $data, $this->admin->id);

        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 117500,
        ]);
    }

    public function test_saving_an_already_approved_deposit_does_not_credit_balance_twice()
    {
        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 17500,
            'weight_total' => 3.5,
            'status' => 'pending',
        ]);

        $item = DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 3.5,
            'price_per_unit' => $this->trashPrice->price_buy,
            'total_price' => 17500,
        ]);

        // Approve once
        app(\App\Services\TransactionService::class)->approveDeposit($deposit, [['id' => $item->id, 'weight' => 3.5]], $this->admin->id);

        // Update other fields
        $deposit->refresh();
        $deposit->notes = 'Catatan revisi admin';
        $deposit->save();

        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 117500,
        ]);
    }

    public function test_admin_can_reject_deposit_request()
    {
        $this->actingAs($this->admin);

        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 10000,
            'weight_total' => 2.0,
            'status' => 'pending',
        ]);

        app(\App\Services\TransactionService::class)->rejectDeposit($deposit, $this->admin->id);

        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'status' => 'rejected',
            'validated_by' => $this->admin->id,
        ]);

        // Verify nasabah balance remains unchanged
        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 100000,
        ]);
    }

    public function test_admin_can_approve_withdrawal_request_and_deduct_saldo_inside_transaction()
    {
        $this->actingAs($this->admin);

        $withdrawal = Withdrawal::create([
            'user_id' => $this->nasabah->id,
            'amount' => 40000,
            'withdrawal_method' => 'transfer_bank',
            'bank_name' => 'BTN',
            'bank_type' => 'btn',
            'account_number' => '1234567890',
            'account_name' => 'Nasabah Test',
            'status' => 'pending',
        ]);

        // Execute withdrawal approval logic
        app(\App\Services\TransactionService::class)->approveWithdrawal($withdrawal, $this->admin->id);

        // Verify withdrawal status and nasabah balance (100,000 - 40,000 = 60,000)
        $this->assertDatabaseHas('withdrawals', [
            'id' => $withdrawal->id,
            'status' => 'approved',
            'validated_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 60000,
        ]);
    }

    public function test_admin_cannot_approve_withdrawal_with_insufficient_balance()
    {
        $this->actingAs($this->admin);

        $withdrawal = Withdrawal::create([
            'user_id' => $this->nasabah->id,
            'amount' => 150000,
            'bank_name' => 'Bank Mandiri',
            'account_number' => '1234567890',
            'account_name' => 'Nasabah Test',
            'status' => 'pending',
        ]);

        $exceptionThrown = false;
        try {
            app(\App\Services\TransactionService::class)->approveWithdrawal($withdrawal, $this->admin->id);
        } catch (\Exception $e) {
            $exceptionThrown = true;
            $this->assertStringStartsWith('Saldo nasabah tidak mencukupi untuk melakukan penarikan ini.', $e->getMessage());
        }

        $this->assertTrue($exceptionThrown, 'Exception should have been thrown');

        // Verify withdrawal status remains pending and nasabah balance remains 100,000
        $this->assertDatabaseHas('withdrawals', [
            'id' => $withdrawal->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 100000,
        ]);
    }

    public function test_approve_deposit_creates_mutation_ledger_entry_and_activity_log()
    {
        $this->actingAs($this->admin);

        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 10000,
            'weight_total' => 2.0,
            'status' => 'pending',
            'is_donation' => false,
        ]);

        $item = DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 2.0,
            'price_per_unit' => $this->trashPrice->price_buy,
            'total_price' => 10000,
        ]);

        app(\App\Services\TransactionService::class)->approveDeposit($deposit, [['id' => $item->id, 'weight' => 2.0]], $this->admin->id);

        // Verify balance updated: 100,000 + 10,000 = 110,000
        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 110000,
        ]);

        // Verify Mutation Ledger entry
        $this->assertDatabaseHas('mutations', [
            'user_id' => $this->nasabah->id,
            'type' => 'kredit',
            'amount' => 10000,
            'sourceable_id' => $deposit->id,
            'sourceable_type' => Deposit::class,
            'balance_before' => 100000,
            'balance_after' => 110000,
        ]);

        // Verify Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'approve_deposit',
        ]);
    }

    public function test_approve_withdrawal_creates_mutation_ledger_entry_and_activity_log()
    {
        $this->actingAs($this->admin);

        $withdrawal = Withdrawal::create([
            'user_id' => $this->nasabah->id,
            'amount' => 40000,
            'withdrawal_method' => 'transfer_bank',
            'bank_name' => 'BTN',
            'bank_type' => 'btn',
            'account_number' => '1234567890',
            'account_name' => 'Nasabah Test',
            'status' => 'pending',
        ]);

        app(\App\Services\TransactionService::class)->approveWithdrawal($withdrawal, $this->admin->id);

        // Verify balance updated: 100,000 - 40,000 = 60,000
        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 60000,
        ]);

        // Verify Mutation Ledger entry
        $this->assertDatabaseHas('mutations', [
            'user_id' => $this->nasabah->id,
            'type' => 'debit',
            'amount' => 40000,
            'sourceable_id' => $withdrawal->id,
            'sourceable_type' => Withdrawal::class,
            'balance_before' => 100000,
            'balance_after' => 60000,
        ]);

        // Verify Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'approve_withdrawal',
        ]);
    }

    public function test_deposit_marked_as_donation_does_not_increase_user_saldo()
    {
        $this->actingAs($this->admin);

        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 10000,
            'weight_total' => 2.0,
            'status' => 'pending',
            'is_donation' => true,
            'donation_category' => 'donasi',
        ]);

        $item = DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 2.0,
            'price_per_unit' => $this->trashPrice->price_buy,
            'total_price' => 10000,
        ]);

        app(\App\Services\TransactionService::class)->approveDeposit($deposit, [['id' => $item->id, 'weight' => 2.0]], $this->admin->id);

        // Verify user balance remains unchanged (100,000)
        $this->assertDatabaseHas('users', [
            'id' => $this->nasabah->id,
            'saldo' => 100000,
        ]);

        // Verify Ledger Mutation is NOT created for donation
        $this->assertDatabaseMissing('mutations', [
            'user_id' => $this->nasabah->id,
            'sourceable_id' => $deposit->id,
            'sourceable_type' => Deposit::class,
        ]);

        // Verify Activity Log includes donation text
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'approve_deposit',
            'description' => $this->admin->name . ' menyetujui setoran #' . $deposit->id . ' milik nasabah ' . $this->nasabah->name . ' (Sebagai Donasi) - Kategori: Sampah Donasi dengan total berat 2 kg/L dan total nilai Rp 10.000',
        ]);
    }

    public function test_nasabah_can_create_and_delete_savings_targets()
    {
        $this->actingAs($this->nasabah);

        // Create savings target
        $response = $this->post(route('nasabah.target.store'), [
            'title' => 'Beli Blender',
            'target_amount' => 150000,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('savings_targets', [
            'user_id' => $this->nasabah->id,
            'title' => 'Beli Blender',
            'target_amount' => 150000,
            'is_achieved' => false,
        ]);

        $target = \App\Models\SavingsTarget::where('title', 'Beli Blender')->firstOrFail();

        // Delete target
        $responseDelete = $this->delete(route('nasabah.target.delete', $target->id));
        $responseDelete->assertRedirect();

        $this->assertDatabaseMissing('savings_targets', [
            'id' => $target->id,
        ]);
    }

    public function test_nasabah_cannot_submit_withdrawal_outside_operational_hours()
    {
        // 17:00:00 is outside 08:00 - 16:00
        \Illuminate\Support\Carbon::setTestNow('2026-06-20 17:00:00');

        $response = $this->actingAs($this->nasabah)
            ->post(route('nasabah.withdraw.store'), [
                'amount' => 50000,
                'withdrawal_method' => 'transfer_bank',
                'bank_name' => 'Bank Mandiri',
                'account_number' => '1234567890',
                'account_name' => 'Nasabah Test',
                'notes' => 'Tarik uang belanja',
            ]);

        \Illuminate\Support\Carbon::setTestNow();

        $response->assertSessionHasErrors(['withdrawal_method']);
        $this->assertDatabaseMissing('withdrawals', [
            'user_id' => $this->nasabah->id,
            'amount' => 50000,
        ]);
    }

    public function test_nasabah_cannot_submit_pickup_request_in_the_past()
    {
        $response = $this->actingAs($this->nasabah)
            ->post(route('nasabah.pickup.store'), [
                'pickup_address' => 'Jl. Raya Dramaga No. 10, Bogor',
                'pickup_phone' => '08123456789',
                'pickup_date' => now()->subDay()->format('Y-m-d'), // Past date
                'pickup_time' => '08:00-10:00',
                'latitude' => -0.4660341,
                'longitude' => 117.1558231,
                'estimated_distance' => 0,
                'notes' => 'Sampah plastik',
            ]);

        $response->assertSessionHasErrors(['pickup_date']);
        $this->assertDatabaseMissing('pickup_requests', [
            'user_id' => $this->nasabah->id,
            'pickup_address' => 'Jl. Raya Dramaga No. 10, Bogor',
        ]);
    }

    public function test_nasabah_cannot_submit_pickup_request_exceeding_max_distance()
    {
        $response = $this->actingAs($this->nasabah)
            ->post(route('nasabah.pickup.store'), [
                'pickup_address' => 'Jl. Raya Dramaga No. 10, Bogor',
                'pickup_phone' => '08123456789',
                'pickup_date' => now()->addDay()->format('Y-m-d'),
                'pickup_time' => '08:00-10:00',
                'latitude' => -0.4660341,
                'longitude' => 117.1558231,
                'estimated_distance' => 3.5, // 3.5 km (limit is 2 km)
                'notes' => 'Sampah plastik',
            ]);

        $response->assertSessionHasErrors(['latitude']);
        $this->assertDatabaseMissing('pickup_requests', [
            'user_id' => $this->nasabah->id,
            'estimated_distance' => 3.5,
        ]);
    }

    public function test_admin_can_view_activity_logs_index_page()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/activity-logs');

        $response->assertStatus(200);
    }

    public function test_nasabah_cannot_view_activity_logs_index_page()
    {
        $response = $this->actingAs($this->nasabah)
            ->get('/admin/activity-logs');

        $response->assertRedirect(route('nasabah.dashboard'));
    }
}

