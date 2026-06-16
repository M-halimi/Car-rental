<?php

namespace App\Filament\Resources\GlobalBookingResource;

use App\Enums\BookingStatus;
use App\Filament\Resources\GlobalBookingResource\Pages\ListGlobalBookings;
use App\Filament\Resources\GlobalBookingResource\Pages\ViewGlobalBooking;
use App\Models\Booking;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class GlobalBookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Global Bookings';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'All Bookings';

    protected static ?string $slug = 'global-bookings';

    public static function table(Table $table): Table
    {
        return $table
            ->query(fn () => Booking::query()->with(['vehicle.agency', 'customer.user', 'pickupCity']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('vehicle.agency.name')
                    ->label('Agency')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->description(fn (Booking $record) => $record->vehicle?->brand.' '.$record->vehicle?->model)
                    ->searchable(),
                TextColumn::make('customer.user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('pickup_date')
                    ->label('Pickup')
                    ->date()
                    ->sortable(),
                TextColumn::make('return_date')
                    ->label('Return')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('MAD')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => BookingStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('vehicle.agency_id')
                    ->label('Agency')
                    ->relationship('vehicle.agency', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(BookingStatus::labels()),
                Filter::make('date_range')
                    ->label('Date Range')
                    ->form([
                        DatePicker::make('pickup_from')
                            ->label('Pickup From'),
                        DatePicker::make('pickup_until')
                            ->label('Pickup Until'),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['pickup_from'], fn ($q, $date) => $q->whereDate('pickup_date', '>=', $date))
                        ->when($data['pickup_until'], fn ($q, $date) => $q->whereDate('pickup_date', '<=', $date))),
                SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'plate_number')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction(fn (Booking $record): string => GlobalBookingResource::getUrl('view', ['record' => $record]))
            ->actions([
                ViewAction::make()
                    ->url(fn (Booking $record): string => GlobalBookingResource::getUrl('view', ['record' => $record])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGlobalBookings::route('/'),
            'view' => ViewGlobalBooking::route('/{record}'),
        ];
    }
}
