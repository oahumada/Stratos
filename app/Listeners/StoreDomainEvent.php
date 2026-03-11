<?php

namespace App\Listeners;

use App\Events\DomainEvent;
use App\Models\EventStore;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreDomainEvent implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(DomainEvent $event): void
    {
        EventStore::create([
            'id' => $event->eventId,
            'event_name' => $event->eventName(),
            'aggregate_type' => $event->aggregateType,
            'aggregate_id' => $event->aggregateId,
            'organization_id' => $event->organizationId,
            'actor_id' => $event->actorId,
            'payload' => $event->payload,
            'occurred_at' => $event->occurredAt,
        ]);
    }
}
