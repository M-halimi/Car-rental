<?php

namespace App\Filament\Resources\Agencies\Pages;

use App\Filament\Resources\Agencies\AgencyResource;
use App\Models\Agency;
use App\Services\AgencyService;
use Filament\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ViewAgency extends ViewRecord
{
    protected static string $resource = AgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit Agency')
                ->icon('heroicon-o-pencil-square')
                ->url(fn () => AgencyResource::getUrl('edit', ['record' => $this->record])),
            Action::make('suspend')
                ->label('Suspend')
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->visible(fn () => $this->record->isActive())
                ->requiresConfirmation()
                ->action(fn () => app(AgencyService::class)->suspend($this->record)),
            Action::make('activate')
                ->label('Activate')
                ->icon('heroicon-o-play-circle')
                ->color('success')
                ->visible(fn () => ! $this->record->isActive())
                ->requiresConfirmation()
                ->action(fn () => app(AgencyService::class)->activate($this->record)),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Agency Information')
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('logo')
                            ->label('')
                            ->circular()
                            ->defaultImageUrl(fn () => asset('images/default-agency.png'))
                            ->columnSpan(1)
                            ->alignCenter(),
                        Grid::make(2)
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Agency Name')
                                    ->weight(FontWeight::Bold)
                                    ->size('lg'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'suspended' => 'danger',
                                        'expired' => 'gray',
                                        default => 'gray',
                                    }),
                                TextEntry::make('email')
                                    ->label('Email'),
                                TextEntry::make('phone')
                                    ->label('Phone'),
                                TextEntry::make('city.name')
                                    ->label('City'),
                                TextEntry::make('country')
                                    ->label('Country'),
                                TextEntry::make('address')
                                    ->label('Address')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Owner Information')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Owner Name'),
                        TextEntry::make('user.email')
                            ->label('Owner Email'),
                    ]),

                Section::make('Subscription')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('subscription_plan')
                            ->label('Plan')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'No Plan')
                            ->color(fn (?string $state): string => match ($state) {
                                'premium' => 'success',
                                'enterprise' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('subscription_start_date')
                            ->label('Start Date')
                            ->date(),
                        TextEntry::make('subscription_end_date')
                            ->label('End Date')
                            ->date()
                            ->color(fn (?string $state, Agency $record): string => $record->isExpired() ? 'danger' : 'success'),
                        IconEntry::make('is_active')
                            ->label('Trial Active')
                            ->boolean()
                            ->visible(fn (Agency $record): bool => $record->subscription_start_date === null),
                    ]),

                Section::make('Company Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('registration_number')
                            ->label('Registration No.'),
                        TextEntry::make('tax_id')
                            ->label('Tax ID'),
                        TextEntry::make('legal_form')
                            ->label('Legal Form')
                            ->formatStateUsing(fn (?string $state): string => $state ? strtoupper($state) : '—'),
                        TextEntry::make('capital')
                            ->label('Capital')
                            ->money('MAD'),
                        TextEntry::make('member_since')
                            ->label('Member Since'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),

                Section::make('Agency Statistics')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('vehicles_count')
                            ->label('Total Vehicles')
                            ->counts('vehicles')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('bookings_count')
                            ->label('Total Bookings')
                            ->counts('bookings')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('rentals_completed_count')
                            ->label('Completed Rentals')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('response_rate')
                            ->label('Response Rate'),
                    ]),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
