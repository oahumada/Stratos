<?php

namespace App\Services\Intelligence;

use App\Models\People;
use App\Models\Scenario;
use Illuminate\Support\Facades\Log;

class RoiCalculatorService
{
    public function __construct(
        protected RetentionDeepPredictorService $retentionPredictor
    ) {}

    /**
     * Calculates the estimated ROI for a given workforce scenario.
     */
    public function calculateScenarioRoi(int $scenarioId): array
    {
        $scenario = Scenario::with(['roles', 'closureStrategies'])->findOrFail($scenarioId);
        $organizationId = $scenario->organization_id;

        // 1. Calculate Potential Savings from Attrition Reduction
        $atRiskPeople = People::where('organization_id', $organizationId)
            ->where('is_high_potential', true)
            ->get();

        $totalReplacementCost = 0;
        $totalRiskScore = 0;

        foreach ($atRiskPeople as $person) {
            $prediction = $this->retentionPredictor->predict($person->id);
            $totalReplacementCost += $prediction['financial_impact']['replacement_cost_usd'] ?? 45000;
            $totalRiskScore += $prediction['flight_risk_score'] ?? 50;
        }

        $avgRisk = $atRiskPeople->count() > 0 ? ($totalRiskScore / $atRiskPeople->count()) : 0;

        // Assumption: Implementing strategies reduces risk by 40%
        $efficiencyGain = 0.40;
        $potentialSavings = $totalReplacementCost * ($avgRisk / 100) * $efficiencyGain;

        // 2. Calculate Implementation Costs (Training/Deployment)
        $estimatedCosts = $scenario->estimated_budget ?? 0;
        if ($estimatedCosts == 0) {
            // Mock: calculate based on strategies
            $estimatedCosts = $scenario->closureStrategies->count() * 2500; // $2.5k per strategy
        }

        // 3. Final ROI Calculation
        $netBenefit = $potentialSavings - $estimatedCosts;
        $roiPercentage = $estimatedCosts > 0 ? ($netBenefit / $estimatedCosts) * 100 : 0;

        $results = [
            'total_potential_savings' => round($potentialSavings, 2),
            'estimated_implementation_cost' => round($estimatedCosts, 2),
            'net_benefit' => round($netBenefit, 2),
            'roi_percentage' => round($roiPercentage, 2),
            'break_even_months' => $netBenefit > 0 ? round(($estimatedCosts / ($potentialSavings / 12)), 1) : null,
            'confidence_index' => 0.85, // AI confidence
            'primary_savings_source' => 'Attrition Mitigation & Upskilling Efficiency',
        ];

        Log::info("ROI Calculated for Scenario {$scenarioId}: {$roiPercentage}%");

        return $results;
    }

    /**
     * Calculates the estimated ROI of developing/retaining an individual.
     */
    public function calculateIndividualRoi(int $peopleId): array
    {
        $person = People::findOrFail($peopleId);
        $prediction = $this->retentionPredictor->predict($person->id);

        $replacementCost = $prediction['financial_impact']['replacement_cost_usd'] ?? 45000;
        $riskScore = $prediction['flight_risk_score'] ?? 50;

        // Potential savings if we mitigate risk
        $potentialSavings = $replacementCost * ($riskScore / 100) * 0.60; // 60% mitigation target

        return [
            'replacement_cost' => $replacementCost,
            'flight_risk' => $riskScore,
            'potential_annual_savings' => round($potentialSavings, 2),
            'risk_driver' => $prediction['primary_driver'] ?? 'Desconocido',
        ];
    }
}
