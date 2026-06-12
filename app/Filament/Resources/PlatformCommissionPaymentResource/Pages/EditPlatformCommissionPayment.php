<?php

namespace App\Filament\Resources\PlatformCommissionPaymentResource\Pages;

use App\Filament\Resources\PlatformCommissionPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlatformCommissionPayment extends EditRecord
{
    protected static string $resource = PlatformCommissionPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
