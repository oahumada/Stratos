<?php

namespace Tests\Unit\Services;

use App\DTOs\VerificationResult;
use App\Services\VerificationIntegrationService;
use Tests\TestCase;

/**
 * Unit Tests for VerificationIntegrationService
 *
 * Tests the verification integration logic and phase decision making
 */
class VerificationIntegrationServiceTest extends TestCase
{
    protected VerificationIntegrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(VerificationIntegrationService::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function get_current_phase_returns_configured_phase(): void
    {
        config(['verification.phase' => 'silent']);
        expect($this->service->getCurrentPhase())->toBe('silent');

        config(['verification.phase' => 'flagging']);
        expect($this->service->getCurrentPhase())->toBe('flagging');

        config(['verification.phase' => 'reject']);
        expect($this->service->getCurrentPhase())->toBe('reject');

        config(['verification.phase' => 'tuning']);
        expect($this->service->getCurrentPhase())->toBe('tuning');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decide_action_silent_phase_always_accepts(): void
    {
        config(['verification.phase' => 'silent']);

        // Invalid result but should accept in silent
        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.3,
            recommendation: 'reject',
            violations: [['rule' => 'test', 'field' => 'test']],
            agentName: 'TestAgent',
            phase: 'silent'
        );

        $action = $this->service->decideAction($result);

        expect($action->type)->toBe('accept');
        expect($action->shouldAccept())->toBeTrue();
        expect($action->shouldReject())->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decide_action_flagging_phase_flags_invalid(): void
    {
        config(['verification.phase' => 'flagging']);

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.65,
            recommendation: 'review',
            violations: [['rule' => 'test', 'field' => 'test']],
            agentName: 'TestAgent',
            phase: 'flagging'
        );

        $action = $this->service->decideAction($result);

        expect($action->type)->toBe('flag_review');
        expect($action->shouldFlag())->toBeTrue();
        expect($action->shouldReject())->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decide_action_flagging_phase_accepts_valid(): void
    {
        config(['verification.phase' => 'flagging']);

        $result = new VerificationResult(
            valid: true,
            confidenceScore: 1.0,
            recommendation: 'accept',
            violations: [],
            agentName: 'TestAgent',
            phase: 'flagging'
        );

        $action = $this->service->decideAction($result);

        expect($action->type)->toBe('accept');
        expect($action->shouldAccept())->toBeTrue();
        expect($action->shouldFlag())->toBeFalse();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decide_action_reject_phase_rejects_invalid(): void
    {
        config(['verification.phase' => 'reject']);

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.2,
            recommendation: 'reject',
            violations: [['rule' => 'test', 'field' => 'test']],
            agentName: 'TestAgent',
            phase: 'reject'
        );

        $action = $this->service->decideAction($result);

        expect($action->type)->toBe('reject');
        expect($action->shouldReject())->toBeTrue();
        expect($action->errorMessage)->not->toBeEmpty();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decide_action_reject_phase_accepts_valid(): void
    {
        config(['verification.phase' => 'reject']);

        $result = new VerificationResult(
            valid: true,
            confidenceScore: 1.0,
            recommendation: 'accept',
            violations: [],
            agentName: 'TestAgent',
            phase: 'reject'
        );

        $action = $this->service->decideAction($result);

        expect($action->type)->toBe('accept');
        expect($action->shouldAccept())->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function decide_action_tuning_phase_enables_retry(): void
    {
        config(['verification.phase' => 'tuning']);

        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.2,
            recommendation: 'reject',
            violations: [['rule' => 'test', 'field' => 'test']],
            agentName: 'TestAgent',
            phase: 'tuning'
        );

        $action = $this->service->decideAction($result);

        expect($action->type)->toBe('reject');
        expect($action->shouldRetry)->toBeTrue();
        expect($action->canRetry())->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_result_confidence_score_correct(): void
    {
        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.75,
            recommendation: 'review',
            violations: [['rule' => 'test', 'field' => 'test']],
            agentName: 'TestAgent',
            phase: 'silent'
        );

        expect($result->confidenceScore)->toBe(0.75);
        expect($result->toArray()['confidence_score'])->toBe(0.75);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_result_should_flag_determines_correctly(): void
    {
        $acceptResult = new VerificationResult(
            valid: true,
            confidenceScore: 1.0,
            recommendation: 'accept',
            violations: [],
            agentName: 'TestAgent',
            phase: 'silent'
        );

        expect($acceptResult->shouldFlag())->toBeFalse();

        $reviewResult = new VerificationResult(
            valid: false,
            confidenceScore: 0.65,
            recommendation: 'review',
            violations: [['rule' => 'test', 'field' => 'test']],
            agentName: 'TestAgent',
            phase: 'silent'
        );

        expect($reviewResult->shouldFlag())->toBeTrue();

        $rejectResult = new VerificationResult(
            valid: false,
            confidenceScore: 0.2,
            recommendation: 'reject',
            violations: [['rule' => 'test', 'field' => 'test']],
            agentName: 'TestAgent',
            phase: 'silent'
        );

        expect($rejectResult->shouldFlag())->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_result_human_readable_errors_generated(): void
    {
        $result = new VerificationResult(
            valid: false,
            confidenceScore: 0.3,
            recommendation: 'reject',
            violations: [
                ['rule' => 'required_field_missing', 'field' => 'strategy', 'message' => 'Required field missing'],
                ['rule' => 'below_minimum', 'field' => 'score', 'message' => 'Score below minimum'],
            ],
            agentName: 'TestAgent',
            phase: 'silent'
        );

        $errors = $result->getHumanReadableErrors();
        expect($errors)->not->toBeEmpty();
        expect($errors)->toContain('Required field missing');
    }
}
