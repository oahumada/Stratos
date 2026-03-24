<?php

namespace App\Services;

use App\Data\VerificationResult;
use App\Data\VerificationViolation;
use Illuminate\Support\Facades\Log;

/**
 * TalentVerificationService — The Verifier (The Critic)
 *
 * Validates agent outputs against business rules, detects hallucinations,
 * contradictions, and multi-tenant compliance.
 *
 * Integrates with:
 * - config/verification_rules.php (centralized rules)
 * - RAGASEvaluator (hallucination detection)
 * - 5 validators (schema, business rules, hallucinations, contradictions, multi-tenant)
 */
class TalentVerificationService
{
    protected RAGASEvaluator $ragasEvaluator;

    protected AuditTrailService $auditTrail;

    public function __construct(
        RAGASEvaluator $ragasEvaluator,
        AuditTrailService $auditTrail
    ) {
        $this->ragasEvaluator = $ragasEvaluator;
        $this->auditTrail = $auditTrail;
    }

    /**
     * Main verification entry point
     *
     * @param  string  $agentId  Agent name/identifier
     * @param  array  $agentOutput  The agent's response (usually parsed JSON)
     * @param  array  $context  Additional context (organization_id, prompt, source_data, etc.)
     * @return VerificationResult Comprehensive verification result with score and recommendations
     */
    public function verify(
        string $agentId,
        array $agentOutput,
        array $context = []
    ): VerificationResult {
        $result = VerificationResult::passed('Starting verification');
        $organizationId = $context['organization_id'] ?? null;

        try {
            // Track total checks
            $result->totalChecks = 5;

            // 1. Multi-tenant validation (FIRST - security critical)
            $this->validateMultiTenant($agentId, $agentOutput, $context, $result);

            // 2. Schema validation (structural)
            $this->validateSchema($agentId, $agentOutput, $result);

            // 3. Business rules validation (per-agent rules)
            $this->validateBusinessRules($agentId, $agentOutput, $result);

            // 4. Hallucination detection (using RAGASEvaluator)
            $this->detectHallucinations($agentId, $agentOutput, $context, $result);

            // 5. Contradiction detection (logical consistency)
            $this->detectContradictions($agentId, $agentOutput, $result);

            // Log verification result
            $this->auditVerification($agentId, $agentOutput, $result, $organizationId);

            return $result;
        } catch (\Throwable $e) {
            Log::error('Verification service error', [
                'agent_id' => $agentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return failed result on critical error
            return VerificationResult::failed(
                'Verification failed: '.$e->getMessage(),
                [
                    new VerificationViolation(
                        rule: 'verification_service_error',
                        severity: 'error',
                        message: $e->getMessage()
                    ),
                ]
            );
        }
    }

    /**
     * 1. Multi-tenant validation - SECURITY CRITICAL
     */
    protected function validateMultiTenant(
        string $agentId,
        array $agentOutput,
        array $context,
        VerificationResult $result
    ): void {
        if (! config('verification_rules.global.validate_multi_tenant')) {
            return;
        }

        $organizationId = $context['organization_id'] ?? null;

        if (! $organizationId) {
            $result->addViolation(
                new VerificationViolation(
                    rule: 'missing_organization_id',
                    severity: 'error',
                    message: 'Organization ID is required for verification'
                )
            );

            return;
        }

        // Verify that output doesn't contain cross-org data
        $outputJson = json_encode($agentOutput);
        if (preg_match('/organization_id["\']?\s*:\s*(?!\b'.$organizationId.'\b)/', $outputJson)) {
            $result->addViolation(
                new VerificationViolation(
                    rule: 'cross_tenant_data_detected',
                    severity: 'error',
                    message: 'Output contains references to different organization_id'
                )
            );
        }
    }

    /**
     * 2. Schema validation - structural checks
     */
    protected function validateSchema(
        string $agentId,
        array $agentOutput,
        VerificationResult $result
    ): void {
        $globalConfig = config('verification_rules.global');

        // Length validation
        $outputStr = json_encode($agentOutput);
        $length = strlen($outputStr);

        if ($length > $globalConfig['max_response_length']) {
            $result->addViolation(
                new VerificationViolation(
                    rule: 'max_length_exceeded',
                    severity: 'warning',
                    message: "Response length {$length} exceeds max {$globalConfig['max_response_length']}"
                )
            );
        }

        if ($length < $globalConfig['min_response_length']) {
            $result->addViolation(
                new VerificationViolation(
                    rule: 'min_length_violated',
                    severity: 'error',
                    message: "Response too short (minimum {$globalConfig['min_response_length']} characters)"
                )
            );
        }

        // Agent-specific required fields
        $agentRules = config("verification_rules.agents.{$agentId}");
        if ($agentRules && isset($agentRules['required_fields'])) {
            foreach ($agentRules['required_fields'] as $field) {
                if (! isset($agentOutput[$field]) || empty($agentOutput[$field])) {
                    $result->addViolation(
                        new VerificationViolation(
                            rule: 'required_field_missing',
                            severity: 'error',
                            message: "Required field '{$field}' is missing",
                            field: $field
                        )
                    );
                }
            }
        }
    }

    /**
     * 3. Business rules validation - per-agent logic
     */
    protected function validateBusinessRules(
        string $agentId,
        array $agentOutput,
        VerificationResult $result
    ): void {
        $agentConfig = config("verification_rules.agents.{$agentId}");

        if (! $agentConfig) {
            Log::warning("No verification rules found for agent: {$agentId}");

            return;
        }

        // Validate max constraints
        $constraints = [
            'max_recommendations' => fn ($v) => is_countable($v) ? count($v) : 0,
            'max_candidates' => fn ($v) => is_countable($v) ? count($v) : 0,
            'max_biases_to_report' => fn ($v) => is_countable($v) ? count($v) : 0,
            'max_path_steps' => fn ($v) => is_countable($v) ? count($v) : 0,
            'max_competencies' => fn ($v) => is_countable($v) ? count($v) : 0,
            'max_anomalies_to_report' => fn ($v) => is_countable($v) ? count($v) : 0,
        ];

        foreach ($constraints as $constraint => $getter) {
            if (isset($agentConfig[$constraint])) {
                foreach ($agentOutput as $key => $value) {
                    $count = $getter($value);
                    if ($count > $agentConfig[$constraint]) {
                        $result->addViolation(
                            new VerificationViolation(
                                rule: 'constraint_violated',
                                severity: 'warning',
                                message: "{$key} exceeds limit of {$agentConfig[$constraint]} (got {$count})"
                            )
                        );
                    }
                }
            }
        }

        // Validate enum values
        if (isset($agentConfig['valid_strategies'])) {
            if (isset($agentOutput['strategy']) && ! in_array($agentOutput['strategy'], $agentConfig['valid_strategies'], true)) {
                $result->addViolation(
                    new VerificationViolation(
                        rule: 'invalid_value',
                        severity: 'error',
                        message: "Invalid strategy: {$agentOutput['strategy']}",
                        field: 'strategy',
                        received: $agentOutput['strategy'],
                        expected: implode(', ', $agentConfig['valid_strategies'])
                    )
                );
            }
        }

        if (isset($agentConfig['valid_role_levels'])) {
            if (isset($agentOutput['role_level']) && ! in_array($agentOutput['role_level'], $agentConfig['valid_role_levels'], true)) {
                $result->addViolation(
                    new VerificationViolation(
                        rule: 'invalid_value',
                        severity: 'error',
                        message: "Invalid role_level: {$agentOutput['role_level']}",
                        field: 'role_level',
                        received: $agentOutput['role_level'],
                        expected: implode(', ', $agentConfig['valid_role_levels'])
                    )
                );
            }
        }

        // Validate numeric ranges
        $ranges = [
            'confidence_score' => ['confidence_min', 'confidence_max'],
            'evaluation_score' => ['evaluation_score_min', 'evaluation_score_max'],
            'ethics_score' => ['ethics_score_min', 'ethics_score_max'],
            'sentiment_score' => ['sentiment_min', 'sentiment_max'],
        ];

        foreach ($ranges as $field => $minMaxKeys) {
            if (isset($agentOutput[$field])) {
                $value = $agentOutput[$field];
                [$minKey, $maxKey] = $minMaxKeys;

                if (isset($agentConfig[$minKey]) && $value < $agentConfig[$minKey]) {
                    $result->addViolation(
                        new VerificationViolation(
                            rule: 'threshold_exceeded',
                            severity: 'warning',
                            message: "{$field} {$value} is below minimum {$agentConfig[$minKey]}",
                            field: $field,
                            received: (string) $value,
                            expected: '>= '.$agentConfig[$minKey]
                        )
                    );
                }

                if (isset($agentConfig[$maxKey]) && $value > $agentConfig[$maxKey]) {
                    $result->addViolation(
                        new VerificationViolation(
                            rule: 'threshold_exceeded',
                            severity: 'warning',
                            message: "{$field} {$value} exceeds maximum {$agentConfig[$maxKey]}",
                            field: $field,
                            received: (string) $value,
                            expected: '<= '.$agentConfig[$maxKey]
                        )
                    );
                }
            }
        }
    }

    /**
     * 4. Hallucination detection - using RAGASEvaluator
     */
    protected function detectHallucinations(
        string $agentId,
        array $agentOutput,
        array $context,
        VerificationResult $result
    ): void {
        $hallucConfig = config('verification_rules.hallucination_detection');

        if (! $hallucConfig['enabled'] || ! $hallucConfig['use_ragas_evaluator']) {
            return;
        }

        try {
            $outputStr = json_encode($agentOutput);

            // Respect sample size limit
            if (strlen($outputStr) > $hallucConfig['sample_size']) {
                $outputStr = substr($outputStr, 0, $hallucConfig['sample_size']);
            }

            // Call RAGAS evaluator
            $sourcePrompt = $context['prompt'] ?? '';
            $sourceContext = $context['source_data'] ?? '';
            $organizationId = $context['organization_id'] ?? null;

            $evaluation = $this->ragasEvaluator->evaluate(
                inputPrompt: $sourcePrompt,
                outputContent: $outputStr,
                organizationId: (string) $organizationId,
                context: $sourceContext,
                provider: $context['provider'] ?? null
            );

            // Check hallucination rate
            if ($evaluation->hallucination_rate > $hallucConfig['threshold']) {
                $result->addHallucination(
                    "Hallucination rate {$evaluation->hallucination_rate} exceeds threshold {$hallucConfig['threshold']}"
                );

                $result->addViolation(
                    new VerificationViolation(
                        rule: 'hallucination_detected',
                        severity: 'error',
                        message: "High hallucination risk detected ({$evaluation->hallucination_rate})"
                    )
                );
            }

            // Also check other RAGAS metrics
            if ($evaluation->faithfulness_score < 0.75) {
                $result->addViolation(
                    new VerificationViolation(
                        rule: 'low_faithfulness',
                        severity: 'warning',
                        message: "Faithfulness score {$evaluation->faithfulness_score} is below 0.75"
                    )
                );
            }
        } catch (\Throwable $e) {
            Log::warning('RAGAS evaluation failed', [
                'agent_id' => $agentId,
                'error' => $e->getMessage(),
            ]);

            // Don't fail verification if RAGAS service is unavailable
            // Just log and continue
        }
    }

    /**
     * 5. Contradiction detection - logical consistency checks
     */
    protected function detectContradictions(
        string $agentId,
        array $agentOutput,
        VerificationResult $result
    ): void {
        $contradictConfig = config('verification_rules.contradiction_detection');

        if (! $contradictConfig['enabled']) {
            return;
        }

        $contradictions = [];

        // Field consistency check
        if ($contradictConfig['check_field_consistency']) {
            $contradictions = array_merge($contradictions, $this->checkFieldConsistency($agentOutput));
        }

        // Logical consistency check
        if ($contradictConfig['check_logical_consistency']) {
            $contradictions = array_merge($contradictions, $this->checkLogicalConsistency($agentOutput));
        }

        foreach ($contradictions as $contradiction) {
            $result->addContradiction($contradiction);
        }
    }

    /**
     * Check field consistency (e.g., status conflicts, date ordering)
     */
    protected function checkFieldConsistency(array $output): array
    {
        $contradictions = [];

        // Example: if approved flag is true, review_date should not be empty
        if (isset($output['approved']) && $output['approved'] === true && empty($output['approved_date'])) {
            $contradictions[] = 'Item marked as approved but has no approval date';
        }

        // Example: confidence_score should not be high if reasoning is empty
        if (isset($output['confidence_score']) && $output['confidence_score'] > 0.8 && empty($output['reasoning'])) {
            $contradictions[] = 'High confidence score without reasoning justification';
        }

        return $contradictions;
    }

    /**
     * Check logical consistency (e.g., sum constraints, dependency violations)
     */
    protected function checkLogicalConsistency(array $output): array
    {
        $contradictions = [];

        // Example: if recommending "Buy", training hours should be 0
        if (isset($output['strategy']) && $output['strategy'] === 'Buy' && isset($output['training_hours']) && $output['training_hours'] > 0) {
            $contradictions[] = '\'Buy\' strategy should not include training hours';
        }

        // Example: if no candidates matched, cultural_fit_score should not be set
        if ((empty($output['matched_candidates']) || count($output['matched_candidates']) === 0) && isset($output['cultural_fit_score']) && $output['cultural_fit_score'] > 0) {
            $contradictions[] = 'Cultural fit score set even though no candidates matched';
        }

        return $contradictions;
    }

    /**
     * Audit the verification result
     */
    protected function auditVerification(
        string $agentId,
        array $agentOutput,
        VerificationResult $result,
        ?int $organizationId
    ): void {
        try {
            $this->auditTrail->log([
                'action' => 'agent_output_verified',
                'agent_id' => $agentId,
                'organization_id' => $organizationId,
                'verification_score' => $result->score,
                'recommendation' => $result->recommendation,
                'error_count' => $result->getErrorCount(),
                'passed' => $result->isPassed(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to audit verification', ['error' => $e->getMessage()]);
        }
    }
}
