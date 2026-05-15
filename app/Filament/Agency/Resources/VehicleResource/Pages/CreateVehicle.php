<?php

namespace App\Filament\Agency\Resources\VehicleResource\Pages;

use App\Filament\Agency\Resources\VehicleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicle extends CreateRecord
{
    protected static string $resource = VehicleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && $user->agency) {
            $data['agency_id'] = $user->agency->id;
        }

        return $data;
    }
}
