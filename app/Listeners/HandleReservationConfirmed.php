<?php

namespace App\Listeners;

use App\Events\ReservationConfirmed;
use App\Services\ReservationStockService;

class HandleReservationConfirmed
{
    public function __construct(
        private ReservationStockService $stockService,
    ) {}

    public function handle(ReservationConfirmed $event): void
    {
        $this->stockService->reserveStock($event->booking);
    }
}
