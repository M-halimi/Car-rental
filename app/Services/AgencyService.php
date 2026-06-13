<?php

namespace App\Services;

use App\Mail\AgencyCreated;
use App\Models\Agency;
use App\Models\AgencySetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AgencyService
{
    public function createWithOwner(array $agencyData, array $ownerData): Agency
    {
        return DB::transaction(function () use ($agencyData, $ownerData) {
            $password = $ownerData['password'] ?? Str::random(12);

            $user = User::create([
                'name' => $ownerData['name'],
                'email' => $ownerData['email'],
                'password' => Hash::make($password),
            ]);

            $user->assignRole('agency');

            $agencyData['slug'] = $agencyData['slug'] ?? Str::slug($agencyData['name']);
            $agencyData['user_id'] = $user->id;

            /** @var Agency $agency */
            $agency = Agency::create($agencyData);

            AgencySetting::create([
                'agency_id' => $agency->id,
            ]);

            Mail::to($ownerData['email'])->queue(
                new AgencyCreated($agency, $ownerData['email'], $password)
            );

            return $agency;
        });
    }

    public function suspend(Agency $agency): void
    {
        $agency->update([
            'status' => 'suspended',
            'is_active' => false,
        ]);
    }

    public function activate(Agency $agency): void
    {
        $agency->update([
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    public function markExpired(): int
    {
        return Agency::whereNotNull('subscription_end_date')
            ->where('subscription_end_date', '<', now())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired', 'is_active' => false]);
    }
}
