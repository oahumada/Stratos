<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Capability;
use App\Models\Competency;
use App\Models\Scenario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CapabilityCompetencyController extends Controller
{
    public const MSG_SCENARIO_NOT_FOUND = 'Scenario not found';
    public const MSG_CAPABILITY_NOT_FOUND = 'Capability not found';
    public const MSG_COMPETENCY_NOT_FOUND = 'Competency not found';
    public const MSG_COMPETENCY_NAME_REQUIRED = 'Competency name is required';
    public const MSG_RELATION_NOT_FOUND = 'Relation not found';
    public const MSG_FORBIDDEN = 'Forbidden';
    public const MSG_UNAUTHENTICATED = 'Unauthenticated';

    /**
     * POST: /strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies
     */
    public function store(Request $request, $scenarioId, $capabilityId): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => self::MSG_UNAUTHENTICATED], 401);
        }

        $scenario = Scenario::find($scenarioId);
        if (!$scenario) {
            return response()->json(['success' => false, 'message' => self::MSG_SCENARIO_NOT_FOUND], 404);
        }
        if ($scenario->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => self::MSG_FORBIDDEN], 403);
        }

        $cap = Capability::find($capabilityId);
        if (!$cap) {
            return response()->json(['success' => false, 'message' => self::MSG_CAPABILITY_NOT_FOUND], 404);
        }

        $competencyId = $request->input('competency_id');

        try {
            $result = DB::transaction(function () use ($request, $scenarioId, $capabilityId, $competencyId, $user) {
                if ($competencyId) {
                    $comp = Competency::find($competencyId);
                    if (!$comp) {
                        throw new \Exception(self::MSG_COMPETENCY_NOT_FOUND);
                    }
                    if ($comp->organization_id !== ($user->organization_id ?? null)) {
                        throw new \Exception(self::MSG_FORBIDDEN);
                    }
                    $createdCompetencyId = $comp->id;
                } else {
                    $payload = $request->input('competency', []);
                    $name = trim($payload['name'] ?? '');
                    if (empty($name)) {
                        throw new \Exception(self::MSG_COMPETENCY_NAME_REQUIRED);
                    }
                    $comp = Competency::create([
                        'organization_id' => $user->organization_id ?? null,
                        'name' => $name,
                        'description' => $payload['description'] ?? null,
                    ]);
                    $createdCompetencyId = $comp->id;
                }

                $resolvedWeight = $this->resolveWeight($request);

                $insert = [
                    'scenario_id' => $scenarioId,
                    'capability_id' => $capabilityId,
                    'competency_id' => $createdCompetencyId,
                    'required_level' => (int) $request->input('required_level', 3),
                    'rationale' => $request->input('rationale', null),
                    'is_required' => (bool) $request->input('is_required', false),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $this->addOptionalPivotColumns($insert, $request, $resolvedWeight);

                $exists = DB::table('capability_competencies')
                    ->where('scenario_id', $scenarioId)
                    ->where('capability_id', $capabilityId)
                    ->where('competency_id', $createdCompetencyId)
                    ->exists();

                if ($exists) {
                    $row = DB::table('capability_competencies')
                        ->where('scenario_id', $scenarioId)
                        ->where('capability_id', $capabilityId)
                        ->where('competency_id', $createdCompetencyId)
                        ->first();

                    return ['status' => 'exists', 'row' => $row];
                }

                DB::table('capability_competencies')->insert($insert);
                return ['status' => 'created', 'data' => $insert];
            });

            if ($result['status'] === 'created') {
                return response()->json(['success' => true, 'data' => $result['data']], 201);
            }

            return response()->json(['success' => true, 'data' => $result['row'], 'note' => 'already_exists'], 200);
        } catch (\Exception $e) {
            Log::error('Error creating capability_competency: ' . $e->getMessage(), ['scenario_id' => $scenarioId, 'capability_id' => $capabilityId]);
            return $this->handleException($e);
        }
    }

    /**
     * PATCH: /strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}
     */
    public function update(Request $request, $scenarioId, $capabilityId, $competencyId): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => self::MSG_UNAUTHENTICATED], 401);
        }
        $scenario = Scenario::find($scenarioId);
        if (!$scenario) {
            return response()->json(['success' => false, 'message' => self::MSG_SCENARIO_NOT_FOUND], 404);
        }
        if ($scenario->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => self::MSG_FORBIDDEN], 403);
        }

        $query = DB::table('capability_competencies')
            ->where('scenario_id', $scenarioId)
            ->where('capability_id', $capabilityId)
            ->where('competency_id', $competencyId);

        if (!$query->exists()) {
            return response()->json(['success' => false, 'message' => self::MSG_RELATION_NOT_FOUND], 404);
        }

        $update = [];
        if ($request->has('required_level')) $update['required_level'] = (int) $request->input('required_level');
        if ($request->has('rationale')) $update['rationale'] = $request->input('rationale');
        if ($request->has('is_required')) $update['is_required'] = (bool) $request->input('is_required');
        
        $resolvedWeight = $this->resolveWeight($request);
        if ($resolvedWeight !== null) {
             if (Schema::hasColumn('capability_competencies', 'strategic_weight')) {
                 $update['strategic_weight'] = $resolvedWeight;
             } elseif (Schema::hasColumn('capability_competencies', 'weight')) {
                 $update['weight'] = $resolvedWeight;
             }
        }

        if ($request->has('priority') && Schema::hasColumn('capability_competencies', 'priority')) {
            $update['priority'] = (int) $request->input('priority');
        }

        if (!empty($update)) {
            $update['updated_at'] = now();
            $query->update($update);
        }

        return response()->json(['success' => true, 'data' => $query->first()]);
    }

    /**
     * DELETE: /strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies/{competencyId}
     */
    public function destroy($scenarioId, $capabilityId, $competencyId): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
             return response()->json(['success' => false, 'message' => self::MSG_UNAUTHENTICATED], 401);
        }
        $scenario = Scenario::find($scenarioId);
        if (!$scenario) {
            return response()->json(['success' => false, 'message' => self::MSG_SCENARIO_NOT_FOUND], 404);
        }
        if ($scenario->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => self::MSG_FORBIDDEN], 403);
        }

        $deleted = DB::table('capability_competencies')
            ->where('scenario_id', $scenarioId)
            ->where('capability_id', $capabilityId)
            ->where('competency_id', $competencyId)
            ->delete();

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Relation deleted']);
        }

        return response()->json(['success' => false, 'message' => self::MSG_RELATION_NOT_FOUND], 404);
    }

    private function resolveWeight(Request $request): ?int
    {
        if ($request->has('weight')) {
            return (int) $request->input('weight');
        }
        if ($request->has('strategic_weight')) {
            return (int) $request->input('strategic_weight');
        }
        return null;
    }

    private function addOptionalPivotColumns(array &$insert, Request $request, ?int $resolvedWeight): void
    {
        if (Schema::hasColumn('capability_competencies', 'strategic_weight')) {
            $insert['strategic_weight'] = $resolvedWeight;
        } elseif (Schema::hasColumn('capability_competencies', 'weight')) {
            $insert['weight'] = $resolvedWeight;
        }
        if (Schema::hasColumn('capability_competencies', 'priority')) {
            $insert['priority'] = $request->has('priority') ? (int) $request->input('priority') : null;
        }
    }

    private function handleException(\Exception $e): JsonResponse
    {
        $msg = $e->getMessage();
        if ($msg === self::MSG_FORBIDDEN) return response()->json(['success' => false, 'message' => $msg], 403);
        if ($msg === self::MSG_COMPETENCY_NOT_FOUND) return response()->json(['success' => false, 'message' => $msg], 404);
        if ($msg === self::MSG_COMPETENCY_NAME_REQUIRED) return response()->json(['success' => false, 'message' => $msg], 422);

        return response()->json(['success' => false, 'message' => 'Server error'], 500);
    }
}
