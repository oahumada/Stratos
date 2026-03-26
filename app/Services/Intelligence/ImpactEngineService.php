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
    /**
     * Per-request memoization cache for expensive metrics + benchmarks queries.
     * Keyed by organizationId to batch business_metrics + financial_indicators.
     * @var array<int, array>
     */
    protected array $metricsAndBenchmarksCache = [];

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
     * Batch load business metrics + financial indicators in single pass.
     * Eliminates 2 queries by reading both in one organizational context fetch.
     * Cached per request.
     */
    protected function fetchMetricsAndBenchmarks(int $organizationId): array
    {
        // Return cached if already fetched this request
        if (isset($this->metricsAndBenchmarksCache[$organizationId])) {
            return $this->metricsAndBenchmarksCache[$organizationId];
        }

        // Batch Query 1: All business metrics in one call
        $metrics = BusinessMetric::where('organization_id', $organizationId)
            ->whereIn('metric_name', ['revenue', 'opex', 'payroll_cost', 'headcount', 'turnover_rate'])
            ->orderBy('period_date', 'desc')
            ->get()
            ->groupBy('metric_name');

        // Batch Query 2: All financial indicators in one call
        $indicators = FinancialIndicator::where('organization_id', $organizationId)
            ->whereIn('indicator_type', ['avg_annual_salary', 'avg_recruitment_cost'])
            ->get()
            ->keyBy('indicator_type');

        // Cache and return as single result set
        $result = [
            'metrics' => $metrics,
            'indicators' => $indicators,
            'reporting_period' => $metrics->flatMap(fn($items) => $items)->max('period_date') ?? null,
        ];

        $this->metricsAndBenchmarksCache[$organizationId] = $result;
        return $result;
    }

    /**
     * Provee benchmarks financieros para el costeo de escenarios (Vanguard).
     * Intenta usar datos históricos reales, de lo contrario usa promedios de industria.
     */
    public function getFinancialBenchmarks(int $organizationId): array
    {
        // Use batched fetch instead of separate query
        $data = $this->fetchMetricsAndBenchmarks($organizationId);
        $rows = $data['indicators'];

        $avgSalary = $rows->get('avg_annual_salary')?->value ?? 45000.00;
        $recruitmentCost = $rows->get('avg_recruitment_cost')?->value ?? 5000.00;

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
        // Use batched fetch (metrics + benchmarks in single pass)
        $data = $this->fetchMetricsAndBenchmarks($organizationId);
        $metrics = $data['metrics'];

        $revenue = $metrics->get('revenue')?->first()?->metric_value ?? 0;
        $opex = $metrics->get('opex')?->first()?->metric_value ?? 0;
        $payroll = $metrics->get('payroll_cost')?->first()?->metric_value ?? 0;
        $headcount = $metrics->get('headcount')?->first()?->metric_value ?? 1; // Evitar división por cero
        $turnover = $metrics->get('turnover_rate')?->first()?->metric_value ?? 15;

        // 2. Aplicar fórmula HCVA: [Revenue - (Total Expenses - Payroll)] / Full-Time Equivalents
        $nonPayrollExpenses = $opex - $payroll;
        $hcva = ($revenue - $nonPayrollExpenses) / ($headcount ?: 1);

        $benchmarks = $this->getFinancialBenchmarks($organizationId); // Now uses cached data
        $replacementRisk = ($turnover / 100) * $headcount * $benchmarks['avg_recruitment_cost'];

        return [
            'hcva_average' => round($hcva, 2),
            'total_replacement_risk_usd' => round($replacementRisk, 2),
            'training_roi_index' => 1.45,
            'headcount_fte' => $headcount,
            'reporting_period' => $data['reporting_period'],
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
