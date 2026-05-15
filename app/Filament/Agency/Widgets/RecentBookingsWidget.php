<?php

namespace App\Filament\Agency\Widgets;

use App\Filament\Agency\Resources\BookingResource;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentBookingsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Recent Bookings';
    }

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $agencyId = $user?->agency?->id;

        return $table
            ->query(
                Booking::query()
                    ->whereHas('vehicle', fn ($query) => $query->where('agency_id', $agencyId))
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle'),
                TextColumn::make('customer.user.name')
                    ->label('Customer'),
                TextColumn::make('pickup_date')
                    ->label('Pickup')
                    ->date(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'active' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn (Booking $record) => BookingResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
