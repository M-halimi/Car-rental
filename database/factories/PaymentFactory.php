<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'amount' => 5000,
            'payment_type' => 'rental',
            'payment_method' => 'cash',
            'status' => Payment::PENDING,
        ];
    }
}
