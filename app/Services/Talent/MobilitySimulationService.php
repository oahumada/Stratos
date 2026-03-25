<?php

namespace App\Services\Talent;

use App\Models\People;
use App\Models\Roles;
use App\Services\Talent\Lms\LmsService;
use Illuminate\Support\Facades\Log;

class MobilitySimulationService
{
    protected LmsService $lmsService;

    public function __construct(LmsService $lmsService)
    {
        $this->lmsService = $lmsService;
    }

    /**
     * Simulate the movement of multiple people to a single target role.
     */
    public function simulateMassMovement(array $personIds, int $targetRoleId): array
    {
        $movements = array_map(fn ($id) => [
            'person_id' => $id,
            'target_role_id' => $targetRoleId,
        ], $personIds);

        return $this->simulatePlannedMovements($movements);
    }

    /**
     * Simulate a complex plan with multiple people and multiple target roles.
     */
    public function simulatePlannedMovements(array $movements): array
    {
        $results = [];
        $totalFit = 0;
        $totalFriction = 0;
        $totalLegacyRisk = 0;
        $totalROI = 0;
        $totalSavings = 0;

        foreach ($movements as $m) {
            $suggestedCourses = $m['suggested_courses'] ?? [];
            $simulation = $this->simulateMovement((int) $m['person_id'], (int) $m['target_role_id'], $suggestedCourses);

            // Inject AI suggestions if present
            if (isset($m['suggested_courses'])) {
                $simulation['suggested_courses'] = $m['suggested_courses'];
            }
            if (isset($m['rationale'])) {
                $simulation['rationale'] = $m['rationale'];
            }

            $results[] = $simulation;
            $totalFit += $simulation['metrics']['fit_score'];
            $totalFriction += $simulation['metrics']['friction_score'];
            $totalLegacyRisk += $simulation['metrics']['legacy_risk'];
            $totalROI += $simulation['financial_impact']['roi_amount'] ?? 0;
            $totalSavings += $simulation['financial_impact']['recruitment_avoidance_cost'] ?? 0;
        }

        $count = count($movements);
        $avgFit = $count > 0 ? $totalFit / $count : 0;
        $avgFriction = $count > 0 ? $totalFriction / $count : 0;
        $avgLegacyRisk = $count > 0 ? $totalLegacyRisk / $count : 0;

        return [
            'individual_results' => $results,
            'aggregated_metrics' => [
                'avg_fit_score' => round($avgFit, 2),
                'avg_friction_score' => round($avgFriction, 2),
                'avg_legacy_risk' => round($avgLegacyRisk, 2),
                'success_probability' => round($avgFit * (1 - $avgFriction), 2),
                'total_moved' => $count,
                'total_roi_projected' => round($totalROI, 2),
                'total_recruitment_savings' => round($totalSavings, 2),
            ],
            'group_insights' => $this->generateGroupInsights($avgFit, $avgLegacyRisk, $count),
        ];
    }

    private function generateGroupInsights(float $avgFit, float $avgRisk, int $count): array
    {
        $insights = [];
        if ($count > 3 && $avgRisk > 0.6) {
            $insights[] = "ALERTA: El movimiento masivo de $count personas genera una desestabilización crítica en las áreas de origen.";
        }
        if ($avgFit > 0.75) {
            $insights[] = 'El grupo seleccionado posee una alta afinidad técnica con el nuevo rol.';
        }

        return $insights;
    }

