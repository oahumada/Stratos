<?php

namespace App\Providers;

use App\Models\ChangeSet;
use App\Models\CompetencyVersion;
use App\Models\WorkforcePlan;
use App\Policies\ChangeSetPolicy;
use App\Policies\CompetencyVersionPolicy;
use App\Policies\WorkforcePlanPolicy;
use App\Models\ScenarioGeneration;
use App\Policies\ScenarioGenerationPolicy;
use App\Models\Scenario;
use App\Policies\ScenarioPolicy;
use App\Models\PromptInstruction;
use App\Policies\PromptInstructionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        WorkforcePlan::class => WorkforcePlanPolicy::class,
        CompetencyVersion::class => CompetencyVersionPolicy::class,
        ChangeSet::class => ChangeSetPolicy::class,
        ScenarioGeneration::class => ScenarioGenerationPolicy::class,
        PromptInstruction::class => PromptInstructionPolicy::class,
        Scenario::class => ScenarioPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
