<?php

namespace App\Providers;

use App\Events\VerificationAlertThreshold;
use App\Events\VerificationPhaseTransitioned;
use App\Events\VerificationViolationDetected;
use App\Listeners\LogFailedLogin;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\SendAlertThresholdNotification;
use App\Listeners\SendPhaseTransitionNotification;
use App\Listeners\SendViolationDetectedNotification;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * EventServiceProvider - Register event listeners for the application
 *
 * Laravel 12 auto-discovery: Listeners in app/Listeners matching event names
 * are automatically discovered and registered. This provider supplements with
 * explicit manual mappings for verification events.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // ── Verification events ────────────────────────────────────────────
        VerificationPhaseTransitioned::class => [
            SendPhaseTransitionNotification::class,
        ],
        VerificationAlertThreshold::class => [
            SendAlertThresholdNotification::class,
        ],
        VerificationViolationDetected::class => [
            SendViolationDetectedNotification::class,
        ],

        // ── Security access audit events ───────────────────────────────────
        Login::class => [LogSuccessfulLogin::class],
        Logout::class => [LogSuccessfulLogout::class],
        Failed::class => [LogFailedLogin::class],
    ];
}
