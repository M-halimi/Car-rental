<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Policies\BookingPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\VehiclePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Booking::class => BookingPolicy::class,
        Customer::class => CustomerPolicy::class,
        Payment::class => PaymentPolicy::class,
        Vehicle::class => VehiclePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }

            return null;
        });
    }
}
