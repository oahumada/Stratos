<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Talent\MobilitySimulationService;
use Illuminate\Http\Request;

class MobilitySimulationController extends Controller
{
    protected $simulationService;

    protected $aiAdvisor;

    public function __construct(
        MobilitySimulationService $simulationService,
        \App\Services\Talent\MobilityAIAdvisorService $aiAdvisor
    ) {
        $this->simulationService = $simulationService;
        $this->aiAdvisor = $aiAdvisor;
    }

    /**
     * Simulate a mobility move (Single or Mass).
     */
    public function simulate(Request $request)
    {
        $request->validate([
            'person_id' => 'nullable|exists:people,id',
            'person_ids' => 'nullable|array',
            'person_ids.*' => 'exists:people,id',
            'target_role_id' => 'nullable|exists:roles,id',
            'movements' => 'nullable|array',
            'movements.*.person_id' => 'required|exists:people,id',
            'movements.*.target_role_id' => 'required|exists:roles,id',
        ]);

        try {
            // Complex planned movements (Multiple roles)
            if ($request->has('movements') && ! empty($request->movements)) {
                $result = $this->simulationService->simulatePlannedMovements(
                    $request->movements
                );
            }
            // Mass simulation (Single role)
            elseif ($request->has('person_ids') && ! empty($request->person_ids)) {
                $result = $this->simulationService->simulateMassMovement(
                    $request->person_ids,
                    (int) $request->target_role_id
                );
            }
            // Single simulation
            elseif ($request->has('person_id')) {
                $result = $this->simulationService->simulateMovement(
                    (int) $request->person_id,
                    (int) $request->target_role_id
                );
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe proporcionar person_id, person_ids o movements.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al ejecutar la simulación: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get organization-wide mobility impact.
     */
    public function organizationImpact(Request $request)
    {
        $organizationId = $request->user()->organization_id;

        try {
            $impact = $this->simulationService->simulateDepartmentImpact($organizationId);

            return response()->json([
                'success' => true,
                'data' => $impact,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener impacto organizacional: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save a mobility simulation as a formal scenario.
     */
    public function saveScenario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'simulation_data' => 'required|array',
            'target_role_id' => 'required|exists:roles,id',
        ]);

        try {
            $user = $request->user();

            // Create a Scenario record
            $scenario = \App\Models\Scenario::create([
                'organization_id' => $user->organization_id,
                'name' => $request->name,
                'description' => 'Escenario de Movilidad Estratégica guardado desde el War-Room.',
                'status' => 'draft',
                'created_by' => $user->id,
                'custom_config' => [
                    'type' => 'mobility_simulation',
                    'target_role_id' => $request->target_role_id,
                    'simulation_payload' => $request->simulation_data,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Escenario guardado correctamente.',
                'data' => $scenario,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el escenario: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Materialize a scenario into an actionable ChangeSet.
     */
    public function materialize(int $id)
    {
        $scenario = \App\Models\Scenario::findOrFail($id);
        $user = auth()->user();

        try {
            $changeSet = $this->simulationService->materializeChangeSet($scenario, $user);

            return response()->json([
                'success' => true,
                'message' => 'Escenario materializado exitosamente.',
                'change_set_id' => $changeSet->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al materializar: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getExecutionTracking()
    {
        $orgId = auth()->user()->organization_id;

        // Fetch ChangeSets created from mobility scenarios
        $changeSets = \App\Models\ChangeSet::where('organization_id', $orgId)
            ->whereNotNull('scenario_id')
            ->with(['scenario', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $tracking = $changeSets->map(function ($cs) {
            // Find development plans related to this scenario/organization
            $devPlans = \App\Models\DevelopmentPath::where('organization_id', $cs->organization_id)
                ->where('metadata->source', 'mobility_simulation')
                ->with(['people', 'actions'])
                ->get();

            return [
                'id' => $cs->id,
                'title' => $cs->title,
                'status' => $cs->status,
                'created_at' => $cs->created_at->toDateTimeString(),
                'scenario_name' => $cs->scenario->name ?? 'N/A',
                'creator' => $cs->creator->name ?? 'Sistema',
                'ops_count' => count($cs->diff['ops'] ?? []),
                'projected_roi' => $cs->metadata['projected_roi'] ?? 0,
                'projected_savings' => $cs->metadata['projected_savings'] ?? 0,
                'development_progress' => $devPlans->map(function ($dp) {
                    $total = $dp->actions->count();
                    $completed = $dp->actions->where('status', 'completed')->count();

                    return [
                        'person_name' => $dp->people->full_name ?? 'N/A',
                        'title' => $dp->title,
                        'progress' => $total > 0 ? round(($completed / $total) * 100) : 0,
                        'status' => $dp->status,
                    ];
                }),
            ];
        });

        return response()->json($tracking);
    }

    /**
     * Get AI-driven mobility suggestions based on a business goal.
     */
    public function getAiSuggestions(Request $request)
    {
        $request->validate([
            'objective' => 'required|string|min:10',
        ]);

        $organizationId = $request->user()->organization_id;

        $result = $this->aiAdvisor->suggestStrategicMovements(
            $request->objective,
            $organizationId
        );

        return response()->json($result);
    }
}
