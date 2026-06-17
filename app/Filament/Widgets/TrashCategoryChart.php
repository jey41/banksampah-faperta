<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\DepositItem;
use Illuminate\Support\Facades\DB;

class TrashCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Volume Sampah (kg/L)';
    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        // Get total weight group by category
        $data = DepositItem::whereHas('deposit', fn($q) => $q->where('status', 'approved'))
            ->join('trash_prices', 'deposit_items.trash_price_id', '=', 'trash_prices.id')
            ->select('trash_prices.category', DB::raw('SUM(deposit_items.weight) as total_weight'))
            ->groupBy('trash_prices.category')
            ->get();

        $labels = [];
        $weights = [];

        // Formatting labels for Indonesian mapping
        $categoryMap = [
            'plastik' => 'Plastik',
            'kertas' => 'Kertas',
            'logam' => 'Logam',
            'kaca' => 'Kaca',
            'minyak_jelantah' => 'Minyak Jelantah',
            'lainnya' => 'Lainnya',
        ];

        foreach ($data as $item) {
            $labels[] = $categoryMap[$item->category] ?? ucfirst($item->category);
            $weights[] = (float)$item->total_weight;
        }

        // Default empty state handling
        if (empty($labels)) {
            $labels = ['Belum Ada Data'];
            $weights = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Berat (kg/L)',
                    'data' => $weights,
                    'backgroundColor' => [
                        '#10B981', // Emerald
                        '#3B82F6', // Blue
                        '#F59E0B', // Amber
                        '#6B7280', // Gray
                        '#EF4444', // Red
                        '#8B5CF6', // Purple
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }
}
