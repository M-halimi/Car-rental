<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource\ReportResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['moderated_by'] = auth()->id();
        $data['moderated_at'] = now();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resolve')
                ->label('Resolve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update([
                        'status' => 'resolved',
                        'moderated_by' => auth()->id(),
                        'moderated_at' => now(),
                    ]);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Action::make('dismiss')
                ->label('Dismiss')
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'dismissed',
                        'moderated_by' => auth()->id(),
                        'moderated_at' => now(),
                    ]);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
