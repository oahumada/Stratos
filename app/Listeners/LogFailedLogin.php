<?php

namespace App\Listeners;

use App\Models\SecurityAccessLog;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the Failed authentication event.
     */
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? null;

        SecurityAccessLog::record('login_failed', [
            'user_id' => $event->user?->id,
            'organization_id' => $event->user?->organization_id ?? null,
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'role' => $event->user?->role ?? null,
            'metadata' => [
                'guard' => $event->guard,
            ],
        ]);
    }
}
