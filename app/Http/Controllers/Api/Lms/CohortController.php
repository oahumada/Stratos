<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsCohort;
use App\Services\Lms\CohortService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CohortController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected CohortService $cohortService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $cohorts = $this->cohortService->getForOrganization($organizationId);

        return response()->json($cohorts);
    }

    public function store(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|integer|exists:lms_courses,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'max_members' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'facilitator_id' => 'nullable|integer|exists:users,id',
        ]);

        $cohort = $this->cohortService->create(
            $organizationId,
            $request->only(['name', 'description', 'course_id', 'starts_at', 'ends_at', 'max_members', 'is_active', 'facilitator_id']),
        );

        return response()->json($cohort, 201);
    }

    public function show(LmsCohort $cohort): JsonResponse
    {
        $cohort->load(['course', 'facilitator', 'members.user']);
        $progress = $this->cohortService->getCohortProgress($cohort->id);

        return response()->json([
            'cohort' => $cohort,
            'progress' => $progress,
        ]);
    }

    public function update(LmsCohort $cohort, Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|integer|exists:lms_courses,id',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'max_members' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'facilitator_id' => 'nullable|integer|exists:users,id',
        ]);

        $cohort->update($request->only([
            'name', 'description', 'course_id', 'starts_at', 'ends_at', 'max_members', 'is_active', 'facilitator_id',
        ]));

        return response()->json($cohort->fresh());
    }

    public function addMembers(LmsCohort $cohort, Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'role' => 'nullable|string|in:member,facilitator,mentor',
        ]);

        $members = $this->cohortService->bulkAddMembers(
            $cohort->id,
            $request->input('user_ids'),
            $request->input('role', 'member'),
        );

        return response()->json($members, 201);
    }

    public function removeMember(LmsCohort $cohort, Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $this->cohortService->removeMember($cohort->id, $request->input('user_id'));

        return response()->json(['message' => 'Member removed.']);
    }

    public function progress(LmsCohort $cohort): JsonResponse
    {
        $progress = $this->cohortService->getCohortProgress($cohort->id);

        return response()->json($progress);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
