<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * VerificationViolationDetected - Event fired when verification detects violations
 *
 * Used to alert when specific issues are found in agent output
 */
class VerificationViolationDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $organizationId,
        public string $agentName,
        public array $violations,
        public float $confidenceScore,
        public string $severity = 'info',  // 'info', 'warning', 'critical'
    ) {}
}
