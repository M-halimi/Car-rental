<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agency') || $user->hasRole('super_admin');
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('agency')) {
            return $user->agency?->id === $vehicle->agency_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agency') || $user->hasRole('super_admin');
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('agency')) {
            return $user->agency?->id === $vehicle->agency_id;
        }

        return false;
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        if ($user->hasRole('agency')) {
            return $user->agency?->id === $vehicle->agency_id;
        }

        return false;
    }
}
