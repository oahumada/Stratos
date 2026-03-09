<?php

namespace App\Services\Intelligence;

use App\Models\People;
use App\Models\SmartAlert;
use App\Services\CultureSentinelService;
use App\Services\GapAnalysisService;
use App\Services\Scenario\CareerPathService;
use Illuminate\Support\Facades\Log;

/**
 * NudgeOrchestratorService — El cerebro detrás de las micro-intervenciones. * Orquesta "nudges" (codazos) para:
 * - Cerrar brechas de skills críticas.
 * - Prevenir riesgos culturales detectados por el Sentinel.
 * - Impulsar misiones de gamificación.
 * - Fomentar la mentoría activa.
 */
class NudgeOrchestratorService
{
    public function __construct(
        protected SmartAlertService $alertService,
        protected GapAnalysisService $gapAnalysis,
        protected CultureSentinelService $sentinel,
        protected CareerPathService $careerPaths,
        protected \App\Services\Intelligence\RetentionDeepPredictorService $retentionPredictor
    ) {}

    /**
     * Ejecuta el ciclo de orquestación para una organización.
     */
    public function orchestrate(int $orgId): array
    {
        Log::info("Iniciando orquestación de nudges para organización $orgId");

        $nudges = [];

        // 1. Nudges de Skills (Brechas Críticas)
        $nudges = array_merge($nudges, $this->generateSkillNudges($orgId));

        // 2. Nudges de Cultura (Riesgos Sentinel)
        $nudges = array_merge($nudges, $this->generateCultureNudges($orgId));

        // 3. Nudges de Carrera (Movilidad)
        $nudges = array_merge($nudges, $this->generateCareerNudges($orgId));

        // 4. Nudges de Retención (Fuga de Talento Crítico)
        $nudges = array_merge($nudges, $this->generateRetentionNudges($orgId));

        return [
            'total_nudges_generated' => count($nudges),
            'nudges' => $nudges,
        ];
    }

    /**
     * Genera nudges basados en brechas de habilidades críticas.
     */
    protected function generateSkillNudges(int $orgId): array
    {
        $generated = [];
        // Obtener personas con brechas críticas (> 2 niveles) en skills estratégicas
        // Por simplicidad, tomamos una muestra de personas
        $people = People::where('organization_id', $orgId)->take(10)->get();

        foreach ($people as $person) {
            /** @var \App\Models\People $person */
            if (!$person->role) {
                continue;
            }
            $analysis = $this->gapAnalysis->calculate($person, $person->role);
            $criticalGaps = collect($analysis['gaps'])->where('gap', '>=', 2);

            if ($criticalGaps->isNotEmpty()) {
                $skill = $criticalGaps->first();
                $title = "🚀 Impulsa tu carrera: {$skill['skill_name']}";
                $message = "Hola {$person->first_name}, detectamos que cerrar tu brecha en {$skill['skill_name']} te acerca al siguiente nivel de maestría. ¿Quieres ver opciones de aprendizaje?";
                
                $this->alertService->notify($orgId, $title, $message, 'info', 'learning', [
                    'label' => 'Ver Ruta de Desarrollo',
                    'url' => "/mi-stratos/development-path",
                ]);

                $generated[] = ['person' => $person->full_name, 'type' => 'skill'];
            }
        }

        return $generated;
    }

    /**
     * Genera nudges basados en anomalías de cultura detectadas.
     */
    protected function generateCultureNudges(int $orgId): array
    {
        $generated = [];
        $health = $this->sentinel->runHealthScan($orgId);

        if ($health['health_score'] < 60) {
            $diagnosis = $health['ai_analysis']['diagnosis'] ?? 'Riesgo de desconexión detectado.';
            $title = "⚠️ Alerta de Salud Organizacional";
            $message = "El Sentinel detectó una caída en el sentimiento: $diagnosis. Recomendamos una charla de feedback abierta con los líderes de equipo.";

            $this->alertService->notify($orgId, $title, $message, 'critical', 'culture', [
                'label' => 'Ver Análisis de Cultura',
                'url' => "/intelligence/culture-sentinel",
            ]);

            $generated[] = ['org_id' => $orgId, 'type' => 'culture'];
        }

        return $generated;
    }

    /**
     * Genera nudges sobre oportunidades de carrera (Siguiente paso).
     */
    protected function generateCareerNudges(int $orgId): array
    {
        $generated = [];
        // Simular para una persona clave
        $person = People::where('organization_id', $orgId)->whereNotNull('role_id')->first();
        
        if ($person) {
            $paths = $this->careerPaths->calculateCareerPaths($person->id, 1);
            $bestPath = $paths['recommended_path'] ?? null;

            if ($bestPath && $bestPath['match_score'] >= 60) {
                $title = "🌟 Próximo Desafío: {$bestPath['role_name']}";
                $message = "Tienes un 60% de compatibilidad con el rol de {$bestPath['role_name']}. ¿Te gustaría explorar qué falta para estar listo?";

                $this->alertService->notify($orgId, $title, $message, 'info', 'career', [
                    'label' => 'Explorar Rol',
                    'url' => "/career-paths/predict/{$person->id}/{$bestPath['role_id']}",
                ]);

                $generated[] = ['person' => $person->full_name, 'type' => 'career'];
            }
        }

        return $generated;
    }

    /**
     * Genera nudges de retención para perfiles estratégicos con riesgo alto.
     */
    protected function generateRetentionNudges(int $orgId): array
    {
        $generated = [];
        
        // Identificar personas clave (High Potential o en roles críticos)
        $strategicPeople = People::where('organization_id', $orgId)
            ->where(function($q) {
                $q->where('is_high_potential', true)
                  ->orWhereHas('roleSkills', fn($sq) => $sq->where('is_critical', true));
            })
            ->take(5)
            ->get();

        foreach ($strategicPeople as $person) {
            $prediction = $this->retentionPredictor->predict($person->id);

            if (($prediction['flight_risk_score'] ?? 0) > 70) {
                $title = "🚨 Riesgo Crítico: {$person->full_name}";
                $message = "La IA detectó un riesgo de fuga del {$prediction['flight_risk_score']}% debido a '{$prediction['primary_driver']}'. El impacto financiero estimado es de \${$prediction['financial_impact']['replacement_cost_usd']}.";
                
                // Notificar a administradores (CEO/HR)
                $this->alertService->notify($orgId, $title, $message, 'critical', 'retention', [
                    'label' => 'Ver Plan de Retención',
                    'url' => "/intelligence/retention/{$person->id}",
                ]);

                $generated[] = ['person' => $person->full_name, 'type' => 'retention', 'score' => $prediction['flight_risk_score']];
            }
        }

        return $generated;
    }
}
