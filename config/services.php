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

    // config/services.php
'n8n' => [
    'webhook_url' => env('N8N_WEBHOOK_URL', 'https://n8n.example.com/webhook/stratos'),
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
        'model' => env('ABACUS_MODEL', null),
        'timeout' => env('ABACUS_TIMEOUT', 120),
        'retries' => env('ABACUS_RETRIES', 3),
        'stream_idle_timeout' => env('ABACUS_STREAM_IDLE_TIMEOUT', 600),
        'chunks_ttl_days' => env('ABACUS_CHUNKS_TTL_DAYS', 30),
    ],

    'embeddings' => [
        'provider' => env('EMBEDDINGS_PROVIDER', 'mock'),
        'model' => env('EMBEDDINGS_MODEL', 'text-embedding-3-small'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    'python_intel' => [
        'base_url' => env('PYTHON_INTEL_URL', 'http://localhost:8000'),
        'timeout' => env('PYTHON_INTEL_TIMEOUT', 30),
        'default_provider' => env('INTEL_DEFAULT_PROVIDER', 'intel'),
    ],

];
