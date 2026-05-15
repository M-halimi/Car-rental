<?php

namespace Database\Seeders;

use App\Models\VehicleCategory;
use Illuminate\Database\Seeder;

class VehicleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Economy', 'slug' => 'economy', 'icon' => 'car', 'description' => 'Budget-friendly compact cars', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Compact', 'slug' => 'compact', 'icon' => 'car', 'description' => 'Small efficient vehicles', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'SUV', 'slug' => 'suv', 'icon' => 'truck', 'description' => 'Sport Utility Vehicles', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Luxury', 'slug' => 'luxury', 'icon' => 'star', 'description' => 'Premium high-end vehicles', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Van', 'slug' => 'van', 'icon' => 'truck', 'description' => 'Cargo and passenger vans', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Minibus', 'slug' => 'minibus', 'icon' => 'users', 'description' => '12-15 passenger minibuses', 'is_active' => true, 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            VehicleCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
