<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create the application.
     */
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // For tests, avoid Symfony appending a charset to Content-Type for CSV responses
        try {
            \Symfony\Component\HttpFoundation\Response::setDefaultCharset('');
        } catch (\Throwable $e) {
            // ignore if not available
        }

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        // Ensure any authenticated guard is logged out between tests to avoid cross-test auth leakage
        try {
            if (app()->bound('auth')) {
                $guard = app('auth')->guard('sanctum');
                if ($guard && method_exists($guard, 'logout')) {
                    $guard->logout();
                }
            }

            // Also ensure the default Auth facade is logged out
            try {
                Auth::logout();
            } catch (\Throwable $e) {
                // ignore
            }
        } finally {
            parent::tearDown();
        }
    }

    /**
     * Helper to act as a user in Sanctum for tests, safely handling null to clear auth.
     */
    public function sanctumActingAs($user, array $abilities = ['*'], string $guard = 'sanctum')
    {
        if ($user === null) {
            // Attempt to fully clear authentication across common guards used in tests
            foreach (['sanctum', 'api', 'web'] as $g) {
                try {
                    $gi = app('auth')->guard($g);
                    if ($gi && method_exists($gi, 'logout')) {
                        $gi->logout();
                    }
                } catch (\Throwable $_) {
                    // ignore missing guards
                }
            }

            try {
                Auth::logout();
            } catch (\Throwable $_) {
            }

            try {
                app('auth')->userResolver(function () {
                    return null;
                });
                if (method_exists(app('auth'), 'setUser')) {
                    app('auth')->setUser(null);
                }
            } catch (\Throwable $_) {
            }

            // Also fully clear the session and CSRF token to avoid persisted auth state
            try {
                if (function_exists('session')) {
                    session()->flush();
                    session()->invalidate();
                    session()->regenerateToken();
                }
            } catch (\Throwable $_) {
                // ignore session store issues in testing environment
            }

            // Clear resolved Auth instances
            try {
                if (method_exists('\Illuminate\Support\Facades\Auth', 'clearResolvedInstances')) {
                    \Illuminate\Support\Facades\Auth::clearResolvedInstances();
                }
            } catch (\Throwable $_) {
            }

            // Also instruct Sanctum to clear any test acting state (Sanctum::actingAs(null) is supported)
            try {
                \Laravel\Sanctum\Sanctum::actingAs(null, [], $guard);
            } catch (\Throwable $_) {
                // ignore if Sanctum isn't available for some test environments
            }

            // Mark app that Sanctum test acting state was explicitly cleared
            try {
                app()->instance('sanctum_test_cleared', true);
            } catch (\Throwable $_) {
            }

            return null;
        }

        return \Laravel\Sanctum\Sanctum::actingAs($user, $abilities, $guard);
    }
}
