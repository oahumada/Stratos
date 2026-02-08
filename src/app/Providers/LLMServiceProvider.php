<?php

namespace App\Providers;

use App\Services\LLMClient;
use Illuminate\Support\ServiceProvider;

class LLMServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LLMClient::class, function ($app) {
            return new LLMClient;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // no-op
    }
}
