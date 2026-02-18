<?php

namespace App\Services;

use App\Models\Competency;
use App\Models\CompetencySkill;
use App\Models\PeopleRoleSkills;
use App\Models\Scenario;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioRoleSkill;

class ScenarioAnalyticsService
{
    /**
     * Calcula el IQ del Escenario basado en Readiness ponderado por Capabilities.
     */
    public function calculateScenarioIQ(int $scenarioId): array
    {
        $scenario = Scenario::with([
            'scenarioCapabilities.capability.competencies.competencySkills',
        ])->findOrFail($scenarioId);

        $totalWeightedReadiness = 0;
        $totalStrategicWeight = 0;
        $capabilityBreakdown = [];

        foreach ($scenario->scenarioCapabilities as $scap) {
            $capReadiness = $this->calculateCapabilityReadiness($scenarioId, $scap->capability_id);

            $totalWeightedReadiness += ($capReadiness * $scap->strategic_weight);
            $totalStrategicWeight += $scap->strategic_weight;

            $capabilityBreakdown[] = [
                'id' => $scap->capability_id,
                'name' => $scap->capability->name,
                'readiness' => round($capReadiness * 100, 1),
                'strategic_role' => $scap->strategic_role,
                'strategic_weight' => $scap->strategic_weight,
                'is_incubating' => $scap->capability->isIncubating(),
            ];
        }

        $iq = $totalStrategicWeight > 0 ? ($totalWeightedReadiness / $totalStrategicWeight) * 100 : 0;

        return [
            'scenario_id' => $scenarioId,
            'scenario_name' => $scenario->name,
            'iq' => round($iq, 0),
            'confidence_score' => $this->getConfidenceScore($scenarioId),
            'capabilities' => $capabilityBreakdown,
            'critical_gaps' => collect($capabilityBreakdown)->where('readiness', '<', 30)->values()->toArray(),
        ];
    }

    /**
     * Calcula el Readiness de una Capability (promedio de sus Competencies).
     */
    public function calculateCapabilityReadiness(int $scenarioId, int $capabilityId): float
    {
        $competencyIds = \DB::table('capability_competencies')
            ->where('scenario_id', $scenarioId)
            ->where('capability_id', $capabilityId)
            ->pluck('competency_id');

        if ($competencyIds->isEmpty()) {
            return 0;
        }

        $totalReadiness = 0;
        foreach ($competencyIds as $compId) {
            $totalReadiness += $this->calculateCompetencyReadiness($scenarioId, $compId);
        }

        return $totalReadiness / $competencyIds->count();
    }

    /**
     * Calcula el Readiness de una Competency (promedio ponderado de sus Skills).
     */
    public function calculateCompetencyReadiness(int $scenarioId, int $competencyId): float
    {
        $compSkills = CompetencySkill::where('competency_id', $competencyId)
            ->with('skill')
            ->get();

        if ($compSkills->isEmpty()) {
            return 0;
        }

        $weightedReadinessSum = 0;
        $totalWeight = 0;

        foreach ($compSkills as $cs) {
            $skillReadiness = $this->calculateSkillReadiness($scenarioId, $cs->skill_id);
            $weightedReadinessSum += ($skillReadiness * $cs->weight);
            $totalWeight += $cs->weight;
        }

        return $totalWeight > 0 ? $weightedReadinessSum / $totalWeight : 0;
    }

    /**
     * Calcula el Readiness de una Skill (promedio de readiness por rol).
     */
    public function calculateSkillReadiness(int $scenarioId, int $skillId): float
    {
        $demands = ScenarioRoleSkill::where('scenario_id', $scenarioId)
            ->where('skill_id', $skillId)
            ->where('change_type', '!=', 'obsolete')
            ->get();

        if ($demands->isEmpty()) {
            return 1.0;
        }

        $readinessSum = 0;
        foreach ($demands as $demand) {
            $baseRoleId = \DB::table('scenario_roles')
                ->where('id', $demand->role_id)
                ->value('role_id');

            $avgCurrentLevel = $this->getAverageLevelForRoleAndSkill($baseRoleId, $skillId);

            $readinessSum += $demand->required_level > 0
                ? min(1, $avgCurrentLevel / $demand->required_level)
                : 1;
        }

        return $readinessSum / $demands->count();
    }

