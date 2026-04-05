<?php

namespace App\Providers;

use App\Repository\CapabilityRepository;
use App\Repository\EvaluationRepository;
use App\Repository\ScenarioRepository;
use App\Services\Cache\MetricsCacheService;
use App\Services\EvolutionEngineService;
use App\Services\Intelligence\ImpactEngineService;
use App\Services\ScenarioAnalysisService;
use App\Services\ScenarioPlanning\ExecutiveSummaryService;
use App\Services\ScenarioPlanning\ExportService;
use App\Services\ScenarioPlanning\ScenarioTemplateService;
use App\Services\ScenarioPlanning\WhatIfAnalysisService;
use App\Services\TalentRoiService;
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
        $this->app->singleton(TalentRoiService::class);
        $this->app->singleton(ImpactEngineService::class); // Phase 3: Per-request cache for metrics batching
        $this->app->singleton(MetricsCacheService::class); // Phase 4: Cross-request Redis cache
        $this->app->singleton(ScenarioTemplateService::class); // Phase 3: Template management service
        $this->app->singleton(WhatIfAnalysisService::class); // Phase 3.2: What-if analysis & sensitivity
        $this->app->singleton(ExecutiveSummaryService::class); // Phase 3.3: Executive summary generation
        $this->app->singleton(ExportService::class); // Phase 3.3: PDF/PPTX export service
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure route-model binding for `scenario` bypasses the organization global scope
        // so authorization policies can make explicit multi-tenant decisions (403 vs 404).
        \Illuminate\Support\Facades\Route::bind('scenario', function ($value) {
            \Log::debug('Route bind scenario invoked', [
                'auth_check' => auth()->check(),
                'auth_user_id' => auth()->id(),
                'value' => $value,
            ]);

            // If the request is unauthenticated, surface 401 to match test expectations
            if (! auth()->check()) {
                throw new \Symfony\Component\HttpKernel\Exception\HttpException(401, 'Unauthenticated');
            }

            return \App\Models\Scenario::withoutGlobalScope('organization')->findOrFail($value);
        });

        // ── Phase 4: Register model observers for cache invalidation ───────
        \App\Models\BusinessMetric::observe(\App\Observers\BusinessMetricObserver::class);
        \App\Models\FinancialIndicator::observe(\App\Observers\FinancialIndicatorObserver::class);

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

        // Governance: RAG ask — user must belong to an organization
        \Illuminate\Support\Facades\Gate::define('rag.ask', fn (\App\Models\User $user) => $user->organization_id !== null);

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
