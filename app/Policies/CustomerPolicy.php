<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agency') || $user->hasRole('super_admin');
    }

    public function view(User $user, Customer $customer): bool
    {
        if ($user->hasRole('customer')) {
            return $user->customer?->id === $customer->id;
        }

        if ($user->hasRole('agency')) {
            return $customer->bookings()->whereHas('vehicle', fn ($q) => $q->where('agency_id', $user->agency->id))->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agency') || $user->hasRole('super_admin');
    }

    public function update(User $user, Customer $customer): bool
    {
        if ($user->hasRole('customer')) {
            return $user->customer?->id === $customer->id;
        }

        if ($user->hasRole('agency')) {
            return $customer->bookings()->whereHas('vehicle', fn ($q) => $q->where('agency_id', $user->agency->id))->exists();
        }

        return false;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasRole('super_admin');
    }
}
