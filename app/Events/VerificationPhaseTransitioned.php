<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * VerificationPhaseTransitioned - Event fired when verification phase changes
 *
 * This event is dispatched after a successful phase transition and triggers
 * all registered notification channels (Slack, Email, Database logging, etc.)
 */
class VerificationPhaseTransitioned
{
    use Dispatchable, InteractsWithBroadcasting, SerializesModels;

    public function __construct(
        public int $organizationId,
        public string $fromPhase,
        public string $toPhase,
        public string $reason,
        public array $metrics = [],
        public \DateTime $transitionedAt = new \DateTime,
    ) {}
}
