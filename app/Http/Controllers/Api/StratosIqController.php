<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organizations;
use App\Services\Talent\StratosIqService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StratosIqController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected StratosIqService $stratosIqService
    ) {}

    /**
     * GET /api/stratos-iq/{organizationId}
     * Returns the intelligence trends and current snapshot.
     */
    public function getTrends(int $organizationId): JsonResponse
    {
        try {
            $org = Organizations::findOrFail($organizationId);
            $trends = $this->stratosIqService->getTrends($org);

            return $this->success($trends, 'Stratos IQ trends retrieved successfully.');
        } catch (\Exception $e) {
            return $this->error('Error retrieving Stratos IQ: '.$e->getMessage(), 500);
        }
    }

    /**
     * POST /api/stratos-iq/{organizationId}/snapshot
     * Manually triggers a snapshot capture for the current month.
     */
    public function captureSnapshot(int $organizationId, Request $request): JsonResponse
    {
        try {
            $org = Organizations::findOrFail($organizationId);
            $metadata = $request->input('metadata', []);

            $snapshot = $this->stratosIqService->captureSnapshot($org, $metadata);

            return $this->success($snapshot, 'Snapshot captured successfully.');
        } catch (\Exception $e) {
            return $this->error('Error capturing snapshot: '.$e->getMessage(), 500);
        }
    }
}
