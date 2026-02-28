<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TalentRoiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestorDashboardController extends Controller
{
    protected TalentRoiService $roiService;

    public function __construct(TalentRoiService $roiService)
    {
        $this->roiService = $roiService;
    }

    /**
     * Executive Dashboard Data
     */
    public function index(Request $request): JsonResponse
    {
        // Add middleware authorization check here if needed (e.g. role:admin,hr_leader)
        
        $summary = $this->roiService->getExecutiveSummary();
        $distributions = $this->roiService->getDistributionData();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'charts' => $distributions,
                'forecast' => [
                    'next_quarter_readiness' => round($summary['org_readiness'] * 1.05, 1), // Simple forecast
                    'projected_savings_usd' => round($summary['talent_roi_usd'] * 1.2, 2),
                ],
                'timestamp' => now()->toIso8601String(),
            ]
        ]);
    }
}
