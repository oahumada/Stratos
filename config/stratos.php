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

    'compliance' => [
        'issuer_did' => env('COMPLIANCE_ISSUER_DID'),
        'verification_method_fragment' => env('COMPLIANCE_VERIFICATION_METHOD', 'stratos-digital-seal'),
        'verifier_version' => env('COMPLIANCE_VERIFIER_VERSION', '2026.03'),
        'policy_version' => env('COMPLIANCE_POLICY_VERSION', 'v1'),
    ],

    'lms' => [
        'certificate_issuance' => [
            'min_resource_completion_ratio' => (float) env('LMS_CERT_MIN_RESOURCE_RATIO', 0.70),
            'require_assessment_score' => (bool) env('LMS_CERT_REQUIRE_ASSESSMENT_SCORE', true),
            'min_assessment_score' => (float) env('LMS_CERT_MIN_ASSESSMENT_SCORE', 80),
        ],
    ],
];
