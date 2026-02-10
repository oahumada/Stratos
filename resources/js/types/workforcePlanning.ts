export interface WorkforcePlan {
    id: number;
    organization_id: number;
    name: string;
    code: string;
    description?: string | null;
    start_date: string;
    end_date: string;
    planning_horizon_months: number;
    scope_type:
        | 'organization_wide'
        | 'business_unit'
        | 'department'
        | 'critical_roles_only';
    scope_notes?: string | null;
    strategic_context?: string | null;
    budget_constraints?: string | null;
    legal_constraints?: string | null;
    labor_relations_constraints?: string | null;
    status:
        | 'draft'
        | 'in_review'
        | 'approved'
        | 'active'
        | 'completed'
        | 'archived';
    approved_at?: string | null;
    approved_by?: number | null;
    owner_user_id?: number | null;
    sponsor_user_id?: number | null;
    created_by?: number | null;
    updated_by?: number | null;
    created_at?: string;
    updated_at?: string;
}

export interface ScopeUnitPayload {
    unit_type:
        | 'department'
        | 'business_unit'
        | 'location'
        | 'cost_center'
        | 'custom';
    unit_id?: number | null;
    unit_name: string;
    inclusion_reason:
        | 'critical'
        | 'high_turnover'
        | 'transformation'
        | 'growth'
        | 'downsizing'
        | 'other';
    notes?: string | null;
}

export interface ScopeRolePayload {
    role_id: number;
    inclusion_reason:
        | 'critical'
        | 'hard_to_fill'
        | 'transformation'
        | 'high_risk'
        | 'other';
    notes?: string | null;
}

export interface TransformationProjectPayload {
    project_name: string;
    project_type:
        | 'digital_transformation'
        | 'process_automation'
        | 'growth'
        | 'downsizing'
        | 'merger_acquisition'
        | 'restructuring'
        | 'other';
    expected_impact?: string | null;
    estimated_fte_impact?: number | null;
    start_date?: string | null;
    end_date?: string | null;
}

export interface TalentRiskPayload {
    risk_type:
        | 'aging_workforce'
        | 'high_turnover'
        | 'scarce_skills'
        | 'succession_gap'
        | 'knowledge_loss'
        | 'external_competition'
        | 'other';
    risk_description: string;
    affected_unit_id?: number | null;
    affected_role_id?: number | null;
    severity: 'low' | 'medium' | 'high' | 'critical';
    likelihood: 'low' | 'medium' | 'high';
    mitigation_strategy?: string | null;
}

export interface StakeholderPayload {
    user_id: number;
    role:
        | 'sponsor'
        | 'owner'
        | 'contributor'
        | 'reviewer'
        | 'approver'
        | 'informed';
    represents?: string | null;
}

export interface ScopeDocumentPayload {
    title?: string;
    notes?: string;
    include_sections?: string[];
}

export interface StatisticsResponse {
    total_units: number;
    total_roles: number;
    total_projects: number;
    total_risks: number;
    completion_percentage: number;
}

export interface PaginatedResponse<T> {
    data: T[];
    links?: any;
    meta?: any;
}

export interface CreatePlanPayload {
    name: string;
    description?: string;
    start_date: string;
    end_date: string;
    planning_horizon_months: number;
    scope_type: WorkforcePlan['scope_type'];
    scope_notes?: string;
    strategic_context?: string;
    budget_constraints?: string;
    legal_constraints?: string;
    labor_relations_constraints?: string;
    owner_user_id: number;
    sponsor_user_id?: number;
    stakeholders?: StakeholderPayload[];
}
export type PlanStatus =
    | 'draft'
    | 'in_review'
    | 'approved'
    | 'active'
    | 'completed'
    | 'archived';
export type ScopeType =
    | 'organization_wide'
    | 'business_unit'
    | 'department'
    | 'critical_roles_only';

export interface WorkforcePlan {
    id: number;
    organization_id: number;
    name: string;
    code: string;
    description?: string;
    start_date: string;
    end_date: string;
    planning_horizon_months: number;
    scope_type: ScopeType;
    scope_notes?: string;
    strategic_context?: string;
    budget_constraints?: string;
    legal_constraints?: string;
    labor_relations_constraints?: string;
    status: PlanStatus;
    approved_at?: string | null;
    approved_by?: number | null;
    owner_user_id: number;
    sponsor_user_id?: number | null;
    created_by: number;
    updated_by?: number | null;
    created_at?: string;
    updated_at?: string;
}

export interface ScopeUnit {
    id: number;
    workforce_plan_id: number;
    unit_type:
        | 'department'
        | 'business_unit'
        | 'location'
        | 'cost_center'
        | 'custom';
    unit_id?: number | null;
    unit_name: string;
    inclusion_reason:
        | 'critical'
        | 'high_turnover'
        | 'transformation'
        | 'growth'
        | 'downsizing'
        | 'other';
    notes?: string;
    created_at: string;
}

export interface ScopeRole {
    id: number;
    workforce_plan_id: number;
    role_id: number;
    inclusion_reason:
        | 'critical'
        | 'hard_to_fill'
        | 'transformation'
        | 'high_risk'
        | 'other';
    notes?: string;
    created_at: string;
}

export interface TransformationProject {
    id: number;
    workforce_plan_id: number;
    project_name: string;
    project_type:
        | 'digital_transformation'
        | 'process_automation'
        | 'growth'
        | 'downsizing'
        | 'merger_acquisition'
        | 'restructuring'
        | 'other';
    expected_impact?: string;
    estimated_fte_impact?: number | null;
    start_date?: string | null;
    end_date?: string | null;
    created_at: string;
}

export interface TalentRisk {
    id: number;
    workforce_plan_id: number;
    risk_type:
        | 'aging_workforce'
        | 'high_turnover'
        | 'scarce_skills'
        | 'succession_gap'
        | 'knowledge_loss'
        | 'external_competition'
        | 'other';
    risk_description: string;
    affected_unit_id?: number | null;
    affected_role_id?: number | null;
    severity: 'low' | 'medium' | 'high' | 'critical';
    likelihood: 'low' | 'medium' | 'high';
    mitigation_strategy?: string;
    created_at?: string;
    updated_at?: string;
}

export interface PlanDocument {
    id: number;
    workforce_plan_id: number;
    document_type:
        | 'strategic_plan'
        | 'business_plan'
        | 'budget'
        | 'transformation_plan'
        | 'other';
    document_name: string;
    document_url?: string | null;
    uploaded_by: number;
    uploaded_at: string;
}

export interface PlanStatistics {
    units: number;
    roles: number;
    projects: number;
    risks: number;
    documents: number;
}
