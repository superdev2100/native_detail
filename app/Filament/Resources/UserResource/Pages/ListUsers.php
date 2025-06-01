<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use App\Models\FinanceTransaction;
use App\Models\FinanceCategory;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('viewSavingDetails')
                ->label('View Saving Details')
                ->icon('heroicon-m-eye')
                ->action(function ($record, $livewire) {
                    $livewire->viewingSavingDetailsFor = $record->id;
                    $livewire->dispatch('openSavingDetailsModal');
                })
                ->modalHeading(fn ($record) => "Saving Details for {$record->name}")
                ->modalContent(function ($record) {
                    $payments = FinanceTransaction::where('member_id', $record->id)
                        ->where('category_id', function ($query) {
                            $query->select('id')
                                ->from('finance_categories')
                                ->where('name', 'Monthly Saving Scheme')
                                ->first();
                        })
                        ->orderBy('date', 'desc')
                        ->get();

                    return view('filament.pages.partials.monthly-saving-payments-modal', [
                        'payments' => $payments,
                        'member' => $record,
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
        ];
    }
}
