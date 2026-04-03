<?php

namespace App\Models\Concerns;

use App\Models\User;

trait HasScenarioBusinessRules
{
    public function getSynthetizationIndexAttribute(): int
    {
        if ($this->talentBlueprints->isEmpty()) {
            return 0;
        }

        return (int) round(
            $this->talentBlueprints->avg('synthetic_percentage')
        );
    }

    public function canBeEdited(?User $user = null): bool
    {
        if (in_array($this->decision_status, ['pending_approval', 'approved'], true)) {
            if ($user) {
                return ($this->created_by === $user->id) || $user->hasRole('admin');
            }

            return false;
        }

        return true;
    }
}
