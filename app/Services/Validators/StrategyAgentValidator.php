<?php

namespace App\Services\Validators;

use App\Data\VerificationViolation;

/**
 * StrategyAgentValidator - Business rules for "Estratega de Talento"
 *
 * Validates:
 * - Required fields: strategy, reasoning, confidence_score
 * - Strategy enum: Buy | Build | Borrow
 * - Confidence score range: 0.5-1.0
 * - Max recommendations: 5
 * - Reasoning length: max 5000 chars
 */
class StrategyAgentValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Estratega de Talento';

    /**
     * Validate agent output against strategy-specific rules
     *
     * @param  array  $agentOutput  Agent-generated output
     * @return array ['valid' => bool, 'violations' => VerificationViolation[]]
     */
    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['strategy', 'reasoning', 'confidence_score'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Strategy enum validation
        if (isset($agentOutput['strategy'])) {
            $validStrategies = $config['valid_strategies'] ?? ['Buy', 'Build', 'Borrow'];
            $violation = $this->validateEnum('strategy', $agentOutput['strategy'], $validStrategies);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Confidence score range validation
        if (isset($agentOutput['confidence_score'])) {
            $minScore = $config['confidence_min'] ?? 0.5;
            $maxScore = $config['confidence_max'] ?? 1.0;
            $violation = $this->validateNumericRange('confidence_score', $agentOutput['confidence_score'], $minScore, $maxScore);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 4. Reasoning string validation
        if (isset($agentOutput['reasoning'])) {
            $violation = $this->validateStringLength('reasoning', $agentOutput['reasoning'], maxLength: 5000, minLength: 10);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 5. Max recommendations constraint
        if (isset($agentOutput['recommendations']) || isset($agentOutput['suggested_actions'])) {
            $fieldToCheck = isset($agentOutput['recommendations']) ? 'recommendations' : 'suggested_actions';
            $maxRecs = $config['max_recommendations'] ?? 5;
            $violation = $this->validateMaxCount($fieldToCheck, $agentOutput[$fieldToCheck] ?? null, $maxRecs);
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
