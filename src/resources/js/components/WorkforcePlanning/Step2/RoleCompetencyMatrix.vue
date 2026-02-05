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
    <v-snackbar v-model="showSuccess" color="success" timeout="2000">
      {{ store.success }}
    </v-snackbar>

    <!-- Loading -->
    <div v-if="store.loading" class="flex justify-center py-8">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>

    <div v-else>
    <!-- Instructions -->
    <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
      <div class="flex items-start gap-3">
        <v-icon icon="mdi-information" class="text-blue-600 mt-1" />
        <div>
          <h3 class="font-semibold text-blue-800 mb-1">Cómo asignar competencias</h3>
          <p class="text-sm text-blue-700">
            Haz click en cualquier celda para asignar una competencia a un rol.
            Las celdas vacías muestran un ícono + para indicar que se pueden asignar.
          </p>
          <p class="text-sm text-blue-700 mt-2">
            Paso 2 consiste en conectar competencias con roles y definir su transición
            (mantención, transformación, enriquecimiento o extinción) de forma simple y visual.
          </p>
        </div>
      </div>
    </div>

    <!-- Tabs por categoría -->
    <v-card>
      <v-tabs
        v-model="activeTab"
        bg-color="gray-100"
        class="border-b"
      >
        <v-tab 
          v-for="(cat, idx) in categories" 
          :key="cat.name" 
          :value="idx"
          :class="activeTab === idx ? 'bg-blue-50' : ''"
        >
          <v-tooltip :text="cat.name" location="top">
            <template #activator="{ props: tooltipProps }">
              <span v-bind="tooltipProps" class="tab-label text-truncate">
                {{ cat.name }}
              </span>
            </template>
          </v-tooltip>
          <v-badge
            :content="countMappedInCategory(cat.name)"
            color="primary"
            location="top-end"
            offset-x="-8"
            offset-y="-8"
          />
        </v-tab>
      </v-tabs>

      <!-- Tab Content -->
      <v-window v-model="activeTab" class="matrix-container">
        <v-window-item v-for="(cat, idx) in categories" :key="cat.name" :value="idx">
          <table class="matrix-table">
            <thead>
              <tr>
                <th class="sticky left-0 z-20 bg-gray-100 border-b border-gray-300 role-column-header">
                  <div class="w-48 p-3 flex items-center justify-between group">
                    <strong>Rol</strong>
                    <div class="cursor-col-resize opacity-0 group-hover:opacity-100 transition-opacity">⋮⋮</div>
                  </div>
                </th>
                <th v-for="comp in cat.comps" :key="comp.id" class="border-b border-gray-300 bg-gray-50">
                  <div class="w-32 p-3 text-center">
                    <v-tooltip :text="comp.name" location="top">
                      <template #activator="{ props: tooltipProps }">
                        <div v-bind="tooltipProps" class="font-semibold text-sm text-truncate comp-name">
                          {{ comp.name }}
                        </div>
                      </template>
                    </v-tooltip>
                    <v-tooltip :text="comp.capability_name || comp.category" location="top">
                      <template #activator="{ props: tooltipProps }">
                        <div v-bind="tooltipProps" class="text-xs text-gray-500 text-truncate comp-capability">
                          {{ comp.capability_name || comp.category }}
                        </div>
                      </template>
                    </v-tooltip>
                  </div>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in store.matrixRows" :key="row.roleId">
                <td class="sticky left-0 z-10 bg-white border-b border-gray-200">
                  <div class="w-48 p-3 border-r border-gray-200">
                    <div class="font-semibold">{{ row.roleName }}</div>
                    <div class="text-xs text-gray-500">
                      {{ row.fte }} FTE • {{ row.status }}
                    </div>
                  </div>
                </td>
                <td v-for="comp in cat.comps" :key="`${row.roleId}-${comp.id}`" class="border-b border-gray-200 p-0">
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
        </v-window-item>
      </v-window>
    </v-card>
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
import { ref, onMounted, computed, watch } from 'vue';
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';
import RoleCompetencyStateModal from './RoleCompetencyStateModal.vue';
import AddRoleDialog from './AddRoleDialog.vue';
import CellContent from './CellContent.vue';

interface Props {
  scenarioId: number;
}

const props = defineProps<Props>();
const store = useRoleCompetencyStore();

const showEditModal = ref(false);
const showAddRoleDialog = ref(false);
const activeTab = ref(0);
const selectedMapping = ref<{
  roleId: number;
  roleName: string;
  competencyId: number;
  competencyName: string;
  mapping: any;
} | null>(null);
const showSuccess = ref(false);

const categories = computed(() => {
  const map: Record<string, any[]> = {};
  store.competencyColumns.forEach((c: any) => {
    const cap = c.capability_name || c.category || 'General';
    if (!map[cap]) map[cap] = [];
    map[cap].push(c);
  });
  return Object.keys(map).map((k) => ({ name: k, comps: map[k], count: map[k].length }));
});

const countMappedInCategory = (categoryName: string) => {
  let total = 0;
  store.matrixRows.forEach((row: any) => {
    const comps = categories.value.find((c: any) => c.name === categoryName)?.comps || [];
    comps.forEach((comp: any) => {
      if (row.mappings && row.mappings.get && row.mappings.get(comp.id)) {
        total += 1;
      }
    });
  });
  return total;
};

onMounted(async () => {
  await store.loadScenarioData(props.scenarioId);
});

watch(
  () => store.success,
  (val) => {
    if (val) showSuccess.value = true;
  }
);

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
  overflow-x: auto;
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

.matrix-table .w-32 { min-width: 160px; }

.text-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.tab-label {
  max-width: 140px;
  display: inline-block;
}

.comp-name {
  max-width: 140px;
}

.comp-capability {
  max-width: 140px;
}

.role-column-header {
  resize: horizontal;
  overflow: hidden;
}

.cursor-col-resize {
  cursor: col-resize;
}

:deep(.v-tab.bg-blue-50) {
  background-color: rgba(59, 130, 246, 0.1) !important;
}
</style>
