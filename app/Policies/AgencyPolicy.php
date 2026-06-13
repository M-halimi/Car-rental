<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\User;

class AgencyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, Agency $agency): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->agency && $user->agency->id === $agency->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Agency $agency): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Agency $agency): bool
    {
        return $user->hasRole('super_admin');
    }

    public function suspend(User $user, Agency $agency): bool
    {
        return $user->hasRole('super_admin') && $agency->isActive();
    }

    public function activate(User $user, Agency $agency): bool
    {
        return $user->hasRole('super_admin') && ! $agency->isActive();
    }
}
