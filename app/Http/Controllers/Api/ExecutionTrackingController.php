<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExecutionTrackingController extends Controller
{
    /**
     * Get all mobility change sets for tracking.
     */
    public function index(Request $request)
    {
        $orgId = $request->user()->organization_id;

        $changeSets = \App\Models\ChangeSet::where('organization_id', $orgId)
            ->whereNotNull('scenario_id')
            ->with(['scenario', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $tracking = $changeSets->map(function ($cs) {
            $devPlans = \App\Models\DevelopmentPath::where('organization_id', $cs->organization_id)
                ->where('metadata->source', 'mobility_simulation')
                // Ideally link to scenario or person explicitly in a real DB,
                // but following current pattern
                ->with(['people', 'actions'])
                ->get();

            return [
                'id' => $cs->id,
                'title' => $cs->title,
                'status' => $cs->status,
                'created_at' => $cs->created_at->toDateTimeString(),
                'scenario_name' => $cs->scenario->name ?? 'N/A',
                'creator' => $cs->creator->name ?? 'Sistema',
                'projected_roi' => $cs->metadata['projected_roi'] ?? 0,
                'development_progress' => $devPlans->map(function ($dp) {
                    $total = $dp->actions->count();
                    $completed = $dp->actions->where('status', 'completed')->count();

                    return [
                        'id' => $dp->id,
                        'person_name' => $dp->people->full_name ?? 'N/A',
                        'progress' => $total > 0 ? round(($completed / $total) * 100) : 0,
                        'status' => $dp->status,
                    ];
                }),
            ];
        });

        return response()->json($tracking);
    }

    /**
     * Get detail of a specific plan/changeset.
     */
    public function show(int $id)
    {
        $cs = \App\Models\ChangeSet::with(['scenario', 'creator'])->findOrFail($id);

        // Find development paths created related to this simulation/scenario
        $devPaths = \App\Models\DevelopmentPath::where('organization_id', $cs->organization_id)
            ->where('metadata->source', 'mobility_simulation')
            ->with(['people', 'actions.mentor'])
            ->get();

        return response()->json([
            'changeset' => $cs,
            'development_paths' => $devPaths,
        ]);
    }

    /**
     * Launch an LMS course.
     */
    public function launchLms(int $actionId, \App\Services\Talent\Lms\LmsService $lmsService)
    {
        $action = \App\Models\DevelopmentAction::findOrFail($actionId);

        try {
            $url = $lmsService->launchAction($action);

            // Mark as in_progress if it was pending
            if ($action->status === 'pending') {
                $action->update(['status' => 'in_progress', 'started_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'launch_url' => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Sync progress of an action.
     */
    public function syncProgress(int $actionId, \App\Services\Talent\Lms\LmsService $lmsService)
    {
        $action = \App\Models\DevelopmentAction::findOrFail($actionId);

        $changed = $lmsService->syncProgress($action);

        return response()->json([
            'success' => true,
            'status' => $action->status,
            'completed' => $changed,
        ]);
    }
}
