<?php

namespace App\Filament\Resources\BookingCommissionResource\Pages;

use App\Filament\Resources\BookingCommissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBookingCommission extends EditRecord
{
    protected static string $resource = BookingCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
