<?php

namespace App\Services\Assessment;

use App\Models\AssessmentFeedback;
use App\Models\PeopleRoleSkills;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompetencyAssessmentService
{
    /**
     * Weights for different relationship types in 360 feedback.
     */
    protected array $weights = [
        'supervisor' => 0.40,
        'peer' => 0.30,
        'subordinate' => 0.20,
        'self' => 0.10,
        'other' => 0.05, // Fallback
    ];

    /**
     * Calculate and update the competency level for a person and skill based on 360 feedback.
     *
     * @param int $peopleId The person being evaluated.
     * @param int $skillId The skill to update.
     * @return array Result metadata including new level and confidence.
     */
    public function calculateAndUpdateLevel(int $peopleId, int $skillId): array
    {
        // 1. Fetch all recent valid feedback for this person/skill (last 6 months)
        $feedbacks = AssessmentFeedback::join('assessment_requests', 'assessment_feedback.assessment_request_id', '=', 'assessment_requests.id')
            ->where('assessment_requests.subject_id', $peopleId)
            ->where('assessment_feedback.skill_id', $skillId)
            ->where('assessment_requests.status', 'completed')
            ->where('assessment_requests.updated_at', '>=', now()->subMonths(6))
            ->select(
                'assessment_feedback.score',
                'assessment_feedback.confidence_level',
                'assessment_requests.relationship',
                'assessment_requests.evaluator_id'
            )
            ->get();

        if ($feedbacks->isEmpty()) {
            return ['status' => 'no_data', 'level' => null];
        }

        // 2. Calculate Weighted Score
        $totalWeight = 0;
        $weightedSum = 0;
        $confidenceSum = 0;
        
        foreach ($feedbacks as $feedback) {
            $weight = $this->weights[$feedback->relationship] ?? $this->weights['other'];
            
            // Adjust weight by evaluator's confidence if provided (0-100)
            // If confidence is low, reduce impact.
            $confidenceMultiplier = ($feedback->confidence_level ?? 80) / 100.0;
            $effectiveWeight = $weight * $confidenceMultiplier;

            $weightedSum += $feedback->score * $effectiveWeight;
            $totalWeight += $effectiveWeight;
            
            $confidenceSum += ($feedback->confidence_level ?? 0);
        }

        if ($totalWeight <= 0) {
            return ['status' => 'insufficient_weight', 'level' => null];
        }

        $finalScore = $weightedSum / $totalWeight;
        $roundedLevel = round($finalScore); // BARS levels are integers 1-5
        
        // Ensure within bounds
        $roundedLevel = max(1, min(5, $roundedLevel));

        // 3. Consistency Analysis (Standard Deviation)
        // Calculate raw unweighted variance for dispersion check
        $variance = 0;
        $count = $feedbacks->count();
        $rawAvg = $feedbacks->avg('score');
        
        foreach ($feedbacks as $f) {
            $variance += pow($f->score - $rawAvg, 2);
        }
        $stdDev = $count > 1 ? sqrt($variance / ($count - 1)) : 0;
        
        $calibrationNeeded = $stdDev > 1.5;
        $notes = "Score: " . number_format($finalScore, 2) . ". Sources: {$count}. SD: " . number_format($stdDev, 2);
        if ($calibrationNeeded) {
            $notes .= " [REQUIRES CALIBRATION - High Dispersion]";
        }

        // 4. Update PeopleRoleSkills
        // We find the active record or create one.
        PeopleRoleSkills::updateOrCreate(
            [
                'people_id' => $peopleId,
                'skill_id' => $skillId,
                'is_active' => true
            ],
            [
                'current_level' => $roundedLevel,
                'evidence_source' => 'Talent360',
                'evaluated_at' => now(),
                'notes' => $notes,
                'verified' => !$calibrationNeeded // Auto-verify only if consistent
            ]
        );

        Log::info("Updated competency level for Person {$peopleId}, Skill {$skillId}. Level: {$roundedLevel}. SD: {$stdDev}");

        return [
            'status' => 'updated',
            'level' => $roundedLevel,
            'raw_score' => $finalScore,
            'std_dev' => $stdDev,
            'calibration_needed' => $calibrationNeeded,
            'sources_count' => $count,
            'avg_confidence' => $feedbacks->count() > 0 ? ($confidenceSum / $feedbacks->count()) : 0
        ];
    }

    /**
     * Process all pending skills for a person.
     * Can be called after an assessment cycle closes.
     */
    public function updateAllSkillsForPerson(int $peopleId): array
    {
        // Find all unique skills rated for this person recently
        $skillIds = AssessmentFeedback::join('assessment_requests', 'assessment_feedback.assessment_request_id', '=', 'assessment_requests.id')
            ->where('assessment_requests.subject_id', $peopleId)
            ->where('assessment_requests.status', 'completed')
            ->whereNotNull('assessment_feedback.skill_id')
            ->distinct()
            ->pluck('assessment_feedback.skill_id');

        $results = [];
        foreach ($skillIds as $skillId) {
            $results[$skillId] = $this->calculateAndUpdateLevel($peopleId, $skillId);
        }

        return $results;
    }
}
