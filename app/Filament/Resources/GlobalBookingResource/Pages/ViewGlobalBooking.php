<?php

namespace App\Filament\Resources\GlobalBookingResource\Pages;

use App\Enums\BookingStatus;
use App\Filament\Resources\GlobalBookingResource\GlobalBookingResource;
use App\Models\Booking;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ViewGlobalBooking extends ViewRecord
{
    protected static string $resource = GlobalBookingResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Booking Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Booking #')
                            ->weight(FontWeight::Bold)
                            ->size('lg'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => BookingStatus::tryFrom($state)?->color() ?? 'gray'),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime(),
                        TextEntry::make('vehicle.agency.name')
                            ->label('Agency'),
                        TextEntry::make('vehicle.plate_number')
                            ->label('Vehicle')
                            ->helperText(fn (Booking $record): string => "{$record->vehicle?->brand} {$record->vehicle?->model} ({$record->vehicle?->year})"),
                        TextEntry::make('customer.user.name')
                            ->label('Customer'),
                        TextEntry::make('pickupCity.name')
                            ->label('Pickup City'),
                        TextEntry::make('returnCity.name')
                            ->label('Return City'),
                        TextEntry::make('pickup_date')
                            ->label('Pickup Date')
                            ->date(),
                        TextEntry::make('return_date')
                            ->label('Return Date')
                            ->date(),
                    ]),

                Section::make('Financial Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('price_per_day')
                            ->label('Price per Day')
                            ->money('MAD'),
                        TextEntry::make('total_days')
                            ->label('Total Days'),
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('MAD'),
                        TextEntry::make('extras_price')
                            ->label('Extras')
                            ->money('MAD'),
                        TextEntry::make('total_price')
                            ->label('Total Price')
                            ->money('MAD'),
                        TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('MAD')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('deposit_amount')
                            ->label('Deposit')
                            ->money('MAD'),
                        TextEntry::make('deposit_status')
                            ->label('Deposit Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'refunded' => 'info',
                                'waived' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('discount')
                            ->label('Discount')
                            ->money('MAD'),
                    ]),

                Section::make('Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('')
                            ->markdown(),
                        TextEntry::make('cancellation_reason')
                            ->label('Cancellation Reason')
                            ->visible(fn (Booking $record): bool => $record->status === 'cancelled'),
                    ]),
            ]);
    }
}
