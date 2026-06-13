<?php

namespace App\Events;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentPending
{
    use Dispatchable;

    public function __construct(
        public Booking $booking,
        public Payment $payment,
    ) {}
}
