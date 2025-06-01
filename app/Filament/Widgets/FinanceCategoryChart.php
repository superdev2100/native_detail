<?php

namespace App\Filament\Widgets;

use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FinanceCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Category-wise Income & Expenses';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get income data by category
        $incomeData = FinanceTransaction::query()
            ->where('type', 'income')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name => $item->total];
            })
            ->toArray();

        // Get expense data by category
        $expenseData = FinanceTransaction::query()
            ->where('type', 'expense')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name => $item->total];
            })
            ->toArray();

        // Get all unique categories
        $categories = array_unique(array_merge(
            array_keys($incomeData),
            array_keys($expenseData)
        ));

        return [
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => array_map(fn($category) => $incomeData[$category] ?? 0, $categories),
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Expenses',
                    'data' => array_map(fn($category) => $expenseData[$category] ?? 0, $categories),
                    'backgroundColor' => '#EF4444',
                    'borderColor' => '#EF4444',
                ],
            ],
            'labels' => $categories,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "₹" + value; }'
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.dataset.label + ": ₹" + context.raw; }'
                    ],
                ],
                'datalabels' => [
                    'anchor' => 'end',
                    'align' => 'top',
                    'formatter' => 'function(value) { return "₹" + value; }',
                    'color' => '#000',
                    'font' => [
                        'weight' => 'bold'
                    ]
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
