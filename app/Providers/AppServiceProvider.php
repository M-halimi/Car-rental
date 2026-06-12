<?php

namespace App\Providers;

use App\Contracts\SmsProvider;
use App\Models\Booking;
use App\Observers\BookingObserver;
use App\Services\Sms\LogSmsProvider;
use App\Services\Sms\OrangeSmsMoroccoProvider;
use App\Services\Sms\TwilioSmsProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsProvider::class, function ($app) {
            return match (config('sms.default')) {
                'twilio' => new TwilioSmsProvider(
                    config('sms.providers.twilio.sid'),
                    config('sms.providers.twilio.token'),
                    config('sms.providers.twilio.from'),
                ),
                'orange' => new OrangeSmsMoroccoProvider(
                    config('sms.providers.orange.client_id'),
                    config('sms.providers.orange.client_secret'),
                    config('sms.providers.orange.sender'),
                ),
                default => new LogSmsProvider,
            };
        });
    }

    public function boot(): void
    {
        Booking::observe(BookingObserver::class);
    }
}
