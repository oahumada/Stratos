<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\CalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function __construct(
        protected CalendarService $calendarService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $from = $request->filled('from') ? Carbon::parse($request->from) : null;
        $to = $request->filled('to') ? Carbon::parse($request->to) : null;

        $events = $this->calendarService->getUserEvents($userId, $orgId, $from, $to);

        return response()->json($events);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|string|in:course_deadline,compliance_deadline,session,quiz_deadline',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
        ]);

        $orgId = $this->resolveOrganizationId($request);
        $event = $this->calendarService->createEvent(
            $orgId,
            $request->user()->id,
            $request->title,
            $request->description,
            $request->event_type,
            Carbon::parse($request->starts_at),
            Carbon::parse($request->ends_at),
            $request->related_type,
            $request->related_id
        );

        return response()->json($event, 201);
    }

    public function destroy(Request $request, int $calendarEvent): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $event = \App\Models\LmsCalendarEvent::where('id', $calendarEvent)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }

    public function ical(Request $request): Response
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $content = $this->calendarService->generateIcal($userId, $orgId);

        return response($content, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="lms-calendar.ics"',
        ]);
    }

    public function syncCompliance(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $count = $this->calendarService->syncComplianceDeadlines($orgId);

        return response()->json(['synced' => $count]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
