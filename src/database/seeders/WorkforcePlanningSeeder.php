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
                'total_headcount_current' => 85,
                'total_headcount_projected' => 105,
                'net_growth' => 20,
                'internal_coverage_percentage' => 72,
                'external_gap_percentage' => 28,
                'succession_risk_percentage' => 18,
                'high_risk_positions' => 3,
                'medium_risk_positions' => 6,
                'critical_skills_at_risk' => 4,
                'estimated_recruitment_cost' => 280000,
                'estimated_training_cost' => 120000,
                'estimated_external_hiring_months' => 5,
            ]
        );

        // Crear role forecasts realistas
        $forecastsData = [
            [
                'role_id' => 1, // Backend Developer
                'department_id' => 1, // Engineering
                'headcount_current' => 12,
                'headcount_projected' => 18,
                'growth_rate' => 50.0,
                'critical_skills' => [1, 3, 5], // PHP, Python, Node.js
                'emerging_skills' => [12, 15], // Go, Rust
                'status' => 'approved',
                'variance_reason' => 'Expansion of API services',
            ],
            [
                'role_id' => 2, // Frontend Developer
                'department_id' => 1, // Engineering
                'headcount_current' => 10,
                'headcount_projected' => 15,
                'growth_rate' => 50.0,
                'critical_skills' => [4, 8, 9], // Vue, React, TypeScript
                'emerging_skills' => [16, 17], // Next.js, Svelte
                'status' => 'approved',
                'variance_reason' => 'New product features',
            ],
            [
                'role_id' => 3, // Senior Full Stack Developer
                'department_id' => 1, // Engineering
                'headcount_current' => 5,
                'headcount_projected' => 8,
                'growth_rate' => 60.0,
                'critical_skills' => [1, 4, 5, 20], // Full stack + Architecture
                'emerging_skills' => [12, 25], // Emerging tech
                'status' => 'approved',
                'variance_reason' => 'Tech leadership needs',
            ],
            [
                'role_id' => 6, // DevOps Engineer
                'department_id' => 1, // Engineering
                'headcount_current' => 3,
                'headcount_projected' => 6,
                'growth_rate' => 100.0,
                'critical_skills' => [21, 22, 23], // Kubernetes, Docker, AWS
                'emerging_skills' => [24, 26], // AI Ops, GitOps
                'status' => 'approved',
                'variance_reason' => 'Cloud infrastructure scaling',
            ],
            [
                'role_id' => 5, // Product Manager
                'department_id' => 8, // Product
                'headcount_current' => 4,
                'headcount_projected' => 6,
                'growth_rate' => 50.0,
                'critical_skills' => [27, 28, 29], // Product Strategy, Analytics
                'emerging_skills' => [30], // AI Product
                'status' => 'draft',
                'variance_reason' => 'New product lines',
            ],
            [
                'role_id' => 4, // QA Engineer
                'department_id' => 1, // Engineering
                'headcount_current' => 8,
                'headcount_projected' => 10,
                'growth_rate' => 25.0,
                'critical_skills' => [31, 32, 33], // Testing, Automation
                'emerging_skills' => [34], // AI Testing
                'status' => 'approved',
                'variance_reason' => 'Quality assurance enhancement',
            ],
        ];

        foreach ($forecastsData as $data) {
            WorkforcePlanningRoleForecast::updateOrCreate(
                ['scenario_id' => $scenario->id, 'role_id' => $data['role_id'], 'department_id' => $data['department_id']],
                $data
            );
        }

        // Crear candidate matches variados (10+)
        $matchesData = [
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 1,
                'person_id' => 1,
                'match_score' => 92,
                'skill_match' => 95,
                'readiness_level' => 'immediate',
                'transition_type' => 'promotion',
                'transition_months' => 1,
                'risk_score' => 5,
                'gaps' => json_encode(['Advanced Architecture']),
                'recommendation' => 'Ready for immediate promotion with mentoring',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 1,
                'person_id' => 2,
                'match_score' => 78,
                'skill_match' => 75,
                'readiness_level' => 'short_term',
                'transition_type' => 'promotion',
                'transition_months' => 3,
                'risk_score' => 15,
                'gaps' => json_encode(['Microservices', 'Advanced PHP']),
                'recommendation' => 'Requires 3-month training program',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 1,
                'person_id' => 3,
                'match_score' => 65,
                'skill_match' => 60,
                'readiness_level' => 'long_term',
                'transition_type' => 'reskilling',
                'transition_months' => 6,
                'risk_score' => 25,
                'gaps' => json_encode(['System Design', 'Leadership']),
                'recommendation' => 'Longer term plan with structured development',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 2,
                'person_id' => 4,
                'match_score' => 88,
                'skill_match' => 90,
                'readiness_level' => 'immediate',
                'transition_type' => 'promotion',
                'transition_months' => 2,
                'risk_score' => 8,
                'gaps' => json_encode(['Performance Optimization']),
                'recommendation' => 'Strong candidate for senior frontend role',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 2,
                'person_id' => 5,
                'match_score' => 72,
                'skill_match' => 70,
                'readiness_level' => 'short_term',
                'transition_type' => 'reskilling',
                'transition_months' => 4,
                'risk_score' => 18,
                'gaps' => json_encode(['React', 'TypeScript Deep Dive']),
                'recommendation' => 'Training plan required for modern stack',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 3,
                'person_id' => 6,
                'match_score' => 95,
                'skill_match' => 98,
                'readiness_level' => 'immediate',
                'transition_type' => 'promotion',
                'transition_months' => 1,
                'risk_score' => 3,
                'gaps' => json_encode(['Emerging frameworks']),
                'recommendation' => 'Excellent candidate for senior role',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 4,
                'person_id' => 7,
                'match_score' => 85,
                'skill_match' => 88,
                'readiness_level' => 'short_term',
                'transition_type' => 'lateral',
                'transition_months' => 2,
                'risk_score' => 10,
                'gaps' => json_encode(['Kubernetes certification']),
                'recommendation' => 'DevOps transition with certification',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 5,
                'person_id' => 8,
                'match_score' => 81,
                'skill_match' => 85,
                'readiness_level' => 'short_term',
                'transition_type' => 'lateral',
                'transition_months' => 3,
                'risk_score' => 12,
                'gaps' => json_encode(['Product Analytics', 'Stakeholder management']),
                'recommendation' => 'Technical background + product training',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 6,
                'person_id' => 9,
                'match_score' => 79,
                'skill_match' => 80,
                'readiness_level' => 'short_term',
                'transition_type' => 'promotion',
                'transition_months' => 3,
                'risk_score' => 14,
                'gaps' => json_encode(['Automation frameworks']),
                'recommendation' => 'QA leadership potential with training',
            ],
            [
                'scenario_id' => $scenario->id,
                'forecast_id' => 1,
                'person_id' => 10,
                'match_score' => 56,
                'skill_match' => 50,
                'readiness_level' => 'long_term',
                'transition_type' => 'reskilling',
                'transition_months' => 12,
                'risk_score' => 35,
                'gaps' => json_encode(['Database design', 'Distributed systems', 'Leadership']),
                'recommendation' => 'Long-term development plan with mentoring',
            ],
        ];

        foreach ($matchesData as $data) {
            WorkforcePlanningMatch::updateOrCreate(
                ['scenario_id' => $data['scenario_id'], 'forecast_id' => $data['forecast_id'], 'person_id' => $data['person_id']],
                $data
            );
        }

        // Crear skill gaps variados (usando skills existentes)
        $skillGapsData = [
            [
                'scenario_id' => $scenario->id,
                'skill_id' => 23, // Kubernetes (si existe)
                'department_id' => 1, // Engineering
                'current_proficiency' => 2,
                'required_proficiency' => 4,
                'gap' => 2,
                'people_with_skill' => 1,
                'coverage_percentage' => 12.5,
                'priority' => 'critical',
                'remediation_strategy' => 'hiring',
                'estimated_cost' => 25000,
                'timeline_months' => 4,
            ],
            [
                'scenario_id' => $scenario->id,
                'skill_id' => 20, // Architecture (si existe)
                'department_id' => 1,
                'current_proficiency' => 2.5,
                'required_proficiency' => 4,
                'gap' => 1.5,
                'people_with_skill' => 2,
                'coverage_percentage' => 25.0,
                'priority' => 'critical',
                'remediation_strategy' => 'training',
                'estimated_cost' => 15000,
                'timeline_months' => 6,
            ],
            [
                'scenario_id' => $scenario->id,
                'skill_id' => 22, // AWS (si existe)
                'department_id' => 1,
                'current_proficiency' => 2,
                'required_proficiency' => 4,
                'gap' => 2,
                'people_with_skill' => 1,
                'coverage_percentage' => 12.5,
                'priority' => 'high',
                'remediation_strategy' => 'training',
                'estimated_cost' => 20000,
                'timeline_months' => 3,
            ],
            [
                'scenario_id' => $scenario->id,
                'skill_id' => 9, // TypeScript (si existe)
                'department_id' => 1,
                'current_proficiency' => 2.5,
                'required_proficiency' => 3.5,
                'gap' => 1.0,
                'people_with_skill' => 5,
                'coverage_percentage' => 62.5,
                'priority' => 'high',
                'remediation_strategy' => 'training',
                'estimated_cost' => 5000,
                'timeline_months' => 2,
            ],
            [
                'scenario_id' => $scenario->id,
                'skill_id' => 28, // Analytics (si existe)
                'department_id' => 8, // Product
                'current_proficiency' => 2,
                'required_proficiency' => 3.5,
                'gap' => 1.5,
                'people_with_skill' => 1,
                'coverage_percentage' => 25.0,
                'priority' => 'medium',
                'remediation_strategy' => 'training',
                'estimated_cost' => 8000,
                'timeline_months' => 3,
            ],
            [
                'scenario_id' => $scenario->id,
                'skill_id' => 33, // Testing (si existe)
                'department_id' => 1,
                'current_proficiency' => 3,
                'required_proficiency' => 4,
                'gap' => 1.0,
                'people_with_skill' => 4,
                'coverage_percentage' => 50.0,
                'priority' => 'medium',
                'remediation_strategy' => 'training',
                'estimated_cost' => 10000,
                'timeline_months' => 2,
            ],
        ];

        foreach ($skillGapsData as $data) {
            // Check if skill exists before inserting
            if (\App\Models\Skills::find($data['skill_id'])) {
                WorkforcePlanningSkillGap::updateOrCreate(
                    ['scenario_id' => $data['scenario_id'], 'skill_id' => $data['skill_id'], 'department_id' => $data['department_id']],
                    $data
                );
            }
        }

        // Crear succession plans
        $successionPlansData = [
            [
                'scenario_id' => $scenario->id,
                'role_id' => 3, // Senior Full Stack Developer
                'department_id' => 1,
                'criticality_level' => 'critical',
                'impact_if_vacant' => 'Loss of technical leadership and system design authority',
                'primary_successor_id' => 6,
                'secondary_successor_id' => 3,
                'tertiary_successor_id' => 2,
                'primary_readiness_level' => 'ready_12m',
                'primary_readiness_percentage' => 75,
                'primary_gap' => json_encode(['System Architecture', 'Team Leadership']),
                'succession_risk' => 'High dependency on current person for critical projects',
                'mitigation_actions' => json_encode(['Mentorship program', 'Architecture workshops', 'Project ownership']),
                'status' => 'approved',
            ],
            [
                'scenario_id' => $scenario->id,
                'role_id' => 2, // Frontend Developer
                'department_id' => 1,
                'criticality_level' => 'important',
                'impact_if_vacant' => 'Frontend architecture and UI standards knowledge loss',
                'primary_successor_id' => 5,
                'secondary_successor_id' => 9,
                'tertiary_successor_id' => null,
                'primary_readiness_level' => 'ready_24m',
                'primary_readiness_percentage' => 60,
                'primary_gap' => json_encode(['Advanced Frontend patterns', 'Design system mastery']),
                'succession_risk' => 'Limited backup options for critical frontend role',
                'mitigation_actions' => json_encode(['Specialized training', 'Mentoring by current lead']),
                'status' => 'draft',
            ],
            [
                'scenario_id' => $scenario->id,
                'role_id' => 7, // Technical Lead (if it exists)
                'department_id' => 1,
                'criticality_level' => 'critical',
                'impact_if_vacant' => 'Loss of project leadership and technical decision-making',
                'primary_successor_id' => 6,
                'secondary_successor_id' => 10,
                'tertiary_successor_id' => 4,
                'primary_readiness_level' => 'ready_24m',
                'primary_readiness_percentage' => 70,
                'primary_gap' => json_encode(['Leadership skills', 'Project management', 'Stakeholder management']),
                'succession_risk' => 'Knowledge concentration in one person',
                'mitigation_actions' => json_encode(['Leadership coaching', 'Executive training', 'Shadowing program']),
                'status' => 'draft',
            ],
        ];

        foreach ($successionPlansData as $data) {
            // Only create if role exists
            if (\App\Models\Roles::find($data['role_id'])) {
                WorkforcePlanningSuccessionPlan::updateOrCreate(
                    ['scenario_id' => $data['scenario_id'], 'role_id' => $data['role_id']],
                    $data
                );
            }
        }

        $this->command->info('✅ Workforce Planning seeder completed successfully');
        $this->command->info('  - 1 scenario created');
        $this->command->info('  - 6 role forecasts created');
        $this->command->info('  - 10 candidate matches created');
        $this->command->info('  - 6 skill gaps created');
        $this->command->info('  - 3 succession plans created');
    }
}
