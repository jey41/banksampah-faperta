<?php

namespace Tests\Feature\Deposit;

use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\Mutation;
use App\Models\TrashPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositApprovalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $nasabah;

    private TrashPrice $trashPrice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->petugas()->create();
        $this->nasabah = User::factory()->nasabah()->withSaldo(0)->create();
        $this->trashPrice = TrashPrice::factory()->create([
            'price_buy' => 2000,
            'carbon_factor' => 1.5,
        ]);
    }

    public function test_admin_can_approve_pending_deposit(): void
    {
        $deposit = Deposit::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $item = DepositItem::factory()->create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 0,
            'price_per_unit' => 2000,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.deposits.approve', $deposit), [
                'items' => [
                    ['id' => $item->id, 'weight' => 10.0],
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $deposit->refresh();
        $this->assertEquals('approved', $deposit->status);
        $this->assertEquals(10.0, $deposit->weight_total);
        $this->assertEquals(20000, $deposit->total_price);
        $this->assertEquals($this->admin->id, $deposit->validated_by);

        // Verify balance was credited
        $this->nasabah->refresh();
        $this->assertEquals(20000, $this->nasabah->saldo);

        // Verify mutation was created
        $this->assertDatabaseHas('mutations', [
            'user_id' => $this->nasabah->id,
            'type' => 'kredit',
            'amount' => 20000,
            'sourceable_type' => Deposit::class,
            'sourceable_id' => $deposit->id,
        ]);
    }

    public function test_approve_deposit_with_donation_does_not_credit_balance(): void
    {
        $deposit = Deposit::factory()->pending()->donation()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $item = DepositItem::factory()->create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 0,
            'price_per_unit' => 2000,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.deposits.approve', $deposit), [
                'items' => [
                    ['id' => $item->id, 'weight' => 5.0],
                ],
            ]);

        $deposit->refresh();
        $this->assertEquals('approved', $deposit->status);

        // Balance should NOT be credited for donations
        $this->nasabah->refresh();
        $this->assertEquals(0, $this->nasabah->saldo);

        // No mutation should be created
        $this->assertDatabaseCount('mutations', 0);
    }

    public function test_cannot_approve_already_approved_deposit(): void
    {
        $deposit = Deposit::factory()->approved()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('cms.deposits.approve', $deposit), [
                'items' => [],
            ]);

        // Policy denies approval of non-pending deposits → 403
        $response->assertStatus(403);
    }

    public function test_nasabah_cannot_approve_deposits(): void
    {
        $deposit = Deposit::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $response = $this->actingAs($this->nasabah)
            ->post(route('cms.deposits.approve', $deposit), [
                'items' => [],
            ]);

        // Nasabah is redirected away from admin panel
        $response->assertRedirect();
    }

    public function test_approve_deposit_creates_activity_log(): void
    {
        $deposit = Deposit::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $item = DepositItem::factory()->create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 0,
            'price_per_unit' => 2000,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.deposits.approve', $deposit), [
                'items' => [
                    ['id' => $item->id, 'weight' => 5.0],
                ],
            ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'approve_deposit',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_multiple_items_weight_is_calculated_correctly(): void
    {
        $deposit = Deposit::factory()->pending()->create([
            'user_id' => $this->nasabah->id,
        ]);

        $item1 = DepositItem::factory()->create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 0,
            'price_per_unit' => 2000,
        ]);

        $item2 = DepositItem::factory()->create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->trashPrice->id,
            'weight' => 0,
            'price_per_unit' => 3000,
        ]);

        $this->actingAs($this->admin)
            ->post(route('cms.deposits.approve', $deposit), [
                'items' => [
                    ['id' => $item1->id, 'weight' => 5.0],
                    ['id' => $item2->id, 'weight' => 3.0],
                ],
            ]);

        $deposit->refresh();
        $this->assertEquals(8.0, $deposit->weight_total);
        // 5 * 2000 + 3 * 3000 = 10000 + 9000 = 19000
        $this->assertEquals(19000, $deposit->total_price);
    }
}
