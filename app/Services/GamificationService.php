<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBadge;
use App\Models\DepositItem;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Level definitions with exponential thresholds.
     */
    public const LEVELS = [
        ['key' => 'pemula_hijau',        'name' => 'Pemula Hijau',        'icon' => 'seedling',         'emoji' => '🌱', 'min_points' => 0],
        ['key' => 'pejuang_lingkungan',  'name' => 'Pejuang Lingkungan',  'icon' => 'eco',              'emoji' => '🌿', 'min_points' => 150],
        ['key' => 'ksatria_hijau',       'name' => 'Ksatria Hijau',       'icon' => 'park',             'emoji' => '🌳', 'min_points' => 400],
        ['key' => 'pahlawan_bumi',       'name' => 'Pahlawan Bumi',       'icon' => 'landscape',        'emoji' => '🏔️', 'min_points' => 800],
        ['key' => 'legenda_faperta',     'name' => 'Legenda Faperta',     'icon' => 'public',           'emoji' => '🌍', 'min_points' => 1500],
    ];

    /**
     * Badge definitions — all conditions are checked programmatically.
     */
    public const BADGES = [
        [
            'key' => 'first_deposit',
            'name' => 'Penyetor Pertama',
            'description' => 'Berhasil menyetor sampah untuk pertama kali',
            'icon' => '⭐',
            'requirement' => '1 setoran disetujui',
        ],
        [
            'key' => 'frequent_depositor',
            'name' => 'Si Rajin Pilah',
            'description' => 'Telah menyetor sampah sebanyak 15 kali',
            'icon' => '🏅',
            'requirement' => '15 setoran disetujui',
        ],
        [
            'key' => 'heavy_lifter',
            'name' => 'Petugas Bersih',
            'description' => 'Menyetor total 75 kg sampah',
            'icon' => '🧹',
            'requirement' => '75 kg total berat setoran',
        ],
        [
            'key' => 'plastic_hero',
            'name' => 'Pahlawan Plastik',
            'description' => 'Menyetor 30 kg sampah kategori plastik',
            'icon' => '♻️',
            'requirement' => '30 kg sampah plastik',
        ],
        [
            'key' => 'tree_friend',
            'name' => 'Sahabat Pohon',
            'description' => 'Mengurangi 50 kg emisi CO₂ melalui daur ulang',
            'icon' => '🌳',
            'requirement' => 'Reduksi karbon ≥ 50 kg CO₂e',
        ],
        [
            'key' => 'noble_donor',
            'name' => 'Donatur Mulia',
            'description' => 'Mendonasikan sampah sebanyak 3 kali',
            'icon' => '💚',
            'requirement' => '3 donasi sampah',
        ],
        [
            'key' => 'streak_master',
            'name' => 'Penabung Setia',
            'description' => 'Setor sampah 5 bulan berturut-turut',
            'icon' => '🔥',
            'requirement' => '5 bulan streak setor',
        ],
        [
            'key' => 'green_millionaire',
            'name' => 'Miliarder Hijau',
            'description' => 'Saldo pernah mencapai Rp 100.000',
            'icon' => '💰',
            'requirement' => 'Saldo pernah ≥ Rp 100.000',
        ],
        [
            'key' => 'pickup_captain',
            'name' => 'Kapten Jemput',
            'description' => 'Menyelesaikan 5 permintaan jemput sampah',
            'icon' => '🚚',
            'requirement' => '5 jemput sampah selesai',
        ],
        [
            'key' => 'campus_legend',
            'name' => 'Legenda Kampus',
            'description' => 'Mencapai level tertinggi: Legenda Faperta',
            'icon' => '👑',
            'requirement' => 'Mencapai level Legenda Faperta',
        ],
    ];

    /**
     * Calculate total eco-points for a user (computed on-the-fly).
     */
    public function getEcoPoints(User $user): int
    {
        $approvedDeposits = $user->deposits()->where('status', 'approved');

        $totalWeight = (float) $approvedDeposits->sum('weight_total');
        $totalDeposits = $approvedDeposits->count();
        $donationWeight = (float) $user->deposits()
            ->where('status', 'approved')
            ->where('is_donation', true)
            ->sum('weight_total');

        // Base points
        $basePoints = (int) floor($totalWeight * 5)   // 5 poin per kg
                    + ($totalDeposits * 3)              // 3 poin per transaksi
                    + (int) floor($donationWeight * 3); // bonus 3 poin per kg donasi

        // Streak bonus (15 poin per consecutive month)
        $streakMonths = $this->calculateStreakMonths($user);
        $streakBonus = max(0, ($streakMonths - 1)) * 15;

        // Diversity bonus (10 poin per month with ≥3 categories)
        $diversityBonus = $this->calculateDiversityBonusMonths($user) * 10;

        return $basePoints + $streakBonus + $diversityBonus;
    }

    /**
     * Get eco-points breakdown for display purposes.
     */
    public function getEcoPointsBreakdown(User $user): array
    {
        $approvedDeposits = $user->deposits()->where('status', 'approved');

        $totalWeight = (float) $approvedDeposits->sum('weight_total');
        $totalDeposits = $approvedDeposits->count();
        $donationWeight = (float) $user->deposits()
            ->where('status', 'approved')
            ->where('is_donation', true)
            ->sum('weight_total');

        $streakMonths = $this->calculateStreakMonths($user);
        $diversityMonths = $this->calculateDiversityBonusMonths($user);

        return [
            'weight_points' => (int) floor($totalWeight * 5),
            'transaction_points' => $totalDeposits * 3,
            'donation_points' => (int) floor($donationWeight * 3),
            'streak_points' => max(0, ($streakMonths - 1)) * 15,
            'diversity_points' => $diversityMonths * 10,
            'streak_months' => $streakMonths,
            'diversity_months' => $diversityMonths,
        ];
    }

    /**
     * Get level info for a given points total.
     */
    public function getLevel(int $points): array
    {
        $levels = self::LEVELS;
        $currentLevel = $levels[0];
        $currentIndex = 0;

        for ($i = count($levels) - 1; $i >= 0; $i--) {
            if ($points >= $levels[$i]['min_points']) {
                $currentLevel = $levels[$i];
                $currentIndex = $i;
                break;
            }
        }

        $isMaxLevel = ($currentIndex >= count($levels) - 1);
        $nextLevel = $isMaxLevel ? null : $levels[$currentIndex + 1];

        $progressPercent = 100;
        $pointsToNext = 0;
        $pointsInCurrentRange = 0;

        if (!$isMaxLevel && $nextLevel) {
            $rangeStart = $currentLevel['min_points'];
            $rangeEnd = $nextLevel['min_points'];
            $pointsInCurrentRange = $points - $rangeStart;
            $rangeSize = $rangeEnd - $rangeStart;
            $progressPercent = min(100, (int) floor(($pointsInCurrentRange / $rangeSize) * 100));
            $pointsToNext = $rangeEnd - $points;
        }

        return [
            'name' => $currentLevel['name'],
            'key' => $currentLevel['key'],
            'icon' => $currentLevel['icon'],
            'emoji' => $currentLevel['emoji'],
            'level_number' => $currentIndex + 1,
            'total_levels' => count($levels),
            'min_points' => $currentLevel['min_points'],
            'next_level_name' => $nextLevel['name'] ?? null,
            'next_level_points' => $nextLevel['min_points'] ?? null,
            'points_to_next' => $pointsToNext,
            'progress_percent' => $progressPercent,
            'is_max_level' => $isMaxLevel,
        ];
    }

    /**
     * Get all badges with their unlock status for a user.
     */
    public function getBadges(User $user): array
    {
        $unlockedBadges = $user->userBadges()->pluck('unlocked_at', 'badge_key')->toArray();

        $stats = $this->getUserStats($user);

        return array_map(function ($badge) use ($unlockedBadges, $stats) {
            $isUnlocked = isset($unlockedBadges[$badge['key']]);
            $unlockedAt = $isUnlocked ? $unlockedBadges[$badge['key']] : null;

            return [
                'key' => $badge['key'],
                'name' => $badge['name'],
                'description' => $badge['description'],
                'icon' => $badge['icon'],
                'requirement' => $badge['requirement'],
                'unlocked' => $isUnlocked,
                'unlocked_at' => $unlockedAt,
                'progress' => $this->getBadgeProgress($badge['key'], $stats),
            ];
        }, self::BADGES);
    }

    /**
     * Check for newly unlocked badges and persist them.
     */
    public function syncBadges(User $user): array
    {
        $stats = $this->getUserStats($user);
        $ecoPoints = $this->getEcoPoints($user);
        $level = $this->getLevel($ecoPoints);
        $existingBadges = $user->userBadges()->pluck('badge_key')->toArray();

        $newlyUnlocked = [];

        $conditions = [
            'first_deposit'      => $stats['total_deposits'] >= 1,
            'frequent_depositor' => $stats['total_deposits'] >= 15,
            'heavy_lifter'       => $stats['total_weight'] >= 75,
            'plastic_hero'       => $stats['plastic_weight'] >= 30,
            'tree_friend'        => $stats['total_carbon'] >= 50,
            'noble_donor'        => $stats['donation_count'] >= 3,
            'streak_master'      => $stats['streak_months'] >= 5,
            'green_millionaire'  => $stats['max_balance'] >= 100000,
            'pickup_captain'     => $stats['completed_pickups'] >= 5,
            'campus_legend'      => $level['key'] === 'legenda_faperta',
        ];

        foreach ($conditions as $badgeKey => $isUnlocked) {
            if ($isUnlocked && !in_array($badgeKey, $existingBadges)) {
                UserBadge::create([
                    'user_id' => $user->id,
                    'badge_key' => $badgeKey,
                    'unlocked_at' => now(),
                ]);
                $newlyUnlocked[] = $badgeKey;
            }
        }

        return $newlyUnlocked;
    }

    /**
     * Calculate the number of consecutive months with at least 1 approved deposit.
     * Counts backwards from the current month.
     */
    public function calculateStreakMonths(User $user): int
    {
        $deposits = $user->deposits()
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->pluck('created_at')
            ->map(fn($date) => $date->format('Y-m'))
            ->unique()
            ->values()
            ->toArray();

        if (empty($deposits)) {
            return 0;
        }

        $streak = 1;
        $currentMonth = now()->format('Y-m');

        // If the most recent deposit month isn't the current month, check if it's last month
        if ($deposits[0] !== $currentMonth) {
            $lastMonth = now()->subMonth()->format('Y-m');
            if ($deposits[0] !== $lastMonth) {
                return 0; // Streak broken
            }
        }

        for ($i = 0; $i < count($deposits) - 1; $i++) {
            $current = \Carbon\Carbon::createFromFormat('Y-m', $deposits[$i]);
            $next = \Carbon\Carbon::createFromFormat('Y-m', $deposits[$i + 1]);

            if ($current->subMonth()->format('Y-m') === $next->format('Y-m')) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Calculate how many months the user deposited ≥3 different trash categories.
     */
    public function calculateDiversityBonusMonths(User $user): int
    {
        $driver = DB::connection()->getDriverName();
        $monthFormat = $driver === 'sqlite' 
            ? "strftime('%Y-%m', d.created_at)" 
            : "DATE_FORMAT(d.created_at, '%Y-%m')";

        $result = DB::select("
            SELECT COUNT(*) as months FROM (
                SELECT {$monthFormat} as month, COUNT(DISTINCT tp.category) as cat_count
                FROM deposits d
                JOIN deposit_items di ON di.deposit_id = d.id
                JOIN trash_prices tp ON tp.id = di.trash_price_id
                WHERE d.user_id = ? AND d.status = 'approved'
                GROUP BY month
                HAVING cat_count >= 3
            ) as diverse_months
        ", [$user->id]);

        return $result[0]->months ?? 0;
    }

    /**
     * Gather all stats needed for badge checking.
     */
    private function getUserStats(User $user): array
    {
        $approvedDeposits = $user->deposits()->where('status', 'approved');

        // Total weight of plastic category deposits
        $plasticWeight = (float) DepositItem::whereHas('deposit', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', 'approved');
        })->whereHas('trashPrice', function ($query) {
            $query->where('category', 'plastik');
        })->sum('weight');

        // Total carbon reduction
        $totalCarbon = (float) DepositItem::whereHas('deposit', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', 'approved');
        })->sum('total_carbon');

        // Max balance ever reached (check mutations for highest balance_after)
        $maxBalance = (int) ($user->saldo); // Current saldo as baseline
        $maxMutationBalance = \App\Models\Mutation::where('user_id', $user->id)
            ->max('balance_after');
        if ($maxMutationBalance !== null) {
            $maxBalance = max($maxBalance, (int) $maxMutationBalance);
        }

        // Completed pickup requests
        $completedPickups = $user->pickupRequests()
            ->where('status', 'completed')
            ->count();

        return [
            'total_deposits' => $approvedDeposits->count(),
            'total_weight' => (float) $approvedDeposits->sum('weight_total'),
            'plastic_weight' => $plasticWeight,
            'total_carbon' => $totalCarbon,
            'donation_count' => (int) $user->deposits()
                ->where('status', 'approved')
                ->where('is_donation', true)
                ->count(),
            'streak_months' => $this->calculateStreakMonths($user),
            'max_balance' => $maxBalance,
            'completed_pickups' => $completedPickups,
        ];
    }

    /**
     * Get progress info for a specific badge (for display purposes).
     */
    private function getBadgeProgress(string $badgeKey, array $stats): array
    {
        $progressMap = [
            'first_deposit'      => ['current' => $stats['total_deposits'],   'target' => 1,      'unit' => 'setoran'],
            'frequent_depositor' => ['current' => $stats['total_deposits'],   'target' => 15,     'unit' => 'setoran'],
            'heavy_lifter'       => ['current' => round($stats['total_weight'], 1),   'target' => 75,     'unit' => 'kg'],
            'plastic_hero'       => ['current' => round($stats['plastic_weight'], 1), 'target' => 30,     'unit' => 'kg plastik'],
            'tree_friend'        => ['current' => round($stats['total_carbon'], 1),   'target' => 50,     'unit' => 'kg CO₂e'],
            'noble_donor'        => ['current' => $stats['donation_count'],   'target' => 3,      'unit' => 'donasi'],
            'streak_master'      => ['current' => $stats['streak_months'],    'target' => 5,      'unit' => 'bulan'],
            'green_millionaire'  => ['current' => $stats['max_balance'],      'target' => 100000, 'unit' => 'rupiah'],
            'pickup_captain'     => ['current' => $stats['completed_pickups'],'target' => 5,      'unit' => 'jemput'],
            'campus_legend'      => ['current' => 0, 'target' => 1, 'unit' => 'level'], // Special case
        ];

        $progress = $progressMap[$badgeKey] ?? ['current' => 0, 'target' => 1, 'unit' => ''];
        $progress['percent'] = min(100, (int) floor(($progress['current'] / max(1, $progress['target'])) * 100));

        return $progress;
    }
}
