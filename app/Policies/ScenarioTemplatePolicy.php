<?php

namespace App\Policies;

use App\Models\ScenarioTemplate;
use App\Models\User;

class ScenarioTemplatePolicy
{
    /**
     * View any templates (authenticated users only)
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user;
    }

    /**
     * View a single template (authenticated users only)
     */
    public function view(User $user, ScenarioTemplate $template): bool
    {
        return (bool) $user;
    }

    /**
     * Create templates (admin/manager only)
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager', 'strategist']);
    }

    /**
     * Update templates (admin/manager only)
     */
    public function update(User $user, ScenarioTemplate $template): bool
    {
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Delete templates (admin only)
     */
    public function delete(User $user, ScenarioTemplate $template): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Instantiate/clone templates (all authenticated users)
     */
    public function instantiate(User $user, ScenarioTemplate $template): bool
    {
        return (bool) $user;
    }
}
