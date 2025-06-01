<?php

namespace App\Filament\Resources\FinanceTransactionResource\Pages;

use App\Filament\Resources\FinanceTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFinanceTransaction extends CreateRecord
{
    protected static string $resource = FinanceTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        // If this is a monthly saving scheme payment, update the member's last payment date
        if ($data['type'] === 'income' &&
            $data['category_id'] &&
            \App\Models\FinanceCategory::find($data['category_id'])?->name === 'Monthly Saving Scheme') {
            \App\Models\User::find($data['member_id'])->update([
                'last_payment_date' => $data['date']
            ]);
        }

        return $data;
    }
}
