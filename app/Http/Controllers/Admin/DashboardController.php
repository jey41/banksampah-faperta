<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $trashFilter = 'all'; // Initial load

        // Fetch all dashboard data from service layer
        $overviewStats = $this->dashboardService->getOverviewStats('all');
        $todaySummary = $this->dashboardService->getTodaySummary();
        $recentActivities = $this->dashboardService->getRecentActivities(8);
        $trend = $this->dashboardService->getDepositTrend(7);
        $donationBreakdown = $this->dashboardService->getDonationBreakdown();
        $trashTypeComparison = $this->dashboardService->getTrashTypeComparison('all');

        // Pass data to view (maintain backward compatibility with view variable names)
        return view('admin.dashboard.index', [
            // Overview stats
            'totalDonationProfit' => $overviewStats['total_donation_profit'],
            'totalWeightAll' => $overviewStats['total_weight'],
            'retainedBalance' => $overviewStats['retained_balance'],
            'activeRatio' => $overviewStats['active_ratio'],
            'activeNasabah' => $overviewStats['active_nasabah'],
            'totalNasabah' => $overviewStats['total_nasabah'],

            // Today summary
            'depositsToday' => $todaySummary['deposits_today'],
            'withdrawalsToday' => $todaySummary['withdrawals_today'],
            'pendingPickups' => $todaySummary['pending_pickups'],
            'pendingDeposits' => $todaySummary['pending_deposits'],
            'pendingWithdrawals' => $todaySummary['pending_withdrawals'],

            // Activities and trends
            'recentActivities' => $recentActivities,
            'trend' => $trend,
            'trashTypeComparison' => $trashTypeComparison,
            'trashFilter' => $trashFilter,

            // Donation breakdown
            'weightSavings' => $donationBreakdown['savings_weight'],
            'weightDonation' => $donationBreakdown['donation_weight'],

            // Site Visits
            'uniqueVisitorsMonth' => SiteVisit::thisMonth()->uniqueVisitors()->count('ip_address'),
            'uniqueVisitorsToday' => SiteVisit::today()->uniqueVisitors()->count('ip_address'),
            'totalViewsMonth' => SiteVisit::thisMonth()->count(),

            // Visitor Trends
            'visitorDaily' => $this->dashboardService->getVisitorTrendDaily(7),
            'visitorWeekly' => $this->dashboardService->getVisitorTrendWeekly(8),
            'visitorMonthly' => $this->dashboardService->getVisitorTrendMonthly(6),
        ]);
    }

    public function cleanupVisits(Request $request)
    {
        $period = $request->input('period', '6_months');
        $date = match ($period) {
            '1_month' => now()->subMonth(),
            '3_months' => now()->subMonths(3),
            '6_months' => now()->subMonths(6),
            '1_year' => now()->subYear(),
            default => now()->subMonths(6),
        };

        $deleted = SiteVisit::where('visited_at', '<', $date)->delete();

        return redirect()->back()->with('success', "Berhasil menghapus {$deleted} data kunjungan pengunjung website.");
    }

    public function getTrashStats(Request $request)
    {
        $type = $request->input('type'); // 'volume' or 'comparison'
        $filter = $request->input('filter', 'all');

        if ($type === 'volume') {
            $stats = $this->dashboardService->getOverviewStats($filter);

            return response()->json([
                'value' => number_format($stats['total_weight'], 2, ',', '.').' kg/L',
                'description' => $filter === 'donasi' ? 'Kategori: Sampah Donasi' : ($filter === 'tabungan' ? 'Kategori: Sampah Tabungan' : 'Gabungan tabungan & donasi'),
            ]);
        }

        if ($type === 'comparison') {
            $comparison = $this->dashboardService->getTrashTypeComparison($filter);

            return response()->json([
                'labels' => $comparison->pluck('label'),
                'weights' => $comparison->pluck('weight'),
            ]);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }
}
