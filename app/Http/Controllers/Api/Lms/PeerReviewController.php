<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsPeerReview;
use App\Services\Lms\PeerReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeerReviewController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function __construct(
        protected PeerReviewService $peerReviewService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $role = $request->input('role', 'reviewee');
        $assignments = $this->peerReviewService->getAssignmentsForUser(
            $request->user()->id,
            $organizationId,
            $role,
        );

        return response()->json($assignments);
    }

    public function store(Request $request): JsonResponse
    {
        $organizationId = $this->resolveOrganizationId($request);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $request->validate([
            'lesson_id' => 'required|integer|exists:lms_lessons,id',
            'reviewer_ids' => 'required|array|min:1',
            'reviewer_ids.*' => 'integer|exists:users,id',
            'reviewee_ids' => 'required|array|min:1',
            'reviewee_ids.*' => 'integer|exists:users,id',
        ]);

        $reviews = $this->peerReviewService->createAssignment(
            $organizationId,
            $request->input('lesson_id'),
            $request->input('reviewer_ids'),
            $request->input('reviewee_ids'),
        );

        return response()->json($reviews, 201);
    }

    public function submitWork(LmsPeerReview $peerReview, Request $request): JsonResponse
    {
        $request->validate([
            'submission_url' => 'nullable|string|max:2048',
            'submission_text' => 'nullable|string',
        ]);

        $review = $this->peerReviewService->submitWork(
            $peerReview->id,
            $request->input('submission_url'),
            $request->input('submission_text'),
        );

        return response()->json($review);
    }

    public function submitReview(LmsPeerReview $peerReview, Request $request): JsonResponse
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'required|string',
            'rubric_scores' => 'nullable|array',
            'rubric_scores.*.criterion' => 'required_with:rubric_scores|string',
            'rubric_scores.*.score' => 'required_with:rubric_scores|integer|min:0',
            'rubric_scores.*.max' => 'required_with:rubric_scores|integer|min:1',
            'rubric_scores.*.comment' => 'nullable|string',
        ]);

        $review = $this->peerReviewService->submitReview(
            $peerReview->id,
            (float) $request->input('score'),
            $request->input('feedback'),
            $request->input('rubric_scores'),
        );

        return response()->json($review);
    }

    public function scores(int $lesson): JsonResponse
    {
        // Resolve org from the authenticated user
        $organizationId = $this->resolveOrganizationId(request());

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $scores = $this->peerReviewService->getAggregatedScores($lesson, $organizationId);

        return response()->json($scores);
    }

    private function resolveOrganizationId(Request $request): int
    {
        return (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);
    }
}
