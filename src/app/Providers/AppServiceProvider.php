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
        // Register policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ScenarioTemplate::class, \App\Policies\ScenarioTemplatePolicy::class);
        // \Illuminate\Support\Facades\Gate::policy(\App\Models\StrategicPlanningScenarios::class, \App\Policies\StrategicPlanningScenariosPolicy::class);
        // \Illuminate\Support\Facades\Gate::policy(\App\Models\StrategicPlanningScenarios::class, \App\Policies\WorkforceScenarioPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ScenarioComparison::class, \App\Policies\ScenarioComparisonPolicy::class);
    }
}
