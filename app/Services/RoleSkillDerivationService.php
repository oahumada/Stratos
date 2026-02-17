<?php

namespace App\Services;

use App\Models\CompetencySkill;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioRoleSkill;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleSkillDerivationService
{
    /**
     * Deriva skills automáticamente desde las competencias asignadas a un rol en un escenario.
     *
     * @return array Estadísticas de la derivación
     */
    public function deriveSkillsFromCompetencies(int $scenarioId, int $roleId): array
    {
        return DB::transaction(function () use ($scenarioId, $roleId) {

            // 1. Eliminar skills derivadas anteriormente (preservar las manuales)
            $deletedCount = ScenarioRoleSkill::where('scenario_id', $scenarioId)
                ->where('role_id', $roleId)
                ->where('source', 'competency')
                ->delete();

            // 2. Obtener competencias del rol en el escenario
            $competencies = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
                ->where('role_id', $roleId)
                ->with('competency')
                ->get();

            if ($competencies->isEmpty()) {
                Log::info("No competencies found for scenario {$scenarioId}, role {$roleId}");

                return [
                    'deleted' => $deletedCount,
                    'created' => 0,
                    'competencies_processed' => 0,
                ];
            }

            $createdCount = 0;

            // 3. Para cada competencia, derivar sus skills
            foreach ($competencies as $scenarioComp) {
                $compSkills = CompetencySkill::where('competency_id', $scenarioComp->competency_id)
                    ->with('skill')
                    ->get();

                foreach ($compSkills as $cs) {
                    // Calcular el nivel requerido de la skill basado en el nivel de la competencia
                    // Opción 1: Heredar directamente el nivel de la competencia
                    $requiredLevel = $scenarioComp->required_level;

                    // Opción 2 (más sofisticada): Ponderar por el peso de la skill
                    // $requiredLevel = $this->calculateWeightedLevel($scenarioComp->required_level, $cs->weight);

                    // Obtener nivel promedio actual de las personas en ese rol (base rol)
                    $baseRoleId = DB::table('scenario_roles')
                        ->where('id', $roleId)
                        ->value('role_id');

                    $avgCurrentLevel = 0;
                    if ($baseRoleId) {
                        $avgCurrentLevel = DB::table('people_role_skills')
                            ->where('role_id', $baseRoleId)
                            ->where('skill_id', $cs->skill_id)
                            ->avg('current_level') ?: 0;
                    }

                    ScenarioRoleSkill::create([
                        'scenario_id' => $scenarioId,
                        'role_id' => $roleId,
                        'skill_id' => $cs->skill_id,
                        'current_level' => round($avgCurrentLevel),
                        'required_level' => max(1, $requiredLevel),
                        'is_critical' => $scenarioComp->is_core, // Heredar criticidad
                        'source' => 'competency',
                        'competency_id' => $scenarioComp->competency_id,
                        'change_type' => $scenarioComp->change_type,
                    ]);

                    $createdCount++;
                }
            }

            Log::info("Derived {$createdCount} skills from {$competencies->count()} competencies for scenario {$scenarioId}, role {$roleId}");

            return [
                'deleted' => $deletedCount,
                'created' => $createdCount,
                'competencies_processed' => $competencies->count(),
            ];
        });
    }

    /**
     * Deriva skills para TODOS los roles de un escenario.
     */
    public function deriveAllSkillsForScenario(int $scenarioId): array
    {
        $roles = ScenarioRoleCompetency::where('scenario_id', $scenarioId)
            ->distinct('role_id')
            ->pluck('role_id');

        $results = [];
        foreach ($roles as $roleId) {
            $results[$roleId] = $this->deriveSkillsFromCompetencies($scenarioId, $roleId);
        }

        return $results;
    }

    /**
     * Calcula el nivel requerido de una skill ponderando por su peso en la competencia.
     * (Opcional - para implementación más sofisticada)
     */
    private function calculateWeightedLevel(int $competencyLevel, int $skillWeight): int
    {
        // Ejemplo: Si la competencia es nivel 4 y la skill tiene peso 80/100,
        // la skill podría requerir nivel 3 o 4 dependiendo de su importancia
        $weightFactor = $skillWeight / 100;
        $calculatedLevel = round($competencyLevel * $weightFactor);

        return max(1, min(5, $calculatedLevel)); // Asegurar rango 1-5
    }
}
