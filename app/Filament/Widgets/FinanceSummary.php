<?php

namespace App\Filament\Widgets;

use App\Models\FinanceTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class FinanceSummary extends BaseWidget
{
    protected function getStats(): array
    {
        // Get the current filter state
        $filters = request()->get('filters', []);

        // Debug: Log the filters
        \Log::info('Current Filters:', $filters);

        // Build the query based on the filters
        $query = FinanceTransaction::query();

        // Apply filters if they exist
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (isset($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }
        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (isset($filters['member'])) {
            $query->where('member_id', $filters['member']);
        }
        if (isset($filters['tags'])) {
            $query->whereHas('tags', function ($query) use ($filters) {
                $query->whereIn('id', $filters['tags']);
            });
        }

        // Debug: Log the query
        \Log::info('Query:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        // Calculate totals based on the filtered query
        $totalIncome = $query->where('type', 'income')->where('status', true)->sum('amount');
        $totalExpense = $query->where('type', 'expense')->where('status', false)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return [
            Stat::make('Total Income', '₹' . number_format($totalIncome, 2))
                ->description('All income transactions')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Expense', '₹' . number_format($totalExpense, 2))
                ->description('All expense transactions')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Balance', '₹' . number_format($balance, 2))
                ->description('Current balance')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($balance >= 0 ? 'success' : 'danger'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            // ->where('id', '!=', 1) // Exclude admin user
            ->where('status', true); // Exclude records where status is false
    }
}
