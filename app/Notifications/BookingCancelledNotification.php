<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
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
            ->subject('Booking Cancelled - CarRental.ma')
            ->greeting("Dear {$notifiable->name},")
            ->line($isCustomer
                ? "Your booking #{$this->booking->id} has been cancelled."
                : "Booking #{$this->booking->id} has been cancelled."
            )
            ->line("Vehicle: {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}")
            ->lineIf($this->booking->cancellation_reason, 'Reason: '.$this->booking->cancellation_reason);
    }

    public function toSms(object $notifiable): string
    {
        $isCustomer = $notifiable->hasRole('customer');

        return $isCustomer
            ? "CarRental.ma: Booking #{$this->booking->id} cancelled. {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}."
            : "CarRental.ma: Booking #{$this->booking->id} cancelled by customer. {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}.";
    }
}
