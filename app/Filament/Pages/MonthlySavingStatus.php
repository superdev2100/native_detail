<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\FinanceTransaction;
use App\Models\FinanceCategory;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Filament\Widgets\MonthlySavingStats;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker as FormsDatePicker;

class MonthlySavingStatus extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $view = 'filament.pages.monthly-saving-status';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Monthly Saving Scheme';
    protected static ?string $title = 'Monthly Saving Status - Quick link';
    protected static ?string $navigationLabel = 'Saving Status - Quick link';
    protected static ?int $navigationSort = 3;

    public $startDate;
    public $endDate;
    public $selectedMonth;
    public $viewingPaymentsFor = null;

    protected $listeners = ['refresh-widgets' => '$refresh'];

    public function mount($startDate = null, $endDate = null): void
    {
        $this->selectedMonth = now()->format('Y-m');
        $this->startDate = $startDate ?? now()->startOfMonth();
        $this->endDate = $endDate ?? now()->endOfMonth();

        // Create category if it doesn't exist
        if (!FinanceCategory::where('name', 'Monthly Saving Scheme')->exists()) {
            FinanceCategory::create([
                'name' => 'Monthly Saving Scheme',
                'type' => 'income',
                'description' => 'Monthly contributions from scheme members',
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedMonth')
                    ->label('Select Month')
                    ->options(function () {
                        $months = [];
                        $start = now()->subMonths(12);
                        $end = now()->addMonths(1);

                        while ($start->lte($end)) {
                            $months[$start->format('Y-m')] = $start->format('F Y');
                            $start->addMonth();
                        }

                        return $months;
                    })
                    ->default(now()->format('Y-m'))
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $date = Carbon::createFromFormat('Y-m', $state);
                        $this->startDate = $date->startOfMonth();
                        $this->endDate = $date->endOfMonth();

                        // Reset the table
                        $this->resetTable();

                        // Refresh all widgets
                        $this->dispatch('refresh-widgets');

                        // Force refresh the page data
                        $this->dispatch('refresh');
                    }),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('is_monthly_saving_scheme_member', true)
                    ->where('id', '!=', 1) // Exclude admin
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Member Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('monthly_saving_amount')
                    ->label('Monthly Contribution')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('last_payment_date')
                    ->label('Last Payment Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->formatStateUsing(function ($record) {
                        $startDate = Carbon::parse($this->startDate);
                        $endDate = Carbon::parse($this->endDate);

                        // Check for actual payment
                        $hasPayment = FinanceTransaction::where('member_id', $record->id)
                            ->where('category_id', function ($query) {
                                $query->select('id')
                                    ->from('finance_categories')
                                    ->where('name', 'Monthly Saving Scheme')
                                    ->first();
                            })
                            ->whereBetween('date', [$startDate, $endDate])
                            ->exists();

                        // Check for promised payment
                        $hasPromisedPayment = FinanceTransaction::where('member_id', $record->id)
                            ->where('category_id', function ($query) {
                                $query->select('id')
                                    ->from('finance_categories')
                                    ->where('name', 'Monthly Saving Scheme')
                                    ->first();
                            })
                            ->whereNotNull('payment_date')
                            ->whereBetween('payment_date', [$startDate, $endDate])
                            ->whereNotExists(function ($subQuery) use ($startDate, $endDate) {
                                $subQuery->select('id')
                                    ->from('finance_transactions as ft2')
                                    ->whereColumn('ft2.member_id', 'finance_transactions.member_id')
                                    ->whereBetween('ft2.date', [$startDate, $endDate]);
                            })
                            ->exists();

                        if ($hasPayment) {
                            return 'Paid';
                        } elseif ($hasPromisedPayment) {
                            return 'Promised';
                        } else {
                            return 'Pending';
                        }
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Promised' => 'warning',
                        'Pending' => 'danger',
                    }),
                TextColumn::make('payment_history')
                    ->label('Payment History')
                    ->formatStateUsing(function ($record) {
                        $startDate = Carbon::parse($this->startDate)->subMonths(5);
                        $endDate = Carbon::parse($this->endDate);

                        $payments = FinanceTransaction::where('member_id', $record->id)
                            ->where('category_id', function ($query) {
                                $query->select('id')
                                    ->from('finance_categories')
                                    ->where('name', 'Monthly Saving Scheme')
                                    ->first();
                            })
                            ->whereBetween('date', [$startDate, $endDate])
                            ->get()
                            ->map(function ($payment) {
                                return $payment->date->format('M Y');
                            })
                            ->toArray();

                        return implode(', ', $payments);
                    }),
            ])
            ->actions([
                Action::make('recordPayment')
                    ->label('Record Payment')
                    ->icon('heroicon-m-banknotes')
                    ->form([
                        Hidden::make('member_id'),
                        FormsDatePicker::make('date')
                            ->label('Payment Date')
                            ->required()
                            ->default(now()),
                        FormsDatePicker::make('payment_date')
                            ->label('Expected Payment Date')
                            ->helperText('When the member says they will make the payment')
                            ->nullable(),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->default(function ($record) {
                                return $record->monthly_saving_amount;
                            }),
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'cheque' => 'Cheque',
                                'upi' => 'UPI',
                                'other' => 'Other',
                            ])
                            ->required(),
                        TextInput::make('reference_number')
                            ->label('Reference Number')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data, $record): void {
                        $category = FinanceCategory::where('name', 'Monthly Saving Scheme')->first();

                        if (!$category) {
                            Notification::make()
                                ->title('Error')
                                ->body('Monthly Saving Scheme category not found. Please contact administrator.')
                                ->danger()
                                ->send();
                            return;
                        }

                        FinanceTransaction::create([
                            'member_id' => $record->id,
                            'category_id' => $category->id,
                            'date' => $data['date'],
                            'payment_date' => $data['payment_date'],
                            'status' => !$data['payment_date'], // Set status to false if payment_date is set
                            'amount' => $data['amount'],
                            'type' => 'income',
                            'payment_method' => $data['payment_method'],
                            'reference_number' => $data['reference_number'],
                            'user_id' => auth()->id(),
                        ]);

                        $record->update([
                            'last_payment_date' => $data['date'],
                        ]);

                        Notification::make()
                            ->title('Payment Recorded')
                            ->body('Payment has been successfully recorded.')
                            ->success()
                            ->send();

                        $this->resetTable();
                        $this->dispatch('refresh-widgets');
                    })
                    ->modalHeading(fn ($record) => "Record Payment for {$record->name}")
                    ->modalSubmitActionLabel('Save Payment')
                    ->visible(fn ($record) => !$this->hasPaymentForCurrentMonth($record)),
                Action::make('markAsPaid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-m-check-circle')
                    ->action(function ($record) {
                        $transaction = FinanceTransaction::where('member_id', $record->id)
                            ->where('category_id', function ($query) {
                                $query->select('id')
                                    ->from('finance_categories')
                                    ->where('name', 'Monthly Saving Scheme')
                                    ->first();
                            })
                            ->whereNotNull('payment_date')
                            ->where('status', false)
                            ->latest()
                            ->first();

                        if ($transaction) {
                            $transaction->update([
                                'status' => true,
                                'date' => now(),
                            ]);

                            Notification::make()
                                ->title('Payment Marked as Received')
                                ->success()
                                ->send();

                            $this->resetTable();
                            $this->dispatch('refresh-widgets');
                        }
                    })
                    ->visible(function ($record) {
                        return FinanceTransaction::where('member_id', $record->id)
                            ->where('category_id', function ($query) {
                                $query->select('id')
                                    ->from('finance_categories')
                                    ->where('name', 'Monthly Saving Scheme')
                                    ->first();
                            })
                            ->whereNotNull('payment_date')
                            ->where('status', false)
                            ->exists();
                    }),
                Action::make('viewPayments')
                    ->label('View Payments')
                    ->icon('heroicon-m-eye')
                    ->action(function ($record, $livewire) {
                        $livewire->viewingPaymentsFor = $record->id;
                        $livewire->dispatch('openPaymentsModal');
                    })
                    ->modalHeading(fn ($record) => "Payment History for {$record->name}")
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
            ])
            ->defaultSort('name', 'asc')
            ->paginated(false);
    }

    protected function hasPaymentForCurrentMonth($record): bool
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        return FinanceTransaction::where('member_id', $record->id)
            ->where('category_id', function ($query) {
                $query->select('id')
                    ->from('finance_categories')
                    ->where('name', 'Monthly Saving Scheme')
                    ->first();
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->exists();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MonthlySavingStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        return [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }
}
