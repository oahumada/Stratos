<?php

namespace App\Services\Lms;

use App\Models\LmsAdaptiveRule;
use App\Models\LmsLearnerProfile;
use App\Models\LmsQuizAttempt;
use Illuminate\Support\Collection;

class AdaptiveLearningService
{
    public function getOrCreateProfile(int $userId, int $orgId): LmsLearnerProfile
    {
        return LmsLearnerProfile::firstOrCreate(
            ['user_id' => $userId, 'organization_id' => $orgId],
            [
                'proficiency_level' => 'beginner',
                'learning_pace' => 'normal',
                'completed_assessments' => 0,
            ],
        );
    }

    public function calibrateProfile(int $userId, int $orgId): LmsLearnerProfile
    {
        $profile = $this->getOrCreateProfile($userId, $orgId);

        // Calculate from quiz attempts
        $attempts = LmsQuizAttempt::where('user_id', $userId)->get();
        $completedCount = $attempts->count();
        $avgScore = $attempts->avg('score');

        $proficiency = match (true) {
            $avgScore === null => 'beginner',
            $avgScore >= 90 => 'expert',
            $avgScore >= 70 => 'advanced',
            $avgScore >= 50 => 'intermediate',
            default => 'beginner',
        };

        $pace = 'normal';
        if ($completedCount > 3 && $attempts->first()?->started_at && $attempts->first()?->completed_at) {
            $avgDuration = $attempts->filter(fn ($a) => $a->started_at && $a->completed_at)
                ->avg(fn ($a) => $a->completed_at->diffInSeconds($a->started_at));
            if ($avgDuration !== null && $avgDuration < 300) {
                $pace = 'fast';
            } elseif ($avgDuration !== null && $avgDuration > 1200) {
                $pace = 'slow';
            }
        }

        $profile->update([
            'proficiency_level' => $proficiency,
            'learning_pace' => $pace,
            'completed_assessments' => $completedCount,
            'average_score' => $avgScore ? round($avgScore, 2) : null,
            'last_calibrated_at' => now(),
        ]);

        return $profile->fresh();
    }

    public function evaluateRules(int $userId, int $courseId, int $orgId): Collection
    {
        $profile = $this->getOrCreateProfile($userId, $orgId);
        $rules = LmsAdaptiveRule::where('organization_id', $orgId)
            ->where('course_id', $courseId)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $actions = collect();

        foreach ($rules as $rule) {
            $matched = match ($rule->condition_type) {
                'score_below' => $profile->average_score !== null && $profile->average_score < (float) $rule->condition_value,
                'score_above' => $profile->average_score !== null && $profile->average_score > (float) $rule->condition_value,
                'proficiency_level' => $profile->proficiency_level === $rule->condition_value,
                'attempts_exceeded' => $profile->completed_assessments > (int) $rule->condition_value,
                default => false,
            };

            if ($matched) {
                $actions->push([
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->rule_name,
                    'action_type' => $rule->action_type,
                    'action_config' => $rule->action_config,
                ]);
            }
        }

        return $actions;
    }

    public function applyAction(int $userId, array $action): array
    {
        // Stub: return action description
        return [
            'applied' => true,
            'user_id' => $userId,
            'action_type' => $action['action_type'],
            'description' => "Action '{$action['action_type']}' would be applied for rule '{$action['rule_name']}'.",
            'config' => $action['action_config'] ?? null,
        ];
    }

    public function createRule(int $orgId, int $courseId, array $data): LmsAdaptiveRule
    {
        return LmsAdaptiveRule::create([
            'organization_id' => $orgId,
            'course_id' => $courseId,
            'rule_name' => $data['rule_name'],
            'condition_type' => $data['condition_type'],
            'condition_value' => $data['condition_value'],
            'action_type' => $data['action_type'],
            'action_config' => $data['action_config'] ?? null,
            'priority' => $data['priority'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function getRecommendedPath(int $userId, int $courseId, int $orgId): array
    {
        $profile = $this->getOrCreateProfile($userId, $orgId);
        $actions = $this->evaluateRules($userId, $courseId, $orgId);

        return [
            'profile' => $profile->toArray(),
            'recommended_actions' => $actions->toArray(),
            'suggested_pace' => $profile->learning_pace,
            'proficiency' => $profile->proficiency_level,
        ];
    }

    public function updateProfileAfterQuiz(int $userId, int $orgId, float $quizScore, ?string $topic = null): LmsLearnerProfile
    {
        $profile = $this->getOrCreateProfile($userId, $orgId);

        $completedAssessments = $profile->completed_assessments + 1;
        $currentAvg = $profile->average_score ?? 0;
        $newAvg = (($currentAvg * ($completedAssessments - 1)) + $quizScore) / $completedAssessments;

        $updates = [
            'completed_assessments' => $completedAssessments,
            'average_score' => round($newAvg, 2),
        ];

        if ($topic) {
            $strengths = $profile->strengths ?? [];
            $weaknesses = $profile->weaknesses ?? [];

            if ($quizScore >= 80) {
                if (! in_array($topic, $strengths)) {
                    $strengths[] = $topic;
                }
                $weaknesses = array_values(array_filter($weaknesses, fn ($w) => $w !== $topic));
            } elseif ($quizScore < 50) {
                if (! in_array($topic, $weaknesses)) {
                    $weaknesses[] = $topic;
                }
                $strengths = array_values(array_filter($strengths, fn ($s) => $s !== $topic));
            }

            $updates['strengths'] = $strengths;
            $updates['weaknesses'] = $weaknesses;
        }

        $profile->update($updates);

        return $profile->fresh();
    }
}
