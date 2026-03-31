<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorTransformationTaskRequest;
use App\Http\Requests\UpdateTransformationPhaseRequest;
use App\Http\Requests\UpdateTransformationTaskRequest;
use App\Models\Scenario;
use App\Models\TransformationPhase;
use App\Models\TransformationTask;
use App\Services\ScenarioPlanning\TransformationRoadmapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransformationRoadmapController extends Controller
{
    public function __construct(private TransformationRoadmapService $service) {}

    /**
     * GET /api/scenarios/{id}/transformation/roadmap
     */
    public function getRoadmap(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $roadmap = $this->service->getRoadmap($scenario);

        return response()->json(['data' => $roadmap]);
    }

    /**
     * POST /api/scenarios/{id}/transformation/generate
     */
    public function generate(Scenario $scenario): JsonResponse
    {
        $this->authorize('update', $scenario);

        $this->service->generateTransformationPlan($scenario);

        return response()->json([
            'message' => 'Transformation roadmap generated',
            'data' => $this->service->getRoadmap($scenario),
        ]);
    }

    /**
     * GET /api/scenarios/{id}/transformation/phases
     */
    public function listPhases(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $phases = TransformationPhase::where('scenario_id', $scenario->id)
            ->orderBy('phase_number')
            ->paginate(20);

        return response()->json([
            'data' => $phases->items(),
            'pagination' => [
                'total' => $phases->total(),
                'per_page' => $phases->perPage(),
                'current_page' => $phases->currentPage(),
                'last_page' => $phases->lastPage(),
            ],
        ]);
    }

    /**
     * PATCH /api/scenarios/{id}/transformation/phases/{id}
     */
    public function updatePhase(
        TransformationPhase $phase,
        UpdateTransformationPhaseRequest $request
    ): JsonResponse {
        $this->authorize('update', $phase);

        $phase->update($request->validated());

        return response()->json(['data' => $phase]);
    }

    /**
     * GET /api/scenarios/{id}/transformation/phases/{id}/tasks
     */
    public function listTasks(TransformationPhase $phase): JsonResponse
    {
        $this->authorize('view', $phase);

        $tasks = $phase->tasks()
            ->with('owner')
            ->paginate(20);

        return response()->json([
            'data' => $tasks->items(),
            'pagination' => [
                'total' => $tasks->total(),
                'per_page' => $tasks->perPage(),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/scenarios/{id}/transformation/tasks
     */
    public function createTask(
        Scenario $scenario,
        StorTransformationTaskRequest $request
    ): JsonResponse {
        $this->authorize('create', TransformationTask::class);

        $phase = TransformationPhase::where('scenario_id', $scenario->id)
            ->where('organization_id', $scenario->organization_id)
            ->findOrFail($request->phase_id);

        $task = $phase->tasks()->create([
            ...$request->validated(),
            'organization_id' => $scenario->organization_id,
        ]);

        return response()->json(['data' => $task], 201);
    }

    /**
     * PATCH /api/scenarios/{id}/transformation/tasks/{id}
     */
    public function updateTask(
        TransformationTask $task,
        UpdateTransformationTaskRequest $request
    ): JsonResponse {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return response()->json(['data' => $task]);
    }

    /**
     * PATCH /api/transformation/tasks/{id}/status
     */
    public function updateTaskStatus(TransformationTask $task, Request $request): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => 'required|in:not_started,in_progress,blocked,completed,cancelled',
        ]);

        if ($validated['status'] === 'completed') {
            $task->markCompleted();
        } else {
            $task->update(['status' => $validated['status']]);
        }

        return response()->json(['data' => $task]);
    }

    /**
     * GET /api/scenarios/{id}/transformation/blockers
     */
    public function getBlockers(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $phases = TransformationPhase::where('scenario_id', $scenario->id)->get();

        $allBlockers = [];
        foreach ($phases as $phase) {
            $phaseBlockers = $this->service->identifyBlockers($phase);
            $allBlockers = array_merge($allBlockers, $phaseBlockers);
        }

        return response()->json(['data' => $allBlockers]);
    }

    /**
     * POST /api/scenarios/{id}/transformation/export
     * Export roadmap as PDF
     */
    public function exportRoadmap(Scenario $scenario)
    {
        $this->authorize('view', $scenario);

        // For now, return JSON representation
        // In production, integrate with PDF library (dompdf, etc.)
        $roadmap = $this->service->getRoadmap($scenario);

        return response()->json([
            'message' => 'Export functionality to be implemented',
            'format' => 'pdf',
            'data' => $roadmap,
        ]);
    }
}
