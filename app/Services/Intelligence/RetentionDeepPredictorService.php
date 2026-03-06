<?php

namespace App\Services\Intelligence;

use App\Models\EmployeePulse;
use App\Models\People;
use App\Models\DevelopmentPath;
use App\Services\AiOrchestratorService;
use App\Services\GapAnalysisService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RetentionDeepPredictorService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator,
        protected GapAnalysisService $gapService
    ) {}

    /**
     * Realiza un análisis multidimensional de riesgo de fuga para un colaborador.
     */
    public function predict(int $peopleId): array
    {
        $person = People::with(['role', 'pulses', 'roleSkills'])->findOrFail($peopleId);
        
        // 1. Dataset de Señales Emocionales (Pulsos)
        $pulses = EmployeePulse::where('people_id', $peopleId)
            ->latest()
            ->limit(5)
            ->get();
            
        $avgEnps = $pulses->avg('e_nps') ?? 7;
        $avgStress = $pulses->avg('stress_level') ?? 2;
        $sentimentTrend = $this->calculatePulseTrend($pulses);

        // 2. Dataset de Estancamiento (Antigüedad vs Rol)
        $tenureMonths = $person->hire_date ? Carbon::parse($person->hire_date)->diffInMonths(now()) : 0;
        $stagnationScore = $this->calculateStagnation($tenureMonths);

        // 3. Dataset de Desajuste de Ambición (Overqualification)
        $currentGap = $this->gapService->calculate($person, $person->role);
        $overqualificationRisk = $currentGap['match_percentage'] > 90 ? 'High' : 'Low';

        // 4. Dataset de Crecimiento (Navigator Engagement)
        $growthEngagement = DevelopmentPath::where('people_id', $peopleId)
            ->where('updated_at', '>=', now()->subMonths(3))
            ->count() > 0 ? 'Active' : 'Stale';

        // 5. Orquestación con IA para el "Reasoning"
        $prompt = $this->buildAnalysisPrompt($person, $avgEnps, $avgStress, $sentimentTrend, $stagnationScore, $overqualificationRisk, $growthEngagement);
        
        try {
            $response = $this->orchestrator->agentThink('Stratos Retention Cortex', $prompt);
            $analysis = json_decode($this->cleanJson($response['response']), true);

            return [
                'flight_risk_score' => $analysis['risk_score'] ?? 50,
                'risk_level' => $analysis['risk_level'] ?? 'medium',
                'primary_driver' => $analysis['primary_driver'] ?? 'unknown',
                'retention_plan' => $analysis['retention_plan'] ?? [],
                'indicators' => [
                    'sentiment' => $avgEnps,
                    'stress' => $avgStress,
                    'stagnation' => $stagnationScore,
                    'overqualification' => $overqualificationRisk,
                    'growth_status' => $growthEngagement
                ]
            ];
        } catch (\Exception $e) {
            Log::error("RetentionDeepPredictor Error: " . $e->getMessage());
            return ['error' => 'Prediction failed'];
        }
    }

    protected function calculatePulseTrend($pulses): string
    {
        if ($pulses->count() < 2) {
            return 'Stable';
        }
        $latest = $pulses->first()->e_nps;
        $oldest = $pulses->last()->e_nps;
        
        if ($latest < $oldest - 1) {
            return 'Declining';
        }
        if ($latest > $oldest + 1) {
            return 'Improving';
        }
        return 'Stable';
    }

    protected function calculateStagnation(int $months): int
    {
        // Heurística: más de 24 meses en el mismo rol sin cambios significativos = score sube
        if ($months < 12) {
            return 10;
        }
        if ($months < 24) {
            return 40;
        }
        return 80;
    }

    protected function buildAnalysisPrompt($person, $enps, $stress, $trend, $stagnation, $overqual, $growth): string
    {
        return "Actúa como el 'Stratos Retention Cortex'. Analiza el riesgo de fuga para {$person->first_name} ({$person->role->name}).\n\n" .
               "DATOS DETECTADOS:\n" .
               "- Engagement (eNPS): {$enps}/10 (Tendencia: {$trend})\n" .
               "- Nivel de Estrés: {$stress}/5\n" .
               "- Score de Estancamiento (Tenure): {$stagnation}/100\n" .
               "- Riesgo de Sobrecalificación: {$overqual}\n" .
               "- Actividad de Aprendizaje: {$growth}\n\n" .
               "REGLAS:\n" .
               "1. Si Risk Score > 70, clasificar como 'High'.\n" .
               "2. Overqualification + Stale Growth es una señal crítica de rotación externa inmediata.\n" .
               "3. Stress Alto + Declining Sentiment indica riesgo de Burnout.\n\n" .
               "Devuelve un JSON con:\n" .
               "- risk_score (0-100)\n" .
               "- risk_level (low/medium/high)\n" .
               "- primary_driver (la causa principal)\n" .
               "- retention_plan (array de 3 acciones concretas para retenerlo)\n";
    }

    protected function cleanJson($string): string
    {
        return trim(str_replace(['```json', '```'], '', $string));
    }
}
