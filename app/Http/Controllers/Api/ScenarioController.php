<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Capability;
use App\Models\Scenario;
use App\Models\ScenarioTemplate;
use App\Repository\ScenarioRepository;
use App\Services\RoleSkillDerivationService;
use App\Services\ScenarioAnalysisService;
use App\Services\ScenarioAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ScenarioController extends Controller
{
    protected $analytics;

    protected $derivation;

    public function __construct(
        private ScenarioRepository $scenarioRepo,
        ScenarioAnalyticsService $analytics,
        RoleSkillDerivationService $derivation
    ) {
        $this->analytics = $analytics;
        $this->derivation = $derivation;
    }

    /**
     * Obtiene el IQ y métricas clave del escenario.
     */
    public function getIQ($id): JsonResponse
    {
        $iqData = $this->analytics->calculateScenarioIQ($id);

        return $this->successResponse($iqData);
    }

    /**
     * POST /api/scenarios/{id}/roles/{roleId}/derive-skills
     * Deriva skills desde competencias para un rol específico
     */
    public function deriveSkills($id, $roleId): JsonResponse
    {
        $result = $this->derivation->deriveSkillsFromCompetencies($id, $roleId);

        return $this->successResponse($result, 'Skills derivadas exitosamente');
    }

    /**
     * POST /api/scenarios/{id}/derive-all-skills
     * Deriva skills para todos los roles del escenario
     */
    public function deriveAllSkills($id): JsonResponse
    {
        $results = $this->derivation->deriveAllSkillsForScenario($id);

        return $this->successResponse($results, 'Skills derivadas para todos los roles');
    }

    /**
     * API: Listar escenarios paginados por organización
     */
    public function listScenarios(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }
        $organizationId = $user->organization_id;

        $filters = [
            'status' => $request->input('status'),
            'fiscal_year' => $request->input('fiscal_year'),
        ];

        $scenarios = $this->scenarioRepo->getScenariosByOrganization($organizationId, $filters);

        return $this->successResponse([
            'items' => $scenarios->items(),
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
    public function getCapabilityTree($id): JsonResponse
    {
        $service = app(\App\Services\ScenarioService::class);
        $tree = $service->getCapabilityTree((int) $id);

        return $this->successResponse($tree);
    }

    /**
     * Aprueba el escenario y "gradúa" las capacidades/skills incubadas.
     */
    public function approve($id): JsonResponse
    {
        return DB::transaction(function () use ($id) {
            $scenario = Scenario::findOrFail($id);

            // 1. Graduar Capabilities
            Capability::where('discovered_in_scenario_id', $id)
                ->update(['status' => 'active']);

            // 2. Graduar Skills
            DB::table('skills')
                ->where('discovered_in_scenario_id', $id)
                ->update(['maturity_status' => 'established']);

            // 3. Cambiar estado del escenario
            $scenario->update(['status' => 'approved']);

            return $this->successResponse(null, 'Escenario aprobado y capacidades graduadas con éxito.');
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

        if (! $scenario) {
            return $this->notFoundResponse('Scenario not found');
        }

        // Tenant isolation: only allow access to scenarios within the user's organization
        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }
        $userOrg = $user->organization_id;
        if ($scenario->organization_id !== $userOrg) {
            return $this->forbiddenResponse();
        }

        // Prepare payload and hide accepted prompt unless authorized
        $payload = $scenario ? $scenario->toArray() : null;
        try {
            $user = auth()->user();
            if ($payload && $user) {
                if (! \Gate::forUser($user)->allows('viewAcceptedPrompt', $scenario)) {
                    unset($payload['accepted_prompt']);
                    unset($payload['accepted_prompt_metadata']);
                }
            }
        } catch (\Throwable $e) {
            // Fail-open: if gate evaluation errors, remove sensitive fields
            if (is_array($payload)) {
                unset($payload['accepted_prompt']);
                unset($payload['accepted_prompt_metadata']);
            }
        }

        return $this->successResponse($payload);
    }

    /**
     * API: Crear un nuevo escenario (compatibilidad /v1 endpoint)
     */
    public function store(Request $request): JsonResponse
    {
        // Validar datos de entrada
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scenario_type' => 'nullable|string',
            'horizon_months' => 'nullable|integer|min:1',
            'planning_horizon_months' => 'nullable|integer|min:1', // Alias para compatibilidad frontend
            'fiscal_year' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'code' => 'nullable|string|max:50',
            'owner_user_id' => 'nullable|integer',
        ]);

        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        // Preparar datos para crear scenario
        $data = [
            'organization_id' => $user->organization_id,
            'created_by' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',  // Proporcionar string vacío si viene null
            'scenario_type' => $validated['scenario_type'] ?? 'transformation',
            // Usar horizon_months si viene, sino planning_horizon_months, sino 12 como default
            'horizon_months' => $validated['horizon_months'] ?? $validated['planning_horizon_months'] ?? 12,
            'fiscal_year' => $validated['fiscal_year'] ?? (int) date('Y'),  // Default al año actual si no se proporciona
            'start_date' => ($validated['start_date'] ?? null) ? \Carbon\Carbon::parse($validated['start_date'])->toDateString() : now()->toDateString(),
            'end_date' => ($validated['end_date'] ?? null) ? \Carbon\Carbon::parse($validated['end_date'])->toDateString() : now()->addMonths(12)->toDateString(),
            'code' => $validated['code'] ?? ('SCN-' . strtoupper(substr(md5(uniqid()), 0, 8))),
            'owner_user_id' => $validated['owner_user_id'] ?? $user->id,
        ];

        // Use Eloquent to create scenario (triggers model events for code generation)
        $scenario = Scenario::create($data);

        return $this->successResponse($scenario, 'Scenario created', 201);
    }

    /**
     * API: Actualizar escenario (compatibility PATCH /v1/...)
     */
    public function updateScenario($id, Request $request): JsonResponse
    {
        $scenario = $this->scenarioRepo->getScenarioById((int) $id);

        if (! $scenario) {
            return $this->notFoundResponse('Scenario not found');
        }

        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        if ($scenario->organization_id !== $user->organization_id) {
            return $this->forbiddenResponse();
        }

        $updated = $this->scenarioRepo->updateScenario((int) $id, $request->all());

        return $this->successResponse($updated);
    }

    /**
     * API: Instanciar un escenario desde una plantilla (compatibility)
     */
    public function instantiateFromTemplate($templateId, Request $request): JsonResponse
    {
        $template = ScenarioTemplate::find($templateId);
        if (! $template) {
            return $this->notFoundResponse('Template not found');
        }

        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        $data = [
            'name' => $request->input('name'),
            'organization_id' => $user->organization_id,
            'template_id' => $template->id,
            'horizon_months' => $request->input('horizon_months', 12),
            'fiscal_year' => $request->input('fiscal_year', now()->year),
            'scenario_type' => $template->type ?? 'template',
            'created_by' => $user->id,
        ];

        // Use Eloquent to create scenario (triggers model events for code generation)
        $scenario = Scenario::create($data);

        $skillDemands = $template->config['predefined_skills'] ?? [];

        return $this->successResponse(
            array_merge($scenario->toArray(), ['skill_demands' => $skillDemands, 'template_id' => $template->id]),
            'Scenario instantiated from template',
            201
        );
    }

    /**
     * API: Calcular gaps de un escenario (stub para tests)
     */
    public function calculateGaps($id): JsonResponse
    {
        $scenario = $this->scenarioRepo->getScenarioById((int) $id);
        if (! $scenario) {
            return $this->notFoundResponse('Scenario not found');
        }
        // Use service to build a proper gap structure
        $service = app(\App\Services\ScenarioService::class);
        $payload = $service->calculateScenarioGaps($scenario);

        return $this->successResponse($payload);
    }

    /**
     * API: Generate suggested strategies for scenario (minimal implementation for tests)
     */
    public function refreshSuggestedStrategies($id, Request $request): JsonResponse
    {
        $scenario = $this->scenarioRepo->getScenarioById((int) $id);
        if (! $scenario) {
            return response()->json(['success' => false, 'message' => 'Scenario not found'], 404);
        }

        // Tenant isolation
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        if ($scenario->organization_id !== $user->organization_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $created = 0;
        // 2. AI-Driven Analysis: Dispatch jobs for each competency gap
        $competencyGaps = \App\Models\ScenarioRoleCompetency::where('scenario_id', $scenario->id)
            ->get();

        foreach ($competencyGaps as $cGap) {
            $currentLevel = 0; // TODO: Real level
            if ($cGap->required_level > $currentLevel) {
                \App\Jobs\AnalyzeTalentGap::dispatch($cGap->id);
                $created++; // Count AI jobs as "created" suggestions for now
            }
        }

        // Create a simple proposed strategy per demanded skill
        // Create strategies based on actual skill gaps (high priority)
        $gaps = \App\Models\ScenarioRoleSkill::where('scenario_id', $scenario->id)
            ->whereRaw('required_level > current_level')
            ->get();

        foreach ($gaps as $gap) {
            // Obtener el Blueprint de talento para el rol, si existe
            // $gap->role_id refers to likely ScenarioRole id in this context (Step 2 logic)
            $scenarioRole = \App\Models\ScenarioRole::find($gap->role_id);
            if (! $scenarioRole) continue;

            $role = \App\Models\Roles::find($scenarioRole->role_id);
            if (! $role) continue;

            $blueprint = null;
            if ($role) {
                // ... same blueprint logic
                $blueprint = \App\Models\TalentBlueprint::where('scenario_id', $scenario->id)
                    ->where('role_name', $role->name)
                    ->first();
            }

            // Determinar estrategia base
            // ... (strategy logic remains same but truncated for replacement context) ...
            $gapSize = $gap->required_level - $gap->current_level;
            $strategy = 'build'; 
            $strategyName = 'Internal Upskilling';
            $description = "Desarrollar talento interno para cubrir la brecha de nivel {$gapSize}.";
            
            if ($blueprint) {
                if ($blueprint->synthetic_leverage > 50) {
                     $strategy = 'bot';
                     $strategyName = 'AI Agent / Automation';
                     $description = "Asignar carga al Talento Sintético ({$blueprint->synthetic_leverage}%). " . ($blueprint->agent_specs['logic_justification'] ?? '');
                } elseif ($blueprint->recommended_strategy === 'Buy') {
                     $strategy = 'buy';
                     $strategyName = 'External Hiring';
                     $description = "Contratación externa sugerida por el Blueprint de Talento.";
                } elseif ($gapSize > 2 || $gap->is_critical) {
                     $strategy = 'buy';
                     $strategyName = 'External Hiring';
                     $description = "Contratación externa para brecha crítica de nivel {$gapSize}.";
                }
                
                if ($blueprint->human_leverage > 0 && $blueprint->synthetic_leverage > 0) {
                     $description .= " (Mix: {$blueprint->human_leverage}% Humano / {$blueprint->synthetic_leverage}% Sintético)";
                }
            } else {
                 if ($gapSize > 2 || $gap->is_critical) {
                     $strategy = 'buy';
                     $strategyName = 'External Hiring';
                     $description = "Contratación externa para brecha de nivel {$gapSize} o habilidad crítica.";
                 }
            }

            // Check if strategy already exists for this skill/role/scenario to avoid duplicates
            // We use the actual Role ID here, not the ScenarioRole ID
            $exists = DB::table('scenario_closure_strategies')
                ->where('scenario_id', $scenario->id)
                ->where('skill_id', $gap->skill_id)
                ->where('role_id', $role->id)
                ->exists();

            if (! $exists) {
                $strategyId = DB::table('scenario_closure_strategies')->insertGetId([
                    'scenario_id' => $scenario->id,
                    'skill_id' => $gap->skill_id,
                    'role_id' => $role->id,
                    'strategy' => $strategy,
                    'strategy_name' => $strategyName,
                    'description' => $description,
                    'status' => 'proposed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                if ($strategyId) {
                    $created++;
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Suggested strategies refreshed', 'created' => $created]);
    }

    /**
     * API: Finalize / Consolidate scenario to move it into budgeting-ready state.
     * Sets a decision_status and returns the updated scenario payload.
     */
    public function finalizeScenario($id, Request $request): JsonResponse
    {
        $scenario = $this->scenarioRepo->getScenarioById((int) $id);

        if (! $scenario) {
            return $this->notFoundResponse('Scenario not found');
        }

        $user = auth()->user();
        if (! $user) {
            return $this->unauthorizedResponse();
        }

        if ($scenario->organization_id !== $user->organization_id) {
            return $this->forbiddenResponse();
        }

        // Use ScenarioService to transition decision_status to 'consolidated'
        $service = app(\App\Services\ScenarioService::class);
        $notes = $request->input('notes');
        try {
            // Use an allowed decision_status value ('approved') to move scenario to budgeting-ready state
            $updated = $service->transitionDecisionStatus($scenario, 'approved', $user, $notes);

            // Return fresh payload via repository to include eager loads
            $fresh = $this->scenarioRepo->getScenarioById((int) $id);

            return $this->successResponse($fresh, 'Scenario finalized');
        } catch (\Throwable $e) {
            \Log::error('Error finalizing scenario', ['id' => $id, 'error' => $e->getMessage()]);

            return $this->errorResponse('Error finalizing scenario', 500);
        }
    }

    /**
     * API: Comparar KPIs entre diferentes versiones de escenarios (Paso 6)
     */
    public function compareVersions(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return $this->errorResponse('No se proporcionaron IDs para comparar');
        }

        $scenarios = Scenario::whereIn('id', $ids)->get();
        $comparison = [];

        foreach ($scenarios as $scenario) {
            $iqData = $this->analytics->calculateScenarioIQ($scenario->id);
            
            // Sumar costos estimados de las estrategias de cierre
            $totalCost = \App\Models\ScenarioClosureStrategy::where('scenario_id', $scenario->id)
                ->sum('estimated_cost');
            
            // Sumar FTEs requeridos vs actuales
            $demand = \App\Models\ScenarioSkillDemand::where('scenario_id', $scenario->id)
                ->selectRaw('SUM(required_headcount) as total_req, SUM(current_headcount) as total_curr')
                ->first();

            $comparison[] = [
                'id' => $scenario->id,
                'name' => $scenario->name,
                'version' => $scenario->version_number,
                'iq' => $iqData['iq'] ?? 0,
                'total_cost' => (float)$totalCost,
                'total_req_fte' => (int)($demand->total_req ?? 0),
                'total_curr_fte' => (int)($demand->total_curr ?? 0),
                'gap_fte' => max(0, (int)($demand->total_req ?? 0) - (int)($demand->total_curr ?? 0)),
                'status' => $scenario->decision_status,
                'created_at' => $scenario->created_at,
            ];
        }

        return $this->successResponse($comparison);
    }

    public function designTalent($id, \App\Services\Talent\TalentDesignOrchestratorService $svc): JsonResponse
    {
        $result = $svc->orchestrate((int) $id);

        if (!$result['success']) {
            return $this->errorResponse($result['error'] ?? 'Error en la orquestación', 500);
        }

        return $this->successResponse($result);
    }

    /**
     * Paso 2 - Fase 2: Aplica en batch las propuestas aprobadas por el usuario.
     */
    public function applyAgentProposals(Request $request, $id, \App\Services\Talent\TalentDesignOrchestratorService $svc): JsonResponse
    {
        $scenario = Scenario::findOrFail($id);

        $user = auth()->user();
        if (!$user || $scenario->organization_id !== $user->organization_id) {
            return $this->forbiddenResponse();
        }

        $validated = $request->validate([
            'approved_role_proposals'                                      => 'present|array',
            'approved_role_proposals.*.type'                               => 'required|string|in:NEW,EVOLVE,REPLACE',
            'approved_role_proposals.*.proposed_name'                      => 'nullable|string',
            'approved_role_proposals.*.target_role_id'                     => 'nullable|integer',
            'approved_role_proposals.*.archetype'                                => 'nullable|string|in:E,T,O',
            'approved_role_proposals.*.fte_suggested'                          => 'nullable|numeric|min:0.1',
            'approved_role_proposals.*.talent_composition'                     => 'nullable|array',
            'approved_role_proposals.*.talent_composition.human_percentage'    => 'nullable|numeric|between:0,100',
            'approved_role_proposals.*.talent_composition.synthetic_percentage'=> 'nullable|numeric|between:0,100',
            'approved_role_proposals.*.talent_composition.logic_justification' => 'nullable|string',
            'approved_role_proposals.*.competency_mappings'                    => 'nullable|array',
            'approved_role_proposals.*.competency_mappings.*.competency_name' => 'nullable|string',
            'approved_role_proposals.*.competency_mappings.*.competency_id'   => 'nullable|integer',
            'approved_role_proposals.*.competency_mappings.*.change_type'     => 'nullable|string|in:maintenance,transformation,enrichment,extinction',
            'approved_role_proposals.*.competency_mappings.*.required_level'  => 'nullable|integer|between:1,5',
            'approved_role_proposals.*.competency_mappings.*.is_core'         => 'nullable|boolean',
            'approved_catalog_proposals'                                   => 'nullable|array',
            'approved_catalog_proposals.*.type'                            => 'required|string|in:ADD,MODIFY,REPLACE',
            'approved_catalog_proposals.*.proposed_name'                   => 'required|string',
            'approved_catalog_proposals.*.competency_id'                   => 'nullable|integer',
            'approved_catalog_proposals.*.action_rationale'                => 'nullable|string',
        ]);

        try {
            $result = $svc->applyProposals(
                (int) $id,
                $validated['approved_role_proposals'],
                $validated['approved_catalog_proposals'] ?? []
            );
        } catch (\Throwable $e) {
            \Log::error('applyAgentProposals exception', ['scenario_id' => $id, 'error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 500);
        }

        if (!$result['success']) {
            return $this->errorResponse($result['error'] ?? 'Error al aplicar propuestas', 500);
        }

        return $this->successResponse($result['stats'], 'Propuestas aplicadas correctamente');
    }

    /**
     * Paso 2 - Fase 4: Aprobación final del diseño de talento.
     */
    public function finalizeStep2(Request $request, $id, \App\Services\Talent\TalentDesignOrchestratorService $svc): JsonResponse
    {
        $scenario = Scenario::findOrFail($id);

        $user = auth()->user();
        if (!$user || $scenario->organization_id !== $user->organization_id) {
            return $this->forbiddenResponse();
        }

        $result = $svc->finalizeStep2((int) $id);

        if (!$result['success']) {
            return $this->errorResponse($result['error'], 422);
        }

        return $this->successResponse(null, $result['message']);
    }
    /**
     * API: Obtener resumen ejecutivo consolidado (Paso 7)
     */
    public function summarize($id): JsonResponse
    {
        $iqData = $this->analytics->calculateScenarioIQ($id);
        
        // Costos por estrategia
        $strategies = \App\Models\ScenarioClosureStrategy::where('scenario_id', $id)
            ->select('strategy', \DB::raw('SUM(estimated_cost) as total_cost'))
            ->groupBy('strategy')
            ->get();
        
        // FTEs Consolidados
        $demand = \App\Models\ScenarioSkillDemand::where('scenario_id', $id)
            ->selectRaw('SUM(required_headcount) as total_req, SUM(current_headcount) as total_curr')
            ->first();

        // Gaps Críticos (Top 5 por headcount)
        $criticalGaps = \App\Models\ScenarioSkillDemand::where('scenario_id', $id)
            ->with('skill')
            ->orderByDesc(\DB::raw('required_headcount - current_headcount'))
            ->limit(5)
            ->get()
            ->map(fn($d) => [
                'skill' => $d->skill->name,
                'gap' => max(0, $d->required_headcount - $d->current_headcount),
                'priority' => $d->priority
            ]);

        // Synthetization Index: Promedio ponderado de leverage sintético por FTE
        $blueprints = \App\Models\TalentBlueprint::where('scenario_id', $id)->get();
        $totalFte = $blueprints->sum('total_fte_required');
        $weightedSynthetic = 0;
        
        if ($totalFte > 0) {
            foreach ($blueprints as $bp) {
                // synthetic_leverage is typically 0-100
                $weightedSynthetic += ($bp->total_fte_required * ($bp->synthetic_leverage ?? 0));
            }
            $mix = round($weightedSynthetic / $totalFte, 1);
        } else {
            $mix = 0;
        }

        return $this->successResponse([
            'iq' => $iqData['iq'] ?? 0,
            'confidence' => $iqData['confidence_score'] ?? 0,
            'investment' => $strategies,
            'total_investment' => $strategies->sum('total_cost'),
            'fte' => [
                'required' => (int)($demand->total_req ?? 0),
                'current' => (int)($demand->total_curr ?? 0),
                'gap' => max(0, (int)($demand->total_req ?? 0) - (int)($demand->total_curr ?? 0))
            ],
            'critical_gaps' => $criticalGaps,
            'synthetization_index' => $mix,
            'risk_level' => $this->calculateRiskLevel($iqData['iq'] ?? 0, $demand)
        ]);
    }

    /**
     * API: Obtener visualización de impacto (Paso 5)
     */
    public function getVersions($id): JsonResponse
    {
        $scenario = Scenario::findOrFail($id);
        
        // Return all scenarios in the same version group, or at least itself
        $query = Scenario::query();
        
        if ($scenario->version_group_id) {
            $query->where('version_group_id', $scenario->version_group_id);
        } else {
            $query->where('id', $id);
        }
        
        $versions = $query->with('owner')
            ->orderBy('version_number', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse($versions);
    }

    public function getImpact($id): JsonResponse
    {
        $user = auth()->user();
        $scenario = Scenario::findOrFail($id);

        if ($scenario->organization_id !== $user->organization_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $impact = $this->analytics->calculateImpact($id);

        return $this->successResponse($impact);
    }

    private function calculateRiskLevel($iq, $demand)
    {
        $gap = max(0, (int)($demand->total_req ?? 0) - (int)($demand->total_curr ?? 0));
        if ($iq > 70 && $gap < 10) return 'Low';
        if ($iq < 40 || $gap > 50) return 'High';
        return 'Medium';
    }

    /**
     * API: Exportar reporte financiero detallado (Excel/CSV)
     */
    public function exportFinancial($id)
    {
        $scenario = Scenario::findOrFail($id);
        
        if ($scenario->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $fileName = 'Stratos_Financial_Report_' . $scenario->code . '_' . date('Y-m-d') . '.csv';

        $strategies = \DB::table('scenario_closure_strategies')
            ->join('skills', 'scenario_closure_strategies.skill_id', '=', 'skills.id')
            ->join('roles', 'scenario_closure_strategies.role_id', '=', 'roles.id')
            ->where('scenario_closure_strategies.scenario_id', $id)
            ->select(
                'scenario_closure_strategies.id',
                'roles.name as role_name',
                'skills.name as skill_name',
                'scenario_closure_strategies.strategy',
                'scenario_closure_strategies.description',
                'scenario_closure_strategies.estimated_cost',
                'scenario_closure_strategies.status'
            )
            ->get();
            
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($strategies, $scenario) {
            $file = fopen('php://output', 'w');
            
            // Header Info
            fputcsv($file, ['Stratos Intelligent Planning - Financial Report']);
            fputcsv($file, ['Scenario:', $scenario->name]);
            fputcsv($file, ['Code:', $scenario->code]);
            fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty line

            // Column Headers
            fputcsv($file, ['ID', 'Role', 'Skill', 'Strategy Type', 'Description', 'Estimated Cost (USD)', 'Status']);

            $totalCost = 0;

            foreach ($strategies as $row) {
                $totalCost += $row->estimated_cost;
                fputcsv($file, [
                    $row->id,
                    $row->role_name,
                    $row->skill_name,
                    strtoupper($row->strategy),
                    $row->description,
                    number_format($row->estimated_cost, 2),
                    ucfirst($row->status)
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL INVESTMENT:', number_format($totalCost, 2)]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: Eliminar un escenario (Destroy)
     */
    public function destroyScenario($id): JsonResponse
    {
        $scenario = Scenario::find($id);

        if (!$scenario) {
            return response()->json([
                'success' => false,
                'message' => 'Scenario not found',
            ], 404);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        if ($scenario->organization_id !== $user->organization_id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        try {
            $scenario->delete();
            return $this->successResponse(null, 'Scenario deleted successfully');
        } catch (\Throwable $e) {
            \Log::error('Error deleting scenario ' . $id . ': ' . $e->getMessage());
            return $this->errorResponse('Error deleting scenario', 500);
        }
    }
}
