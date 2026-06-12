<?php

namespace App\Filament\Agency\Resources\BookingResource\Pages;

use App\Exceptions\InsufficientStockException;
use App\Filament\Agency\Resources\BookingResource;
use App\Filament\Agency\Resources\BookingResource\RelationManagers\PaymentsRelationManager;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return parent::handleRecordUpdate($record, $data);
        } catch (InsufficientStockException $e) {
            Notification::make()
                ->danger()
                ->title('Insufficient Stock')
                ->body($e->getMessage())
                ->send();

            $this->refreshFormData(['status']);

            return $record;
        }
    }
}
