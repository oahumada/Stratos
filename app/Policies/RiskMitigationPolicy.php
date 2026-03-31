<?php

namespace App\Policies;

use App\Models\RiskMitigation;
use App\Models\User;

class RiskMitigationPolicy
{
    protected function sharesOrganization(User $user, RiskMitigation $mitigation): bool
    {
        $indicator = $mitigation->riskIndicator;
        $userOrg = $user->current_organization_id ?? $user->organization_id ?? null;

        return $indicator !== null && $userOrg === $indicator->organization_id;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.view');
    }

    public function view(User $user, RiskMitigation $mitigation): bool
    {
        return $this->sharesOrganization($user, $mitigation) || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('scenarios.create');
    }

    public function update(User $user, RiskMitigation $mitigation): bool
    {
        return ($this->sharesOrganization($user, $mitigation) || $user->isAdmin())
            && ($user->id === $mitigation->assigned_to || $user->isAdmin() || $user->hasPermission('scenarios.edit'));
    }

    public function delete(User $user, RiskMitigation $mitigation): bool
    {
        return ($this->sharesOrganization($user, $mitigation) || $user->isAdmin())
            && ($user->isAdmin() || $user->hasPermission('scenarios.delete'));
    }
}
