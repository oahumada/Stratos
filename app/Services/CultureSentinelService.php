<?php

namespace App\Services;

use App\Models\PulseResponse;
use App\Models\PsychometricProfile;
use Illuminate\Support\Facades\Log;

class CultureSentinelService
{
    protected AiOrchestratorService $orchestrator;
    protected AuditTrailService $audit;

    public function __construct(AiOrchestratorService $orchestrator, AuditTrailService $audit)
    {
        $this->orchestrator = $orchestrator;
        $this->audit = $audit;
    }

    /**
     * Ejecuta un escaneo completo de salud organizacional.
     * Detecta anomalías en pulsos, rotación de rasgos y nodos de riesgo.
     */
    public function runHealthScan(int $organizationId): array
    {
        $signals = $this->gatherSignals($organizationId);
        $anomalies = $this->detectAnomalies($signals);
        $aiAnalysis = $this->analyzeWithSentinel($signals, $anomalies);

        // Audit Trail
        $this->audit->logDecision(
            'Culture Sentinel',
            "org_{$organizationId}",
            "Health Scan: " . count($anomalies) . " anomalías detectadas",
            $aiAnalysis,
            'Stratos Sentinel'
        );

        return [
            'organization_id' => $organizationId,
            'scan_timestamp' => now()->toIso8601String(),
            'signals' => $signals,
            'anomalies' => $anomalies,
            'ai_analysis' => $aiAnalysis,
            'health_score' => $this->calculateHealthScore($signals),
        ];
    }

    /**
     * Recopila señales de múltiples fuentes de datos.
     */
    protected function gatherSignals(int $organizationId): array
    {
        // Señales de Pulse Surveys (sentimiento promedio reciente)
        $recentPulses = PulseResponse::whereHas('people', function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        $avgSentiment = $recentPulses->avg('sentiment_score') ?? 0;
        $sentimentTrend = $this->calculateTrend($organizationId);

        // Señales de Perfiles Psicométricos (distribución de rasgos)
        $profiles = PsychometricProfile::whereHas('people', function ($q) use ($organizationId) {
            $q->where('organization_id', $organizationId);
        })
            ->where('created_at', '>=', now()->subDays(90))
            ->get();

        $traitDistribution = $profiles->groupBy('trait_name')
            ->map(fn($group) => round($group->avg('score'), 2))
            ->toArray();

        return [
            'pulse_count' => $recentPulses->count(),
            'avg_sentiment' => round($avgSentiment, 1),
            'sentiment_trend' => $sentimentTrend,
            'trait_distribution' => $traitDistribution,
            'profiles_analyzed' => $profiles->count(),
        ];
    }

    /**
     * Detecta anomalías basándose en umbrales y patrones.
     */
    protected function detectAnomalies(array $signals): array
    {
        $anomalies = [];

        // Anomalía: Sentimiento bajo
        if ($signals['avg_sentiment'] < 50) {
            $anomalies[] = [
                'type' => 'low_sentiment',
                'severity' => 'high',
                'message' => "Sentimiento promedio en {$signals['avg_sentiment']}% (umbral: 50%). Riesgo de desconexión cultural.",
                'icon' => 'mdi-emoticon-sad-outline',
            ];
        }

        // Anomalía: Tendencia negativa
        if ($signals['sentiment_trend'] === 'declining') {
            $anomalies[] = [
                'type' => 'declining_trend',
                'severity' => 'medium',
                'message' => 'Tendencia descendente en sentimiento organizacional durante los últimos 30 días.',
                'icon' => 'mdi-trending-down',
            ];
        }

        // Anomalía: Falta de datos (baja participación)
        if ($signals['pulse_count'] < 5) {
            $anomalies[] = [
                'type' => 'low_participation',
                'severity' => 'low',
                'message' => "Solo {$signals['pulse_count']} respuestas en 30 días. Participación insuficiente para análisis confiable.",
                'icon' => 'mdi-account-alert-outline',
            ];
        }

        return $anomalies;
    }

    /**
     * Envía las señales al agente Sentinel para análisis profundo.
     */
    protected function analyzeWithSentinel(array $signals, array $anomalies): array
    {
        $anomalySummary = collect($anomalies)->pluck('message')->implode('; ');

        $prompt = "Actúa como el Stratos Sentinel. He ejecutado un escaneo de salud organizacional y necesito tu análisis profundo.

        Señales Detectadas:
        - Respuestas de Pulse recientes: {$signals['pulse_count']}
        - Sentimiento Promedio: {$signals['avg_sentiment']}%
        - Tendencia: {$signals['sentiment_trend']}
        - Perfiles Analizados: {$signals['profiles_analyzed']}
        
        Anomalías: {$anomalySummary}
        
        Necesito:
        1. Un diagnóstico ejecutivo de 2 líneas.
        2. Las 3 acciones prioritarias para el CEO.
        3. El 'Nodo de Riesgo' más crítico en la organización.
        
        Responde en formato JSON con: 'diagnosis' (string), 'ceo_actions' (array de strings), 'critical_node' (string).";

        try {
            $result = $this->orchestrator->agentThink('Stratos Sentinel', $prompt);
            return $result['response'];
        } catch (\Exception $e) {
            Log::error("Culture Sentinel analysis failed: " . $e->getMessage());
            return [
                'diagnosis' => 'Análisis no disponible temporalmente.',
                'ceo_actions' => ['Revisar manualmente las señales de pulso.'],
                'critical_node' => 'Desconocido'
            ];
        }
    }

    /**
     * Calcula la tendencia comparando los últimos 15 días vs los 15 anteriores.
     */
    protected function calculateTrend(int $organizationId): string
    {
        $recent = PulseResponse::whereHas('people', fn($q) => $q->where('organization_id', $organizationId))
            ->where('created_at', '>=', now()->subDays(15))
            ->avg('sentiment_score') ?? 0;

        $previous = PulseResponse::whereHas('people', fn($q) => $q->where('organization_id', $organizationId))
            ->whereBetween('created_at', [now()->subDays(30), now()->subDays(15)])
            ->avg('sentiment_score') ?? 0;

        if ($recent > $previous + 5) {
            return 'improving';
        }
        if ($recent < $previous - 5) {
            return 'declining';
        }
        return 'stable';
    }

    /**
     * Calcula un score de salud general (0-100).
     */
    protected function calculateHealthScore(array $signals): int
    {
        $score = 50; // Base

        // Sentimiento contribuye 40%
        $score += ($signals['avg_sentiment'] - 50) * 0.4;

        // Tendencia contribuye 10%
        if ($signals['sentiment_trend'] === 'improving') {
            $score += 10;
        } elseif ($signals['sentiment_trend'] === 'declining') {
            $score -= 10;
        }

        // Participación contribuye 10%
        if ($signals['pulse_count'] >= 20) {
            $score += 10;
        } elseif ($signals['pulse_count'] < 5) {
            $score -= 5;
        }

        return max(0, min(100, (int) round($score)));
    }
}
