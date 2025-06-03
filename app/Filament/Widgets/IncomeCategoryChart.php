<?php

namespace App\Filament\Widgets;

use App\Models\FinanceTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class IncomeCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Income Categories';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 6;
    protected bool $columnSpanFull = false;

    protected function getData(): array
    {
        // Get income data by category
        $incomeData = FinanceTransaction::query()
            ->where('type', 'income')
            ->where('status', true)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name => $item->total];
            })
            ->toArray();

        // Generate colors for income categories
        $colors = [
            '#10B981', // Emerald
            '#059669', // Green
            '#34D399', // Light Green
            '#6EE7B7', // Mint
            '#A7F3D0', // Pale Green
            '#D1FAE5', // Very Light Green
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Income Categories',
                    'data' => array_values($incomeData),
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                ],
            ],
            'labels' => array_keys($incomeData),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ₹${value} (${percentage}%)`;
                        }'
                    ],
                ],
                'datalabels' => [
                    'display' => false,
                ]
            ],
        ];
    }

    protected function getPlugins(): array
    {
        return [
            'datalabels' => true,
        ];
    }
}
