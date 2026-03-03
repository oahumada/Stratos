<?php

namespace App\Services\Scenario;

use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Roles;
use App\Services\Intelligence\StratosIntelService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CareerPathService — Cierra C3 de Fase 3 del Roadmap.
 *
 * Algoritmos de trayectoria sobre el grafo organizacional.
 * Calcula rutas óptimas de carrera basándose en:
 * - Skills actuales vs requeridas por roles objetivo
 * - Proximidad de skills (transferibilidad)
 * - Historial de movimientos exitosos
 * - Gaps y tiempo estimado de cierre
 */
class CareerPathService
{
    protected StratosIntelService $intel;

    public function __construct(StratosIntelService $intel)
    {
        $this->intel = $intel;
    }

    /**
     * Calcula las rutas de carrera viables para una persona.
     */
    public function calculateCareerPaths(int $peopleId, int $maxPaths = 5): array
    {
        $person = People::with(['role.skills', 'skills'])->findOrFail($peopleId);
        $currentSkills = $this->getCurrentSkillMap($peopleId);

        // Obtener todos los roles de la organización
        $allRoles = Roles::where('organization_id', $person->organization_id)
            ->with('skills')
            ->where('id', '!=', $person->role_id)
            ->get();

        // Calcular compatibilidad para cada rol
        $pathCandidates = $allRoles->map(function ($targetRole) use ($currentSkills, $person) {
            return $this->evaluateRoleFit($person, $targetRole, $currentSkills);
        })
        ->filter(fn ($candidate) => $candidate['match_score'] > 20) // Filtrar matches muy bajos
        ->sortByDesc('match_score')
        ->take($maxPaths)
        ->values();

        return [
            'person' => [
                'id' => $person->id,
                'name' => $person->full_name ?? $person->name,
                'current_role' => $person->role->name ?? 'Sin rol',
                'current_skills_count' => count($currentSkills),
            ],
            'career_paths' => $pathCandidates->toArray(),
            'quick_wins' => $this->identifyQuickWins($currentSkills, $pathCandidates),
            'recommended_path' => $pathCandidates->first(),
        ];
    }

    /**
     * Calcula la ruta óptima entre dos roles (stepping stones).
     */
    public function calculateOptimalRoute(int $fromRoleId, int $toRoleId): array
    {
        $fromRole = Roles::with('skills')->findOrFail($fromRoleId);
        $toRole = Roles::with('skills')->findOrFail($toRoleId);

        $fromSkills = $fromRole->skills->pluck('id')->toArray();
        $toSkills = $toRole->skills->pluck('id')->toArray();

        // Skills en común (transferibles)
        $commonSkills = array_intersect($fromSkills, $toSkills);
        $newSkillsNeeded = array_diff($toSkills, $fromSkills);

        // --- PHASE 3: NEO4J GRAPH TRAVERSAL INTEGRATION ---
        $graphRoute = $this->intel->performPathfinding($fromRoleId, $toRoleId, $fromRole->organization_id);
        
        if ($graphRoute && !empty($graphRoute['path'])) {
            Log::info("Using Neo4j Knowledge Graph for route calculation", ['from' => $fromRoleId, 'to' => $toRoleId]);
            return [
                'from_role' => $fromRole->name,
                'to_role' => $toRole->name,
                'transferability_score' => $graphRoute['total_similarity'] * 100,
                'skills_transferable' => count($commonSkills),
                'new_skills_needed' => count($newSkillsNeeded),
                'estimated_transition_months' => $this->estimateTransitionTime(count($newSkillsNeeded)),
                'difficulty' => $graphRoute['difficulty'],
                'stepping_stones' => array_slice($graphRoute['path'], 1, -1),
                'direct_path_feasible' => $graphRoute['total_similarity'] >= 0.6,
                'route' => $graphRoute['path'],
                'engine' => 'Neo4j (Knowledge Graph)'
            ];
        }

        // --- SQL FALLBACK ---
        // Buscar roles intermedios (stepping stones) via SQL
        $steppingStones = $this->findSteppingStones($fromSkills, $toSkills, $fromRole->organization_id);

        $totalGap = count($newSkillsNeeded);
        $transferability = count($fromSkills) > 0
            ? round((count($commonSkills) / count($fromSkills)) * 100)
            : 0;

        return [
            'from_role' => $fromRole->name,
            'to_role' => $toRole->name,
            'transferability_score' => $transferability,
            'skills_transferable' => count($commonSkills),
            'new_skills_needed' => count($newSkillsNeeded),
            'estimated_transition_months' => $this->estimateTransitionTime($totalGap),
            'stepping_stones' => $steppingStones,
            'direct_path_feasible' => $transferability >= 60,
            'route' => $this->buildRouteSteps($fromRole, $toRole, $steppingStones, $newSkillsNeeded),
            'engine' => 'SQL Fallback (Relational)'
        ];
    }

