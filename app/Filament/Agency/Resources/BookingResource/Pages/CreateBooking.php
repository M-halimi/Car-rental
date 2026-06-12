<?php

namespace App\Filament\Agency\Resources\BookingResource\Pages;

use App\Filament\Agency\Resources\BookingResource;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $service = app(AvailabilityService::class);

        $pickupDate = $data['pickup_date'] ?? null;
        $returnDate = $data['return_date'] ?? null;
        $vehicleId = $data['vehicle_id'] ?? null;

        if ($pickupDate && $returnDate && $vehicleId) {
            $pickup = $pickupDate instanceof Carbon ? $pickupDate->format('Y-m-d') : $pickupDate;
            $return = $returnDate instanceof Carbon ? $returnDate->format('Y-m-d') : $returnDate;

            $stock = $service->getAvailableStock(
                $vehicleId,
                $pickup,
                $return,
                lockForUpdate: true,
            );

            if ($stock <= 0) {
                throw new \RuntimeException('This vehicle has no available stock for the selected dates.');
            }
        }

        return parent::handleRecordCreation($data);
    }
}
