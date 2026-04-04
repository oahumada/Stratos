<?php

namespace App\Services\Lms;

use App\Models\LmsEnrollment;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
    ) {}

    public function createQuiz(array $data, int $organizationId): LmsQuiz
    {
        return DB::transaction(function () use ($data, $organizationId) {
            $quiz = LmsQuiz::create([
                'lms_lesson_id' => $data['lms_lesson_id'] ?? null,
                'organization_id' => $organizationId,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'passing_score' => $data['passing_score'] ?? 70,
                'max_attempts' => $data['max_attempts'] ?? 3,
                'time_limit_minutes' => $data['time_limit_minutes'] ?? null,
                'shuffle_questions' => $data['shuffle_questions'] ?? false,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (! empty($data['questions'])) {
                foreach ($data['questions'] as $index => $q) {
                    $quiz->questions()->create([
                        'question_text' => $q['question_text'],
                        'question_type' => $q['question_type'],
                        'options' => $q['options'] ?? null,
                        'correct_answer' => $q['correct_answer'],
                        'points' => $q['points'] ?? 1,
                        'explanation' => $q['explanation'] ?? null,
                        'order' => $q['order'] ?? $index,
                    ]);
                }
            }

            return $quiz->load('questions');
        });
    }

    public function startAttempt(int $quizId, int $userId, int $organizationId): LmsQuizAttempt
    {
        $quiz = LmsQuiz::where('id', $quizId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $existingAttempts = LmsQuizAttempt::where('lms_quiz_id', $quizId)
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->count();

        if ($existingAttempts >= $quiz->max_attempts) {
            abort(422, 'Maximum number of attempts reached.');
        }

        // Check for an incomplete attempt
        $pending = LmsQuizAttempt::where('lms_quiz_id', $quizId)
            ->where('user_id', $userId)
            ->whereNull('completed_at')
            ->first();

        if ($pending) {
            return $pending;
        }

        return LmsQuizAttempt::create([
            'lms_quiz_id' => $quizId,
            'user_id' => $userId,
            'organization_id' => $organizationId,
            'started_at' => now(),
        ]);
    }

    public function submitAttempt(int $attemptId, array $answers, int $organizationId): LmsQuizAttempt
    {
        $attempt = LmsQuizAttempt::where('id', $attemptId)
            ->where('organization_id', $organizationId)
            ->whereNull('completed_at')
            ->firstOrFail();

        $quiz = $attempt->quiz;

        // Enforce time limit
        if ($quiz->time_limit_minutes && $attempt->started_at) {
            $deadline = $attempt->started_at->addMinutes($quiz->time_limit_minutes);
            if (now()->greaterThan($deadline)) {
                abort(422, 'Time limit exceeded.');
            }
        }

        $questions = $quiz->questions;

        return $this->gradeAttempt($attempt, $answers, $questions);
    }

    private function gradeAttempt(LmsQuizAttempt $attempt, array $answers, Collection $questions): LmsQuizAttempt
    {
        $quiz = $attempt->quiz;
        $gradedAnswers = [];
        $totalPoints = 0;
        $maxPoints = 0;

        $answerMap = collect($answers)->keyBy('question_id');

        foreach ($questions as $question) {
            $maxPoints += $question->points;
            $submittedAnswer = $answerMap->get($question->id);
            $userAnswer = $submittedAnswer['answer'] ?? null;
            $isCorrect = $question->checkAnswer($userAnswer);
            $pointsEarned = $isCorrect ? $question->points : 0;
            $totalPoints += $pointsEarned;

            $gradedAnswers[] = [
                'question_id' => $question->id,
                'answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ];
        }

        $score = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 2) : 0;
        $passed = $score >= $quiz->passing_score;

        $attempt->update([
            'answers' => $gradedAnswers,
            'score' => $score,
            'total_points' => $totalPoints,
            'max_points' => $maxPoints,
            'passed' => $passed,
            'completed_at' => now(),
        ]);

        // Update enrollment assessment_score if quiz is linked to a lesson
        if ($quiz->lms_lesson_id) {
            $lesson = $quiz->lesson;
            if ($lesson) {
                $module = $lesson->module;
                if ($module) {
                    $enrollment = LmsEnrollment::where('lms_course_id', $module->lms_course_id)
                        ->where('user_id', $attempt->user_id)
                        ->first();

                    if ($enrollment) {
                        $enrollment->update(['assessment_score' => $score]);
                    }
                }
            }
        }

        return $attempt->fresh();
    }

    public function getStats(int $quizId, int $organizationId): array
    {
        $quiz = LmsQuiz::where('id', $quizId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $attempts = LmsQuizAttempt::where('lms_quiz_id', $quizId)
            ->whereNotNull('completed_at')
            ->get();

        $totalAttempts = $attempts->count();
        $avgScore = $totalAttempts > 0 ? round($attempts->avg('score'), 2) : 0;
        $passRate = $totalAttempts > 0 ? round(($attempts->where('passed', true)->count() / $totalAttempts) * 100, 2) : 0;

        // Per-question stats
        $questions = $quiz->questions;
        $questionStats = [];

        foreach ($questions as $question) {
            $correctCount = 0;
            $totalAnswered = 0;

            foreach ($attempts as $attempt) {
                $answers = collect($attempt->answers ?? []);
                $qa = $answers->firstWhere('question_id', $question->id);
                if ($qa) {
                    $totalAnswered++;
                    if ($qa['is_correct'] ?? false) {
                        $correctCount++;
                    }
                }
            }

            $questionStats[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'total_answered' => $totalAnswered,
                'correct_count' => $correctCount,
                'accuracy' => $totalAnswered > 0 ? round(($correctCount / $totalAnswered) * 100, 2) : 0,
            ];
        }

        return [
            'quiz_id' => $quizId,
            'total_attempts' => $totalAttempts,
            'average_score' => $avgScore,
            'pass_rate' => $passRate,
            'question_stats' => $questionStats,
        ];
    }

    public function generateQuestionsWithAi(int $quizId, string $lessonContent, int $organizationId): array
    {
        $quiz = LmsQuiz::where('id', $quizId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $prompt = "Genera preguntas de evaluación para el siguiente contenido de lección. "
            . "Devuelve un JSON con un array 'questions' donde cada pregunta tenga: "
            . "question_text, question_type (multiple_choice|true_false|fill_blank), "
            . "options (array de {id, text} para multiple_choice), correct_answer (array), "
            . "points (integer), explanation (text). "
            . "\n\nContenido:\n" . $lessonContent;

        $result = $this->orchestrator->agentThink('Arquitecto de Aprendizaje', $prompt);

        $parsed = json_decode($result['response'] ?? '{}', true);
        $questions = $parsed['questions'] ?? [];

        // Persist the generated questions
        DB::transaction(function () use ($quiz, $questions) {
            $maxOrder = $quiz->questions()->max('order') ?? 0;
            foreach ($questions as $index => $q) {
                $quiz->questions()->create([
                    'question_text' => $q['question_text'] ?? '',
                    'question_type' => $q['question_type'] ?? 'multiple_choice',
                    'options' => $q['options'] ?? null,
                    'correct_answer' => $q['correct_answer'] ?? [],
                    'points' => $q['points'] ?? 1,
                    'explanation' => $q['explanation'] ?? null,
                    'order' => $maxOrder + $index + 1,
                ]);
            }
        });

        return $quiz->fresh()->questions->toArray();
    }
}
