<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\LmsCoursePolicyRequest;
use App\Models\LmsCourse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id para policy LMS.';

    public function show(Request $request, LmsCourse $course): JsonResponse
    {
        $scopedCourse = $this->findCourseForCurrentOrganization($request, $course->id);

        if ($scopedCourse instanceof JsonResponse) {
            return $scopedCourse;
        }

        // Authorization handled via route middleware (`permission:lms.courses.view`)
        return response()->json($scopedCourse->only([
            'id', 'title', 'cert_min_resource_completion_ratio', 'cert_require_assessment_score', 'cert_min_assessment_score',
        ]));
    }

    public function update(LmsCoursePolicyRequest $request, LmsCourse $course): JsonResponse
    {
        $scopedCourse = $this->findCourseForCurrentOrganization($request, $course->id);

        if ($scopedCourse instanceof JsonResponse) {
            return $scopedCourse;
        }

        // Authorization handled via route middleware (`permission:lms.courses.manage`)
        $scopedCourse->fill($request->validated());
        $scopedCourse->save();

        return response()->json(['success' => true, 'course' => $scopedCourse->only([
            'id', 'title', 'cert_min_resource_completion_ratio', 'cert_require_assessment_score', 'cert_min_assessment_score',
        ])]);
    }

    private function findCourseForCurrentOrganization(Request $request, int|string $courseId): LmsCourse|JsonResponse
    {
        $organizationId = (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);

        if ($organizationId <= 0) {
            return response()->json([
                'message' => self::ORG_RESOLUTION_ERROR,
            ], 422);
        }

        return LmsCourse::query()
            ->where('organization_id', $organizationId)
            ->findOrFail($courseId);
    }
}
