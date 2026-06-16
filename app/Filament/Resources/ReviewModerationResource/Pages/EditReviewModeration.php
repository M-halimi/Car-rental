<?php

namespace App\Filament\Resources\ReviewModerationResource\Pages;

use App\Filament\Resources\ReviewModerationResource\ReviewModerationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditReviewModeration extends EditRecord
{
    protected static string $resource = ReviewModerationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update(['is_approved' => true]);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_approved' => false]);
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
