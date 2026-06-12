<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationStockService
{
    public function __construct(
        private AvailabilityService $availabilityService,
    ) {}

    public function reserveStock(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $pickupDate = $this->formatDate($booking->pickup_date);
            $returnDate = $this->formatDate($booking->return_date);

            $available = $this->availabilityService->getAvailableStock(
                vehicleId: $booking->vehicle_id,
                pickupDate: $pickupDate,
                returnDate: $returnDate,
                excludeBookingId: $booking->id,
                lockForUpdate: true,
                statuses: Booking::STOCK_HOLD_STATUSES,
            );

            if ($available <= 0) {
                $booking->updateQuietly(['status' => BookingStatus::Pending->value]);

                throw new InsufficientStockException(
                    "Vehicle #{$booking->vehicle_id} has no available stock for {$pickupDate} to {$returnDate}."
                );
            }
        });
    }

    public function releaseStock(Booking $booking, string $newStatus = BookingStatus::Cancelled->value): void
    {
        DB::transaction(function () use ($booking, $newStatus) {
            if (in_array($booking->status, Booking::STOCK_RELEASE_STATUSES, true)) {
                return;
            }

            $booking->updateQuietly(['status' => $newStatus]);
        });
    }

    private function formatDate(mixed $date): string
    {
        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }

        return (string) $date;
    }
}
