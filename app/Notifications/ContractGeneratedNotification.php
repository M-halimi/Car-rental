<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\RentalContract;
use App\Notifications\Concerns\HasNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractGeneratedNotification extends Notification implements ShouldQueue
{
    use HasNotificationPreferences, Queueable;

    public function __construct(
        public Booking $booking,
        public RentalContract $contract,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->resolveViaChannels($notifiable, ['mail', 'database']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCustomer = $notifiable->hasRole('customer');

        return (new MailMessage)
            ->subject('Rental Contract Generated - CarRental.ma')
            ->greeting("Dear {$notifiable->name},")
            ->line($isCustomer
                ? "The rental contract for booking #{$this->booking->id} has been generated."
                : "Contract for booking #{$this->booking->id} has been generated."
            )
            ->line("Contract: #{$this->contract->contract_number}")
            ->line("Vehicle: {$this->booking->vehicle?->brand} {$this->booking->vehicle?->model}")
            ->line("Pickup: {$this->booking->pickup_date?->format('d M Y')}")
            ->line("Return: {$this->booking->return_date?->format('d M Y')}")
            ->line('Please review and sign the contract at your earliest convenience.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'contract_generated',
            'title' => "Contract Generated - Booking #{$this->booking->id}",
            'body' => "Contract #{$this->contract->contract_number} has been generated for booking #{$this->booking->id}.",
            'icon' => 'heroicon-o-document-text',
            'color' => 'info',
            'action_url' => "/agency/bookings/{$this->booking->id}/edit",
            'action_text' => 'View Booking',
            'model_type' => 'contract',
            'model_id' => $this->contract->id,
        ];
    }
}
