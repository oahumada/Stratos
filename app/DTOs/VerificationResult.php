<?php

namespace App\DTOs;

use App\Models\VerificationViolation;

/**
 * VerificationResult - Output of TalentVerificationService verification
 *
 * Encapsulates the complete result of verifying agent output against business rules
 */
class VerificationResult
{
    public function __construct(
        public bool $valid,
        public float $confidenceScore,
        public string $recommendation, // accept|review|reject
        public array $violations, // VerificationViolation[]
        public string $agentName,
        public string $phase,
        public array $metadata = [],
    ) {}

    /**
     * Get message from violation - handles both objects and arrays
     */
    private function getViolationMessage($violation): ?string
    {
        if (is_object($violation)) {
            return $violation->message ?? null;
        }

        return $violation['message'] ?? null;
    }

    /**
     * Convert to array for response/logging
     */
    public function toArray(): array
    {
        return [
            'valid' => $this->valid,
            'confidence_score' => $this->confidenceScore,
            'recommendation' => $this->recommendation,
            'violations_count' => count($this->violations),
            'violations' => array_map(fn ($v) => [
                'rule' => $v->rule ?? $v['rule'] ?? 'unknown',
                'field' => $v->field ?? $v['field'] ?? 'unknown',
                'message' => $this->getViolationMessage($v) ?? 'Verification failed',
                'received' => $v->received ?? $v['received'] ?? null,
            ], $this->violations),
            'agent_name' => $this->agentName,
            'phase' => $this->phase,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Human-readable error message
     */
    public function getHumanReadableErrors(): string
    {
        if (empty($this->violations)) {
            return 'Agent output failed verification';
        }

        $messages = array_map(fn ($v) => $this->getViolationMessage($v) ?? $v['rule'] ?? 'Unknown error', $this->violations);

        return implode('; ', array_slice($messages, 0, 3)).
            (count($messages) > 3 ? ' (+ '.(count($messages) - 3).' more)' : '');
    }

    /**
     * Determine if should be flagged for review
     */
    public function shouldFlag(): bool
    {
        return $this->recommendation === 'review' || $this->recommendation === 'reject';
    }
}
