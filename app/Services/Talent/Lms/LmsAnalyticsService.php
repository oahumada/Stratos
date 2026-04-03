<?php

namespace App\Services\Talent\Lms;

use App\Models\DevelopmentAction;
use App\Models\IntelligenceMetric;
use App\Models\LmsCertificate;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use Illuminate\Support\Facades\Log;

class LmsAnalyticsService
{
    public function trackSyncBatch(
        ?int $organizationId,
        int $processed,
        int $updated,
        bool $success,
        ?string $error = null,
    ): void {
        $completionRatio = $processed > 0
            ? round($updated / $processed, 4)
            : null;

        $this->storeMetric(
            $organizationId,
            'lms_sync_progress',
            $success,
            $processed,
            $completionRatio,
            [
                'processed' => $processed,
                'updated' => $updated,
                'error' => $error,
            ],
        );
    }

    public function trackCertificateIssued(LmsCertificate $certificate): void
    {
        $courseId = $certificate->enrollment?->lms_course_id;

        $this->storeMetric(
            $certificate->organization_id,
            'lms_certificate_issued',
            true,
            1,
            1.0,
            [
                'certificate_id' => $certificate->id,
                'enrollment_id' => $certificate->lms_enrollment_id,
                'course_id' => $courseId,
                'issued_at' => $certificate->issued_at?->toIso8601String(),
            ],
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function getKpisForOrganization(int $organizationId): array
    {
        $courseIds = LmsCourse::query()
            ->where('organization_id', $organizationId)
            ->pluck('id');

        if ($courseIds->isEmpty()) {
            return $this->emptyKpiPayload();
        }

        $totalEnrollments = LmsEnrollment::query()
            ->whereIn('lms_course_id', $courseIds)
            ->count();

        $completedEnrollments = LmsEnrollment::query()
            ->whereIn('lms_course_id', $courseIds)
            ->where(function ($query): void {
                $query->where('status', 'completed')
                    ->orWhere('progress_percentage', '>=', 100);
            })
            ->count();

        $issuedCertificates = LmsCertificate::query()
            ->where('organization_id', $organizationId)
            ->where('is_revoked', false)
            ->count();

        $issuedCertificatesLast7d = LmsCertificate::query()
            ->where('organization_id', $organizationId)
            ->where('is_revoked', false)
            ->where('issued_at', '>=', now()->subDays(7))
            ->count();

        $atRiskData = $this->buildAtRiskData($courseIds->all());
        $atRiskEnrollments = $atRiskData['count'];
        $atRiskLearners = $atRiskData['learners'];

        $enrollmentsByCourse = LmsEnrollment::query()
            ->selectRaw('lms_course_id, COUNT(*) as total_enrollments')
            ->selectRaw("SUM(CASE WHEN status = 'completed' OR progress_percentage >= 100 THEN 1 ELSE 0 END) as completed_enrollments")
            ->whereIn('lms_course_id', $courseIds)
            ->groupBy('lms_course_id')
            ->get()
            ->keyBy('lms_course_id');

        $certificatesByCourse = LmsCertificate::query()
            ->join('lms_enrollments', 'lms_enrollments.id', '=', 'lms_certificates.lms_enrollment_id')
            ->where('lms_certificates.organization_id', $organizationId)
            ->where('lms_certificates.is_revoked', false)
            ->groupBy('lms_enrollments.lms_course_id')
            ->selectRaw('lms_enrollments.lms_course_id as course_id, COUNT(*) as issued_certificates')
            ->pluck('issued_certificates', 'course_id');

        $courses = LmsCourse::query()
            ->whereIn('id', $courseIds)
            ->orderBy('title')
            ->get(['id', 'title']);

        $perCourse = $courses->map(function (LmsCourse $course) use ($enrollmentsByCourse, $certificatesByCourse): array {
            $courseEnrollmentStats = $enrollmentsByCourse->get($course->id);
            $courseTotalEnrollments = (int) ($courseEnrollmentStats->total_enrollments ?? 0);
            $courseCompletedEnrollments = (int) ($courseEnrollmentStats->completed_enrollments ?? 0);
            $courseIssuedCertificates = (int) ($certificatesByCourse->get($course->id, 0));

            $courseCompletionRate = $courseTotalEnrollments > 0
                ? round(($courseCompletedEnrollments / $courseTotalEnrollments) * 100, 2)
                : 0.0;

            $courseCertificationRate = $courseTotalEnrollments > 0
                ? round(($courseIssuedCertificates / $courseTotalEnrollments) * 100, 2)
                : 0.0;

            return [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'total_enrollments' => $courseTotalEnrollments,
                'completed_enrollments' => $courseCompletedEnrollments,
                'completion_rate_pct' => $courseCompletionRate,
                'issued_certificates' => $courseIssuedCertificates,
                'certification_rate_pct' => $courseCertificationRate,
            ];
        })->values()->all();

        $completionRate = $totalEnrollments > 0
            ? round(($completedEnrollments / $totalEnrollments) * 100, 2)
            : 0.0;

        $certificationRate = $totalEnrollments > 0
            ? round(($issuedCertificates / $totalEnrollments) * 100, 2)
            : 0.0;

        $atRiskRate = $totalEnrollments > 0
            ? round(($atRiskEnrollments / $totalEnrollments) * 100, 2)
            : 0.0;

        return [
            'summary' => [
                'total_courses' => $courseIds->count(),
                'total_enrollments' => $totalEnrollments,
                'completed_enrollments' => $completedEnrollments,
                'completion_rate_pct' => $completionRate,
                'issued_certificates' => $issuedCertificates,
                'certification_rate_pct' => $certificationRate,
                'issued_certificates_last_7d' => $issuedCertificatesLast7d,
                'at_risk_enrollments' => $atRiskEnrollments,
                'at_risk_rate_pct' => $atRiskRate,
            ],
            'per_course' => $perCourse,
            'at_risk_learners' => $atRiskLearners,
        ];
    }

    /**
     * @return array{summary:array<string,int|float>,per_course:array<int,array<string,mixed>>,at_risk_learners:array<int,array<string,mixed>>}
     */
    private function emptyKpiPayload(): array
    {
        return [
            'summary' => [
                'total_courses' => 0,
                'total_enrollments' => 0,
                'completed_enrollments' => 0,
                'completion_rate_pct' => 0.0,
                'issued_certificates' => 0,
                'certification_rate_pct' => 0.0,
                'issued_certificates_last_7d' => 0,
                'at_risk_enrollments' => 0,
                'at_risk_rate_pct' => 0.0,
            ],
            'per_course' => [],
            'at_risk_learners' => [],
        ];
    }

    /**
     * @param  array<int,int>  $courseIds
     * @return array{count:int,learners:array<int,array<string,mixed>>}
     */
    private function buildAtRiskData(array $courseIds): array
    {
        $baseQuery = LmsEnrollment::query()
            ->whereIn('lms_course_id', $courseIds)
            ->where('status', '!=', 'completed')
            ->where(function ($query): void {
                $query->where('progress_percentage', '<', 40)
                    ->orWhere(function ($nested): void {
                        $nested->whereNotNull('assessment_score')
                            ->where('assessment_score', '<', 60);
                    })
                    ->orWhereRaw('(resources_total > 0 AND (resources_completed::numeric / resources_total::numeric) < 0.4)');
            });

        $count = (clone $baseQuery)->count();

        $rows = (clone $baseQuery)
            ->with(['course:id,title', 'user:id,name,email'])
            ->orderBy('progress_percentage')
            ->limit(10)
            ->get();

        $developmentActionByEnrollment = DevelopmentAction::query()
            ->whereIn('lms_enrollment_id', $rows->pluck('id')->map(fn ($id) => (string) $id)->all())
            ->pluck('id', 'lms_enrollment_id');

        $learners = $rows->map(function (LmsEnrollment $enrollment) use ($developmentActionByEnrollment): array {
            $resourcesCompleted = (int) ($enrollment->resources_completed ?? 0);
            $resourcesTotal = (int) ($enrollment->resources_total ?? 0);
            $resourceRatio = $resourcesTotal > 0
                ? round(($resourcesCompleted / $resourcesTotal) * 100, 2)
                : null;

            return [
                'enrollment_id' => (int) $enrollment->id,
                'course_id' => (int) $enrollment->lms_course_id,
                'course_title' => (string) ($enrollment->course?->title ?? 'Curso'),
                'user_id' => (int) ($enrollment->user?->id ?? 0),
                'user_name' => (string) ($enrollment->user?->name ?? 'Usuario'),
                'user_email' => $enrollment->user?->email,
                'development_action_id' => $developmentActionByEnrollment->get((string) $enrollment->id) !== null
                    ? (int) $developmentActionByEnrollment->get((string) $enrollment->id)
                    : null,
                'status' => (string) $enrollment->status,
                'progress_percentage' => (float) ($enrollment->progress_percentage ?? 0),
                'assessment_score' => $enrollment->assessment_score !== null ? (float) $enrollment->assessment_score : null,
                'resource_completion_pct' => $resourceRatio,
            ];
        })->values()->all();

        return [
            'count' => $count,
            'learners' => $learners,
        ];
    }

    /**
     * @param  array<string,mixed>  $metadata
     */
    protected function storeMetric(
        ?int $organizationId,
        string $sourceType,
        bool $success,
        int $contextCount,
        ?float $confidence,
        array $metadata,
    ): void {
        try {
            IntelligenceMetric::query()->create([
                'organization_id' => $organizationId,
                'metric_type' => 'agent',
                'source_type' => $sourceType,
                'context_count' => max(0, $contextCount),
                'confidence' => $confidence,
                'duration_ms' => 0,
                'success' => $success,
                'metadata' => $metadata,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Unable to store LMS analytics metric', [
                'source_type' => $sourceType,
                'organization_id' => $organizationId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
