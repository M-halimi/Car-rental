<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Notifications\Concerns\HasNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification implements ShouldQueue
{
    use HasNotificationPreferences, Queueable;

    public function __construct(
        public Booking $booking,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->resolveViaChannels($notifiable, ['mail', 'database']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCustomer = $notifiable->hasRole('customer');

        return (new MailMessage)
            ->subject($isCustomer ? 'Booking Confirmation - CarRental.ma' : 'New Booking Received - CarRental.ma')
            ->greeting("Dear {$notifiable->name},")
            ->line($isCustomer
                ? "Your booking #{$this->booking->id} has been created successfully."
                : "A new booking #{$this->booking->id} has been received."
            )
            ->line("Vehicle: {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}")
            ->line("Pickup: {$this->booking->pickup_date?->format('d M Y')}")
            ->line("Return: {$this->booking->return_date?->format('d M Y')}")
            ->line('Total: '.number_format($this->booking->total_amount ?? 0, 2).' MAD')
            ->line($isCustomer
                ? 'We will review your booking and confirm shortly.'
                : 'Please review and confirm the booking in your dashboard.'
            );
    }

    public function toDatabase(object $notifiable): array
    {
        $isCustomer = $notifiable->hasRole('customer');

        return [
            'type' => 'booking_created',
            'title' => $isCustomer
                ? "Booking #{$this->booking->id} Created"
                : "New Booking #{$this->booking->id}",
            'body' => $isCustomer
                ? "Your booking for {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model} has been created."
                : "A new booking for {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model} has been received.",
            'icon' => 'heroicon-o-calendar',
            'color' => 'warning',
            'action_url' => $isCustomer
                ? "/bookings/{$this->booking->id}"
                : "/agency/bookings/{$this->booking->id}/edit",
            'action_text' => $isCustomer ? 'View Booking' : 'Review Booking',
            'model_type' => 'booking',
            'model_id' => $this->booking->id,
        ];
    }
}
