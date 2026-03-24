<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationMetricsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $organizationId,
        public array $metrics,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("verification-metrics.org-{$this->organizationId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'metrics.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'metrics' => $this->metrics,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
