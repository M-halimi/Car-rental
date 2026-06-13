<?php

namespace App\Observers;

use App\Enums\BookingStatus;
use App\Events\PaymentPending;
use App\Events\ReservationCancelled;
use App\Events\ReservationConfirmed;
use App\Events\ReservationCreated;
use App\Models\Booking;
use App\Models\BookingCommission;
use App\Models\Payment;
use App\Services\CommissionService;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    public function created(Booking $booking): void
    {
        $amount = $booking->total_amount ?? $booking->total_price ?? 0;
        $deposit = $booking->deposit_amount ?? 0;

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $amount,
            'deposit_amount' => $deposit,
            'payment_type' => 'full',
            'payment_method' => 'cash',
            'status' => Payment::PENDING,
            'due_date' => $booking->pickup_date,
        ]);

        ReservationCreated::dispatch($booking);
        PaymentPending::dispatch($booking, $payment);
    }

    public function updating(Booking $booking): void
    {
        if (! $booking->isDirty('status') || $booking->forceTransition) {
            return;
        }

        $original = $booking->getOriginal('status');
        $originalEnum = BookingStatus::tryFrom($original);
        $targetEnum = BookingStatus::tryFrom($booking->status);

        if (! $originalEnum || ! $targetEnum || $originalEnum->canTransitionTo($targetEnum)) {
            return;
        }

        throw new \RuntimeException(
            sprintf(
                'Invalid status transition for booking #%d: "%s" → "%s". Allowed transitions from "%s": [%s]',
                $booking->id,
                $original,
                $booking->status,
                $original,
                implode(', ', $originalEnum->allowedTransitions()
                    ? array_map(fn (BookingStatus $s) => $s->value, $originalEnum->allowedTransitions())
                    : [])
            )
        );
    }

    public function updated(Booking $booking): void
    {
        if (! $booking->isDirty('status')) {
            return;
        }

        $original = $booking->getOriginal('status');
        $currentEnum = $booking->statusEnum();

        if (! $currentEnum) {
            return;
        }

        if ($currentEnum === BookingStatus::Confirmed && $original !== BookingStatus::Confirmed->value) {
            ReservationConfirmed::dispatch($booking);
        }

        if ($currentEnum === BookingStatus::Completed && $original !== BookingStatus::Completed->value) {
            $this->handleCompletionCommission($booking);
        }

        if ($currentEnum->isStockReleased() && ! in_array($original, Booking::STOCK_RELEASE_STATUSES, true)) {
            if ($original === BookingStatus::Completed->value) {
                $this->handleVoidOnCancellation($booking);
            }

            ReservationCancelled::dispatch($booking);
        }
    }

    private function handleCompletionCommission(Booking $booking): void
    {
        $existing = BookingCommission::where('booking_id', $booking->id)->first();

        if ($existing) {
            if (in_array($existing->status, [BookingCommission::VOID, BookingCommission::PAID])) {
                Log::warning("Booking #{$booking->id} already has a {$existing->status} commission. Skipping recalculation.");

                return;
            }
        }

        if (! app(CommissionService::class)->isBookingEligibleForCommission($booking)) {
            Log::warning("Booking #{$booking->id} is not eligible for commission calculation");

            return;
        }

        app(CommissionService::class)->calculateForBooking($booking);
    }

    private function handleVoidOnCancellation(Booking $booking): void
    {
        $commission = BookingCommission::where('booking_id', $booking->id)->first();

        if (! $commission || $commission->status === BookingCommission::PAID) {
            return;
        }

        app(CommissionService::class)->voidCommission($commission, 'Booking cancelled after completion');
    }
}
