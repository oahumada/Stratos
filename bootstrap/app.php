<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Jobs\ProcessVerificationPhaseTransition;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        // Excluir rutas de API del CSRF (Sanctum las maneja via bearer tokens)
        $middleware->validateCsrfTokens(except: [
            '/api/*',
        ]);

        // Agregar Sanctum middleware para SPA
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // Agregar Rate Limiting middleware para API
        $middleware->api(append: [
            \App\Http\Middleware\ApiRateLimiter::class,
        ]);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'module' => \App\Http\Middleware\CheckTenantModule::class,
            'normalize.csv' => \App\Http\Middleware\NormalizeCsvHeader::class,
            'mfa.required' => \App\Http\Middleware\EnsureMfaEnrolled::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Verification Phase Transition - runs hourly
        // Multi-tenant: dispatches job per organization
        $schedule->call(function (): void {
            // Get all active organizations
            $organizations = \App\Models\Organization::where('is_active', true)->pluck('id');

            foreach ($organizations as $orgId) {
                ProcessVerificationPhaseTransition::dispatch($orgId);
            }
        })
            ->hourly()
            ->name('verification:process-phase-transitions')
            ->withoutOverlapping();

        // Warm metrics cache twice daily (morning and afternoon)
        // to prevent cold starts during peak hours
        $schedule->command('metrics:warm-cache')
            ->twiceDaily(6, 14)
            ->name('metrics:warm-cache-scheduled')
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->command('lms:sync-progress')
            ->hourly()
            ->name('lms:sync-progress-hourly')
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->command('lms:monitor-sync --max-lag-minutes=90')
            ->everyFifteenMinutes()
            ->name('lms:monitor-sync-health')
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (): void {
        //
    })->create();
