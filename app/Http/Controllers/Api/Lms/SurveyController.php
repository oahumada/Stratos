<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\SurveyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected SurveyService $surveyService,
    ) {}

    public function show(int $course): JsonResponse
    {
        $survey = $this->surveyService->getCourseSurvey($course);

        if (! $survey) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => $survey]);
    }

    public function store(Request $request, int $course): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|in:nps,rating,text,multiple_choice',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'nullable|array',
            'is_mandatory' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $survey = $this->surveyService->createSurvey($organizationId, $course, $validated);

        return response()->json(['data' => $survey], 201);
    }

    public function submit(int $survey, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_index' => 'required|integer',
            'answers.*.answer' => 'required',
            'enrollment_id' => 'nullable|integer|exists:lms_enrollments,id',
        ]);

        $userId = $request->user()->id;

        if ($this->surveyService->hasUserResponded($survey, $userId)) {
            return response()->json(['message' => 'You have already submitted a response.'], 422);
        }

        $response = $this->surveyService->submitResponse(
            $survey,
            $userId,
            $validated['answers'],
            $validated['enrollment_id'] ?? null,
        );

        return response()->json(['data' => $response], 201);
    }

    public function results(int $survey): JsonResponse
    {
        $results = $this->surveyService->getSurveyResults($survey);

        return response()->json(['data' => $results]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
