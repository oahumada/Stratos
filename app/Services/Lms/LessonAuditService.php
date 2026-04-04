<?php

namespace App\Services\Lms;

use App\Models\LmsLessonAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonAuditService
{
    public function log(
        int $enrollmentId,
        int $lessonId,
        int $userId,
        int $orgId,
        string $action,
        ?array $metadata = null,
        ?Request $request = null,
    ): LmsLessonAuditLog {
        return LmsLessonAuditLog::create([
            'organization_id' => $orgId,
            'user_id' => $userId,
            'enrollment_id' => $enrollmentId,
            'lesson_id' => $lessonId,
            'action' => $action,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    public function getLessonHistory(int $enrollmentId, int $lessonId): \Illuminate\Database\Eloquent\Collection
    {
        return LmsLessonAuditLog::where('enrollment_id', $enrollmentId)
            ->where('lesson_id', $lessonId)
            ->orderBy('created_at')
            ->get();
    }

    public function getUserTimeline(int $enrollmentId): \Illuminate\Database\Eloquent\Collection
    {
        return LmsLessonAuditLog::where('enrollment_id', $enrollmentId)
            ->orderBy('created_at')
            ->get();
    }

    public function getTimeSpentPerLesson(int $enrollmentId): array
    {
        $logs = LmsLessonAuditLog::where('enrollment_id', $enrollmentId)
            ->whereIn('action', ['started', 'completed'])
            ->orderBy('created_at')
            ->get()
            ->groupBy('lesson_id');

        $result = [];
        foreach ($logs as $lessonId => $lessonLogs) {
            $totalSeconds = 0;
            $startTime = null;

            foreach ($lessonLogs as $log) {
                if ($log->action === 'started') {
                    $startTime = $log->created_at;
                } elseif ($log->action === 'completed' && $startTime) {
                    $totalSeconds += abs((int) $log->created_at->diffInSeconds($startTime));
                    $startTime = null;
                }
            }

            // Also check time_spent metadata entries
            $timeSpentLogs = LmsLessonAuditLog::where('enrollment_id', $enrollmentId)
                ->where('lesson_id', $lessonId)
                ->where('action', 'time_spent')
                ->get();

            foreach ($timeSpentLogs as $tsLog) {
                $totalSeconds += ($tsLog->metadata['time_spent_seconds'] ?? 0);
            }

            $result[$lessonId] = $totalSeconds;
        }

        return $result;
    }

    public function getCourseAuditSummary(int $courseId, int $orgId): array
    {
        $stats = DB::table('lms_lesson_audit_logs as a')
            ->join('lms_enrollments as e', 'a.enrollment_id', '=', 'e.id')
            ->where('e.lms_course_id', $courseId)
            ->where('a.organization_id', $orgId)
            ->select(
                'a.lesson_id',
                DB::raw("COUNT(CASE WHEN a.action = 'viewed' OR a.action = 'started' THEN 1 END) as views"),
                DB::raw("COUNT(CASE WHEN a.action = 'completed' THEN 1 END) as completions"),
                DB::raw('COUNT(DISTINCT a.user_id) as unique_users'),
            )
            ->groupBy('a.lesson_id')
            ->get();

        return $stats->map(fn ($row) => [
            'lesson_id' => $row->lesson_id,
            'views' => (int) $row->views,
            'completions' => (int) $row->completions,
            'unique_users' => (int) $row->unique_users,
        ])->toArray();
    }

    public function exportAuditTrail(int $enrollmentId, string $format = 'csv'): string
    {
        $logs = LmsLessonAuditLog::where('enrollment_id', $enrollmentId)
            ->with(['lesson', 'user'])
            ->orderBy('created_at')
            ->get();

        $lines = [];
        $lines[] = implode(',', ['Date', 'User', 'Lesson', 'Action', 'IP Address', 'Metadata']);

        foreach ($logs as $log) {
            $lines[] = implode(',', [
                $log->created_at?->toIso8601String() ?? '',
                '"'.addslashes($log->user?->name ?? '').'"',
                '"'.addslashes($log->lesson?->title ?? '').'"',
                $log->action,
                $log->ip_address ?? '',
                '"'.addslashes(json_encode($log->metadata ?? [])).'"',
            ]);
        }

        return implode("\n", $lines);
    }
}
