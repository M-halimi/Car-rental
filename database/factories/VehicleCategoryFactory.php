<?php

namespace Database\Factories;

use App\Models\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VehicleCategoryFactory extends Factory
{
    protected $model = VehicleCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Economy', 'Compact', 'SUV', 'Luxury', 'Van', 'Minibus']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }

    public function ununique(): static
    {
        return $this->state(fn () => []);
    }
}
