export interface SuccessionPerson {
    id: number;
    name: string;
    email: string;
    current_role: string;
    photo_url: string | null;
    is_high_potential: boolean;
}

export interface SuccessionMetrics {
    skill_match: number;
    stability: number;
    learning_velocity: number;
    trajectory_fit: number;
    legacy_risk: number;
}

export interface SkillGap {
    skill_name: string;
    missing_level: number;
    priority: 'high' | 'medium' | 'low';
}

export interface SuccessionCandidate {
    person: SuccessionPerson;
    readiness_score: number;
    readiness_level: string;
    estimated_months: number;
    metrics: SuccessionMetrics;
    gaps: SkillGap[];
    trajectory_summary: {
        total_movements: number;
        last_movement: string;
        years_in_org: number;
    };
    potential_replacements?: Array<{
        id: number;
        name: string;
        photo_url: string | null;
        readiness_score: number;
        readiness_level: string;
    }>;
    recommended_courses?: Array<{
        skill: string;
        course: {
            id: string;
            title: string;
            provider: string;
            duration?: string;
            url?: string;
        };
    }>;
}

export interface SuccessionResponse {
    success: boolean;
    role_id: number;
    candidates: SuccessionCandidate[];
}
