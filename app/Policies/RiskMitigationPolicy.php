<?php

namespace App\Policies;

use App\Models\RiskMitigation;
use App\Models\User;

class RiskMitigationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RiskMitigation $mitigation): bool
    {
        $indicator = $mitigation->riskIndicator;

        return $user->organization_id === $indicator->organization_id || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->can('manage_scenarios');
    }

    public function update(User $user, RiskMitigation $mitigation): bool
    {
        $indicator = $mitigation->riskIndicator;

        return ($user->organization_id === $indicator->organization_id || $user->is_admin)
            && ($user->id === $mitigation->assigned_to || $user->can('manage_scenarios'));
    }

    public function delete(User $user, RiskMitigation $mitigation): bool
    {
        $indicator = $mitigation->riskIndicator;

        return ($user->organization_id === $indicator->organization_id || $user->is_admin)
            && $user->can('manage_scenarios');
    }
}
