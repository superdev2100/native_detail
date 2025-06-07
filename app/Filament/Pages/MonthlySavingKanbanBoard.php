<?php

namespace App\Filament\Pages;

use App\Enums\MonthlySavingStatus;
use App\Models\FinanceTransaction;
use Illuminate\Database\Eloquent\Model;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Carbon\Carbon;
use Filament\Actions\Action;
use App\Enums\LineStatus;

class MonthlySavingKanbanBoard extends KanbanBoard
{
    // protected static string $model = Model::class;
    protected static string $model = FinanceTransaction::class;
    protected static string $statusEnum = LineStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Monthly Savings';
    protected static ?string $title = 'Monthly Savings Kanban Board';

    protected static string $recordTitleAttribute = 'date';
    protected static string $recordStatusAttribute = 'line_status';

    public ?array $data = [];
    public $selectedMonth;
    public $selectedYear;

    public function mount(): void
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter')
                ->form([
                    Grid::make(2)
                        ->schema([
                            Select::make('month')
                                ->label('Month')
                                ->options([
                                    1 => 'January',
                                    2 => 'February',
                                    3 => 'March',
                                    4 => 'April',
                                    5 => 'May',
                                    6 => 'June',
                                    7 => 'July',
                                    8 => 'August',
                                    9 => 'September',
                                    10 => 'October',
                                    11 => 'November',
                                    12 => 'December',
                                ])
                                ->default($this->selectedMonth)
                                ->live(),
                            Select::make('year')
                                ->label('Year')
                                ->options(function () {
                                    $years = [];
                                    $currentYear = now()->year;
                                    for ($i = $currentYear - 2; $i <= $currentYear + 2; $i++) {
                                        $years[$i] = $i;
                                    }
                                    return $years;
                                })
                                ->default($this->selectedYear)
                                ->live(),
                        ]),
                ])
                ->action(function (array $data): void {
                    $this->selectedMonth = $data['month'];
                    $this->selectedYear = $data['year'];
                })
                ->label('Filter: ' . Carbon::create($this->selectedYear, $this->selectedMonth, 1)->format('F Y'))
                ->icon('heroicon-m-funnel'),
        ];
    }

    protected function records(): Collection
    {
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        return FinanceTransaction::query()
            ->where('type', 'income')
            ->whereHas('category', function ($query) {
                $query->where('name', 'Monthly Saving Scheme');
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->with('member')
            ->get();
    }

    // protected function getEditModalFormSchema(null|int $recordId): array
    // {
    //     return [
    //         TextInput::make('description')
    //             ->required(),
    //         TextInput::make('amount')
    //             ->numeric()
    //             ->required(),
    //         DatePicker::make('payment_date')
    //             ->required(),
    //         Select::make('status')
    //             ->options(MonthlySavingStatus::class)
    //             ->required(),
    //     ];
    // }

    protected function getRecordTitle(Model $record): string
    {
        return $record->member?->name . ' - ' . $record->date->format('d-m-Y') ?? 'Unknown Date';
    }
}
