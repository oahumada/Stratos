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
            // Si el usuario tiene atributo is_admin (DB o dinámico) y es true, permitir
            if (! empty($user->is_admin)) {
                return true;
            }

            // Si el modelo tiene un método hasRole, usarlo para comprobar rol 'admin'
            if (method_exists($user, 'hasRole')) {
                try {
                    return $user->hasRole('admin');
                } catch (\Throwable $e) {
                    return false;
                }
            }

            return false;
        });
    }
}
