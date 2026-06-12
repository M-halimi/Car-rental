<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Booking;
use App\Models\BookingCommission;
use App\Models\PlatformCommissionPayment;

class CommissionService
{
    public function getAgencyCommissionRate(Agency $agency): float
    {
        $setting = $agency->setting;

        return (float) ($setting?->commission_rate ?? 15.00);
    }

    public function calculateCommission(Booking $booking): float
    {
        $amount = (float) ($booking->total_amount ?? $booking->total_price ?? 0);
        $rate = $this->getAgencyCommissionRate($booking->vehicle->agency);

        return round($amount * $rate / 100, 2);
    }

    public function isBookingEligibleForCommission(Booking $booking): bool
    {
        return $booking->total_amount > 0
            && $booking->vehicle
            && $booking->vehicle->agency;
    }

    public function calculateForBooking(Booking $booking): BookingCommission
    {
        if (! $this->isBookingEligibleForCommission($booking)) {
            throw new \RuntimeException("Booking #{$booking->id} is not eligible for commission calculation");
        }

        $amount = (float) ($booking->total_amount ?? $booking->total_price ?? 0);
        $rate = $this->getAgencyCommissionRate($booking->vehicle->agency);
        $commission = round($amount * $rate / 100, 2);
        $net = round($amount - $commission, 2);

        return BookingCommission::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'agency_id' => $booking->vehicle->agency_id,
                'total_booking_amount' => $amount,
                'commission_rate' => $rate,
                'commission_amount' => $commission,
                'agency_net_amount' => $net,
                'status' => BookingCommission::CALCULATED,
                'calculated_at' => now(),
            ]
        );
    }

    public function markAsPaid(BookingCommission $commission): void
    {
        if (! BookingCommission::canTransitionTo($commission->status, BookingCommission::PAID)) {
            throw new \RuntimeException("Cannot mark commission #{$commission->id} as paid from status '{$commission->status}'");
        }

        $commission->update([
            'status' => BookingCommission::PAID,
            'paid_at' => now(),
        ]);
    }

    public function voidCommission(BookingCommission $commission, ?string $reason = null): void
    {
        if (! BookingCommission::canTransitionTo($commission->status, BookingCommission::VOID)) {
            throw new \RuntimeException("Cannot void commission #{$commission->id} from status '{$commission->status}'");
        }

        $commission->update([
            'status' => BookingCommission::VOID,
            'notes' => $reason ? ($commission->notes ? $commission->notes."\n".$reason : $reason) : $commission->notes,
        ]);
    }

    public function markAsDisputed(BookingCommission $commission, ?string $reason = null): void
    {
        if (! BookingCommission::canTransitionTo($commission->status, BookingCommission::DISPUTED)) {
            throw new \RuntimeException("Cannot dispute commission #{$commission->id} from status '{$commission->status}'");
        }

        $commission->update([
            'status' => BookingCommission::DISPUTED,
            'notes' => $reason ? ($commission->notes ? $commission->notes."\n".$reason : $reason) : $commission->notes,
        ]);
    }

    public function autoMarkBatchAsPaid(PlatformCommissionPayment $payment): void
    {
        $payment->loadMissing('items.bookingCommission');

        foreach ($payment->items as $item) {
            $commission = $item->bookingCommission;

            if (! $commission) {
                continue;
            }

            if (BookingCommission::canTransitionTo($commission->status, BookingCommission::PAID)) {
                $commission->update([
                    'status' => BookingCommission::PAID,
                    'paid_at' => $payment->paid_at,
                ]);
            }
        }
    }

    public function getAgencyBalance(Agency $agency): float
    {
        return (float) BookingCommission::where('agency_id', $agency->id)
            ->where('status', BookingCommission::CALCULATED)
            ->sum('commission_amount');
    }

    public function getAgencyCommissionSummary(Agency $agency): array
    {
        $calculated = BookingCommission::where('agency_id', $agency->id)
            ->where('status', BookingCommission::CALCULATED);

        $paid = BookingCommission::where('agency_id', $agency->id)
            ->where('status', BookingCommission::PAID);

        return [
            'total_gross' => (float) BookingCommission::where('agency_id', $agency->id)->sum('total_booking_amount'),
            'total_commission' => (float) BookingCommission::where('agency_id', $agency->id)->sum('commission_amount'),
            'pending_commission' => (float) (clone $calculated)->sum('commission_amount'),
            'paid_commission' => (float) (clone $paid)->sum('commission_amount'),
            'pending_count' => (clone $calculated)->count(),
            'paid_count' => (clone $paid)->count(),
        ];
    }
}
