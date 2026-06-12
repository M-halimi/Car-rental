<?php

namespace App\Filament\Widgets;

use App\Models\Agency;
use App\Services\FinanceService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Number;

class RevenuePerAgencyWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $finance = app(FinanceService::class);
        $rows = $finance->getRevenuePerAgency();

        return $table
            ->query(
                Agency::query()
                    ->whereIn('id', $rows->pluck('agency.id'))
                    ->orderBy('name')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Agency')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('gross')
                    ->label('Gross Bookings')
                    ->state(fn ($record) => $rows->firstWhere('agency.id', $record->id)['total_gross'] ?? 0)
                    ->formatStateUsing(fn ($state) => Number::currency($state, 'MAD'))
                    ->sortable(),
                TextColumn::make('commission')
                    ->label('Total Commission')
                    ->state(fn ($record) => $rows->firstWhere('agency.id', $record->id)['total_commission'] ?? 0)
                    ->formatStateUsing(fn ($state) => Number::currency($state, 'MAD'))
                    ->sortable()
                    ->color('warning'),
                TextColumn::make('paid')
                    ->label('Paid')
                    ->state(fn ($record) => $rows->firstWhere('agency.id', $record->id)['paid'] ?? 0)
                    ->formatStateUsing(fn ($state) => Number::currency($state, 'MAD'))
                    ->sortable()
                    ->color('success'),
                TextColumn::make('unpaid')
                    ->label('Unpaid')
                    ->state(fn ($record) => $rows->firstWhere('agency.id', $record->id)['unpaid'] ?? 0)
                    ->formatStateUsing(fn ($state) => Number::currency($state, 'MAD'))
                    ->sortable()
                    ->color('danger'),
                TextColumn::make('count')
                    ->label('Commissions')
                    ->state(fn ($record) => $rows->firstWhere('agency.id', $record->id)['commission_count'] ?? 0)
                    ->alignCenter(),
            ]);
    }
}
