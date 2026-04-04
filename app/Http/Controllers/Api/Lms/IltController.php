<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\IltService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IltController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected IltService $iltService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $filters = $request->only(['from', 'to', 'course_id', 'instructor_id', 'upcoming_only', 'per_page']);
        $sessions = $this->iltService->getUpcomingSessions($organizationId, $filters);

        return response()->json($sessions);
    }

    public function store(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $validated = $request->validate([
            'course_id' => 'required|integer|exists:lms_courses,id',
            'instructor_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_type' => 'required|in:in_person,virtual,hybrid',
            'location' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url|max:500',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'timezone' => 'nullable|string|max:50',
            'max_attendees' => 'nullable|integer|min:1',
            'is_recording_available' => 'nullable|boolean',
            'recording_url' => 'nullable|url|max:500',
        ]);

        $session = $this->iltService->createSession($organizationId, $validated['course_id'], $validated);

        return response()->json(['data' => $session], 201);
    }

    public function show(int $session): JsonResponse
    {
        $sessionModel = \App\Models\LmsSession::with(['course:id,title', 'instructor:id,name,email', 'attendances.user:id,name,email'])
            ->findOrFail($session);

        return response()->json(['data' => $sessionModel]);
    }

    public function update(Request $request, int $session): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'session_type' => 'sometimes|in:in_person,virtual,hybrid',
            'location' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url|max:500',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'sometimes|date',
            'timezone' => 'nullable|string|max:50',
            'max_attendees' => 'nullable|integer|min:1',
            'is_recording_available' => 'nullable|boolean',
            'recording_url' => 'nullable|url|max:500',
        ]);

        $updated = $this->iltService->updateSession($session, $validated);

        return response()->json(['data' => $updated]);
    }

    public function destroy(int $session): JsonResponse
    {
        $sessionModel = \App\Models\LmsSession::findOrFail($session);
        $sessionModel->delete();

        return response()->json(['message' => 'Session deleted.']);
    }

    public function register(Request $request, int $session): JsonResponse
    {
        $userId = $request->user()->id;

        try {
            $attendance = $this->iltService->registerAttendee($session, $userId);

            return response()->json(['data' => $attendance], 201);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function cancelRegistration(Request $request, int $session): JsonResponse
    {
        $userId = $request->user()->id;
        $this->iltService->cancelRegistration($session, $userId);

        return response()->json(['message' => 'Registration cancelled.']);
    }

    public function markAttendance(Request $request, int $session): JsonResponse
    {
        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.user_id' => 'required|integer',
            'attendances.*.status' => 'required|in:registered,confirmed,attended,absent,cancelled',
        ]);

        $results = $this->iltService->bulkMarkAttendance($session, $validated['attendances']);

        return response()->json(['data' => $results]);
    }

    public function feedback(Request $request, int $session): JsonResponse
    {
        $validated = $request->validate([
            'feedback' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $userId = $request->user()->id;
        $attendance = $this->iltService->submitFeedback(
            $session,
            $userId,
            $validated['feedback'] ?? null,
            $validated['rating'] ?? null,
        );

        return response()->json(['data' => $attendance]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
