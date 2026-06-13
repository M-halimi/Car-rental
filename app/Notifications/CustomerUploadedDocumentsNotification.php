<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\User;
use App\Notifications\Concerns\HasNotificationPreferences;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerUploadedDocumentsNotification extends Notification implements ShouldQueue
{
    use HasNotificationPreferences, Queueable;

    public function __construct(
        public Customer $customer,
        public User $user,
    ) {}

    public function via(object $notifiable): array
    {
        return $this->resolveViaChannels($notifiable, ['mail', 'database']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Customer Documents Uploaded - CarRental.ma')
            ->greeting("Dear {$notifiable->name},")
            ->line("{$this->customer->first_name} {$this->customer->last_name} has uploaded new documents.")
            ->line('Please review and verify the documents in your dashboard.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'customer_documents_uploaded',
            'title' => 'Documents Uploaded',
            'body' => "{$this->customer->first_name} {$this->customer->last_name} has uploaded new documents for verification.",
            'icon' => 'heroicon-o-document-arrow-up',
            'color' => 'info',
            'action_url' => "/agency/customers/{$this->customer->id}/edit",
            'action_text' => 'Review Documents',
            'model_type' => 'customer',
            'model_id' => $this->customer->id,
        ];
    }
}
