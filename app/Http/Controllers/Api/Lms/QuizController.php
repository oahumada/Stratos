<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Services\Lms\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        protected QuizService $quizService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $quizzes = LmsQuiz::forOrganization($orgId)
            ->with('questions')
            ->paginate($request->integer('per_page', 15));

        return response()->json($quizzes);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lms_lesson_id' => 'nullable|integer|exists:lms_lessons,id',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'questions' => 'nullable|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|string|in:multiple_choice,true_false,fill_blank,matching,short_answer',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'required|array',
            'questions.*.points' => 'nullable|integer|min:1',
            'questions.*.explanation' => 'nullable|string',
            'questions.*.order' => 'nullable|integer',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $quiz = $this->quizService->createQuiz($request->all(), $orgId);

        return response()->json($quiz, 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $quiz = LmsQuiz::where('id', $id)
            ->where('organization_id', $orgId)
            ->with('questions')
            ->firstOrFail();

        return response()->json($quiz);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'shuffle_questions' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $orgId = $this->resolveOrganizationId($request);

        $quiz = LmsQuiz::where('id', $id)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        $quiz->update($request->only([
            'title', 'description', 'passing_score', 'max_attempts',
            'time_limit_minutes', 'shuffle_questions', 'is_active',
        ]));

        return response()->json($quiz->fresh()->load('questions'));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $quiz = LmsQuiz::where('id', $id)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        $quiz->delete();

        return response()->json(['message' => 'Quiz deleted.']);
    }

    public function startAttempt(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $attempt = $this->quizService->startAttempt($id, $userId, $orgId);

        return response()->json($attempt, 201);
    }

    public function submitAttempt(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'attempt_id' => 'required|integer',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.answer' => 'present',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $attempt = $this->quizService->submitAttempt(
            $request->input('attempt_id'),
            $request->input('answers'),
            $orgId,
        );

        return response()->json($attempt);
    }

    public function attempts(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $attempts = LmsQuizAttempt::where('lms_quiz_id', $id)
            ->where('organization_id', $orgId)
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($attempts);
    }

    public function stats(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $stats = $this->quizService->getStats($id, $orgId);

        return response()->json($stats);
    }

    public function generateQuestions(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'lesson_content' => 'required|string',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $questions = $this->quizService->generateQuestionsWithAi(
            $id,
            $request->input('lesson_content'),
            $orgId,
        );

        return response()->json(['questions' => $questions]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) ($request->user()?->current_organization_id ?? $request->user()?->organization_id ?? 0);
    }
}
