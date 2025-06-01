<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlySavingTransactionResource\Pages;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class MonthlySavingTransactionResource extends Resource
{
    protected static ?string $model = FinanceTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Monthly Saving Scheme';

    protected static ?string $navigationLabel = 'Monthly Savings';


    protected static ?string $modelLabel = 'Monthly Saving Transaction';

    protected static ?string $pluralModelLabel = 'Monthly Saving Transactions';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('category', function ($query) {
                $query->where('name', 'Monthly Saving Scheme');
            })
            ->where('type', 'income')
            ->latest('date');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Expected Payment Date')
                            ->helperText('When the member says they will make the payment')
                            ->nullable()
                            ->live(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\Select::make('member_id')
                            ->label('Member')
                            ->relationship(
                                name: 'member',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query
                                    ->where('id', '!=', 1)
                                    ->where('is_monthly_saving_scheme_member', true)
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('Select the member who is making the payment'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Payment Method')
                    ->schema([
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'cheque' => 'Cheque',
                                'upi' => 'UPI',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('reference_number')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Payment Status')
                    ->schema([
                        Forms\Components\Toggle::make('status')
                            ->label('Payment Received')
                            ->helperText('Mark this if the payment has been received')
                            ->default(true)
                            ->live(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn (Forms\Get $get) => $get('payment_date') !== null),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Hidden::make('type')
                            ->default('income'),
                        Forms\Components\Hidden::make('category_id')
                            ->default(fn () => FinanceCategory::where('name', 'Monthly Saving Scheme')->first()?->id),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Expected Payment')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) =>
                        $record->payment_date && $record->payment_date->isPast()
                            ? 'danger'
                            : 'success'
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn ($record) => $record->status ? 'success' : 'warning')
                    ->formatStateUsing(fn ($record) => $record->status ? 'Paid' : 'Promised'),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Member')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Recorded By')
                    ->searchable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('member')
                    ->relationship('member', 'name')
                    ->label('Member')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'cheque' => 'Cheque',
                        'upi' => 'UPI',
                        'other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-m-check-circle')
                    ->action(function ($record) {
                        $record->update([
                            'status' => true,
                            'date' => now(),
                        ]);

                        Notification::make()
                            ->title('Payment Marked as Received')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => !$record->status && $record->payment_date)
                    ->color('success'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonthlySavingTransactions::route('/'),
            'create' => Pages\CreateMonthlySavingTransaction::route('/create'),
            'edit' => Pages\EditMonthlySavingTransaction::route('/{record}/edit'),
        ];
    }
}
