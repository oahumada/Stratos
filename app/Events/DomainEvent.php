<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

abstract class DomainEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $eventId;

    public string $occurredAt;

    public string $aggregateId;

    public string $aggregateType;

    public int $organizationId;

    public ?int $actorId;

    public array $payload;

    /**
     * Create a new event instance.
     */
    public function __construct(string $aggregateId, string $aggregateType, int $organizationId, array $payload = [], ?int $actorId = null)
    {
        $this->eventId = (string) Str::uuid();
        $this->occurredAt = now()->toIso8601String();
        $this->aggregateId = $aggregateId;
        $this->aggregateType = $aggregateType;
        $this->organizationId = $organizationId;
        $this->payload = $payload;
        $this->actorId = $actorId ?: auth()->id();
    }

    /**
     * Get the descriptive name of the event.
     */
    abstract public function eventName(): string;
}
