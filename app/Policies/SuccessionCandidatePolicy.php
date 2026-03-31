<?php

namespace App\Policies;

use App\Models\SuccessionCandidate;
use App\Models\User;

class SuccessionCandidatePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view
    }

    public function view(User $user, SuccessionCandidate $candidate): bool
    {
        return $user->organization_id === $candidate->organization_id || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->can('manage_scenarios');
    }

    public function update(User $user, SuccessionCandidate $candidate): bool
    {
        return ($user->organization_id === $candidate->organization_id || $user->is_admin)
            && $user->can('manage_scenarios');
    }

    public function delete(User $user, SuccessionCandidate $candidate): bool
    {
        return ($user->organization_id === $candidate->organization_id || $user->is_admin)
            && $user->can('manage_scenarios');
    }
}
