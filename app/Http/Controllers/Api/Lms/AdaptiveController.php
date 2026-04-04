<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsAdaptiveRule;
use App\Services\Lms\AdaptiveLearningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdaptiveController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected AdaptiveLearningService $adaptiveService,
    ) {}

    public function profile(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $profile = $this->adaptiveService->getOrCreateProfile(
            $request->user()->id,
            $organizationId,
        );

        return response()->json($profile);
    }

    public function calibrate(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $profile = $this->adaptiveService->calibrateProfile(
            $request->user()->id,
            $organizationId,
        );

        return response()->json($profile);
    }

    public function recommendations(int $course, Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $path = $this->adaptiveService->getRecommendedPath(
            $request->user()->id,
            $course,
            $organizationId,
        );

        return response()->json($path);
    }

    public function rules(int $course, Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $rules = LmsAdaptiveRule::where('organization_id', $organizationId)
            ->where('course_id', $course)
            ->orderBy('priority', 'desc')
            ->get();

        return response()->json($rules);
    }

    public function storeRule(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $request->validate([
            'course_id' => 'required|integer|exists:lms_courses,id',
            'rule_name' => 'required|string|max:255',
            'condition_type' => 'required|string|in:score_below,score_above,time_exceeded,attempts_exceeded,proficiency_level',
            'condition_value' => 'required|string',
            'action_type' => 'required|string|in:skip_lesson,add_remedial,suggest_resource,unlock_advanced,slow_pace,fast_pace',
            'action_config' => 'nullable|array',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $rule = $this->adaptiveService->createRule(
            $organizationId,
            $request->input('course_id'),
            $request->only(['rule_name', 'condition_type', 'condition_value', 'action_type', 'action_config', 'priority', 'is_active']),
        );

        return response()->json($rule, 201);
    }

    public function updateRule(LmsAdaptiveRule $adaptiveRule, Request $request): JsonResponse
    {
        $request->validate([
            'rule_name' => 'sometimes|required|string|max:255',
            'condition_type' => 'nullable|string|in:score_below,score_above,time_exceeded,attempts_exceeded,proficiency_level',
            'condition_value' => 'nullable|string',
            'action_type' => 'nullable|string|in:skip_lesson,add_remedial,suggest_resource,unlock_advanced,slow_pace,fast_pace',
            'action_config' => 'nullable|array',
            'priority' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $adaptiveRule->update($request->only([
            'rule_name', 'condition_type', 'condition_value', 'action_type', 'action_config', 'priority', 'is_active',
        ]));

        return response()->json($adaptiveRule->fresh());
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
