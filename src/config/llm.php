<?php

return [
    'provider' => env('LLM_PROVIDER', 'mock'),

    'mock' => [
        // Optional mock response for tests
        'model_version' => env('LLM_MOCK_VERSION', 'mock-1.0'),
        'response' => null,
        // For testing: simulate rate limit (429) and optional retry-after seconds
        'simulate_429' => env('LLM_MOCK_SIMULATE_429', false),
        'simulate_429_retry_after' => env('LLM_MOCK_SIMULATE_429_RETRY_AFTER', null),
    ],

    'openai' => [
        'api_key' => env('LLM_API_KEY', null),
        'model' => env('LLM_OPENAI_MODEL', 'gpt-4o'),
        'endpoint' => env('LLM_OPENAI_ENDPOINT', 'https://api.openai.com/v1/chat/completions'),
    ],
];
