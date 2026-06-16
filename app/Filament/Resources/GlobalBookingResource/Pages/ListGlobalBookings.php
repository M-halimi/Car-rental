<?php

namespace App\Filament\Resources\GlobalBookingResource\Pages;

use App\Filament\Resources\GlobalBookingResource\GlobalBookingResource;
use Filament\Resources\Pages\ListRecords;

class ListGlobalBookings extends ListRecords
{
    protected static string $resource = GlobalBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
