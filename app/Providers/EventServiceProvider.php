<?php

namespace App\Providers;

use App\Events\ContractGenerated;
use App\Events\CustomerUploadedDocuments;
use App\Events\PaymentPending;
use App\Events\PaymentReceived;
use App\Events\ReservationCancelled;
use App\Events\ReservationConfirmed;
use App\Events\ReservationCreated;
use App\Events\VehicleMarkedUnavailable;
use App\Listeners\HandleReservationCancelled;
use App\Listeners\HandleReservationConfirmed;
use App\Listeners\SendBookingNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ReservationCreated::class => [
            SendBookingNotification::class,
        ],
        ReservationConfirmed::class => [
            HandleReservationConfirmed::class,
            SendBookingNotification::class,
        ],
        ReservationCancelled::class => [
            HandleReservationCancelled::class,
            SendBookingNotification::class,
        ],
        PaymentReceived::class => [
            SendBookingNotification::class,
        ],
        PaymentPending::class => [
            SendBookingNotification::class,
        ],
        ContractGenerated::class => [
            SendBookingNotification::class,
        ],
        VehicleMarkedUnavailable::class => [
            SendBookingNotification::class,
        ],
        CustomerUploadedDocuments::class => [
            SendBookingNotification::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
