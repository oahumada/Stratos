<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkforcePlanningScenario;
use App\Models\WorkforcePlanningAnalytic;
use App\Models\WorkforcePlanningRoleForecast;
use App\Models\WorkforcePlanningMatch;
use App\Models\WorkforcePlanningSkillGap;
use App\Models\WorkforcePlanningSuccessionPlan;

class WorkforcePlanningSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un scenario de ejemplo (o usar uno existente)
        $scenario = WorkforcePlanningScenario::firstOrCreate(
            ['id' => 1],
            [
                'organization_id' => 1,
                'name' => 'Q1 2026 Planning',
                'description' => 'Workforce planning for first quarter 2026',
                'fiscal_year' => 2026,
                'horizon_months' => 12,
                'status' => 'draft',
                'created_by' => 1,
            ]
        );

        // Crear analytics para el scenario
        WorkforcePlanningAnalytic::updateOrCreate(
            ['scenario_id' => $scenario->id],
            [
                'total_headcount_current' => 450,
                'total_headcount_projected' => 520,
                'net_growth' => 70,
                'internal_coverage_percentage' => 68,
                'external_gap_percentage' => 32,
                'succession_risk_percentage' => 15,
                'high_risk_positions' => 8,
                'medium_risk_positions' => 12,
                'critical_skills_at_risk' => 5,
                'estimated_recruitment_cost' => 1500000,
                'estimated_training_cost' => 450000,
                'estimated_external_hiring_months' => 4,
            ]
        );

        $this->command->info('✅ Workforce Planning analytics seeded successfully');
    }
}
