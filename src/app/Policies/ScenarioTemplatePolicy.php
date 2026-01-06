<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ScenarioTemplate;

class ScenarioTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user;
    }

    public function view(User $user, ScenarioTemplate $template): bool
    {
        return (bool) $user;
    }

    // Opcional: edición solo para admins internos
    public function update(User $user, ScenarioTemplate $template): bool
    {
        return $user->role === 'admin';
    }
}
