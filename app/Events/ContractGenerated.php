<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\RentalContract;
use Illuminate\Foundation\Events\Dispatchable;

class ContractGenerated
{
    use Dispatchable;

    public function __construct(
        public Booking $booking,
        public RentalContract $contract,
    ) {}
}
