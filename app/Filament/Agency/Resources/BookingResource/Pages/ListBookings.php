<?php

namespace App\Filament\Agency\Resources\BookingResource\Pages;

use App\Filament\Agency\Resources\BookingResource;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;
}
