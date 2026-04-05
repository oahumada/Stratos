<?php

namespace App\Jobs;

use App\Models\ImprovementFeedback;
use App\Services\Intelligence\FeedbackLoopService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessImprovementSignals implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $organizationId
    ) {}

    public function handle(FeedbackLoopService $feedbackService): void
    {
        $pending = ImprovementFeedback::where('organization_id', $this->organizationId)
            ->pending()
            ->get();

        if ($pending->isEmpty()) {
            Log::channel('agents')->info('ProcessImprovementSignals: no pending feedback', [
                'organization_id' => $this->organizationId,
            ]);

            return;
        }

        $grouped = $pending->groupBy('agent_id');
        $flaggedAgents = [];

        foreach ($grouped as $agentId => $agentFeedback) {
            $total = $agentFeedback->count();

            // Calculate hallucination rate
            $hallucinationCount = $agentFeedback->filter(function ($fb) {
                return is_array($fb->tags) && in_array('hallucination', $fb->tags);
            })->count();

            $hallucinationRate = $total > 0 ? $hallucinationCount / $total : 0;

            if ($hallucinationRate > 0.10) {
                $flaggedAgents[] = [
                    'agent_id' => $agentId,
                    'reason' => 'high_hallucination_rate',
                    'rate' => round($hallucinationRate * 100, 1),
                ];

                Log::channel('agents')->warning('Agent flagged: high hallucination rate', [
                    'agent_id' => $agentId,
                    'organization_id' => $this->organizationId,
                    'hallucination_rate' => round($hallucinationRate * 100, 1).'%',
                    'total_feedback' => $total,
                ]);
            }

            // Check average rating
            $avgRating = $agentFeedback->avg('rating');
            if ($avgRating < 2.5) {
                $flaggedAgents[] = [
                    'agent_id' => $agentId,
                    'reason' => 'low_avg_rating',
                    'avg_rating' => round($avgRating, 2),
                ];

                Log::channel('agents')->warning('Agent flagged: low average rating', [
                    'agent_id' => $agentId,
                    'organization_id' => $this->organizationId,
                    'avg_rating' => round($avgRating, 2),
                ]);
            }
        }

        // Mark all pending as processed
        foreach ($pending as $feedback) {
            $feedbackService->markAsProcessed($feedback);
        }

        // Trigger reindex if needed
        if ($feedbackService->shouldTriggerReindex($this->organizationId)) {
            ReindexKnowledge::dispatch($this->organizationId);
        }

        Log::channel('agents')->info('ProcessImprovementSignals completed', [
            'organization_id' => $this->organizationId,
            'processed_count' => $pending->count(),
            'flagged_agents' => $flaggedAgents,
        ]);
    }
}
