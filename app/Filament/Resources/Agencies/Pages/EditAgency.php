<?php

namespace App\Filament\Resources\Agencies\Pages;

use App\Filament\Resources\Agencies\AgencyResource;
use App\Services\AgencyService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAgency extends EditRecord
{
    protected static string $resource = AgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
