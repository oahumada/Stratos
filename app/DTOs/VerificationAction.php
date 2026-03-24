<?php

namespace App\DTOs;

/**
 * VerificationAction - Decision to take based on verification result
 *
 * Determines the next action in AiOrchestratorService based on:
 * - Verification result (violations, recommendation)
 * - Current rollout phase (silent|flagging|reject|tuning)
 */
class VerificationAction
{
    public function __construct(
        public string $type, // accept|flag_review|reject
        public array $responseMetadata = [],
        public ?string $errorMessage = null,
        public bool $shouldRetry = false,
        public string $phase = 'silent',
    ) {}

    /**
     * Convert to array for response
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'phase' => $this->phase,
            'should_retry' => $this->shouldRetry,
            'response_metadata' => $this->responseMetadata,
            'error_message' => $this->errorMessage,
        ];
    }

    /**
     * Check if action should accept output
     */
    public function shouldAccept(): bool
    {
        return $this->type === 'accept';
    }

    /**
     * Check if action should flag for review
     */
    public function shouldFlag(): bool
    {
        return $this->type === 'flag_review';
    }

    /**
     * Check if action should reject output
     */
    public function shouldReject(): bool
    {
        return $this->type === 'reject';
    }

    /**
     * Check if action should retry with refined prompt
     */
    public function canRetry(): bool
    {
        return $this->shouldRetry && $this->phase === 'tuning';
    }
}