    /**
     * Genera el mapa de movilidad organizacional completo.
     */
    public function generateMobilityMap(int $organizationId): array
    {
        $roles = Roles::where('organization_id', $organizationId)
            ->with('skills')
            ->get();

        $mobilityMatrix = [];

        foreach ($roles as $fromRole) {
            foreach ($roles as $toRole) {
                if ($fromRole->id === $toRole->id) continue;

                $fromSkills = $fromRole->skills->pluck('id')->toArray();
                $toSkills = $toRole->skills->pluck('id')->toArray();
                $common = count(array_intersect($fromSkills, $toSkills));
                $total = count(array_unique(array_merge($fromSkills, $toSkills)));

                $similarity = $total > 0 ? round(($common / $total) * 100) : 0;

                if ($similarity > 25) { // Solo incluir conexiones relevantes
                    $mobilityMatrix[] = [
                        'from_role_id' => $fromRole->id,
                        'from_role' => $fromRole->name,
                        'to_role_id' => $toRole->id,
                        'to_role' => $toRole->name,
                        'similarity' => $similarity,
                        'feasibility' => $similarity >= 60 ? 'high' : ($similarity >= 40 ? 'medium' : 'low'),
                    ];
                }
            }
        }

        // Ordenar por afinidad
        usort($mobilityMatrix, fn ($a, $b) => $b['similarity'] <=> $a['similarity']);

        return [
            'organization_id' => $organizationId,
            'total_roles' => $roles->count(),
            'total_viable_transitions' => count($mobilityMatrix),
            'mobility_connections' => array_slice($mobilityMatrix, 0, 50),
            'mobility_index' => $this->calculateMobilityIndex($mobilityMatrix, $roles->count()),
        ];
    }

    /**
     * Predice la probabilidad de éxito para un movimiento de carrera.
     */
    public function predictTransitionSuccess(int $peopleId, int $targetRoleId): array
    {
        $person = People::with(['role', 'skills'])->findOrFail($peopleId);
        $targetRole = Roles::with('skills')->findOrFail($targetRoleId);
        $currentSkills = $this->getCurrentSkillMap($peopleId);

        $evaluation = $this->evaluateRoleFit($person, $targetRole, $currentSkills);

        // Factores adicionales de predicción
        $tenureYears = $person->hired_at ? now()->diffInYears($person->hired_at) : 0;
        $hasActiveDevelopment = DB::table('development_paths')
            ->where('people_id', $peopleId)
            ->where('status', 'active')
            ->exists();

        $tenureBonus = min(10, $tenureYears * 2); // Max 10 puntos por tenure
        $developmentBonus = $hasActiveDevelopment ? 10 : 0;

        $successProbability = min(100, $evaluation['match_score'] + $tenureBonus + $developmentBonus);

        return [
            'person' => $person->full_name ?? $person->name,
            'target_role' => $targetRole->name,
            'success_probability' => $successProbability,
            'match_score' => $evaluation['match_score'],
            'factors' => [
                'skill_match' => $evaluation['match_score'],
                'tenure_bonus' => $tenureBonus,
                'development_activity_bonus' => $developmentBonus,
            ],
            'gaps_to_close' => $evaluation['gaps'],
            'estimated_preparation_months' => $evaluation['estimated_months'],
            'recommendation' => $successProbability >= 70
                ? 'RECOMMENDED — Alta probabilidad de transición exitosa'
                : ($successProbability >= 50
                    ? 'VIABLE — Requiere programa de desarrollo previo'
                    : 'NOT RECOMMENDED — Gap demasiado grande, considerar stepping stones'),
        ];
    }

    // ── Helpers ──────────────────────────────────────────────

    protected function getCurrentSkillMap(int $peopleId): array
    {
        return PeopleRoleSkills::where('people_id', $peopleId)
            ->pluck('current_level', 'skill_id')
            ->toArray();
    }

