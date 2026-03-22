<?php

namespace App\Policies;

use App\Models\IntelligenceMetricAggregate;
use App\Models\User;

class IntelligenceMetricAggregatePolicy
{
    /**
     * Determine whether the user can view any intelligence metric aggregates.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the intelligence metric aggregate.
     */
    public function view(User $user, IntelligenceMetricAggregate $aggregate): bool
    {
        return $user->organization_id === $aggregate->organization_id;
    }
}
