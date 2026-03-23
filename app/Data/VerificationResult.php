<?php

namespace App\Data;

use Illuminate\Support\Collection;

/**
 * Value Object: Represents the result of verifying an agent's output.
 *
 * Contains:
 * - Overall verification score (0-1)
 * - List of violations/failures
 * - Detected hallucinations
 * - Detected contradictions
 * - Recommendation (accept/reject/review)
 */
class VerificationResult
{
    /** @var Collection<int, VerificationViolation> */
    public Collection $violations;

    /** @var Collection<int, string> */
    public Collection $hallucinations;

    /** @var Collection<int, string> */
    public Collection $contradictions;

    public function __construct(
        public float $score = 1.0,                                    // 0-1, default "passed"
        public string $recommendation = 'accept',                    // 'accept' | 'reject' | 'review'
        public ?string $reasoning = null,                            // Why this recommendation
        public int $totalChecks = 0,                                 // How many checks ran
        public int $passedChecks = 0,                                // How many passed
        ?array $violations = null,
        ?array $hallucinations = null,
        ?array $contradictions = null,
    ) {
        $this->violations = collect($violations ?? [])
            ->map(fn ($v) => $v instanceof VerificationViolation ? $v : VerificationViolation::fromArray($v))
            ->values();

        $this->hallucinations = collect($hallucinations ?? [])->values();
        $this->contradictions = collect($contradictions ?? [])->values();
    }

    /**
     * Add a violation to the results
     */
    public function addViolation(VerificationViolation $violation): self
    {
        $this->violations->push($violation);
        $this->recalculateScore();

        return $this;
    }

    /**
     * Add multiple violations at once
     */
    public function addViolations(array $violations): self
    {
        foreach ($violations as $violation) {
            $this->addViolation($violation instanceof VerificationViolation ? $violation : VerificationViolation::fromArray($violation));
        }

        return $this;
    }

    /**
     * Add a hallucination to results
     */
    public function addHallucination(string $hallucination): self
    {
        $this->hallucinations->push($hallucination);
        $this->recalculateScore();

        return $this;
    }

    /**
     * Add a detected contradiction
     */
    public function addContradiction(string $contradiction): self
    {
        $this->contradictions->push($contradiction);
        $this->recalculateScore();

        return $this;
    }

    /**
     * Check if verification passed (no violations, no hallucinations)
     */
    public function isPassed(): bool
    {
        return $this->violations->isEmpty()
            && $this->hallucinations->isEmpty()
            && $this->score >= 0.8;
    }

    /**
     * Get error count (violations + hallucinations + contradictions)
     */
    public function getErrorCount(): int
    {
        return $this->violations->count() + $this->hallucinations->count() + $this->contradictions->count();
    }

    /**
     * Recalculate score based on violations and issues
     */
    private function recalculateScore(): void
    {
        $errorCount = $this->getErrorCount();

        if ($errorCount === 0) {
            $this->score = 1.0;
            $this->recommendation = 'accept';
        } elseif ($errorCount === 1) {
            // 1 issue = minor problem
            $this->score = 0.75;
            $this->recommendation = 'review';
        } elseif ($errorCount <= 3) {
            // 2-3 issues = moderate
            $this->score = 0.5;
            $this->recommendation = 'review';
        } else {
            // 4+ issues = reject
            $this->score = 0.2;
            $this->recommendation = 'reject';
        }

        $this->passedChecks = max(0, $this->totalChecks - $this->violations->count());
    }

    /**
     * Convert to array for JSON serialization / API response
     */
    public function toArray(): array
    {
        return [
            'score' => round($this->score, 2),
            'recommendation' => $this->recommendation,
            'reasoning' => $this->reasoning,
            'summary' => [
                'total_checks' => $this->totalChecks,
                'passed_checks' => $this->passedChecks,
                'failed_checks' => $this->violations->count(),
                'hallucinations_detected' => $this->hallucinations->count(),
                'contradictions_detected' => $this->contradictions->count(),
            ],
            'violations' => $this->violations->map->toArray()->toArray(),
            'hallucinations' => $this->hallucinations->toArray(),
            'contradictions' => $this->contradictions->toArray(),
            'passed' => $this->isPassed(),
        ];
    }

    /**
     * Create from array (hydration)
     */
    public static function fromArray(array $data): self
    {
        $result = new self(
            score: $data['score'] ?? 1.0,
            recommendation: $data['recommendation'] ?? 'accept',
            reasoning: $data['reasoning'] ?? null,
            totalChecks: $data['total_checks'] ?? 0,
            passedChecks: $data['passed_checks'] ?? 0,
            violations: $data['violations'] ?? [],
            hallucinations: $data['hallucinations'] ?? [],
            contradictions: $data['contradictions'] ?? [],
        );

        return $result;
    }

    /**
     * Create a successful (no issues) result
     */
    public static function passed(?string $reasoning = null): self
    {
        return new self(
            score: 1.0,
            recommendation: 'accept',
            reasoning: $reasoning ?? 'All checks passed',
            totalChecks: 1,
            passedChecks: 1,
        );
    }

    /**
     * Create a failed result with custom message
     */
    public static function failed(string $reasoning, array $violations = []): self
    {
        $result = new self(
            score: 0.2,
            recommendation: 'reject',
            reasoning: $reasoning,
            totalChecks: 1,
            passedChecks: 0,
            violations: $violations,
        );

        return $result;
    }

    /**
     * Create a "needs review" result
     */
    public static function review(string $reasoning, array $issues = []): self
    {
        $result = new self(
            score: 0.5,
            recommendation: 'review',
            reasoning: $reasoning,
            totalChecks: 1,
            passedChecks: 0,
            violations: $issues,
        );

        return $result;
    }
}
