<?php

namespace App\Services\Validators;

/**
 * CoachValidator - Business rules for "Coach de Crecimiento"
 *
 * Validates:
 * - Required fields: learning_path, estimated_duration, success_factors
 * - Max path steps: 10
 * - Valid durations: weeks | months
 * - Estimated duration range: 1-52
 */
class CoachValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Coach de Crecimiento';

    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['learning_path', 'estimated_duration', 'success_factors'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Learning path - max steps constraint
        if (isset($agentOutput['learning_path'])) {
            $maxSteps = $config['max_path_steps'] ?? 10;
            $violation = $this->validateMaxCount('learning_path', $agentOutput['learning_path'], $maxSteps);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Success factors - min constraint
        if (isset($agentOutput['success_factors'])) {
            $violation = $this->validateIsArray('success_factors', $agentOutput['success_factors']);
            if ($violation) {
                $violations[] = $violation;
            } else {
                $violation = $this->validateMinCount('success_factors', $agentOutput['success_factors'], 1);
                if ($violation) {
                    $violations[] = $violation;
                }
            }
        }

        // 4. Estimated duration - numeric range
        if (isset($agentOutput['estimated_duration'])) {
            $violation = $this->validateNumericRange('estimated_duration', $agentOutput['estimated_duration'], 1, 52);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 5. Valid durations enum
        if (isset($agentOutput['duration_unit'])) {
            $validDurations = $config['valid_durations'] ?? ['weeks', 'months'];
            $violation = $this->validateEnum('duration_unit', $agentOutput['duration_unit'], $validDurations);
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
