<?php

namespace App\Policies;

use App\Models\TransformationPhase;
use App\Models\User;

class TransformationPhasePolicy
{
    protected function sharesOrganization(User $user, TransformationPhase $phase): bool
    {
        $userOrg = $user->current_organization_id ?? $user->organization_id ?? null;

        return $userOrg === $phase->organization_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.view');
    }

    public function view(User $user, TransformationPhase $phase): bool
    {
        return $this->sharesOrganization($user, $phase) || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.create');
    }

    public function update(User $user, TransformationPhase $phase): bool
    {
        return ($this->sharesOrganization($user, $phase) || $user->isAdmin())
            && ($user->isAdmin() || $user->hasPermission('scenarios.edit'));
    }

    public function delete(User $user, TransformationPhase $phase): bool
    {
        return ($this->sharesOrganization($user, $phase) || $user->isAdmin())
            && ($user->isAdmin() || $user->hasPermission('scenarios.delete'));
    }
}
