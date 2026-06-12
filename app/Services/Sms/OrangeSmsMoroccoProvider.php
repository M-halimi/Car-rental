<?php

namespace App\Services\Sms;

use App\Contracts\SmsProvider;

class OrangeSmsMoroccoProvider implements SmsProvider
{
    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $sender,
    ) {}

    public function send(string $to, string $message): bool
    {
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer '.$this->getAccessToken(),
        //     'Content-Type' => 'application/json',
        // ])->post('https://api.orange-sms.ma/send', [
        //     'sender' => $this->sender,
        //     'recipient' => $to,
        //     'message' => $message,
        // ]);

        return true;
    }
}
