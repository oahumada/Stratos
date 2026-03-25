<?php

namespace App\Services\Intelligence;

use App\Models\BusinessMetric;
use App\Models\FinancialIndicator;
use App\Models\ImpactAnalysis;
use App\Models\Organizations;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class ImpactEngineService
{
    public function __construct(
        protected AiOrchestratorService $orchestrator
    ) {}

    /**
     * Genera un análisis LAMP correlacionando datos de talento con métricas de negocio.
     */
    public function generateLampAnalysis(int $organizationId, string $targetEngine = 'cerbero'): array
    {
        $organization = Organizations::findOrFail($organizationId);

        // 1. Gather Measures (Métricas de Negocio)
        $metrics = BusinessMetric::where('organization_id', $organizationId)
            ->latest()
            ->limit(10)
            ->get();

        // 2. Orquestar Analytics & Logic con IA
        $prompt = $this->buildLampPrompt($organization, $metrics, $targetEngine);

        try {
            $response = $this->orchestrator->agentThink('Stratos Impact Cortex', $prompt);
            $analysisData = json_decode($this->cleanJson($response['response']), true);

            // 3. Persist Analysis (Measures + Logic + Analytics)
            $analysis = ImpactAnalysis::create([
                'organization_id' => $organizationId,
                'type' => 'lamp_correlation',
                'target_engine' => $targetEngine,
                'correlations' => $analysisData['correlations'] ?? [],
                'logic_narrative' => $analysisData['logic_narrative'] ?? '',
                'insight_summary' => $analysisData['insight_summary'] ?? 'Análisis generado',
                'recommendations' => $analysisData['recommendations'] ?? [],
            ]);

            // 4. Seal with SSS (Process/Governance)
            $analysis->seal();

            return [
                'status' => 'success',
                'analysis_id' => $analysis->id,
                'summary' => $analysis->insight_summary,
                'correlations' => $analysis->correlations,
            ];

        } catch (\Exception $e) {
            Log::error('ImpactEngine Error: '.$e->getMessage());

            return ['status' => 'error', 'message' => 'Failed to generate impact analysis'];
        }
    }

    /**
     * Provee benchmarks financieros para el costeo de escenarios (Vanguard).
     * Intenta usar datos históricos reales, de lo contrario usa promedios de industria.
     */
    public function getFinancialBenchmarks(int $organizationId): array
    {
        // En una implementación completa, esto consultaría la tabla financial_indicators
        // o agregaría datos de business_metrics (Payroll total / headcount).

        $avgSalary = FinancialIndicator::where('organization_id', $organizationId)
            ->where('indicator_type', 'avg_annual_salary')
            ->value('value') ?? 45000.00;

        $recruitmentCost = FinancialIndicator::where('organization_id', $organizationId)
            ->where('indicator_type', 'avg_recruitment_cost')
            ->value('value') ?? 5000.00;

        return [
            'avg_annual_salary' => (float) $avgSalary,
            'avg_monthly_salary' => round($avgSalary / 12, 2),
            'avg_recruitment_cost' => (float) $recruitmentCost,
            'avg_severance_multiplier' => 3.0, // meses de sueldo promedio por despido
        ];
    }

    /**
     * Calcula indicadores financieros clave (HCVA, ROI).
     */
    public function calculateFinancialKPIs(int $organizationId): array
    {
        // 1. Obtener las métricas más recientes
        $metrics = BusinessMetric::where('organization_id', $organizationId)
            ->whereIn('metric_name', ['revenue', 'opex', 'payroll_cost', 'headcount'])
            ->orderBy('period_date', 'desc')
            ->get()
            ->groupBy('metric_name');

        $revenue = $metrics->get('revenue')?->first()?->metric_value ?? 0;
        $opex = $metrics->get('opex')?->first()?->metric_value ?? 0;
        $payroll = $metrics->get('payroll_cost')?->first()?->metric_value ?? 0;
        $headcount = $metrics->get('headcount')?->first()?->metric_value ?? 1; // Evitar división por cero

        // 2. Aplicar fórmula HCVA: [Revenue - (Total Expenses - Payroll)] / Full-Time Equivalents
        // NOTA: En este contexto OPEX suele incluir todo menos intereses/impuestos,
        // pero la fórmula clásica de HCVA busca aislar el valor que el humano agrega después de gastos operativos no-humanos.
        $nonPayrollExpenses = $opex - $payroll;
        $hcva = ($revenue - $nonPayrollExpenses) / $headcount;

        // 3. Calcular Riesgo de Reemplazo (Replacement Risk)
        // Estimado: turnover_rate * headcount * avg_recruitment_cost
        $turnover = BusinessMetric::where('organization_id', $organizationId)
            ->where('metric_name', 'turnover_rate')
            ->latest()
            ->value('metric_value') ?? 15; // 15% default

        $benchmarks = $this->getFinancialBenchmarks($organizationId);
        $replacementRisk = ($turnover / 100) * $headcount * $benchmarks['avg_recruitment_cost'];

        return [
            'hcva_average' => round($hcva, 2),
            'total_replacement_risk_usd' => round($replacementRisk, 2),
            'training_roi_index' => 1.45, // Valor estático a refinar con datos de Navigator
            'headcount_fte' => $headcount,
            'reporting_period' => BusinessMetric::where('organization_id', $organizationId)->max('period_date'),
        ];
    }

    protected function buildLampPrompt($organization, $metrics, $engine): string
    {
        $metricsString = $metrics->map(fn ($m) => "- {$m->metric_name}: {$m->metric_value} ({$m->unit})")->implode("\n");

        return "Actúa como el 'Stratos Impact Cortex' especializado en el Framework LAMP (Logic, Analytics, Measures, Process).\n\n".
               "CONTEXTO TRABAJADO:\n".
               "Motor de Talento a Correlacionar: {$engine}\n".
               "Organización: {$organization->name}\n\n".
               "ÚLTIMAS MÉTRICAS DE NEGOCIO (Measures):\n".
               "{$metricsString}\n\n".
               "TU MISIÓN:\n".
               "1. LOGIC: Crea una narrativa que conecte el desempeño en {$engine} con los resultados de negocio arriba listados.\n".
               "2. ANALYTICS: Identifica posibles correlaciones estadísticas (r-square proyectado).\n".
               "3. INSIGHT: Resume el hallazgo más crítico.\n".
               "4. RECOMMENDATIONS: Define 3 acciones estratégicas (Process).\n\n".
               'Devuelve un JSON con: logic_narrative, correlations (array de objetos {metric, r_square, p_value, summary}), insight_summary, recommendations.';
    }

    protected function cleanJson($string): string
    {
        return trim(str_replace(['```json', '```'], '', $string));
    }
}
