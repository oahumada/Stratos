<?php

namespace App\Policies;

use App\Models\Scenario;
use App\Models\User;

class ScenarioPolicy
{
    public function view(User $user, Scenario $scenario): bool
    {
        $userOrg = $user->current_organization_id ?? $user->organization_id ?? null;
        return $userOrg === ($scenario->organization_id ?? null);
    }

    public function viewAcceptedPrompt(User $user, Scenario $scenario): bool
    {
        $userOrg = $user->current_organization_id ?? $user->organization_id ?? null;
        if ($userOrg !== ($scenario->organization_id ?? null)) {
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

    public function activate(User $user, Scenario $scenario): bool
    {
        $userOrg = $user->current_organization_id ?? $user->organization_id ?? null;
        if ($userOrg !== ($scenario->organization_id ?? null)) {
            return false;
        }

        // Owners and admins can activate
        if (($scenario->created_by ?? null) === ($user->id ?? null)) {
            return true;
        }

        $role = $user->role ?? null;
        if (in_array($role, ['admin', 'owner', 'superadmin'], true)) {
            return true;
        }

        if (property_exists($user, 'is_admin') && (bool) $user->is_admin) {
            return true;
        }

        if (method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('owner'))) {
            return true;
        }

        return false;
    }
}
