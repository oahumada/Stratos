<?php

namespace App\Services\Intelligence;

use App\Models\DevelopmentPath;
use App\Models\EmployeePulse;
use App\Models\People;
use App\Services\AiOrchestratorService;
use App\Services\GapAnalysisService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        $currentGap = $this->gapService->calculate($person, $person->role);
        $overqualificationRisk = $currentGap['match_percentage'] > 90 ? 'High' : 'Low';

        // 4. Dataset de Crecimiento (Navigator Engagement)
        $growthEngagement = DevelopmentPath::where('people_id', $peopleId)
            ->where('updated_at', '>=', now()->subMonths(3))
            ->count() > 0 ? 'Active' : 'Stale';

        // 5. Contexto Colectivo y Fricción de Liderazgo
        $deptAvgPulse = EmployeePulse::whereHas('person', function ($q) use ($person) {
            $q->where('department_id', $person->department_id);
        })->latest()->limit(20)->avg('e_nps') ?? 7;

        $manager = $person->managers()->first();
        $managerHealth = 100;
        if ($manager) {
            // Un proxy rápido: si el eNPS del equipo del manager es bajo
            $managerHealth = EmployeePulse::whereHas('person', function ($q) use ($manager) {
                $q->where('department_id', $manager->department_id);
            })->latest()->limit(20)->avg('e_nps') * 10 ?? 100;
        }

        // 6. Riesgo de Continuidad y Valor de Mercado (Mock)
        $isStrategic = $person->is_high_potential || $person->roleSkills()->where('is_critical', true)->count() > 3;
        $marketValueBenchmark = 1.15; // 15% por encima de su salario actual (Mock)

        // 7. Orquestación con IA para el "Reasoning"
        $prompt = $this->buildAnalysisPrompt($person, $avgEnps, $avgStress, $sentimentTrend, $stagnationScore, $overqualificationRisk, $growthEngagement, $deptAvgPulse, $managerHealth);

        try {
            $response = $this->orchestrator->agentThink('Stratos Retention Cortex', $prompt);
            $analysis = json_decode($this->cleanJson($response['response']), true);

            // 6. Impacto financiero (ROI)
            $salary = 45000; // Mock: En producción vendría de la ficha del empleado
            $replacementCost = $salary * 0.5;

            return [
                'flight_risk_score' => $analysis['risk_score'] ?? 50,
                'risk_level' => $analysis['risk_level'] ?? 'medium',
                'primary_driver' => $analysis['primary_driver'] ?? 'unknown',
                'predicted_impact' => $analysis['predicted_impact'] ?? 'Mejora esperada: 15-20% en estabilidad.',
                'retention_plan' => [
                    'individual' => $analysis['retention_plan'] ?? ($analysis['individual_actions'] ?? []),
                    'team' => $analysis['team_interventions'] ?? [],
                    'organizational' => $analysis['organizational_policies'] ?? [],
                ],
                'financial_impact' => [
                    'replacement_cost_usd' => $replacementCost,
                    'potential_loss_index' => ($analysis['risk_score'] ?? 50) / 100 * $replacementCost,
                ],
                'strategic_metrics' => [
                    'market_position' => $marketValueBenchmark > 1.1 ? 'Underpaid' : 'Fair',
                    'business_continuity_risk' => $isStrategic ? 'Critical' : 'Standard',
                    'leadership_friction' => $managerHealth < 60 ? 'High' : 'Low',
                ],
                'indicators' => [
                    'sentiment' => $avgEnps,
                    'stress' => $avgStress,
                    'stagnation' => $stagnationScore,
                    'overqualification' => $overqualificationRisk,
                    'growth_status' => $growthEngagement,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('RetentionDeepPredictor Error: '.$e->getMessage());

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

        $trend = 'Stable';
        if ($latest < $oldest - 1) {
            $trend = 'Declining';
        } elseif ($latest > $oldest + 1) {
            $trend = 'Improving';
        }

        return $trend;
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

    protected function buildAnalysisPrompt($person, $enps, $stress, $trend, $stagnation, $overqual, $growth, $deptAvgPulse, $managerHealth): string
    {
        return "Actúa como el 'Stratos Retention Cortex'. Analiza el riesgo de fuga para {$person->first_name} ({$person->role->name}).\n\n".
               "DATOS INDIVIDUALES:\n".
               "- Engagement (eNPS): {$enps}/10 (Tendencia: {$trend})\n".
               "- Nivel de Estrés: {$stress}/5\n".
               "- Score de Estancamiento (Tenure): {$stagnation}/100\n".
               "- Riesgo de Sobrecalificación: {$overqual}\n".
               "- Actividad de Aprendizaje: {$growth}\n\n".
               "DATOS COLECTIVOS & LIDERAZGO:\n".
               "- eNPS Promedio del Equipo: {$deptAvgPulse}/10\n".
               "- Salud de Liderazgo (Manager Score): {$managerHealth}/100\n\n".
               "REGLAS DE ANÁLISIS AVANZADO:\n".
               "1. Si Risk Score > 70, clasificar como 'High'.\n".
               "2. Si el Manager Score < 60, priorizar 'Fricción de Liderazgo' como driver.\n".
               "3. Evaluar el impacto de las acciones sugeridas en el score futuro.\n\n".
               "Devuelve un JSON estrictamente con:\n".
               "- risk_score (0-100)\n".
               "- risk_level (low/medium/high)\n".
               "- primary_driver (causa principal)\n".
               "- individual_actions (array de 3 acciones)\n".
               "- team_interventions (array de 2 acciones de clima/liderazgo)\n".
               "- organizational_policies (array de 2 cambios estructurales)\n".
               "- predicted_impact (String corto: qué % bajará el riesgo si se aplica el plan)\n";
    }

    protected function cleanJson($string): string
    {
        return trim(str_replace(['```json', '```'], '', $string));
    }
}
