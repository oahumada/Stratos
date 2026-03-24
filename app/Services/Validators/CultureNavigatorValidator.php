<?php

namespace App\Services\Validators;

/**
 * CultureNavigatorValidator - Business rules for "Navegador de Cultura"
 *
 * Validates:
 * - Required fields: sentiment_score, culture_analysis, anomalies
 * - Sentiment score range: 0.0-1.0
 * - Max anomalies to report: 5
 */
class CultureNavigatorValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Navegador de Cultura';

    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['sentiment_score', 'culture_analysis', 'anomalies'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Sentiment score range
        if (isset($agentOutput['sentiment_score'])) {
            $minSent = $config['sentiment_min'] ?? 0.0;
            $maxSent = $config['sentiment_max'] ?? 1.0;
            $violation = $this->validateNumericRange('sentiment_score', $agentOutput['sentiment_score'], $minSent, $maxSent);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Culture analysis string validation
        if (isset($agentOutput['culture_analysis'])) {
            $violation = $this->validateStringLength('culture_analysis', $agentOutput['culture_analysis'], maxLength: 5000, minLength: 10);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 4. Anomalies - max constraint
        if (isset($agentOutput['anomalies'])) {
            $maxAnomalies = $config['max_anomalies_to_report'] ?? 5;
            $violation = $this->validateMaxCount('anomalies', $agentOutput['anomalies'], $maxAnomalies);
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
