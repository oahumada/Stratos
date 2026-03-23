<?php

namespace Tests\Unit\Data;

use App\Data\VerificationResult;
use App\Data\VerificationViolation;
use Tests\TestCase;

class VerificationResultTest extends TestCase
{
    public function test_verify_violation_can_be_created()
    {
        $violation = new VerificationViolation(
            rule: 'max_candidates',
            severity: 'error',
            message: 'Exceeded maximum candidates',
            field: 'candidates',
            received: 10,
            expected: 5
        );

        expect($violation->rule)->toBe('max_candidates');
        expect($violation->severity)->toBe('error');
        expect($violation->message)->toBe('Exceeded maximum candidates');
        expect($violation->received)->toBe(10);
        expect($violation->expected)->toBe(5);
    }

    public function test_violation_converts_to_array()
    {
        $violation = new VerificationViolation(
            rule: 'confidence_below_threshold',
            severity: 'warning',
            message: 'Confidence score too low',
            field: 'confidence_score',
            received: 0.4,
            expected: 0.65
        );

        $array = $violation->toArray();

        expect($array['rule'])->toBe('confidence_below_threshold');
        expect($array['severity'])->toBe('warning');
        expect($array['received'])->toBe(0.4);
        expect($array['expected'])->toBe(0.65);
    }

    public function test_violation_hydrates_from_array()
    {
        $data = [
            'rule' => 'invalid_strategy',
            'severity' => 'error',
            'message' => 'Strategy must be Buy, Build, or Borrow',
            'field' => 'strategy',
            'received' => 'Steal',
            'expected' => 'Buy|Build|Borrow',
        ];

        $violation = VerificationViolation::fromArray($data);

        expect($violation->rule)->toBe('invalid_strategy');
        expect($violation->severity)->toBe('error');
        expect($violation->received)->toBe('Steal');
    }

    public function test_verification_result_creates_with_defaults()
    {
        $result = new VerificationResult;

        expect($result->score)->toBe(1.0);
        expect($result->recommendation)->toBe('accept');
        expect($result->violations->isEmpty())->toBeTrue();
        expect($result->hallucinations->isEmpty())->toBeTrue();
        expect($result->isPassed())->toBeTrue();
    }

    public function test_verification_result_adds_violations()
    {
        $result = new VerificationResult;

        $result->addViolation(new VerificationViolation(
            rule: 'max_candidates',
            severity: 'error',
            message: 'Too many candidates'
        ));

        expect($result->violations->count())->toBe(1);
        expect($result->isPassed())->toBeFalse();
        expect($result->getErrorCount())->toBe(1);
    }

    public function test_verification_result_adds_hallucinations()
    {
        $result = new VerificationResult;

        $result->addHallucination('False claim about candidate experience');

        expect($result->hallucinations->count())->toBe(1);
        expect($result->getErrorCount())->toBe(1);
    }

    public function test_verification_result_adds_contradictions()
    {
        $result = new VerificationResult;

        $result->addContradiction('Claim says high performer but confidence score is 0.3');

        expect($result->contradictions->count())->toBe(1);
        expect($result->getErrorCount())->toBe(1);
    }

    public function test_verification_result_recalculates_score_on_multiple_issues()
    {
        $result = new VerificationResult;

        // Add violations
        $result->addViolation(new VerificationViolation(
            rule: 'test1',
            severity: 'error',
            message: 'Test'
        ));
        expect($result->recommendation)->toBe('review');

        $result->addViolation(new VerificationViolation(
            rule: 'test2',
            severity: 'error',
            message: 'Test'
        ));
        expect($result->recommendation)->toBe('review');

        $result->addViolation(new VerificationViolation(
            rule: 'test3',
            severity: 'error',
            message: 'Test'
        ));
        expect($result->recommendation)->toBe('review');

        // 4th issue should trigger 'reject'
        $result->addViolation(new VerificationViolation(
            rule: 'test4',
            severity: 'error',
            message: 'Test'
        ));
        expect($result->recommendation)->toBe('reject');
        expect($result->score)->toBeLessThan(0.3);
    }

    public function test_verification_result_converts_to_array()
    {
        $result = new VerificationResult(
            score: 0.75,
            recommendation: 'review',
            reasoning: 'Minor issues found',
            totalChecks: 5,
            passedChecks: 4,
            violations: [
                ['rule' => 'test1', 'severity' => 'warning', 'message' => 'Test'],
            ]
        );

        $array = $result->toArray();

        expect($array['score'])->toBe(0.75);
        expect($array['recommendation'])->toBe('review');
        expect($array['reasoning'])->toBe('Minor issues found');
        expect($array['summary']['total_checks'])->toBe(5);
        expect($array['summary']['passed_checks'])->toBe(4);
        expect(count($array['violations']))->toBe(1);
    }

    public function test_verification_result_hydrates_from_array()
    {
        $data = [
            'score' => 0.85,
            'recommendation' => 'accept',
            'reasoning' => 'All checks passed',
            'total_checks' => 3,
            'passed_checks' => 3,
            'violations' => [],
            'hallucinations' => ['False claim 1'],
            'contradictions' => [],
        ];

        $result = VerificationResult::fromArray($data);

        expect($result->score)->toBe(0.85);
        expect($result->hallucinations->count())->toBe(1);
    }

    public function test_verification_result_factory_passed()
    {
        $result = VerificationResult::passed('All good');

        expect($result->score)->toBe(1.0);
        expect($result->recommendation)->toBe('accept');
        expect($result->reasoning)->toBe('All good');
        expect($result->isPassed())->toBeTrue();
    }

    public function test_verification_result_factory_failed()
    {
        $result = VerificationResult::failed(
            'Multiple critical issues',
            [
                ['rule' => 'hallucination', 'severity' => 'error', 'message' => 'False info'],
            ]
        );

        expect($result->score)->toBe(0.2);
        expect($result->recommendation)->toBe('reject');
        expect($result->isPassed())->toBeFalse();
    }

    public function test_verification_result_factory_review()
    {
        $result = VerificationResult::review(
            'Needs human review',
            [
                ['rule' => 'contradiction', 'severity' => 'warning', 'message' => 'Inconsistent'],
            ]
        );

        expect($result->score)->toBe(0.5);
        expect($result->recommendation)->toBe('review');
    }

    public function test_verification_result_json_serialization()
    {
        $result = new VerificationResult(
            score: 0.8,
            recommendation: 'review',
            totalChecks: 5,
            passedChecks: 4
        );
        $result->addViolation(new VerificationViolation(
            rule: 'test',
            severity: 'warning',
            message: 'Test warning'
        ));

        $json = json_encode($result->toArray());
        $decoded = json_decode($json, true);

        expect($decoded['recommendation'])->toBe('review');
        expect(count($decoded['violations']))->toBe(1);
        expect($decoded['score'])->toBe(0.75); // Recalculated after adding violation
    }

    public function test_add_multiple_violations_at_once()
    {
        $violations = [
            ['rule' => 'test1', 'severity' => 'error', 'message' => 'Error 1'],
            ['rule' => 'test2', 'severity' => 'warning', 'message' => 'Warning 1'],
            ['rule' => 'test3', 'severity' => 'error', 'message' => 'Error 2'],
            ['rule' => 'test4', 'severity' => 'error', 'message' => 'Error 3'],
        ];

        $result = new VerificationResult;
        $result->addViolations($violations);

        expect($result->violations->count())->toBe(4);
        expect($result->recommendation)->toBe('reject');
    }
}
