<?php

namespace App\Services\Validators;

/**
 * OrchestracionValidator - Business rules for "Orquestador 360"
 *
 * Validates:
 * - Required fields: evaluation_score, bias_detection, calibration
 * - Evaluation score range: 0-5
 * - Max biases: 3
 * - Calibration string: max 2000 chars
 */
class OrchestracionValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Orquestador 360';

    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['evaluation_score', 'bias_detection', 'calibration'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Evaluation score range
        if (isset($agentOutput['evaluation_score'])) {
            $minScore = $config['evaluation_score_min'] ?? 0.0;
            $maxScore = $config['evaluation_score_max'] ?? 5.0;
            $violation = $this->validateNumericRange('evaluation_score', $agentOutput['evaluation_score'], $minScore, $maxScore);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Bias detection must be array
        if (isset($agentOutput['bias_detection'])) {
            $violation = $this->validateIsArray('bias_detection', $agentOutput['bias_detection']);
            if ($violation) {
                $violations[] = $violation;
            }

            // Max biases constraint
            $maxBiases = $config['max_biases_to_report'] ?? 3;
            $violation = $this->validateMaxCount('bias_detection', $agentOutput['bias_detection'] ?? null, $maxBiases);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 4. Calibration string validation
        if (isset($agentOutput['calibration'])) {
            $violation = $this->validateStringLength('calibration', $agentOutput['calibration'], maxLength: 2000, minLength: 10);
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
