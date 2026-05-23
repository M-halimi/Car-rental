<?php

namespace App\Filament\Agency\Resources\CustomerResource\RelationManagers;

use App\Filament\Agency\Resources\PaymentResource;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;


class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payments';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();

        $agencyId = $user?->agency?->id;

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('booking_id')
                    ->label('Booking')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => Number::currency($state ?? 0, 'MAD'))
                    ->sortable(),
                TextColumn::make('payment_type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'partial' => 'warning',
                        'pending' => 'gray',
                        'refunded' => 'info',
                        'failed' => 'danger',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereHas('booking.vehicle', fn ($q) => $q->where('agency_id', $agencyId))
            )
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make()
                    ->url(fn ($record) => PaymentResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
