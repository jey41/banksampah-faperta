<?php

namespace Tests\Feature\Withdrawal;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalRejectionTest extends TestCase
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

    public function test_admin_can_reject_pending_withdrawal(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
            'amount' => 50000,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.reject', $withdrawal));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $withdrawal->refresh();
        $this->assertEquals('rejected', $withdrawal->status);
        $this->assertEquals($this->admin->id, $withdrawal->validated_by);

        // Balance should not change
        $this->nasabah->refresh();
        $this->assertEquals(100000, $this->nasabah->saldo);
    }

    public function test_reject_withdrawal_creates_history_record(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.reject', $withdrawal));

        $this->assertDatabaseHas('withdrawal_history', [
            'withdrawal_id' => $withdrawal->id,
            'status' => 'rejected',
            'processed_by' => $this->admin->id,
        ]);
    }

    public function test_nasabah_cannot_reject_withdrawals(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $response = $this->actingAs($this->nasabah)
            ->post(route('cms.withdrawals.reject', $withdrawal));

        // Nasabah is redirected away from admin panel
        $response->assertRedirect();
    }

    public function test_reject_withdrawal_creates_activity_log(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.reject', $withdrawal));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'reject_withdrawal',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_reject_withdrawal_sends_notification(): void
    {
        $withdrawal = Withdrawal::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.withdrawals.reject', $withdrawal));

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->nasabah->id,
            'type' => \App\Notifications\WithdrawalRejectedNotification::class,
        ]);
    }
}
