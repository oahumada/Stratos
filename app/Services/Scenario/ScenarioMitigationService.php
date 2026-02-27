<?php

namespace App\Services\Scenario;

use App\Services\AiOrchestratorService;
use App\Models\Scenario;
use App\Services\AuditTrailService;

class ScenarioMitigationService
{
    protected AiOrchestratorService $orchestrator;
    protected AuditTrailService $audit;

    public function __construct(AiOrchestratorService $orchestrator, AuditTrailService $audit)
    {
        $this->orchestrator = $orchestrator;
        $this->audit = $audit;
    }

    /**
     * Genera un plan de mitigación agéntico basado en los riesgos de un escenario.
     */
    public function generateMitigationPlan(int $scenarioId, array $currentMetrics): array
    {
        $scenario = Scenario::findOrFail($scenarioId);
        
        $prompt = "Actúa como el Stratos Sentinel. He detectado los siguientes riesgos en la simulación del escenario '{$scenario->name}':
        
        Métricas Actuales:
        - Fricción Cultural: {$currentMetrics['cultural_friction']}%
        - Probabilidad de Éxito: {$currentMetrics['success_probability']}%
        - Sinergia Estimada: {$currentMetrics['synergy_score']}/10
        
        Necesito que generes un plan de remediación instantánea que incluya:
        1. Tres acciones concretas para reducir la fricción cultural.
        2. Una recomendación de capacitación técnica para el nodo de mayor riesgo.
        3. Un 'Insight de Seguridad' sobre la integridad del plan.
        
        Responde en formato JSON con las llaves: 'actions' (array de strings), 'training' (string), 'security_insight' (string).";

        $result = $this->orchestrator->agentThink('Stratos Sentinel', $prompt);
        $plan = $result['response'];

        // Audit Trail
        $this->audit->logDecision(
            'Scenario IQ',
            "scenario_{$scenarioId}",
            "Generación de Plan de Mitigación",
            $plan,
            'Stratos Sentinel'
        );

        return $plan;
    }
}
