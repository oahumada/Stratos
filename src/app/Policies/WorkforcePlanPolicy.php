<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkforcePlan;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkforcePlanPolicy
{
    use HandlesAuthorization;

    public function view(User $user, WorkforcePlan $plan): bool
    {
        return $user->organization_id === $plan->organization_id;
    }

    public function create(User $user): bool
    {
        // user must belong to an organization to create plans
        return !empty($user->organization_id);
    }

    public function update(User $user, WorkforcePlan $plan): bool
    {
        if ($user->organization_id !== $plan->organization_id) {
            return false;
        }

        // allow owner or sponsor to update while in editable states
        return $user->id === $plan->owner_user_id || $user->id === $plan->sponsor_user_id;
    }

    public function delete(User $user, WorkforcePlan $plan): bool
    {
        if ($user->organization_id !== $plan->organization_id) {
            return false;
        }

        // only owner can delete drafts
        return $plan->isDraft() && $user->id === $plan->owner_user_id;
    }

    public function approve(User $user, WorkforcePlan $plan): bool
    {
        if ($user->organization_id !== $plan->organization_id) {
            return false;
        }

        // prefer explicit approver if set, otherwise allow sponsor
        if (!empty($plan->approver_user_id)) {
            return $user->id === $plan->approver_user_id;
        }

        return $user->id === $plan->sponsor_user_id;
    }

    public function archive(User $user, WorkforcePlan $plan): bool
    {
        if ($user->organization_id !== $plan->organization_id) {
            return false;
        }

        // allow owner or sponsor to archive
        return $user->id === $plan->owner_user_id || $user->id === $plan->sponsor_user_id;
    }
}
