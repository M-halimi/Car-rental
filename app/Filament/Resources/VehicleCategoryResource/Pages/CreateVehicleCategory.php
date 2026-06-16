<?php

namespace App\Filament\Resources\VehicleCategoryResource\Pages;

use App\Filament\Resources\VehicleCategoryResource\VehicleCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleCategory extends CreateRecord
{
    protected static string $resource = VehicleCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
