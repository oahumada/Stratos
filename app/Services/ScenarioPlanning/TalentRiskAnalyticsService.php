<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Organizations;
use App\Models\People;
use App\Models\Scenario;
use App\Models\TalentRiskIndicator;

class TalentRiskAnalyticsService
{
    /**
     * Analyze volatility risk for a person in organization context
     * Returns: 0-100 risk score
     */
    public function analyzeVolatilityRisk(People $person, Organizations $organization): float
    {
        // Factors:
        $deptTurnoverRate = $this->getDepartmentTurnoverRate($person->department_id ?? null); // %
        $personTenure = $this->getPersonTenureYears($person); // years
        $roleCriticality = $person->currentRole?->criticality ?? 5; // 1-10 scale
        $salaryVsMarket = $this->getSalaryCompetitiveness($person, $organization); // %

        $volatilityScore =
            ($deptTurnoverRate * 2) +           // Higher dept turnover = more risk (x2)
            (max(0, 10 - $personTenure) * 3) +  // Newer employees = more risk
            ((10 - $roleCriticality) * 5) +     // Less critical roles = higher flight risk
            (max(0, 100 - $salaryVsMarket) * 0.5); // Underpaid = risk

        return min(100, $volatilityScore);
    }

    /**
     * Predict retention probability for person in scenario context
     * Returns: 0-100 probability of staying
     */
    public function predictRetentionRisk(People $person, Scenario $scenario): float
    {
        $careerOpportunitiesInScenario = $this->assessCareerGrowth($person, $scenario); // 0-100
        $skillAlignmentWithFuture = $this->calculateFutureSkillFit($person, $scenario); // 0-100
        $teamDynamics = $this->assessTeamEngagement($person); // 0-100
        $compCompetitiveness = $this->getSalaryCompetitiveness($person, $scenario->organization); // 0-100

        $retentionScore =
            ($careerOpportunitiesInScenario * 0.30) +
            ($skillAlignmentWithFuture * 0.25) +
            ($teamDynamics * 0.20) +
            ($compCompetitiveness * 0.15) +
            (rand(0, 100) * 0.10); // Unknown factors buffer

        return min(100, $retentionScore);
    }

    /**
     * Identify skill gaps across workforce for scenario
     * Returns: [{skill_id, skill_name, gap_count, priority, training_recommendations}]
     */
    public function calculateSkillGaps(Scenario $scenario): array
    {
        $gaps = [];

        // For now, simplified implementation
        // In production, would integrate with actual skill demand planning

        return collect($gaps)
            ->sortByDesc('gap_count')
            ->values()
            ->toArray();
    }

    /**
     * Identify high-risk talent (multiple risk factors)
     */
    public function identifyHighRiskTalent(Scenario $scenario): array
    {
        $highRisk = TalentRiskIndicator::where('scenario_id', $scenario->id)
            ->where('risk_score', '>=', 70)
            ->with(['person', 'mitigations'])
            ->get()
            ->groupBy('person_id')
            ->map(fn ($indicators) => [
                'person_id' => $indicators->first()->person_id,
                'person_name' => $indicators->first()->person->name,
                'risk_types' => $indicators->pluck('risk_type')->toArray(),
                'average_risk_score' => round($indicators->avg('risk_score'), 2),
                'total_indicators' => $indicators->count(),
                'active_mitigations' => $indicators->sum(fn ($i) => $i->mitigations()->active()->count()),
            ])
            ->values()
            ->toArray();

        return $highRisk;
    }

    // ── Private Helper Methods ──────────────────────────────────────────────

    /**
     * Get department turnover rate (0-100 %)
     */
    private function getDepartmentTurnoverRate(?int $deptId): float
    {
        if (! $deptId) {
            return 15; // Default average
        }

        // In production, calculate from historical data
        // For now, return sample value
        return rand(5, 30);
    }

    /**
     * Get person's tenure in years
     */
    private function getPersonTenureYears(People $person): int
    {
        $hireDate = $person->hire_date ?? $person->created_at;
        if (! $hireDate) {
            return 0;
        }

        return (int) ceil($hireDate->diffInYears(now()));
    }

    /**
     * Calculate salary competitiveness (0-100, where 100 = market rate)
     */
    private function getSalaryCompetitiveness(People $person, Organizations $organization): float
    {
        // Sample: 75% of market rate = 75 score
        // In production, use actual market data APIs
        $salary = $person->current_salary ?? 0;
        $marketAverage = 100000; // Placeholder

        if ($marketAverage === 0) {
            return 50;
        }

        return min(100, ($salary / $marketAverage) * 100);
    }

    /**
     * Assess career growth opportunities in scenario (0-100)
     */
    private function assessCareerGrowth(People $person, Scenario $scenario): float
    {
        // Count promotion/role change opportunities in scenario
        // For now, return default
        return 50;
    }

    /**
     * Calculate future skill fit for scenario (0-100)
     */
    private function calculateFutureSkillFit(People $person, Scenario $scenario): float
    {
        // Assess if person's skills align with future scenario roles
        // For now, return default
        return 50;
    }

    /**
     * Assess team engagement/satisfaction (0-100)
     */
    private function assessTeamEngagement(People $person): float
    {
        // In production, integrate with engagement survey data
        // For now, return default
        return 60;
    }
}
