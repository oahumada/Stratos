<?php

namespace App\Services;

use App\Models\Scenario;
use App\Models\ScenarioCapability;
use App\Models\ScenarioRoleSkill;
use App\Models\PersonRoleSkill;
use App\Models\Competency;
use App\Models\CompetencySkill;
use Illuminate\Support\Facades\DB;

class ScenarioAnalyticsService
{
    /**
     * Calcula el IQ del Escenario basado en Readiness ponderado por Capabilities.
     */
    public function calculateScenarioIQ(int $scenarioId): array
    {
        $scenario = Scenario::with([
            'scenarioCapabilities.capability.competencies.competencySkills'
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
                'is_incubating' => $scap->capability->isIncubating()
            ];
        }

        $iq = $totalStrategicWeight > 0 ? ($totalWeightedReadiness / $totalStrategicWeight) * 100 : 0;

        return [
            'scenario_id' => $scenarioId,
            'scenario_name' => $scenario->name,
            'iq' => round($iq, 0),
            'confidence_score' => $this->getConfidenceScore($scenarioId),
            'capabilities' => $capabilityBreakdown,
            'critical_gaps' => collect($capabilityBreakdown)->where('readiness', '<', 30)->values()->toArray()
        ];
    }

    /**
     * Calcula el Readiness de una Capability (promedio de sus Competencies).
     */
    public function calculateCapabilityReadiness(int $scenarioId, int $capabilityId): float
    {
        $competencies = Competency::where('capability_id', $capabilityId)->get();
      
        if ($competencies->isEmpty()) {
            return 0;
        }

        $totalReadiness = 0;
        foreach ($competencies as $comp) {
            $totalReadiness += $this->calculateCompetencyReadiness($scenarioId, $comp->id);
        }

        return $totalReadiness / $competencies->count();
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
        // Obtener todas las demandas de esta skill en el escenario
        $demands = ScenarioRoleSkill::where('scenario_id', $scenarioId)
            ->where('skill_id', $skillId)
            ->where('change_type', '!=', 'obsolete') // Ignorar skills obsoletas
            ->get();

        if ($demands->isEmpty()) {
            return 1.0; // Si no se requiere, estamos "listos"
        }

        $readinessSum = 0;
        $rolesEvaluated = 0;

        foreach ($demands as $demand) {
            // Obtener nivel promedio actual de las personas en ese rol
            $avgCurrentLevel = PersonRoleSkill::where('role_id', $demand->role_id)
                ->where('skill_id', $skillId)
                ->avg('current_level') ?: 0;

            // Calcular readiness: min(1, current/required)
            $readiness = $demand->required_level > 0 
                ? min(1, $avgCurrentLevel / $demand->required_level) 
                : 1;
          
            $readinessSum += $readiness;
            $rolesEvaluated++;
        }

        return $rolesEvaluated > 0 ? $readinessSum / $rolesEvaluated : 0;
    }

    /**
     * Calcula el Confidence Score basado en la calidad de la evidencia.
     */
    public function getConfidenceScore(int $scenarioId): float
    {
        // Contar evidencias por tipo
        $total = PersonRoleSkill::count();
      
        if ($total == 0) {
            return 0;
        }

        // Ponderación por tipo de evidencia
        $weights = [
            'test' => 1.0,
            'certification' => 0.9,
            'manager_review' => 0.7,
            'self_assessment' => 0.3
        ];

        $weightedSum = PersonRoleSkill::selectRaw('
            SUM(CASE 
                WHEN evidence_source = "test" THEN 1.0
                WHEN evidence_source = "certification" THEN 0.9
                WHEN evidence_source = "manager_review" THEN 0.7
                WHEN evidence_source = "self_assessment" THEN 0.3
                ELSE 0
            END) as weighted_confidence
        ')->value('weighted_confidence') ?: 0;

        return round($weightedSum / $total, 2);
    }

    /**
     * Obtiene un breakdown detallado de gaps por competencia.
     */
    public function getCompetencyGapAnalysis(int $scenarioId, int $roleId): array
    {
        $competencies = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
            ->where('role_id', $roleId)
            ->with('competency.competencySkills.skill')
            ->get();

        $gaps = [];

        foreach ($competencies as $scenarioComp) {
            $readiness = $this->calculateCompetencyReadiness($scenarioId, $scenarioComp->competency_id);
          
            $gaps[] = [
                'competency_id' => $scenarioComp->competency_id,
                'competency_name' => $scenarioComp->competency->name,
                'required_level' => $scenarioComp->required_level,
                'readiness' => round($readiness * 100, 1),
                'gap' => round((1 - $readiness) * 100, 1),
                'is_core' => $scenarioComp->is_core,
                'change_type' => $scenarioComp->change_type
            ];
        }

        return $gaps;
    }
}