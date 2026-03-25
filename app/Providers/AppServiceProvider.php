<?php

namespace App\Providers;

use App\Repository\CapabilityRepository;
use App\Repository\EvaluationRepository;
use App\Repository\ScenarioRepository;
use App\Services\EvolutionEngineService;
use App\Services\ScenarioAnalysisService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositorios
        $this->app->singleton(ScenarioRepository::class);
        $this->app->singleton(EvaluationRepository::class);
        $this->app->singleton(CapabilityRepository::class);

        // Servicios
        $this->app->singleton(EvolutionEngineService::class);
        $this->app->singleton(ScenarioAnalysisService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Strategy: Domain Event Store (Event Sourcing Lite)
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\DomainEvent::class,
            \App\Listeners\StoreDomainEvent::class
        );

        // Quality Hub Automatic Sentinel
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Log\Events\MessageLogged::class,
            \App\Listeners\LogQualitySentinel::class
        );

        // Register policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ScenarioTemplate::class, \App\Policies\ScenarioTemplatePolicy::class);
        // \Illuminate\Support\Facades\Gate::policy(\App\Models\StrategicPlanningScenarios::class, \App\Policies\StrategicPlanningScenariosPolicy::class);
        // \Illuminate\Support\Facades\Gate::policy(\App\Models\StrategicPlanningScenarios::class, \App\Policies\WorkforceScenarioPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ScenarioComparison::class, \App\Policies\ScenarioComparisonPolicy::class);

        // --- Stratos Security: Rate Limiting ---

        // 1. AI Generation (Heavy tokens)
        \Illuminate\Support\Facades\RateLimiter::for('ai_generation', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        // 2. AI Analysis & Curation (Medium tokens)
        \Illuminate\Support\Facades\RateLimiter::for('ai_analysis', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // 3. General Strategic Actions
        \Illuminate\Support\Facades\RateLimiter::for('strategic_api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
    }
}
