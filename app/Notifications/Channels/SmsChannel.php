<?php

namespace App\Notifications\Channels;

use App\Contracts\SmsProvider;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(
        private SmsProvider $provider,
    ) {}

    public function send(object $notifiable, Notification $notification): void
    {
        $to = $notifiable->routeNotificationFor('sms', $notification);

        if (! $to) {
            return;
        }

        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $message = $notification->toSms($notifiable);

        $this->provider->send($to, $message);
    }
}
