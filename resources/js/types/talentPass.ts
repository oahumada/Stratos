/**
 * Talent Pass (CV 2.0) - TypeScript Type Definitions
 *
 * Comprehensive type definitions for all Talent Pass models, DTOs, and API responses.
 * Multi-tenant support via organization_id scoping on all entities.
 *
 * @see docs/TALENT_PASS_API.md - API endpoint reference
 * @see docs/TALENT_PASS_FRONTEND_IMPLEMENTATION.md - Implementation guide
 */

// ============================================================================
// CORE MODELS
// ============================================================================

export interface Person {
    id: number;
    organization_id: number;
    email: string;
    first_name: string;
    last_name: string;
    avatar_url?: string;
    created_at: string;
    updated_at: string;
}

export type TalentPassStatus =
    | 'draft'
    | 'in_review'
    | 'approved'
    | 'active'
    | 'completed'
    | 'archived';

export type TalentPassVisibility = 'private' | 'public';

export interface TalentPass {
    id: number;
    organization_id: number;
    people_id: number;
    ulid: string;
    title: string;
    summary?: string;
    status: TalentPassStatus;
    visibility: TalentPassVisibility;
    views_count: number;
    featured_at?: string | null;
    created_at: string;
    updated_at: string;

    // Relationships (populated with 'with=' in API)
    person: Person;
    skills?: TalentPassSkill[];
    credentials?: TalentPassCredential[];
    experiences?: TalentPassExperience[];
}

export interface TalentPassSkill {
    id: number;
    talent_pass_id: number;
    name: string;
    proficiency_level: 'beginner' | 'intermediate' | 'advanced' | 'expert';
    years_of_experience: number;
    endorsed_count: number;
    created_at: string;
    updated_at: string;
}

export interface TalentPassCredential {
    id: number;
    talent_pass_id: number;
    title: string;
    issuer: string;
    issue_date: string;
    expiry_date?: string;
    credential_url?: string;
    credential_id?: string;
    created_at: string;
    updated_at: string;
}

export interface TalentPassExperience {
    id: number;
    talent_pass_id: number;
    title: string;
    company: string;
    employment_type:
        | 'full_time'
        | 'part_time'
        | 'contract'
        | 'freelance'
        | 'internship';
    location?: string;
    start_date: string;
    end_date?: string;
    is_current: boolean;
    description?: string;
    created_at: string;
    updated_at: string;
}

// ============================================================================
// DTO - Request Payloads
// ============================================================================

export interface CreateTalentPassRequest {
    title: string;
    summary?: string;
    visibility: TalentPassVisibility;
}

export interface UpdateTalentPassRequest {
    title?: string;
    summary?: string;
    visibility?: TalentPassVisibility;
}

export interface AddSkillRequest {
    name: string;
    proficiency_level: 'beginner' | 'intermediate' | 'advanced' | 'expert';
    years_of_experience: number;
}

export interface UpdateSkillRequest {
    name?: string;
    proficiency_level?: 'beginner' | 'intermediate' | 'advanced' | 'expert';
    years_of_experience?: number;
}

export interface AddCredentialRequest {
    title: string;
    issuer: string;
    issue_date: string;
    expiry_date?: string;
    credential_url?: string;
    credential_id?: string;
}

export interface AddExperienceRequest {
    title: string;
    company: string;
    employment_type:
        | 'full_time'
        | 'part_time'
        | 'contract'
        | 'freelance'
        | 'internship';
    location?: string;
    start_date: string;
    end_date?: string;
    is_current: boolean;
    description?: string;
}

export interface PublishTalentPassRequest {
    reason?: string;
}

export interface ArchiveTalentPassRequest {
    reason?: string;
}

export interface CloneTalentPassRequest {
    title: string;
}

export interface ExportTalentPassRequest {
    format: 'pdf' | 'json' | 'linkedin';
    include_private_notes?: boolean;
}

export interface ShareTalentPassRequest {
    message?: string;
    expiry_days?: number;
}

