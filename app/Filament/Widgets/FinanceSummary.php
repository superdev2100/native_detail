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

        // Create base query with common filters
        $baseQuery = FinanceTransaction::query();

        // Apply common filters if they exist
        if (isset($filters['category'])) {
            $baseQuery->where('category_id', $filters['category']);
        }
        if (isset($filters['payment_method'])) {
            $baseQuery->where('payment_method', $filters['payment_method']);
        }
        if (isset($filters['member'])) {
            $baseQuery->where('member_id', $filters['member']);
        }
        if (isset($filters['tags'])) {
            $baseQuery->whereHas('tags', function ($query) use ($filters) {
                $query->whereIn('id', $filters['tags']);
            });
        }

        // Create separate queries for income and expenses
        $incomeQuery = clone $baseQuery;
        $expenseQuery = clone $baseQuery;

        // Calculate totals based on the filtered queries
        $totalIncome = $incomeQuery->where('type', 'income')->where('status', true)->sum('amount');
        $totalExpense = $expenseQuery->where('type', 'expense')->where('status', true)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        // Debug: Log the queries
        \Log::info('Income Query:', ['sql' => $incomeQuery->toSql(), 'bindings' => $incomeQuery->getBindings()]);
        \Log::info('Expense Query:', ['sql' => $expenseQuery->toSql(), 'bindings' => $expenseQuery->getBindings()]);

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

    // public static function getEloquentQuery(): Builder
    // {
    //     return parent::getEloquentQuery()
    //         // ->where('id', '!=', 1) // Exclude admin user
    //         ->where('status', true); // Exclude records where status is false
    // }
}
