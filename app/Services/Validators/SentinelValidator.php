<?php

namespace App\Services\Validators;

/**
 * SentinelValidator - Business rules for "Stratos Sentinel"
 *
 * Validates:
 * - Required fields: ethics_score, governance_violations, compliance_notes
 * - Ethics score range: 0.7-1.0 (high bar for ethics)
 * - Max violations allowed: 0 (strict compliance)
 * - Compliance notes: max 2000 chars
 */
class SentinelValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Stratos Sentinel';

    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['ethics_score', 'governance_violations', 'compliance_notes'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Ethics score - HIGH BAR: must be 0.7-1.0
        if (isset($agentOutput['ethics_score'])) {
            $minEthics = $config['ethics_score_min'] ?? 0.7;
            $maxEthics = $config['ethics_score_max'] ?? 1.0;
            $violation = $this->validateNumericRange('ethics_score', $agentOutput['ethics_score'], $minEthics, $maxEthics);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Governance violations - must be array
        if (isset($agentOutput['governance_violations'])) {
            $violation = $this->validateIsArray('governance_violations', $agentOutput['governance_violations']);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 4. Strict mode: max violations allowed (typically 0)
        if (isset($agentOutput['governance_violations'])) {
            $maxViolations = $config['max_governance_violations_allowed'] ?? 0;
            $violation = $this->validateMaxCount('governance_violations', $agentOutput['governance_violations'], $maxViolations);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 5. Compliance notes string validation
        if (isset($agentOutput['compliance_notes'])) {
            $violation = $this->validateStringLength('compliance_notes', $agentOutput['compliance_notes'], maxLength: 2000, minLength: 10);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        return [
            'valid' => empty($violations),
            'violations' => $violations,
        ];
    }
}
