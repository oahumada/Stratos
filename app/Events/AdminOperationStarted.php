<?php

namespace App\Events;

use App\Models\AdminOperationAudit;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminOperationStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public AdminOperationAudit $operation,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("admin-operations.org-{$this->operation->organization_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'operation.started';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->operation->id,
            'organization_id' => $this->operation->organization_id,
            'operation_type' => $this->operation->operation_type,
            'status' => $this->operation->status,
            'started_at' => $this->operation->started_at?->toIso8601String(),
        ];
    }
}
