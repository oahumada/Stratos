<template>
  <div class="role-competency-matrix">
    <!-- Header -->
    <div class="matrix-header mb-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold">
            {{ store.scenarioName || 'Escenario' }}
          </h2>
          <p class="text-gray-600 mt-1">
            Mapeo: Roles ↔ Competencias (Horizonte: {{ store.horizonMonths }}
            meses)
          </p>
        </div>
        <div class="flex gap-2">
          <v-btn
            color="primary"
            prepend-icon="mdi-plus"
            @click="showAddRoleDialog = true"
          >
            + Nuevo Rol
          </v-btn>
        </div>
      </div>
    </div>

    <!-- Alerts -->
    <div v-if="store.error" class="mb-4">
      <v-alert type="error" closable @click:close="store.clearMessages()">
        {{ store.error }}
      </v-alert>
    </div>
    <div v-if="store.success" class="mb-4">
      <v-alert type="success" closable @click:close="store.clearMessages()">
        {{ store.success }}
      </v-alert>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="flex justify-center py-8">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>

    <!-- Matrix Table -->
    <div v-else class="matrix-container overflow-x-auto">
      <table class="matrix-table">
        <thead>
          <tr>
            <!-- Rol header -->
            <th class="sticky left-0 z-20 bg-gray-100 border-b border-gray-300">
              <div class="w-48 p-3">
                <strong>Rol</strong>
              </div>
            </th>

            <!-- Competency headers -->
            <th
              v-for="comp in store.competencyColumns"
              :key="comp.id"
              class="border-b border-gray-300 bg-gray-50 sticky top-0"
            >
              <div class="w-32 p-3 text-center">
                <div class="font-semibold text-sm">{{ comp.name }}</div>
                <div class="text-xs text-gray-500">{{ comp.category }}</div>
              </div>
            </th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="row in store.matrixRows" :key="row.roleId">
            <!-- Role cell (sticky) -->
            <td class="sticky left-0 z-10 bg-white border-b border-gray-200">
              <div class="w-48 p-3 border-r border-gray-200">
                <div class="font-semibold">{{ row.roleName }}</div>
                <div class="text-xs text-gray-500">
                  {{ row.fte }} FTE • {{ row.status }}
                </div>
              </div>
            </td>

            <!-- Competency cells -->
            <td
              v-for="comp in store.competencyColumns"
              :key="`${row.roleId}-${comp.id}`"
              class="border-b border-gray-200 p-0"
            >
              <div
                class="w-32 h-24 flex items-center justify-center cursor-pointer hover:bg-gray-50 border-r border-gray-200 transition"
                @click="openEditModal(row.roleId, comp.id)"
              >
                <CellContent
                  :mapping="row.mappings.get(comp.id)"
                  :role-id="row.roleId"
                  :role-name="row.roleName"
                  :competency-id="comp.id"
                  :competency-name="comp.name"
                  @edit="openEditModal(row.roleId, comp.id)"
                  @remove="removeMapping(row.roleId, comp.id)"
                />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Edit Modal -->
    <RoleCompetencyStateModal
      v-if="selectedMapping"
      :visible="showEditModal"
      :role-id="selectedMapping.roleId"
      :role-name="selectedMapping.roleName"
      :competency-id="selectedMapping.competencyId"
      :competency-name="selectedMapping.competencyName"
      :mapping="selectedMapping.mapping"
      @save="saveMapping"
      @close="showEditModal = false"
    />

    <!-- Add Role Dialog -->
    <AddRoleDialog
      :visible="showAddRoleDialog"
      @save="handleAddRole"
      @close="showAddRoleDialog = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';
import RoleCompetencyStateModal from './RoleCompetencyStateModal.vue';
import AddRoleDialog from './AddRoleDialog.vue';
import CellContent from './CellContent.vue';

const page = usePage();
const store = useRoleCompetencyStore();

const showEditModal = ref(false);
const showAddRoleDialog = ref(false);
const selectedMapping = ref<{
  roleId: number;
  roleName: string;
  competencyId: number;
  competencyName: string;
  mapping: any;
} | null>(null);

onMounted(async () => {
  const scenarioId = Number(page.props.scenarioId);
  await store.loadScenarioData(scenarioId);
});

const openEditModal = (roleId: number, competencyId: number) => {
  const role = store.roles.find((r) => r.role_id === roleId);
  const competency = store.competencies.find((c) => c.id === competencyId);
  const mapping = store.getMapping(roleId, competencyId);

  selectedMapping.value = {
    roleId,
    roleName: role?.role_name || '',
    competencyId,
    competencyName: competency?.name || '',
    mapping,
  };

  showEditModal.value = true;
};

const saveMapping = async (mappingData: any) => {
  if (!selectedMapping.value) return;

  const newMapping = {
    id: mappingData.id,
    scenario_id: store.scenarioId!,
    role_id: selectedMapping.value.roleId,
    competency_id: selectedMapping.value.competencyId,
    competency_name: selectedMapping.value.competencyName,
    required_level: mappingData.required_level,
    is_core: mappingData.is_core,
    change_type: mappingData.change_type,
    rationale: mappingData.rationale,
  };

  await store.saveMapping(newMapping);
  showEditModal.value = false;
};

const removeMapping = async (roleId: number, competencyId: number) => {
  const mapping = store.getMapping(roleId, competencyId);
  if (mapping && confirm('¿Eliminar esta asociación?')) {
    await store.removeMapping(roleId, competencyId, mapping.id);
  }
};

const handleAddRole = async (roleData: any) => {
  await store.addNewRole(roleData);
  showAddRoleDialog.value = false;
};
</script>

<style scoped>
.matrix-container {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background: white;
}

.matrix-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

.matrix-table th {
  padding: 0;
  text-align: center;
  vertical-align: middle;
}

.matrix-table td {
  padding: 0;
}

.matrix-header {
  padding: 1.5rem;
  background: white;
}
</style>
