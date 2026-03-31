<?php

namespace App\Policies;

use App\Models\TransformationPhase;
use App\Models\User;

class TransformationPhasePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TransformationPhase $phase): bool
    {
        return $user->organization_id === $phase->organization_id || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->can('manage_scenarios');
    }

    public function update(User $user, TransformationPhase $phase): bool
    {
        return ($user->organization_id === $phase->organization_id || $user->is_admin)
            && $user->can('manage_scenarios');
    }

    public function delete(User $user, TransformationPhase $phase): bool
    {
        return ($user->organization_id === $phase->organization_id || $user->is_admin)
            && $user->can('manage_scenarios');
    }
}
