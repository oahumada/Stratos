<?php

namespace App\Policies;

use App\Models\LLMEvaluation;
use App\Models\User;

class LLMEvaluationPolicy
{
    /**
     * Determine whether the user can view any evaluations.
     */
    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can view the evaluation.
     */
    public function view(User $user, LLMEvaluation $evaluation): bool
    {
        // Multi-tenant: Users can only view evaluations from their organization
        return $user->organization_id === $evaluation->organization_id;
    }

    /**
     * Determine whether the user can create evaluations.
     */
    public function create(User $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can update the evaluation.
     */
    public function update(User $user, LLMEvaluation $evaluation): bool
    {
        // Only the creator can update
        return $user->id === $evaluation->created_by &&
               $user->organization_id === $evaluation->organization_id;
    }

    /**
     * Determine whether the user can delete the evaluation.
     */
    public function delete(User $user, LLMEvaluation $evaluation): bool
    {
        // Only the creator or admin can delete
        return $user->organization_id === $evaluation->organization_id;
    }
}
