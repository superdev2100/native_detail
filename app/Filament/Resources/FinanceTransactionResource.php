<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinanceTransactionResource\Pages;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinanceTransactionResource extends Resource
{
    protected static ?string $model = FinanceTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Finance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Expected Payment Date')
                            ->helperText('When the member says they will make the payment')
                            ->nullable(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'income' => 'Income',
                                'expense' => 'Expense',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('category_id', null)),
                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(function (Forms\Get $get) {
                                return FinanceCategory::where('type', $get('type'))
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('tags')
                            ->multiple()
                            ->relationship('tags', 'name')
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\ColorPicker::make('color')
                                    ->required()
                                    ->default('#6B7280'),
                            ])
                            ->badge()
                            ->color(fn ($state) => $state?->color ?? '#6B7280')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Payment Information')
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
                    ])->columns(2),

                Forms\Components\Section::make('Member Information')
                    ->schema([
                        Forms\Components\Select::make('member_id')
                            ->label('Member')
                            ->options(User::query()
                                ->where('id', '!=', 1) // Exclude admin
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Forms\Get $get) =>
                                $get('type') === 'income'
                            )
                            ->helperText('Select the member who is making the payment'),
                    ])->columns(1),
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
                Tables\Columns\TextColumn::make('amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Member')
                    ->searchable()
                    ->visible(fn ($record) =>
                        $record &&
                        $record->type === 'income'
                    ),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Recorded By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tags.name')
                    ->badge()
                    ->searchable()
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->formatStateUsing(fn ($state) => $state)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'bank_transfer' => 'Bank Transfer',
                        'cheque' => 'Cheque',
                        'upi' => 'UPI',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('member')
                    ->relationship('member', 'name')
                    ->label('Member'),
                Tables\Filters\SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinanceTransactions::route('/'),
            'create' => Pages\CreateFinanceTransaction::route('/create'),
            'edit' => Pages\EditFinanceTransaction::route('/{record}/edit'),
        ];
    }
}
