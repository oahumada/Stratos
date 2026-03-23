<?php

namespace App\Data;

/**
 * Value Object: Represents a single verification violation/error.
 */
class VerificationViolation
{
    public function __construct(
        public string $rule,          // Name of the failed rule (e.g., 'max_candidates')
        public string $severity,      // 'error' | 'warning'
        public string $message,       // Human-readable error message
        public ?string $field = null, // Optional: which field failed (e.g., 'candidates[0].score')
        public mixed $received = null, // What was received
        public mixed $expected = null  // What was expected
    ) {}

    /**
     * Convert to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'rule' => $this->rule,
            'severity' => $this->severity,
            'message' => $this->message,
            'field' => $this->field,
            'received' => $this->received,
            'expected' => $this->expected,
        ];
    }

    /**
     * Create from array (hydration)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            rule: $data['rule'] ?? 'unknown',
            severity: $data['severity'] ?? 'error',
            message: $data['message'] ?? 'Verification failed',
            field: $data['field'] ?? null,
            received: $data['received'] ?? null,
            expected: $data['expected'] ?? null,
        );
    }
}
