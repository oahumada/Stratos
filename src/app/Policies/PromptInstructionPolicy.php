<?php

namespace App\Policies;

use App\Models\PromptInstruction;
use App\Models\User;

class PromptInstructionPolicy
{
    /**
     * Any authenticated user may view instructions (read-only).
     */
    public function viewAny(User $user): bool
    {
        return (bool) $user;
    }

    public function view(User $user, PromptInstruction $pi): bool
    {
        return (bool) $user;
    }

    /**
     * Create new editable instruction: restrict to operators/admins.
     */
    public function create(User $user): bool
    {
        $role = $user->role ?? null;
        if (in_array($role, ['admin', 'operator', 'approver', 'superadmin'], true)) {
            return true;
        }
        if (property_exists($user, 'is_admin') && (bool) $user->is_admin) return true;
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin') || $user->hasRole('operator') || $user->hasRole('approver')) return true;
        }
        return false;
    }

    /**
     * Update existing instruction: similar restriction.
     */
    public function update(User $user, PromptInstruction $pi): bool
    {
        return $this->create($user);
    }

    /**
     * Restore default (creating an editable copy) requires create permission.
     */
    public function restore(User $user): bool
    {
        return $this->create($user);
    }
}
