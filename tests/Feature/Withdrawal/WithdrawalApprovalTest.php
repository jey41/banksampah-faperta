<?php

namespace Tests\Feature\Withdrawal;

use App\Models\Mutation;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $nasabah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->petugas()->create();
        $this->nasabah = User::factory()->nasabah()->withSaldo(100000)->create();
    }

    public function test_admin_can_approve_pending_withdrawal(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->tunai()->create([
            'user_id' => $this->nasabah->id,
            'amount' => 50000,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.approve', $withdrawal));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $withdrawal->refresh();
        $this->assertEquals('approved', $withdrawal->status);
        $this->assertEquals($this->admin->id, $withdrawal->validated_by);

        // Balance should be deducted (no admin fee for tunai)
        $this->nasabah->refresh();
        $this->assertEquals(50000, $this->nasabah->saldo);

        // Debit mutation should be created
        $this->assertDatabaseHas('mutations', [
            'user_id' => $this->nasabah->id,
            'type' => 'debit',
            'amount' => 50000,
        ]);
    }

    public function test_approve_withdrawal_with_non_btn_bank_charges_fee(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->transferNonBtn()->create([
            'user_id' => $this->nasabah->id,
            'amount' => 50000,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.approve', $withdrawal));

        $withdrawal->refresh();
        $this->assertEquals('approved', $withdrawal->status);
        $this->assertEquals(2500, $withdrawal->admin_fee);

        // Balance should be deducted (amount + admin fee)
        $this->nasabah->refresh();
        // 100000 - 50000 - 2500 = 47500
        $this->assertEquals(47500, $this->nasabah->saldo);

        // Debit mutation should include admin fee
        $this->assertDatabaseHas('mutations', [
            'user_id' => $this->nasabah->id,
            'type' => 'debit',
            'amount' => 52500, // 50000 + 2500 admin fee
        ]);
    }

    public function test_approve_withdrawal_with_btn_bank_no_fee(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->transferBtn()->create([
            'user_id' => $this->nasabah->id,
            'amount' => 50000,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.approve', $withdrawal));

        $withdrawal->refresh();
        $this->assertEquals(0, $withdrawal->admin_fee);

        // Balance should be deducted (no admin fee for BTN)
        $this->nasabah->refresh();
        $this->assertEquals(50000, $this->nasabah->saldo);
    }

    public function test_cannot_approve_withdrawal_with_insufficient_balance(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->tunai()->create([
            'user_id' => $this->nasabah->id,
            'amount' => 200000, // More than saldo (100000)
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.approve', $withdrawal));

        $response->assertSessionHas('error');

        $withdrawal->refresh();
        $this->assertEquals('pending', $withdrawal->status);

        // Balance should not change
        $this->nasabah->refresh();
        $this->assertEquals(100000, $this->nasabah->saldo);
    }

    public function test_nasabah_cannot_approve_withdrawals(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $response = $this->actingAs($this->nasabah)
            ->post(route('cms.withdrawals.approve', $withdrawal));

        // Nasabah is redirected away from admin panel
        $response->assertRedirect();
    }

    public function test_approve_withdrawal_creates_history_record(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->tunai()->create([
            'user_id' => $this->nasabah->id,
            'amount' => 30000,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.approve', $withdrawal));

        $this->assertDatabaseHas('withdrawal_history', [
            'withdrawal_id' => $withdrawal->id,
            'status' => 'approved',
            'processed_by' => $this->admin->id,
        ]);
    }

    public function test_approve_withdrawal_creates_activity_log(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->tunai()->create([
            'user_id' => $this->nasabah->id,
            'amount' => 30000,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.approve', $withdrawal));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'approve_withdrawal',
            'user_id' => $this->admin->id,
        ]);
    }
}
