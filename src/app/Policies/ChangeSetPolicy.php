<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ChangeSet;

class ChangeSetPolicy
{
    public function view(User $user, ChangeSet $cs): bool
    {
        return ($user->organization_id ?? null) === ($cs->organization_id ?? null);
    }

    public function create(User $user): bool
    {
        return !empty($user->organization_id);
    }

    public function apply(User $user, ChangeSet $cs): bool
    {
        // Require same organization and basic approval right (is_admin) when available
        if (($user->organization_id ?? null) !== ($cs->organization_id ?? null)) {
            return false;
        }
        if (property_exists($user, 'is_admin')) {
            return (bool) $user->is_admin;
        }
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('approver') || $user->hasRole('admin');
        }
        // default conservative: only allow if user created the ChangeSet
        return ($cs->created_by ?? null) === ($user->id ?? null);
    }
}
