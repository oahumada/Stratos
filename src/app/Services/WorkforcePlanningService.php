<?php

namespace App\Services;

use App\Models\WorkforcePlanningScenario;
use App\Models\WorkforcePlanningRoleForecast;
use App\Models\WorkforcePlanningMatch;
use App\Models\WorkforcePlanningSkillGap;
use App\Models\WorkforcePlanningAnalytic;
use App\Models\ScenarioSkillDemand;
use App\Models\ScenarioClosureStrategy;
use App\Repositories\WorkforcePlanningRepository;
use App\Models\People;
use App\Models\Skills;
use App\Models\Skill;
use App\Models\PeopleRoleSkills;
use App\Models\Organizations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WorkforcePlanningService
{
    protected WorkforcePlanningRepository $repository;

    public function __construct(WorkforcePlanningRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Calcular matching de talento interno para un escenario
     * Compara skills actuales de personas vs requisitos de roles proyectados
     */
    public function calculateMatches($scenarioId): array
    {
        $scenario = WorkforcePlanningScenario::find($scenarioId);
        $forecasts = $this->repository->getForecastsByScenario($scenarioId);
        
        // Si no hay forecasts, retornar array vacío
        if ($forecasts->isEmpty()) {
            return [
                'total_matches' => 0,
                'matches' => [],
                'errors' => ['No role forecasts found for this scenario'],
            ];
        }
        
        $people = People::with(['skills', 'currentRole'])->where('organization_id', $scenario->organization_id)->get();

        $matches = [];
        $matchErrors = [];

        foreach ($forecasts as $forecast) {
            foreach ($people as $person) {
                try {
                    $matchData = $this->calculateIndividualMatch($person, $forecast, $scenario);
                    
                    if ($matchData['match_score'] >= 30) { // Solo guardar matches con score mínimo
                        $matches[] = [
                            'scenario_id' => $scenarioId,
                            'forecast_id' => $forecast->id,
                            'person_id' => $person->id,
                            'match_score' => $matchData['match_score'],
                            'skill_match' => $matchData['skill_match'],
                            'readiness_level' => $matchData['readiness_level'],
                            'gaps' => $matchData['gaps'],
                            'transition_type' => $matchData['transition_type'],
                            'transition_months' => $matchData['transition_months'],
                            'risk_score' => $matchData['risk_score'],
                            'risk_factors' => $matchData['risk_factors'],
                            'recommendation' => $matchData['recommendation'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } catch (\Exception $e) {
                    $matchErrors[] = "Error matching {$person->name} para rol {$forecast->role_id}: {$e->getMessage()}";
                }
            }
        }

        // Insertar matches en batch
        if (!empty($matches)) {
            // Limpiar matches previos si existen
            WorkforcePlanningMatch::where('scenario_id', $scenarioId)->delete();
            
            // Batch insert
            $chunkSize = 100;
            foreach (array_chunk($matches, $chunkSize) as $chunk) {
                WorkforcePlanningMatch::insert($chunk);
            }
        }

        return [
            'total_matches' => count($matches),
            'matches' => $matches,
            'errors' => $matchErrors,
        ];
    }

    /**
     * Calcular matching individual entre persona y puesto
     */
    private function calculateIndividualMatch($person, $forecast, $scenario): array
    {
        // Obtener skills de la persona
        $personSkills = PeopleRoleSkills::where('people_id', $person->id)
            ->with('skill')
            ->get()
            ->keyBy('skill_id');

        // Obtener skills requeridos del rol
        $requiredSkills = $forecast->critical_skills ?? [];
        $emergingSkills = $forecast->emerging_skills ?? [];

        $skillMatches = 0;
        $totalRequired = 0;
        $gaps = [];

        // Evaluar critical skills
        foreach ($requiredSkills as $skillId => $requiredProf) {
            $totalRequired++;
            if (isset($personSkills[$skillId])) {
                $personProf = $personSkills[$skillId]->proficiency_level;
                if ($personProf >= $requiredProf) {
                    $skillMatches++;
                } else {
                    $gaps[] = [
                        'skill_id' => $skillId,
                        'required' => $requiredProf,
                        'current' => $personProf,
                        'gap' => $requiredProf - $personProf,
                        'type' => 'critical',
                    ];
                }
            } else {
                $gaps[] = [
                    'skill_id' => $skillId,
                    'required' => $requiredProf,
                    'current' => 0,
                    'gap' => $requiredProf,
                    'type' => 'missing',
                ];
            }
        }

        $skillMatch = $totalRequired > 0 ? ($skillMatches / $totalRequired) * 100 : 0;

        // Determinar readiness level basado en skill match y brecha
        $readinessLevel = $this->determineReadinessLevel($skillMatch, count($gaps));
        
        // Meses estimados de transición
        $transitionMonths = $this->estimateTransitionMonths($readinessLevel, count($gaps));

        // Tipo de transición
        $transitionType = $this->determineTransitionType($person->current_role_id, $forecast->role_id, $skillMatch);

        // Score final (0-100)
        // Basado en: skill match (60%), readiness (20%), risk (20%)
        $riskScore = $this->calculateRiskScore($person, $gaps, $readinessLevel);
        $riskFactors = $this->identifyRiskFactors($person, $gaps);

        $matchScore = ($skillMatch * 0.6) + ($this->readinessToScore($readinessLevel) * 0.2) + ((100 - $riskScore) * 0.2);

        $recommendation = $this->generateRecommendation($matchScore, $readinessLevel, $gaps);

        return [
            'match_score' => round($matchScore, 2),
            'skill_match' => round($skillMatch, 2),
            'readiness_level' => $readinessLevel,
            'gaps' => $gaps,
            'transition_type' => $transitionType,
            'transition_months' => $transitionMonths,
            'risk_score' => $riskScore,
            'risk_factors' => $riskFactors,
            'recommendation' => $recommendation,
        ];
    }

    private function determineReadinessLevel($skillMatch, $gapCount): string
    {
        if ($skillMatch >= 85 && $gapCount <= 1) {
            return 'immediate';
        } elseif ($skillMatch >= 70 && $gapCount <= 2) {
            return 'short_term';
        } elseif ($skillMatch >= 50 && $gapCount <= 4) {
            return 'long_term';
        }
        return 'not_ready';
    }

    private function estimateTransitionMonths($readinessLevel, $gapCount): int
    {
        return match($readinessLevel) {
            'immediate' => 1,
            'short_term' => 3 + ($gapCount * 2),
            'long_term' => 6 + ($gapCount * 3),
            default => 12,
        };
    }

    private function determineTransitionType($currentRoleId, $newRoleId, $skillMatch): string
    {
        if ($skillMatch >= 80) {
            return 'promotion';
        } elseif ($skillMatch >= 60) {
            return 'lateral';
        } elseif ($skillMatch >= 40) {
            return 'reskilling';
        }
        return 'no_match';
    }

    private function calculateRiskScore($person, $gaps, $readinessLevel): float
    {
        $risk = 0;

        // Risk por gaps
        $risk += count($gaps) * 10;

        // Risk por readiness
        $riskByReadiness = match($readinessLevel) {
            'immediate' => 5,
            'short_term' => 20,
            'long_term' => 40,
            'not_ready' => 70,
        };
        $risk += $riskByReadiness;

        // Normalizar a 0-100
        return min(100, $risk);
    }

    private function identifyRiskFactors($person, $gaps): array
    {
        $factors = [];

        if (count($gaps) >= 3) {
            $factors[] = 'multiple_skill_gaps';
        }

        // Aquí se podría agregar lógica para otros factores de riesgo
        // (edad, experiencia, rotación previa, etc)

        return $factors;
    }

    private function readinessToScore($readinessLevel): int
    {
        return match($readinessLevel) {
            'immediate' => 100,
            'short_term' => 70,
            'long_term' => 40,
            'not_ready' => 10,
        };
    }

    private function generateRecommendation($matchScore, $readinessLevel, $gaps): string
    {
        if ($matchScore >= 80) {
            return "Excelente candidato. Listo para transición inmediata.";
        } elseif ($matchScore >= 65) {
            return "Buen candidato. Requiere formación especializada de " . count($gaps) . " skill(s).";
        } elseif ($matchScore >= 50) {
            return "Candidato potencial. Necesita programa de desarrollo a largo plazo.";
        }
        return "No es recomendado actualmente. Considerar para futuras oportunidades.";
    }

    /**
     * Calcular brechas de skills por escenario
     */
    public function calculateSkillGaps($scenarioId): array
    {
        $scenario = WorkforcePlanningScenario::find($scenarioId);
        $forecasts = $this->repository->getForecastsByScenario($scenarioId);

        // Si no hay forecasts, retornar array vacío
        if ($forecasts->isEmpty()) {
            return [
                'total_gaps' => 0,
                'critical_gaps' => 0,
                'gaps' => [],
            ];
        }

        $gapsData = [];

        foreach ($forecasts as $forecast) {
            $requiredSkills = array_merge(
                $forecast->critical_skills ?? [],
                $forecast->emerging_skills ?? []
            );

            foreach ($requiredSkills as $skillId => $requiredProf) {
                // Contar cuántas personas en el departamento tienen este skill
                $peopleWithSkill = PeopleRoleSkills::whereHas('people', function ($q) use ($scenario, $forecast) {
                    $q->where('organization_id', $scenario->organization_id)
                        ->where('department_id', $forecast->department_id);
                })
                    ->where('skill_id', $skillId)
                    ->where('proficiency_level', '>=', $requiredProf)
                    ->count();

                $totalPeopleInDept = People::where('organization_id', $scenario->organization_id)
                    ->where('department_id', $forecast->department_id)
                    ->count();

                $coveragePercentage = $totalPeopleInDept > 0 ? ($peopleWithSkill / $totalPeopleInDept) * 100 : 0;

                $gapsData[] = [
                    'scenario_id' => $scenarioId,
                    'department_id' => $forecast->department_id,
                    'role_id' => $forecast->role_id,
                    'skill_id' => $skillId,
                    'current_proficiency' => 0,
                    'required_proficiency' => $requiredProf,
                    'gap' => $requiredProf,
                    'people_with_skill' => $peopleWithSkill,
                    'coverage_percentage' => round($coveragePercentage, 2),
                    'priority' => $coveragePercentage < 30 ? 'critical' : ($coveragePercentage < 60 ? 'high' : 'medium'),
                    'remediation_strategy' => $this->suggestRemediationStrategy($coveragePercentage, $requiredProf),
                    'estimated_cost' => $this->estimateRemediationCost($coveragePercentage, $requiredProf),
                    'timeline_months' => $this->estimateRemediationTimeline($coveragePercentage),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Limpiar gaps previos e insertar nuevos
        WorkforcePlanningSkillGap::where('scenario_id', $scenarioId)->delete();
        
        if (!empty($gapsData)) {
            $chunkSize = 100;
            foreach (array_chunk($gapsData, $chunkSize) as $chunk) {
                WorkforcePlanningSkillGap::insert($chunk);
            }
        }

        return [
            'total_gaps' => count($gapsData),
            'critical_gaps' => count(array_filter($gapsData, fn($g) => $g['priority'] === 'critical')),
            'gaps' => $gapsData,
        ];
    }

    private function suggestRemediationStrategy($coveragePercentage, $requiredProf): string
    {
        if ($coveragePercentage == 0) {
            return 'hiring';
        } elseif ($coveragePercentage < 30) {
            return 'training'; // Entrenar a algunos
        } elseif ($coveragePercentage < 60) {
            return 'reskilling'; // Re-entrenar a más personas
        }
        return 'training';
    }

    private function estimateRemediationCost($coveragePercentage, $requiredProf): float
    {
        $baseCost = 5000; // USD base
        $profCost = $requiredProf * 1000; // Costo por nivel de proficiency
        return ($baseCost + $profCost) * (100 - $coveragePercentage) / 100;
    }

    private function estimateRemediationTimeline($coveragePercentage): int
    {
        if ($coveragePercentage == 0) {
            return 6; // Hiring toma más tiempo
        } elseif ($coveragePercentage < 30) {
            return 4;
        } elseif ($coveragePercentage < 60) {
            return 3;
        }
        return 1;
    }

    /**
     * Calcular análisis agregado de métricas
     */
    public function calculateAnalytics($scenarioId): array
    {
        $scenario = WorkforcePlanningScenario::find($scenarioId);
        
        // Total headcount
        $totalCurrentHeadcount = People::where('organization_id', $scenario->organization_id)->count();
        $forecasts = $this->repository->getForecastsByScenario($scenarioId);
        $totalProjectedHeadcount = $forecasts->sum('headcount_projected');

        // Matches y coverage
        $matches = WorkforcePlanningMatch::where('scenario_id', $scenarioId)->get();
        $internalCoveragePercentage = count($matches) > 0 ? (count($matches->where('transition_type', '!=', 'no_match')) / count($forecasts)) * 100 : 0;

        // Skills
        $skillGaps = WorkforcePlanningSkillGap::where('scenario_id', $scenarioId)->get();
        $criticalSkillsAtRisk = $skillGaps->where('priority', 'critical')->count();

        // Succession
        $successionPlans = WorkforcePlanningSuccessionPlan::where('scenario_id', $scenarioId)->get();
        $criticalRolesWithSuccessor = $successionPlans->where('primary_successor_id', '!=', null)->count();
        $successionRiskPercentage = count($successionPlans) > 0 ? (count($successionPlans->where('primary_readiness_percentage', '<', 50)) / count($successionPlans)) * 100 : 0;

        $analyticsData = [
            'scenario_id' => $scenarioId,
            'total_headcount_current' => $totalCurrentHeadcount,
            'total_headcount_projected' => $totalProjectedHeadcount,
            'net_growth' => $totalProjectedHeadcount - $totalCurrentHeadcount,
            'internal_coverage_percentage' => round($internalCoveragePercentage, 2),
            'external_gap_percentage' => round(100 - $internalCoveragePercentage, 2),
            'total_skills_required' => $forecasts->sum(fn($f) => count(array_merge($f->critical_skills ?? [], $f->emerging_skills ?? []))),
            'skills_with_gaps' => $skillGaps->count(),
            'critical_skills_at_risk' => $criticalSkillsAtRisk,
            'critical_roles' => $successionPlans->where('criticality_level', 'critical')->count(),
            'critical_roles_with_successor' => $criticalRolesWithSuccessor,
            'succession_risk_percentage' => round($successionRiskPercentage, 2),
            'estimated_recruitment_cost' => $skillGaps->sum('estimated_cost'),
            'estimated_training_cost' => $skillGaps->where('remediation_strategy', 'training')->sum('estimated_cost'),
            'estimated_external_hiring_months' => round($skillGaps->where('remediation_strategy', 'hiring')->avg('timeline_months') ?? 0, 1),
            'high_risk_positions' => $matches->where('risk_score', '>=', 60)->count(),
            'medium_risk_positions' => $matches->where('risk_score', '>=', 40)->where('risk_score', '<', 60)->count(),
            'calculated_at' => now(),
        ];

        // Actualizar o crear analítica
        WorkforcePlanningAnalytic::updateOrCreate(
            ['scenario_id' => $scenarioId],
            $analyticsData
        );

        return $analyticsData;
    }

    /**
     * Ejecutar análisis completo (matching + skill gaps + analytics)
     */
    public function runFullAnalysis($scenarioId): array
    {
        return DB::transaction(function () use ($scenarioId) {
            return [
                'matches' => $this->calculateMatches($scenarioId),
                'skill_gaps' => $this->calculateSkillGaps($scenarioId),
                'analytics' => $this->calculateAnalytics($scenarioId),
            ];
        });
    }

    // ==================== SCENARIO MODELING METHODS ====================

    /**
     * Calcula brechas de skills para un escenario específico (Scenario Modeling).
     * Compara demanda requerida vs inventario actual desde people_skills.
     *
     * @param WorkforcePlanningScenario $scenario
     * @return array ['scenario_id', 'generated_at', 'summary', 'gaps']
     */
    public function calculateScenarioGaps(WorkforcePlanningScenario $scenario): array
    {
        $demands = $scenario->skillDemands()->with('skill')->get();
        $organization = $scenario->organization;

        $gaps = [];

        foreach ($demands as $demand) {
            $currentInventory = $this->calculateCurrentInventory(
                $organization,
                $demand->skill_id,
                $demand->role_id,
                $demand->department
            );

            // Actualizar demanda con inventario actual calculado
            $demand->update([
                'current_headcount' => $currentInventory['headcount'],
                'current_avg_level' => $currentInventory['avg_level']
            ]);

            $gapHeadcount = max(0, $demand->required_headcount - $currentInventory['headcount']);
            $gapLevel = round(max(0, (float)$demand->required_level - (float)$currentInventory['avg_level']), 1);

            $gaps[] = [
                'skill_id' => $demand->skill_id,
                'skill_name' => $demand->skill->name,
                'priority' => $demand->priority,
                'department' => $demand->department,
                'role_id' => $demand->role_id,
                'current_headcount' => $currentInventory['headcount'],
                'required_headcount' => $demand->required_headcount,
                'gap_headcount' => $gapHeadcount,
                'current_avg_level' => (float)$currentInventory['avg_level'],
                'required_level' => (float)$demand->required_level,
                'gap_level' => $gapLevel,
                'coverage_pct' => $demand->required_headcount > 0
                    ? round(($currentInventory['headcount'] / $demand->required_headcount) * 100)
                    : 100,
                'rationale' => $demand->rationale
            ];
        }

        // KPIs agregados del escenario
        $summary = $this->summarizeScenarioGaps($scenario, $gaps);

        return [
            'scenario_id' => $scenario->id,
            'generated_at' => now()->toISOString(),
            'summary' => $summary,
            'gaps' => $gaps
        ];
    }

    /**
     * Calcula inventario actual real para Scenario Modeling.
     *
     * @param Organizations $organization
     * @param int $skillId
     * @param int|null $roleId
     * @param string|null $department
     * @param int $minLevel
     * @return array ['headcount', 'avg_level']
     */
    private function calculateCurrentInventory(
        Organizations $organization,
        int $skillId,
        ?int $roleId = null,
        ?string $department = null,
        int $minLevel = 2
    ): array {
        $peopleQuery = People::query()->where('organization_id', $organization->id);

        if ($department && Schema::hasColumn('people', 'department')) {
            $peopleQuery->where('department', $department);
        }

        if ($roleId && Schema::hasColumn('people', 'role_id')) {
            $peopleQuery->where('role_id', $roleId);
        }

        $peopleIds = $peopleQuery->pluck('id');

        // Detectar tabla correcta
        $skillsTable = 'people_skills';
        if (!Schema::hasTable($skillsTable)) {
            $skillsTable = Schema::hasTable('person_role_skills') ? 'person_role_skills' : 'people_skills';
        }

        if (!Schema::hasTable($skillsTable)) {
            return ['headcount' => 0, 'avg_level' => 0];
        }

        $headcount = DB::table($skillsTable)
            ->whereIn('people_id', $peopleIds)
            ->where('skill_id', $skillId)
            ->where('current_level', '>=', $minLevel)
            ->distinct()
            ->count('people_id');

        $avgLevel = DB::table($skillsTable)
            ->whereIn('people_id', $peopleIds)
            ->where('skill_id', $skillId)
            ->avg('current_level');

        $avgLevel = round((float)($avgLevel ?: 0), 1);

        return [
            'headcount' => (int)$headcount,
            'avg_level' => $avgLevel
        ];
    }

    /**
     * Resume gaps en KPIs agregados.
     */
    private function summarizeScenarioGaps(WorkforcePlanningScenario $scenario, array $gaps): array
    {
        $totalSkills = count($gaps);
        $critical = collect($gaps)->where('priority', 'critical')->count();
        $withHeadcountGap = collect($gaps)->where('gap_headcount', '>', 0)->count();

        $avgCoverage = $totalSkills > 0
            ? round(collect($gaps)->avg('coverage_pct'), 1)
            : 100;

        $riskScore = min(100, ($critical * 15) + ($withHeadcountGap * 5) + (100 - $avgCoverage));

        return [
            'total_skills' => $totalSkills,
            'critical_skills' => $critical,
            'skills_with_headcount_gap' => $withHeadcountGap,
            'avg_coverage_pct' => $avgCoverage,
            'risk_score' => round($riskScore, 0),
            'estimated_cost_total' => (float)$scenario->getTotalEstimatedCost(),
            'estimated_time_avg_weeks' => round((float)$scenario->getAverageCompletionTime(), 1),
            'milestones_completion_pct' => (int)$scenario->getCompletionPercentage()
        ];
    }

    /**
     * Recomienda estrategias para cerrar una brecha (MVP: reglas simples).
     */
    public function recommendStrategiesForGap(WorkforcePlanningScenario $scenario, array $gap, array $preferences = []): array
    {
        $timePressure = $preferences['time_pressure'] ?? 'medium';
        $budgetSensitivity = $preferences['budget_sensitivity'] ?? 'medium';
        $automationAllowed = $preferences['automation_allowed'] ?? false;

        $recommendations = [];
        $gapHeadcount = (int)$gap['gap_headcount'];
        $gapLevel = (float)$gap['gap_level'];

        if ($gapHeadcount >= 10 && $timePressure === 'high') {
            $recommendations[] = ['strategy' => 'buy', 'reason' => 'Brecha grande y alta presión de tiempo'];
            $recommendations[] = ['strategy' => 'borrow', 'reason' => 'Cubrir rápido mientras se desarrolla interno'];
        } elseif ($gapLevel >= 1.5 && $gapHeadcount <= 5) {
            $recommendations[] = ['strategy' => 'build', 'reason' => 'Brecha de nivel relevante con pocos puestos'];
        } else {
            $recommendations[] = ['strategy' => 'build', 'reason' => 'Ruta estándar de upskilling'];
            $recommendations[] = ['strategy' => 'bridge', 'reason' => 'Solución temporal para continuidad'];
        }

        if ($budgetSensitivity === 'high') {
            $recommendations = array_values(array_filter($recommendations, fn($r) => in_array($r['strategy'], ['build', 'bridge', 'bind'])));
            $recommendations[] = ['strategy' => 'bind', 'reason' => 'Retención más barata que contratación'];
        }

        if ($automationAllowed) {
            $recommendations[] = ['strategy' => 'bot', 'reason' => 'Automatización parcial permitida'];
        }

        return $recommendations;
    }

    /**
     * Genera/actualiza estrategias sugeridas basadas en gaps.
     */
    public function refreshSuggestedStrategies(WorkforcePlanningScenario $scenario, array $preferences = []): int
    {
        $result = $this->calculateScenarioGaps($scenario);
        $gaps = $result['gaps'];
        $created = 0;

        foreach ($gaps as $gap) {
            if ($gap['gap_headcount'] <= 0 && $gap['gap_level'] <= 0) {
                continue;
            }

            $recs = $this->recommendStrategiesForGap($scenario, $gap, $preferences);

            foreach ($recs as $rec) {
                $exists = $scenario->closureStrategies()
                    ->where('skill_id', $gap['skill_id'])
                    ->where('strategy', $rec['strategy'])
                    ->exists();

                if ($exists) continue;

                ScenarioClosureStrategy::create([
                    'scenario_id' => $scenario->id,
                    'skill_id' => $gap['skill_id'],
                    'strategy' => $rec['strategy'],
                    'strategy_name' => strtoupper($rec['strategy']) . ' (Sugerida)',
                    'description' => $rec['reason'],
                    'estimated_cost' => null,
                    'estimated_time_weeks' => null,
                    'success_probability' => 0.6,
                    'risk_level' => 'medium',
                    'status' => 'proposed',
                    'action_items' => [
                        'Definir plan detallado',
                        'Asignar responsable',
                        'Aprobar presupuesto',
                        'Ejecutar y medir resultados'
                    ]
                ]);

                $created++;
            }
        }

        return $created;
    }

    /**
     * Compara múltiples escenarios según criterios.
     */
    public function compareScenarios(array $scenarioIds, array $criteria = []): array
    {
        $scenarios = WorkforcePlanningScenario::with(['skillDemands', 'closureStrategies', 'milestones'])
            ->whereIn('id', $scenarioIds)
            ->get();

        if ($scenarios->count() < 2) {
            throw new \InvalidArgumentException('Se requieren al menos 2 escenarios para comparar');
        }

        $defaultCriteria = ['cost', 'time', 'risk', 'coverage'];
        $criteria = empty($criteria) ? $defaultCriteria : $criteria;

        $comparison = [];

        foreach ($scenarios as $scenario) {
            $gapsResult = $this->calculateScenarioGaps($scenario);
            
            $scenarioData = [
                'scenario_id' => $scenario->id,
                'scenario_name' => $scenario->name,
                'scenario_type' => $scenario->scenario_type,
            ];

            if (in_array('cost', $criteria)) {
                $scenarioData['total_cost'] = $scenario->getTotalEstimatedCost();
            }

            if (in_array('time', $criteria)) {
                $scenarioData['avg_time_weeks'] = round($scenario->getAverageCompletionTime(), 1);
            }

            if (in_array('risk', $criteria)) {
                $scenarioData['risk_score'] = $gapsResult['summary']['risk_score'];
            }

            if (in_array('coverage', $criteria)) {
                $scenarioData['avg_coverage_pct'] = $gapsResult['summary']['avg_coverage_pct'];
            }

            $scenarioData['critical_skills'] = $gapsResult['summary']['critical_skills'];
            $scenarioData['completion_pct'] = $scenario->getCompletionPercentage();

            $comparison[] = $scenarioData;
        }

        $comparison = collect($comparison)->sortBy(function ($item) use ($criteria) {
            $score = 0;
            if (in_array('cost', $criteria)) $score += $item['total_cost'] ?? 0;
            if (in_array('time', $criteria)) $score += ($item['avg_time_weeks'] ?? 0) * 1000;
            if (in_array('risk', $criteria)) $score += ($item['risk_score'] ?? 0) * 100;
            if (in_array('coverage', $criteria)) $score -= ($item['avg_coverage_pct'] ?? 0) * 50;
            return $score;
        })->values()->all();

        return [
            'criteria' => $criteria,
            'scenarios_compared' => count($comparison),
            'comparison' => $comparison,
            'generated_at' => now()->toISOString(),
        ];
    }
}
