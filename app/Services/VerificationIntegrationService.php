<?php

namespace App\Services;

use App\DTOs\VerificationAction;
use App\DTOs\VerificationResult;
use App\Exceptions\UnauthorizedTenantException;
use App\Services\Validators\ValidatorFactory;
use Illuminate\Support\Facades\Log;

/**
 * VerificationIntegrationService - Bridge between AiOrchestratorService & TalentVerificationService
 *
 * Responsibilities:
 * 1. Verify agent output using appropriate validator
 * 2. Calculate confidence score based on violations
 * 3. Generate recommendation (accept|review|reject)
 * 4. Decide action based on current rollout phase
 * 5. Log verification events for audit trail
 */
class VerificationIntegrationService
{
    public function __construct(
        protected TalentVerificationService $verifier,
        protected AuditService $audit,
    ) {}

    /**
     * Verify agent output and return detailed result
     *
     * @throws UnauthorizedTenantException
     */
    public function verifyAgentOutput(
        string $agentName,
        array $output,
        array $context = []
    ): VerificationResult {
        // 1. Extract and validate organization context
        $organizationId = $context['organization_id'] ?? null;
        if (auth()->check() && auth()->user()->organization_id !== $organizationId) {
            throw new UnauthorizedTenantException(
                "Organization mismatch: authenticated user org != verification context org"
            );
        }

        // 2. Instantiate validator for this agent
        try {
            $validator = ValidatorFactory::make($agentName);
        } catch (\Exception $e) {
            Log::warning('Validator not found for agent', ['agent' => $agentName]);
            // Fallback: if no validator, accept output (graceful degradation)
            return $this->createNeutralResult($agentName);
        }

        // 3. Validate agent output
        $validationResult = $validator->validate($output);

        // 4. Calculate confidence score
        $confidenceScore = $this->calculateConfidenceScore($validationResult);

        // 5. Generate recommendation
        $recommendation = $this->generateRecommendation($validationResult, $confidenceScore);

        // 6. Create VerificationResult
        $result = new VerificationResult(
            valid: $validationResult['valid'] ?? false,
            confidenceScore: $confidenceScore,
            recommendation: $recommendation,
            violations: $validationResult['violations'] ?? [],
            agentName: $agentName,
            phase: $this->getCurrentPhase(),
            metadata: [
                'timestamp' => now(),
                'organization_id' => $organizationId,
                'task_snippet' => substr($context['task_prompt'] ?? '', 0, 100),
                'provider' => $context['provider'] ?? config('stratos.llm.provider'),
            ]
        );

        // 7. Audit log the verification
        $this->auditVerification($result, $context);

        return $result;
    }

    /**
     * Get current verification rollout phase
     */
    public function getCurrentPhase(): string
    {
        return config('verification.phase', 'silent');
    }

    /**
     * Decide action based on verification result and current phase
     */
    public function decideAction(VerificationResult $result): VerificationAction
    {
        $phase = $result->phase;

        // Phase 1: Silent - always accept, just log
        if ($phase === 'silent') {
            return new VerificationAction(
                type: 'accept',
                phase: $phase,
                responseMetadata: $this->buildMetadata($result)
            );
        }

        // Phase 2: Flagging - accept but flag if invalid
        if ($phase === 'flagging') {
            if ($result->recommendation === 'reject' || $result->recommendation === 'review') {
                return new VerificationAction(
                    type: 'flag_review',
                    phase: $phase,
                    responseMetadata: $this->buildMetadata($result),
                    errorMessage: $result->getHumanReadableErrors()
                );
            }

            return new VerificationAction(
                type: 'accept',
                phase: $phase,
                responseMetadata: $this->buildMetadata($result)
            );
        }

        // Phase 3: Reject - reject invalid output
        if ($phase === 'reject') {
            if ($result->recommendation === 'accept') {
                return new VerificationAction(
                    type: 'accept',
                    phase: $phase,
                    responseMetadata: $this->buildMetadata($result)
                );
            }

            return new VerificationAction(
                type: 'reject',
                phase: $phase,
                errorMessage: $result->getHumanReadableErrors(),
                responseMetadata: $result->toArray()
            );
        }

        // Phase 4: Tuning - reject but allow retry
        if ($phase === 'tuning') {
            if ($result->recommendation === 'accept') {
                return new VerificationAction(
                    type: 'accept',
                    phase: $phase,
                    responseMetadata: $this->buildMetadata($result)
                );
            }

            return new VerificationAction(
                type: 'reject',
                phase: $phase,
                shouldRetry: true,
                errorMessage: $result->getHumanReadableErrors(),
                responseMetadata: $result->toArray()
            );
        }

        // Default: accept (safety)
        return new VerificationAction(
            type: 'accept',
            phase: $phase,
            responseMetadata: $this->buildMetadata($result)
        );
    }

