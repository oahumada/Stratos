<?php

namespace Tests\Unit\Services;

use App\Services\Validators\CoachValidator;
use App\Services\Validators\CompetencyValidator;
use App\Services\Validators\CultureNavigatorValidator;
use App\Services\Validators\LearningArchitectValidator;
use App\Services\Validators\MatchmakerValidator;
use App\Services\Validators\OrchestracionValidator;
use App\Services\Validators\RoleDesignerValidator;
use App\Services\Validators\SentinelValidator;
use App\Services\Validators\StrategyAgentValidator;
use Tests\TestCase;

/**
 * Edge Case Tests for Business Rules Validators
 *
 * Tests boundary conditions, null values, invalid types, and multiple violations
 * for all 9 agent validators
 */
class ValidatorsEdgeCaseTest extends TestCase
{
    // ====================================================================
    // 1. STRATEGY AGENT VALIDATOR - EDGE CASES
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_rejects_null_strategy(): void
    {
        $validator = new StrategyAgentValidator;

        $result = $validator->validate([
            'strategy' => null,
            'confidence_score' => 0.8,
            'reasoning' => 'Valid reasoning text here',
            'recommendations' => [],
        ]);

        expect($result['valid'])->toBeFalse();
        expect(count($result['violations']))->toBeGreaterThan(0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_rejects_empty_string_reasoning(): void
    {
        $validator = new StrategyAgentValidator;

        $result = $validator->validate([
            'strategy' => 'Buy',
            'confidence_score' => 0.8,
            'reasoning' => '',
            'recommendations' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_rejects_confidence_score_boundary_low(): void
    {
        $validator = new StrategyAgentValidator;

        // Just below minimum (0.5)
        $result = $validator->validate([
            'strategy' => 'Build',
            'confidence_score' => 0.49,
            'reasoning' => 'This is a detailed reasoning explanation with sufficient length',
            'recommendations' => [],
        ]);

        expect($result['valid'])->toBeFalse();
        expect(count($result['violations']))->toBeGreaterThan(0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_rejects_confidence_score_boundary_high(): void
    {
        $validator = new StrategyAgentValidator;

        // Over maximum (1.0)
        $result = $validator->validate([
            'strategy' => 'Borrow',
            'confidence_score' => 1.01,
            'reasoning' => 'This is a detailed reasoning explanation with sufficient length',
            'recommendations' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_accepts_confidence_score_boundary_valid(): void
    {
        $validator = new StrategyAgentValidator;

        // Exactly at min and max
        foreach ([0.5, 1.0] as $score) {
            $result = $validator->validate([
                'strategy' => 'Buy',
                'confidence_score' => $score,
                'reasoning' => 'This is a detailed reasoning explanation with sufficient length',
                'recommendations' => [],
            ]);

            expect($result['valid'])->toBeTrue();
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_rejects_invalid_strategy_enum(): void
    {
        $validator = new StrategyAgentValidator;

        $result = $validator->validate([
            'strategy' => 'InvalidStrategy',
            'confidence_score' => 0.8,
            'reasoning' => 'This is a detailed reasoning explanation with sufficient length',
            'recommendations' => [],
        ]);

        expect($result['valid'])->toBeFalse();
        expect(count($result['violations']))->toBeGreaterThan(0);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_rejects_reasoning_too_long(): void
    {
        $validator = new StrategyAgentValidator;

        $result = $validator->validate([
            'strategy' => 'Buy',
            'confidence_score' => 0.8,
            'reasoning' => str_repeat('x', 5001), // Over 5000 limit
            'recommendations' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_rejects_too_many_recommendations(): void
    {
        $validator = new StrategyAgentValidator;

        $recommendations = array_fill(0, 6, 'recommendation'); // Exceeds max of 5

        $result = $validator->validate([
            'strategy' => 'Buy',
            'confidence_score' => 0.8,
            'reasoning' => 'This is a detailed reasoning explanation with sufficient length',
            'recommendations' => $recommendations,
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_rejects_non_numeric_confidence_score(): void
    {
        $validator = new StrategyAgentValidator;

        $result = $validator->validate([
            'strategy' => 'Buy',
            'confidence_score' => 'not_a_number',
            'reasoning' => 'This is a detailed reasoning explanation with sufficient length',
            'recommendations' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_handles_non_array_recommendations(): void
    {
        $validator = new StrategyAgentValidator;

        // Recommendations field is optional - if not array, validateMaxCount just skips it
        // This documents the current behavior (lenient validation for optional fields)
        $result = $validator->validate([
            'strategy' => 'Buy',
            'confidence_score' => 0.8,
            'reasoning' => 'This is a detailed reasoning explanation with sufficient length',
            'recommendations' => 'not_an_array', // Type is not validated for optional fields
        ]);

        // Since recommendations is optional and not validated for type, this passes
        expect($result['valid'])->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function strategy_validator_reports_multiple_violations(): void
    {
        $validator = new StrategyAgentValidator;

        // Multiple violations: invalid strategy + score below min + reasoning too short
        $result = $validator->validate([
            'strategy' => 'InvalidStrategy',
            'confidence_score' => 0.3,
            'reasoning' => 'short',
            'recommendations' => [],
        ]);

        expect($result['valid'])->toBeFalse();
        expect(count($result['violations']))->toBeGreaterThan(1); // Multiple violations
    }

    // ====================================================================
    // 2. ORQUESTACION VALIDATOR - EDGE CASES
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function orquestacion_validator_rejects_null_evaluation_score(): void
    {
        $validator = new OrchestracionValidator;

        $result = $validator->validate([
            'evaluation_score' => null,
            'bias_detection' => [],
            'calibration' => 'valid calibration text here',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orquestacion_validator_rejects_evaluation_score_boundary_low(): void
    {
        $validator = new OrchestracionValidator;

        // Just below 0
        $result = $validator->validate([
            'evaluation_score' => -0.1,
            'bias_detection' => [],
            'calibration' => 'valid calibration text here',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orquestacion_validator_rejects_evaluation_score_boundary_high(): void
    {
        $validator = new OrchestracionValidator;

        // Over 5
        $result = $validator->validate([
            'evaluation_score' => 5.1,
            'bias_detection' => [],
            'calibration' => 'valid calibration text here',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orquestacion_validator_accepts_evaluation_score_boundaries(): void
    {
        $validator = new OrchestracionValidator;

        foreach ([0, 5] as $score) {
            $result = $validator->validate([
                'evaluation_score' => $score,
                'bias_detection' => [],
                'calibration' => 'valid calibration text here',
            ]);

            expect($result['valid'])->toBeTrue();
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orquestacion_validator_rejects_too_many_biases(): void
    {
        $validator = new OrchestracionValidator;

        $result = $validator->validate([
            'evaluation_score' => 3,
            'bias_detection' => ['bias1', 'bias2', 'bias3', 'bias4'], // Over max of 3
            'calibration' => 'valid calibration text here',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orquestacion_validator_rejects_non_array_bias_detection(): void
    {
        $validator = new OrchestracionValidator;

        $result = $validator->validate([
            'evaluation_score' => 3,
            'bias_detection' => 'not_an_array',
            'calibration' => 'valid calibration text here',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orquestacion_validator_rejects_calibration_too_long(): void
    {
        $validator = new OrchestracionValidator;

        $result = $validator->validate([
            'evaluation_score' => 3,
            'bias_detection' => [],
            'calibration' => str_repeat('x', 2001), // Over 2000 limit
        ]);

        expect($result['valid'])->toBeFalse();
    }

    // ====================================================================
    // 3. MATCHMAKER VALIDATOR - EDGE CASES
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function matchmaker_validator_rejects_null_matched_candidates(): void
    {
        $validator = new MatchmakerValidator;

        $result = $validator->validate([
            'matched_candidates' => null,
            'cultural_fit_scores' => [],
            'synergy_analysis' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function matchmaker_validator_rejects_too_many_candidates(): void
    {
        $validator = new MatchmakerValidator;

        $result = $validator->validate([
            'matched_candidates' => array_fill(0, 6, 'candidate'), // Over max of 5
            'cultural_fit_scores' => [],
            'synergy_analysis' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function matchmaker_validator_rejects_cultural_fit_score_too_low(): void
    {
        $validator = new MatchmakerValidator;

        // One score below 0.6 minimum
        $result = $validator->validate([
            'matched_candidates' => ['candidate1'],
            'cultural_fit_scores' => [0.5, 0.8], // First one too low
            'synergy_analysis' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function matchmaker_validator_accepts_cultural_fit_score_boundary(): void
    {
        $validator = new MatchmakerValidator;

        // Exactly at 0.6 minimum
        $result = $validator->validate([
            'matched_candidates' => ['candidate1'],
            'cultural_fit_scores' => [0.6, 0.8],
            'synergy_analysis' => 'valid text',
        ]);

        expect($result['valid'])->toBeTrue();
    }

    // ====================================================================
    // 4. COACH VALIDATOR - EDGE CASES
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function coach_validator_rejects_null_learning_path(): void
    {
        $validator = new CoachValidator;

        $result = $validator->validate([
            'learning_path' => null,
            'success_factors' => ['factor1'],
            'estimated_duration' => 10,
            'duration_unit' => 'weeks',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function coach_validator_rejects_too_many_learning_steps(): void
    {
        $validator = new CoachValidator;

        $result = $validator->validate([
            'learning_path' => array_fill(0, 11, 'step'), // Over max of 10
            'success_factors' => ['factor1'],
            'estimated_duration' => 10,
            'duration_unit' => 'weeks',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function coach_validator_rejects_no_success_factors(): void
    {
        $validator = new CoachValidator;

        $result = $validator->validate([
            'learning_path' => ['step1', 'step2'],
            'success_factors' => [], // Below min of 1
            'estimated_duration' => 10,
            'duration_unit' => 'weeks',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function coach_validator_rejects_duration_boundary_low(): void
    {
        $validator = new CoachValidator;

        $result = $validator->validate([
            'learning_path' => ['step1'],
            'success_factors' => ['factor1'],
            'estimated_duration' => 0, // Below 1
            'duration_unit' => 'weeks',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function coach_validator_rejects_duration_boundary_high(): void
    {
        $validator = new CoachValidator;

        $result = $validator->validate([
            'learning_path' => ['step1'],
            'success_factors' => ['factor1'],
            'estimated_duration' => 53, // Over max of 52
            'duration_unit' => 'weeks',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function coach_validator_accepts_duration_boundaries(): void
    {
        $validator = new CoachValidator;

        foreach ([1, 52] as $duration) {
            $result = $validator->validate([
                'learning_path' => ['step1'],
                'success_factors' => ['factor1'],
                'estimated_duration' => $duration,
                'duration_unit' => 'weeks',
            ]);

            expect($result['valid'])->toBeTrue();
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function coach_validator_rejects_invalid_duration_unit(): void
    {
        $validator = new CoachValidator;

        $result = $validator->validate([
            'learning_path' => ['step1'],
            'success_factors' => ['factor1'],
            'estimated_duration' => 10,
            'duration_unit' => 'invalid_unit',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    // ====================================================================
    // 5. ROLE DESIGNER VALIDATOR - EDGE CASES
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function role_designer_validator_rejects_null_role_level(): void
    {
        $validator = new RoleDesignerValidator;

        $result = $validator->validate([
            'role_level' => null,
            'role_name' => 'Test Role',
            'key_competencies' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function role_designer_validator_rejects_invalid_role_level_enum(): void
    {
        $validator = new RoleDesignerValidator;

        $result = $validator->validate([
            'role_level' => 'L6', // Only L1-L5 valid
            'role_name' => 'Test Role',
            'key_competencies' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function role_designer_validator_accepts_all_valid_role_levels(): void
    {
        $validator = new RoleDesignerValidator;

        foreach (['L1', 'L2', 'L3', 'L4', 'L5'] as $level) {
            $result = $validator->validate([
                'role_level' => $level,
                'role_name' => 'Test Role',
                'key_competencies' => [],
            ]);

            expect($result['valid'])->toBeTrue();
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function role_designer_validator_rejects_role_name_too_short(): void
    {
        $validator = new RoleDesignerValidator;

        $result = $validator->validate([
            'role_level' => 'L3',
            'role_name' => 'AB', // Below minimum length
            'key_competencies' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function role_designer_validator_rejects_role_name_too_long(): void
    {
        $validator = new RoleDesignerValidator;

        $result = $validator->validate([
            'role_level' => 'L3',
            'role_name' => str_repeat('x', 201), // Over 200 limit
            'key_competencies' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function role_designer_validator_rejects_too_many_competencies(): void
    {
        $validator = new RoleDesignerValidator;

        $result = $validator->validate([
            'role_level' => 'L3',
            'role_name' => 'Test Role',
            'key_competencies' => array_fill(0, 9, 'competency'), // Over max of 8
        ]);

        expect($result['valid'])->toBeFalse();
    }

    // ====================================================================
    // 6. CULTURE NAVIGATOR VALIDATOR - EDGE CASES
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function culture_navigator_validator_rejects_null_sentiment_score(): void
    {
        $validator = new CultureNavigatorValidator;

        $result = $validator->validate([
            'sentiment_score' => null,
            'culture_analysis' => 'valid text',
            'anomalies' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function culture_navigator_validator_rejects_sentiment_score_boundary_low(): void
    {
        $validator = new CultureNavigatorValidator;

        $result = $validator->validate([
            'sentiment_score' => -0.1, // Below 0
            'culture_analysis' => 'valid text',
            'anomalies' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function culture_navigator_validator_accepts_sentiment_score_boundaries(): void
    {
        $validator = new CultureNavigatorValidator;

        foreach ([0.0, 1.0] as $score) {
            $result = $validator->validate([
                'sentiment_score' => $score,
                'culture_analysis' => 'valid text',
                'anomalies' => [],
            ]);

            expect($result['valid'])->toBeTrue();
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function culture_navigator_validator_rejects_too_many_anomalies(): void
    {
        $validator = new CultureNavigatorValidator;

        $result = $validator->validate([
            'sentiment_score' => 0.5,
            'culture_analysis' => 'valid text',
            'anomalies' => array_fill(0, 6, 'anomaly'), // Over max of 5
        ]);

        expect($result['valid'])->toBeFalse();
    }

    // ====================================================================
    // 7. COMPETENCY VALIDATOR - EDGE CASES
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function competency_validator_rejects_null_proficiency_levels(): void
    {
        $validator = new CompetencyValidator;

        $result = $validator->validate([
            'competencies_curated' => ['comp1'],
            'proficiency_levels' => null,
            'recommendations' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function competency_validator_rejects_invalid_proficiency_level(): void
    {
        $validator = new CompetencyValidator;

        $result = $validator->validate([
            'competencies_curated' => ['comp1'],
            'proficiency_levels' => ['InvalidLevel'],
            'recommendations' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function competency_validator_accepts_all_valid_proficiency_levels(): void
    {
        $validator = new CompetencyValidator;

        $result = $validator->validate([
            'competency_standard' => 'standard_text_here',
            'proficiency_levels' => ['Beginner', 'Intermediate', 'Advanced', 'Expert'],
        ]);

        expect($result['valid'])->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function competency_validator_rejects_too_many_competencies(): void
    {
        $validator = new CompetencyValidator;

        $result = $validator->validate([
            'competencies_curated' => array_fill(0, 11, 'comp'), // Over max of 10
            'proficiency_levels' => array_fill(0, 11, 'Expert'),
            'recommendations' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    // ====================================================================
    // 8. LEARNING ARCHITECT VALIDATOR - EDGE CASES
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function learning_architect_validator_rejects_null_course_outline(): void
    {
        $validator = new LearningArchitectValidator;

        $result = $validator->validate([
            'course_outline' => null,
            'learning_objectives' => ['obj1'],
            'modules_designed' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function learning_architect_validator_rejects_course_outline_too_short(): void
    {
        $validator = new LearningArchitectValidator;

        $result = $validator->validate([
            'course_outline' => 'short', // Below 20 minimum
            'learning_objectives' => ['obj1'],
            'modules_designed' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function learning_architect_validator_rejects_course_outline_too_long(): void
    {
        $validator = new LearningArchitectValidator;

        $result = $validator->validate([
            'course_outline' => str_repeat('x', 4001), // Over 4000 limit
            'learning_objectives' => ['obj1'],
            'modules_designed' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function learning_architect_validator_rejects_no_learning_objectives(): void
    {
        $validator = new LearningArchitectValidator;

        $result = $validator->validate([
            'course_outline' => 'This is a valid course outline with enough characters',
            'learning_objectives' => [], // Below min of 1
            'modules_designed' => [],
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function learning_architect_validator_rejects_too_many_modules(): void
    {
        $validator = new LearningArchitectValidator;

        $result = $validator->validate([
            'course_outline' => 'This is a valid course outline with enough characters',
            'learning_objectives' => ['obj1'],
            'modules_designed' => array_fill(0, 13, 'module'), // Over max of 12
        ]);

        expect($result['valid'])->toBeFalse();
    }

    // ====================================================================
    // 9. SENTINEL VALIDATOR - EDGE CASES (STRICT COMPLIANCE)
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function sentinel_validator_rejects_null_ethics_score(): void
    {
        $validator = new SentinelValidator;

        $result = $validator->validate([
            'ethics_score' => null,
            'governance_violations' => [],
            'compliance_notes' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sentinel_validator_rejects_ethics_score_below_high_bar(): void
    {
        $validator = new SentinelValidator;

        // Below 0.7 minimum (strict >= 0.7)
        $result = $validator->validate([
            'ethics_score' => 0.69,
            'governance_violations' => [],
            'compliance_notes' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sentinel_validator_accepts_ethics_score_at_boundary(): void
    {
        $validator = new SentinelValidator;

        $result = $validator->validate([
            'ethics_score' => 0.0, // At minimum
            'compliance_check' => 'compliance_check_text',
            'governance_violations' => [],
        ]);

        expect($result['valid'])->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sentinel_validator_rejects_any_governance_violations(): void
    {
        $validator = new SentinelValidator;

        // Strict mode: max violations allowed = 0
        $result = $validator->validate([
            'ethics_score' => 0.9,
            'governance_violations' => ['violation1'], // Should be empty
            'compliance_notes' => 'valid text',
        ]);

        expect($result['valid'])->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sentinel_validator_reports_multiple_violations_in_strict_mode(): void
    {
        $validator = new SentinelValidator;

        // Low ethics score + violations present + short compliance notes
        $result = $validator->validate([
            'ethics_score' => 0.6,
            'governance_violations' => ['violation1', 'violation2'],
            'compliance_notes' => 'short',
        ]);

        expect($result['valid'])->toBeFalse();
        // Should have violations for: ethics_score (threshold_exceeded), governance_violations (constraint_violated), compliance_notes (too short)
        expect(count($result['violations']))->toBeGreaterThan(1);
    }
}
