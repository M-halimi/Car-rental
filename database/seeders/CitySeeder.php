<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['name' => 'Casablanca', 'slug' => 'casablanca'],
            ['name' => 'Marrakech', 'slug' => 'marrakech'],
            ['name' => 'Tangier', 'slug' => 'tangier'],
            ['name' => 'Agadir', 'slug' => 'agadir'],
            ['name' => 'Rabat', 'slug' => 'rabat'],
            ['name' => 'Fes', 'slug' => 'fes'],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(['slug' => $city['slug']], $city);
        }
    }
}
