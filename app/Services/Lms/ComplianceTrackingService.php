<?php

namespace App\Services\Lms;

use App\Models\LmsComplianceRecord;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ComplianceTrackingService
{
    /**
     * Create compliance record when a user is enrolled in a compliance course.
     */
    public function createRecord(LmsEnrollment $enrollment): ?LmsComplianceRecord
    {
        $course = $enrollment->course;

        if (! $course || ! $course->is_compliance) {
            return null;
        }

        $startDate = $enrollment->started_at ?? $enrollment->created_at ?? Carbon::now();
        $dueDate = $course->compliance_deadline_days
            ? Carbon::parse($startDate)->addDays($course->compliance_deadline_days)
            : Carbon::parse($startDate)->addDays(30);

        return LmsComplianceRecord::create([
            'lms_enrollment_id' => $enrollment->id,
            'lms_course_id' => $course->id,
            'user_id' => $enrollment->user_id,
            'organization_id' => $course->organization_id,
            'due_date' => $dueDate,
            'status' => 'pending',
            'escalation_level' => 0,
        ]);
    }

    /**
     * Check and update overdue records. Returns count of newly overdue records.
     */
    public function checkOverdueRecords(int $organizationId): int
    {
        return LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->where('status', 'pending')
            ->where('due_date', '<', Carbon::today())
            ->update(['status' => 'overdue']);
    }

    /**
     * Process escalations based on how overdue records are.
     */
    public function processEscalations(int $organizationId): array
    {
        $results = ['reminders' => 0, 'urgent' => 0, 'escalated' => 0];
        $today = Carbon::today();

        // 7 days before due: reminder (escalation_level=1)
        $results['reminders'] = LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->whereIn('status', ['pending'])
            ->where('escalation_level', '<', 1)
            ->where('due_date', '<=', $today->copy()->addDays(7))
            ->where('due_date', '>', $today)
            ->update(['escalation_level' => 1]);

        // On due date or past: urgent reminder (escalation_level=2)
        $results['urgent'] = LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->whereIn('status', ['pending', 'overdue'])
            ->where('escalation_level', '<', 2)
            ->where('due_date', '<=', $today)
            ->update(['escalation_level' => 2]);

        // 7 days after due: manager notification (escalation_level=3)
        $results['escalated'] = LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->whereIn('status', ['pending', 'overdue'])
            ->where('escalation_level', '<', 3)
            ->where('due_date', '<=', $today->copy()->subDays(7))
            ->update(['escalation_level' => 3]);

        return $results;
    }

    /**
     * Get compliance dashboard data for an organization.
     */
    public function getDashboardData(int $organizationId): array
    {
        $baseQuery = LmsComplianceRecord::query()->forOrganization($organizationId);

        $totalRecords = (clone $baseQuery)->count();
        $completed = (clone $baseQuery)->completed()->count();
        $pending = (clone $baseQuery)->pending()->count();
        $overdue = (clone $baseQuery)->overdue()->count();

        $complianceRate = $totalRecords > 0
            ? round(($completed / $totalRecords) * 100, 1)
            : 0;

        // By category breakdown
        $byCategory = LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->join('lms_courses', 'lms_compliance_records.lms_course_id', '=', 'lms_courses.id')
            ->select(
                'lms_courses.compliance_category as category',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN lms_compliance_records.status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN lms_compliance_records.status = 'overdue' THEN 1 ELSE 0 END) as overdue"),
            )
            ->groupBy('lms_courses.compliance_category')
            ->get()
            ->toArray();

        // Upcoming due (next 30 days)
        $upcomingDue = (clone $baseQuery)
            ->upcoming(30)
            ->with('user:id,name,email', 'course:id,title,compliance_category')
            ->orderBy('due_date')
            ->limit(50)
            ->get()
            ->map(fn (LmsComplianceRecord $r) => [
                'id' => $r->id,
                'user_name' => $r->user?->name,
                'user_email' => $r->user?->email,
                'course_title' => $r->course?->title,
                'category' => $r->course?->compliance_category,
                'due_date' => $r->due_date->toDateString(),
                'days_until_due' => $r->daysUntilDue(),
            ]);

        // Overdue list
        $overdueList = LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->overdue()
            ->with('user:id,name,email', 'course:id,title,compliance_category')
            ->orderBy('due_date')
            ->limit(50)
            ->get()
            ->map(fn (LmsComplianceRecord $r) => [
                'id' => $r->id,
                'user_name' => $r->user?->name,
                'user_email' => $r->user?->email,
                'course_title' => $r->course?->title,
                'category' => $r->course?->compliance_category,
                'due_date' => $r->due_date->toDateString(),
                'days_overdue' => abs($r->daysUntilDue()),
                'escalation_level' => $r->escalation_level,
            ]);

        return [
            'total_records' => $totalRecords,
            'completed' => $completed,
            'pending' => $pending,
            'overdue' => $overdue,
            'compliance_rate_pct' => $complianceRate,
            'by_category' => $byCategory,
            'upcoming_due' => $upcomingDue,
            'overdue_list' => $overdueList,
        ];
    }

    /**
     * Handle course completion: mark compliance record as completed.
     */
    public function handleCompletion(LmsEnrollment $enrollment): ?LmsComplianceRecord
    {
        $record = LmsComplianceRecord::query()
            ->where('lms_enrollment_id', $enrollment->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->first();

        if (! $record) {
            return null;
        }

        $record->markCompleted();

        return $record;
    }

    /**
     * Create recertification records for expired certifications.
     */
    public function processRecertifications(int $organizationId): int
    {
        $expiredRecords = LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->where('status', 'completed')
            ->whereNotNull('recertification_due_date')
            ->where('recertification_due_date', '<=', Carbon::today())
            ->get();

        $count = 0;

        foreach ($expiredRecords as $record) {
            // Check if a new record already exists for this user+course
            $exists = LmsComplianceRecord::query()
                ->where('user_id', $record->user_id)
                ->where('lms_course_id', $record->lms_course_id)
                ->whereIn('status', ['pending', 'overdue'])
                ->exists();

            if ($exists) {
                continue;
            }

            $course = $record->course;
            $dueDate = $course && $course->compliance_deadline_days
                ? Carbon::today()->addDays($course->compliance_deadline_days)
                : Carbon::today()->addDays(30);

            LmsComplianceRecord::create([
                'lms_enrollment_id' => $record->lms_enrollment_id,
                'lms_course_id' => $record->lms_course_id,
                'user_id' => $record->user_id,
                'organization_id' => $organizationId,
                'due_date' => $dueDate,
                'status' => 'pending',
                'escalation_level' => 0,
                'notes' => 'Recertification required — previous certification expired.',
            ]);

            // Mark original record as expired
            $record->update(['status' => 'expired']);

            $count++;
        }

        return $count;
    }

    /**
     * Export compliance data to CSV format.
     */
    public function exportCsv(int $organizationId, array $filters = []): string
    {
        $query = LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->with('user:id,name,email', 'course:id,title,compliance_category');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category'])) {
            $query->whereHas('course', fn ($q) => $q->where('compliance_category', $filters['category']));
        }

        $records = $query->orderBy('due_date')->get();

        $csv = "ID,User,Email,Course,Category,Due Date,Completed Date,Status,Escalation Level\n";

        foreach ($records as $record) {
            $csv .= implode(',', [
                $record->id,
                '"'.str_replace('"', '""', $record->user?->name ?? '').'"',
                $record->user?->email ?? '',
                '"'.str_replace('"', '""', $record->course?->title ?? '').'"',
                $record->course?->compliance_category ?? '',
                $record->due_date->toDateString(),
                $record->completed_date?->toDateString() ?? '',
                $record->status,
                $record->escalation_level,
            ])."\n";
        }

        return $csv;
    }
}
