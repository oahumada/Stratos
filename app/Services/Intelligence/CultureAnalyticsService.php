<?php

namespace App\Services\Intelligence;

use App\Models\PulseSurvey;
use App\Models\PulseResponse;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class CultureAnalyticsService
{
    protected AiOrchestratorService $orchestrator;

    public function __construct(AiOrchestratorService $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * Genera un reporte cualitativo de una encuesta Pulse usando el Navegador de Cultura.
     */
    public function generateClimateReport(int $surveyId): array
    {
        $survey = PulseSurvey::with('responses')->findOrFail($surveyId);
        $responses = $survey->responses;

        if ($responses->isEmpty()) {
            return ['status' => 'error', 'message' => 'Sin respuestas para analizar.'];
        }

        $avgSentiment = $responses->avg('sentiment_score');
        
        $prompt = "Como Navegador de Cultura, analiza los resultados de la encuesta Pulse: '{$survey->title}'.
        
        Métricas Cuantitativas:
        - Total Respuestas: {$responses->count()}
        - Sentiment Score Promedio (0-10): {$avgSentiment}
        
        Respuestas Cualitativas (Muestra):
        ";

        // Tomar una muestra de comentarios si existen
        foreach ($responses->take(10) as $res) {
            $prompt .= "- Answer: " . (is_array($res->answers) ? json_encode($res->answers) : $res->answers) . "\n";
        }

        $prompt .= "\nPor favor, genera un reporte que incluya:
        1. Diagnóstico emocional del equipo.
        2. Principales focos de tensión o riesgo detectados.
        3. 3 recomendaciones accionables para RRHH para mejorar el clima y el engagement.";

        try {
            $result = $this->orchestrator->agentThink('Navegador de Cultura', $prompt);
            return [
                'status' => 'report_generated',
                'survey_title' => $survey->title,
                'metrics' => [
                    'total_responses' => $responses->count(),
                    'avg_sentiment' => $avgSentiment
                ],
                'qualitative_analysis' => $result['response']
            ];
        } catch (\Exception $e) {
            Log::error("Error en reporte de cultura: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
