<?php

namespace App\Services\Dashboard;

use App\Models\ActivityLog;
use App\Models\Deposit;
use App\Models\DepositItem;
use App\Models\PickupRequest;
use App\Models\SiteVisit;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Get overview statistics for dashboard.
     * Cached for 5 minutes for performance.
     */
    public function getOverviewStats(string $trashFilter = 'all'): array
    {
        return Cache::remember("dashboard.overview_stats.{$trashFilter}", now()->addMinutes(5), function () use ($trashFilter) {
            return [
                'total_donation_profit' => $this->getTotalDonationProfit(),
                'total_weight' => $this->getTotalWeightAll($trashFilter),
                'retained_balance' => $this->getRetainedBalance(),
                'active_ratio' => $this->getActiveNasabahRatio(),
                'active_nasabah' => $this->getActiveNasabahCount(),
                'total_nasabah' => $this->getTotalNasabahCount(),
            ];
        });
    }

    /**
     * Get today's transaction summary.
     */
    public function getTodaySummary(): array
    {
        $today = Carbon::today();

        return [
            'deposits_today' => Deposit::whereDate('created_at', $today)->count(),
            'withdrawals_today' => Withdrawal::whereDate('created_at', $today)->count(),
            'pending_pickups' => PickupRequest::whereIn('status', ['pending', 'assigned'])->count(),
            'pending_deposits' => Deposit::where('status', 'pending')->count(),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
        ];
    }

    /**
     * Get recent activity logs.
     */
    public function getRecentActivities(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::with('user')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get deposit trend for last N days.
     */
    public function getDepositTrend(int $days = 7): Collection
    {
        return collect(range($days - 1, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'label' => $date->translatedFormat('d M'),
                'date' => $date->format('Y-m-d'),
                'weight' => (float) Deposit::where('status', 'approved')
                    ->whereDate('created_at', $date)
                    ->sum('weight_total'),
            ];
        });
    }

    /**
     * Get donation breakdown (savings vs donation).
     */
    public function getDonationBreakdown(): array
    {
        $weightSavings = (float) Deposit::where('status', 'approved')
            ->where('is_donation', false)
            ->sum('weight_total');

        $weightDonation = (float) Deposit::where('status', 'approved')
            ->where('is_donation', true)
            ->sum('weight_total');

        return [
            'savings_weight' => $weightSavings,
            'donation_weight' => $weightDonation,
            'savings_percentage' => $this->calculatePercentage($weightSavings, $weightSavings + $weightDonation),
            'donation_percentage' => $this->calculatePercentage($weightDonation, $weightSavings + $weightDonation),
        ];
    }

    /**
     * Get trash type comparison data.
     *
     * @param  string  $filter  'all', 'donasi', or 'tabungan'
     */
    public function getTrashTypeComparison(string $filter = 'all'): Collection
    {
        $query = DepositItem::join('deposits', 'deposit_items.deposit_id', '=', 'deposits.id')
            ->join('trash_prices', 'deposit_items.trash_price_id', '=', 'trash_prices.id')
            ->where('deposits.status', 'approved')
            ->selectRaw('trash_prices.category as category_name, SUM(deposit_items.weight) as total_weight');

        if ($filter === 'donasi') {
            $query->where('deposits.is_donation', true);
        } elseif ($filter === 'tabungan') {
            $query->where('deposits.is_donation', false);
        }

        return $query->groupBy('trash_prices.category')
            ->orderByDesc('total_weight')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucwords(str_replace('_', ' ', $item->category_name)),
                    'weight' => (float) $item->total_weight,
                ];
            });
    }

    /**
     * Get daily visitor trend
     */
    public function getVisitorTrendDaily(int $days = 7): Collection
    {
        return collect(range($days - 1, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            $views = SiteVisit::whereDate('visited_at', $date)->count();
            $unique = SiteVisit::whereDate('visited_at', $date)->distinct('ip_address')->count('ip_address');

            return [
                'label' => $date->translatedFormat('d M'),
                'date' => $date->format('Y-m-d'),
                'views' => $views,
                'unique' => $unique,
            ];
        });
    }

    /**
     * Get weekly visitor trend
     */
    public function getVisitorTrendWeekly(int $weeks = 8): Collection
    {
        return collect(range($weeks - 1, 0))->map(function ($weeksAgo) {
            $startDate = Carbon::today()->subWeeks($weeksAgo)->startOfWeek();
            $endDate = Carbon::today()->subWeeks($weeksAgo)->endOfWeek();

            $views = SiteVisit::whereBetween('visited_at', [$startDate, $endDate])->count();
            $unique = SiteVisit::whereBetween('visited_at', [$startDate, $endDate])->distinct('ip_address')->count('ip_address');

            return [
                'label' => 'Mg '.$startDate->weekOfMonth.' '.$startDate->translatedFormat('M'),
                'date' => $startDate->format('Y-m-d'),
                'views' => $views,
                'unique' => $unique,
            ];
        });
    }

    /**
     * Get monthly visitor trend
     */
    public function getVisitorTrendMonthly(int $months = 6): Collection
    {
        return collect(range($months - 1, 0))->map(function ($monthsAgo) {
            $date = Carbon::today()->startOfMonth()->subMonths($monthsAgo);

            $views = SiteVisit::whereYear('visited_at', $date->year)
                ->whereMonth('visited_at', $date->month)
                ->count();
            $unique = SiteVisit::whereYear('visited_at', $date->year)
                ->whereMonth('visited_at', $date->month)
                ->distinct('ip_address')
                ->count('ip_address');

            return [
                'label' => $date->translatedFormat('M Y'),
                'date' => $date->format('Y-m-d'),
                'views' => $views,
                'unique' => $unique,
            ];
        });
    }

    /**
     * Clear dashboard cache.
     * Call this when data changes that affect dashboard stats.
     */
    public function clearCache(): void
    {
        Cache::forget('dashboard.overview_stats.all');
        Cache::forget('dashboard.overview_stats.donasi');
        Cache::forget('dashboard.overview_stats.tabungan');
    }

    // ========================================
    // Private Helper Methods
    // ========================================

    /**
     * Calculate total donation profit.
     */
    private function getTotalDonationProfit(): int
    {
        return (int) Deposit::where('status', 'approved')
            ->where('is_donation', true)
            ->sum('total_price');
    }

    /**
     * Calculate total weight of all approved deposits.
     */
    private function getTotalWeightAll(string $filter = 'all'): float
    {
        $query = Deposit::where('status', 'approved');
        if ($filter === 'donasi') {
            $query->where('is_donation', true);
        } elseif ($filter === 'tabungan') {
            $query->where('is_donation', false);
        }

        return (float) $query->sum('weight_total');
    }

    /**
     * Calculate total retained balance (saldo nasabah).
     */
    private function getRetainedBalance(): int
    {
        return (int) User::where('role', 'nasabah')
            ->sum('saldo');
    }

    /**
     * Calculate active nasabah ratio (%).
     */
    private function getActiveNasabahRatio(): float
    {
        $total = $this->getTotalNasabahCount();

        if ($total === 0) {
            return 0.0;
        }

        $active = $this->getActiveNasabahCount();

        return round(($active / $total) * 100, 1);
    }

    /**
     * Get count of active nasabah (had transaction in last 30 days).
     */
    private function getActiveNasabahCount(): int
    {
        return User::where('role', 'nasabah')
            ->where(function ($q) {
                $q->whereHas('deposits', fn ($d) => $d->where('created_at', '>=', now()->subDays(30)))
                    ->orWhereHas('withdrawals', fn ($w) => $w->where('created_at', '>=', now()->subDays(30)));
            })
            ->count();
    }

    /**
     * Get total nasabah count.
     */
    private function getTotalNasabahCount(): int
    {
        return User::where('role', 'nasabah')->count();
    }

    /**
     * Calculate percentage.
     */
    private function calculatePercentage(float $value, float $total): float
    {
        if ($total == 0) {
            return 0.0;
        }

        return round(($value / $total) * 100, 1);
    }
}
