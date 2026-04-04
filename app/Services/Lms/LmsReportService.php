<?php

namespace App\Services\Lms;

use App\Models\LmsComplianceRecord;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LmsReportService
{
    /**
     * Completion report by department/category.
     */
    public function completionReport(int $organizationId, array $filters = []): array
    {
        $query = LmsEnrollment::query()
            ->join('lms_courses', 'lms_enrollments.lms_course_id', '=', 'lms_courses.id')
            ->where('lms_courses.organization_id', $organizationId);

        if (! empty($filters['category'])) {
            $query->where('lms_courses.category', $filters['category']);
        }

        $byCategory = (clone $query)
            ->select(
                'lms_courses.category',
                DB::raw('COUNT(*) as total_enrollments'),
                DB::raw("SUM(CASE WHEN lms_enrollments.status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN lms_enrollments.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress"),
                DB::raw('ROUND(AVG(lms_enrollments.progress_percentage)::numeric, 1) as avg_progress'),
            )
            ->groupBy('lms_courses.category')
            ->get()
            ->map(function ($row) {
                $row->completion_rate = $row->total_enrollments > 0
                    ? round(($row->completed / $row->total_enrollments) * 100, 1)
                    : 0;

                return $row;
            })
            ->toArray();

        $totals = (clone $query)
            ->select(
                DB::raw('COUNT(*) as total_enrollments'),
                DB::raw("SUM(CASE WHEN lms_enrollments.status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw('ROUND(AVG(lms_enrollments.progress_percentage)::numeric, 1) as avg_progress'),
            )
            ->first();

        return [
            'by_category' => $byCategory,
            'totals' => [
                'total_enrollments' => $totals->total_enrollments ?? 0,
                'completed' => $totals->completed ?? 0,
                'avg_progress' => (float) ($totals->avg_progress ?? 0),
                'overall_completion_rate' => ($totals->total_enrollments ?? 0) > 0
                    ? round((($totals->completed ?? 0) / $totals->total_enrollments) * 100, 1)
                    : 0,
            ],
        ];
    }

    /**
     * Compliance status report.
     */
    public function complianceStatusReport(int $organizationId): array
    {
        $records = LmsComplianceRecord::query()
            ->forOrganization($organizationId)
            ->join('lms_courses', 'lms_compliance_records.lms_course_id', '=', 'lms_courses.id')
            ->where('lms_courses.is_compliance', true)
            ->select(
                'lms_courses.id as course_id',
                'lms_courses.title',
                'lms_courses.compliance_category',
                DB::raw('COUNT(*) as total_records'),
                DB::raw("SUM(CASE WHEN lms_compliance_records.status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN lms_compliance_records.status = 'pending' THEN 1 ELSE 0 END) as pending"),
                DB::raw("SUM(CASE WHEN lms_compliance_records.status = 'overdue' THEN 1 ELSE 0 END) as overdue"),
            )
            ->groupBy('lms_courses.id', 'lms_courses.title', 'lms_courses.compliance_category')
            ->get()
            ->map(function ($row) {
                $row->compliance_rate = $row->total_records > 0
                    ? round(($row->completed / $row->total_records) * 100, 1)
                    : 0;

                return $row;
            })
            ->toArray();

        $totalCompliance = count($records) > 0
            ? round(collect($records)->avg('compliance_rate'), 1)
            : 0;

        return [
            'courses' => $records,
            'overall_compliance_rate' => $totalCompliance,
        ];
    }

    /**
     * Time-to-complete report.
     */
    public function timeToCompleteReport(int $organizationId): array
    {
        $courses = LmsEnrollment::query()
            ->join('lms_courses', 'lms_enrollments.lms_course_id', '=', 'lms_courses.id')
            ->where('lms_courses.organization_id', $organizationId)
            ->where('lms_enrollments.status', 'completed')
            ->whereNotNull('lms_enrollments.started_at')
            ->whereNotNull('lms_enrollments.completed_at')
            ->select(
                'lms_courses.id as course_id',
                'lms_courses.title',
                'lms_courses.category',
                DB::raw('COUNT(*) as completions'),
                DB::raw('AVG(EXTRACT(EPOCH FROM (lms_enrollments.completed_at - lms_enrollments.started_at)) / 3600) as avg_hours'),
                DB::raw('MIN(EXTRACT(EPOCH FROM (lms_enrollments.completed_at - lms_enrollments.started_at)) / 3600) as min_hours'),
                DB::raw('MAX(EXTRACT(EPOCH FROM (lms_enrollments.completed_at - lms_enrollments.started_at)) / 3600) as max_hours'),
            )
            ->groupBy('lms_courses.id', 'lms_courses.title', 'lms_courses.category')
            ->get()
            ->map(function ($row) {
                $row->avg_hours = round((float) $row->avg_hours, 1);
                $row->min_hours = (int) $row->min_hours;
                $row->max_hours = (int) $row->max_hours;

                return $row;
            })
            ->toArray();

        return [
            'courses' => $courses,
        ];
    }

    /**
     * Engagement trends report — monthly enrollment and completion counts over last 12 months.
     */
    public function engagementTrendsReport(int $organizationId): array
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $enrollments = LmsEnrollment::query()
            ->join('lms_courses', 'lms_enrollments.lms_course_id', '=', 'lms_courses.id')
            ->where('lms_courses.organization_id', $organizationId)
            ->where('lms_enrollments.created_at', '>=', $startDate)
            ->select(
                DB::raw("TO_CHAR(lms_enrollments.created_at, 'YYYY-MM') as month"),
                DB::raw('COUNT(*) as enrollments'),
                DB::raw("SUM(CASE WHEN lms_enrollments.status = 'completed' THEN 1 ELSE 0 END) as completions"),
            )
            ->groupBy(DB::raw("TO_CHAR(lms_enrollments.created_at, 'YYYY-MM')"))
            ->orderBy('month')
            ->get()
            ->toArray();

        return [
            'months' => $enrollments,
        ];
    }

    /**
     * Export any report to CSV.
     */
    public function exportToCsv(array $reportData, string $reportType): string
    {
        $csv = '';

        switch ($reportType) {
            case 'completion':
                $csv = "Category,Total Enrollments,Completed,In Progress,Avg Progress,Completion Rate\n";
                foreach ($reportData['by_category'] ?? [] as $row) {
                    $csv .= implode(',', [
                        $row['category'] ?? 'N/A',
                        $row['total_enrollments'],
                        $row['completed'],
                        $row['in_progress'] ?? 0,
                        $row['avg_progress'],
                        $row['completion_rate'],
                    ])."\n";
                }
                break;

            case 'compliance':
                $csv = "Course,Category,Total,Completed,Pending,Overdue,Compliance Rate\n";
                foreach ($reportData['courses'] ?? [] as $row) {
                    $csv .= implode(',', [
                        '"'.str_replace('"', '""', $row['title'] ?? '').'"',
                        $row['compliance_category'] ?? 'N/A',
                        $row['total_records'],
                        $row['completed'],
                        $row['pending'],
                        $row['overdue'],
                        $row['compliance_rate'],
                    ])."\n";
                }
                break;

            case 'time-to-complete':
                $csv = "Course,Category,Completions,Avg Hours,Min Hours,Max Hours\n";
                foreach ($reportData['courses'] ?? [] as $row) {
                    $csv .= implode(',', [
                        '"'.str_replace('"', '""', $row['title'] ?? '').'"',
                        $row['category'] ?? 'N/A',
                        $row['completions'],
                        $row['avg_hours'],
                        $row['min_hours'],
                        $row['max_hours'],
                    ])."\n";
                }
                break;

            case 'engagement':
                $csv = "Month,Enrollments,Completions\n";
                foreach ($reportData['months'] ?? [] as $row) {
                    $csv .= implode(',', [
                        $row['month'],
                        $row['enrollments'],
                        $row['completions'],
                    ])."\n";
                }
                break;
        }

        return $csv;
    }
}
