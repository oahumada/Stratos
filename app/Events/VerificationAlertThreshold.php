<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * VerificationAlertThreshold - Event fired when metrics exceed alert thresholds
 *
 * Used in hybrid and monitoring modes to alert when system health degrades
 */
class VerificationAlertThreshold
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $organizationId,
        public string $metricName,  // 'error_rate', 'confidence_score', etc.
        public float $currentValue,
        public float $threshold,
        public string $severity = 'warning',  // 'info', 'warning', 'critical'
        public array $context = [],
    ) {}
}
