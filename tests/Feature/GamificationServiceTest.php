<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TrashPrice;
use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\UserBadge;
use App\Models\PickupRequest;
use App\Models\Mutation;
use App\Services\GamificationService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $nasabah;
    private User $petugas;
    private TrashPrice $plasticPrice;
    private TrashPrice $paperPrice;
    private TrashPrice $metalPrice;
    private GamificationService $gamificationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gamificationService = new GamificationService();

        // Create a nasabah
        $this->nasabah = User::create([
            'name' => 'Test Nasabah',
            'email' => 'nasabah_test@bsfpunmul.com',
            'password' => bcrypt('password'),
            'role' => 'nasabah',
            'status' => 'verified',
            'saldo' => 0,
            'account_no' => 'BS-10005',
        ]);

        // Create a petugas
        $this->petugas = User::create([
            'name' => 'Test Petugas',
            'email' => 'petugas_test@bsfpunmul.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
            'status' => 'verified',
        ]);

        // Create trash prices for different categories
        $this->plasticPrice = TrashPrice::create([
            'name' => 'Plastik PET',
            'category' => 'plastik',
            'category_type' => 'umum',
            'price_buy' => 4000,
            'price_sell' => 6000,
            'unit' => 'kg',
            'carbon_factor' => 2.15,
        ]);

        $this->paperPrice = TrashPrice::create([
            'name' => 'Kertas HVS',
            'category' => 'kertas',
            'category_type' => 'umum',
            'price_buy' => 2500,
            'price_sell' => 4000,
            'unit' => 'kg',
            'carbon_factor' => 0.94,
        ]);

        $this->metalPrice = TrashPrice::create([
            'name' => 'Besi Tua',
            'category' => 'logam',
            'category_type' => 'umum',
            'price_buy' => 5000,
            'price_sell' => 8000,
            'unit' => 'kg',
            'carbon_factor' => 1.40,
        ]);
    }

    public function test_eco_points_base_calculation(): void
    {
        // Create an approved deposit of 10kg plastic
        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 40000,
            'weight_total' => 10.0,
            'status' => 'approved',
            'validated_by' => $this->petugas->id,
        ]);

        DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->plasticPrice->id,
            'weight' => 10.0,
            'price_per_unit' => $this->plasticPrice->price_buy,
            'total_price' => 40000,
            'total_carbon' => 21.5,
        ]);

        // Base points should be:
        // Weight points: 10 kg * 5 pts = 50 pts
        // Transaction points: 1 deposit * 3 pts = 3 pts
        // Donation points: 0
        // Expected total: 53 pts
        $points = $this->gamificationService->getEcoPoints($this->nasabah);
        $this->assertEquals(53, $points);

        $breakdown = $this->gamificationService->getEcoPointsBreakdown($this->nasabah);
        $this->assertEquals(50, $breakdown['weight_points']);
        $this->assertEquals(3, $breakdown['transaction_points']);
        $this->assertEquals(0, $breakdown['donation_points']);
    }

    public function test_eco_points_with_donation_bonus(): void
    {
        // Create an approved donation deposit of 5kg paper
        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 0,
            'weight_total' => 5.0,
            'status' => 'approved',
            'is_donation' => true,
            'validated_by' => $this->petugas->id,
        ]);

        DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->paperPrice->id,
            'weight' => 5.0,
            'price_per_unit' => $this->paperPrice->price_buy,
            'total_price' => 0,
            'total_carbon' => 4.7,
        ]);

        // Base points:
        // Weight: 5 kg * 5 = 25 pts
        // Transaction: 1 deposit * 3 = 3 pts
        // Donation: 5 kg * 3 = 15 pts
        // Expected: 43 pts
        $points = $this->gamificationService->getEcoPoints($this->nasabah);
        $this->assertEquals(43, $points);

        $breakdown = $this->gamificationService->getEcoPointsBreakdown($this->nasabah);
        $this->assertEquals(25, $breakdown['weight_points']);
        $this->assertEquals(3, $breakdown['transaction_points']);
        $this->assertEquals(15, $breakdown['donation_points']);
    }

    public function test_levels_thresholds(): void
    {
        // 0 points -> Pemula Hijau (Level 1)
        $level = $this->gamificationService->getLevel(0);
        $this->assertEquals('Pemula Hijau', $level['name']);
        $this->assertEquals(1, $level['level_number']);
        $this->assertEquals(150, $level['points_to_next']);
        $this->assertEquals(0, $level['progress_percent']);

        // 149 points -> Pemula Hijau (Level 1)
        $level = $this->gamificationService->getLevel(149);
        $this->assertEquals('Pemula Hijau', $level['name']);
        $this->assertEquals(99, $level['progress_percent']);

        // 150 points -> Pejuang Lingkungan (Level 2)
        $level = $this->gamificationService->getLevel(150);
        $this->assertEquals('Pejuang Lingkungan', $level['name']);
        $this->assertEquals(2, $level['level_number']);
        $this->assertEquals(250, $level['points_to_next']); // 400 - 150
        $this->assertEquals(0, $level['progress_percent']);

        // 500 points -> Ksatria Hijau (Level 3)
        $level = $this->gamificationService->getLevel(500);
        $this->assertEquals('Ksatria Hijau', $level['name']);
        $this->assertEquals(3, $level['level_number']);
        $this->assertEquals(300, $level['points_to_next']); // 800 - 500
        $this->assertEquals(25, $level['progress_percent']); // (500-400)/(800-400) = 100/400 = 25%

        // 1600 points -> Legenda Faperta (Level 5)
        $level = $this->gamificationService->getLevel(1600);
        $this->assertEquals('Legenda Faperta', $level['name']);
        $this->assertEquals(5, $level['level_number']);
        $this->assertTrue($level['is_max_level']);
    }

    public function test_badge_sync_first_deposit(): void
    {
        // Initially no badges
        $badges = $this->gamificationService->getBadges($this->nasabah);
        $firstDepositBadge = collect($badges)->firstWhere('key', 'first_deposit');
        $this->assertFalse($firstDepositBadge['unlocked']);

        // Set up approved deposit
        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 4000,
            'weight_total' => 1.0,
            'status' => 'approved',
            'validated_by' => $this->petugas->id,
        ]);

        DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->plasticPrice->id,
            'weight' => 1.0,
            'price_per_unit' => $this->plasticPrice->price_buy,
            'total_price' => 4000,
            'total_carbon' => 2.15,
        ]);

        // Sync badges
        $newlyUnlocked = $this->gamificationService->syncBadges($this->nasabah);
        $this->assertContains('first_deposit', $newlyUnlocked);

        // Verify database and status
        $this->assertDatabaseHas('user_badges', [
            'user_id' => $this->nasabah->id,
            'badge_key' => 'first_deposit',
        ]);

        $badgesAfter = $this->gamificationService->getBadges($this->nasabah);
        $firstDepositBadgeAfter = collect($badgesAfter)->firstWhere('key', 'first_deposit');
        $this->assertTrue($firstDepositBadgeAfter['unlocked']);
    }

    public function test_badge_sync_heavy_lifter(): void
    {
        // 80 kg deposit total
        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 320000,
            'weight_total' => 80.0,
            'status' => 'approved',
            'validated_by' => $this->petugas->id,
        ]);

        DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->plasticPrice->id,
            'weight' => 80.0,
            'price_per_unit' => $this->plasticPrice->price_buy,
            'total_price' => 320000,
            'total_carbon' => 172.0,
        ]);

        $newlyUnlocked = $this->gamificationService->syncBadges($this->nasabah);
        $this->assertContains('first_deposit', $newlyUnlocked);
        $this->assertContains('heavy_lifter', $newlyUnlocked);
        $this->assertContains('plastic_hero', $newlyUnlocked); // Since plastik weight 80 >= 30
        $this->assertContains('tree_friend', $newlyUnlocked);  // Carbon 172 >= 50
    }

    public function test_streak_calculation(): void
    {
        // Create deposits across multiple consecutive months
        // Let's create an approved deposit 2 months ago, 1 month ago, and this month
        $months = [
            now()->subMonths(2),
            now()->subMonth(),
            now()
        ];

        foreach ($months as $index => $dateTime) {
            $deposit = Deposit::create([
                'user_id' => $this->nasabah->id,
                'total_price' => 4000,
                'weight_total' => 1.0,
                'status' => 'approved',
                'validated_by' => $this->petugas->id,
            ]);
            $deposit->created_at = $dateTime;
            $deposit->updated_at = $dateTime;
            $deposit->save();

            $item = DepositItem::create([
                'deposit_id' => $deposit->id,
                'trash_price_id' => $this->plasticPrice->id,
                'weight' => 1.0,
                'price_per_unit' => $this->plasticPrice->price_buy,
                'total_price' => 4000,
                'total_carbon' => 2.15,
            ]);
            $item->created_at = $dateTime;
            $item->save();
        }

        $streak = $this->gamificationService->calculateStreakMonths($this->nasabah);
        $this->assertEquals(3, $streak);

        // Expected eco-points breakdown for streak: (3 - 1) * 15 = 30 pts
        $breakdown = $this->gamificationService->getEcoPointsBreakdown($this->nasabah);
        $this->assertEquals(30, $breakdown['streak_points']);
    }

    public function test_diversity_calculation(): void
    {
        // Create an approved deposit with 3 categories in one month
        $deposit = Deposit::create([
            'user_id' => $this->nasabah->id,
            'total_price' => 10000,
            'weight_total' => 3.0,
            'status' => 'approved',
            'validated_by' => $this->petugas->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->plasticPrice->id,
            'weight' => 1.0,
            'price_per_unit' => $this->plasticPrice->price_buy,
            'total_price' => 4000,
            'total_carbon' => 2.15,
            'created_at' => now(),
        ]);

        DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->paperPrice->id,
            'weight' => 1.0,
            'price_per_unit' => $this->paperPrice->price_buy,
            'total_price' => 2500,
            'total_carbon' => 0.94,
            'created_at' => now(),
        ]);

        DepositItem::create([
            'deposit_id' => $deposit->id,
            'trash_price_id' => $this->metalPrice->id,
            'weight' => 1.0,
            'price_per_unit' => $this->metalPrice->price_buy,
            'total_price' => 5000,
            'total_carbon' => 1.40,
            'created_at' => now(),
        ]);

        $diversityMonths = $this->gamificationService->calculateDiversityBonusMonths($this->nasabah);
        $this->assertEquals(1, $diversityMonths);

        // Expected eco-points breakdown for diversity: 1 * 10 = 10 pts
        $breakdown = $this->gamificationService->getEcoPointsBreakdown($this->nasabah);
        $this->assertEquals(10, $breakdown['diversity_points']);
    }
}
