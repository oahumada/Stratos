<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreDevelopmentPlanRequest;
use App\Http\Requests\StoreSuccessionCandidateRequest;
use App\Http\Requests\UpdateSuccessionCandidateRequest;
use App\Models\DevelopmentPlan;
use App\Models\Scenario;
use App\Models\SuccessionCandidate;
use App\Services\ScenarioPlanning\SuccessionPlanningService;
use Illuminate\Http\JsonResponse;

class SuccessionPlanningController extends Controller
{
    public function __construct(private SuccessionPlanningService $service) {}

    /**
     * GET /api/scenarios/{id}/succession/candidates
     */
    public function indexCandidates(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $candidates = SuccessionCandidate::where('scenario_id', $scenario->id)
            ->with(['person', 'targetRole'])
            ->paginate(20);

        return response()->json([
            'data' => $candidates->items(),
            'pagination' => [
                'total' => $candidates->total(),
                'per_page' => $candidates->perPage(),
                'current_page' => $candidates->currentPage(),
                'last_page' => $candidates->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/scenarios/{id}/succession/candidates
     */
    public function storeCandidates(Scenario $scenario, StoreSuccessionCandidateRequest $request): JsonResponse
    {
        $this->authorize('create', SuccessionCandidate::class);

        $candidate = SuccessionCandidate::create([
            'organization_id' => $scenario->organization_id,
            'scenario_id' => $scenario->id,
            'person_id' => $request->person_id,
            'target_role_id' => $request->target_role_id,
            'skill_match_score' => $this->service->calculateSkillMatch(
                \App\Models\People::find($request->person_id),
                \App\Models\Roles::find($request->target_role_id),
                $scenario
            ),
            'status' => 'potential',
        ]);

        // Calculate readiness level
        $candidate->readiness_level = $this->service->assessReadiness($candidate);
        $candidate->save();

        return response()->json(['data' => $candidate], 201);
    }

    /**
     * PATCH /api/scenarios/{id}/succession/candidates/{id}
     */
    public function updateCandidate(SuccessionCandidate $candidate, UpdateSuccessionCandidateRequest $request): JsonResponse
    {
        $this->authorize('update', $candidate);

        $candidate->update($request->validated());

        return response()->json(['data' => $candidate]);
    }

    /**
     * GET /api/scenarios/{id}/succession/coverage
     */
    public function getCoverage(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $coverage = $this->service->calculateSuccessionCoverage($scenario);

        return response()->json(['data' => $coverage]);
    }

    /**
     * DELETE /api/scenarios/{id}/succession/candidates/{id}
     */
    public function deleteCandidate(SuccessionCandidate $candidate): JsonResponse
    {
        $this->authorize('delete', $candidate);

        $candidate->delete();

        return response()->json(['message' => 'Candidate deleted'], 204);
    }

    /**
     * POST /api/scenarios/{id}/succession/analyze
     * Trigger skill match + readiness recalculation for all candidates
     */
    public function analyze(Scenario $scenario): JsonResponse
    {
        $this->authorize('update', $scenario);

        $count = 0;
        $candidates = SuccessionCandidate::where('scenario_id', $scenario->id)->get();

        foreach ($candidates as $candidate) {
            $candidate->skill_match_score = $this->service->calculateSkillMatch(
                $candidate->person,
                $candidate->targetRole,
                $scenario
            );
            $candidate->readiness_level = $this->service->assessReadiness($candidate);
            $candidate->save();
            $count++;
        }

        return response()->json([
            'message' => "Recalculated $count candidates",
            'count' => $count,
        ]);
    }

    /**
     * GET /api/scenarios/{id}/succession/development-plans
     */
    public function listDevelopmentPlans(Scenario $scenario): JsonResponse
    {
        $this->authorize('view', $scenario);

        $plans = DevelopmentPlan::where('organization_id', $scenario->organization_id)
            ->whereHas('successionCandidate', fn ($q) => $q->where('scenario_id', $scenario->id))
            ->with('successionCandidate.person')
            ->paginate(20);

        return response()->json([
            'data' => $plans->items(),
            'pagination' => [
                'total' => $plans->total(),
                'per_page' => $plans->perPage(),
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/succession-candidates/{id}/development-plans
     */
    public function createDevelopmentPlan(
        SuccessionCandidate $candidate,
        StoreDevelopmentPlanRequest $request
    ): JsonResponse {
        $this->authorize('create', DevelopmentPlan::class);

        $plan = DevelopmentPlan::create([
            'organization_id' => $candidate->organization_id,
            'succession_candidate_id' => $candidate->id,
            'goal_description' => $request->goal_description,
            'target_completion_date' => $request->target_completion_date,
            'activities' => $request->activities ?? [],
            'status' => 'active',
        ]);

        return response()->json(['data' => $plan], 201);
    }
}
