<?php

namespace App\Services\Validators;

/**
 * RoleDesignerValidator - Business rules for "Diseñador de Roles"
 *
 * Validates:
 * - Required fields: role_name, key_competencies, role_level
 * - Role level enum: L1 | L2 | L3 | L4 | L5
 * - Max competencies: 8
 * - Role name length: max 200 chars
 */
class RoleDesignerValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Diseñador de Roles';

    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['role_name', 'key_competencies', 'role_level'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Role level enum validation
        if (isset($agentOutput['role_level'])) {
            $validLevels = $config['valid_role_levels'] ?? ['L1', 'L2', 'L3', 'L4', 'L5'];
            $violation = $this->validateEnum('role_level', $agentOutput['role_level'], $validLevels);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Role name string validation
        if (isset($agentOutput['role_name'])) {
            $violation = $this->validateStringLength('role_name', $agentOutput['role_name'], maxLength: 200, minLength: 3);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 4. Key competencies - max constraint
        if (isset($agentOutput['key_competencies'])) {
            $maxComps = $config['max_competencies'] ?? 8;
            $violation = $this->validateMaxCount('key_competencies', $agentOutput['key_competencies'], $maxComps);
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
