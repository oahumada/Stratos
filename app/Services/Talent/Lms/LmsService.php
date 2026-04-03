<?php

namespace App\Services\Talent\Lms;

use App\Events\CertificateIssued;
use App\Models\DevelopmentAction;
use App\Models\LmsCertificate;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Notifications\LmsCourseCompletedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class LmsService
{
    protected $providers = [];

    public function __construct(
        protected CertificateService $certificateService,
        MockLmsProvider $mockProvider,
        StratosInternalProvider $internalProvider,
        MoodleProvider $moodleProvider,
        LinkedInLearningProvider $linkedinProvider,
        UdemyBusinessProvider $udemyProvider
    ) {
        $this->providers['mock'] = $mockProvider;
        $this->providers['internal'] = $internalProvider;
        $this->providers['moodle'] = $moodleProvider;
        $this->providers['linkedin'] = $linkedinProvider;
        $this->providers['udemy'] = $udemyProvider;
    }

    /**
     * Obtiene el proveedor adecuado.
     */
    protected function getProvider(string $name): LmsProviderInterface
    {
        return $this->providers[$name] ?? $this->providers['mock'];
    }

    /**
     * Lanza una acción de desarrollo en el LMS.
     */
    public function launchAction(DevelopmentAction $action): string
    {
        if (! $action->lms_course_id) {
            throw new \InvalidArgumentException('Esta acción no está vinculada a un curso de LMS.');
        }

        $provider = $this->getProvider($action->lms_provider ?? 'mock');
        $userId = (string) auth()->id();

        // Si no está inscrito, inscribir ahora
        if (! $action->lms_enrollment_id) {
            $enrollmentId = $provider->enrollUser($action->lms_course_id, $userId);
            $action->update(['lms_enrollment_id' => $enrollmentId]);
        }

        return $provider->getLaunchUrl($action->lms_course_id, $userId);
    }

    /**
     * Sincroniza el progreso de una acción desde el LMS.
     */
    public function syncProgress(DevelopmentAction $action): bool
    {
        if (! $action->lms_enrollment_id) {
            return false;
        }

        $provider = $this->getProvider($action->lms_provider ?? 'mock');

        try {
            $isCompleted = $provider->isCompleted($action->lms_enrollment_id);

            if ($isCompleted && $action->status !== 'completed') {
                $action->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                $this->issueCertificateOnCompletion($action);

                // 🏅 GAMIFICATION: Award points to the user
                $course = LmsCourse::find($action->lms_course_id);
                if ($course) {
                    $xp = $course->xp_points > 0 ? $course->xp_points : 50; // default 50
                    $userId = $action->path?->people?->user_id;

                    if ($userId) {
                        $gamification = \App\Models\UserGamification::firstOrCreate(
                            ['user_id' => $userId],
                            ['total_xp' => 0, 'level' => 1, 'current_points' => 0]
                        );
                        $gamification->addExperience($xp);

                        Log::info("User {$userId} awarded {$xp} XP for completing course {$course->title}");
                    }
                }

                $recipientUser = $action->path?->people?->user;
                if ($recipientUser && ! app()->runningUnitTests()) {
                    Notification::send($recipientUser, new LmsCourseCompletedNotification($action->fresh(), $course));
                }

                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error syncing LMS progress: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Sincroniza acciones LMS pendientes por lote.
     *
     * @return array{processed:int,updated:int}
     */
    public function syncPendingActions(?int $organizationId = null, ?int $actionId = null): array
    {
        $query = DevelopmentAction::query()
            ->whereNotNull('lms_enrollment_id')
            ->where('status', '!=', 'completed')
            ->with('path');

        if ($actionId !== null) {
            $query->whereKey($actionId);
        }

        if ($organizationId !== null) {
            $query->whereHas('path', function (Builder $builder) use ($organizationId): void {
                $builder->where('organization_id', $organizationId);
            });
        }

        $processed = 0;
        $updated = 0;

        $query->chunkById(100, function ($actions) use (&$processed, &$updated): void {
            foreach ($actions as $action) {
                $processed++;

                if ($this->syncProgress($action)) {
                    $updated++;
                }
            }
        });

        return [
            'processed' => $processed,
            'updated' => $updated,
        ];
    }

    /**
     * Busca cursos en el proveedor por defecto.
     */
    public function searchCourses(string $query): array
    {
        $allResults = [];

        foreach ($this->providers as $provider) {
            $allResults = array_merge($allResults, $provider->searchCourses($query));
        }

        return $allResults;
    }

    protected function issueCertificateOnCompletion(DevelopmentAction $action): ?LmsCertificate
    {
        $path = $action->path;

        if (! $path || ! $path->organization_id || ! $path->people_id) {
            return null;
        }

        [$eligible, $metrics] = $this->canIssueCompletionCredentials($action);

        if (! $eligible) {
            Log::info('LMS completion does not meet certificate/diploma criteria', [
                'development_action_id' => $action->id,
                'organization_id' => $path->organization_id,
                'metrics' => $metrics,
            ]);

            return null;
        }

        $existingCertificate = LmsCertificate::query()
            ->where('lms_enrollment_id', $action->lms_enrollment_id)
            ->first();

        if ($existingCertificate) {
            return $existingCertificate;
        }

        $courseTemplateId = LmsCourse::query()
            ->whereKey($action->lms_course_id)
            ->where('organization_id', $path->organization_id)
            ->value('cert_template_id');

        $certificate = $this->certificateService->issue([
            'organization_id' => $path->organization_id,
            'person_id' => $path->people_id,
            'lms_enrollment_id' => $action->lms_enrollment_id,
            'certificate_template_id' => $courseTemplateId,
            'meta' => [
                'development_action_id' => $action->id,
                'lms_course_id' => $action->lms_course_id,
                'source' => 'lms_sync_progress',
                'completion_metrics' => $metrics,
            ],
        ]);

        Event::dispatch(new CertificateIssued($certificate));

        Log::info('LMS certificate auto-issued on completion sync', [
            'development_action_id' => $action->id,
            'certificate_id' => $certificate->id,
            'organization_id' => $path->organization_id,
        ]);

        return $certificate;
    }

    /**
     * Determina si una acción completada cumple reglas configurables para emitir certificado/diploma.
     *
     * @return array{0:bool,1:array<string,mixed>}
     */
    protected function canIssueCompletionCredentials(DevelopmentAction $action): array
    {
        $policy = config('stratos.lms.certificate_issuance', []);
        $coursePolicy = $this->resolveCoursePolicyOverrides($action, $policy);

        $minResourceCompletionRatio = (float) ($coursePolicy['min_resource_completion_ratio'] ?? 0.70);
        $requireAssessmentScore = (bool) ($coursePolicy['require_assessment_score'] ?? true);
        $minAssessmentScore = (float) ($coursePolicy['min_assessment_score'] ?? 80);

        $enrollment = LmsEnrollment::query()->find($action->lms_enrollment_id);

        $progressPercentage = $enrollment?->progress_percentage;
        $resourcesCompleted = $enrollment?->resources_completed;
        $resourcesTotal = $enrollment?->resources_total;
        $assessmentScore = $enrollment?->assessment_score;

        if ($resourcesTotal !== null && $resourcesTotal > 0) {
            $resourceCompletionRatio = ((int) $resourcesCompleted) / ((int) $resourcesTotal);
        } else {
            $resourceCompletionRatio = ((float) ($progressPercentage ?? 0)) / 100;
        }

        $meetsResourceRequirement = $resourceCompletionRatio >= $minResourceCompletionRatio;
        $meetsAssessmentRequirement = ! $requireAssessmentScore
            || ($assessmentScore !== null && (float) $assessmentScore >= $minAssessmentScore);

        $metrics = [
            'resource_completion_ratio' => round($resourceCompletionRatio, 4),
            'resources_completed' => $resourcesCompleted,
            'resources_total' => $resourcesTotal,
            'progress_percentage' => $progressPercentage,
            'assessment_score' => $assessmentScore,
            'policy' => [
                'min_resource_completion_ratio' => $minResourceCompletionRatio,
                'require_assessment_score' => $requireAssessmentScore,
                'min_assessment_score' => $minAssessmentScore,
            ],
            'meets_resource_requirement' => $meetsResourceRequirement,
            'meets_assessment_requirement' => $meetsAssessmentRequirement,
        ];

        return [
            $meetsResourceRequirement && $meetsAssessmentRequirement,
            $metrics,
        ];
    }

    /**
     * Resuelve overrides de política para el curso de la acción (scoped por organization_id del curso).
     *
     * @param  array<string,mixed>  $defaultPolicy
     * @return array<string,mixed>
     */
    protected function resolveCoursePolicyOverrides(DevelopmentAction $action, array $defaultPolicy): array
    {
        $course = LmsCourse::query()
            ->whereKey($action->lms_course_id)
            ->first();

        if (! $course) {
            return $defaultPolicy;
        }

        return [
            'min_resource_completion_ratio' => $course->cert_min_resource_completion_ratio
                ?? ($defaultPolicy['min_resource_completion_ratio'] ?? 0.70),
            'require_assessment_score' => $course->cert_require_assessment_score
                ?? ($defaultPolicy['require_assessment_score'] ?? true),
            'min_assessment_score' => $course->cert_min_assessment_score
                ?? ($defaultPolicy['min_assessment_score'] ?? 80),
        ];
    }
}
