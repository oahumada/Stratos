<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Jobs\ProcessVerificationPhaseTransition;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
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

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'module' => \App\Http\Middleware\CheckTenantModule::class,
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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
