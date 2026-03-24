<?php

namespace App\Services\Validators;

use App\Data\VerificationViolation;

/**
 * CompetencyValidator - Business rules for "Curador de Competencias"
 *
 * Validates:
 * - Required fields: competencies_curated, proficiency_levels, recommendations
 * - Each proficiency level is enum (Beginner, Intermediate, Advanced, Expert)
 * - Max 10 competencies
 */
class CompetencyValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Curador de Competencias';

    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['competencies_curated', 'proficiency_levels', 'recommendations'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Competencies array validation - must be array
        if (isset($agentOutput['competencies_curated'])) {
            $violation = $this->validateIsArray('competencies_curated', $agentOutput['competencies_curated']);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Proficiency levels - each must be valid enum
        if (isset($agentOutput['proficiency_levels'])) {
            $allowedProficiencies = $config['proficiency_levels'] ?? ['Beginner', 'Intermediate', 'Advanced', 'Expert'];

            // Check that proficiency_levels is array
            $violation = $this->validateIsArray('proficiency_levels', $agentOutput['proficiency_levels']);
            if ($violation) {
                $violations[] = $violation;
            } else {
                // Validate each proficiency level in the array
                foreach ($agentOutput['proficiency_levels'] as $index => $proficiency) {
                    if (! in_array($proficiency, $allowedProficiencies, true)) {
                        $violations[] = new VerificationViolation(
                            field: "proficiency_levels[$index]",
                            field_value: (string) $proficiency,
                            rule: 'invalid_value',
                            message: "Invalid proficiency level '{$proficiency}'. Must be one of: ".implode(', ', $allowedProficiencies),
                        );
                    }
                }
            }
        }

        // 4. Max competencies count
        if (isset($agentOutput['competencies_curated'])) {
            $maxCompetencies = $config['max_competencies'] ?? 10;
            $violation = $this->validateMaxCount('competencies_curated', $agentOutput['competencies_curated'], $maxCompetencies);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 5. Recommendations string validation
        if (isset($agentOutput['recommendations'])) {
            $violation = $this->validateStringLength('recommendations', $agentOutput['recommendations'], maxLength: 3000, minLength: 10);
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
