<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'callmebot' => [
        'phone' => env('CALLMEBOT_PHONE'),
        'apikey' => env('CALLMEBOT_APIKEY'),
    ],

    'twilio' => [
        // Accept both naming conventions so the code works regardless of which .env key is set
        'sid'                   => env('TWILIO_SID', env('TWILIO_ACCOUNT_SID')),
        'token'                 => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from'         => env('TWILIO_WHATSAPP_FROM', env('TWILIO_WHATSAPP_NUMBER')),
        'owner_whatsapp_number' => env('OWNER_WHATSAPP_NUMBER'),
    ],

    'telegram' => [
        'bot_token'    => env('TELEGRAM_BOT_TOKEN'),
        'bot_username' => env('TELEGRAM_BOT_USERNAME', 'naa_um_bot'),
        'chat_id'      => env('TELEGRAM_CHAT_ID'),
    ],

];
