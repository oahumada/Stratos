<?php

namespace App\Policies;

use App\Models\TalentPass;
use App\Models\User;

class TalentPassPolicy
{
    /**
     * View the talent pass
     */
    public function view(User $user, TalentPass $talentPass): bool
    {
        return $user->organization_id === $talentPass->organization_id;
    }

    /**
     * Update the talent pass
     */
    public function update(User $user, TalentPass $talentPass): bool
    {
        return $user->organization_id === $talentPass->organization_id &&
               ($user->people_id === $talentPass->people_id || $user->hasPermissionTo('talent.edit'));
    }

    /**
     * Delete the talent pass
     */
    public function delete(User $user, TalentPass $talentPass): bool
    {
        return $user->organization_id === $talentPass->organization_id &&
               ($user->people_id === $talentPass->people_id || $user->hasPermissionTo('talent.delete'));
    }

    /**
     * Publish the talent pass
     */
    public function publish(User $user, TalentPass $talentPass): bool
    {
        return $user->organization_id === $talentPass->organization_id &&
               ($user->people_id === $talentPass->people_id || $user->hasPermissionTo('talent.manage'));
    }

    /**
     * Archive the talent pass
     */
    public function archive(User $user, TalentPass $talentPass): bool
    {
        return $user->organization_id === $talentPass->organization_id &&
               ($user->people_id === $talentPass->people_id || $user->hasPermissionTo('talent.manage'));
    }

    /**
     * Clone the talent pass
     */
    public function clone(User $user, TalentPass $talentPass): bool
    {
        return $user->organization_id === $talentPass->organization_id;
    }

    /**
     * Export the talent pass
     */
    public function export(User $user, TalentPass $talentPass): bool
    {
        return $user->organization_id === $talentPass->organization_id &&
               ($user->people_id === $talentPass->people_id || $user->hasPermissionTo('talent.export'));
    }

    /**
     * Share the talent pass
     */
    public function share(User $user, TalentPass $talentPass): bool
    {
        return $user->organization_id === $talentPass->organization_id &&
               ($user->people_id === $talentPass->people_id || $user->hasPermissionTo('talent.share'));
    }
}
