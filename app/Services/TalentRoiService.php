<?php

namespace App\Services;

use App\Models\People;
use App\Models\Scenario;
use App\Models\PeopleRoleSkills;
use Illuminate\Support\Facades\DB;

class TalentRoiService
{
    /**
     * ROI Estimations (Industry Averages - can be customized per org)
     */
    const AVG_HIRING_COST = 4500; // USD per hire
    const AVG_REPLACEMENT_COST_MULTIPLIER = 0.5; // 50% of annual salary
    const AVG_ANNUAL_SALARY = 45000; // USD

    protected ScenarioAnalyticsService $scenarioAnalytics;

    public function __construct(ScenarioAnalyticsService $scenarioAnalytics)
    {
        $this->scenarioAnalytics = $scenarioAnalytics;
    }

    /**
     * Get consolidated high-level metrics for investors.
     */
    public function getExecutiveSummary(): array
    {
        $totalHeadcount = People::count();
        $totalScenarios = Scenario::count();
        
        $avgReadiness = $this->calculateGlobalReadiness();
        $talentRoi = $this->calculateTotalTalentRoi();
        
        // High-level risks
        $criticalGapRate = $this->calculateCriticalGapRate();

        return [
            'headcount' => $totalHeadcount,
            'active_scenarios' => $totalScenarios,
            'org_readiness' => round($avgReadiness * 100, 1),
            'talent_roi_usd' => $talentRoi,
            'critical_gap_rate' => round($criticalGapRate * 100, 1),
            'ai_augmentation_index' => $this->calculateAiAugmentationIndex(),
        ];
    }

    /**
     * Calculate global Readiness across all people/roles.
     */
    private function calculateGlobalReadiness(): float
    {
        $total = PeopleRoleSkills::count();
        if ($total === 0) {
            return 0;
        }

        $avgReadiness = PeopleRoleSkills::selectRaw('AVG(LEAST(1.0, current_level / NULLIF(required_level, 0))) as avg')
            ->value('avg') ?: 0;

        return (float) $avgReadiness;
    }

    /**
     * Estimated ROI = (Internal Growth Savings) + (Attrition Prevention Value)
     */
    private function calculateTotalTalentRoi(): float
    {
        // 1. Savings from internal upskilling (people who reached required level)
        $upskilledCount = PeopleRoleSkills::whereColumn('current_level', '>=', 'required_level')
            ->where('required_level', '>', 0)
            ->distinct('people_id')
            ->count();
            
        $upskillingSavings = $upskilledCount * self::AVG_HIRING_COST;

        // 2. Value from AI-Assisted productivity (mock/aggregated from bot strategies)
        $botStrategies = DB::table('scenario_closure_strategies')
            ->where('strategy', 'bot')
            ->count();
        $aiProductivityValue = $botStrategies * (self::AVG_ANNUAL_SALARY * 0.2); // Assuming 20% efficiency boost

        return round($upskillingSavings + $aiProductivityValue, 2);
    }

    private function calculateCriticalGapRate(): float
    {
        $totalPivotRows = PeopleRoleSkills::count();
        if ($totalPivotRows === 0) {
            return 0;
        }

        $criticalGaps = PeopleRoleSkills::whereRaw('current_level < (required_level * 0.5)')
            ->where('required_level', '>', 0)
            ->count();

        return $criticalGaps / $totalPivotRows;
    }

    private function calculateAiAugmentationIndex(): float
    {
        // Percentage of roles that have at least one 'bot' strategy mapped
        $totalRoles = DB::table('roles')->count();
        if ($totalRoles === 0) {
            return 0;
        }

        $augmentedRoles = DB::table('scenario_closure_strategies')
            ->where('strategy', 'bot')
            ->distinct('role_id')
            ->count('role_id');

        return round(($augmentedRoles / $totalRoles) * 100, 1);
    }

    /**
     * Get data for distribution charts.
     */
    public function getDistributionData(): array
    {
        return [
            'skill_levels' => DB::table('people_role_skills')
                ->select('current_level', DB::raw('count(*) as count'))
                ->groupBy('current_level')
                ->orderBy('current_level')
                ->get(),
            'department_readiness' => PeopleRoleSkills::join('people', 'people_role_skills.people_id', '=', 'people.id')
                ->join('departments', 'people.department_id', '=', 'departments.id')
                ->select('departments.name', DB::raw('AVG(LEAST(1.0, current_level / NULLIF(required_level, 0))) * 100 as readiness'))
                ->groupBy('departments.name')
                ->get(),
        ];
    }
}
