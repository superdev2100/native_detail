<?php

namespace App\Filament\Widgets;

use App\Models\FinanceTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceSummary extends BaseWidget
{
    protected function getStats(): array
    {
        $totalIncome = FinanceTransaction::where('type', 'income')->sum('amount');
        $totalExpense = FinanceTransaction::where('type', 'expense')->sum('amount');
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
}
