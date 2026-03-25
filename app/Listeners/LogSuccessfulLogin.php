<?php

namespace App\Listeners;

use App\Models\SecurityAccessLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the Login event.
     */
    public function handle(Login $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;

        SecurityAccessLog::record('login', [
            'user_id' => $user->id,
            'organization_id' => $user->organization_id ?? null,
            'email' => $user->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'role' => $user->role ?? null,
            'mfa_used' => $user->hasEnabledTwoFactorAuthentication(),
            'metadata' => [
                'guard' => $event->guard,
                'remember' => $event->remember,
            ],
        ]);
    }
}
