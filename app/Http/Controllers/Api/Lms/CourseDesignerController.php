<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\CourseDesignerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseDesignerController extends Controller
{
    public function __construct(
        protected CourseDesignerService $designer,
    ) {}

    public function generateOutline(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'target_audience' => 'required|string|max:500',
            'skill_gaps' => 'nullable|array',
            'skill_gaps.*' => 'string|max:200',
            'duration_target' => 'nullable|numeric|min:1|max:200',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
        ]);

        $organizationId = $this->resolveOrganizationId($request);

        $result = $this->designer->generateOutline($organizationId, $request->only([
            'topic', 'target_audience', 'skill_gaps', 'duration_target', 'level',
        ]));

        return response()->json($result);
    }

    public function generateContent(Request $request): JsonResponse
    {
        $request->validate([
            'lesson_title' => 'required|string|max:500',
            'module_context' => 'nullable|string|max:500',
            'course_topic' => 'nullable|string|max:500',
            'content_type' => 'nullable|string|in:article,video_script,exercise',
        ]);

        $organizationId = $this->resolveOrganizationId($request);

        $result = $this->designer->generateLessonContent($organizationId, $request->only([
            'lesson_title', 'module_context', 'course_topic', 'content_type',
        ]));

        return response()->json($result);
    }

    public function persist(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'estimated_duration_minutes' => 'nullable|integer|min:0',
            'xp_points' => 'nullable|integer|min:0',
            'cert_min_resource_completion_ratio' => 'nullable|numeric|min:0|max:1',
            'cert_require_assessment_score' => 'nullable|boolean',
            'cert_min_assessment_score' => 'nullable|numeric|min:0|max:100',
            'cert_template_id' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.order' => 'nullable|integer',
            'modules.*.lessons' => 'nullable|array',
            'modules.*.lessons.*.title' => 'required|string|max:255',
            'modules.*.lessons.*.description' => 'nullable|string',
            'modules.*.lessons.*.content_type' => 'nullable|string|max:50',
            'modules.*.lessons.*.content_body' => 'nullable|string',
            'modules.*.lessons.*.content_url' => 'nullable|string|max:2048',
            'modules.*.lessons.*.order' => 'nullable|integer',
            'modules.*.lessons.*.duration_minutes' => 'nullable|integer|min:0',
        ]);

        $organizationId = $this->resolveOrganizationId($request);

        $course = $this->designer->persistCourse($organizationId, $request->all());

        return response()->json([
            'success' => true,
            'course' => $course,
        ], 201);
    }

    public function review(Request $request, int $id): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        $result = $this->designer->reviewCourse($id, $organizationId);

        return response()->json($result);
    }

    public function preview(Request $request, int $id): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        $course = $this->designer->previewCourse($id, $organizationId);

        return response()->json(['course' => $course]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) ($request->user()?->current_organization_id ?? $request->user()?->organization_id ?? 0);
    }
}
