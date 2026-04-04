<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsScormPackage;
use App\Services\Lms\ScormPlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScormPlayerController extends Controller
{
    public function __construct(
        protected ScormPlayerService $service,
    ) {}

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:zip|max:524288',
            'title' => 'nullable|string|max:255',
            'lms_course_id' => 'nullable|integer|exists:lms_courses,id',
            'lms_lesson_id' => 'nullable|integer|exists:lms_lessons,id',
        ]);

        $orgId = $this->resolveOrganizationId($request);

        $package = $this->service->uploadPackage(
            $request->file('file'),
            $orgId,
            $request->input('lms_course_id'),
            $request->input('lms_lesson_id'),
        );

        // Override title if provided
        if ($request->filled('title')) {
            $package->update(['title' => $request->input('title')]);
            $package->refresh();
        }

        return response()->json(['success' => true, 'data' => $package], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $packages = LmsScormPackage::forOrganization($orgId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $packages]);
    }

    public function launch(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $data = $this->service->getLaunchData($id, $userId, $orgId);

        return response()->json(['data' => $data]);
    }

    public function saveCmi(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $request->validate([
            'cmi_data' => 'required|array',
        ]);

        $tracking = $this->service->getOrCreateTracking($id, $userId, $orgId);
        $tracking = $this->service->saveCmiData($tracking->id, $request->input('cmi_data'), $orgId);

        return response()->json(['success' => true, 'data' => $tracking]);
    }

    public function tracking(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);
        $userId = $request->user()->id;

        $tracking = $this->service->getOrCreateTracking($id, $userId, $orgId);

        return response()->json(['data' => $tracking]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $orgId = $this->resolveOrganizationId($request);

        $this->service->deletePackage($id, $orgId);

        return response()->json(['success' => true]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
