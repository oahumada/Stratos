import axios from 'axios';
import { defineStore } from 'pinia';

export interface Scenario {
    id: number;
    name: string;
    description: string;
    planning_horizon: number;
    status: 'draft' | 'active' | 'archived';
    created_at: string;
    updated_at: string;
}

export interface Analytics {
    total_headcount_current: number;
    total_headcount_projected: number;
    net_growth: number;
    internal_coverage_percentage: number;
    external_gap_percentage: number;
    total_skills_required: number;
    skills_with_gaps: number;
    critical_skills_at_risk: number;
    critical_roles: number;
    critical_roles_with_successor: number;
    succession_risk_percentage: number;
    estimated_recruitment_cost: number;
    estimated_training_cost: number;
    estimated_external_hiring_months: number;
    high_risk_positions: number;
    medium_risk_positions: number;
}

export interface RoleForecast {
    id: number;
    role_name: string;
    current_headcount: number;
    projected_headcount: number;
    growth_rate: number;
    critical_skills: string[];
    emerging_skills: string[];
    criticality_level: 'critical' | 'high' | 'medium' | 'low';
    area: string;
}

export interface Match {
    id: number;
    candidate_name: string;
    current_role: string;
    target_role: string;
    match_score: number;
    readiness_level: 'immediate' | 'short_term' | 'long_term' | 'not_ready';
    skill_gaps: string[];
    transition_type: 'promotion' | 'lateral' | 'reskilling' | 'no_match';
    risk_level: 'low' | 'medium' | 'high';
}

export interface SkillGap {
    id: number;
    skill_name: string;
    priority: 'critical' | 'high' | 'medium' | 'low';
    coverage_percentage: number;
    remediation_strategy: string;
    department: string;
}

export interface SuccessionPlan {
    id: number;
    role_name: string;
    current_holder: string;
    criticality_level: 'critical' | 'high' | 'medium';
    planned_retirement?: string;
    months_to_retirement?: number;
    tenure_years?: number;
    primary_successor?: {
        id: number;
        name: string;
        readiness: number;
        current_role: string;
    };
    secondary_successors?: Array<{
        id: number;
        name: string;
        readiness: number;
        current_role: string;
    }>;
    development_plan?: {
        duration?: string;
        description?: string;
    };
    mentoring_assigned?: boolean;
}

interface ScenarioPlanningState {
    scenarios: Scenario[];
    activeScenario: Scenario | null;
    activeScenarioId: number | null;

    // Caches por scenario
    analyticsByScenario: Map<number, Analytics>;
    forecastsByScenario: Map<number, RoleForecast[]>;
    matchesByScenario: Map<number, Match[]>;
    gapsByScenario: Map<number, SkillGap[]>;
    successionPlansByScenario: Map<number, SuccessionPlan[]>;

    // Filters compartidos
    filters: {
        forecastArea: string | null;
        forecastCriticality: string | null;
        matchReadiness: string | null;
        matchScoreRange: 'high' | 'medium' | 'low' | null;
        gapPriority: string | null;
        gapDepartment: string | null;
        searchTerm: string | null;
    };

    // Loading states
    loading: {
        scenarios: boolean;
        analytics: boolean;
        forecasts: boolean;
        matches: boolean;
        gaps: boolean;
        succession: boolean;
    };

    // Errors
    errors: {
        [key: string]: string | null;
    };
}

