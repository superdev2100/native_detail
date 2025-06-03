<?php

namespace App\Filament\Resources\FinanceTagResource\Pages;

use App\Filament\Resources\FinanceTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinanceTag extends EditRecord
{
    protected static string $resource = FinanceTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
