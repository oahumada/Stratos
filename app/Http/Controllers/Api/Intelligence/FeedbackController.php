<?php

namespace App\Http\Controllers\Api\Intelligence;

use App\Http\Controllers\Controller;
use App\Models\ImprovementFeedback;
use App\Services\Intelligence\FeedbackLoopService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct(
        protected FeedbackLoopService $feedbackService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_text' => 'nullable|string|max:5000',
            'tags' => 'nullable|array',
            'tags.*' => 'string|in:hallucination,irrelevant,incomplete,excellent,biased,slow',
            'agent_id' => 'nullable|string|max:255',
            'evaluation_id' => 'nullable|integer|exists:llm_evaluations,id',
            'intelligence_metric_id' => 'nullable|integer|exists:intelligence_metrics,id',
            'context' => 'nullable|array',
        ]);

        $validated['user_id'] = $request->user()->id;

        $feedback = $this->feedbackService->submitFeedback(
            $validated,
            $request->user()->organization_id
        );

        return response()->json(['data' => $feedback], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'agent_id' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'status' => 'nullable|string|in:pending,processed,applied',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = ImprovementFeedback::where('organization_id', $request->user()->organization_id);

        if ($request->filled('agent_id')) {
            $query->byAgent($request->input('agent_id'));
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->input('rating'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->input('to'));
        }

        $paginated = $query->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json($paginated);
    }

    public function summary(Request $request): JsonResponse
    {
        $summary = $this->feedbackService->getFeedbackSummary(
            $request->user()->organization_id
        );

        return response()->json(['data' => $summary]);
    }

    public function patterns(Request $request): JsonResponse
    {
        $request->validate([
            'agent_id' => 'nullable|string',
            'days' => 'nullable|integer|min:1|max:365',
        ]);

        $patterns = $this->feedbackService->getPatterns(
            $request->user()->organization_id,
            $request->input('agent_id'),
            $request->input('days', 30)
        );

        return response()->json(['data' => $patterns]);
    }
}
