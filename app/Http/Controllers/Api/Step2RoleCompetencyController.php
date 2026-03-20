<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use App\Models\People;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioRoleSkill;
use App\Models\Skill;
use App\Services\EmbeddingService;
use App\Services\RoleSkillDerivationService;
use App\Services\Talent\RoleDesignerService;
use App\Services\Talent\TalentDesignOrchestratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Paso 2: Roles ↔ Competencies Mapping
 *
 * Endpoints para gestionar la asignación de competencias a roles
 * en un escenario específico, con sus cambios de estado (maintenance,
 * transformation, enrichment, extinction)
 */
class Step2RoleCompetencyController extends Controller
{
    private const ROLE_NAME_SELECT = 'roles.name as role_name';

    public function __construct(
        private RoleSkillDerivationService $derivationService,
        private TalentDesignOrchestratorService $orchestrator
    ) {}

    /**
     * GET /api/v1/scenarios/{scenarioId}/step2/data
     * Obtener datos completos para la matriz (roles, competencias, mappings)
     */
    public function getMatrixData(int $scenarioId): JsonResponse
    {
        $scenario = Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        // Roles en este escenario
        $roles = ScenarioRole::where('scenario_id', $scenarioId)
            ->join('roles', 'scenario_roles.role_id', '=', 'roles.id')
            ->select(
                'scenario_roles.id',
                'scenario_roles.role_id',
                self::ROLE_NAME_SELECT,
                'scenario_roles.fte',
                'scenario_roles.role_change',
                'scenario_roles.impact_level',
                'scenario_roles.evolution_type',
                'scenario_roles.rationale',
                'scenario_roles.human_leverage',
                'scenario_roles.archetype'
            )
            ->get();

        // Competencias del escenario: solo las que pertenecen a capabilities
        // incluidas en este escenario (Paso 1), tal como muestra el diagrama de nodos.
        // También incluimos cualquier competencia que ya tenga un mapping en el escenario
        // (por si fue asignada manualmente o por el agente fuera del blueprint base).
        $scenarioCompetencyIds = DB::table('scenario_capabilities')
            ->where('scenario_capabilities.scenario_id', $scenarioId)
            ->join('capability_competencies', 'scenario_capabilities.capability_id', '=', 'capability_competencies.capability_id')
            ->pluck('capability_competencies.competency_id')
            ->unique();

        // IDs ya mapeados en este escenario (no perder data existente)
        $mappedCompetencyIds = DB::table('scenario_role_competencies')
            ->where('scenario_id', $scenarioId)
            ->whereNotNull('competency_id')
            ->pluck('competency_id');

        $allRelevantIds = $scenarioCompetencyIds->merge($mappedCompetencyIds)->unique()->values();

        // El JOIN de capability_competencies se restringe a las capabilities
        // del escenario, así cada competencia muestra su pestaña correcta.
        // Las competencias mapeadas por el agente pero sin capability del escenario
        // aparecen bajo 'General'.
        $scenarioCapabilityIds = DB::table('scenario_capabilities')
            ->where('scenario_id', $scenarioId)
            ->pluck('capability_id');

        // Competencias: se muestran las del escenario (y las ya mapeadas).
        // Para capability_name usamos la asociación del catálogo priorizando
        // las capabilities del escenario — si la competencia pertenece a más
        // de una capability, mostramos la del escenario; si no, cualquier otra.
        // Así se evita el fallback a 'General' cuando sí existe asociación.
        $competencies = DB::table('competencies')
            ->leftJoin('capability_competencies', 'competencies.id', '=', 'capability_competencies.competency_id')
            ->leftJoin('capabilities', 'capability_competencies.capability_id', '=', 'capabilities.id')
            ->where('competencies.organization_id', auth()->user()->organization_id)
            ->whereIn('competencies.id', $allRelevantIds->isEmpty()
                ? DB::table('competencies')
                    ->where('organization_id', auth()->user()->organization_id)
                    ->pluck('id')
                : $allRelevantIds
            )
            ->select(
                'competencies.id',
                'competencies.name',
                DB::raw('MIN(capability_competencies.capability_id) as capability_id'),
                DB::raw('COALESCE(MIN(CASE WHEN capability_competencies.capability_id IN ('
                    .implode(',', $scenarioCapabilityIds->toArray() ?: [0])
                    .") THEN capabilities.name END), MIN(capabilities.name), 'General') as capability_name")
            )
            ->groupBy('competencies.id', 'competencies.name')
            ->orderBy('capability_name')
            ->orderBy('competencies.name')
            ->get();

        // Mappings: Qué competencias están asignadas a cada rol.
        // Incluye 'source' para que el frontend muestre 🤖/👤/⚙️ correctamente.
        $mappings = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
            ->with(['role', 'competency:id,name', 'version:id,metadata'])
            ->select('id', 'scenario_id', 'role_id', 'competency_id', 'competency_version_id', 'required_level', 'is_core', 'is_referent', 'change_type', 'rationale', 'source')
            ->get();

        return response()->json([
            'scenario' => [
                'id' => $scenario->id,
                'name' => $scenario->name,
                'horizon_months' => $scenario->time_horizon_weeks ? intdiv($scenario->time_horizon_weeks, 4) : null,
            ],
            'roles' => $roles,
            'competencies' => $competencies,
            'mappings' => $mappings,
        ]);
    }

