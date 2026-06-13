<?php

namespace App\Observers;

use App\Events\VehicleMarkedUnavailable;
use App\Models\Vehicle;

class VehicleObserver
{
    public function updated(Vehicle $vehicle): void
    {
        if (! $vehicle->isDirty('status')) {
            return;
        }

        $newStatus = $vehicle->status;

        if (in_array($newStatus, ['unavailable', 'maintenance'], true)) {
            VehicleMarkedUnavailable::dispatch($vehicle);
        }
    }
}
