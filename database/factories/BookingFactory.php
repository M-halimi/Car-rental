<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\City;
use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'customer_id' => Customer::factory(),
            'pickup_city_id' => City::factory(),
            'return_city_id' => City::factory(),
            'pickup_date' => now()->addDay(),
            'return_date' => now()->addDays(3),
            'price_per_day' => 500,
            'total_days' => 3,
            'subtotal' => 1500,
            'total_price' => 1500,
            'total_amount' => 1500,
            'deposit_amount' => 500,
            'status' => BookingStatus::Confirmed->value,
        ];
    }
}
