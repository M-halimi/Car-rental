<?php

namespace App\Filament\Resources\Agencies\Tables;

use App\Filament\Resources\Agencies\AgencyResource;
use App\Models\Agency;
use App\Services\AgencyService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AgenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn () => asset('images/default-agency.png'))
                    ->size(40),
                TextColumn::make('name')
                    ->label('Agency')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Agency $record) => $record->email),
                TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subscription_plan')
                    ->label('Plan')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : '—')
                    ->color(fn (?string $state): string => match ($state) {
                        'premium' => 'success',
                        'enterprise' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'danger',
                        'expired' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('vehicles_count')
                    ->label('Cars')
                    ->counts('vehicles')
                    ->sortable(),
                TextColumn::make('bookings_count')
                    ->label('Reservations')
                    ->counts('bookings')
                    ->sortable(),
                TextColumn::make('subscription_end_date')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'expired' => 'Expired',
                    ]),
                SelectFilter::make('subscription_plan')
                    ->label('Plan')
                    ->options([
                        'basic' => 'Basic',
                        'premium' => 'Premium',
                        'enterprise' => 'Enterprise',
                    ]),
                Filter::make('expired')
                    ->label('Expired Subscriptions')
                    ->query(fn ($query) => $query->where('subscription_end_date', '<', now())),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Agency $record): string => AgencyResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (Agency $record): string => AgencyResource::getUrl('edit', ['record' => $record])),
                Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn (Agency $record): bool => $record->isActive())
                    ->requiresConfirmation()
                    ->action(fn (Agency $record) => app(AgencyService::class)->suspend($record)),
                Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->visible(fn (Agency $record): bool => ! $record->isActive())
                    ->requiresConfirmation()
                    ->action(fn (Agency $record) => app(AgencyService::class)->activate($record)),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Action::make('suspendSelected')
                        ->label('Suspend Selected')
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (array $records) => app(AgencyService::class)->suspend(Agency::find($records))),
                    Action::make('activateSelected')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-play-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (array $records) => app(AgencyService::class)->activate(Agency::find($records))),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
