<?php

return [
    'default' => env('SMS_PROVIDER', 'log'),

    'providers' => [
        'twilio' => [
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_AUTH_TOKEN'),
            'from' => env('TWILIO_FROM', '+15005550006'),
        ],

        'orange' => [
            'client_id' => env('ORANGE_SMS_CLIENT_ID'),
            'client_secret' => env('ORANGE_SMS_CLIENT_SECRET'),
            'sender' => env('ORANGE_SMS_SENDER', 'CarRental'),
        ],
    ],
];
