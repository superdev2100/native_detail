<?php

namespace App\Filament\Resources\MonthlySavingTransactionResource\Pages;

use App\Filament\Resources\MonthlySavingTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMonthlySavingTransaction extends CreateRecord
{
    protected static string $resource = MonthlySavingTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

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
