<?php

namespace App\Filament\Resources\VehicleCategoryResource\Pages;

use App\Filament\Resources\VehicleCategoryResource\VehicleCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleCategories extends ListRecords
{
    protected static string $resource = VehicleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
