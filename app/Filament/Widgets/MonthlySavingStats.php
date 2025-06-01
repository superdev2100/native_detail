<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\FinanceTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Livewire\Attributes\On;

class MonthlySavingStats extends BaseWidget
{
    public $startDate;
    public $endDate;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth();
        $this->endDate = now()->endOfMonth();
    }

    #[On('refresh-widgets')]
    public function refresh(): void
    {
        $this->startDate = request()->get('startDate', now()->startOfMonth());
        $this->endDate = request()->get('endDate', now()->endOfMonth());
    }

    public function getStats(): array
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        $totalMembers = User::where('is_monthly_saving_scheme_member', true)
            ->where('id', '!=', 1)
            ->count();

        // Count members who have actually paid
        $paidMembers = User::where('is_monthly_saving_scheme_member', true)
            ->where('id', '!=', 1)
            ->whereHas('monthlySchemeTransactions', function ($query) use ($startDate, $endDate) {
                $query->where('status', true)
                    ->whereBetween('date', [$startDate, $endDate]);
            })
            ->count();

        // Count members who have promised to pay but haven't paid yet
        $promisedMembers = User::where('is_monthly_saving_scheme_member', true)
            ->where('id', '!=', 1)
            ->whereHas('monthlySchemeTransactions', function ($query) use ($startDate, $endDate) {
                $query->where('status', false)
                    ->whereNotNull('payment_date')
                    ->whereBetween('payment_date', [$startDate, $endDate]);
            })
            ->count();

        $totalExpected = User::where('is_monthly_saving_scheme_member', true)
            ->where('id', '!=', 1)
            ->sum('monthly_saving_amount');

        // Calculate total collected (only actual payments)
        $totalCollected = FinanceTransaction::where('category_id', function ($query) {
                $query->select('id')
                    ->from('finance_categories')
                    ->where('name', 'Monthly Saving Scheme')
                    ->first();
            })
            ->where('status', true)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Calculate promised amount (only for transactions that haven't been paid yet)
        $totalPromised = FinanceTransaction::where('category_id', function ($query) {
                $query->select('id')
                    ->from('finance_categories')
                    ->where('name', 'Monthly Saving Scheme')
                    ->first();
            })
            ->where('status', false)
            ->whereNotNull('payment_date')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        return [
            Stat::make('Total Members', $totalMembers)
                ->description('Active scheme members')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Paid Members', $paidMembers)
                ->description('Members who have paid this month')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Promised Members', $promisedMembers)
                ->description('Members who have promised to pay')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('warning'),
            Stat::make('Collection Rate', $totalMembers > 0 ? number_format(($paidMembers / $totalMembers) * 100, 1) . '%' : '0%')
                ->description('Percentage of members who have paid')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
            Stat::make('Total Expected', '₹' . number_format($totalExpected, 2))
                ->description('Expected collection this month')
                ->descriptionIcon('heroicon-m-currency-rupee')
                ->color('info'),
            Stat::make('Total Collected', '₹' . number_format($totalCollected, 2))
                ->description('Actual collection this month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($totalCollected >= $totalExpected ? 'success' : 'danger'),
            Stat::make('Total Promised', '₹' . number_format($totalPromised, 2))
                ->description('Amount promised to be paid')
                ->descriptionIcon('heroicon-m-hand-raised')
                ->color('warning'),
        ];
    }
}
