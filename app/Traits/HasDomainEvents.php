<?php

namespace App\Traits;

use App\Events\DomainEvent;

trait HasDomainEvents
{
    /**
     * The pending domain events.
     *
     * @var DomainEvent[]
     */
    protected array $pendingDomainEvents = [];

    /**
     * Record a new domain event.
     */
    public function recordDomainEvent(DomainEvent $event): void
    {
        $this->pendingDomainEvents[] = $event;
    }

    /**
     * Get and clear the pending domain events.
     *
     * @return DomainEvent[]
     */
    public function releaseDomainEvents(): array
    {
        $events = $this->pendingDomainEvents;

        $this->pendingDomainEvents = [];

        return $events;
    }

    /**
     * Dispatch all pending domain events.
     */
    public function dispatchDomainEvents(): void
    {
        foreach ($this->releaseDomainEvents() as $event) {
            event($event);
        }
    }
}
