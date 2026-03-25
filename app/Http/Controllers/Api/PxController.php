<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PxController extends Controller
{
    protected PxService $pxService;

    public function __construct(PxService $pxService)
    {
        $this->pxService = $pxService;
    }

    /**
     * List current PX campaigns for the organization.
     */
    public function index(): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $campaigns = $this->pxService->getActiveCampaigns($orgId);

        return response()->json([
            'status' => 'success',
            'data' => $campaigns,
        ]);
    }

    /**
     * Manually trigger a campaign for testing purposes.
     */
    public function trigger(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => 'required|string',
        ]);

        $orgId = auth()->user()->organization_id;
        $campaign = $this->pxService->triggerEventCampaign($orgId, $validated['event_type']);

        if (! $campaign) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to trigger campaign. Event type may be invalid.',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Campaign '{$campaign->name}' triggered successfully.",
            'data' => $campaign,
        ]);
    }
}
