<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\LmsCoursePolicyRequest;
use App\Models\LmsCertificateTemplate;
use App\Models\LmsCourse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id para policy LMS.';

    private const TEMPLATE_SCOPE_ERROR = 'La plantilla de certificado no pertenece a la organización actual.';

    public function show(Request $request, LmsCourse $course): JsonResponse
    {
        $scopedCourse = $this->findCourseForCurrentOrganization($request, $course->id);

        if ($scopedCourse instanceof JsonResponse) {
            return $scopedCourse;
        }

        // Authorization handled via route middleware (`permission:lms.courses.view`)
        return response()->json($scopedCourse->only([
            'id', 'title', 'cert_min_resource_completion_ratio', 'cert_require_assessment_score', 'cert_min_assessment_score', 'cert_template_id',
        ]));
    }

    public function templates(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json([
                'message' => self::ORG_RESOLUTION_ERROR,
            ], 422);
        }

        $templates = LmsCertificateTemplate::query()
            ->where('is_active', true)
            ->where(function ($query) use ($organizationId): void {
                $query->where('organization_id', $organizationId)
                    ->orWhereNull('organization_id');
            })
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'description',
                'is_default',
                'organization_id',
            ]);

        return response()->json([
            'templates' => $templates,
        ]);
    }

    public function update(LmsCoursePolicyRequest $request, LmsCourse $course): JsonResponse
    {
        $scopedCourse = $this->findCourseForCurrentOrganization($request, $course->id);

        if ($scopedCourse instanceof JsonResponse) {
            return $scopedCourse;
        }

        $validated = $request->validated();
        $organizationId = $this->resolveOrganizationId($request);

        if (
            array_key_exists('cert_template_id', $validated)
            && $validated['cert_template_id'] !== null
            && ! $this->templateBelongsToOrganization((int) $validated['cert_template_id'], $organizationId)
        ) {
            return response()->json([
                'message' => self::TEMPLATE_SCOPE_ERROR,
            ], 422);
        }

        // Authorization handled via route middleware (`permission:lms.courses.manage`)
        $scopedCourse->fill($validated);
        $scopedCourse->save();

        return response()->json(['success' => true, 'course' => $scopedCourse->only([
            'id', 'title', 'cert_min_resource_completion_ratio', 'cert_require_assessment_score', 'cert_min_assessment_score', 'cert_template_id',
        ])]);
    }

    private function findCourseForCurrentOrganization(Request $request, int|string $courseId): LmsCourse|JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json([
                'message' => self::ORG_RESOLUTION_ERROR,
            ], 422);
        }

        return LmsCourse::query()
            ->where('organization_id', $organizationId)
            ->findOrFail($courseId);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }

    private function templateBelongsToOrganization(int $templateId, int $organizationId): bool
    {
        return LmsCertificateTemplate::query()
            ->whereKey($templateId)
            ->where(function ($query) use ($organizationId): void {
                $query->where('organization_id', $organizationId)
                    ->orWhereNull('organization_id');
            })
            ->exists();
    }
}
