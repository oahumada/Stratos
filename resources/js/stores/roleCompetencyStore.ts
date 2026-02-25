import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export interface ScenarioRole {
    id: number;
    scenario_id: number;
    role_id: number;
    role_name: string;
    role_change: string;
    impact_level: string;
    evolution_type: string;
    rationale: string;
    fte?: number;
    human_leverage?: number;
    archetype?: string;
}

export interface RoleCompetencyMapping {
    id: number;
    scenario_id: number;
    role_id: number;
    competency_id: number;
    competency_name: string;
    required_level: number;
    is_core: boolean;
    is_referent?: boolean;
    change_type: 'maintenance' | 'transformation' | 'enrichment' | 'extinction';
    rationale: string;
    competency_version_id?: number | null;
    source?: 'agent' | 'manual' | 'auto';
}

export interface RowData {
    roleId: number;
    roleName: string;
    fte?: number;
    status: string;
    human_leverage?: number;
    archetype?: string;
    mappings: Map<number, RoleCompetencyMapping>;
}

export const useRoleCompetencyStore = defineStore('roleCompetency', () => {
    // State
    const scenarioId = ref<number | null>(null);
    const scenarioName = ref<string>('');
    const horizonMonths = ref<number>(12);

    const roles = ref<ScenarioRole[]>([]);
    const competencies = ref<any[]>([]);
    const mappings = ref<RoleCompetencyMapping[]>([]);

    const loading = ref(false);
    const error = ref<string | null>(null);
    const success = ref<string | null>(null);

    // Computed
    const matrixRows = computed(() => {
        return roles.value.map((role) => ({
            roleId: role.id, // ID del pivote (scenario_roles.id)
            roleName: role.role_name,
            fte: role.fte || 0,
            status: role.role_change,
            human_leverage: role.human_leverage,
            archetype: role.archetype,
            mappings: new Map(
                mappings.value
                    .filter(
                        (m) =>
                            m.role_id === role.id &&
                            m.scenario_id === scenarioId.value,
                    )
                    .map((m) => [m.competency_id, m]),
            ),
        }));
    });

    const competencyColumns = computed(() => {
        return competencies.value.map((c) => ({
            id: c.id,
            name: c.name,
            // API now returns capability association via pivot: capability_id + capability_name
            capability_id: c.capability_id ?? null,
            capability_name: c.capability_name ?? c.category ?? 'General',
            category: c.category ?? null,
        }));
    });

    // Actions
    const loadScenarioData = async (id: number) => {
        loading.value = true;
        error.value = null;
        try {
            // Cargar todos los datos del escenario para Step2
            const response = await fetch(`/api/scenarios/${id}/step2/data`);
            if (!response.ok) throw new Error('Error loading scenario data');

            const res = await response.json();

            scenarioId.value = id;
            scenarioName.value = res.scenario.name;
            horizonMonths.value = res.scenario.horizon_months || 12;

            roles.value = res.roles;
            competencies.value = res.competencies;
            mappings.value = res.mappings;
        } catch (err: any) {
            error.value = err.message || 'Error loading scenario data';
        } finally {
            loading.value = false;
        }
    };

    const saveMapping = async (mapping: RoleCompetencyMapping) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await fetch(
                `/api/scenarios/${scenarioId.value}/step2/mappings`,
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: mapping.id,
                        role_id: mapping.role_id,
                        competency_id: mapping.competency_id,
                        required_level: mapping.required_level,
                        is_core: mapping.is_core,
                        is_referent: mapping.is_referent ?? false,
                        change_type: mapping.change_type,
                        rationale: mapping.rationale,
                        competency_version_id:
                            (mapping as any).competency_version_id ?? null,
                    }),
                },
            );

            if (!response.ok) throw new Error('Error saving mapping');
            const res = await response.json();

            // Update local state
            const idx = mappings.value.findIndex(
                (m) =>
                    m.role_id === mapping.role_id &&
                    m.competency_id === mapping.competency_id,
            );

            if (idx >= 0) {
                mappings.value[idx] = res.mapping;
            } else {
                mappings.value.push(res.mapping);
            }

            success.value = res.message || 'Mapping saved successfully';
            setTimeout(() => (success.value = null), 3000);
        } catch (err: any) {
            error.value = err.message || 'Error saving mapping';
        } finally {
            loading.value = false;
        }
    };

    const removeMapping = async (
        roleId: number,
        competencyId: number,
        mappingId: number,
    ) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await fetch(
                `/api/scenarios/${scenarioId.value}/step2/mappings/${mappingId}`,
                { method: 'DELETE' },
            );

            if (!response.ok) throw new Error('Error removing mapping');

            mappings.value = mappings.value.filter(
                (m) =>
                    !(m.role_id === roleId && m.competency_id === competencyId),
            );

            success.value = 'Mapping removed successfully';
            setTimeout(() => (success.value = null), 3000);
        } catch (err: any) {
            error.value = err.message || 'Error removing mapping';
        } finally {
            loading.value = false;
        }
    };

    const addNewRole = async (roleData: any) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await fetch(
                `/api/scenarios/${scenarioId.value}/step2/roles`,
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(roleData),
                },
            );

            if (!response.ok) throw new Error('Error adding role');
            const res = await response.json();

            // Verificar si el rol ya existe en el array
            const existingRoleIndex = roles.value.findIndex(
                (r) => r.role_id === res.role.role_id,
            );

            if (existingRoleIndex !== -1) {
                // Actualizar el rol existente
                roles.value[existingRoleIndex] = res.role;
            } else {
                // Añadir nuevo rol
                roles.value.push(res.role);
            }

            success.value = res.message || 'Role added successfully';
            setTimeout(() => (success.value = null), 3000);
        } catch (err: any) {
            error.value = err.message || 'Error adding role';
        } finally {
            loading.value = false;
        }
    };

    const designTalent = async () => {
        if (!scenarioId.value) return;
        loading.value = true;
        error.value = null;
        try {
            const response = await fetch(
                `/api/scenarios/${scenarioId.value}/step2/design-talent`,
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                },
            );

            const res = await response.json();

            if (!response.ok) {
                throw new Error(
                    res.message ?? 'Error en la orquestación de talento',
                );
            }

            return res.proposals;
        } catch (err: unknown) {
            error.value =
                err instanceof Error ? err.message : 'Error en la orquestación';
            return null;
        } finally {
            loading.value = false;
        }
    };

    /**
     * FASE 2: Aplica en batch las propuestas aprobadas por el usuario.
     * Llama a POST /api/scenarios/{id}/step2/agent-proposals/apply
     */
    const applyAgentProposals = async (
        approvedRoleProposals: unknown[],
        approvedCatalogProposals: unknown[],
    ): Promise<boolean> => {
        if (!scenarioId.value) return false;
        loading.value = true;
        error.value = null;
        try {
            const response = await fetch(
                `/api/scenarios/${scenarioId.value}/step2/agent-proposals/apply`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-XSRF-TOKEN': decodeURIComponent(
                            document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ??
                                '',
                        ),
                    },
                    body: JSON.stringify({
                        approved_role_proposals: approvedRoleProposals,
                        approved_catalog_proposals: approvedCatalogProposals,
                    }),
                },
            );

            if (!response.ok) {
                const err = await response.json();
                throw new Error(err.message ?? 'Error al aplicar propuestas');
            }

            const res = await response.json();
            success.value = `Propuestas aplicadas: ${res.stats?.roles_created ?? 0} roles, ${res.stats?.mappings_created ?? 0} competencias.`;
            setTimeout(() => (success.value = null), 5000);

            // Recargar la matriz con los datos actualizados
            await loadScenarioData(scenarioId.value);
            return true;
        } catch (err: unknown) {
            error.value =
                err instanceof Error
                    ? err.message
                    : 'Error al aplicar propuestas';
            return false;
        } finally {
            loading.value = false;
        }
    };

    /**
     * FASE 4: Aprobación final — mueve roles y competencias a incubación.
     * Llama a POST /api/scenarios/{id}/step2/finalize
     */
    const finalizeStep2 = async (): Promise<{
        success: boolean;
        message?: string;
    }> => {
        if (!scenarioId.value) return { success: false };
        loading.value = true;
        error.value = null;
        try {
            const response = await fetch(
                `/api/scenarios/${scenarioId.value}/step2/finalize`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-XSRF-TOKEN': decodeURIComponent(
                            document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ??
                                '',
                        ),
                    },
                },
            );

            const res = await response.json();

            if (!response.ok) {
                throw new Error(res.message ?? 'Error al finalizar el Paso 2');
            }

            success.value = res.message ?? 'Paso 2 finalizado correctamente.';
            return { success: true, message: res.message };
        } catch (err: unknown) {
            const message =
                err instanceof Error ? err.message : 'Error al finalizar';
            error.value = message;
            return { success: false, message };
        } finally {
            loading.value = false;
        }
    };

    const getMapping = (
        roleId: number,
        competencyId: number,
    ): RoleCompetencyMapping | undefined => {
        return mappings.value.find(
            (m) =>
                m.role_id === roleId &&
                m.competency_id === competencyId &&
                m.scenario_id === scenarioId.value,
        );
    };

    const clearMessages = () => {
        error.value = null;
        success.value = null;
    };

    return {
        // State
        scenarioId,
        scenarioName,
        horizonMonths,
        roles,
        competencies,
        mappings,
        loading,
        error,
        success,

        // Computed
        matrixRows,
        competencyColumns,

        // Actions
        loadScenarioData,
        saveMapping,
        removeMapping,
        addNewRole,
        designTalent,
        applyAgentProposals,
        finalizeStep2,
        getMapping,
        clearMessages,
    };
});
