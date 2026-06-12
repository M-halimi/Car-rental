<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('agency') || $user->hasRole('super_admin');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('customer')) {
            return $user->customer?->id === $payment->booking->customer_id;
        }

        if ($user->hasRole('agency')) {
            return $user->agency?->id === $payment->booking->vehicle->agency_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('agency');
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($user->hasRole('agency')) {
            return $user->agency?->id === $payment->booking->vehicle->agency_id;
        }

        return false;
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole('agency') && $user->agency?->id === $payment->booking->vehicle->agency_id;
    }
}
