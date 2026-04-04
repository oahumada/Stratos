<?php

namespace App\Services\Lms;

use App\Models\LmsCalendarEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    public function createEvent(
        int $orgId,
        int $userId,
        string $title,
        ?string $description,
        string $type,
        Carbon $startsAt,
        Carbon $endsAt,
        ?string $relatedType = null,
        ?int $relatedId = null
    ): LmsCalendarEvent {
        return LmsCalendarEvent::create([
            'organization_id' => $orgId,
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'event_type' => $type,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
        ]);
    }

    public function getUserEvents(int $userId, int $orgId, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = LmsCalendarEvent::where('user_id', $userId)
            ->where('organization_id', $orgId);

        if ($from) {
            $query->where('starts_at', '>=', $from);
        }
        if ($to) {
            $query->where('ends_at', '<=', $to);
        }

        return $query->orderBy('starts_at')->get();
    }

    public function generateIcal(int $userId, int $orgId): string
    {
        $events = $this->getUserEvents($userId, $orgId);

        $ical = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Stratos LMS//EN\r\n";

        foreach ($events as $event) {
            $dtStart = $event->starts_at->format('Ymd\THis\Z');
            $dtEnd = $event->ends_at->format('Ymd\THis\Z');
            $uid = "lms-event-{$event->id}@stratos";

            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:{$uid}\r\n";
            $ical .= "DTSTART:{$dtStart}\r\n";
            $ical .= "DTEND:{$dtEnd}\r\n";
            $ical .= "SUMMARY:{$event->title}\r\n";
            if ($event->description) {
                $ical .= "DESCRIPTION:{$event->description}\r\n";
            }
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        return $ical;
    }

    public function syncComplianceDeadlines(int $orgId): int
    {
        $records = \App\Models\LmsComplianceRecord::where('organization_id', $orgId)
            ->whereNotNull('due_date')
            ->where('status', '!=', 'completed')
            ->get();

        $created = 0;
        foreach ($records as $record) {
            $exists = LmsCalendarEvent::where('organization_id', $orgId)
                ->where('related_type', 'compliance_record')
                ->where('related_id', $record->id)
                ->exists();

            if (! $exists) {
                $this->createEvent(
                    $orgId,
                    $record->user_id,
                    "Compliance deadline: " . ($record->course?->title ?? 'Course'),
                    null,
                    'compliance_deadline',
                    Carbon::parse($record->due_date)->startOfDay(),
                    Carbon::parse($record->due_date)->endOfDay(),
                    'compliance_record',
                    $record->id
                );
                $created++;
            }
        }

        return $created;
    }

    public function deleteEvent(int $eventId): bool
    {
        return LmsCalendarEvent::findOrFail($eventId)->delete();
    }
}
