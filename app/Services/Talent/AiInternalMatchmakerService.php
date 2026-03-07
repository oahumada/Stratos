<?php

namespace App\Services\Talent;

use App\Models\DevelopmentPath;
use App\Models\JobOpening;
use App\Models\People;
use App\Services\AiOrchestratorService;
use App\Services\Assessment\Stratos360TriangulationService;
use App\Services\GapAnalysisService;
use Illuminate\Support\Facades\Log;

class AiInternalMatchmakerService
{
    public function __construct(
        protected AiOrchestratorService $ai,
        protected GapAnalysisService $gapService,
        protected Stratos360TriangulationService $triangulationService,
        protected \App\Services\Intelligence\RetentionDeepPredictorService $retentionService
    ) {}

    /**
     * Genera un reporte de "Fitness Score" enriquecido con IA para un candidato interno.
     */
    public function assessCandidateFitness(int $openingId, int $candidateId): array
    {
        $opening = JobOpening::with('role.skills')->findOrFail($openingId);
        $candidate = People::with(['role', 'skills', 'pulses'])->findOrFail($candidateId);

        // 1. Análisis Técnico (Gaps)
        $gapAnalysis = $this->gapService->calculate($candidate, $opening->role);

        // 2. Análisis de Desempeño (360 Triangulado)
        // Intentamos obtener datos de triangulación previa o calculamos un resumen rápido
        $performanceData = $this->getPerformanceSummary($candidate);

        // 3. Análisis de Crecimiento (Growth Navigator)
        $growthData = $this->getGrowthSummary($candidate);

        // 4. Análisis de Retención Profundo (Flight Risk)
        $retentionAnalysis = $this->retentionService->predict($candidateId);

        // 5. Construcción del Prompt Maestro de Resonancia
        $prompt = $this->buildMatchmakerPrompt($opening, $candidate, $gapAnalysis, $performanceData, $growthData, $retentionAnalysis);

        try {
            $agentResponse = $this->ai->agentThink('Stratos Matchmaker', $prompt);

            $analysisString = $agentResponse['response'] ?? '{}';
            $analysisString = str_replace(['```json', '```'], '', $analysisString);
            $analysis = json_decode(trim($analysisString), true);

            return [
                'status' => 'success',
                'fitness_data' => $analysis,
                'metadata' => [
                    'match_percentage' => $gapAnalysis['match_percentage'],
                    'performance_avg' => $performanceData['calibrated_avg'] ?? null,
                    'learning_velocity' => $growthData['velocity_score'] ?? 'N/A',
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Stratos Matchmaker Error: '.$e->getMessage());

            return [
                'status' => 'error',
                'message' => 'No se pudo generar el match inteligente.',
            ];
        }
    }

    protected function getPerformanceSummary(People $candidate): array
    {
        // En una implementación real, buscaríamos el último reporte de triangulación guardado
        // Por ahora simulamos los datos clave que extraería el servicio
        return [
            'calibrated_avg' => $candidate->skills()->avg('current_level') ?? 0,
            'top_competency' => 'Adaptabilidad', // placeholder
            'last_360_feedback' => 'Consistente cumplimiento de objetivos técnicos con alta calidad de entrega.',
        ];
    }

    protected function getGrowthSummary(People $candidate): array
    {
        $activePaths = DevelopmentPath::where('people_id', $candidate->id)
            ->whereIn('status', ['active', 'in_progress'])
            ->with('actions')
            ->get();

        $totalProgress = $activePaths->avg('progress') ?? 0;
        $velocity = 'Low';
        if ($totalProgress > 50) {
            $velocity = 'High';
        } elseif ($totalProgress > 20) {
            $velocity = 'Medium';
        }

        return [
            'active_paths_count' => $activePaths->count(),
            'avg_progress' => round($totalProgress, 2),
            'velocity_score' => $velocity,
            'current_focus' => $activePaths->pluck('action_title')->first() ?? 'Sin foco activo',
        ];
    }

    protected function buildMatchmakerPrompt($opening, $candidate, $gapAnalysis, $performance, $growth, $retention): string
    {
        return "Actúa como el 'Stratos Internal Matchmaker'. Tu misión es evaluar si {$candidate->full_name} es el candidato IDEAL para la vacante de '{$opening->title}'.\n\n".
               "📊 ENGINE DATA:\n".
               '1. GAP ANALYSIS: '.json_encode($gapAnalysis)."\n".
               '2. PERFORMANCE (Stratos 360): '.json_encode($performance)."\n".
               '3. GROWTH VELOCITY (Navigator): '.json_encode($growth)."\n".
               '4. RETENTION RISK (Sentinel): '.json_encode($retention)."\n\n".
               "🎯 TU TAREA:\n".
               "Genera un reporte de 'Fitness Score' que no solo mire el match de skills hoy, sino el POTENCIAL de éxito y riesgo de retención.\n\n".
               "DEBES DEVOLVER UN JSON con esta estructura:\n".
               "{\n".
               "  \"fitness_score\": <1-100>,\n".
               "  \"hidden_potential_label\": \"Alto / Medio / Bajo\",\n".
               "  \"strengths\": [\"punto fuerte 1\", \"punto fuerte 2\"],\n".
               "  \"risks\": [\"riesgo/brecha crítica 1\"],\n".
               "  \"strategic_rationale\": \"Párrafo clave de por qué sí o por qué no.\",\n".
               "  \"retention_forecast\": \"Bajo / Medio / Alto riesgo de que el rol le quede chico o grande.\",\n".
               "  \"mitigation_plan\": \"Recomendación para cerrar los gaps específicos en los primeros 90 días.\"\n".
               '}';
    }
}
