<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Lms\DiscussionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected DiscussionService $discussionService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $filters = $request->only(['course_id', 'lesson_id', 'root_only']);
        $perPage = (int) $request->input('per_page', 20);

        $discussions = $this->discussionService->getDiscussions($organizationId, $filters, $perPage);

        return response()->json($discussions);
    }

    public function store(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $request->validate([
            'body' => 'required|string',
            'title' => 'nullable|string|max:255',
            'course_id' => 'nullable|integer|exists:lms_courses,id',
            'lesson_id' => 'nullable|integer|exists:lms_lessons,id',
        ]);

        $discussion = $this->discussionService->createPost(
            $request->only(['body', 'title', 'course_id', 'lesson_id']),
            $request->user()->id,
            $organizationId,
        );

        return response()->json($discussion, 201);
    }

    public function reply(Request $request, int $id): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $request->validate([
            'body' => 'required|string',
        ]);

        $reply = $this->discussionService->reply(
            $id,
            $request->input('body'),
            $request->user()->id,
            $organizationId,
        );

        return response()->json($reply, 201);
    }

    public function like(Request $request, int $id): JsonResponse
    {
        $result = $this->discussionService->toggleLike($id, $request->user()->id);

        return response()->json($result);
    }

    public function pin(Request $request, int $id): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $discussion = $this->discussionService->pinPost($id, $organizationId);

        return response()->json($discussion);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $this->discussionService->deletePost($id, $request->user()->id, $organizationId);

        return response()->json(['message' => 'Post deleted.']);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
