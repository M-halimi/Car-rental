<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\RentalContract;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\ContractGeneratedNotification;
use App\Notifications\CustomerUploadedDocumentsNotification;
use App\Notifications\PaymentPendingNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\VehicleMarkedUnavailableNotification;

class NotificationService
{
    public function sendBookingCreated(Booking $booking): void
    {
        $notification = new BookingCreatedNotification($booking);

        $this->sendToUsers($notification, [
            $booking->customer?->user,
            $booking->vehicle?->agency?->user,
        ]);
    }

    public function sendBookingConfirmed(Booking $booking): void
    {
        $notification = new BookingConfirmedNotification($booking);

        $this->sendToUsers($notification, [
            $booking->customer?->user,
            $booking->vehicle?->agency?->user,
        ]);
    }

    public function sendBookingCancelled(Booking $booking): void
    {
        $notification = new BookingCancelledNotification($booking);

        $this->sendToUsers($notification, [
            $booking->customer?->user,
            $booking->vehicle?->agency?->user,
        ]);
    }

    public function sendPaymentReceived(Booking $booking, Payment $payment): void
    {
        $notification = new PaymentReceivedNotification($booking, $payment);

        $this->sendToUsers($notification, [
            $booking->customer?->user,
            $booking->vehicle?->agency?->user,
        ]);
    }

    public function sendPaymentPending(Booking $booking, Payment $payment): void
    {
        $notification = new PaymentPendingNotification($booking, $payment);

        $this->sendToUsers($notification, [
            $booking->customer?->user,
            $booking->vehicle?->agency?->user,
        ]);
    }

    public function sendContractGenerated(Booking $booking, RentalContract $contract): void
    {
        $notification = new ContractGeneratedNotification($booking, $contract);

        $this->sendToUsers($notification, [
            $booking->customer?->user,
            $booking->vehicle?->agency?->user,
        ]);
    }

    public function sendVehicleMarkedUnavailable(Vehicle $vehicle): void
    {
        $notification = new VehicleMarkedUnavailableNotification($vehicle);

        $this->sendToUsers($notification, [
            $vehicle->agency?->user,
        ]);
    }

    public function sendCustomerUploadedDocuments(Customer $customer, User $user): void
    {
        $notification = new CustomerUploadedDocumentsNotification($customer, $user);
        $agencyUser = $customer->bookings()->first()?->vehicle?->agency?->user;

        $this->sendToUsers($notification, [
            $agencyUser,
        ]);
    }

    private function sendToUsers($notification, array $users): void
    {
        foreach (array_filter($users) as $user) {
            $user->notify($notification);
        }
    }
}
