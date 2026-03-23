<?php

/**
 * Configuración de reglas de verificación para agentes
 *
 * Define qué reglas de negocio debe validar el VerifierAgent
 * para cada tipo de agente y contexto.
 */

return [
    /*
     * ====================================================================
     * Reglas Globales (aplican a todos los agentes)
     * ====================================================================
     */
    'global' => [
        'max_response_length' => 50000,         // Máximo characters en output
        'min_response_length' => 10,            // Mínimo characters (evita respuestas vacías)
        'require_json_schema' => false,         // Forzar JSON válido
        'validate_multi_tenant' => true,        // Verificar organization_id match
        'detect_hallucinations' => true,        // Usar RAGAS evaluator
        'detect_contradictions' => true,        // Verificar auto-consistencia
    ],

    /*
     * ====================================================================
     * Reglas por Agente (especializado)
     * ====================================================================
     */
    'agents' => [
        'Estratega de Talento' => [
            'max_recommendations' => 5,
            'required_fields' => ['strategy', 'reasoning', 'confidence_score'],
            'valid_strategies' => ['Buy', 'Build', 'Borrow'],
            'confidence_min' => 0.5,
            'confidence_max' => 1.0,
            'schema_validation' => [
                'strategy' => 'string|in:Buy,Build,Borrow',
                'confidence_score' => 'numeric|between:0.5,1',
                'reasoning' => 'string|max:5000',
            ],
        ],

        'Orquestador 360' => [
            'required_fields' => ['evaluation_score', 'bias_detection', 'calibration'],
            'evaluation_score_min' => 0.0,
            'evaluation_score_max' => 5.0,
            'max_biases_to_report' => 3,
            'schema_validation' => [
                'evaluation_score' => 'numeric|between:0,5',
                'bias_detection' => 'array',
                'calibration' => 'string|max:2000',
            ],
        ],

        'Matchmaker de Resonancia' => [
            'max_candidates' => 5,
            'required_fields' => ['matched_candidates', 'cultural_fit_scores'],
            'min_cultural_fit_score' => 0.6,
            'schema_validation' => [
                'matched_candidates' => 'array|min:1|max:5',
                'cultural_fit_scores' => 'array',
                'synergy_analysis' => 'string|max:3000',
            ],
        ],

        'Coach de Crecimiento' => [
            'required_fields' => ['learning_path', 'estimated_duration', 'success_factors'],
            'max_path_steps' => 10,
            'valid_durations' => ['weeks', 'months'],
            'schema_validation' => [
                'learning_path' => 'array|min:1|max:10',
                'estimated_duration' => 'numeric|between:1,52',
                'success_factors' => 'array|min:1|max:5',
            ],
        ],

        'Diseñador de Roles' => [
            'required_fields' => ['role_name', 'key_competencies', 'role_level'],
            'max_competencies' => 8,
            'valid_role_levels' => ['L1', 'L2', 'L3', 'L4', 'L5'],
            'schema_validation' => [
                'role_name' => 'string|max:200',
                'key_competencies' => 'array|min:1|max:8',
                'role_level' => 'string|in:L1,L2,L3,L4,L5',
            ],
        ],

        'Navegador de Cultura' => [
            'required_fields' => ['sentiment_score', 'culture_analysis', 'anomalies'],
            'sentiment_min' => 0.0,
            'sentiment_max' => 1.0,
            'max_anomalies_to_report' => 5,
        ],

        'Curador de Competencias' => [
            'required_fields' => ['competency_standard', 'proficiency_levels'],
            'max_proficiency_levels' => 5,
            'valid_levels' => ['Beginner', 'Intermediate', 'Advanced', 'Expert', 'Master'],
        ],

        'Arquitecto de Aprendizaje' => [
            'required_fields' => ['course_outline', 'learning_objectives', 'assessment_plan'],
            'max_modules' => 20,
            'max_objectives_per_module' => 5,
        ],

        'Stratos Sentinel' => [
            'required_fields' => ['ethics_score', 'compliance_check', 'governance_violations'],
            'ethics_score_min' => 0.0,
            'ethics_score_max' => 100.0,
            'max_violations_allowed' => 0,  // Zero tolerance
        ],
    ],

    /*
     * ====================================================================
     * Severidades de Violaciones
     * ====================================================================
     */
    'violation_severity_mapping' => [
        'required_field_missing' => 'error',
        'invalid_value' => 'error',
        'hallucination_detected' => 'error',
        'contradiction_detected' => 'warning',
        'threshold_exceeded' => 'warning',
        'schema_mismatch' => 'error',
        'multi_tenant_violation' => 'error',
        'performance_acceptable' => 'info',
    ],

    /*
     * ====================================================================
     * Confidence Thresholds
     * ====================================================================
     */
    'confidence_thresholds' => [
        'high' => 0.85,          // Green: Muy seguro
        'medium' => 0.65,        // Yellow: Moderadamente seguro
        'low' => 0.4,            // Red: Bajo confianza
    ],

    /*
     * ====================================================================
     * Hallucination Detection Config
     * ====================================================================
     */
    'hallucination_detection' => [
        'enabled' => true,
        'threshold' => 0.3,      // Si hallucination_rate > 30%, marcar como error
        'use_ragas_evaluator' => true,
        'sample_size' => 500,    // Máximo characters a evaluar
    ],

    /*
     * ====================================================================
     * Contradiction Detection Config
     * ====================================================================
     */
    'contradiction_detection' => [
        'enabled' => true,
        'check_field_consistency' => true,
        'check_temporal_consistency' => false,
        'check_logical_consistency' => true,
    ],

    /*
     * ====================================================================
     * Cache Config
     * ====================================================================
     */
    'cache' => [
        'rules_ttl' => 3600 * 24,  // Cache verification rules for 24h
    ],
];
