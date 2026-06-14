<?php

namespace App\Providers;

use App\Livewire\NotificationBell;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Observers\BookingObserver;
use App\Observers\VehicleObserver;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Livewire::component('notification-bell', NotificationBell::class);

        Booking::observe(BookingObserver::class);
        Vehicle::observe(VehicleObserver::class);
    }
}
