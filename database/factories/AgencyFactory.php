<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AgencyFactory extends Factory
{
    protected $model = Agency::class;

    public function definition(): array
    {
        $name = fake()->company().' Rental';

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'email' => fake()->companyEmail(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'city_id' => City::factory(),
            'is_active' => true,
        ];
    }
}
