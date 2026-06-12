<?php

namespace App\Services\Sms;

use App\Contracts\SmsProvider;

class TwilioSmsProvider implements SmsProvider
{
    public function __construct(
        private string $sid,
        private string $token,
        private string $from,
    ) {}

    public function send(string $to, string $message): bool
    {
        // $client = new \Twilio\Rest\Client($this->sid, $this->token);
        // $client->messages->create($to, ['from' => $this->from, 'body' => $message]);

        return true;
    }
}
