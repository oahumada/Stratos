<?php

namespace App\Policies;

use App\Models\AdminOperationAudit;
use App\Models\User;

class AdminOperationAuditPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, $organization): bool
    {
        // Only admins of the organization can view operations
        return $user->people?->organization_id === $organization->id && $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdminOperationAudit $adminOperationAudit): bool
    {
        return $user->people?->organization_id === $adminOperationAudit->organization_id && $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admins can initiate operations
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdminOperationAudit $adminOperationAudit): bool
    {
        // Only org admins can preview/execute operations in their org
        return $user->people?->organization_id === $adminOperationAudit->organization_id && $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdminOperationAudit $adminOperationAudit): bool
    {
        // Only org admins can cancel operations
        return $user->people?->organization_id === $adminOperationAudit->organization_id && $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AdminOperationAudit $adminOperationAudit): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AdminOperationAudit $adminOperationAudit): bool
    {
        return false;
    }
}