// ============================================================================
// API RESPONSES
// ============================================================================

export interface ApiResponse<T> {
    data: T;
    message?: string;
    success?: boolean;
}

export interface PaginatedResponse<T> {
    data: T[];
    links: {
        first: string;
        last: string;
        prev?: string;
        next?: string;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        per_page: number;
        to: number;
        total: number;
    };
}

export interface TalentPassResponse extends ApiResponse<TalentPass> {
    completeness: number;
}

export interface PublicTalentPassResponse {
    data: TalentPass;
    completeness: number;
    is_owner: boolean;
}

export interface SearchResponse extends PaginatedResponse<TalentPass> {
    meta: PaginatedResponse<TalentPass>['meta'] & {
        search_query: string;
        filters_applied: Record<string, any>;
    };
}

export interface ExportResponse {
    url: string;
    format: 'pdf' | 'json' | 'linkedin';
    expires_at: string;
}

export interface ShareResponse {
    public_url: string;
    public_id: string;
    expires_at?: string;
}

// ============================================================================
// PAGINATION & FILTERING
// ============================================================================

export interface PaginationParams {
    page?: number;
    per_page?: number;
    sort_by?: string;
    sort_order?: 'asc' | 'desc';
}

export interface SearchFilters {
    status?: TalentPassStatus;
    visibility?: TalentPassVisibility;
    search?: string;
    skill?: string;
    company?: string;
    location?: string;
    min_completeness?: number;
}

export interface TrendingParams {
    limit?: number;
    days?: number;
}

// ============================================================================
// STATE MANAGEMENT (Pinia Store)
// ============================================================================

export interface TalentPassState {
    // Collections
    talentPasses: TalentPass[];
    currentTalentPass: TalentPass | null;

    // UI State
    loading: boolean;
    error: string | null;
    selectedSkillId: number | null;
    selectedCredentialId: number | null;
    selectedExperienceId: number | null;

    // Filters & Search
    searchQuery: string;
    filters: SearchFilters;

    // Pagination
    currentPage: number;
    perPage: number;
    totalPages: number;

    // Modal/Dialog State
    isCreateModalOpen: boolean;
    isShareDialogOpen: boolean;
    isExportMenuOpen: boolean;
}

// ============================================================================
// UI COMPONENT PROPS & EMITS
// ============================================================================

export interface TalentPassCardProps {
    talentPass: TalentPass;
    isEditable?: boolean;
    onEdit?: (talentPass: TalentPass) => void;
    onDelete?: (id: number) => void;
    onShare?: (id: number) => void;
}

export interface CompletenessIndicatorProps {
    talentPass: TalentPass;
    showLabel?: boolean;
    showPercentage?: boolean;
    animated?: boolean;
}

export interface SkillsManagerProps {
    talentPass: TalentPass;
    isEditable: boolean;
    onSkillAdded?: (skill: TalentPassSkill) => void;
    onSkillRemoved?: (skillId: number) => void;
}

export interface ExperienceManagerProps {
    talentPass: TalentPass;
    isEditable: boolean;
    onExperienceAdded?: (exp: TalentPassExperience) => void;
    onExperienceRemoved?: (expId: number) => void;
}

export interface CredentialManagerProps {
    talentPass: TalentPass;
    isEditable: boolean;
    onCredentialAdded?: (cred: TalentPassCredential) => void;
    onCredentialRemoved?: (credId: number) => void;
}

// ============================================================================
// COMPUTED/DERIVED TYPES
// ============================================================================

export interface TalentPassComputedState {
    isDraft: boolean;
    isPublished: boolean;
    isArchived: boolean;
    canEdit: boolean;
    canPublish: boolean;
    canDelete: boolean;
    completenessPercentage: number;
    missingElements: string[];
}

export interface TalentPassMetrics {
    totalViews: number;
    skillsCount: number;
    credentialsCount: number;
    experienceCount: number;
    completenessScore: number;
    lastModified: string;
}
