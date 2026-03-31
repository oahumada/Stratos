<?php

namespace App\Policies;

use App\Models\TransformationTask;
use App\Models\User;

class TransformationTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TransformationTask $task): bool
    {
        $phase = $task->phase;

        return $user->organization_id === $phase->organization_id || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->can('manage_scenarios');
    }

    public function update(User $user, TransformationTask $task): bool
    {
        $phase = $task->phase;

        return ($user->organization_id === $phase->organization_id || $user->is_admin)
            && ($user->id === $task->owner_id || $user->can('manage_scenarios'));
    }

    public function delete(User $user, TransformationTask $task): bool
    {
        $phase = $task->phase;

        return ($user->organization_id === $phase->organization_id || $user->is_admin)
            && $user->can('manage_scenarios');
    }
}
