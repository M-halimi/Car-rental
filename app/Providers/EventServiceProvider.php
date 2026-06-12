<?php

namespace App\Providers;

use App\Events\ContractGenerated;
use App\Events\PaymentReceived;
use App\Events\ReservationCancelled;
use App\Events\ReservationConfirmed;
use App\Listeners\HandleReservationCancelled;
use App\Listeners\HandleReservationConfirmed;
use App\Listeners\SendBookingNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ReservationConfirmed::class => [
            HandleReservationConfirmed::class,
            SendBookingNotification::class,
        ],
        ReservationCancelled::class => [
            HandleReservationCancelled::class,
            SendBookingNotification::class,
        ],
        ReservationCreated::class => [
            SendBookingNotification::class,
        ],
        PaymentReceived::class => [
            SendBookingNotification::class,
        ],
        ContractGenerated::class => [
            SendBookingNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
