<?php

namespace App\Notifications;

use App\Notifications\Channels\SmsChannel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractGeneratedNotification extends Notification
{
    public function __construct(
        public $booking,
        public $contract,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', SmsChannel::class];
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

    public function toSms(object $notifiable): string
    {
        $isCustomer = $notifiable->hasRole('customer');

        return $isCustomer
            ? "CarRental.ma: Contract #{$this->contract->contract_number} ready for booking #{$this->booking->id}. Please sign in your dashboard."
            : "CarRental.ma: Contract #{$this->contract->contract_number} generated for booking #{$this->booking->id}.";
    }
}
