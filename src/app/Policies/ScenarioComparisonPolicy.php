<?php

namespace App\Policies;

use App\Models\ScenarioComparison;
use App\Models\User;

class ScenarioComparisonPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user;
    }

    public function view(User $user, ScenarioComparison $comparison): bool
    {
        return $user->organization_id === $comparison->organization_id;
    }

    public function create(User $user): bool
    {
        return (bool) $user->organization_id;
    }
}
