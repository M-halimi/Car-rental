<?php

namespace App\Notifications\Concerns;

use App\Models\NotificationPreference;

trait HasNotificationPreferences
{
    public function resolveViaChannels(object $notifiable, array $defaultChannels): array
    {
        $preference = NotificationPreference::where('user_id', $notifiable->id)
            ->where('type', static::class)
            ->first();

        if (! $preference) {
            return $defaultChannels;
        }

        return array_values(array_filter($defaultChannels, function (string $channel) use ($preference) {
            if ($channel === 'mail' && ! $preference->email_enabled) {
                return false;
            }

            if ($channel === 'database' && ! $preference->database_enabled) {
                return false;
            }

            return true;
        }));
    }
}
