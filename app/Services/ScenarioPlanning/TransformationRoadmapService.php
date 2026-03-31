<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Scenario;
use App\Models\TransformationPhase;
use App\Models\TransformationTask;

class TransformationRoadmapService
{
    /**
     * Generate phased transformation roadmap from current to future state
     */
    public function generateTransformationPlan(Scenario $scenario, array $options = []): void
    {
        // Delete existing phases for scenario (fresh generation)
        TransformationPhase::where('scenario_id', $scenario->id)->delete();

        $totalTimelineMonths = ($scenario->time_horizon_weeks ?? 52) / 4;

        // Phase 1 (0-6 months): Foundation
        $this->createPhase($scenario, 1, 'Foundation', 0, 6, [
            'objectives' => [
                'Identify and hire for critical roles',
                'Establish succession pipelines',
                'Launch leadership development programs',
            ],
            'headcount_targets' => $this->getPhase1HeadcountTargets($scenario),
        ]);

        // Phase 2 (6-12 months): Scale
        $this->createPhase($scenario, 2, 'Scale', 6, 6, [
            'objectives' => [
                'Build core teams',
                'Develop internal talent',
                'Establish performance metrics',
            ],
            'headcount_targets' => $this->getPhase2HeadcountTargets($scenario),
        ]);

        // Phase 3 (12+ months): Optimize (if timeline allows)
        if ($totalTimelineMonths > 12) {
            $remainingMonths = (int) ($totalTimelineMonths - 12);
            $this->createPhase($scenario, 3, 'Optimize', 12, $remainingMonths, [
                'objectives' => [
                    'Mature processes',
                    'Optimize performance',
                    'Exit legacy roles',
                ],
                'headcount_targets' => $this->getPhase3HeadcountTargets($scenario),
            ]);
        }

        // Generate tasks for each phase
        $this->generatePhaseTasks($scenario);
    }

    /**
     * Identify blockers/dependencies between tasks
     */
    public function identifyBlockers(TransformationPhase $phase): array
    {
        $blockers = [];

        foreach ($phase->tasks as $task) {
            $potentialBlockers = [];

            // Check resource conflicts
            if ($this->hasResourceConflict($task)) {
                $potentialBlockers[] = 'Resource conflict with other tasks';
            }

            // Check skill availability
            if ($this->hasSkillBottleneck($task, $phase->scenario)) {
                $potentialBlockers[] = 'Skill bottleneck - insufficient trained people';
            }

            // Check budget constraints
            if ($this->exceedsBudget($task, $phase->scenario)) {
                $potentialBlockers[] = 'Budget constraint - phase budget exceeded';
            }

            // Check timeline feasibility
            if ($this->isTimelineUnrealistic($task, $phase)) {
                $potentialBlockers[] = 'Timeline unrealistic given dependencies';
            }

            if (count($potentialBlockers) > 0) {
                $blockers[] = [
                    'task_id' => $task->id,
                    'task_name' => $task->task_name,
                    'blockers' => $potentialBlockers,
                ];
            }
        }

        return $blockers;
    }

    /**
     * Get all phases with their tasks for a scenario
     */
    public function getRoadmap(Scenario $scenario): array
    {
        $phases = TransformationPhase::where('scenario_id', $scenario->id)
            ->with('tasks')
            ->orderBy('phase_number')
            ->get();

        return $phases->map(fn ($phase) => [
            'id' => $phase->id,
            'phase_number' => $phase->phase_number,
            'phase_name' => $phase->phase_name,
            'start_month' => $phase->start_month,
            'duration_months' => $phase->duration_months,
            'end_month' => $phase->getEndMonth(),
            'completion_pct' => $phase->getCompletionPercentage(),
            'task_count' => $phase->taskCount(),
            'completed_count' => $phase->completedTaskCount(),
            'objectives' => $phase->objectives ?? [],
            'tasks' => $phase->tasks->map(fn ($t) => [
                'id' => $t->id,
                'task_name' => $t->task_name,
                'status' => $t->status,
                'owner' => $t->owner?->name,
                'due_date' => $t->due_date?->format('Y-m-d'),
            ]),
        ])->toArray();
    }

    // ── Private Helper Methods ──────────────────────────────────────────────

