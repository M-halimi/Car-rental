<?php

namespace App\Listeners;

use App\Events\ContractGenerated;
use App\Events\PaymentReceived;
use App\Events\ReservationCancelled;
use App\Events\ReservationConfirmed;
use App\Events\ReservationCreated;
use App\Services\NotificationService;

class SendBookingNotification
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function handle(
        ReservationCreated|ReservationConfirmed|ReservationCancelled|PaymentReceived|ContractGenerated $event
    ): void {
        match (true) {
            $event instanceof ReservationCreated => $this->notificationService->sendBookingCreated($event->booking),
            $event instanceof ReservationConfirmed => $this->notificationService->sendBookingConfirmed($event->booking),
            $event instanceof ReservationCancelled => $this->notificationService->sendBookingCancelled($event->booking),
            $event instanceof PaymentReceived => $this->notificationService->sendPaymentReceived($event->booking, $event->payment),
            $event instanceof ContractGenerated => $this->notificationService->sendContractGenerated($event->booking, $event->contract),
        };
    }
}
