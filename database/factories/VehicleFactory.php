<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'category_id' => VehicleCategory::factory(),
            'brand' => fake()->randomElement(['Renault', 'Dacia', 'Peugeot', 'Toyota', 'Volkswagen']),
            'model' => fake()->word(),
            'year' => fake()->year(),
            'registration_number' => strtoupper(fake()->bothify('??-###-??')),
            'daily_rate' => fake()->randomFloat(2, 200, 1500),
            'status' => 'available',
            'is_active' => true,
            'seats' => 5,
            'transmission' => 'manual',
            'fuel_type' => 'gasoline',
        ];
    }
}
