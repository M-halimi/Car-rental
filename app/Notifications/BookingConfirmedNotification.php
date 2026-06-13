<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Notifications\Concerns\HasNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject('Booking Confirmed - CarRental.ma')
            ->greeting("Dear {$notifiable->name},")
            ->line($isCustomer
                ? "Your booking #{$this->booking->id} has been confirmed!"
                : "Booking #{$this->booking->id} has been confirmed."
            )
            ->line("Vehicle: {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}")
            ->line("Pickup: {$this->booking->pickup_date?->format('d M Y')} at {$this->booking->pickup_time}")
            ->line("Return: {$this->booking->return_date?->format('d M Y')} at {$this->booking->return_time}")
            ->line('Total: '.number_format($this->booking->total_amount ?? 0, 2).' MAD')
            ->line($isCustomer
                ? 'Please arrive on time for pickup. Drive safely!'
                : 'Prepare the vehicle for the customer.'
            );
    }

    public function toDatabase(object $notifiable): array
    {
        $isCustomer = $notifiable->hasRole('customer');

        return [
            'type' => 'booking_confirmed',
            'title' => $isCustomer
                ? "Booking #{$this->booking->id} Confirmed"
                : "Booking #{$this->booking->id} Confirmed",
            'body' => $isCustomer
                ? "Your booking for {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model} has been confirmed."
                : "Booking #{$this->booking->id} for {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model} has been confirmed.",
            'icon' => 'heroicon-o-check-circle',
            'color' => 'success',
            'action_url' => $isCustomer
                ? "/bookings/{$this->booking->id}"
                : "/agency/bookings/{$this->booking->id}/edit",
            'action_text' => 'View Booking',
            'model_type' => 'booking',
            'model_id' => $this->booking->id,
        ];
    }
}
