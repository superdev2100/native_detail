<?php

namespace App\Filament\Resources\FinanceTagResource\Pages;

use App\Filament\Resources\FinanceTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinanceTags extends ListRecords
{
    protected static string $resource = FinanceTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
