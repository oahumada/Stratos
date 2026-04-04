<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\LessonAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LessonAuditController extends Controller
{
    public function __construct(
        protected LessonAuditService $service,
    ) {}

    public function log(Request $request): JsonResponse
    {
        $request->validate([
            'enrollment_id' => 'required|integer|exists:lms_enrollments,id',
            'lesson_id' => 'required|integer|exists:lms_lessons,id',
            'action' => 'required|string|in:started,viewed,completed,paused,resumed,time_spent,assessment_submitted,resource_downloaded',
            'metadata' => 'nullable|array',
            'module_id' => 'nullable|integer|exists:lms_modules,id',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $log = $this->service->log(
            $request->input('enrollment_id'),
            $request->input('lesson_id'),
            $userId,
            $orgId,
            $request->input('action'),
            $request->input('metadata'),
            $request,
        );

        return response()->json(['success' => true, 'data' => $log], 201);
    }

    public function lessonHistory(Request $request, int $enrollment, int $lesson): JsonResponse
    {
        $logs = $this->service->getLessonHistory($enrollment, $lesson);

        return response()->json(['data' => $logs]);
    }

    public function userTimeline(Request $request, int $enrollment): JsonResponse
    {
        $logs = $this->service->getUserTimeline($enrollment);

        return response()->json(['data' => $logs]);
    }

    public function courseSummary(Request $request, int $course): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $summary = $this->service->getCourseAuditSummary($course, $orgId);

        return response()->json(['data' => $summary]);
    }

    public function export(Request $request, int $enrollment): StreamedResponse|JsonResponse
    {
        $csv = $this->service->exportAuditTrail($enrollment);

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, "audit-trail-enrollment-{$enrollment}.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
