<?php

namespace App\Events;

use App\Models\Vehicle;
use Illuminate\Foundation\Events\Dispatchable;

class VehicleMarkedUnavailable
{
    use Dispatchable;

    public function __construct(
        public Vehicle $vehicle,
    ) {}
}
