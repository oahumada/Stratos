<?php

namespace App\Events;

use App\Models\AdminOperationAudit;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminOperationRolledBack implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public AdminOperationAudit $audit,
    ) {}

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("admin-operations.org-{$this->audit->organization_id}"),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->audit->id,
            'organization_id' => $this->audit->organization_id,
            'operation_type' => $this->audit->operation_type,
            'status' => $this->audit->status,
            'error_message' => $this->audit->error_message,
            'rolled_back_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the name of the event to broadcast.
     */
    public function broadcastAs(): string
    {
        return 'operation.rolled_back';
    }
}
