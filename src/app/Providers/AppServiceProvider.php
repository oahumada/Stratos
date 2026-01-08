<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ScenarioTemplate::class, \App\Policies\ScenarioTemplatePolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\WorkforcePlanningScenario::class, \App\Policies\WorkforcePlanningScenarioPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\WorkforcePlanningScenario::class, \App\Policies\WorkforceScenarioPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\ScenarioComparison::class, \App\Policies\ScenarioComparisonPolicy::class);
    }
}
