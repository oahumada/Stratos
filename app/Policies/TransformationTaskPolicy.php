<?php

namespace App\Policies;

use App\Models\TransformationTask;
use App\Models\User;

class TransformationTaskPolicy
{
    protected function sharesOrganization(User $user, TransformationTask $task): bool
    {
        $phase = $task->phase;
        $userOrg = $user->current_organization_id ?? $user->organization_id ?? null;

        return $phase !== null && $userOrg === $phase->organization_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.view');
    }

    public function view(User $user, TransformationTask $task): bool
    {
        return $this->sharesOrganization($user, $task) || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.create');
    }

    public function update(User $user, TransformationTask $task): bool
    {
        return ($this->sharesOrganization($user, $task) || $user->isAdmin())
            && ($user->id === $task->owner_id || $user->isAdmin() || $user->hasPermission('scenarios.edit'));
    }

    public function delete(User $user, TransformationTask $task): bool
    {
        return ($this->sharesOrganization($user, $task) || $user->isAdmin())
            && ($user->isAdmin() || $user->hasPermission('scenarios.delete'));
    }
}
