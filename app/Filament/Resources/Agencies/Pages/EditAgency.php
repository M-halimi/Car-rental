<?php

namespace App\Filament\Resources\Agencies\Pages;

use App\Filament\Resources\Agencies\AgencyResource;
use App\Models\Agency;
use App\Services\AgencyService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAgency extends EditRecord
{
    protected static string $resource = AgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('View Details')
                ->icon('heroicon-o-eye')
                ->url(fn () => AgencyResource::getUrl('view', ['record' => $this->record])),
            Action::make('extend_trial')
                ->label('Extend Trial')
                ->icon('heroicon-o-clock')
                ->color('info')
                ->form([
                    DatePicker::make('new_end_date')
                        ->label('New Subscription End Date')
                        ->required()
                        ->default(fn () => now()->addDays(30)),
                ])
                ->action(function (array $data, Agency $record) {
                    $record->update([
                        'subscription_end_date' => $data['new_end_date'],
                        'status' => 'active',
                        'is_active' => true,
                    ]);
                    Notification::make()
                        ->title('Trial extended successfully')
                        ->success()
                        ->send();
                }),
            Action::make('change_plan')
                ->label('Change Plan')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Select::make('subscription_plan')
                        ->label('Subscription Plan')
                        ->options([
                            'basic' => 'Basic',
                            'premium' => 'Premium',
                            'enterprise' => 'Enterprise',
                        ])
                        ->required()
                        ->default(fn (Agency $record) => $record->subscription_plan),
                    DatePicker::make('subscription_start_date')
                        ->label('Start Date')
                        ->default(fn (Agency $record) => $record->subscription_start_date),
                    DatePicker::make('subscription_end_date')
                        ->label('End Date')
                        ->default(fn (Agency $record) => $record->subscription_end_date),
                ])
                ->action(function (array $data, Agency $record) {
                    $record->update([
                        'subscription_plan' => $data['subscription_plan'],
                        'subscription_start_date' => $data['subscription_start_date'] ?? $record->subscription_start_date,
                        'subscription_end_date' => $data['subscription_end_date'] ?? $record->subscription_end_date,
                    ]);
                    Notification::make()
                        ->title('Plan updated successfully')
                        ->success()
                        ->send();
                }),
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
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
