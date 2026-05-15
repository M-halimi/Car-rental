<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $agencyId = 1;

        $vehicles = [
            [
                'agency_id' => $agencyId,
                'city_id' => 1,
                'category_id' => 2,
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2024,
                'registration_number' => 'A-12345',
                'plate_number' => 'A-12345',
                'color' => 'White',
                'mileage' => 15000,
                'transmission' => 'automatic',
                'fuel_type' => 'gasoline',
                'doors' => 4,
                'seats' => 5,
                'daily_rate' => 300,
                'price_per_day' => 300,
                'description' => 'Toyota Corolla 2024 - Comfortable sedan perfect for city driving',
                'features' => ['AC', 'Bluetooth', 'USB', 'Backup Camera'],
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'agency_id' => 1,
                'city_id' => 1,
                'category_id' => 3,
                'brand' => 'Hyundai',
                'model' => 'Tucson',
                'year' => 2023,
                'registration_number' => 'A-12346',
                'plate_number' => 'A-12346',
                'color' => 'Black',
                'mileage' => 25000,
                'transmission' => 'automatic',
                'fuel_type' => 'diesel',
                'doors' => 4,
                'seats' => 5,
                'daily_rate' => 450,
                'price_per_day' => 450,
                'description' => 'Hyundai Tucson 2023 - Spacious SUV for family trips',
                'features' => ['AC', 'Bluetooth', 'GPS', 'Leather Seats', 'Sunroof'],
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'agency_id' => 1,
                'city_id' => 2,
                'category_id' => 4,
                'brand' => 'Mercedes',
                'model' => 'C200',
                'year' => 2024,
                'registration_number' => 'A-12347',
                'plate_number' => 'A-12347',
                'color' => 'Silver',
                'mileage' => 5000,
                'transmission' => 'automatic',
                'fuel_type' => 'gasoline',
                'doors' => 4,
                'seats' => 5,
                'daily_rate' => 800,
                'price_per_day' => 800,
                'description' => 'Mercedes C200 2024 - Luxury sedan for premium experience',
                'features' => ['AC', 'Bluetooth', 'GPS', 'Leather Seats', 'Premium Audio'],
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'agency_id' => 1,
                'city_id' => 2,
                'category_id' => 3,
                'brand' => 'Dacia',
                'model' => 'Duster',
                'year' => 2023,
                'registration_number' => 'A-12348',
                'plate_number' => 'A-12348',
                'color' => 'Blue',
                'mileage' => 30000,
                'transmission' => 'manual',
                'fuel_type' => 'diesel',
                'doors' => 4,
                'seats' => 5,
                'daily_rate' => 250,
                'price_per_day' => 250,
                'description' => 'Dacia Duster 2023 - Reliable SUV for adventures',
                'features' => ['AC', 'Bluetooth', 'USB'],
                'status' => 'available',
                'is_active' => true,
            ],
            [
                'agency_id' => 1,
                'city_id' => 3,
                'category_id' => 1,
                'brand' => 'Renault',
                'model' => 'Clio',
                'year' => 2024,
                'registration_number' => 'A-12349',
                'plate_number' => 'A-12349',
                'color' => 'Red',
                'mileage' => 10000,
                'transmission' => 'manual',
                'fuel_type' => 'gasoline',
                'doors' => 4,
                'seats' => 5,
                'daily_rate' => 200,
                'price_per_day' => 200,
                'description' => 'Renault Clio 2024 - Economic and stylish compact car',
                'features' => ['AC', 'Bluetooth', 'USB'],
                'status' => 'available',
                'is_active' => true,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
