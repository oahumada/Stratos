<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerificationComplianceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $organizationId,
        public array $complianceData,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("verification-compliance.org-{$this->organizationId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'compliance.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'compliance' => $this->complianceData,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
