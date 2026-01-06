<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkforcePlanningScenario;

class WorkforcePlanningScenarioPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user;
    }

    public function view(User $user, WorkforcePlanningScenario $scenario): bool
    {
        return $user->organization_id === $scenario->organization_id;
    }

    public function create(User $user): bool
    {
        return (bool) $user->organization_id;
    }

    public function update(User $user, WorkforcePlanningScenario $scenario): bool
    {
        return $user->organization_id === $scenario->organization_id;
    }

    public function delete(User $user, WorkforcePlanningScenario $scenario): bool
    {
        return $user->organization_id === $scenario->organization_id;
    }
}
