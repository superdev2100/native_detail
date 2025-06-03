<?php

namespace App\Filament\Resources\MonthlySavingTransactionResource\Pages;

use App\Filament\Resources\MonthlySavingTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlySavingTransaction extends EditRecord
{
    protected static string $resource = MonthlySavingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update the member's last payment date
        \App\Models\User::find($data['member_id'])->update([
            'last_payment_date' => $data['date']
        ]);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
