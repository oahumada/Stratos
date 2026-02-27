<?php

namespace App\Services\Assessment;

use App\Models\AssessmentCycle;
use App\Models\People;
use App\Models\AssessmentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AssessmentCycleSchedulerService
{
    /**
     * Process all cycles that should be active or processed.
     */
    public function processCycles(): void
    {
        // 1. Find cycles that should start today
        $pendingCycles = AssessmentCycle::where('status', 'scheduled')
            ->where('starts_at', '<=', now())
            ->get();

        /** @var \App\Models\AssessmentCycle $cycle */
        foreach ($pendingCycles as $cycle) {
            $this->activateCycle($cycle);
        }

        // 2. Process active cycles that haven't been launched yet
        // In this simplified version, we'll assume "activation" launches the requests
    }

    /**
     * Activate a cycle and generate initial assessment requests.
     */
    public function activateCycle(AssessmentCycle $cycle): void
    {
        Log::info("Activating Assessment Cycle: {$cycle->name}");

        $subjects = $this->getTargetPopulation($cycle);
        
        /** @var \App\Models\People $subject */
        foreach ($subjects as $subject) {
            $this->launchRequestsForSubject($cycle, $subject);
        }

        $cycle->update(['status' => 'active']);
    }

    /**
     * Resolve the target population based on cycle scope.
     */
    protected function getTargetPopulation(AssessmentCycle $cycle)
    {
        $query = People::where('organization_id', $cycle->organization_id);
        $scope = $cycle->scope;

        switch ($scope['type'] ?? 'all') {
            case 'department':
                $query->whereIn('department_id', $scope['ids'] ?? []);
                break;
            case 'hipo':
                $query->where('is_high_potential', true);
                break;
            case 'scenario':
                // Logic to find people related to scenario (e.g. via roles)
                // For now, placeholder or all if not implemented
                break;
            case 'all':
            default:
                break;
        }

        return $query->get();
    }

    /**
     * Launch assessment requests for a specific subject based on cycle configuration.
     */
    protected function launchRequestsForSubject(AssessmentCycle $cycle, People $subject): void
    {
        $evaluators = $cycle->evaluators;

        // Self-assessment
        if ($evaluators['self'] ?? false) {
            $this->createRequest($cycle, $subject, $subject, 'self');
        }

        // Manager
        if ($evaluators['manager'] ?? false) {
            $manager = $subject->managers()->first();
            if ($manager) {
                $this->createRequest($cycle, $manager, $subject, 'supervisor');
            }
        }

        // Peers
        if (($evaluators['peers'] ?? 0) > 0) {
            $peers = $subject->peers()->limit($evaluators['peers'])->get();
            foreach ($peers as $peer) {
                $this->createRequest($cycle, $peer, $subject, 'peer');
            }
        }

        // Subordinates
        if ($evaluators['reports'] ?? false) {
            $reports = $subject->subordinates()->get();
            foreach ($reports as $report) {
                $this->createRequest($cycle, $report, $subject, 'subordinate');
            }
        }
    }

    /**
     * Helper to create an assessment request.
     */
    protected function createRequest(AssessmentCycle $cycle, People $evaluator, People $subject, string $relationship): void
    {
        AssessmentRequest::create([
            'organization_id' => $cycle->organization_id,
            'assessment_cycle_id' => $cycle->id,
            'evaluator_id' => $evaluator->id,
            'subject_id' => $subject->id,
            'relationship' => $relationship,
            'status' => 'pending',
            'token' => Str::random(32),
        ]);
        
        // Dispatch notification
        app(\App\Services\Assessment\AssessmentCycleNotificationService::class)->notifyEvaluator($evaluator, $subject, $cycle);
    }
}
