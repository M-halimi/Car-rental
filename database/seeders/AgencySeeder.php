<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
        $agencyUser = User::firstOrCreate(
            ['email' => 'agency@carrental.ma'],
            ['name' => 'Agence Casablanca', 'password' => 'password']
        );
        $agencyUser->assignRole('agency');

        Agency::firstOrCreate(
            ['email' => 'agency@carrental.ma'],
            [
                'user_id' => $agencyUser->id,
                'city_id' => 1,
                'name' => 'CarRental.ma Casablanca',
                'slug' => 'carrental-ma-casablanca',
                'address' => '123 Boulevard Mohammed V, Casablanca',
                'phone' => '+212 522 123 456',
                'description' => 'Leading car rental service in Casablanca',
                'is_active' => true,
            ]
        );
    }
}
