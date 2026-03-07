<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Intelligence\SmartAlertService;
use Illuminate\Http\JsonResponse;

class SmartAlertController extends Controller
{
    protected $alertService;

    public function __construct(SmartAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Get unread alerts for the current organization.
     */
    public function index(): JsonResponse
    {
        $orgId = auth()->user()->organization_id;
        $alerts = $this->alertService->getActiveAlerts($orgId);

        return response()->json([
            'success' => true,
            'data' => $alerts,
        ]);
    }

    /**
     * Mark an alert as read.
     */
    public function markAsRead($id): JsonResponse
    {
        \Illuminate\Support\Facades\DB::table('smart_alerts')
            ->where('id', $id)
            ->where('organization_id', auth()->user()->organization_id)
            ->update(['is_read' => true, 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }
}
