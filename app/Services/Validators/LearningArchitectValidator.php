<?php

namespace App\Services\Validators;

/**
 * LearningArchitectValidator - Business rules for "Arquitecto de Aprendizaje"
 *
 * Validates:
 * - Required fields: course_outline, learning_objectives, modules_designed
 * - Course outline string length: 20-4000 chars
 * - Max modules: 12
 * - Min objectives: 1
 */
class LearningArchitectValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Arquitecto de Aprendizaje';

    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['course_outline', 'learning_objectives', 'modules_designed'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Course outline string validation
        if (isset($agentOutput['course_outline'])) {
            $violation = $this->validateStringLength('course_outline', $agentOutput['course_outline'], maxLength: 4000, minLength: 20);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Modules designed - must be array
        if (isset($agentOutput['modules_designed'])) {
            $violation = $this->validateIsArray('modules_designed', $agentOutput['modules_designed']);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 4. Max modules constraint
        if (isset($agentOutput['modules_designed'])) {
            $maxModules = $config['max_modules_designed'] ?? 12;
            $violation = $this->validateMaxCount('modules_designed', $agentOutput['modules_designed'], $maxModules);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 5. Learning objectives - must be array with min 1
        if (isset($agentOutput['learning_objectives'])) {
            $violation = $this->validateIsArray('learning_objectives', $agentOutput['learning_objectives']);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 6. Min objective count - at least 1
        if (isset($agentOutput['learning_objectives'])) {
            $minObjectives = $config['min_learning_objectives'] ?? 1;
            $violation = $this->validateMinCount('learning_objectives', $agentOutput['learning_objectives'], $minObjectives);
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
