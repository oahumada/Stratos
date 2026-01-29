<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use App\Models\Capability;
use App\Services\ScenarioAnalysisService;
use App\Repository\ScenarioRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\ScenarioTemplate;

class ScenarioController extends Controller
{
    protected $analytics;

    public function __construct(
        private ScenarioRepository $scenarioRepo,
        private ScenarioAnalysisService $analysisService
    ) {
        $this->analytics = $analysisService;
    }

    /**
     * Obtiene el IQ y métricas clave del escenario.
     */
    public function getIQ($id)
    {
        $metrics = $this->analytics->calculateScenarioIQ($id);
        return response()->json($metrics);
    }

    /**
     * API: Listar escenarios paginados por organización
     */
    public function listScenarios(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $organizationId = $user->organization_id;

        $filters = [
            'status' => $request->input('status'),
            'fiscal_year' => $request->input('fiscal_year'),
        ];

        $scenarios = $this->scenarioRepo->getScenariosByOrganization($organizationId, $filters);

        return response()->json([
            'success' => true,
            'data' => $scenarios->items(),
            'pagination' => [
                'current_page' => $scenarios->currentPage(),
                'total' => $scenarios->total(),
                'per_page' => $scenarios->perPage(),
                'last_page' => $scenarios->lastPage(),
            ],
        ]);
    }

    /**
     * Obtiene el árbol jerárquico de capacidades para el escenario.
     */
    public function getCapabilityTree($id)
    {
        $scenario = Scenario::with([
            'capabilities.competencies.skills'
        ])->findOrFail($id);

        $tree = $scenario->capabilities->map(function ($capability) use ($id) {
            return [
                'id' => $capability->id,
                'name' => $capability->name,
                'description' => $capability->description ?? null,
                'importance' => $capability->importance ?? null,
                'position_x' => $capability->position_x ?? null,
                'position_y' => $capability->position_y ?? null,
                'strategic_role' => $capability->pivot->strategic_role ?? null,
                'strategic_weight' => $capability->pivot->strategic_weight ?? null,
                'priority' => $capability->pivot->priority ?? null,
                'rationale' => $capability->pivot->rationale ?? null,
                'required_level' => $capability->pivot->required_level ?? null,
                'is_critical' => $capability->pivot->is_critical ?? null,
                'is_incubating' => $capability->isIncubating(),
                'readiness' => round($this->analytics->calculateCapabilityReadiness($id, $capability->id) * 100, 1),
                'competencies' => $capability->competencies->map(function ($comp) use ($id) {
                    return [
                        'id' => $comp->id,
                        'name' => $comp->name,
                        'description' => $comp->description ?? null,
                        'readiness' => round($this->analytics->calculateCompetencyReadiness($id, $comp->id) * 100, 1),
                        // include pivot attributes for capability<->competency relation so frontend can show/edit them
                        'pivot' => [
                            'strategic_weight' => $comp->pivot->strategic_weight ?? null,
                            'priority' => $comp->pivot->priority ?? null,
                            'required_level' => $comp->pivot->required_level ?? null,
                            'is_critical' => $comp->pivot->is_critical ?? null,
                            'rationale' => $comp->pivot->rationale ?? null,
                        ],
                        'skills' => $comp->skills->map(function ($skill) use ($id) {
                            return [
                                'id' => $skill->id,
                                'name' => $skill->name,
                                'weight' => $skill->pivot->weight ?? null,
                                'is_incubating' => !is_null($skill->discovered_in_scenario_id),
                                'readiness' => round($this->analytics->calculateSkillReadiness($id, $skill->id) * 100, 1)
                            ];
                        })
                    ];
                })
            ];
        });

        return response()->json($tree);
    }

    /**
     * Aprueba el escenario y "gradúa" las capacidades/skills incubadas.
     */
    public function approve($id)
    {
        return DB::transaction(function () use ($id) {
            $scenario = Scenario::findOrFail($id);

            // 1. Graduar Capabilities
            Capability::where('discovered_in_scenario_id', $id)
                ->update(['status' => 'active']);

            // 2. Graduar Skills (asumiendo tabla skills tiene discovered_in_scenario_id)
            DB::table('skills')
                ->where('discovered_in_scenario_id', $id)
                ->update(['maturity_status' => 'established']);

            // 3. Cambiar estado del escenario
            $scenario->update(['status' => 'approved']);

            return response()->json(['message' => 'Escenario aprobado y capacidades graduadas con éxito.']);
        });
    }

