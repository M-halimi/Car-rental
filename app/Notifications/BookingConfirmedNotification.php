<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
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

    public function toSms(object $notifiable): string
    {
        $isCustomer = $notifiable->hasRole('customer');

        return $isCustomer
            ? "CarRental.ma: Booking #{$this->booking->id} confirmed! {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}. Pickup {$this->booking->pickup_date?->format('d/m')}."
            : "CarRental.ma: Booking #{$this->booking->id} confirmed for {$this->booking->customer?->first_name} {$this->booking->customer?->last_name}. {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}.";
    }
}
