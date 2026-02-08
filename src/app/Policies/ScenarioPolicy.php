<?php

namespace App\Policies;

use App\Models\Scenario;
use App\Models\User;

class ScenarioPolicy
{
    public function view(User $user, Scenario $scenario): bool
    {
        return ($user->organization_id ?? null) === ($scenario->organization_id ?? null);
    }

    public function viewAcceptedPrompt(User $user, Scenario $scenario): bool
    {
        if (($user->organization_id ?? null) !== ($scenario->organization_id ?? null)) {
            return false;
        }

        $role = $user->role ?? null;
        if (in_array($role, ['admin', 'approver', 'owner', 'superadmin'], true)) {
            return true;
        }

        if (property_exists($user, 'is_admin') && (bool) $user->is_admin) {
            return true;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('approver') || $user->hasRole('admin'))) {
            return true;
        }

        // Allow owner/creator of scenario to view
        return ($scenario->created_by ?? null) === ($user->id ?? null);
    }
}
