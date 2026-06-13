<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Notifications\Concerns\HasNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification implements ShouldQueue
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
            ->subject('Booking Cancelled - CarRental.ma')
            ->greeting("Dear {$notifiable->name},")
            ->line($isCustomer
                ? "Your booking #{$this->booking->id} has been cancelled."
                : "Booking #{$this->booking->id} has been cancelled."
            )
            ->line("Vehicle: {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}")
            ->lineIf($this->booking->cancellation_reason, 'Reason: '.$this->booking->cancellation_reason);
    }

    public function toDatabase(object $notifiable): array
    {
        $isCustomer = $notifiable->hasRole('customer');

        return [
            'type' => 'booking_cancelled',
            'title' => $isCustomer
                ? "Booking #{$this->booking->id} Cancelled"
                : "Booking #{$this->booking->id} Cancelled",
            'body' => $isCustomer
                ? "Your booking for {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model} has been cancelled."
                : "Booking #{$this->booking->id} for {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model} has been cancelled."
                .($this->booking->cancellation_reason ? " Reason: {$this->booking->cancellation_reason}" : ''),
            'icon' => 'heroicon-o-x-circle',
            'color' => 'danger',
            'action_url' => $isCustomer
                ? "/bookings/{$this->booking->id}"
                : "/agency/bookings/{$this->booking->id}/edit",
            'action_text' => 'View Booking',
            'model_type' => 'booking',
            'model_id' => $this->booking->id,
        ];
    }
}
