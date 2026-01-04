<?php

namespace App\Repositories;

use App\Models\WorkforcePlanningScenario;
use App\Models\WorkforcePlanningRoleForecast;
use App\Models\WorkforcePlanningMatch;
use App\Models\WorkforcePlanningSkillGap;
use App\Models\WorkforcePlanningSuccessionPlan;
use App\Models\WorkforcePlanningAnalytic;
use Illuminate\Support\Collection;

class WorkforcePlanningRepository
{
    // Scenarios
    public function getScenarioById($id)
    {
        return WorkforcePlanningScenario::with([
            'roleForecasts',
            'matches',
            'skillGaps',
            'successionPlans',
            'analytics'
        ])->find($id);
    }

    public function getScenariosByOrganization($organizationId, $filters = [])
    {
        $query = WorkforcePlanningScenario::where('organization_id', $organizationId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['fiscal_year'])) {
            $query->where('fiscal_year', $filters['fiscal_year']);
        }

        return $query->orderByDesc('created_at')->paginate(15);
    }

    public function createScenario(array $data)
    {
        return WorkforcePlanningScenario::create($data);
    }

    public function updateScenario($id, array $data)
    {
        $scenario = WorkforcePlanningScenario::find($id);
        $scenario->update($data);
        return $scenario;
    }

    public function deleteScenario($id)
    {
        return WorkforcePlanningScenario::destroy($id);
    }

    // Role Forecasts
    public function getForecastsByScenario($scenarioId)
    {
        return WorkforcePlanningRoleForecast::where('scenario_id', $scenarioId)
            ->with(['role', 'department'])
            ->get();
    }

    public function createForecast(array $data)
    {
        return WorkforcePlanningRoleForecast::create($data);
    }

    public function updateForecast($id, array $data)
    {
        $forecast = WorkforcePlanningRoleForecast::find($id);
        $forecast->update($data);
        return $forecast;
    }

    // Matches
    public function getMatchesByScenario($scenarioId, $filters = [])
    {
        $query = WorkforcePlanningMatch::where('scenario_id', $scenarioId)
            ->with(['person', 'forecast.role', 'developmentPath']);

        if (!empty($filters['readiness_level'])) {
            $query->where('readiness_level', $filters['readiness_level']);
        }

        if (!empty($filters['min_score'])) {
            $query->where('match_score', '>=', $filters['min_score']);
        }

        if (!empty($filters['max_risk'])) {
            $query->where('risk_score', '<=', $filters['max_risk']);
        }

        return $query->orderByDesc('match_score')->paginate(20);
    }

    public function getMatchesByForecast($forecastId)
    {
        return WorkforcePlanningMatch::where('forecast_id', $forecastId)
            ->with(['person', 'developmentPath'])
            ->orderByDesc('match_score')
            ->get();
    }

    public function createMatch(array $data)
    {
        return WorkforcePlanningMatch::create($data);
    }

    public function updateMatch($id, array $data)
    {
        $match = WorkforcePlanningMatch::find($id);
        $match->update($data);
        return $match;
    }

    // Skill Gaps
    public function getSkillGapsByScenario($scenarioId, $filters = [])
    {
        $query = WorkforcePlanningSkillGap::where('scenario_id', $scenarioId)
            ->with(['skill', 'role', 'department']);

        if (!empty($filters['priority'])) {
            if (is_array($filters['priority'])) {
                $query->whereIn('priority', $filters['priority']);
            } else {
                $query->where('priority', $filters['priority']);
            }
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->orderByDesc('gap')->paginate(20);
    }

    public function createSkillGap(array $data)
    {
        return WorkforcePlanningSkillGap::create($data);
    }

    public function updateSkillGap($id, array $data)
    {
        $gap = WorkforcePlanningSkillGap::find($id);
        $gap->update($data);
        return $gap;
    }

    // Succession Plans
    public function getSuccessionPlansByScenario($scenarioId)
    {
        return WorkforcePlanningSuccessionPlan::where('scenario_id', $scenarioId)
            ->with(['role', 'department', 'primarySuccessor', 'secondarySuccessor', 'tertiarySuccessor'])
            ->orderBy('criticality_level')
            ->get();
    }

    public function getSuccessionPlansByCriticality($scenarioId, $criticalityLevel)
    {
        return WorkforcePlanningSuccessionPlan::where('scenario_id', $scenarioId)
            ->where('criticality_level', $criticalityLevel)
            ->with(['role', 'primarySuccessor'])
            ->get();
    }

    public function getAtRiskSuccessionPlans($scenarioId)
    {
        return WorkforcePlanningSuccessionPlan::where('scenario_id', $scenarioId)
            ->where('primary_readiness_percentage', '<', 50)
            ->with(['role', 'primarySuccessor'])
            ->get();
    }

    public function createSuccessionPlan(array $data)
    {
        return WorkforcePlanningSuccessionPlan::create($data);
    }

    public function updateSuccessionPlan($id, array $data)
    {
        $plan = WorkforcePlanningSuccessionPlan::find($id);
        $plan->update($data);
        return $plan;
    }

    // Analytics
    public function getAnalyticsByScenario($scenarioId)
    {
        return WorkforcePlanningAnalytic::where('scenario_id', $scenarioId)
            ->firstOrFail();
    }

    public function createAnalytic(array $data)
    {
        return WorkforcePlanningAnalytic::create($data);
    }

    public function updateAnalytic($scenarioId, array $data)
    {
        return WorkforcePlanningAnalytic::where('scenario_id', $scenarioId)
            ->update($data);
    }
}
