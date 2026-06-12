<?php

namespace App\Listeners;

use App\Events\ReservationCancelled;
use App\Services\ReservationStockService;

class HandleReservationCancelled
{
    public function __construct(
        private ReservationStockService $stockService,
    ) {}

    public function handle(ReservationCancelled $event): void
    {
        $this->stockService->releaseStock($event->booking);
    }
}
