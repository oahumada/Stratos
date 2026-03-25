<?php

namespace App\Listeners;

use App\Models\SecurityAccessLog;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the Logout event.
     */
    public function handle(Logout $event): void
    {
        /** @var \App\Models\User|null $user */
        $user = $event->user;

        if (! $user) {
            return;
        }

        SecurityAccessLog::record('logout', [
            'user_id' => $user->id,
            'organization_id' => $user->organization_id ?? null,
            'email' => $user->email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'role' => $user->role ?? null,
            'metadata' => [
                'guard' => $event->guard,
            ],
        ]);
    }
}
