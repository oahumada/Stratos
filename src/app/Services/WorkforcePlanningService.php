<?php

namespace App\Services;

use App\Models\WorkforcePlanningScenario;
use App\Models\WorkforcePlanningRoleForecast;
use App\Models\WorkforcePlanningMatch;
use App\Models\WorkforcePlanningSkillGap;
use App\Models\WorkforcePlanningAnalytic;
use App\Repositories\WorkforcePlanningRepository;
use App\Models\People;
use App\Models\Skills;
use App\Models\PeopleRoleSkills;
use Illuminate\Support\Facades\DB;

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
}