    /**
     * Simulate the movement of a person to a target role.
     */
    public function simulateMovement(int $personId, int $targetRoleId, array $suggestedCourses = []): array
    {
        $person = People::with(['skills', 'psychometricProfiles', 'role', 'department'])->findOrFail($personId);
        $targetRole = Roles::with('skills')->findOrFail($targetRoleId);

        $fitScore = $this->calculateFit($person, $targetRole);
        $skillGaps = $this->getSkillGaps($person, $targetRole);
        $frictionScore = $this->calculateFriction($person, $targetRole, $fitScore);
        $legacyRisk = $this->calculateLegacyRisk($person);

        // 📚 LMS Integration: Get real courses for the gaps
        $recommendedCourses = $this->getRecommendedCoursesForGaps($skillGaps);

        // Merge suggested courses from AI with those found in LMS
        $allSuggestedCourses = array_merge($suggestedCourses, $recommendedCourses);

        $overallSuccessProbability = ($fitScore * (1 - $frictionScore));
        $financialImpact = $this->calculateROI($targetRole, $frictionScore, $person, $allSuggestedCourses);

        // Domino Effect: Suggest potential successors for the role this person is leaving
        $dominoEffect = null;
        if ($person->role) {
            $dominoEffect = $this->simulateDominoEffect($person->role);
        }

        return [
            'person' => [
                'id' => $person->id,
                'name' => $person->full_name,
                'current_role' => $person->role->name ?? 'N/A',
                'current_department' => $person->department->name ?? 'N/A',
            ],
            'target_role' => [
                'id' => $targetRole->id,
                'name' => $targetRole->name,
            ],
            'metrics' => [
                'fit_score' => round($fitScore, 2),
                'friction_score' => round($frictionScore, 2),
                'legacy_risk' => round($legacyRisk, 2),
                'success_probability' => round($overallSuccessProbability, 2),
            ],
            'skill_gaps' => $skillGaps,
            'suggested_courses' => $allSuggestedCourses,
            'financial_impact' => $financialImpact,
            'domino_effect' => $dominoEffect,
            'succession_chain' => $person->role ? $this->simulateSuccessionChain($person->role) : null,
            'insights' => $this->generateInsights($fitScore, $frictionScore, $legacyRisk),
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Simulate impact on all departments to find the best relocations or identify critical gaps.
     */
    public function simulateDepartmentImpact(int $organizationId): array
    {
        $departments = \App\Models\Departments::where('organization_id', $organizationId)
            ->with(['People.skills', 'People.role'])
            ->get();

        $impact = [];

        foreach ($departments as $dept) {
            $deptMetrics = $this->getDepartmentMetrics($dept);
            $impact[] = [
                'department_id' => $dept->id,
                'department_name' => $dept->name,
                'health_score' => $deptMetrics['health_score'],
                'critical_roles_count' => $deptMetrics['critical_roles_count'],
                'potential_mobility_out' => $deptMetrics['high_potential_count'],
                'friction_index' => $this->calculateDeptFriction($dept),
                'risk_level' => $this->calculateDeptRisk($deptMetrics),
            ];
        }

        return $impact;
    }

    private function getDepartmentMetrics($dept): array
    {
        $people = $dept->People;
        if ($people->isEmpty()) {
            return [
                'health_score' => 0,
                'critical_roles_count' => 0,
                'high_potential_count' => 0,
            ];
        }

        $hiPoCount = $people->where('is_high_potential', true)->count();
        $avgSkills = $people->avg('skills_count') ?? 0;

        // Base health on talent density and skill coverage
        $healthScore = min(($avgSkills / 5) * 0.5 + ($hiPoCount / $people->count()) * 0.5, 1.0);

        return [
            'health_score' => round($healthScore, 2),
            'critical_roles_count' => $people->count(), // Simplified: assuming all roles in small depts are critical
            'high_potential_count' => $hiPoCount,
            'total_members' => $people->count(),
        ];
    }

    private function calculateDeptFriction($dept): float
    {
        // Friction increases with department size and hierarchy levels (conceptual)
        $memberCount = $dept->People->count();

        return round(min(0.2 + ($memberCount * 0.02), 0.8), 2);
    }

    private function calculateDeptRisk(array $metrics): string
    {
        if ($metrics['health_score'] < 0.3) {
            return 'CRÍTICO';
        }
        if ($metrics['health_score'] < 0.6) {
            return 'ADVERTENCIA';
        }

        return 'ESTABLE';
    }

    /**
     * Calculate how well the person fits the target role requirements.
     */
    private function calculateFit(People $person, Roles $targetRole): float
    {
        $targetSkills = $targetRole->skills;
        if ($targetSkills->isEmpty()) {
            return 0.5; // Neutral if no requirements defined
        }

        $personSkills = $person->skills->pluck('pivot.current_level', 'id')->toArray();
        $matches = 0;
        $totalWeight = 0;

        foreach ($targetSkills as $skill) {
            $requiredLevel = $skill->pivot->required_level ?? 3;
            $currentLevel = $personSkills[$skill->id] ?? 0;
            $weight = ($skill->pivot->is_critical ?? false) ? 2 : 1;

            $totalWeight += $weight;
            if ($currentLevel >= $requiredLevel) {
                $matches += $weight;
            } else {
                $matches += $weight * ($currentLevel / $requiredLevel);
            }
        }

        return $matches / $totalWeight;
    }

    /**
     * Identify specific skills where the person is below requirements.
     */
    private function getSkillGaps(People $person, Roles $targetRole): array
    {
        $targetSkills = $targetRole->skills;
        $personSkills = $person->skills->pluck('pivot.current_level', 'id')->toArray();
        $gaps = [];

        foreach ($targetSkills as $skill) {
            $requiredLevel = $skill->pivot->required_level ?? 3;
            $currentLevel = $personSkills[$skill->id] ?? 0;

            if ($currentLevel < $requiredLevel) {
                $gaps[] = [
                    'skill_id' => $skill->id,
                    'name' => $skill->name,
                    'current_level' => $currentLevel,
                    'required_level' => $requiredLevel,
                    'gap' => $requiredLevel - $currentLevel,
                    'is_critical' => (bool) ($skill->pivot->is_critical ?? false),
                ];
            }
        }

        return $gaps;
    }

    /**
     * Calculate friction (learning curve, cultural cost, etc.)
     */
    private function calculateFriction(People $person, Roles $targetRole, ?float $fit = null): float
    {
        $friction = 0.2; // Base friction

        // 1. Context Change Friction
        if ($person->role && $person->role->parent_id !== $targetRole->parent_id) {
            $friction += 0.15;
        }

        // 2. Skill gap contribution to friction
        $fit = $fit ?? $this->calculateFit($person, $targetRole);
        $friction += (1 - $fit) * 0.4;

        // 🧠 OPTIMIZATION: Historical Stability Adjustment
        // If they moved recently (last 6 months), friction increases significantly
        $recentMovementsCount = \App\Models\PersonMovement::where('person_id', $person->id)
            ->where('movement_date', '>=', now()->subMonths(6))
            ->count();

        if ($recentMovementsCount > 0) {
            // High turnover penalty: too many moves in short time increases cognitive load
            $friction += ($recentMovementsCount * 0.2);
            Log::info("Friction increased for {$person->full_name} due to {$recentMovementsCount} recent movements.");
        }

        return min($friction, 1.0);
    }

    /**
     * Calculate the risk for the team left behind.
     */
    public function calculateLegacyRisk(People $person): float
    {
        $risk = 0.1;

        if ($person->is_high_potential) {
            $risk += 0.4;
        }

        // Check department context
        if ($person->department) {
            $deptId = $person->department_id;
            $deptMembersCount = People::where('department_id', $deptId)->count();

            if ($deptMembersCount <= 3) {
                $risk += 0.3;
            }

            // If the person is a manager, risk increases
            if ($person->department->manager_id === $person->id) {
                $risk += 0.25;
            }

            // 🧠 OPTIMIZATION: Stability Check
            // If there were many EXITS in this department recently, the risk is compounding
            $recentExits = \App\Models\PersonMovement::where('organization_id', $person->organization_id)
                ->where('from_department_id', $deptId)
                ->where('type', 'exit')
                ->where('movement_date', '>=', now()->subMonths(3))
                ->count();

            if ($recentExits > 1) {
                $risk += ($recentExits * 0.15); // Compounding loss effect
                Log::info("Legacy risk increased for department {$person->department->name} due to {$recentExits} recent exits.");
            }
        }

        return min($risk, 1.0);
    }

    /**
     * Generate human-readable insights based on the metrics.
     */
    private function generateInsights(float $fit, float $friction, float $risk): array
    {
        $insights = [];

        if ($fit > 0.8) {
            $insights[] = 'Excelente alineamiento técnico detectado.';
        } elseif ($fit < 0.5) {
            $insights[] = 'Brecha significativa de habilidades detectada. Se requerirá un plan de capacitación intenso.';
        }

        if ($friction > 0.6) {
            $insights[] = 'Alta resistencia a la transición. El cambio de contexto es drástico.';
        }

        if ($risk > 0.7) {
            $insights[] = 'ALERTA: El equipo de origen quedará en estado crítico tras este movimiento.';
        }

        return $insights;
    }

    /**
     * Calculate Financial ROI of internal mobility vs external recruitment.
     */
    private function calculateROI(Roles $targetRole, float $frictionScore, ?People $person = null, array $suggestedCourses = []): array
    {
        $targetSalary = $this->getRoleBaseSalary($targetRole);
        $currentSalary = $person?->salary ?? ($person?->role ? $this->getRoleBaseSalary($person->role) : ($targetSalary * 0.85));

        // External Recruitment Cost (Proxy: 20% of annual salary)
        $externalRecruitmentCost = $targetSalary * 12 * 0.20;

        // 📚 NEW: Specific Training Cost from LMS Hub
        $trainingCost = 0;
        foreach ($suggestedCourses as $course) {
            // Buscamos el costo en nuestra DB interna si es de Stratos
            if (isset($course['id']) && is_numeric($course['id'])) {
                $trainingCost += \App\Models\LmsCourse::find($course['id'])?->cost_per_seat ?? 0;
            } else {
                // Para externos (LinkedIn/Udemy) usamos un proxy estándar si no tenemos API de pricing
                $trainingCost += 50; // Ejemplo: 50 USD promedio por curso externo
            }
        }

        // Internal Mobility Cost (Training + Friction/Productivity loss)
        // Proxy: 0.5 months for base onboarding + friction scale + specific training
        $internalOnboardingCost = ($targetSalary * (0.5 + $frictionScore)) + $trainingCost;

        $roiProjected = $externalRecruitmentCost - $internalOnboardingCost;

        return [
            'currency' => 'USD',
            'target_monthly_salary' => round($targetSalary, 2),
            'current_monthly_salary' => round($currentSalary, 2),
            'recruitment_avoidance_cost' => round($externalRecruitmentCost, 2),
            'training_hub_cost' => round($trainingCost, 2),
            'internal_transition_cost' => round($internalOnboardingCost, 2),
            'roi_amount' => round($roiProjected, 2),
            'roi_percentage' => round(($roiProjected / max($internalOnboardingCost, 1)) * 100, 2),
        ];
    }

    /**
     * Helper to get salary based on role level.
     */
    private function getRoleBaseSalary(Roles $role): float
    {
        if ($role->base_salary) {
            return (float) $role->base_salary;
        }

        $levelMap = [
            'junior' => 1800,
            'middle' => 3200,
            'senior' => 5500,
            'lead' => 8500,
            'director' => 12000,
        ];

        return $levelMap[strtolower($role->level)] ?? 3000;
    }

    /**
     * Simulate the domino effect of leaving a role vacant.
     */
    private function simulateDominoEffect(Roles $vacantRole): array
    {
        $potentialSuccessors = $this->findPotentialSuccessors($vacantRole);

        return [
            'vacant_role' => [
                'id' => $vacantRole->id,
                'name' => $vacantRole->name,
            ],
            'succession_candidates' => $potentialSuccessors,
            'impact_description' => count($potentialSuccessors) > 0
                ? 'Se han detectado '.count($potentialSuccessors).' candidatos internos óptimos para cubrir esta vacante.'
                : 'No se detectaron candidatos internos con alto fit inmediato. Se requiere búsqueda externa o plan de desarrollo intensivo.',
        ];
    }

    /**
     * Search LMS for courses that can close identified skill gaps.
     */
    protected function getRecommendedCoursesForGaps(array $gaps): array
    {
        $recommendations = [];

        // Take the top 3 gaps (ideally critical ones first)
        $targetGaps = array_slice($gaps, 0, 3);

        foreach ($targetGaps as $gap) {
            $courses = $this->lmsService->searchCourses($gap['name']);
            if (! empty($courses)) {
                // Return the course data in a format compatible with the financial calculator
                $bestCourse = $courses[0];
                $recommendations[] = [
                    'id' => $bestCourse['id'] ?? null,
                    'title' => $bestCourse['title'] ?? ($bestCourse['name'] ?? $gap['name']),
                    'provider' => $bestCourse['provider'] ?? 'Internal',
                    'cost' => $bestCourse['cost_per_seat'] ?? 0,
                    'skill' => $gap['name'],
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Search for internal people who could fill a vacant role.
     */
    private function findPotentialSuccessors(Roles $role): array
    {
        // Simple logic: find people in the same organization, with different role, who have at least 60% skill fit
        $candidates = People::where('organization_id', $role->organization_id)
            ->where('role_id', '!=', $role->id)
            ->with(['skills', 'role'])
            ->get();

        $potential = [];
        foreach ($candidates as $person) {
            $fit = $this->calculateFit($person, $role);
            if ($fit >= 0.6) {
                $potential[] = [
                    'id' => $person->id,
                    'name' => $person->full_name,
                    'current_role' => $person->role->name ?? 'Sin Rol',
                    'fit_score' => round($fit, 2),
                ];
            }
        }

        // Sort by fit score descending and take top 3
        usort($potential, fn ($a, $b) => $b['fit_score'] <=> $a['fit_score']);

        return array_slice($potential, 0, 3);
    }

    /**
     * Simulate a deep chain of succession movements.
     */
    public function simulateSuccessionChain(Roles $initialVacantRole, int $maxDepth = 3): array
    {
        $nodes = [];
        $links = [];
        $processedRoles = [];

        $queue = [[
            'role' => $initialVacantRole,
            'depth' => 0,
            'source_node_id' => null,
        ]];

        while (! empty($queue)) {
            $current = array_shift($queue);
            $role = $current['role'];
            $depth = $current['depth'];

            if ($depth >= $maxDepth || in_array($role->id, $processedRoles)) {
                continue;
            }

            $processedRoles[] = $role->id;
            $nodeId = 'role_'.$role->id;

            $nodes[] = [
                'id' => $nodeId,
                'label' => $role->name,
                'type' => 'vacancy',
                'level' => $depth,
            ];

            if ($current['source_node_id']) {
                $links[] = [
                    'source' => $nodeId,
                    'target' => $current['source_node_id'],
                    'label' => 'Cubre a',
                ];
            }

            // Find the best successor to "move" recursively
            $successors = $this->findPotentialSuccessors($role);
            if (! empty($successors)) {
                $best = $successors[0]; // Take the best fit
                $personNodeId = 'person_'.$best['id'];

                $nodes[] = [
                    'id' => $personNodeId,
                    'label' => $best['name'],
                    'type' => 'candidate',
                    'fit' => $best['fit_score'],
                    'level' => $depth,
                ];

                $links[] = [
                    'source' => $personNodeId,
                    'target' => $nodeId,
                    'label' => 'Candidato ideal',
                ];

                // If this person has a role, that role will become the next vacancy in the chain
                $personDetails = People::with('role')->find($best['id']);
                if ($personDetails && $personDetails->role) {
                    $queue[] = [
                        'role' => $personDetails->role,
                        'depth' => $depth + 1,
                        'source_node_id' => $personNodeId,
                    ];
                }
            }
        }

        return [
            'nodes' => $nodes,
            'links' => $links,
        ];
    }

    /**
     * Materialize a simulation into a ChangeSet.
     */
    public function materializeChangeSet(\App\Models\Scenario $scenario, \App\Models\User $actor): \App\Models\ChangeSet
    {
        $config = $scenario->custom_config ?? [];
        $payload = $config['simulation_payload'] ?? [];
        $targetRoleId = $config['target_role_id'] ?? null;

        $ops = [];

        // 1. Process Movements
        if (isset($payload['individual_results'])) {
            // Mass Movement or Strategic Plan
            foreach ($payload['individual_results'] as $res) {
                $ops[] = $this->createMoveOp($res, $targetRoleId);

                // 2. Process Domino Effect (Vacancies)
                if (isset($res['domino_effect']['vacant_role'])) {
                    $ops[] = $this->createVacancyOp($res['domino_effect']['vacant_role']);
                }

                // 3. Process Skill Gaps (Development Plans)
                if (isset($res['skill_gaps']) && ! empty($res['skill_gaps'])) {
                    $ops[] = $this->createDevelopmentPlanOp($res);
                }
            }
        } elseif (isset($payload['person'])) {
            // Single Movement
            $ops[] = $this->createMoveOp($payload, $targetRoleId);

            if (isset($payload['domino_effect']['vacant_role'])) {
                $ops[] = $this->createVacancyOp($payload['domino_effect']['vacant_role']);
            }

            if (isset($payload['skill_gaps']) && ! empty($payload['skill_gaps'])) {
                $ops[] = $this->createDevelopmentPlanOp($payload);
            }
        }

        // Create the ChangeSet
        return \App\Models\ChangeSet::create([
            'organization_id' => $scenario->organization_id,
            'scenario_id' => $scenario->id,
            'title' => 'Implementación: '.$scenario->name,
            'description' => 'Movimientos estratégicos generados desde el simulador de movilidad.',
            'status' => 'draft',
            'created_by' => $actor->id,
            'diff' => ['ops' => $ops],
            'metadata' => [
                'source' => 'mobility_simulation',
                'projected_roi' => $payload['aggregated_metrics']['total_roi_projected'] ?? ($payload['financial_impact']['roi_amount'] ?? 0),
                'projected_savings' => $payload['aggregated_metrics']['total_recruitment_savings'] ?? ($payload['financial_impact']['recruitment_avoidance_cost'] ?? 0),
            ],
        ]);
    }

    private function createMoveOp(array $res, ?int $fallbackTargetRoleId = null): array
    {
        $targetRoleId = $res['target_role']['id'] ?? $fallbackTargetRoleId;

        if (! $targetRoleId) {
            throw new \InvalidArgumentException('No se ha proporcionado un rol de destino para el colaborador: '.($res['person']['name'] ?? 'ID '.$res['person']['id']));
        }

        return [
            'type' => 'move_person',
            'person_id' => $res['person']['id'],
            'target_role_id' => $targetRoleId,
            'metadata' => [
                'fit_score' => $res['metrics']['fit_score'] ?? 0,
                'friction_score' => $res['metrics']['friction_score'] ?? 0,
            ],
        ];
    }

    private function createVacancyOp(array $vacantRole): array
    {
        return [
            'type' => 'create_vacancy',
            'role_id' => $vacantRole['id'],
            'title' => 'Nueva Vacante: '.$vacantRole['name'],
            'status' => 'open',
            'is_external' => false, // Internal first by default
            'metadata' => [
                'source' => 'domino_effect_simulation',
            ],
        ];
    }

    private function createDevelopmentPlanOp(array $res): array
    {
        return [
            'type' => 'create_development_plan',
            'person_id' => $res['person']['id'],
            'title' => 'Plan de Upskilling: Transición a '.($res['target_role']['name'] ?? 'Nuevo Rol'),
            'gaps' => $res['skill_gaps'],
            'suggested_courses' => $res['suggested_courses'] ?? [],
            'metadata' => [
                'source' => 'mobility_simulation',
                'fit_score_at_simulation' => $res['metrics']['fit_score'] ?? 0,
                'rationale' => $res['rationale'] ?? null,
            ],
        ];
    }
}