    public function show($id)
    {
        $scenario = $this->scenarioRepo->findWithCapabilities($id);

        $capabilities = $scenario->capabilities()
            ->withPivot('required_level', 'is_critical')
            ->get()
            ->map(function ($cap) {
                return [
                    'id' => $cap->id,
                    'name' => $cap->name,
                    'description' => $cap->description,
                    'importance' => $cap->importance,
                    'level' => 3,  // Calcular desde evaluaciones
                    'required' => $cap->pivot->required_level,
                    'x' => $cap->position_x,
                    'y' => $cap->position_y,
                ];
            });

        // Generar conexiones (puedes tener una tabla capability_links)
        $connections = [
            ['source_id' => 1, 'target_id' => 2, 'is_critical' => true],
            ['source_id' => 2, 'target_id' => 3, 'is_critical' => false],
        ];

        return Inertia::render('Stratos/ScenarioView', [
            'scenario' => $scenario,
            'capabilities' => $capabilities,
            'connections' => $connections,
        ]);
    }


    /**
     * API: Obtener escenario como JSON (compatibility method)
     */
    public function showScenario($id): JsonResponse
    {
        $scenario = $this->scenarioRepo->getScenarioById((int) $id);

        if (!$scenario) {
            return response()->json([
                'success' => false,
                'message' => 'Scenario not found',
            ], 404);
        }

        // Tenant isolation: only allow access to scenarios within the user's organization
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $userOrg = $user->organization_id;
        if ($scenario->organization_id !== $userOrg) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $scenario,
        ]);
    }

    /**
     * API: Crear un nuevo escenario (compatibilidad /v1 endpoint)
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->only(['name', 'description', 'scenario_type', 'horizon_months', 'fiscal_year']);
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $data['organization_id'] = $user->organization_id;
        $data['created_by'] = $user->id;
        $data['created_at'] = now()->toDateTimeString();
        $data['updated_at'] = now()->toDateTimeString();

        // Insert into legacy compatibility table to avoid strict 'scenarios' schema constraints
        $id = DB::table('workforce_planning_scenarios')->insertGetId($data);

        $scenario = (array) DB::table('workforce_planning_scenarios')->where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Scenario created',
            'data' => $scenario,
        ], 201);
    }

    /**
     * API: Actualizar escenario (compatibility PATCH /v1/...)
     */
    public function updateScenario($id, Request $request): JsonResponse
    {
        $scenario = $this->scenarioRepo->getScenarioById((int) $id);

        if (!$scenario) {
            return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        if ($scenario->organization_id !== $user->organization_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $updated = $this->scenarioRepo->updateScenario((int) $id, $request->all());

        return response()->json(['success' => true, 'data' => $updated]);
    }

    /**
     * API: Instanciar un escenario desde una plantilla (compatibility)
     */
    public function instantiateFromTemplate($templateId, Request $request): JsonResponse
    {
        $template = ScenarioTemplate::find($templateId);
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Template not found'], 404);
        }

        $data = [
            'name' => $request->input('name'),
            'organization_id' => (auth()->user() ? auth()->user()->organization_id : null),
            'template_id' => $template->id,
            'horizon_months' => $request->input('horizon_months', 12),
            'scenario_type' => $template->type ?? 'template',
        ];
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }
        $data['created_at'] = now()->toDateTimeString();
        $data['updated_at'] = now()->toDateTimeString();

        $id = DB::table('workforce_planning_scenarios')->insertGetId($data);
        $scenario = (array) DB::table('workforce_planning_scenarios')->where('id', $id)->first();

        $skillDemands = $template->config['predefined_skills'] ?? [];

        return response()->json([
            'success' => true,
            'message' => 'Scenario instantiated from template',
            'data' => array_merge($scenario, ['skill_demands' => $skillDemands, 'template_id' => $template->id]),
        ], 201);
    }

    /**
     * API: Calcular gaps de un escenario (stub para tests)
     */
    public function calculateGaps($id): JsonResponse
    {
        $scenario = $this->scenarioRepo->getScenarioById((int) $id);
        if (!$scenario) {
            return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
        }
        // Use service to build a proper gap structure
        $service = app(\App\Services\ScenarioService::class);
        $payload = $service->calculateScenarioGaps($scenario);

        return response()->json(['success' => true, 'data' => $payload]);
    }

    /**
     * API: Generate suggested strategies for scenario (minimal implementation for tests)
     */
    public function refreshSuggestedStrategies($id, Request $request): JsonResponse
    {
        $scenario = $this->scenarioRepo->getScenarioById((int) $id);
        if (!$scenario) {
            return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
        }

        // Tenant isolation
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        if ($scenario->organization_id !== $user->organization_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $created = 0;
        // Create a simple proposed strategy per demanded skill
        foreach (\App\Models\ScenarioSkillDemand::where('scenario_id', $scenario->id)->get() as $d) {
            $id = DB::table('scenario_closure_strategies')->insertGetId([
                'scenario_id' => $scenario->id,
                'skill_id' => $d->skill_id,
                'strategy' => 'build',
                'strategy_name' => 'Auto suggested',
                'description' => 'Auto-generated suggested strategy',
                'status' => 'proposed',
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
            if ($id)
                $created++;
        }

        return response()->json(['success' => true, 'message' => 'Suggested strategies refreshed', 'created' => $created]);
    }
}
