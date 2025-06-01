<?php

namespace App\Filament\Resources\FinanceTransactionResource\Pages;

use App\Filament\Resources\FinanceTransactionResource;
use App\Filament\Widgets\FinanceSummary;
use App\Models\FinanceTransaction;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;

class ListFinanceTransactions extends ListRecords
{
    protected static string $resource = FinanceTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FinanceSummary::class,
        ];
    }

    protected function getTableFiltersLayout(): ?string
    {
        return FiltersLayout::AboveContent;
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-m-banknotes')
                ->badge(FinanceTransaction::count()),
            'income' => Tab::make('Income')
                ->icon('heroicon-m-arrow-trending-up')
                ->badge(FinanceTransaction::where('type', 'income')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'income')),
            'expense' => Tab::make('Expense')
                ->icon('heroicon-m-arrow-trending-down')
                ->badge(FinanceTransaction::where('type', 'expense')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'expense')),
        ];
    }
}
