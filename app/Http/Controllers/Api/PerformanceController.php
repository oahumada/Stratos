<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceCycle;
use App\Models\PerformanceReview;
use App\Services\PerformanceAIService;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    public function __construct(
        private PerformanceAIService $aiService,
    ) {}

    // ── Cycles ──

    public function indexCycles(Request $request)
    {
        $orgId = $request->user()->organization_id;

        $cycles = PerformanceCycle::forOrganization($orgId)
            ->withCount('reviews')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $cycles]);
    }

    public function storeCycle(Request $request)
    {
        $orgId = $request->user()->organization_id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'period' => 'required|string|max:50',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
        ]);

        $cycle = PerformanceCycle::create([
            ...$validated,
            'organization_id' => $orgId,
            'created_by' => $request->user()->id,
            'status' => 'draft',
        ]);

        return response()->json(['data' => $cycle], 201);
    }

    public function showCycle(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;

        $cycle = PerformanceCycle::forOrganization($orgId)
            ->withCount('reviews')
            ->findOrFail($id);

        return response()->json(['data' => $cycle]);
    }

    public function activateCycle(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;

        $cycle = PerformanceCycle::forOrganization($orgId)->findOrFail($id);
        $cycle->update(['status' => 'active']);

        return response()->json(['data' => $cycle]);
    }

    public function closeCycle(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;

        $cycle = PerformanceCycle::forOrganization($orgId)->findOrFail($id);
        $cycle->update(['status' => 'closed']);

        return response()->json(['data' => $cycle]);
    }

    // ── Reviews ──

    public function indexReviews(Request $request, int $cycleId)
    {
        $orgId = $request->user()->organization_id;

        PerformanceCycle::forOrganization($orgId)->findOrFail($cycleId);

        $reviews = PerformanceReview::forOrganization($orgId)
            ->where('cycle_id', $cycleId)
            ->with(['person', 'reviewer'])
            ->get();

        return response()->json(['data' => $reviews]);
    }

    public function storeReview(Request $request, int $cycleId)
    {
        $orgId = $request->user()->organization_id;

        PerformanceCycle::forOrganization($orgId)->findOrFail($cycleId);

        $validated = $request->validate([
            'people_id' => 'required|integer|exists:people,id',
            'self_score' => 'nullable|numeric|min:0|max:100',
            'manager_score' => 'nullable|numeric|min:0|max:100',
            'peer_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $review = new PerformanceReview([
            ...$validated,
            'organization_id' => $orgId,
            'cycle_id' => $cycleId,
            'reviewer_id' => $request->user()->id,
        ]);

        $review->final_score = $this->aiService->computeFinalScore($review);
        $review->save();

        return response()->json(['data' => $review], 201);
    }

    public function updateReview(Request $request, int $cycleId, int $reviewId)
    {
        $orgId = $request->user()->organization_id;

        PerformanceCycle::forOrganization($orgId)->findOrFail($cycleId);

        $review = PerformanceReview::forOrganization($orgId)
            ->where('cycle_id', $cycleId)
            ->findOrFail($reviewId);

        $validated = $request->validate([
            'self_score' => 'nullable|numeric|min:0|max:100',
            'manager_score' => 'nullable|numeric|min:0|max:100',
            'peer_score' => 'nullable|numeric|min:0|max:100',
            'status' => 'nullable|in:pending,in_progress,completed,calibrated',
        ]);

        $review->fill($validated);
        $review->final_score = $this->aiService->computeFinalScore($review);
        $review->save();

        return response()->json(['data' => $review]);
    }

    public function calibrateCycle(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;

        $cycle = PerformanceCycle::forOrganization($orgId)->findOrFail($id);
        $result = $this->aiService->calibrateCycle($cycle);

        return response()->json(['data' => $result]);
    }

    public function insights(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;

        $cycle = PerformanceCycle::forOrganization($orgId)->findOrFail($id);
        $result = $this->aiService->generateInsights($cycle);

        return response()->json(['data' => $result]);
    }
}
