<?php

namespace App\Events;

class RoleRequirementsUpdated extends DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(int $roleId, int $organizationId, array $payload = [])
    {
        parent::__construct(
            (string) $roleId,
            \App\Models\Roles::class,
            $organizationId,
            $payload
        );
    }

    public function eventName(): string
    {
        return 'role.requirements_updated';
    }
}
