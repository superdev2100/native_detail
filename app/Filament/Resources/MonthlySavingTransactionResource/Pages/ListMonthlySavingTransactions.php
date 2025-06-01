<?php

namespace App\Filament\Resources\MonthlySavingTransactionResource\Pages;

use App\Filament\Resources\MonthlySavingTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonthlySavingTransactions extends ListRecords
{
    protected static string $resource = MonthlySavingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
