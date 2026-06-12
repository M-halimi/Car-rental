<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agency') || $user->hasRole('super_admin') || $user->hasRole('customer');
    }

    public function view(User $user, Booking $booking): bool
    {
        if ($user->hasRole('customer')) {
            return $user->customer?->id === $booking->customer_id;
        }

        if ($user->hasRole('agency')) {
            return $user->agency?->id === $booking->vehicle->agency_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('customer') || $user->hasRole('agency');
    }

    public function update(User $user, Booking $booking): bool
    {
        if ($user->hasRole('agency')) {
            return $user->agency?->id === $booking->vehicle->agency_id;
        }

        return $user->hasRole('customer') && $user->customer?->id === $booking->customer_id;
    }

    public function delete(User $user, Booking $booking): bool
    {
        if ($user->hasRole('agency')) {
            return $user->agency?->id === $booking->vehicle->agency_id;
        }

        return $user->hasRole('customer') && $user->customer?->id === $booking->customer_id;
    }
}