    protected function evaluateRoleFit(People $person, Roles $targetRole, array $currentSkills): array
    {
        $requiredSkills = $targetRole->skills;
        $totalRequired = $requiredSkills->count();

        if ($totalRequired === 0) {
            return [
                'role_id' => $targetRole->id,
                'role_name' => $targetRole->name,
                'match_score' => 0,
                'gaps' => [],
                'estimated_months' => 0,
            ];
        }

        $matchedSkills = 0;
        $totalGap = 0;
        $gaps = [];

        foreach ($requiredSkills as $requiredSkill) {
            $currentLevel = $currentSkills[$requiredSkill->id] ?? 0;
            $requiredLevel = $requiredSkill->pivot->required_level ?? $requiredSkill->pivot->level ?? 3;
            $gap = max(0, $requiredLevel - $currentLevel);

            if ($gap === 0) {
                $matchedSkills++;
            } else {
                $gaps[] = [
                    'skill' => $requiredSkill->name,
                    'current' => $currentLevel,
                    'required' => $requiredLevel,
                    'gap' => $gap,
                ];
                $totalGap += $gap;
            }
        }

        $matchScore = round(($matchedSkills / $totalRequired) * 100);

        return [
            'role_id' => $targetRole->id,
            'role_name' => $targetRole->name,
            'match_score' => $matchScore,
            'skills_matched' => $matchedSkills,
            'skills_required' => $totalRequired,
            'gaps' => $gaps,
            'total_gap_levels' => $totalGap,
            'estimated_months' => $this->estimateTransitionTime($totalGap),
        ];
    }

    protected function findSteppingStones(array $fromSkills, array $toSkills, ?int $orgId): array
    {
        $newSkillsNeeded = array_diff($toSkills, $fromSkills);

        if (empty($newSkillsNeeded)) return [];

        // Buscar roles que compartan skills con ambos (From y To)
        $query = Roles::with('skills');
        if ($orgId) {
            $query->where('organization_id', $orgId);
        }

        $candidates = $query->get()
            ->map(function ($role) use ($fromSkills, $toSkills) {
                $roleSkills = $role->skills->pluck('id')->toArray();
                $overlapWithFrom = count(array_intersect($roleSkills, $fromSkills));
                $overlapWithTo = count(array_intersect($roleSkills, $toSkills));
                $bridgeScore = $overlapWithFrom + $overlapWithTo;

                return [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'bridge_score' => $bridgeScore,
                    'skills_from_origin' => $overlapWithFrom,
                    'skills_toward_target' => $overlapWithTo,
                ];
            })
            ->filter(fn ($c) => $c['skills_from_origin'] > 0 && $c['skills_toward_target'] > 0)
            ->sortByDesc('bridge_score')
            ->take(3)
            ->values();

        return $candidates->toArray();
    }

    protected function estimateTransitionTime(int $totalGap): int
    {
        if ($totalGap <= 2) return 3;
        if ($totalGap <= 5) return 6;
        if ($totalGap <= 10) return 12;
        return 18;
    }

    protected function identifyQuickWins(array $currentSkills, $pathCandidates): array
    {
        $quickWins = [];

        foreach ($pathCandidates as $candidate) {
            foreach ($candidate['gaps'] ?? [] as $gap) {
                if ($gap['gap'] === 1) {
                    $quickWins[] = [
                        'skill' => $gap['skill'],
                        'target_role' => $candidate['role_name'],
                        'effort' => 'Curso corto (2-4 semanas)',
                    ];
                }
            }
        }

        return array_slice($quickWins, 0, 5);
    }

    protected function buildRouteSteps($fromRole, $toRole, array $steppingStones, array $newSkills): array
    {
        $steps = [];
        $steps[] = [
            'step' => 1,
            'action' => "Desde: {$fromRole->name}",
            'type' => 'origin',
        ];

        foreach ($steppingStones as $i => $stone) {
            $steps[] = [
                'step' => $i + 2,
                'action' => "Paso intermedio: {$stone['role_name']}",
                'type' => 'stepping_stone',
                'bridge_score' => $stone['bridge_score'],
            ];
        }

        $steps[] = [
            'step' => count($steps) + 1,
            'action' => "Destino: {$toRole->name}",
            'type' => 'destination',
            'new_skills_needed' => count($newSkills),
        ];

        return $steps;
    }

    protected function calculateMobilityIndex(array $matrix, int $totalRoles): float
    {
        if ($totalRoles <= 1) return 0;

        $maxPossibleConnections = $totalRoles * ($totalRoles - 1);
        $actualConnections = count($matrix);

        return round(($actualConnections / $maxPossibleConnections) * 100, 1);
    }
}
