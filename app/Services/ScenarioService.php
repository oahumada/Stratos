<?php

namespace App\Services;

use App\Models\Scenario;
use App\Repository\ScenarioRepository;

class ScenarioService
{
    public function __construct() {}

    public function runFullAnalysis(int $scenarioId): array
    {
        $scenario = Scenario::find($scenarioId);
        if (! $scenario) {
            throw new \Exception('Scenario not found');
        }

        // Minimal analysis placeholder
        return [
            'scenario_id' => $scenarioId,
            'summary' => [
                'total_headcount' => rand(100, 500),
                'skill_gap_index' => rand(10, 90),
            ],
            'status' => 'completed',
        ];
    }

    public function transitionDecisionStatus(Scenario $scenario, string $toStatus, $user, $notes = null): Scenario
    {
        $scenario->decision_status = $toStatus;
        $scenario->save();

        return $scenario;
    }

    public function startExecution(Scenario $scenario, $user): Scenario
    {
        $scenario->execution_status = 'in_progress';
        $scenario->save();

        return $scenario;
    }

    public function pauseExecution(Scenario $scenario, $user, $notes = null): Scenario
    {
        $scenario->execution_status = 'paused';
        $scenario->save();

        return $scenario;
    }

    public function completeExecution(Scenario $scenario, $user): Scenario
    {
        $scenario->execution_status = 'completed';
        $scenario->save();

        return $scenario;
    }

    public function createNewVersion(Scenario $scenario, $name, $description, $user, $notes = null, $copySkills = true, $copyStrategies = true)
    {
        $new = $scenario->replicate();
        $new->name = $name ?? ($scenario->name.' (copy)');
        $new->description = $description ?? $scenario->description;
        $new->is_current_version = false;
        $new->version_group_id = $scenario->version_group_id ?? (string) \Str::uuid();
        $new->version_number = ($scenario->version_number ?? 1) + 1;
        $new->created_by = $user->id ?? null;
        $new->save();

        return $new;
    }

    public function syncParentMandatorySkills(Scenario $scenario): int
    {
        // Placeholder - no-op
        return 0;
    }

    public function consolidateParent(Scenario $scenario): array
    {
        // Simple rollup placeholder
        return [
            'scenario_id' => $scenario->id,
            'total_children' => $scenario->children()->count() ?? 0,
        ];
    }

    /**
     * Calculate scenario gaps and return a structured summary used by tests.
     */
    public function calculateScenarioGaps(Scenario $scenario): array
    {
        $demands = $scenario->skillDemands()->with('skill')->get();

        $gaps = [];
        $totalSkills = $demands->count();
        $critical = 0;
        $coverageSum = 0.0;

        foreach ($demands as $d) {
            $current = $d->current_headcount ?? 0;
            $required = $d->required_headcount ?? 0;
            $gap = max(0, $required - $current);
            $coverage = $required > 0 ? round(($current / $required) * 100, 1) : 0.0;
            $coverageSum += $coverage;
            if ($d->priority === 'critical') {
                $critical++;
            }

            $gaps[] = [
                'skill_id' => $d->skill_id,
                'skill_name' => $d->skill?->name ?? null,
                'priority' => $d->priority,
                'gap_headcount' => $gap,
                'coverage_pct' => $coverage,
            ];
        }

        $avgCoverage = $totalSkills > 0 ? round($coverageSum / $totalSkills, 1) : 0.0;

        return [
            'scenario_id' => $scenario->id,
            'generated_at' => now()->toDateTimeString(),
            'summary' => [
                'total_skills' => $totalSkills,
                'critical_skills' => $critical,
                'risk_score' => 0, // placeholder
                'avg_coverage_pct' => $avgCoverage,
            ],
            'gaps' => $gaps,
        ];
    }

    /**
     * Get the hierarchical capability tree for a scenario.
     */
    public function getCapabilityTree(int $scenarioId): array
    {
        $scenarioAnalytics = app(\App\Services\ScenarioAnalyticsService::class);

        $scenario = Scenario::with([
            'capabilities' => function ($q) use ($scenarioId) {
                $q->with([
                    'competencies' => function ($qc) use ($scenarioId) {
                        $qc->wherePivot('scenario_id', $scenarioId)
                            ->with('skills');
                    },
                ]);
            },
        ])->findOrFail($scenarioId);

        return $scenario->capabilities->map(function ($capability) use ($scenarioId, $scenarioAnalytics) {
            return [
                'id' => $capability->id,
                'type' => $capability->type ?? null,
                'category' => $capability->category ?? null,
                'name' => $capability->name,
                'description' => $capability->description ?? null,
                'importance' => $capability->importance ?? null,
                'position_x' => $capability->position_x ?? null,
                'position_y' => $capability->position_y ?? null,
                'strategic_role' => $capability->pivot?->strategic_role ?? null,
                'strategic_weight' => $capability->pivot?->strategic_weight ?? $capability->pivot?->weight ?? null,
                'priority' => $capability->pivot?->priority ?? null,
                'rationale' => $capability->pivot?->rationale ?? null,
                'required_level' => $capability->pivot?->required_level ?? null,
                'is_critical' => $capability->pivot?->is_critical ?? null,
                'is_incubating' => $capability->isIncubating(),
                'readiness' => round($scenarioAnalytics->calculateCapabilityReadiness($scenarioId, $capability->id) * 100, 1),
                'competencies' => $capability->competencies->map(function ($comp) use ($scenarioId, $scenarioAnalytics) {
                    return [
                        'id' => $comp->id,
                        'name' => $comp->name,
                        'description' => $comp->description ?? null,
                        'readiness' => round($scenarioAnalytics->calculateCompetencyReadiness($scenarioId, $comp->id) * 100, 1),
                        'pivot' => [
                            'strategic_weight' => $comp->pivot?->strategic_weight ?? $comp->pivot?->weight ?? null,
                            'priority' => $comp->pivot?->priority ?? null,
                            'required_level' => $comp->pivot?->required_level ?? null,
                            'is_critical' => $comp->pivot?->is_critical ?? null,
                            'rationale' => $comp->pivot?->rationale ?? null,
                        ],
                        'skills' => $comp->skills->map(function ($skill) use ($scenarioId, $scenarioAnalytics) {
                            return [
                                'id' => $skill->id,
                                'name' => $skill->name,
                                'weight' => $skill->pivot->weight ?? null,
                                'is_incubating' => ! is_null($skill->discovered_in_scenario_id),
                                'readiness' => round($scenarioAnalytics->calculateSkillReadiness($scenarioId, $skill->id) * 100, 1),
                            ];
                        }),
                    ];
                }),
            ];
        })->toArray();
    }
}