export const useScenarioPlanningStore = defineStore('scenarioPlanning', {
    state: (): ScenarioPlanningState => ({
        scenarios: [],
        activeScenario: null,
        activeScenarioId: null,

        analyticsByScenario: new Map(),
        forecastsByScenario: new Map(),
        matchesByScenario: new Map(),
        gapsByScenario: new Map(),
        successionPlansByScenario: new Map(),

        filters: {
            forecastArea: null,
            forecastCriticality: null,
            matchReadiness: null,
            matchScoreRange: null,
            gapPriority: null,
            gapDepartment: null,
            searchTerm: null,
        },

        loading: {
            scenarios: false,
            analytics: false,
            forecasts: false,
            matches: false,
            gaps: false,
            succession: false,
        },

        errors: {
            scenarios: null,
            analytics: null,
            forecasts: null,
            matches: null,
            gaps: null,
            succession: null,
        },
    }),

    getters: {
        // Scenarios
        getAllScenarios: (state) => state.scenarios,
        getActiveScenario: (state) => state.activeScenario,

        // Analytics
        getAnalytics: (state) => (scenarioId: number) => {
            return state.analyticsByScenario.get(scenarioId) || null;
        },

        // Forecasts
        getForecasts: (state) => (scenarioId: number) => {
            return state.forecastsByScenario.get(scenarioId) || [];
        },

        getFilteredForecasts: (state) => (scenarioId: number) => {
            const forecasts = state.forecastsByScenario.get(scenarioId) || [];
            if (!Array.isArray(forecasts)) return [];
            let filtered = forecasts;

            if (state.filters.forecastArea) {
                filtered = filtered.filter(
                    (f) => f.area === state.filters.forecastArea,
                );
            }

            if (state.filters.forecastCriticality) {
                filtered = filtered.filter(
                    (f) =>
                        f.criticality_level ===
                        state.filters.forecastCriticality,
                );
            }

            return filtered;
        },

        // Matches
        getMatches: (state) => (scenarioId: number) => {
            return state.matchesByScenario.get(scenarioId) || [];
        },

        getFilteredMatches: (state) => (scenarioId: number) => {
            const matches = state.matchesByScenario.get(scenarioId) || [];
            if (!Array.isArray(matches)) return [];
            let filtered = matches;

            if (state.filters.matchReadiness) {
                filtered = filtered.filter(
                    (m) => m.readiness_level === state.filters.matchReadiness,
                );
            }

            if (state.filters.matchScoreRange) {
                filtered = filtered.filter((m) => {
                    if (state.filters.matchScoreRange === 'high')
                        return m.match_score >= 80;
                    if (state.filters.matchScoreRange === 'medium')
                        return m.match_score >= 60 && m.match_score < 80;
                    if (state.filters.matchScoreRange === 'low')
                        return m.match_score < 60;
                    return true;
                });
            }

            if (state.filters.searchTerm) {
                const term = state.filters.searchTerm.toLowerCase();
                filtered = filtered.filter(
                    (m) =>
                        m.candidate_name.toLowerCase().includes(term) ||
                        m.current_role.toLowerCase().includes(term) ||
                        m.target_role.toLowerCase().includes(term),
                );
            }

            return filtered;
        },

        // Skill Gaps
        getSkillGaps: (state) => (scenarioId: number) => {
            return state.gapsByScenario.get(scenarioId) || [];
        },

        getFilteredSkillGaps: (state) => (scenarioId: number) => {
            const gaps = state.gapsByScenario.get(scenarioId) || [];
            if (!Array.isArray(gaps)) return [];
            let filtered = gaps;

            if (state.filters.gapPriority) {
                filtered = filtered.filter(
                    (g) => g.priority === state.filters.gapPriority,
                );
            }

            if (state.filters.gapDepartment) {
                filtered = filtered.filter(
                    (g) => g.department === state.filters.gapDepartment,
                );
            }

            return filtered;
        },

        // Succession Plans
        getSuccessionPlans: (state) => (scenarioId: number) => {
            return state.successionPlansByScenario.get(scenarioId) || [];
        },

        // Loading states
        isLoading: (state) =>
            Object.values(state.loading).some((v) => v === true),
        getLoadingState: (state) => (key: string) =>
            state.loading[key as keyof typeof state.loading],

        // Errors
        getError: (state) => (key: string) => state.errors[key] || null,
    },

    actions: {
        // Scenario Management
        async fetchScenarios() {
            this.loading.scenarios = true;
            this.errors.scenarios = null;

            try {
                const response = await axios.get(
                    '/api/strategic-planning/scenarios',
                );
                this.scenarios =
                    response.data && Array.isArray(response.data.data)
                        ? response.data.data
                        : Array.isArray(response.data)
                          ? response.data
                          : [];
            } catch (error: any) {
                this.errors.scenarios =
                    error.message || 'Failed to fetch scenarios';
                console.error('Error fetching scenarios:', error);
            } finally {
                this.loading.scenarios = false;
            }
        },

        async fetchScenario(id: number) {
            try {
                const response = await axios.get(
                    `/api/strategic-planning/scenarios/${id}`,
                );
                this.activeScenario =
                    response.data && response.data.data
                        ? response.data.data
                        : response.data;
                this.activeScenarioId = id;
                return response.data && response.data.data
                    ? response.data.data
                    : response.data;
            } catch (error: any) {
                this.errors.scenarios =
                    error.message || 'Failed to fetch scenario';
                console.error('Error fetching scenario:', error);
            }
        },

        setActiveScenario(id: number) {
            this.activeScenarioId = id;
            const scenario = this.scenarios.find((s) => s.id === id);
            if (scenario) {
                this.activeScenario = scenario;
            }
        },

        // Analytics
        async fetchAnalytics(scenarioId: number) {
            // Return cached analytics if available
            if (this.analyticsByScenario.has(scenarioId)) {
                return this.analyticsByScenario.get(scenarioId);
            }

            this.loading.analytics = true;
            this.errors.analytics = null;

            try {
                // Endpoint legacy deshabilitado temporalmente hasta migrar al nuevo módulo
                // this.analyticsByScenario.set(scenarioId, response.data)
                // return response.data
                return null;
            } catch (error: any) {
                this.errors.analytics =
                    error.message || 'Failed to fetch analytics';
                console.error('Error fetching analytics:', error);
            } finally {
                this.loading.analytics = false;
            }
        },

        invalidateAnalyticsCache(scenarioId: number) {
            this.analyticsByScenario.delete(scenarioId);
        },

        // Role Forecasts
        async fetchForecasts(scenarioId: number) {
            if (!scenarioId) {
                console.warn(
                    'fetchForecasts called with invalid scenarioId:',
                    scenarioId,
                );
                return [];
            }

            // Return cached forecasts if available
            if (this.forecastsByScenario.has(scenarioId)) {
                return this.forecastsByScenario.get(scenarioId);
            }

            this.loading.forecasts = true;
            this.errors.forecasts = null;

            try {
                // Endpoints legacy deshabilitados temporalmente
                this.forecastsByScenario.set(scenarioId, []);
                return [];
            } catch (error: any) {
                this.errors.forecasts =
                    error.message || 'Failed to fetch forecasts';
                console.error('Error fetching forecasts:', error);
                this.forecastsByScenario.set(scenarioId, []);
                return [];
            } finally {
                this.loading.forecasts = false;
            }
        },

        setForecastAreaFilter(area: string | null) {
            this.filters.forecastArea = area;
        },

        setForecastCriticalityFilter(criticality: string | null) {
            this.filters.forecastCriticality = criticality;
        },

        // Matches
        async fetchMatches(scenarioId: number) {
            if (!scenarioId) {
                console.warn(
                    'fetchMatches called with invalid scenarioId:',
                    scenarioId,
                );
                return [];
            }

            // Return cached matches if available
            if (this.matchesByScenario.has(scenarioId)) {
                return this.matchesByScenario.get(scenarioId);
            }

            this.loading.matches = true;
            this.errors.matches = null;

            try {
                const res = await axios.get(
                    `/api/strategic-planning/scenarios/${scenarioId}/step2/matching-results`,
                );

                // Backend returns { data: [...] }
                const payload = res?.data?.data ?? res?.data ?? [];

                const mapped = Array.isArray(payload)
                    ? payload.map((r: any) => {
                          const score =
                              r.match_percentage ?? r.match_score ?? 0;
                          const timeline =
                              r.productivity_timeline ??
                              r.productivity_timeline_weeks ??
                              0;
                          const readiness =
                              timeline <= 1
                                  ? 'immediate'
                                  : timeline <= 3
                                    ? 'short_term'
                                    : timeline <= 12
                                      ? 'long_term'
                                      : 'not_ready';
                          const skillGaps = Array.isArray(r.skill_gaps)
                              ? r.skill_gaps.map((g: any) =>
                                    typeof g === 'string'
                                        ? g
                                        : g.skill_name || g.name || g,
                                )
                              : [];

                          const matchScore = Math.round(Number(score) || 0);

                          const risk_level =
                              matchScore >= 80
                                  ? 'low'
                                  : matchScore >= 60
                                    ? 'medium'
                                    : 'high';

                          const transition_type =
                              skillGaps.length > 0 ? 'reskilling' : 'lateral';

                          return {
                              id:
                                  r.id ??
                                  `${r.candidate_id || 'p'}-${r.target_role_id || 'r'}`,
                              candidate_name:
                                  r.candidate_name ?? r.full_name ?? 'Unknown',
                              current_role:
                                  r.current_role ?? r.current_position ?? '—',
                              target_role:
                                  r.target_role ??
                                  r.target_position ??
                                  r.target_role_name ??
                                  '—',
                              match_score: matchScore,
                              readiness_level: readiness,
                              skill_gaps: skillGaps,
                              transition_type: transition_type,
                              risk_level: risk_level,
                          } as any;
                      })
                    : [];

                this.matchesByScenario.set(scenarioId, mapped);
                return mapped;
            } catch (error: any) {
                this.errors.matches =
                    error.message || 'Failed to fetch matches';
                console.error('Error fetching matches:', error);
                this.matchesByScenario.set(scenarioId, []);
                return [];
            } finally {
                this.loading.matches = false;
            }
        },

        setMatchReadinessFilter(readiness: string | null) {
            this.filters.matchReadiness = readiness;
        },

        setMatchScoreRangeFilter(range: 'high' | 'medium' | 'low' | null) {
            this.filters.matchScoreRange = range;
        },

        setSearchTermFilter(term: string | null) {
            this.filters.searchTerm = term;
        },

        // Skill Gaps
        async fetchSkillGaps(scenarioId: number) {
            if (!scenarioId) {
                console.warn(
                    'fetchSkillGaps called with invalid scenarioId:',
                    scenarioId,
                );
                return [];
            }

            // Return cached gaps if available
            if (this.gapsByScenario.has(scenarioId)) {
                return this.gapsByScenario.get(scenarioId);
            }

            this.loading.gaps = true;
            this.errors.gaps = null;

            try {
                this.gapsByScenario.set(scenarioId, []);
                return [];
            } catch (error: any) {
                this.errors.gaps =
                    error.message || 'Failed to fetch skill gaps';
                console.error('Error fetching skill gaps:', error);
                this.gapsByScenario.set(scenarioId, []);
                return [];
            } finally {
                this.loading.gaps = false;
            }
        },

        setGapPriorityFilter(priority: string | null) {
            this.filters.gapPriority = priority;
        },

        setGapDepartmentFilter(department: string | null) {
            this.filters.gapDepartment = department;
        },

        // Succession Plans
        async fetchSuccessionPlans(scenarioId: number) {
            if (!scenarioId) {
                console.warn(
                    'fetchSuccessionPlans called with invalid scenarioId:',
                    scenarioId,
                );
                return [];
            }

            // Return cached succession plans if available
            if (this.successionPlansByScenario.has(scenarioId)) {
                return this.successionPlansByScenario.get(scenarioId);
            }

            this.loading.succession = true;
            this.errors.succession = null;

            try {
                this.successionPlansByScenario.set(scenarioId, []);
                return [];
            } catch (error: any) {
                this.errors.succession =
                    error.message || 'Failed to fetch succession plans';
                console.error('Error fetching succession plans:', error);
                this.successionPlansByScenario.set(scenarioId, []);
                return [];
            } finally {
                this.loading.succession = false;
            }
        },

        // Cache Management
        clearAllCaches() {
            this.analyticsByScenario.clear();
            this.forecastsByScenario.clear();
            this.matchesByScenario.clear();
            this.gapsByScenario.clear();
            this.successionPlansByScenario.clear();
        },

        clearScenarioCaches(scenarioId: number) {
            this.analyticsByScenario.delete(scenarioId);
            this.forecastsByScenario.delete(scenarioId);
            this.matchesByScenario.delete(scenarioId);
            this.gapsByScenario.delete(scenarioId);
            this.successionPlansByScenario.delete(scenarioId);
        },

        // Reset Filters
        resetFilters() {
            this.filters = {
                forecastArea: null,
                forecastCriticality: null,
                matchReadiness: null,
                matchScoreRange: null,
                gapPriority: null,
                gapDepartment: null,
                searchTerm: null,
            };
        },
    },
});
