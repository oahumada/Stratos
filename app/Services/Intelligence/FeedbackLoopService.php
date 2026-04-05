<?php

namespace App\Services\Intelligence;

use App\Jobs\ProcessImprovementSignals;
use App\Models\ImprovementFeedback;

class FeedbackLoopService
{
    protected int $reindexThreshold = 10;

    /**
     * Store feedback and dispatch processing if threshold reached.
     */
    public function submitFeedback(array $data, int $organizationId): ImprovementFeedback
    {
        $feedback = ImprovementFeedback::create(array_merge($data, [
            'organization_id' => $organizationId,
            'status' => 'pending',
        ]));

        $pendingCount = ImprovementFeedback::where('organization_id', $organizationId)
            ->pending()
            ->count();

        if ($pendingCount >= $this->reindexThreshold) {
            ProcessImprovementSignals::dispatch($organizationId);
        }

        return $feedback;
    }

    /**
     * Analyze feedback patterns: top error tags, worst agents, trend data.
     */
    public function getPatterns(int $organizationId, ?string $agentId = null, int $days = 30): array
    {
        $query = ImprovementFeedback::where('organization_id', $organizationId)
            ->where('created_at', '>=', now()->subDays($days));

        if ($agentId) {
            $query->byAgent($agentId);
        }

        $feedback = $query->get();

        // Collect all tags across feedback
        $tagCounts = [];
        foreach ($feedback as $fb) {
            foreach (($fb->tags ?? []) as $tag) {
                $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
            }
        }
        arsort($tagCounts);

        // Worst agents by average rating
        $agentRatings = $feedback->groupBy('agent_id')->map(function ($group) {
            return [
                'agent_id' => $group->first()->agent_id,
                'avg_rating' => round($group->avg('rating'), 2),
                'count' => $group->count(),
                'negative_count' => $group->where('rating', '<=', 2)->count(),
            ];
        })->sortBy('avg_rating')->values()->all();

        // Trend: daily feedback counts over the period
        $trend = $feedback->groupBy(fn ($fb) => $fb->created_at->toDateString())
            ->map(fn ($group, $date) => [
                'date' => $date,
                'count' => $group->count(),
                'avg_rating' => round($group->avg('rating'), 2),
            ])->sortKeys()->values()->all();

        return [
            'top_error_tags' => $tagCounts,
            'worst_agents' => $agentRatings,
            'trend' => $trend,
            'total_feedback' => $feedback->count(),
            'period_days' => $days,
        ];
    }

    /**
     * Get acceptance rate, avg rating, feedback count for a specific agent.
     */
    public function getAgentAccuracy(string $agentId, int $organizationId, int $days = 30): array
    {
        $feedback = ImprovementFeedback::where('organization_id', $organizationId)
            ->byAgent($agentId)
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        $total = $feedback->count();
        $positive = $feedback->where('rating', '>=', 4)->count();
        $negative = $feedback->where('rating', '<=', 2)->count();

        return [
            'agent_id' => $agentId,
            'total_feedback' => $total,
            'avg_rating' => $total > 0 ? round($feedback->avg('rating'), 2) : 0,
            'acceptance_rate' => $total > 0 ? round($positive / $total * 100, 1) : 0,
            'negative_rate' => $total > 0 ? round($negative / $total * 100, 1) : 0,
            'positive_count' => $positive,
            'negative_count' => $negative,
            'period_days' => $days,
        ];
    }

    /**
     * Check if negative feedback count in last 7 days exceeds threshold.
     */
    public function shouldTriggerReindex(int $organizationId): bool
    {
        $negativeCount = ImprovementFeedback::where('organization_id', $organizationId)
            ->negative()
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return $negativeCount > $this->reindexThreshold;
    }

    /**
     * Mark a single feedback as processed.
     */
    public function markAsProcessed(ImprovementFeedback $feedback): void
    {
        $feedback->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Dashboard summary: total, by_status, by_tag, by_agent, avg_rating.
     */
    public function getFeedbackSummary(int $organizationId): array
    {
        $feedback = ImprovementFeedback::where('organization_id', $organizationId)->get();

        $byStatus = $feedback->groupBy('status')->map->count()->toArray();

        $tagCounts = [];
        foreach ($feedback as $fb) {
            foreach (($fb->tags ?? []) as $tag) {
                $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
            }
        }

        $byAgent = $feedback->groupBy('agent_id')->map(function ($group) {
            return [
                'count' => $group->count(),
                'avg_rating' => round($group->avg('rating'), 2),
            ];
        })->toArray();

        return [
            'total' => $feedback->count(),
            'avg_rating' => $feedback->count() > 0 ? round($feedback->avg('rating'), 2) : 0,
            'by_status' => $byStatus,
            'by_tag' => $tagCounts,
            'by_agent' => $byAgent,
        ];
    }
}