    /**
     * Calculate confidence score based on violations
     *
     * Algorithm:
     * - 0 violations → 1.0 (perfect)
     * - 1-2 violations → 0.65-0.85 (needs review)
     * - 3+ violations → <0.40 (low confidence)
     */
    private function calculateConfidenceScore(array $validationResult): float
    {
        $violationCount = count($validationResult['violations'] ?? []);

        if ($violationCount === 0) {
            return 1.0;
        }

        if ($violationCount === 1) {
            return 0.85;
        }

        if ($violationCount === 2) {
            return 0.65;
        }

        if ($violationCount >= 3) {
            return max(0.1, 0.40 - ($violationCount - 3) * 0.05);
        }

        return 0.5; // Default fallback
    }

    /**
     * Generate recommendation based on validation and confidence
     */
    private function generateRecommendation(array $validationResult, float $confidenceScore): string
    {
        if ($validationResult['valid'] ?? false) {
            return 'accept';
        }

        if ($confidenceScore >= config('verification.thresholds.confidence_medium', 0.65)) {
            return 'review';
        }

        return 'reject';
    }

    /**
     * Create neutral result when validator not found (graceful degradation)
     */
    private function createNeutralResult(string $agentName): VerificationResult
    {
        return new VerificationResult(
            valid: true,
            confidenceScore: 0.5,
            recommendation: 'accept',
            violations: [],
            agentName: $agentName,
            phase: $this->getCurrentPhase(),
            metadata: ['graceful_degradation' => true, 'reason' => 'validator_not_found']
        );
    }

    /**
     * Build metadata object for response
     */
    private function buildMetadata(VerificationResult $result): array
    {
        return [
            'valid' => $result->valid,
            'confidence' => $result->confidenceScore,
            'recommendation' => $result->recommendation,
            'violations' => count($result->violations),
            'phase' => $result->phase,
            'flagged' => $result->shouldFlag(),
        ];
    }

    /**
     * Log verification event to audit trail
     */
    private function auditVerification(VerificationResult $result, array $context): void
    {
        try {
            $this->audit->log(
                eventName: 'agent_output_verified',
                aggregateType: 'Agent',
                aggregateId: $context['agent_id'] ?? null,
                data: [
                    'agent_name' => $result->agentName,
                    'verification_valid' => $result->valid,
                    'confidence_score' => $result->confidenceScore,
                    'recommendation' => $result->recommendation,
                    'violations_count' => count($result->violations),
                    'violations' => array_map(fn ($v) => [
                        'rule' => $v->rule ?? $v['rule'],
                        'field' => $v->field ?? $v['field'],
                    ], $result->violations),
                    'phase' => $result->phase,
                    'timestamp' => now()->toIso8601String(),
                ],
                organizationId: $context['organization_id'] ?? auth()->user()?->organization_id
            );
        } catch (\Exception $e) {
            Log::warning('Failed to audit verification', ['error' => $e->getMessage()]);
        }
    }
}
