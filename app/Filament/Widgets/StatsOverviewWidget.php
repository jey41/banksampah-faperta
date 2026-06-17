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
        // 1. Calculate Net Profit
        $netProfit = DepositItem::whereHas('deposit', fn($q) => $q->where('status', 'approved'))
            ->join('trash_prices', 'deposit_items.trash_price_id', '=', 'trash_prices.id')
            ->sum(DB::raw('deposit_items.weight * (trash_prices.price_sell - trash_prices.price_buy)'));

        // 2. Calculate Total Weight (Approved)
        $totalWeight = Deposit::where('status', 'approved')->sum('weight_total');

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
            Stat::make('Keuntungan Bersih (BI)', 'Rp ' . number_format($netProfit, 0, ',', '.'))
                ->description('Selisih harga jual pabrik & harga beli nasabah')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Total Berat Sampah', number_format($totalWeight, 2, ',', '.') . ' kg/L')
                ->description('Total sampah terpilah yang didaur ulang')
                ->descriptionIcon('heroicon-m-scale')
                ->color('primary'),
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
