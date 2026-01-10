<?php

namespace App\Services\WorkforcePlanning;

use App\Models\WorkforcePlan;
use App\Models\WorkforcePlanTalentRisk;
use Illuminate\Support\Facades\DB;

class WorkforcePlanningPhase1Service
{
    public function createPlan(int $orgId, array $data, int $creatorId): WorkforcePlan
    {
        return DB::transaction(function () use ($orgId, $data, $creatorId) {
            $plan = WorkforcePlan::create([
                'organization_id' => $orgId,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'planning_horizon_months' => $data['planning_horizon_months'],
                'scope_type' => $data['scope_type'],
                'scope_notes' => $data['scope_notes'] ?? null,
                'strategic_context' => $data['strategic_context'] ?? null,
                'budget_constraints' => $data['budget_constraints'] ?? null,
                'legal_constraints' => $data['legal_constraints'] ?? null,
                'labor_relations_constraints' => $data['labor_relations_constraints'] ?? null,
                'owner_user_id' => $data['owner_user_id'],
                'sponsor_user_id' => $data['sponsor_user_id'] ?? null,
                'status' => 'draft',
                'created_by' => $creatorId,
            ]);

            if (!empty($data['stakeholders'])) {
                foreach ($data['stakeholders'] as $stakeholder) {
                    $plan->stakeholders()->create($stakeholder);
                }
            }

            return $plan->load(['owner', 'sponsor', 'stakeholders.user']);
        });
    }

    public function updatePlan(WorkforcePlan $plan, array $data, int $updaterId): WorkforcePlan
    {
        if (!$plan->canBeEdited()) {
            throw new \Exception('El plan no puede ser editado en su estado actual');
        }

        $plan->update(array_merge($data, ['updated_by' => $updaterId]));

        return $plan->fresh();
    }

    public function addScopeUnits(WorkforcePlan $plan, array $units): void
    {
        foreach ($units as $unit) {
            $plan->scopeUnits()->create($unit);
        }
    }

    public function addScopeRoles(WorkforcePlan $plan, array $roles): void
    {
        foreach ($roles as $role) {
            $plan->scopeRoles()->create($role);
        }
    }

    public function addTransformationProjects(WorkforcePlan $plan, array $projects): void
    {
        foreach ($projects as $project) {
            $plan->transformationProjects()->create($project);
        }
    }

    public function addTalentRisk(WorkforcePlan $plan, array $riskData): WorkforcePlanTalentRisk
    {
        return $plan->talentRisks()->create($riskData);
    }

    public function analyzeBasicRisks(WorkforcePlan $plan): array
    {
        $risks = [];
        $orgId = $plan->organization_id;

        // 1. High turnover
        $highTurnoverRoles = DB::table('users')
            ->select('role_id', DB::raw('COUNT(*) as exits'))
            ->where('organization_id', $orgId)
            ->whereNotNull('exit_date')
            ->whereNotNull('role_id')
            ->where('exit_date', '>=', now()->subYear())
            ->groupBy('role_id')
            ->having('exits', '>', 3)
            ->get();

        foreach ($highTurnoverRoles as $roleData) {
            $risks[] = [
                'risk_type' => 'high_turnover',
                'risk_description' => "Rol con {$roleData->exits} salidas en el último año",
                'affected_role_id' => $roleData->role_id,
                'severity' => $roleData->exits > 5 ? 'high' : 'medium',
                'likelihood' => 'high',
            ];
        }

        // Other analyses omitted for brevity — implement later as needed

        return $risks;
    }

    public function saveDetectedRisks(WorkforcePlan $plan): int
    {
        $risks = $this->analyzeBasicRisks($plan);

        foreach ($risks as $risk) {
            $this->addTalentRisk($plan, $risk);
        }

        return count($risks);
    }

    public function generateScopeDocument(WorkforcePlan $plan): array
    {
        $plan->load([
            'owner',
            'sponsor',
            'scopeUnits',
            'scopeRoles.role',
            'transformationProjects',
            'talentRisks.affectedRole',
            'stakeholders.user',
        ]);

        return [
            'plan_code' => $plan->code,
            'plan_name' => $plan->name,
            'description' => $plan->description,
            'period' => [
                'start' => $plan->start_date->format('Y-m-d'),
                'end' => $plan->end_date->format('Y-m-d'),
                'duration_months' => $plan->start_date->diffInMonths($plan->end_date),
            ],
            'horizon_months' => $plan->planning_horizon_months,
            // minimal payload; frontend can format
            'scope' => [
                'type' => $plan->scope_type,
                'notes' => $plan->scope_notes,
            ],
            'generated_at' => now()->toIso8601String(),
        ];
    }

    public function getPlanStatistics(WorkforcePlan $plan): array
    {
        return [
            'scope_units_count' => $plan->scopeUnits()->count(),
            'scope_roles_count' => $plan->scopeRoles()->count(),
            'transformation_projects_count' => $plan->transformationProjects()->count(),
            'total_estimated_fte_impact' => $plan->transformationProjects()->sum('estimated_fte_impact'),
            'talent_risks_count' => $plan->talentRisks()->count(),
            'critical_risks_count' => $plan->talentRisks()->where('severity', 'critical')->count(),
            'stakeholders_count' => $plan->stakeholders()->count(),
            'documents_count' => $plan->documents()->count(),
            'completion_percentage' => $this->calculateCompletionPercentage($plan),
        ];
    }

    private function calculateCompletionPercentage(WorkforcePlan $plan): int
    {
        $checks = [
            !empty($plan->name),
            !empty($plan->start_date),
            !empty($plan->end_date),
            !empty($plan->scope_type),
            !empty($plan->owner_user_id),
            !empty($plan->strategic_context),
            $plan->scopeUnits()->count() > 0 || $plan->scopeRoles()->count() > 0 || $plan->scope_type === 'organization_wide',
            $plan->transformationProjects()->count() > 0,
            $plan->talentRisks()->count() > 0,
            $plan->stakeholders()->count() > 0,
        ];

        $completed = count(array_filter($checks));
        $total = count($checks);

        return (int) round(($completed / $total) * 100);
    }
}
