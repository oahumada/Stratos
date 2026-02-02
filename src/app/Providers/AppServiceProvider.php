<?php

namespace App\Providers;

use App\Services\EvolutionEngineService;
use Illuminate\Support\ServiceProvider;
use App\Services\EvolutionEngine;
use App\Services\ScenarioAnalysisService;
use App\Repository\ScenarioRepository;
use App\Repository\EvaluationRepository;
use App\Repository\CapabilityRepository;

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
