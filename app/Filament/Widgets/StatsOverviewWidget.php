<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Deposit;
use App\Models\DepositItem;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Calculate Total Donation Profit
        $totalDonationProfit = Deposit::where('status', 'approved')
            ->where('is_donation', true)
            ->sum('total_price');

        // 2. Calculate Total Weight (Approved) - Split by Tabungan vs Donasi and Combined
        $totalWeightSavings = Deposit::where('status', 'approved')->where('is_donation', false)->sum('weight_total');
        $totalWeightDonation = Deposit::where('status', 'approved')->where('is_donation', true)->sum('weight_total');
        $totalWeightAll = $totalWeightSavings + $totalWeightDonation;

        // 3. Calculate Retained Balance (Float)
        $retainedBalance = User::where('role', 'nasabah')->sum('saldo');

        // 4. Calculate Active Nasabah Ratio (transacted in last 30 days)
        $totalNasabah = User::where('role', 'nasabah')->count();
        $activeNasabah = User::where('role', 'nasabah')
            ->where(function ($query) {
                $query->whereHas('deposits', fn($q) => $q->where('created_at', '>=', now()->subDays(30)))
                    ->orWhereHas('withdrawals', fn($q) => $q->where('created_at', '>=', now()->subDays(30)));
            })
            ->count();
        
        $activeRatio = $totalNasabah > 0 ? round(($activeNasabah / $totalNasabah) * 100, 1) : 0;

        return [
            Stat::make('Total Keuntungan Donasi', 'Rp ' . number_format($totalDonationProfit, 0, ',', '.'))
                ->description('Total nilai uang dari setoran donasi/sedekah')
                ->descriptionIcon('heroicon-m-heart')
                ->color('success'),
            Stat::make('Total Volume Sampah', number_format($totalWeightAll, 2, ',', '.') . ' kg/L')
                ->description('Gabungan Volume Tabungan & Donasi')
                ->descriptionIcon('heroicon-m-trash')
                ->color('success'),
            Stat::make('Saldo Mengendap (Retained)', 'Rp ' . number_format($retainedBalance, 0, ',', '.'))
                ->description('Total tabungan nasabah di bank sampah')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('warning'),
            Stat::make('Rasio Nasabah Aktif', $activeRatio . '%')
                ->description("{$activeNasabah} dari {$totalNasabah} nasabah aktif (30 hari terakhir)")
                ->descriptionIcon('heroicon-m-user-group')
                ->color($activeRatio > 50 ? 'success' : 'gray'),
        ];
    }
}