    /**
     * Create a transformation phase with given parameters
     */
    private function createPhase(Scenario $scenario, int $phaseNumber, string $phaseName, int $startMonth, int $durationMonths, array $data): void
    {
        TransformationPhase::create([
            'organization_id' => $scenario->organization_id,
            'scenario_id' => $scenario->id,
            'phase_number' => $phaseNumber,
            'phase_name' => $phaseName,
            'start_month' => $startMonth,
            'duration_months' => $durationMonths,
            'objectives' => $data['objectives'] ?? [],
            'headcount_targets' => $data['headcount_targets'] ?? [],
            'key_milestones' => $data['key_milestones'] ?? [],
        ]);
    }

    /**
     * Get headcount targets for Phase 1
     */
    private function getPhase1HeadcountTargets(Scenario $scenario): array
    {
        return [
            'total_headcount' => 50,
            'new_hires' => 10,
            'internal_transfers' => 5,
            'attrition_expected' => 2,
        ];
    }

    /**
     * Get headcount targets for Phase 2
     */
    private function getPhase2HeadcountTargets(Scenario $scenario): array
    {
        return [
            'total_headcount' => 75,
            'new_hires' => 20,
            'internal_transfers' => 10,
            'attrition_expected' => 3,
        ];
    }

    /**
     * Get headcount targets for Phase 3
     */
    private function getPhase3HeadcountTargets(Scenario $scenario): array
    {
        return [
            'total_headcount' => 100,
            'new_hires' => 15,
            'internal_transfers' => 5,
            'attrition_expected' => 2,
        ];
    }

    /**
     * Generate sample tasks for each phase
     */
    private function generatePhaseTasks(Scenario $scenario): void
    {
        $phases = TransformationPhase::where('scenario_id', $scenario->id)->get();

        foreach ($phases as $phase) {
            // Create sample tasks per phase
            TransformationTask::create([
                'organization_id' => $scenario->organization_id,
                'phase_id' => $phase->id,
                'task_name' => 'Define objectives and success metrics',
                'description' => 'Align stakeholders on phase objectives',
                'status' => 'not_started',
                'start_date' => now()->addMonths($phase->start_month),
                'due_date' => now()->addMonths($phase->start_month + 1),
            ]);

            TransformationTask::create([
                'organization_id' => $scenario->organization_id,
                'phase_id' => $phase->id,
                'task_name' => 'Resource allocation',
                'description' => 'Allocate budget and people to phase activities',
                'status' => 'not_started',
                'start_date' => now()->addMonths($phase->start_month),
                'due_date' => now()->addMonths($phase->start_month + 1),
            ]);
        }
    }

    /**
     * Check if task has resource conflicts
     */
    private function hasResourceConflict(TransformationTask $task): bool
    {
        // Simplified: check if owner has overlapping tasks
        if (! $task->owner_id) {
            return false;
        }

        $overlappingTasks = TransformationTask::where('owner_id', $task->owner_id)
            ->where('id', '!=', $task->id)
            ->where('status', '!=', 'completed')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('start_date', [$task->start_date, $task->due_date])
            ->count();

        return $overlappingTasks > 0;
    }

    /**
     * Check if task has skill bottleneck
     */
    private function hasSkillBottleneck(TransformationTask $task, Scenario $scenario): bool
    {
        // Simplified: estimate based on task description keywords
        $keywords = ['training', 'development', 'certification'];
        $taskLower = strtolower($task->task_name);

        foreach ($keywords as $keyword) {
            if (str_contains($taskLower, $keyword)) {
                return rand(0, 1) === 1; // 50% chance for demo
            }
        }

        return false;
    }

    /**
     * Check if task exceeds budget
     */
    private function exceedsBudget(TransformationTask $task, Scenario $scenario): bool
    {
        // Simplified: check if scenario budget is set and low
        $budget = $scenario->estimated_budget ?? 100000;

        return $budget < 50000; // Demo logic
    }

    /**
     * Check if timeline is unrealistic
     */
    private function isTimelineUnrealistic(TransformationTask $task, TransformationPhase $phase): bool
    {
        if (! $task->due_date) {
            return false;
        }

        $phaseEndDate = now()->addMonths($phase->start_month + $phase->duration_months);

        return $task->due_date > $phaseEndDate;
    }
}