    private function getAverageLevelForRoleAndSkill(?int $roleId, int $skillId): float
    {
        if (!$roleId) {
            return 0;
        }

        $hipoAvg = PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
            ->where('people_role_skills.role_id', $roleId)
            ->where('people_role_skills.skill_id', $skillId)
            ->where('people.is_high_potential', true)
            ->avg('people_role_skills.current_level');

        if ($hipoAvg !== null) {
            return (float) $hipoAvg;
        }

        return (float) (PeopleRoleSkills::where('role_id', $roleId)
            ->where('skill_id', $skillId)
            ->avg('current_level') ?: 0);
    }

    /**
     * Calcula el Confidence Score basado en la calidad de la evidencia.
     */
    public function getConfidenceScore(int $scenarioId): float
    {
        // Contar evidencias por tipo
        $total = PeopleRoleSkills::count();

        if ($total == 0) {
            return 0;
        }

        // Ponderación por tipo de evidencia
        $weights = [
            'test' => 1.0,
            'certification' => 0.9,
            'manager_review' => 0.7,
            'self_assessment' => 0.3,
        ];

        $weightedSum = PeopleRoleSkills::selectRaw("
            SUM(CASE 
                WHEN evidence_source = 'test' THEN 1.0
                WHEN evidence_source = 'certification' THEN 0.9
                WHEN evidence_source = 'manager_review' THEN 0.7
                WHEN evidence_source = 'self_assessment' THEN 0.3
                ELSE 0
            END) as weighted_confidence
        ")->value('weighted_confidence') ?: 0;

        return round($weightedSum / $total, 2);
    }

    /**
     * Calcula el impacto proyectado del escenario basado en las estrategias aplicadas.
     */
    public function calculateImpact(int $scenarioId): array
    {
        $scenario = Scenario::with(['capabilities.competencies'])->findOrFail($scenarioId);
        
        $labels = [];
        $actualLevels = [];
        $projectedLevels = [];
        
        // 1. Obtener todas las competencias vinculadas al escenario
        $competencies = \DB::table('capability_competencies')
            ->join('competencies', 'capability_competencies.competency_id', '=', 'competencies.id')
            ->where('capability_competencies.scenario_id', $scenarioId)
            ->select('competencies.id', 'competencies.name')
            ->distinct()
            ->get();

        foreach ($competencies as $comp) {
            $actual = $this->calculateCompetencyReadiness($scenarioId, $comp->id) * 100;
            
            // Proyectado: Buscamos si hay estrategias aprobadas/propuestas para skills de esta competencia
            // Simplificación: si hay estrategias, el gap se reduce un 80% (marginal impact)
            $strategiesCount = \DB::table('scenario_closure_strategies')
                ->join('competency_skills', 'scenario_closure_strategies.skill_id', '=', 'competency_skills.skill_id')
                ->where('scenario_closure_strategies.scenario_id', $scenarioId)
                ->where('competency_skills.competency_id', $comp->id)
                ->count();

            $improvement = $strategiesCount > 0 ? (100 - $actual) * 0.85 : 0;
            $projected = $actual + $improvement;

            $labels[] = $comp->name;
            $actualLevels[] = round($actual, 1);
            $projectedLevels[] = round($projected, 1);
        }

        // KPIs Mejorados basados en datos reales de estrategias
        $strategyStats = \DB::table('scenario_closure_strategies')
            ->where('scenario_id', $scenarioId)
            ->select('strategy', \DB::raw('count(*) as count'), \DB::raw('sum(estimated_cost) as total_cost'))
            ->groupBy('strategy')
            ->get();

        $totalStrategies = $strategyStats->sum('count');
        $totalCost = $strategyStats->sum('total_cost');

        // Desglose de Tiempo a Plena Capacidad (TFC)
        $tfcBreakdown = [
            'buy' => 12,    // Semanas promedio para contratar + onboarding
            'build' => 24,  // Semanas promedio para upskilling real
            'borrow' => 6,  // Semanas promedio para freelance/outsourcing
            'bot' => 16     // Semanas promedio para implementación AI
        ];

        $weightedTFC = 0;
        if ($totalStrategies > 0) {
            foreach ($strategyStats as $stat) {
                $weightedTFC += ($stat->count * ($tfcBreakdown[$stat->strategy] ?? 12));
            }
            $weightedTFC = round($weightedTFC / $totalStrategies, 1);
        }

        $gapClosure = $totalStrategies > 0 ? 85 : 0;
        $riskData = $this->calculateRiskScore($scenarioId, $strategyStats, $actualLevels);
        
        return [
            'gap_closure' => $gapClosure,
            'productivity_index' => 15 + ($gapClosure * 0.8),
            'time_to_fill' => $weightedTFC ?: 12,
            'tfc_breakdown' => $strategyStats->map(fn($s) => [
                'type' => $s->strategy,
                'weeks' => $tfcBreakdown[$s->strategy] ?? 12,
                'count' => $s->count
            ]),
            'estimated_roi' => $totalCost > 0 ? round((($gapClosure * 5000) / $totalCost), 2) : 0,
            'risk_score' => $riskData['score'],
            'risk_level' => $riskData['level'],
            'risk_factors' => $riskData['factors'],
            'summary' => "Basado en " . $totalStrategies . " acciones estratégicas, la organización proyecta un cierre de brechas del " . $gapClosure . "%. " . $riskData['summary'],
            'chart' => [
                'labels' => $labels,
                'actual' => $actualLevels,
                'projected' => $projectedLevels
            ]
        ];
    }

    /**
     * Calcula el nivel de riesgo de ejecución del escenario.
     */
    private function calculateRiskScore(int $scenarioId, $strategyStats, array $actualLevels): array
    {
        $score = 0;
        $factors = [];
        $totalStrategies = $strategyStats->sum('count') ?: 1;
        
        $stats = $strategyStats->pluck('count', 'strategy')->toArray();
        $buyPct = (($stats['buy'] ?? 0) / $totalStrategies) * 100;
        $buildPct = (($stats['build'] ?? 0) / $totalStrategies) * 100;
        $botPct = (($stats['bot'] ?? 0) / $totalStrategies) * 100;

        // 1. Riesgo por dependencia de mercado (BUY)
        if ($buyPct > 40) {
            $score += 25;
            $factors[] = "Alta dependencia de contratación externa (" . round($buyPct) . "%). Riesgo de volatilidad salarial y escasez de talento.";
        }

        // 2. Riesgo por tiempos de maduración (BUILD)
        if ($buildPct > 50) {
            $score += 20;
            $factors[] = "Carga excesiva en desarrollo interno. Riesgo de fatiga organizacional y retrasos en upskilling.";
        }

        // 3. Riesgo de implementación tecnológica (BOT)
        if ($botPct > 30) {
            $score += 30;
            $factors[] = "Alta transformación hacia IA (" . round($botPct) . "%). Riesgo de fricción cultural y desafíos de integración técnica.";
        }

        // 4. Riesgo por calidad de datos (Confidence Score)
        $confidence = $this->getConfidenceScore($scenarioId);
        if ($confidence < 0.6) {
            $penalty = (0.6 - $confidence) * 50;
            $score += $penalty;
            $factors[] = "Baja calidad de evidencia en datos de origen (Confidence: " . ($confidence * 100) . "%). El plan podría basarse en supuestos imprecisos.";
        }

        // 5. Riesgo por profundidad de brecha
        $avgActual = count($actualLevels) > 0 ? array_sum($actualLevels) / count($actualLevels) : 100;
        if ($avgActual < 40) {
            $score += 20;
            $factors[] = "Brechas de competencia críticas identificadas. La magnitud del cambio requerido es estructuralmente alta.";
        }

        $score = min(100, round($score));
        $level = 'Bajo';
        if ($score > 75) $level = 'Crítico';
        elseif ($score > 50) $level = 'Alto';
        elseif ($score > 25) $level = 'Medio';

        $summary = "El riesgo de ejecución se califica como **" . $level . "** (" . $score . "/100).";
        if ($score > 60) {
            $summary .= " Se recomienda revisar los factores de mitigación de la brecha logística.";
        } else {
            $summary .= " El plan es balanceado y ejecutable dentro de los márgenes estándar.";
        }

        return [
            'score' => $score,
            'level' => $level,
            'factors' => $factors,
            'summary' => $summary
        ];
    }
}
