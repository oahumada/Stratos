<?php

namespace App\Services\ScenarioPlanning;

use App\Models\EmployeePulse;
use App\Models\People;
use App\Models\Scenario;

class PeopleExperienceIntegrationService
{
    /**
     * @return array{
     *     people_experience: array<string, float|int|null>,
     *     headcount: array{current:int,projected:int,change:int}
     * }
     */
    public function summarizeForScenario(Scenario $scenario): array
    {
        $organizationId = (int) $scenario->organization_id;
        $peopleCount = People::query()
            ->where('organization_id', $organizationId)
            ->count();

        $pulseBaseQuery = EmployeePulse::query()
            ->whereHas('person', function ($query) use ($organizationId): void {
                $query->where('organization_id', $organizationId);
            });

        $recentPulseQuery = (clone $pulseBaseQuery)->where('created_at', '>=', now()->subDays(30));

        $avgEnps = (clone $recentPulseQuery)->avg('e_nps');
        $avgEngagement = (clone $recentPulseQuery)->avg('engagement_level');
        $avgStress = (clone $recentPulseQuery)->avg('stress_level');
        $highRiskPeople = (clone $recentPulseQuery)
            ->whereNotNull('ai_turnover_risk')
            ->where('ai_turnover_risk', '>=', 70)
            ->count();

        $projectedHeadcount = (int) round((float) $scenario->roles()->sum('fte'));
        if ($projectedHeadcount <= 0) {
            $projectedHeadcount = $peopleCount;
        }

        return [
            'people_experience' => [
                'active_people' => $peopleCount,
                'pulses_last_30d' => (clone $recentPulseQuery)->count(),
                'avg_enps' => $avgEnps !== null ? round((float) $avgEnps, 2) : null,
                'avg_engagement_level' => $avgEngagement !== null ? round((float) $avgEngagement, 2) : null,
                'avg_stress_level' => $avgStress !== null ? round((float) $avgStress, 2) : null,
                'high_turnover_risk_people' => $highRiskPeople,
            ],
            'headcount' => [
                'current' => $peopleCount,
                'projected' => $projectedHeadcount,
                'change' => $projectedHeadcount - $peopleCount,
            ],
        ];
    }
}
