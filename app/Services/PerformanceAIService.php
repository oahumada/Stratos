<?php

namespace App\Services;

use App\Models\PerformanceCycle;
use App\Models\PerformanceReview;

class PerformanceAIService
{
    /**
     * Compute final score using weighted average.
     * Weights: self 20%, manager 50%, peer 30%.
     * Nulls are skipped and weight redistributed proportionally.
     */
    public function computeFinalScore(PerformanceReview $review): float
    {
        $weights = [];

        if ($review->self_score !== null) {
            $weights['self'] = ['score' => (float) $review->self_score, 'weight' => 0.20];
        }
        if ($review->manager_score !== null) {
            $weights['manager'] = ['score' => (float) $review->manager_score, 'weight' => 0.50];
        }
        if ($review->peer_score !== null) {
            $weights['peer'] = ['score' => (float) $review->peer_score, 'weight' => 0.30];
        }

        if (empty($weights)) {
            return 0.0;
        }

        $totalWeight = array_sum(array_column($weights, 'weight'));
        $weightedSum = 0.0;

        foreach ($weights as $item) {
            $weightedSum += $item['score'] * ($item['weight'] / $totalWeight);
        }

        return round($weightedSum, 2);
    }

    /**
     * Calibrate all completed reviews in a cycle using z-score outlier detection.
     * Adjusts calibration_score for outliers (z-score > 1.5).
     */
    public function calibrateCycle(PerformanceCycle $cycle): array
    {
        $reviews = PerformanceReview::withoutGlobalScopes()
            ->where('cycle_id', $cycle->id)
            ->where('status', 'completed')
            ->whereNotNull('final_score')
            ->get();

        if ($reviews->isEmpty()) {
            return ['adjusted' => 0, 'mean' => 0.0, 'std_dev' => 0.0];
        }

        $scores = $reviews->pluck('final_score')->map(fn ($s) => (float) $s)->toArray();
        $count = count($scores);
        $mean = array_sum($scores) / $count;

        $variance = array_sum(array_map(fn ($s) => ($s - $mean) ** 2, $scores)) / $count;
        $stdDev = sqrt($variance);

        $adjusted = 0;

        foreach ($reviews as $review) {
            $score = (float) $review->final_score;
            $zScore = $stdDev > 0 ? abs($score - $mean) / $stdDev : 0;

            if ($zScore > 1.5) {
                // Adjust toward mean by pulling 50% of the deviation
                $adjustedScore = round($mean + ($score - $mean) * 0.5, 2);
                $review->calibration_score = $adjustedScore;
                $review->status = 'calibrated';
            } else {
                $review->calibration_score = $score;
                $review->status = 'calibrated';
            }

            $review->saveQuietly();
            $adjusted++;
        }

        return [
            'adjusted' => $adjusted,
            'mean' => round($mean, 2),
            'std_dev' => round($stdDev, 2),
        ];
    }

    /**
     * Generate insights for a performance cycle.
     */
    public function generateInsights(PerformanceCycle $cycle): array
    {
        $reviews = PerformanceReview::withoutGlobalScopes()
            ->where('cycle_id', $cycle->id)
            ->whereNotNull('final_score')
            ->with('person')
            ->get();

        if ($reviews->isEmpty()) {
            return [
                'top_performers' => [],
                'needs_attention' => [],
                'avg_score' => 0.0,
                'distribution' => ['high' => 0, 'mid' => 0, 'low' => 0],
            ];
        }

        $scores = $reviews->pluck('final_score')->map(fn ($s) => (float) $s);
        $avgScore = round($scores->avg(), 2);

        $topPerformers = $reviews
            ->filter(fn ($r) => (float) $r->final_score >= 80)
            ->map(fn ($r) => [
                'people_id' => $r->people_id,
                'final_score' => (float) $r->final_score,
            ])
            ->values()
            ->toArray();

        $needsAttention = $reviews
            ->filter(fn ($r) => (float) $r->final_score < 50)
            ->map(fn ($r) => [
                'people_id' => $r->people_id,
                'final_score' => (float) $r->final_score,
            ])
            ->values()
            ->toArray();

        $distribution = [
            'high' => $reviews->filter(fn ($r) => (float) $r->final_score >= 80)->count(),
            'mid' => $reviews->filter(fn ($r) => (float) $r->final_score >= 50 && (float) $r->final_score < 80)->count(),
            'low' => $reviews->filter(fn ($r) => (float) $r->final_score < 50)->count(),
        ];

        return [
            'top_performers' => $topPerformers,
            'needs_attention' => $needsAttention,
            'avg_score' => $avgScore,
            'distribution' => $distribution,
        ];
    }
}
