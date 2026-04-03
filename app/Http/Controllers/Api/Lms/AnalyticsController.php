<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Services\Talent\Lms\LmsAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        protected LmsAnalyticsService $lmsAnalyticsService,
    ) {}

    public function overview(Request $request): JsonResponse
    {
        $organizationId = (int) ($request->user()?->organization_id ?? 0);

        if ($organizationId <= 0) {
            return response()->json([
                'message' => 'No se pudo resolver organization_id para analytics LMS.',
            ], 422);
        }

        $kpis = $this->lmsAnalyticsService->getKpisForOrganization($organizationId);

        return response()->json([
            'data' => $kpis,
        ]);
    }
}
