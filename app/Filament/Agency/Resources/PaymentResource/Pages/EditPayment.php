<?php

namespace App\Filament\Agency\Resources\PaymentResource\Pages;

use App\Filament\Agency\Resources\PaymentResource;
use App\Filament\Agency\Resources\PaymentResource\RelationManagers\PaymentLogsRelationManager;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getRelationManagers(): array
    {
        return [
            PaymentLogsRelationManager::class,
        ];
    }
}
