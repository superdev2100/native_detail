<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PeopleStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total People', User::where('id', '!=', 1)->count())
                ->description('All village residents')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->url(route('filament.admin.resources.people.index')),

            Stat::make('Male', User::where('id', '!=', 1)->where('gender', 'male')->count())
                ->description('Male population')
                ->descriptionIcon('heroicon-m-user')
                ->color('info')
                ->url(route('filament.admin.resources.people.index', ['tableFilters[gender][value]' => 'male'])),

            Stat::make('Female', User::where('id', '!=', 1)->where('gender', 'female')->count())
                ->description('Female population')
                ->descriptionIcon('heroicon-m-user')
                ->color('warning')
                ->url(route('filament.admin.resources.people.index', ['tableFilters[gender][value]' => 'female'])),

            Stat::make('Students', User::where('id', '!=', 1)->where('is_student', true)->count())
                ->description('Currently studying')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->url(route('filament.admin.resources.people.index', ['tableFilters[is_student][value]' => '1'])),

            Stat::make('Working Professionals', User::where('id', '!=', 1)->where('is_employed', true)->count())
                ->description('Currently employed')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info')
                ->url(route('filament.admin.resources.people.index', ['tableFilters[is_employed][value]' => '1'])),
        ];
    }
}
