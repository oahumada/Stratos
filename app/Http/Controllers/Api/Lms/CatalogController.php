<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\CourseCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected CourseCatalogService $catalogService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $filters = $request->only([
            'search', 'category', 'level', 'tags', 'duration_min',
            'duration_max', 'featured', 'enrollment_type',
        ]);
        $sort = $request->input('sort', 'newest');
        $perPage = (int) $request->input('per_page', 12);

        $results = $this->catalogService->search($organizationId, $filters, $sort, $perPage);

        return response()->json($results);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $data = $this->catalogService->getCourseDetail($id, $organizationId);

        return response()->json($data);
    }

    public function rate(Request $request, int $id): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);

        $rating = $this->catalogService->rateCourse(
            $id,
            $request->user()->id,
            $organizationId,
            (int) $request->input('rating'),
            $request->input('review'),
        );

        return response()->json(['success' => true, 'rating' => $rating]);
    }

    public function enroll(Request $request, int $id): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        try {
            $enrollment = $this->catalogService->selfEnroll(
                $id,
                $request->user()->id,
                $organizationId,
            );

            return response()->json(['success' => true, 'enrollment' => $enrollment], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function recommendations(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $limit = (int) $request->input('limit', 6);
        $data = $this->catalogService->getRecommendations(
            $request->user()->id,
            $organizationId,
            $limit,
        );

        return response()->json($data);
    }

    public function categories(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $data = $this->catalogService->getCategories($organizationId);

        return response()->json(['categories' => $data]);
    }

    public function tags(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $data = $this->catalogService->getTags($organizationId);

        return response()->json(['tags' => $data]);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
