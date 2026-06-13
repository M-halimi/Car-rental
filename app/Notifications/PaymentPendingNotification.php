<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Payment;
use App\Notifications\Concerns\HasNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentPendingNotification extends Notification implements ShouldQueue
{
    use HasNotificationPreferences, Queueable;

    public function __construct(
        public Booking $booking,
        public Payment $payment,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->resolveViaChannels($notifiable, ['mail', 'database']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCustomer = $notifiable->hasRole('customer');

        return (new MailMessage)
            ->subject('Payment Pending - CarRental.ma')
            ->greeting("Dear {$notifiable->name},")
            ->line($isCustomer
                ? 'A payment of '.number_format($this->payment->amount, 2)." MAD for booking #{$this->booking->id} is pending."
                : "A payment for booking #{$this->booking->id} is pending."
            )
            ->line('Amount: '.number_format($this->payment->amount, 2).' MAD')
            ->line('Method: '.ucfirst($this->payment->payment_method))
            ->line("Booking: #{$this->booking->id} - {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}")
            ->line($isCustomer
                ? 'Please complete your payment to confirm the booking.'
                : 'Please follow up with the customer to complete the payment.'
            );
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'payment_pending',
            'title' => "Payment Pending - Booking #{$this->booking->id}",
            'body' => 'Payment of '.number_format($this->payment->amount, 2).' MAD via '.ucfirst($this->payment->payment_method).' is pending.',
            'icon' => 'heroicon-o-clock',
            'color' => 'warning',
            'action_url' => "/agency/bookings/{$this->booking->id}/edit",
            'action_text' => 'View Booking',
            'model_type' => 'payment',
            'model_id' => $this->payment->id,
        ];
    }
}
