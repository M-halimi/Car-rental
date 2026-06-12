<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\AgencySetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgencySettingFactory extends Factory
{
    protected $model = AgencySetting::class;

    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'commission_rate' => fake()->randomFloat(2, 5, 30),
            'opening_morning_start' => '08:00',
            'opening_morning_end' => '12:00',
            'opening_afternoon_start' => '14:00',
            'opening_afternoon_end' => '18:00',
            'minimum_rental_days' => 1,
            'cancellation_hours' => 24,
            'late_return_fee_per_hour' => 50,
            'allow_delivery' => true,
            'delivery_fee' => 100,
            'require_deposit' => true,
            'default_deposit' => 500,
        ];
    }

    public function withCommissionRate(float $rate): static
    {
        return $this->state(fn () => ['commission_rate' => $rate]);
    }
}
