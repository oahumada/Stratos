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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'abacus' => [
        'base_url' => env('ABACUS_BASE_URL', 'https://api.abacus.ai'),
        'stream_url' => env('ABACUS_STREAM_URL', null),
        'key' => env('ABACUS_API_KEY'),
        'model' => env('ABACUS_MODEL', 'abacus-default'),
        'timeout' => env('ABACUS_TIMEOUT', 120),
        'retries' => env('ABACUS_RETRIES', 3),
        'stream_idle_timeout' => env('ABACUS_STREAM_IDLE_TIMEOUT', 120),
        'chunks_ttl_days' => env('ABACUS_CHUNKS_TTL_DAYS', 30),
    ],

];
