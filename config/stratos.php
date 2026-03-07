<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stratos Custom Configuration
    |--------------------------------------------------------------------------
    */

    'qa' => [
        'e2e_bypass' => env('E2E_BYPASS', false),
        'e2e_admin_id' => env('E2E_ADMIN_ID', 2),
    ],

    'llm' => [
        'default_provider' => env('LLM_PROVIDER', 'mock'),
        'api_key' => env('LLM_API_KEY'),
        'openai_model' => env('LLM_OPENAI_MODEL', 'gpt-4o'),
        'deepseek_api_key' => env('DEEPSEEK_API_KEY'),
    ],
];
