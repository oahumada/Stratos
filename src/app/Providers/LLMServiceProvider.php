<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\LLMClient;

class LLMServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LLMClient::class, function ($app) {
            return new LLMClient();
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
