<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Deposit;

class DepositCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Perbandingan Volume Setoran (Tabungan vs Donasi)';
    protected static ?int $sort = 4;

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $totalWeightSavings = Deposit::where('status', 'approved')->where('is_donation', false)->sum('weight_total');
        $totalWeightDonation = Deposit::where('status', 'approved')->where('is_donation', true)->sum('weight_total');

        return [
            'datasets' => [
                [
                    'label' => 'Volume Sampah (kg/L)',
                    'data' => [
                        (float)$totalWeightSavings,
                        (float)$totalWeightDonation,
                    ],
                    'backgroundColor' => [
                        '#3B82F6', // Blue for Tabungan
                        '#EF4444', // Red for Donasi
                    ],
                ],
            ],
            'labels' => ['Tabungan', 'Donasi'],
        ];
    }
}
