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
        'wave' => [
            'public_key' => env('WAVE_PUBLIC_KEY'),
            'secret_key' => env('WAVE_SECRET_KEY'),
            'environment' => env('WAVE_ENVIRONMENT', 'sandbox'),
        ],
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'genius_pay' => [
        'public_key' => env('GENIUS_PAY_PUBLIC_KEY'),
        'secret_key' => env('GENIUS_PAY_SECRET_KEY'),
        'webhook_secret' => env('GENIUS_PAY_WEBHOOK_SECRET'),
        'environment' => env('GENIUS_PAY_ENVIRONMENT', 'sandbox'),
        'base_url' => env('GENIUS_PAY_ENVIRONMENT', 'sandbox') === 'live' 
            ? 'https://pay.genius.ci/api/v1/merchant' 
            : 'https://pay.genius.ci/api/v1/merchant', // Usually documentation says the same endpoint or distinct ones, here we use what user provided
    ],
];
