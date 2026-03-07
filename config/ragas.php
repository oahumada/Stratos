<?php

/**
 * RAGAS Configuration for LLM Evaluation (Provider-Agnostic)
 *
 * Supports multiple LLM providers: DeepSeek, ABACUS, OpenAI, Intel, Mock
 * Each provider has configurable baselines, weights, and thresholds
 */

return [
    /**
     * Provider-specific configurations and baselines
     * Used to normalize evaluation scores across different LLM providers
     */
    'providers' => [
        'deepseek' => [
            'name' => 'DeepSeek',
            'baseline_score' => 0.82,      // Historical average quality
            'weight' => 1.0,               // Full weight in aggregated metrics
            'max_retries' => 3,
            'timeout_seconds' => 60,
        ],
        'abacus' => [
            'name' => 'ABACUS AI',
            'baseline_score' => 0.88,      // Higher baseline (legacy system)
            'weight' => 1.0,
            'max_retries' => 3,
            'timeout_seconds' => 90,
        ],
        'openai' => [
            'name' => 'OpenAI',
            'baseline_score' => 0.90,      // GPT-4 higher baseline
            'weight' => 1.0,
            'max_retries' => 3,
            'timeout_seconds' => 60,
        ],
        'intel' => [
            'name' => 'Intel LLM',
            'baseline_score' => 0.75,      // Experimental/lower performance
            'weight' => 1.0,
            'max_retries' => 2,
            'timeout_seconds' => 120,
        ],
        'mock' => [
            'name' => 'Mock Provider',
            'baseline_score' => 0.95,      // Perfect for testing
            'weight' => 0.0,               // Zero weight in production metrics
            'max_retries' => 0,
            'timeout_seconds' => 1,
        ],
    ],

    /**
     * RAGAS Metrics Thresholds (per metric)
     * Defines minimum acceptable quality standards
     */
    'thresholds' => [
        'faithfulness' => [
            'min' => 0.85,                 // Min consistency with source
            'target' => 0.95,              // Target score
            'weight' => 0.30,              // 30% of composite score
        ],
        'relevance' => [
            'min' => 0.80,                 // Min relevance to query
            'target' => 0.90,
            'weight' => 0.25,              // 25% of composite score
        ],
        'context_alignment' => [
            'min' => 0.75,                 // Min alignment with context
            'target' => 0.88,
            'weight' => 0.20,              // 20% of composite score
        ],
        'coherence' => [
            'min' => 0.70,                 // Min structural coherence
            'target' => 0.85,
            'weight' => 0.15,              // 15% of composite score
        ],
        'hallucination' => [
            'max' => 0.15,                 // Max allowed hallucination rate
            'target' => 0.05,
            'weight' => 0.10,              // 10% of composite score (inverse)
        ],
    ],

    /**
     * Evaluation modes
     * - 'strict': All metrics must pass min threshold
     * - 'balanced': Weighted average must exceed min_composite_score
     * - 'lenient': At least 70% of metrics pass min threshold
     */
    'evaluation_mode' => env('RAGAS_MODE', 'balanced'),

    /**
     * Composite score thresholds
     */
    'min_composite_score' => env('RAGAS_MIN_COMPOSITE', 0.80),
    'target_composite_score' => env('RAGAS_TARGET_COMPOSITE', 0.88),

    /**
     * Caching and job configuration
     */
    'cache_ttl_hours' => env('RAGAS_CACHE_TTL', 24),
    'evaluation_queue' => env('RAGAS_QUEUE', 'default'),
    'batch_size' => env('RAGAS_BATCH_SIZE', 10),

    /**
     * Historical tracking
     * Keep evaluation history for trend analysis
     */
    'keep_history_days' => env('RAGAS_HISTORY_DAYS', 90),
    'track_trends' => env('RAGAS_TRACK_TRENDS', true),

    /**
     * Provider detection strategy
     * How to determine which provider generated the content
     */
    'auto_detect_provider' => true,
    'default_provider' => env('LLM_PROVIDER', 'mock'),
];
