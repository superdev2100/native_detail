<?php

namespace App\Filament\Widgets;

use App\Models\FinanceTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Expense Categories';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 6;
    protected bool $columnSpanFull = false;

    protected function getData(): array
    {
        // Get expense data by category
        $expenseData = FinanceTransaction::query()
            ->where('type', 'expense')
            ->where('status', true)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name => $item->total];
            })
            ->toArray();

        // Generate colors for expense categories
        $colors = [
            '#EF4444', // Red
            '#DC2626', // Dark Red
            '#F87171', // Light Red
            '#FCA5A5', // Pale Red
            '#FECACA', // Very Light Red
            '#FEE2E2', // Super Light Red
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Expense Categories',
                    'data' => array_values($expenseData),
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                ],
            ],
            'labels' => array_keys($expenseData),
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
 