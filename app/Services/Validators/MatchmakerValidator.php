<?php

namespace App\Services\Validators;

use App\Data\VerificationViolation;

/**
 * MatchmakerValidator - Business rules for "Matchmaker de Resonancia"
 *
 * Validates:
 * - Required fields: matched_candidates, cultural_fit_scores
 * - Max candidates: 5
 * - Min cultural fit score: 0.6
 * - Synergy analysis: max 3000 chars
 */
class MatchmakerValidator
{
    use BaseBusinessValidator;

    protected string $agentId = 'Matchmaker de Resonancia';

    public function validate(array $agentOutput): array
    {
        $violations = [];
        $config = config("verification_rules.agents.{$this->agentId}", []);

        // 1. Required fields
        $requiredFields = $config['required_fields'] ?? ['matched_candidates', 'cultural_fit_scores'];
        foreach ($requiredFields as $field) {
            $violation = $this->validateRequired($field, $agentOutput[$field] ?? null);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 2. Matched candidates - max constraint
        if (isset($agentOutput['matched_candidates'])) {
            $maxCandidates = $config['max_candidates'] ?? 5;
            $violation = $this->validateMaxCount('matched_candidates', $agentOutput['matched_candidates'], $maxCandidates);
            if ($violation) {
                $violations[] = $violation;
            }
        }

        // 3. Cultural fit scores - validate array and min values
        if (isset($agentOutput['cultural_fit_scores'])) {
            $violation = $this->validateIsArray('cultural_fit_scores', $agentOutput['cultural_fit_scores']);
            if ($violation) {
                $violations[] = $violation;
            }

            if (is_array($agentOutput['cultural_fit_scores'])) {
                $minFitScore = $config['min_cultural_fit_score'] ?? 0.6;
                foreach ($agentOutput['cultural_fit_scores'] as $index => $score) {
                    if (is_numeric($score) && $score < $minFitScore) {
                        $violations[] = new VerificationViolation(
                            rule: 'score_below_minimum',
                            severity: 'warning',
                            message: "cultural_fit_scores[{$index}] = {$score} is below minimum {$minFitScore}",
                            field: "cultural_fit_scores[{$index}]",
                            received: (string) $score,
                            expected: ">= {$minFitScore}"
                        );
                    }
                }
            }
        }

        // 4. Synergy analysis string validation
        if (isset($agentOutput['synergy_analysis'])) {
            $violation = $this->validateStringLength('synergy_analysis', $agentOutput['synergy_analysis'], maxLength: 3000, minLength: 10);
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
