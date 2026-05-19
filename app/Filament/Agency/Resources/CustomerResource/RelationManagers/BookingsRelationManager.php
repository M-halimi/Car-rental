<?php

namespace App\Filament\Agency\Resources\CustomerResource\RelationManagers;

use App\Models\Booking;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookingsRelationManager extends RelationManager
{
    protected static string $model = Booking::class;

    protected static string $relationship = 'bookings';

    protected static ?string $label = 'Booking';

    protected static ?string $pluralLabel = 'Bookings';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $agencyId = $user?->agency?->id;

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle'),
                TextColumn::make('pickup_date')
                    ->label('Pickup Date')
                    ->date(),
                TextColumn::make('return_date')
                    ->label('Return Date')
                    ->date(),
                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('MAD'),
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
            ->modifyQueryUsing(fn ($query) => $query
                ->whereHas('vehicle', fn ($q) => $q->where('agency_id', $agencyId))
            )
            ->actions([
                EditAction::make(),
            ]);
    }
}
