<?php

namespace App\Services\Sms;

use App\Contracts\SmsProvider;
use Illuminate\Support\Facades\Log;

class LogSmsProvider implements SmsProvider
{
    public function send(string $to, string $message): bool
    {
        Log::info('SMS notification', ['to' => $to, 'message' => $message]);

        return true;
    }
}
