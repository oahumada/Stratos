<?php

namespace App\Policies;

use App\Models\TalentRiskIndicator;
use App\Models\User;

class TalentRiskIndicatorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TalentRiskIndicator $indicator): bool
    {
        return $user->organization_id === $indicator->organization_id || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->can('manage_scenarios');
    }

    public function update(User $user, TalentRiskIndicator $indicator): bool
    {
        return ($user->organization_id === $indicator->organization_id || $user->is_admin)
            && $user->can('manage_scenarios');
    }

    public function delete(User $user, TalentRiskIndicator $indicator): bool
    {
        return ($user->organization_id === $indicator->organization_id || $user->is_admin)
            && $user->can('manage_scenarios');
    }
}
