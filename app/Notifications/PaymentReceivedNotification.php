<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    public function __construct(
        public $booking,
        public $payment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', SmsChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCustomer = $notifiable->hasRole('customer');

        return (new MailMessage)
            ->subject('Payment Received - CarRental.ma')
            ->greeting("Dear {$notifiable->name},")
            ->line($isCustomer
                ? 'Payment of '.number_format($this->payment->amount, 2)." MAD for booking #{$this->booking->id} has been received."
                : "Payment received for booking #{$this->booking->id}."
            )
            ->line('Amount: '.number_format($this->payment->amount, 2).' MAD')
            ->line('Method: '.ucfirst($this->payment->payment_method))
            ->line("Booking: #{$this->booking->id} - {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}");
    }

    public function toSms(object $notifiable): string
    {
        $isCustomer = $notifiable->hasRole('customer');

        return $isCustomer
            ? 'CarRental.ma: Payment of '.number_format($this->payment->amount, 2)." MAD received for booking #{$this->booking->id}."
            : 'CarRental.ma: Payment of '.number_format($this->payment->amount, 2)." MAD received for booking #{$this->booking->id}.";
    }
}
