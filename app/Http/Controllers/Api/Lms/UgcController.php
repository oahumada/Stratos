<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsUserContent;
use App\Services\Lms\UgcService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UgcController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected UgcService $ugcService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $filters = $request->only(['title', 'content_type', 'course_id', 'tags']);
        $perPage = (int) $request->input('per_page', 20);

        $content = $this->ugcService->listPublished($organizationId, $filters, $perPage);

        return response()->json($content);
    }

    public function pendingReview(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $content = $this->ugcService->listPendingReview($organizationId);

        return response()->json($content);
    }

    public function store(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'nullable|string|in:article,video_link,resource,tip,tutorial',
            'content_body' => 'nullable|string',
            'content_url' => 'nullable|string|max:2048',
            'course_id' => 'nullable|integer|exists:lms_courses,id',
            'tags' => 'nullable|array',
        ]);

        $content = $this->ugcService->create(
            $organizationId,
            $request->user()->id,
            $request->only(['title', 'description', 'content_type', 'content_body', 'content_url', 'course_id', 'tags']),
        );

        return response()->json($content, 201);
    }

    public function update(LmsUserContent $userContent, Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'nullable|string|in:article,video_link,resource,tip,tutorial',
            'content_body' => 'nullable|string',
            'content_url' => 'nullable|string|max:2048',
            'course_id' => 'nullable|integer|exists:lms_courses,id',
            'tags' => 'nullable|array',
        ]);

        $userContent->update($request->only([
            'title', 'description', 'content_type', 'content_body', 'content_url', 'course_id', 'tags',
        ]));

        return response()->json($userContent->fresh());
    }

    public function submitForReview(LmsUserContent $userContent): JsonResponse
    {
        $content = $this->ugcService->submitForReview($userContent->id);

        return response()->json($content);
    }

    public function approve(LmsUserContent $userContent, Request $request): JsonResponse
    {
        $content = $this->ugcService->approve($userContent->id, $request->user()->id);

        return response()->json($content);
    }

    public function reject(LmsUserContent $userContent, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string',
        ]);

        $content = $this->ugcService->reject(
            $userContent->id,
            $request->user()->id,
            $request->input('reason'),
        );

        return response()->json($content);
    }

    public function like(LmsUserContent $userContent, Request $request): JsonResponse
    {
        $result = $this->ugcService->toggleLike($userContent->id, $request->user()->id);

        return response()->json($result);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
