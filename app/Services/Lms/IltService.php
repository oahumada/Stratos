<?php

namespace App\Services\Lms;

use App\Models\LmsSession;
use App\Models\LmsSessionAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IltService
{
    public function createSession(int $orgId, int $courseId, array $data): LmsSession
    {
        return LmsSession::create([
            'organization_id' => $orgId,
            'course_id' => $courseId,
            'instructor_id' => $data['instructor_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'session_type' => $data['session_type'],
            'location' => $data['location'] ?? null,
            'meeting_url' => $data['meeting_url'] ?? null,
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'timezone' => $data['timezone'] ?? 'UTC',
            'max_attendees' => $data['max_attendees'] ?? null,
            'is_recording_available' => $data['is_recording_available'] ?? false,
            'recording_url' => $data['recording_url'] ?? null,
        ]);
    }

    public function updateSession(int $sessionId, array $data): LmsSession
    {
        $session = LmsSession::findOrFail($sessionId);
        $session->update($data);

        return $session->fresh();
    }

    public function registerAttendee(int $sessionId, int $userId): LmsSessionAttendance
    {
        $session = LmsSession::findOrFail($sessionId);

        if ($session->isFull()) {
            throw new \RuntimeException('Session is full. No more attendees can register.');
        }

        return LmsSessionAttendance::firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => $userId],
            ['status' => 'registered'],
        );
    }

    public function cancelRegistration(int $sessionId, int $userId): bool
    {
        $attendance = LmsSessionAttendance::where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->first();

        if (! $attendance) {
            return false;
        }

        $attendance->update(['status' => 'cancelled']);

        return true;
    }

    public function markAttendance(int $sessionId, int $userId, string $status): LmsSessionAttendance
    {
        $attendance = LmsSessionAttendance::where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $updateData = ['status' => $status];

        if ($status === 'attended' && ! $attendance->check_in_at) {
            $updateData['check_in_at'] = Carbon::now();
        }

        $attendance->update($updateData);

        return $attendance->fresh();
    }

    public function bulkMarkAttendance(int $sessionId, array $attendanceData): array
    {
        $results = [];

        DB::transaction(function () use ($sessionId, $attendanceData, &$results) {
            foreach ($attendanceData as $entry) {
                $results[] = $this->markAttendance($sessionId, $entry['user_id'], $entry['status']);
            }
        });

        return $results;
    }

    public function getUpcomingSessions(int $orgId, array $filters = []): mixed
    {
        $query = LmsSession::query()
            ->forOrganization($orgId)
            ->with(['course:id,title', 'instructor:id,name', 'attendances'])
            ->orderBy('starts_at');

        if (! empty($filters['from'])) {
            $query->where('starts_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->where('starts_at', '<=', $filters['to']);
        }

        if (! empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (! empty($filters['instructor_id'])) {
            $query->where('instructor_id', $filters['instructor_id']);
        }

        if (! empty($filters['upcoming_only'])) {
            $query->where('starts_at', '>', Carbon::now());
        }

        return $query->paginate($filters['per_page'] ?? 25);
    }

    public function getSessionAttendees(int $sessionId): array
    {
        return LmsSessionAttendance::where('session_id', $sessionId)
            ->with('user:id,name,email')
            ->get()
            ->toArray();
    }

    public function submitFeedback(int $sessionId, int $userId, ?string $feedback, ?int $rating): LmsSessionAttendance
    {
        $attendance = LmsSessionAttendance::where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $attendance->update([
            'feedback' => $feedback,
            'rating' => $rating,
        ]);

        return $attendance->fresh();
    }
}
