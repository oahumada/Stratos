<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScenarioRoiController extends Controller
{
    /**
     * Calcula el ROI estratégico comparando diferentes modos de adquisición de talento.
     * POST /api/strategic-planning/roi-calculator/calculate
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scenario_id' => 'required|integer',
            'talent_nodes_needed' => 'required|integer|min:1',
            'acquisition_cost_per_node' => 'required|numeric|min:0',
            'reskilling_cost_per_node' => 'required|numeric|min:0',
            'reskilling_duration_months' => 'required|integer|min:1',
            'internal_talent_pipeline' => 'required|integer|min:0',
            'external_market_salary' => 'required|numeric|min:0',
            'internal_maintenance_salary' => 'required|numeric|min:0',
        ]);

        // Estrategia BUILD (Upskilling/Reskilling interno)
        $build = $this->calculateBuild($validated);
        // Estrategia BUY (Reclutamiento externo)
        $buy = $this->calculateBuy($validated);
        // Estrategia BORROW (Consultoría/Freelance)
        $borrow = $this->calculateBorrow($validated);
        // Estrategia BOT (Automatización/IA)
        $bot = $this->calculateBot($validated);

        // Algoritmo de recomendación estratégica
        $bestStrategy = $this->determineBestStrategy($build, $buy, $borrow, $bot);

        return response()->json([
            'success' => true,
            'data' => [
                'roi_comparison' => [
                    'build' => $build,
                    'buy' => $buy,
                    'borrow' => $borrow,
                    'bot' => $bot,
                ],
                'recommendation' => $bestStrategy,
            ],
        ]);
    }

    private function calculateBuild($params): array
    {
        $baseCost = $params['reskilling_cost_per_node'] * $params['internal_talent_pipeline'];
        $mentorshipOverhead = $baseCost * 0.10;
        $totalCost = $baseCost + $mentorshipOverhead;

        $benefit = ($params['external_market_salary'] - $params['internal_maintenance_salary']) * $params['talent_nodes_needed'];

        return [
            'total_cost' => $totalCost,
            'time_to_delivery_months' => $params['reskilling_duration_months'],
            'risk_level' => 'MEDIUM (Retention concern)',
            'success_probability' => 0.8,
            'roi_index' => round((($benefit - $totalCost) / max($totalCost, 1)) * 100),
            'strategic_value' => 'HIGH (Cultural consistency + Talent growth)',
        ];
    }

    private function calculateBuy($params): array
    {
        $recruitmentFees = $params['acquisition_cost_per_node'] * $params['talent_nodes_needed'];
        $onboardingCosts = 5000 * $params['talent_nodes_needed'];
        $totalCost = $recruitmentFees + $onboardingCosts;

        return [
            'total_cost' => $totalCost,
            'time_to_delivery_months' => 4, // Promedio mercado experto
            'risk_level' => 'LOW (Fast expertise)',
            'success_probability' => 0.9,
            'roi_index' => round((($params['external_market_salary'] * 0.2) / max($totalCost, 1)) * 100), // ROI basado en valor inmediato
            'strategic_value' => 'MEDIUM (Faster time-to-market)',
        ];
    }

    private function calculateBorrow($params): array
    {
        $monthlyFreelanceRate = 8000;
        $totalCost = $monthlyFreelanceRate * $params['talent_nodes_needed'] * 6; // Proyecto 6 meses

        return [
            'total_cost' => $totalCost,
            'time_to_delivery_months' => 1,
            'risk_level' => 'HIGH (Knowledge leakage)',
            'success_probability' => 0.7,
            'roi_index' => -15, // Usualmente más costoso por hora
            'strategic_value' => 'LOW (Tactical / Gap filler only)',
        ];
    }

    private function calculateBot($params): array
    {
        $aiInitialInvestment = 50000;
        $maintenanceMonthly = 2000;
        $totalCost = $aiInitialInvestment + ($maintenanceMonthly * 12);

        return [
            'total_cost' => $totalCost,
            'time_to_delivery_months' => 8,
            'risk_level' => 'MEDIUM (Technical debt / Integration)',
            'success_probability' => 0.6,
            'roi_index' => 120, // Altísimo escalabilidad
            'strategic_value' => 'VERY HIGH (Future scalability + Innovation)',
        ];
    }

    private function determineBestStrategy($build, $buy, $borrow, $bot): array
    {
        if ($bot['roi_index'] > 100 && $bot['success_probability'] > 0.5) {
            return [
                'strategy' => 'BOT (Hybrid Automation)',
                'reasoning' => 'Long-term scalability and high ROI justify the integration risk.',
            ];
        }

        if ($build['roi_index'] > $buy['roi_index']) {
            return [
                'strategy' => 'BUILD (Internal Transformation)',
                'reasoning' => 'Leveraging internal talent reduces salary gap costs and strengthens culture.',
            ];
        }

        return [
            'strategy' => 'BUY (Expert Acquisition)',
            'reasoning' => 'Immediate expertise is required to meet scenario deadlines.',
        ];
    }

    public function listCalculations(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}
