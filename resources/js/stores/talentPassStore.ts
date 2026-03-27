/**
 * Talent Pass Store (Pinia)
 *
 * Centralized state management for Talent Pass CRUD operations, search, and filtering.
 * All actions are multi-tenant scoped (organization_id validated server-side).
 *
 * @see resources/js/types/talentPass.ts - Type definitions
 * @see docs/TALENT_PASS_FRONTEND_IMPLEMENTATION.md - Implementation guide
 *
 * PHASES:
 * Phase 2: Store (45 min)
 *   - State management
 *   - CRUD actions
 *   - Search/filtering
 *   - Computed properties
 */

import { apiClient } from '@/lib/apiClient';
import type {
    CreateTalentPassRequest,
    PaginationParams,
    SearchFilters,
    TalentPass,
    UpdateTalentPassRequest,
} from '@/types/talentPass';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useTalentPassStore = defineStore('talents-pass', () => {
    // ========================================================================
    // STATE
    // ========================================================================

    const talentPasses = ref<TalentPass[]>([]);
    const currentTalentPass = ref<TalentPass | null>(null);

    const loading = ref(false);
    const error = ref<string | null>(null);
    const selectedSkillId = ref<number | null>(null);
    const selectedCredentialId = ref<number | null>(null);
    const selectedExperienceId = ref<number | null>(null);

    const searchQuery = ref('');
    const filters = ref<SearchFilters>({});

    const currentPage = ref(1);
    const perPage = ref(10);
    const totalPages = ref(1);

    const isCreateModalOpen = ref(false);
    const isShareDialogOpen = ref(false);
    const isExportMenuOpen = ref(false);

    // ========================================================================
    // COMPUTED PROPERTIES
    // ========================================================================

    /**
     * Calculate completeness score for a Talent Pass
     * Based on populated sections: skills, experiences, credentials
     */
    const calculateCompleteness = (tp: TalentPass): number => {
        let score = 0;
        let total = 5;

        if (tp.title) score++;
        if (tp.summary) score++;
        if (tp.skills && tp.skills.length > 0) score++;
        if (tp.experiences && tp.experiences.length > 0) score++;
        if (tp.credentials && tp.credentials.length > 0) score++;

        return Math.round((score / total) * 100);
    };

    const currentCompleteness = computed(() => {
        return currentTalentPass.value
            ? calculateCompleteness(currentTalentPass.value)
            : 0;
    });

    const isDraft = computed(() => {
        return currentTalentPass.value?.status === 'draft';
    });

    const isPublished = computed(() => {
        return (
            currentTalentPass.value?.status === 'active' ||
            currentTalentPass.value?.status === 'approved'
        );
    });

    const filteredTalentPasses = computed(() => {
        return talentPasses.value.filter((tp) => {
            if (searchQuery.value) {
                const query = searchQuery.value.toLowerCase();
                if (
                    !tp.title.toLowerCase().includes(query) &&
                    !tp.summary?.toLowerCase().includes(query)
                ) {
                    return false;
                }
            }

            if (filters.value.status && tp.status !== filters.value.status) {
                return false;
            }

            if (
                filters.value.visibility &&
                tp.visibility !== filters.value.visibility
            ) {
                return false;
            }

            return true;
        });
    });

    const paginatedTalentPasses = computed(() => {
        const start = (currentPage.value - 1) * perPage.value;
        const end = start + perPage.value;
        return filteredTalentPasses.value.slice(start, end);
    });

    // ========================================================================
    // ACTIONS - CRUD
    // ========================================================================

    /**
     * Fetch all Talent Passes for current organization
     */
    const fetchTalentPasses = async (params?: PaginationParams) => {
        loading.value = true;
        error.value = null;

        try {
            const queryParams = new URLSearchParams();
            if (params?.page)
                queryParams.append('page', params.page.toString());
            if (params?.per_page)
                queryParams.append('per_page', params.per_page.toString());
            if (params?.sort_by) queryParams.append('sort_by', params.sort_by);
            if (params?.sort_order)
                queryParams.append('sort_order', params.sort_order);

            const response = await apiCall(
                `/api/talent-passes?${queryParams.toString()}`,
            );
            talentPasses.value = response.data || [];
            totalPages.value = response.meta?.last_page || 1;
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error fetching Talent Passes';
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch single Talent Pass by ID
     */
    const fetchTalentPass = async (id: number) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall(`/api/talent-passes/${id}`);
            currentTalentPass.value = response.data || null;
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error fetching Talent Pass';
        } finally {
            loading.value = false;
        }
    };

    /**
     * Fetch public Talent Pass by ULID (no auth required)
     */
    const fetchPublicTalentPass = async (ulid: string) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiCall(`/api/talent-pass/${ulid}`);
            currentTalentPass.value = response.data || null;
        } catch (err: any) {
            error.value =
                err.response?.data?.message ||
                'Error fetching public Talent Pass';
        } finally {
            loading.value = false;
        }
    };

    /**
     * Create new Talent Pass
     */
    const createTalentPass = async (payload: CreateTalentPassRequest) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiClient.post(
                '/api/talent-passes',
                payload,
            );
            talentPasses.value.unshift(response.data.data);
            currentTalentPass.value = response.data.data;
            isCreateModalOpen.value = false;
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error creating Talent Pass';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Update existing Talent Pass
     */
    const updateTalentPass = async (
        id: number,
        payload: UpdateTalentPassRequest,
    ) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiClient.put(
                `/api/talent-passes/${id}`,
                payload,
            );
            const index = talentPasses.value.findIndex((tp) => tp.id === id);
            if (index !== -1) {
                talentPasses.value[index] = response.data.data;
            }
            currentTalentPass.value = response.data.data;
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error updating Talent Pass';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Delete Talent Pass
     */
    const deleteTalentPass = async (id: number) => {
        loading.value = true;
        error.value = null;

        try {
            await apiCall(`/api/talent-passes/${id}`, {
                method: 'DELETE',
            });
            talentPasses.value = talentPasses.value.filter(
                (tp) => tp.id !== id,
            );
            if (currentTalentPass.value?.id === id) {
                currentTalentPass.value = null;
            }
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error deleting Talent Pass';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // ========================================================================
    // ACTIONS - ADVANCED OPERATIONS
    // ========================================================================

    /**
     * Publish Talent Pass (transition from draft to active)
     */
    const publishTalentPass = async (id: number, reason?: string) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiClient.post(
                `/api/talent-passes/${id}/publish`,
                { reason },
            );
            currentTalentPass.value = response.data.data;
            const index = talentPasses.value.findIndex((tp) => tp.id === id);
            if (index !== -1) {
                talentPasses.value[index] = response.data.data;
            }
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error publishing Talent Pass';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Archive Talent Pass
     */
    const archiveTalentPass = async (id: number, reason?: string) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiClient.post(
                `/api/talent-passes/${id}/archive`,
                { reason },
            );
            currentTalentPass.value = response.data.data;
            const index = talentPasses.value.findIndex((tp) => tp.id === id);
            if (index !== -1) {
                talentPasses.value[index] = response.data.data;
            }
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error archiving Talent Pass';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Clone Talent Pass
     */
    const cloneTalentPass = async (id: number, title: string) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiClient.post(
                `/api/talent-passes/${id}/clone`,
                { title },
            );
            talentPasses.value.unshift(response.data.data);
            currentTalentPass.value = response.data.data;
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error cloning Talent Pass';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Export Talent Pass
     */
    const exportTalentPass = async (
        id: number,
        format: 'pdf' | 'json' | 'linkedin',
    ) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiClient.post(
                `/api/talent-passes/${id}/export`,
                { format },
            );
            return response.data.data; // Returns { url, format, expires_at }
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error exporting Talent Pass';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Share Talent Pass (generate public URL)
     */
    const shareTalentPass = async (id: number, message?: string) => {
        loading.value = true;
        error.value = null;

        try {
            const response = await apiClient.post(
                `/api/talent-passes/${id}/share`,
                { message },
            );
            return response.data.data; // Returns { public_url, public_id, expires_at }
        } catch (err: any) {
            error.value =
                err.response?.data?.message || 'Error sharing Talent Pass';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    // ========================================================================
    // ACTIONS - SKILLS MANAGEMENT
    // ========================================================================

    const addSkill = async (talentPassId: number, skill: any) => {
        try {
            const response = await apiClient.post(
                `/api/talent-passes/${talentPassId}/skills`,
                skill,
            );
            if (currentTalentPass.value?.id === talentPassId) {
                if (!currentTalentPass.value.skills) {
                    currentTalentPass.value.skills = [];
                }
                currentTalentPass.value.skills.push(response.data.data);
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Error adding skill';
            throw err;
        }
    };

    const removeSkill = async (talentPassId: number, skillId: number) => {
        try {
            await apiClient.delete(
                `/api/talent-passes/${talentPassId}/skills/${skillId}`,
            );
            if (currentTalentPass.value?.skills) {
                currentTalentPass.value.skills =
                    currentTalentPass.value.skills.filter(
                        (s) => s.id !== skillId,
                    );
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || 'Error removing skill';
            throw err;
        }
    };

    // ========================================================================
    // ACTIONS - SEARCH & FILTERING
    // ========================================================================

    const setSearchQuery = (query: string) => {
        searchQuery.value = query;
        currentPage.value = 1;
    };

    const setFilters = (newFilters: Partial<SearchFilters>) => {
        filters.value = { ...filters.value, ...newFilters };
        currentPage.value = 1;
    };

    const clearFilters = () => {
        filters.value = {};
        searchQuery.value = '';
        currentPage.value = 1;
    };

    // ========================================================================
    // ACTIONS - UI STATE
    // ========================================================================

    const openCreateModal = () => {
        isCreateModalOpen.value = true;
    };

    const closeCreateModal = () => {
        isCreateModalOpen.value = false;
    };

    const openShareDialog = () => {
        isShareDialogOpen.value = true;
    };

    const closeShareDialog = () => {
        isShareDialogOpen.value = false;
    };

    const toggleExportMenu = () => {
        isExportMenuOpen.value = !isExportMenuOpen.value;
    };

    // ========================================================================
    // RETURN PUBLIC API
    // ========================================================================

    return {
        // State
        talentPasses,
        currentTalentPass,
        loading,
        error,
        searchQuery,
        filters,
        currentPage,
        perPage,
        totalPages,
        isCreateModalOpen,
        isShareDialogOpen,
        isExportMenuOpen,

        // Computed
        currentCompleteness,
        isDraft,
        isPublished,
        filteredTalentPasses,
        paginatedTalentPasses,

        // CRUD Actions
        fetchTalentPasses,
        fetchTalentPass,
        fetchPublicTalentPass,
        createTalentPass,
        updateTalentPass,
        deleteTalentPass,

        // Advanced Actions
        publishTalentPass,
        archiveTalentPass,
        cloneTalentPass,
        exportTalentPass,
        shareTalentPass,

        // Skills
        addSkill,
        removeSkill,

        // Search & Filtering
        setSearchQuery,
        setFilters,
        clearFilters,

        // UI State
        openCreateModal,
        closeCreateModal,
        openShareDialog,
        closeShareDialog,
        toggleExportMenu,
    };
});
