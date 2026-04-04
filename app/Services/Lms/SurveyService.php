<?php

namespace App\Services\Lms;

use App\Models\LmsSurvey;
use App\Models\LmsSurveyResponse;
use Carbon\Carbon;

class SurveyService
{
    public function createSurvey(int $orgId, int $courseId, array $data): LmsSurvey
    {
        return LmsSurvey::create([
            'organization_id' => $orgId,
            'course_id' => $courseId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'questions' => $data['questions'],
            'is_mandatory' => $data['is_mandatory'] ?? false,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function submitResponse(int $surveyId, int $userId, array $answers, ?int $enrollmentId = null): LmsSurveyResponse
    {
        $survey = LmsSurvey::findOrFail($surveyId);

        // Extract NPS score from answers if present
        $npsScore = null;
        foreach ($survey->questions as $index => $question) {
            if (($question['type'] ?? '') === 'nps') {
                foreach ($answers as $answer) {
                    if (($answer['question_index'] ?? null) === $index) {
                        $npsScore = (int) $answer['answer'];
                        break 2;
                    }
                }
            }
        }

        return LmsSurveyResponse::create([
            'survey_id' => $surveyId,
            'user_id' => $userId,
            'enrollment_id' => $enrollmentId,
            'answers' => $answers,
            'nps_score' => $npsScore,
            'completed_at' => Carbon::now(),
        ]);
    }

    public function getSurveyResults(int $surveyId): array
    {
        $survey = LmsSurvey::with('responses')->findOrFail($surveyId);
        $responses = $survey->responses;

        $results = [
            'total_responses' => $responses->count(),
            'questions' => [],
        ];

        foreach ($survey->questions as $index => $question) {
            $questionResult = [
                'question' => $question['question'],
                'type' => $question['type'],
            ];

            $questionAnswers = $responses->map(function ($r) use ($index) {
                foreach ($r->answers as $a) {
                    if (($a['question_index'] ?? null) === $index) {
                        return $a['answer'];
                    }
                }

                return null;
            })->filter(fn ($v) => $v !== null);

            switch ($question['type']) {
                case 'nps':
                    $npsData = $this->calculateNps($surveyId);
                    $questionResult['nps'] = $npsData;
                    break;
                case 'rating':
                    $questionResult['average'] = $questionAnswers->avg();
                    $questionResult['count'] = $questionAnswers->count();
                    break;
                case 'text':
                    $questionResult['responses'] = $questionAnswers->values()->toArray();
                    break;
                case 'multiple_choice':
                    $questionResult['distribution'] = $questionAnswers->countBy()->toArray();
                    break;
            }

            $results['questions'][] = $questionResult;
        }

        return $results;
    }

    public function getCourseSurvey(int $courseId): ?LmsSurvey
    {
        return LmsSurvey::where('course_id', $courseId)
            ->where('is_active', true)
            ->latest()
            ->first();
    }

    public function calculateNps(int $surveyId): array
    {
        $responses = LmsSurveyResponse::where('survey_id', $surveyId)
            ->whereNotNull('nps_score')
            ->get();

        $total = $responses->count();

        if ($total === 0) {
            return ['promoters' => 0, 'passives' => 0, 'detractors' => 0, 'nps_score' => 0];
        }

        $promoters = $responses->where('nps_score', '>=', 9)->count();
        $passives = $responses->whereBetween('nps_score', [7, 8])->count();
        $detractors = $responses->where('nps_score', '<=', 6)->count();

        $npsScore = round((($promoters - $detractors) / $total) * 100);

        return [
            'promoters' => $promoters,
            'passives' => $passives,
            'detractors' => $detractors,
            'nps_score' => $npsScore,
        ];
    }

    public function hasUserResponded(int $surveyId, int $userId): bool
    {
        return LmsSurveyResponse::where('survey_id', $surveyId)
            ->where('user_id', $userId)
            ->exists();
    }
}
