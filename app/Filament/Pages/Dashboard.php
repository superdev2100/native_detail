<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FinanceCategoryChart;
use App\Filament\Widgets\FinanceOverview;
use App\Filament\Widgets\PeopleStatsOverview;
use App\Filament\Widgets\TopContributorsTable;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            // PeopleStatsOverview::class,
            // FinanceOverview::class,
            // FinanceCategoryChart::class,
            TopContributorsTable::class,
            FinanceSummary::class,
        ];
    }
}
