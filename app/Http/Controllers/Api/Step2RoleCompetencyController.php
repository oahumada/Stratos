<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\People;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioRoleSkill;
use App\Services\RoleSkillDerivationService;
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
        private RoleSkillDerivationService $derivationService
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

        // Competencias disponibles (sin filtro de escenario, son globales)
        // Incluimos `category` para que el frontend pueda agrupar por capacidad
        // Obtener competencias junto con la capacidad (capability) asociada
        // para agrupar en el frontend por la capacidad del pivote `capability_competencies`.
        // Nota: una competencia puede estar asociada a varias capacidades; la devolveremos
        // una fila por asociación (competency + capability).
        $competencies = \DB::table('competencies')
            ->join('capability_competencies', 'competencies.id', '=', 'capability_competencies.competency_id')
            ->join('capabilities', 'capability_competencies.capability_id', '=', 'capabilities.id')
            ->where('competencies.organization_id', auth()->user()->organization_id)
            ->select(
                'competencies.id',
                'competencies.name',
                'capability_competencies.capability_id',
                \DB::raw("COALESCE(capabilities.name, 'General') as capability_name")
            )
            ->orderBy('capability_name')
            ->orderBy('competencies.name')
            ->get();

        // Mappings: Qué competencias están asignadas a cada rol
        $mappings = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
            ->with(['role:id,name', 'competency:id,name'])
            ->select('id', 'scenario_id', 'role_id', 'competency_id', 'competency_version_id', 'required_level', 'is_core', 'is_referent', 'change_type', 'rationale')
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

        return DB::transaction(function () use ($scenarioId, $validated) {
            $mapping = ScenarioRoleCompetency::updateOrCreate(
                [
                    'scenario_id' => $scenarioId,
                    'role_id' => $validated['role_id'],
                    'competency_id' => $validated['competency_id'],
                ],
                [
                    'required_level' => $validated['required_level'],
                    'is_core' => $validated['is_core'] ?? false,
                    'is_referent' => $validated['is_referent'] ?? false,
                    'change_type' => $validated['change_type'],
                    'rationale' => $validated['rationale'],
                    'competency_version_id' => $validated['competency_version_id'] ?? null,
                ]
            );

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
            'fte' => 'required|numeric|min:0.1',
            'role_change' => 'required|string|in:create,modify,eliminate,maintain',
            'evolution_type' => 'required|string|in:new_role,upgrade_skills,transformation,downsize,elimination',
            'impact_level' => 'required|string|in:critical,high,medium,low',
            'rationale' => 'nullable|string',
        ]);

        // Si no existe role_id, crear uno nuevo
        if (empty($validated['role_id'])) {
            $role = \App\Models\Roles::create([
                'organization_id' => auth()->user()->organization_id,
                'name' => $validated['role_name'],
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
            ]
        );

        // Cargar la relación con el rol para obtener el nombre
        $scenarioRole->load('role');

        // Formatear la respuesta para que coincida con el formato esperado por el frontend
        $roleData = [
            'id' => $scenarioRole->id,
            'scenario_id' => $scenarioRole->scenario_id,
            'role_id' => $scenarioRole->role_id,
            'role_name' => $scenarioRole->role->name,
            'fte' => $scenarioRole->fte,
            'role_change' => $scenarioRole->role_change,
            'evolution_type' => $scenarioRole->evolution_type,
            'impact_level' => $scenarioRole->impact_level,
            'rationale' => $scenarioRole->rationale,
            'human_leverage' => $scenarioRole->human_leverage,
            'archetype' => $scenarioRole->archetype,
        ];

        return response()->json([
            'success' => true,
            'role' => $roleData,
            'message' => $scenarioRole->wasRecentlyCreated
                ? 'Rol agregado al escenario'
                : 'Rol actualizado en el escenario',
        ], $scenarioRole->wasRecentlyCreated ? 201 : 200);
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
        $skills = \App\Models\Skill::join('competency_skills', 'skills.id', '=', 'competency_skills.skill_id')
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
            ->groupBy(fn ($gap) => $gap->skill_id . '-' . $gap->role_id)
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
                ->where('role_id', $sRole->role_id)
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
            $currentLevel = $pSkill ? (int) $pSkill->current_level : 0;
            $requiredLevel = (int) $req->required_level;
            $gap = max(0, $requiredLevel - $currentLevel);

            if ($gap === 0) {
                $metSkills++;
            } else {
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
            ->reject(fn($p) => $currentHolder && $p->id === $currentHolder->id)
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
}
