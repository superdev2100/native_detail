<?php

namespace App\Filament\Resources\FinanceTagResource\Pages;

use App\Filament\Resources\FinanceTagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFinanceTag extends CreateRecord
{
    protected static string $resource = FinanceTagResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
