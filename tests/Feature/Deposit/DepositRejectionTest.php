<?php

namespace Tests\Feature\Deposit;

use App\Models\Deposit;
use App\Models\User;
use App\Notifications\DepositRejectedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositRejectionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $nasabah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->petugas()->create();
        $this->nasabah = User::factory()->nasabah()->withSaldo(0)->create();
    }

    public function test_admin_can_reject_pending_deposit(): void
    {
        $deposit = Deposit::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.deposits.reject', $deposit));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $deposit->refresh();
        $this->assertEquals('rejected', $deposit->status);
        $this->assertEquals($this->admin->id, $deposit->validated_by);

        // Balance should not change
        $this->nasabah->refresh();
        $this->assertEquals(0, $this->nasabah->saldo);
    }

    public function test_reject_approved_deposit_rollback_balance(): void
    {
        // Create an approved deposit with balance credited
        $deposit = Deposit::factory()->approved()->create([
            'user_id' => $this->nasabah->id,
            'total_price' => 50000,
        ]);

        // Credit the balance first (simulating approval)
        $this->nasabah->update(['saldo' => 50000]);

        // Policy only allows rejecting pending deposits
        // Trying to reject an approved deposit should be forbidden
        $response = $this->actingAs($this->admin)
            ->post(route('cms.deposits.reject', $deposit));

        // Should be rejected by policy (status is not pending)
        $response->assertStatus(403);

        // Balance should not change
        $this->nasabah->refresh();
        $this->assertEquals(50000, $this->nasabah->saldo);
    }

    public function test_nasabah_cannot_reject_deposits(): void
    {
        $deposit = Deposit::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $response = $this->actingAs($this->nasabah)
            ->post(route('cms.deposits.reject', $deposit));

        // Nasabah is redirected away from admin panel
        $response->assertRedirect();
    }

    public function test_reject_deposit_creates_activity_log(): void
    {
        $deposit = Deposit::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.deposits.reject', $deposit));

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'reject_deposit',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_reject_deposit_sends_notification(): void
    {
        $deposit = Deposit::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.deposits.reject', $deposit));

        // Check notification was created in database
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $this->nasabah->id,
            'type' => DepositRejectedNotification::class,
        ]);
    }
}
