<?php

namespace App\Filament\Agency\Resources\BookingResource\Pages;

use App\Filament\Agency\Resources\BookingResource;
use App\Filament\Agency\Resources\BookingResource\RelationManagers\PaymentsRelationManager;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateContract')
                ->label('View Contract')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->url(fn () => route('agency.booking.contract', $this->getRecord()->id))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }
}
