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
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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

        // Gate para controlar ejecución del sync Neo4j
        Gate::define('sync-neo4j', function (User $user) {
            // Si usa Spatie Permission, preferir permiso explícito
            if (method_exists($user, 'can')) {
                try {
                    if ($user->can('sync_neo4j')) {
                        return true;
                    }
                } catch (\Throwable $e) {}
            }

            // Fallback: admin role o is_admin
            if (method_exists($user, 'hasRole')) {
                try {
                    if ($user->hasRole('admin')) {
                        return true;
                    }
                } catch (\Throwable $e) {}
            }
            if (!empty($user->is_admin)) {
                return true;
            }
            return false;
        });
    }
}
