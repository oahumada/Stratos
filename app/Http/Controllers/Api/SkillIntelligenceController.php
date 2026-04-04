<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SkillIntelligenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillIntelligenceController extends Controller
{
    public function __construct(
        private SkillIntelligenceService $service
    ) {}

    /**
     * GET /api/skill-intelligence/heatmap
     * Department × Skill gap heatmap matrix.
     */
    public function heatmap(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $category = $request->query('category');

        return response()->json(
            $this->service->departmentHeatmap($orgId, $category)
        );
    }

    /**
     * GET /api/skill-intelligence/top-gaps
     * Top N skills with largest aggregate gaps.
     */
    public function topGaps(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $limit = (int) $request->query('limit', 10);
        $limit = min(max($limit, 1), 50);

        return response()->json(
            $this->service->topGaps($orgId, $limit)
        );
    }

    /**
     * GET /api/skill-intelligence/upskilling
     * Upskilling recommendations per skill gap.
     */
    public function upskilling(Request $request): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $limit = (int) $request->query('limit', 8);
        $limit = min(max($limit, 1), 20);

        return response()->json(
            $this->service->upskillingRecommendations($orgId, $limit)
        );
    }

    /**
     * GET /api/skill-intelligence/coverage
     * Skill coverage % summary for the org.
     */
    public function coverage(): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        return response()->json(
            $this->service->coverageSummary($orgId)
        );
    }
}
