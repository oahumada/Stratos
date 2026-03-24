<?php

/**
 * Verification Configuration
 *
 * Defines the 4-phase rollout strategy for TalentVerificationService integration
 * into AiOrchestratorService as a quality gate for agent outputs.
 *
 * Phases:
 * - silent: Log violations, accept output (invisible to users)
 * - flagging: Include violations in response metadata (visible flag)
 * - reject: Reject invalid outputs, return error
 * - tuning: Reject + re-prompt with refinement (max 2 retries)
 */

return [
    /**
     * Enable/disable verification globally
     */
    'enabled' => env('VERIFICATION_ENABLED', true),

    /**
     * Current rollout phase
     *
     * Options: silent|flagging|reject|tuning
     * Recommend progression: silent (dev) → flagging (staging) → reject (prod) → tuning (optional)
     */
    'phase' => env('VERIFICATION_PHASE', 'silent'),

    /**
     * Phase definitions and behaviors
     */
    'phases' => [
        'silent' => [
            'description' => 'Log violations, accept output (invisible to users)',
            'log_violations' => true,
            'flag_response' => false,
            'reject_output' => false,
            're_prompt_enabled' => false,
        ],
        'flagging' => [
            'description' => 'Include violations in response metadata (visible flag)',
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => false,
            're_prompt_enabled' => false,
        ],
        'reject' => [
            'description' => 'Reject invalid outputs, return error',
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => true,
            're_prompt_enabled' => false,
        ],
        'tuning' => [
            'description' => 'Reject + re-prompt with refinement (max 2 retries)',
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => true,
            're_prompt_enabled' => true,
            'max_retries' => 2,
        ],
    ],

    /**
     * Confidence score thresholds
     *
     * Used to determine if output should be accepted, reviewed, or rejected
     */
    'thresholds' => [
        'confidence_high' => 0.85,    // >= 85% confidence: accept
        'confidence_medium' => 0.65,  // 65-85% confidence: review
        'confidence_low' => 0.40,     // < 40% confidence: reject
    ],

    /**
     * Audit trail settings
     */
    'audit' => [
        'log_enabled' => env('VERIFICATION_AUDIT_LOG', true),
        'include_violations' => true,
        'include_output_snippet' => true,
        'max_output_snippet_length' => 200,
    ],

    /**
     * Error response settings
     */
    'errors' => [
        'include_violations' => true,
        'include_confidence' => true,
        'include_recommendation' => true,
        'max_violation_messages' => 3,
    ],

    /**
     * Retry logic (tuning phase only)
     */
    'retry' => [
        'max_attempts' => 3, // 1 original + 2 retries
        'backoff_ms' => 100, // Wait before retry
        'prompt_refinement' => [
            'enabled' => true,
            'template' => 'verification_error_correction', // Prompt template for retries
        ],
    ],
];
