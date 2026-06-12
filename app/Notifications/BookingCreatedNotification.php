<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification
{
    public function __construct(
        public $booking,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', SmsChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCustomer = $notifiable->hasRole('customer');

        return (new MailMessage)
            ->subject($isCustomer ? 'Booking Confirmation - CarRental.ma' : 'New Booking Received - CarRental.ma')
            ->greeting($isCustomer ? "Dear {$notifiable->name}," : "Dear {$notifiable->name},")
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

    public function toSms(object $notifiable): string
    {
        $isCustomer = $notifiable->hasRole('customer');

        return $isCustomer
            ? "CarRental.ma: Booking #{$this->booking->id} created. {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model} from {$this->booking->pickup_date?->format('d/m')} to {$this->booking->return_date?->format('d/m')}."
            : "CarRental.ma: New booking #{$this->booking->id} received. {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}, {$this->booking->pickup_date?->format('d/m')} - {$this->booking->return_date?->format('d/m')}.";
    }
}
