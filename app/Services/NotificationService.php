<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalContract;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\ContractGeneratedNotification;
use App\Notifications\PaymentReceivedNotification;

class NotificationService
{
    public function sendBookingCreated(Booking $booking): void
    {
        $notification = new BookingCreatedNotification($booking);

        $booking->customer?->user?->notify($notification);
        $booking->vehicle?->agency?->user?->notify($notification);
    }

    public function sendBookingConfirmed(Booking $booking): void
    {
        $notification = new BookingConfirmedNotification($booking);

        $booking->customer?->user?->notify($notification);
        $booking->vehicle?->agency?->user?->notify($notification);
    }

    public function sendBookingCancelled(Booking $booking): void
    {
        $notification = new BookingCancelledNotification($booking);

        $booking->customer?->user?->notify($notification);
        $booking->vehicle?->agency?->user?->notify($notification);
    }

    public function sendPaymentReceived(Booking $booking, Payment $payment): void
    {
        $notification = new PaymentReceivedNotification($booking, $payment);

        $booking->customer?->user?->notify($notification);
        $booking->vehicle?->agency?->user?->notify($notification);
    }

    public function sendContractGenerated(Booking $booking, RentalContract $contract): void
    {
        $notification = new ContractGeneratedNotification($booking, $contract);

        $booking->customer?->user?->notify($notification);
        $booking->vehicle?->agency?->user?->notify($notification);
    }
}
