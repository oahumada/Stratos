<?php

namespace App\Services;

use App\Models\StrategicPlanningScenarios;
use App\Repository\ScenarioRepository;
use Illuminate\Support\Facades\Log;

class ScenarioService
{
    public function __construct(private ScenarioRepository $repository)
    {
    }

    public function runFullAnalysis(int $scenarioId): array
    {
        $scenario = StrategicPlanningScenarios::find($scenarioId);
        if (!$scenario) {
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

    public function transitionDecisionStatus(StrategicPlanningScenarios $scenario, string $toStatus, $user, $notes = null): StrategicPlanningScenarios
    {
        $scenario->decision_status = $toStatus;
        $scenario->save();
        return $scenario;
    }

    public function startExecution(StrategicPlanningScenarios $scenario, $user): StrategicPlanningScenarios
    {
        $scenario->execution_status = 'in_progress';
        $scenario->save();
        return $scenario;
    }

    public function pauseExecution(StrategicPlanningScenarios $scenario, $user, $notes = null): StrategicPlanningScenarios
    {
        $scenario->execution_status = 'paused';
        $scenario->save();
        return $scenario;
    }

    public function completeExecution(StrategicPlanningScenarios $scenario, $user): StrategicPlanningScenarios
    {
        $scenario->execution_status = 'completed';
        $scenario->save();
        return $scenario;
    }

    public function createNewVersion(StrategicPlanningScenarios $scenario, $name, $description, $user, $notes = null, $copySkills = true, $copyStrategies = true)
    {
        $new = $scenario->replicate();
        $new->name = $name ?? ($scenario->name . ' (copy)');
        $new->description = $description ?? $scenario->description;
        $new->is_current_version = false;
        $new->version_group_id = $scenario->version_group_id ?? (string) \Str::uuid();
        $new->version_number = ($scenario->version_number ?? 1) + 1;
        $new->created_by = $user->id ?? null;
        $new->save();

        return $new;
    }

    public function syncParentMandatorySkills(StrategicPlanningScenarios $scenario): int
    {
        // Placeholder - no-op
        return 0;
    }

    public function consolidateParent(StrategicPlanningScenarios $scenario): array
    {
        // Simple rollup placeholder
        return [
            'scenario_id' => $scenario->id,
            'total_children' => $scenario->children()->count() ?? 0,
        ];
    }
}
