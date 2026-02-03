<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioRoleSkill;
use App\Models\PersonRoleSkill;
use App\Services\RoleSkillDerivationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
    public function __construct(
        private RoleSkillDerivationService $derivationService
    ) {
    }

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
            ->select('scenario_roles.id', 'scenario_roles.role_id', 'roles.name as role_name', 'scenario_roles.fte', 'scenario_roles.role_change', 'scenario_roles.impact_level', 'scenario_roles.evolution_type', 'scenario_roles.rationale')
            ->get();

        // Competencias disponibles (sin filtro de escenario, son globales)
        $competencies = \App\Models\Competency::where('organization_id', auth()->user()->organization_id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Mappings: Qué competencias están asignadas a cada rol
        $mappings = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
            ->with(['role:id,name', 'competency:id,name'])
            ->select('id', 'scenario_id', 'role_id', 'competency_id', 'required_level', 'is_core', 'change_type', 'rationale')
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
            'change_type' => 'required|string|in:maintenance,transformation,enrichment,extinction',
            'rationale' => 'nullable|string',
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
                    'change_type' => $validated['change_type'],
                    'rationale' => $validated['rationale'],
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

        $scenarioRole = ScenarioRole::create([
            'scenario_id' => $scenarioId,
            'role_id' => $roleId,
            'fte' => $validated['fte'],
            'role_change' => $validated['role_change'],
            'evolution_type' => $validated['evolution_type'],
            'impact_level' => $validated['impact_level'],
            'rationale' => $validated['rationale'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'role' => $scenarioRole,
            'message' => 'Rol agregado al escenario',
        ], 201);
    }

    /**
     * GET /api/v1/scenarios/{scenarioId}/step2/role-forecasts
     * Pronósticos de roles (FTE actual vs futuro)
     */
    public function getRoleForecasts(int $scenarioId): JsonResponse
    {
        $scenario = Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        $forecasts = ScenarioRole::where('scenario_id', $scenarioId)
            ->join('roles', 'scenario_roles.role_id', '=', 'roles.id')
            ->select(
                'scenario_roles.id',
                'scenario_roles.role_id',
                'roles.name as role_name',
                'scenario_roles.fte as fte_future',
                'scenario_roles.evolution_type',
                'scenario_roles.impact_level',
                'scenario_roles.rationale'
            )
            ->get()
            ->map(function ($forecast) {
                // Obtener FTE actual (aproximado: usar el promedio de equipos actuales)
                // En la práctica, esto vendría de person_role_skills o un registro histórico
                $forecast->fte_current = $forecast->fte_future; // Placeholder
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
        $scenario = Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        // Roles en escenario
        $roles = ScenarioRole::where('scenario_id', $scenarioId)
            ->join('roles', 'scenario_roles.role_id', '=', 'roles.id')
            ->select('scenario_roles.id', 'roles.name', 'scenario_roles.fte')
            ->get();

        // Skills requeridos en escenario (vía competencias)
        $skills = \App\Models\Skill::join('competency_skills', 'skills.id', '=', 'competency_skills.skill_id')
            ->join('scenario_role_competencies', 'competency_skills.competency_id', '=', 'scenario_role_competencies.competency_id')
            ->where('scenario_role_competencies.scenario_id', $scenarioId)
            ->distinct()
            ->select('skills.id', 'skills.name', 'competency_skills.competency_id')
            ->with('competency:id,name')
            ->get();

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
            ->map(function ($gap) {
                $gap->gap = $gap->required_level - ($gap->current_level ?? 0);
                return $gap;
            });

        return response()->json([
            'roles' => $roles,
            'skills' => $skills,
            'gaps' => $gaps,
        ]);
    }

    /**
     * GET /api/v1/scenarios/{scenarioId}/step2/matching-results
     * Resultados de matching candidato-posición
     */
    public function getMatchingResults(int $scenarioId): JsonResponse
    {
        $scenario = Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        // En la práctica, esto vendría de un cálculo de matching
        // Para MVP, retornar estructura vacía
        $results = collect();

        return response()->json([
            'data' => $results,
        ]);
    }

    /**
     * GET /api/v1/scenarios/{scenarioId}/step2/succession-plans
     * Planes de sucesión
     */
    public function getSuccessionPlans(int $scenarioId): JsonResponse
    {
        $scenario = Scenario::where('id', $scenarioId)
            ->where('organization_id', auth()->user()->organization_id)
            ->firstOrFail();

        // En la práctica, esto vendría de análisis de sucesión
        // Para MVP, retornar estructura vacía
        $plans = collect();

        return response()->json([
            'data' => $plans,
        ]);
    }
}
