<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Payment;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        $amount = $booking->total_amount ?? $booking->total_price ?? 0;
        $deposit = $booking->deposit_amount ?? 0;

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $amount,
            'deposit_amount' => $deposit,
            'remaining_balance' => max(0, $amount - $deposit),
            'payment_type' => 'full',
            'payment_method' => 'cash',
            'status' => Payment::PENDING,
            'due_date' => $booking->pickup_date,
        ]);
    }
}
