<?php

namespace App\Policies;

use App\Models\ScenarioGeneration;
use App\Models\User;

class ScenarioGenerationPolicy
{
    public function view(User $user, ScenarioGeneration $gen): bool
    {
        return ($user->organization_id ?? null) === ($gen->organization_id ?? null);
    }

    public function accept(User $user, ScenarioGeneration $gen): bool
    {
        if (($user->organization_id ?? null) !== ($gen->organization_id ?? null)) {
            return false;
        }

        $role = $user->role ?? null;
        if (in_array($role, ['admin', 'approver', 'owner', 'superadmin'], true)) {
            return true;
        }

        if (property_exists($user, 'is_admin')) {
            if ((bool) $user->is_admin) return true;
        }

        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('approver') || $user->hasRole('admin')) return true;
        }

        // Allow the creator of the generation to accept (conservative)
        return ($gen->created_by ?? null) === ($user->id ?? null);
    }
}
