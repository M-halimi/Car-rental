<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customerUser = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            ['name' => 'Test Customer', 'password' => 'password']
        );
        $customerUser->assignRole('customer');

        Customer::firstOrCreate(
            ['user_id' => $customerUser->id],
            [
                'first_name' => 'Test',
                'last_name' => 'Customer',
                'phone' => '+212 612 345 678',
                'city_id' => 1,
            ]
        );
    }
}