    /**
     * POST /api/v1/scenarios/{scenarioId}/step2/mappings
     * Crear o actualizar un mapping de rol-competencia
     */
    public function saveMapping(Request $request, int $scenarioId): JsonResponse
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:scenario_role_competencies,id',
            'role_id' => 'required|integer|exists:scenario_roles,id',
            'competency_id' => 'required|integer|exists:competencies,id',
            'required_level' => 'required|integer|between:1,5',
            'is_core' => 'boolean',
            'is_referent' => 'nullable|boolean',
            'change_type' => 'required|string|in:maintenance,transformation,enrichment,extinction',
            'rationale' => 'nullable|string',
            'competency_version_id' => 'nullable|integer|exists:competency_versions,id',
            'current_level' => 'nullable|integer|between:1,5',
            'timeline_months' => 'nullable|integer|in:6,12,18,24',
        ]);

        return DB::transaction(function () use ($scenarioId, $validated, $request) {
            $mapping = ScenarioRoleCompetency::where([
                'scenario_id' => $scenarioId,
                'role_id' => $validated['role_id'],
                'competency_id' => $validated['competency_id'],
            ])->first() ?? new ScenarioRoleCompetency([
                'scenario_id' => $scenarioId,
                'role_id' => $validated['role_id'],
                'competency_id' => $validated['competency_id'],
            ]);

            $mapping->required_level = $validated['required_level'];
            $mapping->is_core = $validated['is_core'] ?? $mapping->is_core ?? false;
            $mapping->is_referent = $validated['is_referent'] ?? $mapping->is_referent ?? false;
            $mapping->change_type = $validated['change_type'];
            $mapping->rationale = $validated['rationale'] ?? $mapping->rationale;

            if ($request->has('competency_version_id')) {
                $mapping->competency_version_id = $validated['competency_version_id'];
            }

            $mapping->save();

            $mapping->load('version:id,metadata');

            // Derivar automáticamente skills basados en esta competencia
            // Usa el método deriveSkillsFromCompetencies del servicio
            $this->derivationService->deriveSkillsFromCompetencies(
                $scenarioId,
                $validated['role_id']
            );

            return response()->json([
                'success' => true,
                'mapping' => $mapping,
                'message' => 'Mapeo guardado y skills actualizados automáticamente',
            ], 201);
        });
    }

    /**
     * DELETE /api/v1/scenarios/{scenarioId}/step2/mappings/{mappingId}
     * Eliminar un mapping de rol-competencia
     */
    public function deleteMapping(int $scenarioId, int $mappingId): JsonResponse
    {
        $mapping = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
            ->where('id', $mappingId)
            ->firstOrFail();

        return DB::transaction(function () use ($mapping, $scenarioId) {
            // Eliminar skills derivados automáticamente de esta competencia
            ScenarioRoleSkill::where('scenario_id', $scenarioId)
                ->where('role_id', $mapping->role_id)
                ->where('competency_id', $mapping->competency_id)
                ->where('source', 'competency')
                ->delete();

            $mapping->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mapeo eliminado y skills relacionados removidos',
            ]);
        });
    }

    /**
     * POST /api/v1/scenarios/{scenarioId}/step2/roles
     * Agregar un nuevo rol al escenario
     */
    public function addRole(Request $request, int $scenarioId): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => 'nullable|integer|exists:roles,id',
            'role_name' => 'nullable|string|max:255',
            'role_description' => 'nullable|string',
            'fte' => 'required|numeric|min:0.1',
            'role_change' => 'required|string|in:create,modify,eliminate,maintain',
            'evolution_type' => 'required|string|in:new_role,upgrade_skills,transformation,downsize,elimination',
            'impact_level' => 'required|string|in:critical,high,medium,low',
            'rationale' => 'nullable|string',
            'cube_dimensions' => 'nullable|array',
            'competencies' => 'nullable|array',
            'ai_archetype_config' => 'nullable|array',
        ]);

        // Si no existe role_id, crear uno nuevo en el catálogo (como propuesta)
        if (empty($validated['role_id'])) {
            $role = Roles::create([
                'organization_id' => auth()->user()->organization_id,
                'name' => $validated['role_name'],
                'description' => $validated['role_description'] ?? null,
                'status' => 'proposed',
                'ai_archetype_config' => $validated['ai_archetype_config'] ?? null,
                'cube_dimensions' => $validated['cube_dimensions'] ?? null,
            ]);
            $roleId = $role->id;
        } else {
            $roleId = $validated['role_id'];
        }

        $scenarioRole = ScenarioRole::updateOrCreate(
            [
                'scenario_id' => $scenarioId,
                'role_id' => $roleId,
            ],
            [
                'fte' => $validated['fte'],
                'role_change' => $validated['role_change'],
                'evolution_type' => $validated['evolution_type'],
                'impact_level' => $validated['impact_level'],
                'rationale' => $validated['rationale'] ?? null,
                'archetype' => $validated['cube_dimensions']['x_archetype'] ?? $validated['ai_archetype_config']['cube_coordinates']['x_archetype'] ?? null,
                'human_leverage' => $validated['cube_dimensions']['human_leverage'] ?? null,
                'ai_suggestions' => $validated['ai_archetype_config'] ?? null,
            ]
        );

        // Si se pasaron competencias, grabarlas en scenario_role_competencies
        if ($request->has('competencies') && is_array($request->competencies)) {
            $orgId = auth()->user()->organization_id;
            $embeddingService = config('features.generate_embeddings', false)
                ? app(EmbeddingService::class)
                : null;

            foreach ($request->competencies as $compData) {
                $competency = null;
                $isNew = false;
                $embeddingVector = null;

                // 1. Similitud Vectorial / Búsqueda Semántica
                if ($embeddingService) {
                    // Vectorizamos la propuesta del agente
                    $textToEmbed = $compData['name'].' | '.($compData['rationale'] ?? '');
                    $embedding = $embeddingService->generate($textToEmbed);

                    if ($embedding) {
                        $embeddingVector = $embeddingService->toVectorString($embedding);

                        // Buscamos similitud matemática en el catálogo de la organización
                        $similar = $embeddingService->findSimilar('competencies', $embedding, 1, $orgId);

                        // Umbral del 90%: Si la similitud matemática es alta, reutilizamos la competencia existente
                        if (! empty($similar) && $similar[0]->similarity >= 0.90) {
                            $competency = Competency::find($similar[0]->id);
                        }
                    }
                }

                // 2. Si no hubo match semántico (o si el modelo vector está apagado), fallback a nombre exacto o catalogar como nueva
                if (! $competency) {
                    $competency = Competency::firstOrCreate(
                        [
                            'name' => $compData['name'],
                            'organization_id' => $orgId,
                        ],
                        [
                            'description' => $compData['rationale'] ?? null,
                            'status' => 'proposed',
                        ]
                    );

                    if ($competency->wasRecentlyCreated) {
                        $isNew = true;
                        // Persistir el vector en la base de datos si logramos generarlo
                        if ($embeddingVector) {
                            DB::statement(
                                'UPDATE competencies SET embedding = ?::vector WHERE id = ?',
                                [$embeddingVector, $competency->id]
                            );
                        }
                    }
                }

                // 3. Impacto Orgánico Inteligente
                // Si la IA ideó algo matemáticamente nuevo para la organización -> 'creation'
                // Si ya existía y solo lo estamos adoptando para el rol -> 'enrichment' por defecto
                $changeType = $isNew ? 'creation' : 'enrichment';

                ScenarioRoleCompetency::updateOrCreate(
                    [
                        'scenario_id' => $scenarioId,
                        'role_id' => $scenarioRole->id,
                        'competency_id' => $competency->id,
                    ],
                    [
                        'required_level' => $compData['level'] ?? 3,
                        'change_type' => $changeType,
                        'rationale' => $compData['rationale'] ?? null,
                        'source' => 'agent',
                    ]
                );
            }
            // Disparar derivación de skills
            $this->derivationService->deriveSkillsFromCompetencies($scenarioId, $scenarioRole->id);
        }

        // Cargar la relación con el rol para obtener el nombre
        $scenarioRole->load('role');

        return response()->json([
            'success' => true,
            'role' => $scenarioRole,
            'message' => $scenarioRole->wasRecentlyCreated
                ? 'Rol agregado al escenario con diseño de Cubo'
                : 'Rol actualizado en el escenario',
        ], $scenarioRole->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * POST /api/v1/scenarios/{scenarioId}/step2/orchestrate-capabilities
     * Analiza las competencias generadas en el Paso 1 y orquesta su empaquetado organizacional.
     * Esta es la vista Capability-First de Stratos.
     */
    public function orchestrateCapabilities(Request $request, int $scenarioId): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        // 1. Obtener las competencias "descubiertas" en este escenario (Paso 1)
        // Ya que el Paso 1 las vinculó tempranamente en scenario_role_competencies
        $competencyIds = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
            ->pluck('competency_id')
            ->unique();

        $scenarioCompetencies = Competency::whereIn('id', $competencyIds)
            ->where('organization_id', $orgId)
            ->get();

        $embeddingService = config('features.generate_embeddings', false)
            ? app(EmbeddingService::class)
            : null;

        $impactedRoles = [];
        $newOrphanCompetencies = [];

        // 2. Filtro Vectorial contra el Catálogo Activo (Organigrama Oficial)
        foreach ($scenarioCompetencies as $comp) {
            $matched = false;

            if ($embeddingService && $comp->embedding) {
                // Postgres vector column values look like "[0.1, 0.2]". json_decode parses it correctly into PHP array
                $embeddingArray = json_decode($comp->embedding, true);

                if (is_array($embeddingArray)) {
                    $similarities = $embeddingService->findSimilar('competencies', $embeddingArray, 5, $orgId);

                    foreach ($similarities as $sim) {
                        if ($sim->id != $comp->id && $sim->similarity >= 0.90) {
                            $matchedComp = Competency::find($sim->id);

                            // Nos interesan las competencias que ya estén activas en el catálogo real de la empresa
                            if ($matchedComp && $matchedComp->status === 'active') {
                                $matched = true;

                                // Esto indica que el escenario exige una competencia oficial.
                                // Quien la tenga actualmente asignada, estará impactado orgánicamente.
                                $impactedRoles[] = [
                                    'official_competency' => $matchedComp->name,
                                    'scenario_capability_name' => $comp->name,
                                    'vector_similarity_score' => round($sim->similarity * 100, 2).'%',
                                    'action_required' => 'upskilling',
                                ];
                                break;
                            }
                        }
                    }
                }
            }

            if (! $matched) {
                $newOrphanCompetencies[] = [
                    'id' => $comp->id,
                    'name' => $comp->name,
                    'rationale' => $comp->description,
                ];
            }
        }

        // 3. Orquestación IA para las competencias Genuninas (Las Huérfanas de Transformación)
        // Agrupamos este paquete (batch) para no fragmentar a la organización con micro-roles.
        $orchestrationPlan = [];
        if (! empty($newOrphanCompetencies)) {
            // Obtenemos una muestra de roles actuales de la organización para darle contexto al Agente
            $candidateRoles = Roles::where('organization_id', $orgId)
                ->where('status', 'active')
                ->limit(20)
                ->pluck('name')
                ->toArray();

            $roleDesigner = app(RoleDesignerService::class);
            $orchestrationPlan = $roleDesigner->bundleNewCapabilities($newOrphanCompetencies, $candidateRoles);
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'total_capabilities_from_step1' => count($scenarioCompetencies),
                'existing_capabilities_matched' => count($impactedRoles),
                'new_alien_capabilities' => count($newOrphanCompetencies),
            ],
            'organic_impact' => $impactedRoles, // El Balde Verde
            'ai_orchestration' => $orchestrationPlan, // El Balde Amarillo (Role Bundling propuesto)
        ]);
    }

    /**
     * GET /api/v1/scenarios/{scenarioId}/step2/role-forecasts
     * Pronósticos de roles (FTE actual vs futuro)
     */
    public function getRoleForecasts(int $scenarioId): JsonResponse
    {
        Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        // Obtener FTE actual real desde la tabla de personas de la organización
        $actualHeadcounts = People::where('organization_id', auth()->user()->organization_id)
            ->select('role_id', DB::raw('count(*) as count'))
            ->groupBy('role_id')
            ->pluck('count', 'role_id');

        $forecasts = ScenarioRole::where('scenario_id', $scenarioId)
            ->join('roles', 'scenario_roles.role_id', '=', 'roles.id')
            ->select(
                'scenario_roles.id',
                'scenario_roles.role_id',
                self::ROLE_NAME_SELECT,
                'scenario_roles.fte as fte_future',
                'scenario_roles.evolution_type',
                'scenario_roles.impact_level',
                'scenario_roles.rationale'
            )
            ->get()
            ->map(function ($forecast) use ($actualHeadcounts) {
                // Usar el conteo real de personas con este rol
                $forecast->fte_current = (float) ($actualHeadcounts[$forecast->role_id] ?? 0);
                $forecast->fte_delta = $forecast->fte_future - $forecast->fte_current;

                return $forecast;
            });

        return response()->json([
            'data' => $forecasts,
        ]);
    }

    /**
     * GET /api/v1/scenarios/{scenarioId}/step2/skill-gaps-matrix
     * Matriz de brechas: Skills × Roles
     */
    public function getSkillGapsMatrix(int $scenarioId): JsonResponse
    {
        Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        $roles = ScenarioRole::where('scenario_id', $scenarioId)
            ->join('roles', 'scenario_roles.role_id', '=', 'roles.id')
            ->select('scenario_roles.id', 'roles.name', 'scenario_roles.fte')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => (int) $role->id,
                    'name' => $role->name,
                    'fte' => (float) $role->fte,
                ];
            });

        // Skills requeridos en escenario (vía competencias)
        $skills = Skill::join('competency_skills', 'skills.id', '=', 'competency_skills.skill_id')
            ->join('scenario_role_competencies', 'competency_skills.competency_id', '=', 'scenario_role_competencies.competency_id')
            ->join('competencies', 'competency_skills.competency_id', '=', 'competencies.id')
            ->where('scenario_role_competencies.scenario_id', $scenarioId)
            ->select(
                'skills.id',
                'skills.name',
                'competencies.name as competency_name'
            )
            ->get()
            ->groupBy('id')
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'id' => (int) $first->id,
                    'name' => $first->name,
                    'competency_name' => $group->pluck('competency_name')->unique()->implode(', '),
                ];
            })
            ->values()
            ->sortBy('name')
            ->values();

        // Brechas: required_level vs current_level
        $gaps = ScenarioRoleSkill::where('scenario_role_skills.scenario_id', $scenarioId)
            ->join('scenario_roles', 'scenario_role_skills.role_id', '=', 'scenario_roles.id')
            ->join('roles', 'scenario_roles.role_id', '=', 'roles.id')
            ->select(
                'scenario_role_skills.skill_id',
                'scenario_role_skills.role_id',
                'roles.name as role_name',
                'scenario_role_skills.required_level',
                'scenario_role_skills.current_level'
            )
            ->get()
            ->groupBy(fn ($gap) => $gap->skill_id.'-'.$gap->role_id)
            ->map(function ($group) {
                $first = $group->first();
                $req = (int) $group->max('required_level');
                $curr = (int) $group->max('current_level');

                return [
                    'skill_id' => (int) $first->skill_id,
                    'role_id' => (int) $first->role_id,
                    'role_name' => $first->role_name,
                    'current_level' => $curr,
                    'required_level' => $req,
                    'gap' => $req - $curr,
                ];
            })
            ->values();

        return response()->json([
            'roles' => $roles,
            'skills' => $skills,
            'gaps' => $gaps,
        ]);
    }

    /**
     * GET /api/scenarios/{scenarioId}/step2/matching-results
     * Resultados de matching candidato-posición
     */
    public function getMatchingResults(int $scenarioId): JsonResponse
    {
        $this->validateScenario($scenarioId);

        $scenarioRoles = ScenarioRole::where('scenario_id', $scenarioId)->with('role')->get();

        if ($scenarioRoles->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $people = People::where('organization_id', auth()->user()->organization_id)
            ->with(['role', 'activeSkills'])
            ->get();

        $results = $scenarioRoles->flatMap(function ($sRole) use ($scenarioId, $people) {
            $requiredSkills = ScenarioRoleSkill::where('scenario_role_skills.scenario_id', $scenarioId)
                ->where('scenario_role_skills.role_id', $sRole->id) // Corregido: usar id de scenario_roles
                ->join('skills', 'scenario_role_skills.skill_id', '=', 'skills.id')
                ->select('scenario_role_skills.*', 'skills.name as skill_name')
                ->get();

            if ($requiredSkills->isEmpty()) {
                return [];
            }

            return $people->map(function ($p) use ($requiredSkills, $sRole) {
                $matchData = $this->calculatePersonMatch($p, $requiredSkills);

                if ($matchData['match_percentage'] < 40) {
                    return null;
                }

                return [
                    'id' => $p->id.'-'.$sRole->id,
                    'candidate_name' => $p->full_name,
                    'current_role' => $p->role?->name ?? 'Sin Rol',
                    'target_position' => $sRole->role->name,
                    'match_percentage' => $matchData['match_percentage'],
                    'risk_factors' => $matchData['match_percentage'] < 70 ? [['id' => 1, 'factor' => 'Brecha de competencias moderada']] : [],
                    'productivity_timeline' => max(1, $matchData['total_required'] - $matchData['met_skills']),
                    'skill_gaps' => $matchData['skill_gaps'],
                    'notes' => 'Matching basado en el diseño del escenario.',
                ];
            })->filter();
        })->sortByDesc('match_percentage')->values();

        return response()->json(['data' => $results]);
    }

    /**
     * GET /api/v1/scenarios/{scenarioId}/step2/succession-plans
     * Planes de sucesión
     */
    public function getSuccessionPlans(int $scenarioId): JsonResponse
    {
        $this->validateScenario($scenarioId);

        $criticalRoles = ScenarioRole::where('scenario_id', $scenarioId)
            ->whereIn('impact_level', ['critical', 'high'])
            ->with('role')
            ->get();

        if ($criticalRoles->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $people = People::where('organization_id', auth()->user()->organization_id)
            ->with(['role', 'activeSkills'])
            ->get();

        $plans = $criticalRoles->map(function ($sRole) use ($scenarioId, $people) {
            $requiredSkills = ScenarioRoleSkill::where('scenario_id', $scenarioId)
                ->where('role_id', $sRole->id) // Corregido: usar ID de scenario_roles
                ->get();

            if ($requiredSkills->isEmpty()) {
                return null;
            }

            return $this->calculateRoleSuccessionPlan($sRole, $people, $requiredSkills);
        })->filter()->values();

        return response()->json(['data' => $plans]);
    }

    private function validateScenario(int $scenarioId): void
    {
        Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();
    }

    private function calculatePersonMatch(People $person, $requiredSkills): array
    {
        $metSkills = 0;
        $totalRequired = $requiredSkills->count();
        $skillGaps = [];

        foreach ($requiredSkills as $req) {
            $pSkill = $person->activeSkills->firstWhere('skill_id', $req->skill_id);
            $currentLevel = $pSkill ? (float) $pSkill->current_level : 0.0;
            $requiredLevel = (float) $req->required_level;

            if ($requiredLevel > 0) {
                // Contribución proporcional (máximo 1.0 por skill)
                $contribution = min(1.0, $currentLevel / $requiredLevel);
                $metSkills += $contribution;
            } else {
                // Si no requiere nivel, cualquier cosa es 100%
                $metSkills += 1.0;
            }

            $gap = max(0, (int) $requiredLevel - (int) $currentLevel);
            if ($gap > 0) {
                $skillGaps[] = [
                    'id' => $req->id,
                    'skill_name' => $req->skill_name ?? ($req->skill ? $req->skill->name : 'Unknown Skill'),
                    'current_level' => $currentLevel,
                    'required_level' => $requiredLevel,
                ];
            }
        }

        $matchPercentage = $totalRequired > 0 ? ($metSkills / $totalRequired) * 100 : 0;

        return [
            'match_percentage' => round($matchPercentage, 0),
            'met_skills' => $metSkills,
            'total_required' => $totalRequired,
            'skill_gaps' => $skillGaps,
        ];
    }

    private function calculateRoleSuccessionPlan($sRole, $people, $requiredSkills): array
    {
        $currentHolder = $people->firstWhere('role_id', $sRole->role_id);

        $potentialSuccessors = $people
            ->reject(fn ($p) => $currentHolder && $p->id === $currentHolder->id)
            ->map(function ($p) use ($requiredSkills) {
                $match = $this->calculatePersonMatch($p, $requiredSkills);

                if ($match['match_percentage'] < 60) {
                    return null;
                }

                return [
                    'id' => $p->id,
                    'name' => $p->full_name,
                    'readiness_percentage' => $match['match_percentage'],
                    'readiness_level' => $this->getReadinessLevel($match['match_percentage']),
                    'current_role' => $p->role?->name ?? 'Sin Rol',
                    'skill_gaps' => $match['skill_gaps'],
                    'timeline_months' => $this->calculateTimeline($match['match_percentage']),
                ];
            })
            ->filter()
            ->sortByDesc('readiness_percentage')
            ->values();

        return [
            'id' => $sRole->id,
            'position_name' => $sRole->role->name,
            'role_name' => $sRole->role->name, // Keep for tests
            'department' => $sRole->role->department ?? 'N/A',
            'criticality' => $sRole->impact_level,
            'criticality_level' => $sRole->impact_level, // Keep for tests
            'current_holder_name' => $currentHolder ? $currentHolder->full_name : 'Vacante / Externo',
            'current_holder' => $currentHolder ? $currentHolder->full_name : 'Vacante / Externo', // Keep for tests
            'current_holder_age' => 45, // Placeholder
            'years_in_position' => $currentHolder && $currentHolder->hire_date ? $currentHolder->hire_date->diffInYears(now()) : 2,
            'estimated_retirement' => '2028-12-31',
            'successors' => $potentialSuccessors,
            'primary_successor' => $potentialSuccessors->first(), // Keep for tests
            'secondary_successors' => $potentialSuccessors->slice(1, 2)->values(), // Keep for tests
            'notes' => $sRole->rationale,
        ];
    }

    private function calculateTimeline(int $percentage): int
    {
        if ($percentage >= 90) {
            return 0;
        }
        if ($percentage >= 75) {
            return 12;
        }

        return 24;
    }

    private function getReadinessLevel(int $percentage): string
    {
        $level = 'not_ready';
        if ($percentage >= 90) {
            $level = 'ready_now';
        } elseif ($percentage >= 75) {
            $level = 'ready_12_months';
        } elseif ($percentage >= 60) {
            $level = 'ready_24_months';
        }

        return $level;
    }

    /**
     * GET /api/scenarios/{scenarioId}/step2/cube
     * Datos para el Cubo de Ingeniería Organizacional al final del Paso 2.
     * Lee de scenario_roles (datos ya consolidados en la matriz).
     */
    public function getCubeData(int $scenarioId): JsonResponse
    {
        $orgId = auth()->user()->organization_id;

        Scenario::where('id', $scenarioId)
            ->where('organization_id', $orgId)
            ->firstOrFail();

        // 1. Capabilities del escenario
        $capabilities = DB::table('scenario_capabilities')
            ->join('capabilities', 'scenario_capabilities.capability_id', '=', 'capabilities.id')
            ->where('scenario_capabilities.scenario_id', $scenarioId)
            ->select(
                'capabilities.id',
                'capabilities.name',
                DB::raw("COALESCE(capabilities.category, 'Core Business') as category"),
            )
            ->orderBy('capabilities.name')
            ->get();

        // 2. Roles del escenario con datos del Cubo
        // Incluye ai_suggestions para exponer la propuesta del agente
        // (coordenadas del cubo: archetype, mastery_level, business_process)
        $scenarioRoles = DB::table('scenario_roles')
            ->join('roles', 'scenario_roles.role_id', '=', 'roles.id')
            ->where('scenario_roles.scenario_id', $scenarioId)
            ->select(
                'scenario_roles.id as sr_id',
                'roles.id as role_id',
                'roles.name as role_name',
                'roles.description as role_description',
                'scenario_roles.archetype',
                'scenario_roles.fte',
                'scenario_roles.human_leverage',
                'scenario_roles.role_change',
                'scenario_roles.rationale',
                'scenario_roles.ai_suggestions',   // propuesta del agente (cubo + justificación)
            )
            ->orderBy('roles.name')
            ->get();

        // 3. Competencias por rol (con capability del escenario)
        // IMPORTANTE: leftJoin con scenario_capabilities directamente para
        // garantizar que capability_id sea de las capabilities del escenario.
        // Sin este filtro, una competencia que pertenece a múltiples capabilities
        // en el catálogo puede aparecer asociada a una capability incorrecta.
        $mappings = DB::table('scenario_role_competencies')
            ->join('competencies', 'scenario_role_competencies.competency_id', '=', 'competencies.id')
            ->leftJoin('capability_competencies', 'competencies.id', '=', 'capability_competencies.competency_id')
            ->leftJoin('scenario_capabilities', function ($join) use ($scenarioId) {
                $join->on('capability_competencies.capability_id', '=', 'scenario_capabilities.capability_id')
                    ->where('scenario_capabilities.scenario_id', '=', $scenarioId);
            })
            ->where('scenario_role_competencies.scenario_id', $scenarioId)
            ->select(
                'scenario_role_competencies.role_id',
                'competencies.id as competency_id',
                'competencies.name as competency_name',
                'scenario_role_competencies.required_level',
                'scenario_role_competencies.change_type',
                // Usar el capability_id filtrado por el escenario (NULL si no pertenece al escenario)
                DB::raw('CASE WHEN scenario_capabilities.capability_id IS NOT NULL THEN scenario_capabilities.capability_id ELSE NULL END as capability_id'),
            )
            ->distinct()
            ->get()
            ->groupBy('role_id');

        // 4. Construir respuesta
        $rolesData = $scenarioRoles->map(function ($sr) use ($mappings) {
            $roleMappings = $mappings->get($sr->role_id, collect());
            $keyCompetencies = $roleMappings->map(fn ($m) => [
                'name' => $m->competency_name,
                'level' => $m->required_level ?? 3,
                'change_type' => $m->change_type,
                'capability_id' => $m->capability_id,
            ])->values()->toArray();

            // Decodificar ai_suggestions (propuesta del agente) si viene como JSON string
            $aiSuggestions = $sr->ai_suggestions;
            if (is_string($aiSuggestions)) {
                $aiSuggestions = json_decode($aiSuggestions, true) ?? [];
            }

            // Extraer coordenadas del cubo de la propuesta del agente
            $cubeCoordinates = $aiSuggestions['cube_coordinates'] ?? null;

            return [
                'id' => $sr->sr_id,
                'role_id' => $sr->role_id,
                'role_name' => $sr->role_name,
                'role_description' => $sr->role_description ?? '',
                'archetype' => $sr->archetype ?? 'O',
                'fte' => (float) ($sr->fte ?? 1.0),
                'human_leverage' => (int) ($sr->human_leverage ?? 50),
                'role_change' => $sr->role_change ?? 'modify',
                'rationale' => $sr->rationale ?? '',
                // Propuesta del agente: coordenadas del cubo y justificación
                'cube_coordinates' => $cubeCoordinates,
                'ai_suggestions' => $aiSuggestions,
                'key_competencies' => $keyCompetencies,
                'similarity_warnings' => [],
            ];
        });

        return response()->json([
            'data' => [
                'capabilities' => $capabilities->values(),
                'roles' => $rolesData->values(),
                'competencies' => [],
            ],
        ]);
    }

    /**
     * POST /api/scenarios/{scenarioId}/step2/approve-cube
     * Promote roles and items from incubation to engineering/active phase.
     */
    public function approveCube(Request $request, int $scenarioId): JsonResponse
    {
        $selection = $request->input('selection', []);

        $scenario = Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        $promotedCount = 0;
        $roleIdsInScenarioRole = [];

        foreach ($selection as $itemKey) {
            if (str_contains($itemKey, 'role:')) {
                $roleIdsInScenarioRole[] = (int) str_replace('role:', '', $itemKey);
            }
        }

        if (empty($roleIdsInScenarioRole)) {
            return response()->json([
                'success' => false,
                'message' => 'No hay roles seleccionados para promover.',
            ], 400);
        }

        try {
            DB::transaction(function () use ($scenarioId, $roleIdsInScenarioRole, &$promotedCount) {
                // Obtener los roles reales (de la tabla 'roles') vinculados a estos ScenarioRoles
                $scenarioRoles = ScenarioRole::where('scenario_id', $scenarioId)
                    ->whereIn('id', $roleIdsInScenarioRole)
                    ->get();

                $realRoleIds = $scenarioRoles->pluck('role_id')->unique();

                // 1. Promover roles a 'active'
                Roles::whereIn('id', $realRoleIds)
                    ->update(['status' => 'active']);

                // 2. Promover competencias vinculadas a esos roles si son nuevas ('proposed')
                // Nota: scenario_role_competencies.role_id apunta a scenario_roles.id
                $newCompetencyIds = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
                    ->whereIn('role_id', $roleIdsInScenarioRole)
                    ->pluck('competency_id')
                    ->unique();

                Competency::whereIn('id', $newCompetencyIds)
                    ->where('status', 'proposed')
                    ->update(['status' => 'active']);

                $promotedCount = $scenarioRoles->count();
            });

            return response()->json([
                'success' => true,
                'message' => "{$promotedCount} roles promovidos exitosamente a la fase de ingeniería.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al promover los elementos: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/scenarios/{scenarioId}/step2/engine/generate-bars
     */
    public function generateBars(Request $request, int $scenarioId): JsonResponse
    {
        $validated = $request->validate([
            'role_name' => 'required|string',
            'competency_name' => 'required|string',
            'required_level' => 'required|integer|between:1,5',
            'archetype' => 'required|string|in:E,T,O',
        ]);

        try {
            $bars = $this->orchestrator->generateEngineeringBlueprint(
                $scenarioId,
                $validated['role_name'],
                $validated['competency_name'],
                $validated['required_level'],
                $validated['archetype']
            );

            return response()->json(['success' => true, 'bars' => $bars]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
