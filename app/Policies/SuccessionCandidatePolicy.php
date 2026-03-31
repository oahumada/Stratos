<?php

namespace App\Policies;

use App\Models\SuccessionCandidate;
use App\Models\User;

class SuccessionCandidatePolicy
{
    protected function sharesOrganization(User $user, SuccessionCandidate $candidate): bool
    {
        $userOrg = $user->current_organization_id ?? $user->organization_id ?? null;

        return $userOrg === $candidate->organization_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.view');
    }

    public function view(User $user, SuccessionCandidate $candidate): bool
    {
        return $this->sharesOrganization($user, $candidate) || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.create');
    }

    public function update(User $user, SuccessionCandidate $candidate): bool
    {
        return ($this->sharesOrganization($user, $candidate) || $user->isAdmin())
            && ($user->isAdmin() || $user->hasPermission('scenarios.edit'));
    }

    public function delete(User $user, SuccessionCandidate $candidate): bool
    {
        return ($this->sharesOrganization($user, $candidate) || $user->isAdmin())
            && ($user->isAdmin() || $user->hasPermission('scenarios.delete'));
    }
}
