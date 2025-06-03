<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->visibleOn('create'),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->nullable(),
                        Forms\Components\TextInput::make('age')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('door_number')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('aadhar_number')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Employment & Education')
                    ->schema([
                        Forms\Components\Toggle::make('is_student')
                            ->required()
                            ->live(),
                        Forms\Components\Toggle::make('is_employed')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Student Information')
                    ->schema([
                        Forms\Components\TextInput::make('studentDetails.school_name')
                            ->label('School Name')
                            ->maxLength(255)
                            ->required(fn (Forms\Get $get) => $get('is_student'))
                            ->visible(fn (Forms\Get $get) => $get('is_student'))
                            ->dehydrated(fn (Forms\Get $get) => $get('is_student')),
                        Forms\Components\TextInput::make('studentDetails.school_address')
                            ->label('School Address')
                            ->maxLength(255)
                            ->required(fn (Forms\Get $get) => $get('is_student'))
                            ->visible(fn (Forms\Get $get) => $get('is_student'))
                            ->dehydrated(fn (Forms\Get $get) => $get('is_student')),
                        Forms\Components\TextInput::make('studentDetails.current_standard')
                            ->label('Current Standard')
                            ->maxLength(255)
                            ->required(fn (Forms\Get $get) => $get('is_student'))
                            ->visible(fn (Forms\Get $get) => $get('is_student'))
                            ->dehydrated(fn (Forms\Get $get) => $get('is_student')),
                        Forms\Components\Select::make('skills')
                            ->label('Skills / Interests')
                            ->multiple()
                            ->relationship('skills', 'name')
                            ->preload()
                            ->visible(fn (Forms\Get $get) => $get('is_student')),
                    ])->columns(2)
                    ->visible(fn (Forms\Get $get) => $get('is_student')),

                Forms\Components\Section::make('Family Information')
                    ->schema([
                        Forms\Components\Select::make('father_id')
                            ->relationship('father', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('mother_id')
                            ->relationship('mother', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('marital_status')
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                                'divorced' => 'Divorced',
                                'widowed' => 'Widowed',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Select::make('blood_group')
                            ->options([
                                'A+' => 'A+',
                                'A-' => 'A-',
                                'B+' => 'B+',
                                'B-' => 'B-',
                                'AB+' => 'AB+',
                                'AB-' => 'AB-',
                                'O+' => 'O+',
                                'O-' => 'O-',
                            ]),
                        Forms\Components\TextInput::make('disability_status')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('voter_id')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ration_card_number')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Monthly Saving Scheme')
                    ->schema([
                        Forms\Components\Toggle::make('is_monthly_saving_scheme_member')
                            ->label('Is Monthly Saving Scheme Member')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (!$state) {
                                    $set('monthly_saving_amount', null);
                                    $set('last_payment_date', null);
                                }
                            }),
                        Forms\Components\TextInput::make('monthly_saving_amount')
                            ->numeric()
                            ->prefix('₹')
                            ->visible(fn (Forms\Get $get) => $get('is_monthly_saving_scheme_member'))
                            ->required(fn (Forms\Get $get) => $get('is_monthly_saving_scheme_member')),
                        Forms\Components\DatePicker::make('last_payment_date')
                            ->visible(fn (Forms\Get $get) => $get('is_monthly_saving_scheme_member')),
                    ])->columns(3),

                Forms\Components\Toggle::make('status')
                    ->label('Active')
                    ->default(false)
                    ->helperText('Toggle to set the user as active or inactive'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('email')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('gender'),
                // Tables\Columns\TextColumn::make('age'),
                Tables\Columns\IconColumn::make('is_student')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_employed')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_monthly_saving_scheme_member')
                    ->label('Scheme Member')
                    ->boolean()
                    ->default(false),
                Tables\Columns\TextColumn::make('monthly_saving_amount')
                    ->money('INR')
                    ->default('-')
                    ->visible(fn ($record) => $record?->is_monthly_saving_scheme_member ?? false),
                Tables\Columns\TextColumn::make('last_payment_date')
                    ->date()
                    ->default('-')
                    ->visible(fn ($record) => $record?->is_monthly_saving_scheme_member ?? false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_monthly_saving_scheme_member')
                    ->label('Scheme Membership')
                    ->options([
                        true => 'Members',
                        false => 'Non-members',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('viewSavingDetails')
                    ->label('View Saving Details')
                    ->icon('heroicon-m-eye')
                    ->action(function ($record, $livewire) {
                        $livewire->viewingSavingDetailsFor = $record->id;
                        $livewire->dispatch('openSavingDetailsModal');
                    })
                    ->modalHeading(fn ($record) => "Saving Details for {$record->name}")
                    ->modalContent(function ($record) {
                        $payments = \App\Models\FinanceTransaction::where('member_id', $record->id)
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
                    ->modalCancelActionLabel('Close')
                    ->visible(fn ($record) => $record->is_monthly_saving_scheme_member),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id', '!=', 1) // Exclude admin user
            ->where('status', true); // Exclude records where status is false
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!isset($data['is_student']) || !$data['is_student']) {
            // If user is not a student, remove student details
            if (isset($data['studentDetails'])) {
                unset($data['studentDetails']);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->is_student) {
            // Create or update student details
            $record->studentDetails()->updateOrCreate(
                ['user_id' => $record->id],
                [
                    'school_name' => $this->data['studentDetails']['school_name'] ?? null,
                    'school_address' => $this->data['studentDetails']['school_address'] ?? null,
                    'current_standard' => $this->data['studentDetails']['current_standard'] ?? null,
                ]
            );
        } else {
            // Delete student details if user is not a student
            $record->studentDetails()->delete();
        }
    }
}
