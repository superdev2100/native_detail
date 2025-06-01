<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeopleResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class PeopleResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Village People';

    protected static ?string $modelLabel = 'Person';

    protected static ?string $pluralModelLabel = 'People';

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
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('date_of_birth'),
                        Forms\Components\TextInput::make('age')
                            ->numeric(),
                        Forms\Components\TextInput::make('door_number'),
                        Forms\Components\TextInput::make('aadhar_number')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel(),
                        Forms\Components\Select::make('marital_status')
                            ->options([
                                'single' => 'Single',
                                'married' => 'Married',
                                'divorced' => 'Divorced',
                                'widowed' => 'Widowed',
                            ]),
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
                        Forms\Components\Select::make('disability_status')
                            ->options([
                                'none' => 'None',
                                'physical' => 'Physical',
                                'visual' => 'Visual',
                                'hearing' => 'Hearing',
                                'intellectual' => 'Intellectual',
                            ]),
                        Forms\Components\TextInput::make('voter_id')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('ration_card_number')
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

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
                    ])->columns(2),

                Forms\Components\Section::make('Education Information')
                    ->schema([
                        Forms\Components\Toggle::make('is_student')
                            ->label('Is Student?'),
                        Forms\Components\Select::make('education.education_level')
                            ->options([
                                'primary' => 'Primary',
                                'secondary' => 'Secondary',
                                'higher_secondary' => 'Higher Secondary',
                                'graduate' => 'Graduate',
                                'post_graduate' => 'Post Graduate',
                                'phd' => 'PhD',
                            ]),
                        Forms\Components\TextInput::make('education.school_name'),
                        Forms\Components\TextInput::make('education.college_name'),
                        Forms\Components\TextInput::make('education.course_name'),
                        Forms\Components\TextInput::make('education.current_class'),
                        Forms\Components\TextInput::make('education.current_school'),
                        Forms\Components\TextInput::make('education.scholarship_status'),
                    ])->columns(2),

                Forms\Components\Section::make('Occupation Information')
                    ->schema([
                        Forms\Components\Toggle::make('is_employed')
                            ->label('Is Employed?'),
                        Forms\Components\Select::make('occupation.occupation_type')
                            ->options([
                                'government' => 'Government',
                                'private' => 'Private',
                                'self_employed' => 'Self Employed',
                                'unemployed' => 'Unemployed',
                                'student' => 'Student',
                                'retired' => 'Retired',
                            ]),
                        Forms\Components\TextInput::make('occupation.company_name'),
                        Forms\Components\TextInput::make('occupation.job_title'),
                        Forms\Components\TextInput::make('occupation.monthly_income')
                            ->numeric(),
                        Forms\Components\TextInput::make('occupation.work_location'),
                        Forms\Components\TextInput::make('occupation.work_experience')
                            ->numeric(),
                        Forms\Components\TextInput::make('occupation.skills'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(function (User $record) {
                        if ($record->children()->exists()) {
                            return $record->gender === 'male' ? 'Father' : 'Mother';
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->sortable(),
                Tables\Columns\TextColumn::make('door_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aadhar_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_student')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_employed')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('marital_status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('blood_group')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disability_status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('voter_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ration_card_number')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),
                Tables\Filters\SelectFilter::make('is_student')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),
                Tables\Filters\SelectFilter::make('is_employed')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasPermission('edit_people')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasPermission('delete_people')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasPermission('delete_people')),
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
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePeople::route('/create'),
            'edit' => Pages\EditPeople::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('id', '!=', 1); // Exclude admin user
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermission('view_people');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermission('create_people');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->hasPermission('edit_people');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->hasPermission('delete_people');
    }
}
