<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DevelopmentAction;
use App\Services\Talent\Lms\LmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DevelopmentActionController extends Controller
{
    protected $lmsService;

    public function __construct(LmsService $lmsService)
    {
        $this->lmsService = $lmsService;
    }
    /**
     * Actualiza el estado de una acción de desarrollo.
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $action = DevelopmentAction::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,completed,cancelled'],
        ]);

        $action->status = $data['status'];

        if ($data['status'] === 'in_progress' && !$action->started_at) {
            $action->started_at = now();
        }

        if ($data['status'] === 'completed') {
            $action->completed_at = now();
        }

        $action->save();

        // Podríamos disparar un evento aquí para recalcular el progreso del Path
        // $this->updatePathProgress($action->development_path_id);

        return response()->json([
            'message' => 'Estado de la acción actualizado',
            'data' => $action
        ]);
    }
    
    /**
     * Lanza el curso asociado en el LMS.
     */
    public function launchLms($id): JsonResponse
    {
        try {
            $action = DevelopmentAction::findOrFail($id);
            $url = $this->lmsService->launchAction($action);

            return response()->json([
                'url' => $url,
                'data' => $action->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Sincroniza el progreso desde el LMS.
     */
    public function syncLms($id): JsonResponse
    {
        $action = DevelopmentAction::findOrFail($id);
        $updated = $this->lmsService->syncProgress($action);

        return response()->json([
            'updated' => $updated,
            'data' => $action->fresh()
        ]);
    }
}
