<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsLearningPath;
use App\Services\Lms\LearningPathService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LearningPathController extends Controller
{
    public function __construct(
        protected LearningPathService $service,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $paths = LmsLearningPath::forOrganization($orgId)
            ->withCount(['items', 'enrollments'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $paths]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'estimated_duration_minutes' => 'nullable|integer|min:0',
            'is_mandatory' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'items' => 'nullable|array',
            'items.*.lms_course_id' => 'required|integer|exists:lms_courses,id',
            'items.*.order' => 'nullable|integer',
            'items.*.is_required' => 'nullable|boolean',
            'items.*.unlock_after_item_id' => 'nullable|integer',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $path = $this->service->createPath($request->all(), $orgId, $request->user()->id);

        return response()->json(['success' => true, 'data' => $path], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $path = LmsLearningPath::forOrganization($orgId)
            ->with(['items.course', 'enrollments'])
            ->findOrFail($id);

        return response()->json(['data' => $path]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $path = LmsLearningPath::forOrganization($orgId)->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'nullable|string|in:beginner,intermediate,advanced',
            'estimated_duration_minutes' => 'nullable|integer|min:0',
            'is_mandatory' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $path->update($request->only([
            'title', 'description', 'level', 'estimated_duration_minutes',
            'is_mandatory', 'is_active',
        ]));

        return response()->json(['success' => true, 'data' => $path->fresh()]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $path = LmsLearningPath::forOrganization($orgId)->findOrFail($id);

        if ($path->enrollments()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una ruta con inscripciones activas.',
            ], 422);
        }

        $path->delete();

        return response()->json(['success' => true]);
    }

    public function enroll(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        // Verify path belongs to org
        LmsLearningPath::forOrganization($orgId)->findOrFail($id);

        $enrollment = $this->service->enrollUser($id, $request->user()->id, $orgId);

        return response()->json(['success' => true, 'data' => $enrollment]);
    }

    public function progress(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        // Verify path belongs to org
        LmsLearningPath::forOrganization($orgId)->findOrFail($id);

        $courses = $this->service->getAvailableCourses($id, $userId, $orgId);

        $enrollment = \App\Models\LmsLearningPathEnrollment::where('lms_learning_path_id', $id)
            ->where('user_id', $userId)
            ->first();

        return response()->json([
            'enrollment' => $enrollment,
            'courses' => $courses,
        ]);
    }

    public function recalculate(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        // Verify path belongs to org
        LmsLearningPath::forOrganization($orgId)->findOrFail($id);

        $enrollment = $this->service->recalculateProgress($id, $userId, $orgId);

        return response()->json(['success' => true, 'data' => $enrollment]);
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:500',
            'target_audience' => 'required|string|max:500',
        ]);

        $orgId = $this->resolveOrganizationId($request);

        $path = $this->service->generatePathWithAi(
            $request->input('topic'),
            $request->input('target_audience'),
            $orgId,
            $request->user()->id,
        );

        return response()->json(['success' => true, 'data' => $path], 201);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
