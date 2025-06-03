<?php

namespace App\Filament\Widgets;

use App\Models\FinanceTransaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\View\View;

class TopContributorsTable extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Contributors';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                FinanceTransaction::query()
                    ->select('member_id')
                    ->selectRaw('SUM(amount) as total_amount')
                    ->with(['member', 'category', 'tags'])
                    ->where('type', 'income')
                    ->where('status', true)
                    ->groupBy('member_id')
                    ->orderByDesc('total_amount')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Contributor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Contribution')
                    ->money('INR')
                    ->sortable(),
            ])
            ->actions([
                Action::make('details')
                    ->label('Details')
                    ->icon('heroicon-m-information-circle')
                    ->modalContent(function (Model $record): View {
                        $transactions = FinanceTransaction::where('member_id', $record->member_id)
                            ->where('type', 'income')
                            ->where('status', true)
                            ->with(['category', 'tags'])
                            ->get();

                        $categories = $transactions->groupBy('category.name')
                            ->map(fn ($transactions) => $transactions->sum('amount'));

                        $tags = $transactions->flatMap->tags
                            ->groupBy('name')
                            ->map(fn ($tagCollection) => $tagCollection->count());

                        return view('filament.widgets.contributor-details', [
                            'categories' => $categories,
                            'tags' => $tags,
                        ]);
                    })
                    ->modalWidth('4xl')
                    ->modalHeading(fn (Model $record) => "Details for {$record->member->name}")
            ])
            ->defaultSort('total_amount', 'desc')
            ->paginated(false);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->member_id;
    }
}
