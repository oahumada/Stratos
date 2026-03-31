<?php

namespace App\Providers;

use App\Models\ChangeSet;
use App\Models\CompetencyVersion;
use App\Models\IntelligenceMetricAggregate;
use App\Models\LLMEvaluation;
use App\Models\PromptInstruction;
use App\Models\RiskMitigation;
use App\Models\Scenario;
use App\Models\ScenarioGeneration;
use App\Models\SuccessionCandidate;
use App\Models\TalentPass;
use App\Models\TalentRiskIndicator;
use App\Models\TransformationPhase;
use App\Models\TransformationTask;
use App\Models\User;
use App\Models\WorkforcePlan;
use App\Policies\ChangeSetPolicy;
use App\Policies\CompetencyVersionPolicy;
use App\Policies\IntelligenceMetricAggregatePolicy;
use App\Policies\LLMEvaluationPolicy;
use App\Policies\PromptInstructionPolicy;
use App\Policies\RiskMitigationPolicy;
use App\Policies\ScenarioGenerationPolicy;
use App\Policies\ScenarioPolicy;
use App\Policies\SuccessionCandidatePolicy;
use App\Policies\TalentPassPolicy;
use App\Policies\TalentRiskIndicatorPolicy;
use App\Policies\TransformationPhasePolicy;
use App\Policies\TransformationTaskPolicy;
use App\Policies\WorkforcePlanPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        WorkforcePlan::class => WorkforcePlanPolicy::class,
        CompetencyVersion::class => CompetencyVersionPolicy::class,
        ChangeSet::class => ChangeSetPolicy::class,
        ScenarioGeneration::class => ScenarioGenerationPolicy::class,
        PromptInstruction::class => PromptInstructionPolicy::class,
        Scenario::class => ScenarioPolicy::class,
        LLMEvaluation::class => LLMEvaluationPolicy::class,
        IntelligenceMetricAggregate::class => IntelligenceMetricAggregatePolicy::class,
        TalentPass::class => TalentPassPolicy::class,
        // Scenario Planning Phase 2 policies
        SuccessionCandidate::class => SuccessionCandidatePolicy::class,
        TalentRiskIndicator::class => TalentRiskIndicatorPolicy::class,
        RiskMitigation::class => RiskMitigationPolicy::class,
        TransformationPhase::class => TransformationPhasePolicy::class,
        TransformationTask::class => TransformationTaskPolicy::class,
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
                } catch (\Throwable $e) {
                }
            }

            // Fallback: admin role o is_admin
            if (method_exists($user, 'hasRole')) {
                try {
                    if ($user->hasRole('admin')) {
                        return true;
                    }
                } catch (\Throwable $e) {
                }
            }
            if (! empty($user->is_admin)) {
                return true;
            }

            return false;
        });
    }
}
