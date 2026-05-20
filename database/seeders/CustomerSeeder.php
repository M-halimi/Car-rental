<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['email' => 'saad2saaad@gmail.com', 'name' => 'Saad Halimi', 'first_name' => 'Saad', 'last_name' => 'Halimi', 'phone' => '+212 600 000 001'],
            ['email' => 'halimi767@gmail.com', 'name' => 'Halimi Mohamed', 'first_name' => 'Halimi', 'last_name' => 'Mohamed', 'phone' => '+212 600 000 002'],
        ];

        foreach ($customers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'password',
                ]
            );
            $user->assignRole('customer');

            Customer::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                ]
            );
        }
    }
}
