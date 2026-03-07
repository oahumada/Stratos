<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleRequirementsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $roleId;

    public int $organizationId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $roleId, int $organizationId)
    {
        $this->roleId = $roleId;
        $this->organizationId = $organizationId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
