<?php

namespace App\Policies;

use App\Models\TalentRiskIndicator;
use App\Models\User;

class TalentRiskIndicatorPolicy
{
    protected function sharesOrganization(User $user, TalentRiskIndicator $indicator): bool
    {
        $userOrg = $user->current_organization_id ?? $user->organization_id ?? null;

        return $userOrg === $indicator->organization_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.view');
    }

    public function view(User $user, TalentRiskIndicator $indicator): bool
    {
        return $this->sharesOrganization($user, $indicator) || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.create');
    }

    public function update(User $user, TalentRiskIndicator $indicator): bool
    {
        return ($this->sharesOrganization($user, $indicator) || $user->isAdmin())
            && ($user->isAdmin() || $user->hasPermission('scenarios.edit'));
    }

    public function delete(User $user, TalentRiskIndicator $indicator): bool
    {
        return ($this->sharesOrganization($user, $indicator) || $user->isAdmin())
            && ($user->isAdmin() || $user->hasPermission('scenarios.delete'));
    }
}
