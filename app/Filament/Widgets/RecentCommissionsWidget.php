<?php

namespace App\Filament\Widgets;

use App\Models\BookingCommission;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Number;

class RecentCommissionsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                BookingCommission::query()
                    ->with(['booking', 'agency'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('booking_id')
                    ->label('Booking')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->sortable(),
                TextColumn::make('agency.name')
                    ->label('Agency')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('total_booking_amount')
                    ->label('Gross')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD'))
                    ->sortable(),
                TextColumn::make('commission_amount')
                    ->label('Commission')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD'))
                    ->sortable()
                    ->color('warning'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'calculated' => 'info',
                        'pending' => 'gray',
                        'void' => 'danger',
                        'disputed' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('calculated_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
