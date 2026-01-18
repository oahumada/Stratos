<?php
// app/Services/ScenarioAnalysisService.php
namespace App\Services;

use App\Models\Scenario;
use App\Repository\ScenarioRepository;
use App\Repository\EvaluationRepository;

class ScenarioAnalysisService
{
    public function __construct(
        private ScenarioRepository $scenarioRepo,
        private EvaluationRepository $evaluationRepo,
        private EvolutionEngineService $evolutionEngine
    ) {}

    /**
     * Calcula el "Health Score" del escenario
     */
    public function calculateHealth(Scenario $scenario): array
    {
        $capabilities = $scenario->capabilities;
        $totalCapabilities = $capabilities->count();
        
        if ($totalCapabilities === 0) {
            return ['health' => 0, 'coverage' => 0, 'risk' => 0];
        }

        $healthyCount = 0;
        $atRiskCount = 0;

        foreach ($capabilities as $capability) {
            $requiredLevel = $capability->pivot->required_level;
            
            // Aquí deberías obtener el nivel actual promedio de todos los usuarios
            // Para simplificar, asumimos que tienes una función que lo calcula
            $currentLevel = $this->getAverageCapabilityLevel($capability->id, $scenario->id);
            
            $gap = $requiredLevel - $currentLevel;
            
            if ($gap <= 0) {
                $healthyCount++;
            } elseif ($gap >= 2) {
                $atRiskCount++;
            }
        }

        $health = round(($healthyCount / $totalCapabilities) * 100);
        $coverage = round((($totalCapabilities - $atRiskCount) / $totalCapabilities) * 100);

        return [
            'health' => $health,
            'coverage' => $coverage,
            'risk' => $atRiskCount
        ];
    }

    private function getAverageCapabilityLevel(int $capabilityId, int $scenarioId): float
    {
        // Implementación simplificada
        // En producción, esto debería calcular el promedio de todos los usuarios
        return 3.0;
    }
}