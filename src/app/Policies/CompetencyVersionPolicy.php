<?php

namespace App\Policies;

use App\Models\CompetencyVersion;
use App\Models\User;

class CompetencyVersionPolicy
{
    public function view(User $user, CompetencyVersion $cv): bool
    {
        return $user->organization_id === $cv->organization_id;
    }

    public function create(User $user): bool
    {
        return ! is_null($user->organization_id);
    }

    public function delete(User $user, CompetencyVersion $cv): bool
    {
        return $user->organization_id === $cv->organization_id;
    }
}
