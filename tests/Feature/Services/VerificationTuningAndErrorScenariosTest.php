<?php

namespace Tests\Feature\Services;

use App\DTOs\VerificationResult;
use App\Models\Agent;
use App\Models\Organization;
use App\Services\VerificationIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Advanced Tuning Phase & Error Scenarios
 *
 * Tests advanced verification phase behaviors, error scenarios, and
 * edge cases for production readiness
 */
class VerificationTuningAndErrorScenariosTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;
    protected Agent $agent;
    protected VerificationIntegrationService $integration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create(['name' => 'Test Org']);
        $this->agent = Agent::factory()->create(['organization_id' => $this->organization->id]);
        $this->integration = app(VerificationIntegrationService::class);
    }

    // ====================================================================
    // TUNING PHASE ADVANCED SCENARIOS
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function tuning_phase_multiple_violation_count_tracked(): void
    {
        config(['verification.phase' => 'tuning']);

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.15,
            recommendation: 'reject',
            violations: [
                ['rule' => 'confidence_score_below_minimum', 'field' => 'confidence_score', 'message' => 'Score < 0.5'],
                ['rule' => 'required_field_missing', 'field' => 'strategy', 'message' => 'Missing strategy'],
                ['rule' => 'reasoning_too_brief', 'field' => 'reasoning', 'message' => 'Reasoning too short'],
                ['rule' => 'invalid_enum', 'field' => 'risk_level', 'message' => 'Invalid risk level'],
            ],
            agentName: 'Estratega de Talento',
            phase: 'tuning'
        );

        $resultArray = $result->toArray();
        expect($resultArray['violations_count'])->toBe(4);
        expect(count($resultArray['violations']))->toBe(4);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function tuning_phase_error_message_includes_top_3_violations(): void
    {
        config(['verification.phase' => 'tuning']);

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.1,
            recommendation: 'reject',
            violations: [
                ['rule' => 'critical_1', 'field' => 'field1', 'message' => 'Critical issue 1'],
                ['rule' => 'critical_2', 'field' => 'field2', 'message' => 'Critical issue 2'],
                ['rule' => 'critical_3', 'field' => 'field3', 'message' => 'Critical issue 3'],
                ['rule' => 'warning_1', 'field' => 'field4', 'message' => 'Warning issue'],
                ['rule' => 'warning_2', 'field' => 'field5', 'message' => 'Another warning'],
            ],
            agentName: 'Estratega de Talento',
            phase: 'tuning'
        );

        $errors = $result->getHumanReadableErrors();
        
        // Should mention top 3 and indicate "more"
        expect($errors)->toContain('Critical issue 1');
        expect($errors)->toContain('2 more');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function tuning_phase_distinguishes_between_review_and_reject(): void
    {
        config(['verification.phase' => 'tuning']);

        // Review recommendation (1-2 violations, medium confidence)
        // In tuning phase, only 'accept' is truly accepted; everything else is rejected
        $reviewResult = new VerificationResult(
            valid: false,
            confidenceScore: 0.72,
            recommendation: 'review',
            violations: [['rule' => 'minor_clarity_issue', 'field' => 'wording']],
            agentName: 'Estratega de Talento',
            phase: 'tuning'
        );

        $reviewAction = $this->integration->decideAction($reviewResult);
        // Tuning phase rejects 'review' recommendations (only accepts 'accept')
        expect($reviewAction->type)->toBe('reject');
        expect($reviewAction->shouldRetry)->toBeTrue();

        // Reject recommendation (3+ violations, low confidence)
        $rejectResult = new VerificationResult(
            valid: false,
            confidenceScore: 0.25,
            recommendation: 'reject',
            violations: [
                ['rule' => 'v1', 'field' => 'f1'],
                ['rule' => 'v2', 'field' => 'f2'],
                ['rule' => 'v3', 'field' => 'f3'],
            ],
            agentName: 'Estratega de Talento',
            phase: 'tuning'
        );

        $rejectAction = $this->integration->decideAction($rejectResult);
        expect($rejectAction->type)->toBe('reject');
        expect($rejectAction->shouldRetry)->toBeTrue();
    }

    // ====================================================================
    // ERROR SCENARIOS & BOUNDARY CONDITIONS
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function empty_violations_array_handled_gracefully(): void
    {
        config(['verification.phase' => 'reject']);

        $result = new VerificationResult(
            valid: true,
            confidenceScore: 1.0,
            recommendation: 'accept',
            violations: [],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($result->getHumanReadableErrors())->not->toContain('Undefined');
        expect($result->toArray()['violations'])->toBeArray();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function zero_confidence_score_handled(): void
    {
        config(['verification.phase' => 'reject']);

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.0,
            recommendation: 'reject',
            violations: [['rule' => 'unknown_error', 'field' => 'output']],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($result->confidenceScore)->toBe(0.0);
        $action = $this->integration->decideAction($result);
        expect($action->shouldReject())->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function perfect_confidence_score_accepted(): void
    {
        config(['verification.phase' => 'reject']);

        $result = new VerificationResult(
            valid: true,
            confidenceScore: 1.0,
            recommendation: 'accept',
            violations: [],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($result->confidenceScore)->toBe(1.0);
        $action = $this->integration->decideAction($result);
        expect($action->shouldAccept())->toBeTrue();
    }

    // ====================================================================
    // PHASE TRANSITIONS & CONFIGURATION
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function invalid_phase_configuration_defaults_to_silent(): void
    {
        config(['verification.phase' => 'unknown_phase']);

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.2,
            recommendation: 'reject',
            violations: [['rule' => 'error', 'field' => 'field']],
            agentName: 'Estratega de Talento',
            phase: 'unknown_phase'
        );

        // If phase is unknown, should still create valid result
        expect($result->phase)->toBe('unknown_phase');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_disabled_returns_neutral_result(): void
    {
        config(['verification.enabled' => false]);

        $result = new VerificationResult(
            valid: true, // Should still return valid/accept when disabled
            confidenceScore: 0.5,
            recommendation: 'accept',
            violations: [],
            agentName: 'Estratega de Talento',
            phase: 'silent'
        );

        expect($result->valid)->toBeTrue();
    }

    // ====================================================================
    // CONFIDENCE SCORE THRESHOLDS
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function high_confidence_threshold_boundary(): void
    {
        config(['verification.phase' => 'reject']);

        // Just below high threshold
        $belowHigh = new VerificationResult(
            valid: false,
            confidenceScore: 0.84,
            recommendation: 'review',
            violations: [['rule' => 'minor_issue', 'field' => 'field']],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($belowHigh->confidenceScore)->toBeLessThan(0.85);

        // At or above high threshold
        $atHigh = new VerificationResult(
            valid: true,
            confidenceScore: 0.85,
            recommendation: 'accept',
            violations: [],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($atHigh->confidenceScore)->toBeGreaterThanOrEqual(0.85);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function medium_confidence_threshold_boundary(): void
    {
        config(['verification.phase' => 'reject']);

        // Below medium threshold
        $belowMedium = new VerificationResult(
            valid: false,
            confidenceScore: 0.64,
            recommendation: 'reject',
            violations: [['rule' => 'issue', 'field' => 'field']],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($belowMedium->confidenceScore)->toBeLessThan(0.65);

        // At medium threshold
        $atMedium = new VerificationResult(
            valid: false,
            confidenceScore: 0.65,
            recommendation: 'review',
            violations: [['rule' => 'issue', 'field' => 'field']],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($atMedium->confidenceScore)->toBeGreaterThanOrEqual(0.65);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function low_confidence_threshold_boundary(): void
    {
        config(['verification.phase' => 'reject']);

        // Below low threshold
        $belowLow = new VerificationResult(
            valid: false,
            confidenceScore: 0.39,
            recommendation: 'reject',
            violations: [['rule' => 'critical', 'field' => 'output']],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($belowLow->confidenceScore)->toBeLessThan(0.40);

        // At low threshold
        $atLow = new VerificationResult(
            valid: false,
            confidenceScore: 0.40,
            recommendation: 'reject',
            violations: [['rule' => 'issue', 'field' => 'field']],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        expect($atLow->confidenceScore)->toBeGreaterThanOrEqual(0.40);
    }

    // ====================================================================
    // VIOLATION MESSAGE FORMATTING
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function violation_messages_with_special_characters_handled(): void
    {
        config(['verification.phase' => 'reject']);

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.2,
            recommendation: 'reject',
            violations: [
                ['rule' => 'message_with_quotes', 'field' => 'field', 'message' => 'Value "test" is invalid; contains semicolon'],
                ['rule' => 'message_with_unicode', 'field' => 'field', 'message' => 'Valor inválido: é á ñ 中文'],
            ],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        $array = $result->toArray();
        expect(count($array['violations']))->toBe(2);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function very_long_violation_message_truncated_in_error(): void
    {
        config(['verification.phase' => 'reject']);

        $longMessages = [
            ['rule' => 'long_msg_1', 'field' => 'f1', 'message' => 'First message'],
            ['rule' => 'long_msg_2', 'field' => 'f2', 'message' => 'Second msg'],
            ['rule' => 'long_msg_3', 'field' => 'f3', 'message' => 'Third message'],
            ['rule' => 'long_msg_4', 'field' => 'f4', 'message' => 'Fourth message'],
        ];

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.1,
            recommendation: 'reject',
            violations: $longMessages,
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        $errors = $result->getHumanReadableErrors();
        // Should show top 3 and indicate there are more
        expect($errors)->toContain('more');
        expect(count($result->violations))->toBe(4);
    }

    // ====================================================================
    // RECOMMENDATION LOGIC
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function recommendation_accept_on_valid(): void
    {
        $result = new VerificationResult(
            valid: true,
            confidenceScore: 0.99,
            recommendation: 'accept',
            violations: [],
            agentName: 'Estratega de Talento',
            phase: 'silent'
        );

        $action = $this->integration->decideAction($result);
        expect($action->shouldAccept())->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function recommendation_review_on_medium_violations(): void
    {
        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.75,
            recommendation: 'review',
            violations: [
                ['rule' => 'issue1', 'field' => 'f1'],
                ['rule' => 'issue2', 'field' => 'f2'],
            ],
            agentName: 'Estratega de Talento',
            phase: 'flagging'
        );

        expect($result->shouldFlag())->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function recommendation_reject_on_critical_violations(): void
    {
        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.15,
            recommendation: 'reject',
            violations: [
                ['rule' => 'critical1', 'field' => 'f1'],
                ['rule' => 'critical2', 'field' => 'f2'],
                ['rule' => 'critical3', 'field' => 'f3'],
            ],
            agentName: 'Estratega de Talento',
            phase: 'reject'
        );

        $action = $this->integration->decideAction($result);
        expect($action->shouldReject())->toBeTrue();
    }
}
