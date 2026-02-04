import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

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
}

export interface RoleCompetencyMapping {
  id: number;
  scenario_id: number;
  role_id: number;
  competency_id: number;
  competency_name: string;
  required_level: number;
  is_core: boolean;
  change_type: 'maintenance' | 'transformation' | 'enrichment' | 'extinction';
  rationale: string;
}

export interface RowData {
  roleId: number;
  roleName: string;
  fte?: number;
  status: string;
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
      roleId: role.role_id,
      roleName: role.role_name,
      fte: role.fte || 0,
      status: role.role_change,
      mappings: new Map(
        mappings.value
          .filter((m) => m.role_id === role.role_id)
          .map((m) => [m.competency_id, m])
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
            change_type: mapping.change_type,
            rationale: mapping.rationale,
          }),
        }
      );
      
      if (!response.ok) throw new Error('Error saving mapping');
      const res = await response.json();

      // Update local state
      const idx = mappings.value.findIndex(
        (m) =>
          m.role_id === mapping.role_id &&
          m.competency_id === mapping.competency_id
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
    mappingId: number
  ) => {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(
        `/api/scenarios/${scenarioId.value}/step2/mappings/${mappingId}`,
        { method: 'DELETE' }
      );
      
      if (!response.ok) throw new Error('Error removing mapping');

      mappings.value = mappings.value.filter(
        (m) => !(m.role_id === roleId && m.competency_id === competencyId)
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
        }
      );
      
      if (!response.ok) throw new Error('Error adding role');
      const res = await response.json();

      roles.value.push(res.role);

      success.value = res.message || 'Role added successfully';
      setTimeout(() => (success.value = null), 3000);
    } catch (err: any) {
      error.value = err.message || 'Error adding role';
    } finally {
      loading.value = false;
    }
  };

  const getMapping = (
    roleId: number,
    competencyId: number
  ): RoleCompetencyMapping | undefined => {
    return mappings.value.find(
      (m) =>
        m.role_id === roleId &&
        m.competency_id === competencyId &&
        m.scenario_id === scenarioId.value
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
    getMapping,
    clearMessages,
  };
});
