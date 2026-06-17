<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class TransactionTrendChart extends ChartWidget
{
    protected ?string $heading = 'Tren Arus Kas Bulanan (Setoran vs Penarikan)';
    protected static ?int $sort = 3;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        // Get last 6 months starting from current month going backwards
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->put(now()->subMonths($i)->format('Y-m'), now()->subMonths($i)->translatedFormat('F Y'));
        }

        $depositData = [];
        $withdrawalData = [];
        $labels = [];

        // Fetch aggregated deposits and withdrawals
        $depositsGrouped = Deposit::where('status', 'approved')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(total_price) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        $withdrawalsGrouped = Withdrawal::where('status', 'approved')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->pluck('total', 'month');

        foreach ($months as $key => $name) {
            $labels[] = $name;
            $depositData[] = (int)($depositsGrouped->get($key) ?? 0);
            $withdrawalData[] = (int)($withdrawalsGrouped->get($key) ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Setoran (Rp)',
                    'data' => $depositData,
                    'borderColor' => '#10B981', // Emerald
                    'fill' => false,
                ],
                [
                    'label' => 'Total Penarikan (Rp)',
                    'data' => $withdrawalData,
                    'borderColor' => '#EF4444', // Red
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
