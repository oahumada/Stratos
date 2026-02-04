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
        </div>
      </div>
    </div>

    <div class="matrix-container overflow-x-auto">
      <table class="matrix-table">
        <thead>
          <tr>
            <!-- Rol header -->
            <th class="sticky left-0 z-20 bg-gray-100 border-b border-gray-300">
              <div class="w-48 p-3">
                <strong>Rol</strong>
              </div>
            </th>

            <!-- Group headers (por categoría/capacidad) -->
            <th v-for="cat in categories" :key="cat.name" :colspan="collapsedCategories[cat.name] ? 1 : cat.count" class="border-b border-gray-300 bg-gray-50 sticky top-0 z-30">
              <div class="p-2 flex items-center justify-between gap-2 w-full">
                <span class="font-semibold text-gray-800">{{ cat.name }}</span>
                <v-btn icon dense small class="min-w-0 p-0" @click.stop="toggleCategory(cat.name)" :title="collapsedCategories[cat.name] ? 'Expandir' : 'Colapsar'">
                  <v-icon :icon="collapsedCategories[cat.name] ? 'mdi-chevron-down' : 'mdi-chevron-up'" />
                </v-btn>
              </div>
            </th>
          </tr>
          <!-- Second header row: competency names (only when group expanded) -->
          <tr v-if="true">
            <th class="sticky left-0 bg-gray-100" style="z-index:25; top:48px;"></th>
            <template v-for="cat in categories" :key="`sub-${cat.name}`">
              <th v-for="comp in compsForRender(cat)" v-if="!collapsedCategories[cat.name]" :key="comp.id" class="border-b border-gray-300 bg-gray-50" style="position:sticky; top:48px; z-index:20;">
                <div class="w-32 p-3 text-center">
                  <div class="font-semibold text-sm">{{ comp.name }}</div>
                  <div class="text-xs text-gray-500">{{ comp.capability_name || comp.category }}</div>
                </div>
              </th>
              <th v-if="!collapsedCategories[cat.name] && cat.count > VISIBLE_LIMIT" class="border-b border-gray-300 bg-gray-50" style="position:sticky; top:48px; z-index:20;">
                <div class="w-32 p-3 text-center">
                  <v-btn text small @click.stop="expandedCategories[cat.name] = !expandedCategories[cat.name]">
                    {{ expandedCategories[cat.name] ? 'Mostrar menos' : `+${cat.count - VISIBLE_LIMIT} más` }}
                  </v-btn>
                </div>
              </th>
            </template>
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

            <!-- Competency cells grouped by category; collapsed groups render a summary cell -->
            <template v-for="cat in categories" :key="`group-${row.roleId}-${cat.name}`">
              <td v-if="collapsedCategories[cat.name]" :key="`summary-${row.roleId}-${cat.name}`" class="border-b border-gray-200 p-0">
                <transition name="collapse-fade">
                <div class="w-32 h-24 flex flex-col items-center justify-center border-r border-gray-200 p-2">
                  <div class="text-sm font-medium">{{ countMappedInCategory(row, cat.name) }} / {{ cat.count }}</div>
                  <div class="flex gap-1 mt-1">
                    <span v-for="(v,k) in summaryForCategory(row, cat.name).counts" :key="k" v-if="v>0" class="text-xs px-1 py-0.5 rounded bg-gray-100">{{ k.charAt(0).toUpperCase() }}:{{ v }}</span>
                  </div>
                  <div class="text-xs text-gray-500 mt-1" v-if="summaryForCategory(row, cat.name).avgLevel">
                    Avg Lvl: {{ summaryForCategory(row, cat.name).avgLevel }}
                  </div>
                </div>
                </transition>
              </td>
              <template v-else>
                <td v-for="comp in compsForRender(cat)" :key="`${row.roleId}-${comp.id}`" class="border-b border-gray-200 p-0">
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
              </template>
            </template>
          </tr>
        </tbody>
      </table>
    </div>
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
const selectedMapping = ref<{
  roleId: number;
  roleName: string;
  competencyId: number;
  competencyName: string;
  mapping: any;
} | null>(null);

// Group competencies by category (capacidad) and support collapsing groups
const collapsedCategories = ref<Record<string, boolean>>({});

// Persist collapsed/expanded state per scenario
const storageKeyCollapsed = `rcm_collapsed_${props.scenarioId}`;
const storageKeyExpanded = `rcm_expanded_${props.scenarioId}`;
try {
  const raw = localStorage.getItem(storageKeyCollapsed);
  if (raw) collapsedCategories.value = JSON.parse(raw);
} catch (e) {
  // ignore
}
try {
  const raw2 = localStorage.getItem(storageKeyExpanded);
  if (raw2) expandedCategories.value = JSON.parse(raw2);
} catch (e) {
  // ignore
}

// Support 'show more' for large categories
const expandedCategories = ref<Record<string, boolean>>({});

const categories = computed(() => {
  const map: Record<string, any[]> = {};
  store.competencyColumns.forEach((c: any) => {
    const cap = c.capability_name || c.category || 'General';
    if (!map[cap]) map[cap] = [];
    map[cap].push(c);
  });
  return Object.keys(map).map((k) => ({ name: k, comps: map[k], count: map[k].length }));
});

const countMappedInCategory = (row: any, categoryName: string) => {
  const comps = categories.value.find((c: any) => c.name === categoryName)?.comps || [];
  let cnt = 0;
  comps.forEach((comp: any) => {
    if (row.mappings && row.mappings.get && row.mappings.get(comp.id)) cnt += 1;
  });
  return cnt;
};

// Toggle a category open exclusively: when opening a category, collapse others
const toggleCategory = (name: string) => {
  const isCollapsed = !!collapsedCategories.value[name];
  // If currently collapsed, expand this and collapse others
  if (isCollapsed) {
    categories.value.forEach((c: any) => {
      collapsedCategories.value[c.name] = c.name !== name;
    });
  } else {
    // If currently expanded, collapse it (so all collapsed)
    collapsedCategories.value[name] = true;
  }
};

const summaryForCategory = (row: any, categoryName: string) => {
  const comps = categories.value.find((c: any) => c.name === categoryName)?.comps || [];
  const summary: Record<string, number> = { maintenance: 0, transformation: 0, enrichment: 0, extinction: 0 };
  let levelSum = 0;
  let levelCount = 0;
  comps.forEach((comp: any) => {
    const m = row.mappings && row.mappings.get ? row.mappings.get(comp.id) : null;
    if (m) {
      const t = m.change_type || 'maintenance';
      if (summary[t] !== undefined) summary[t] += 1;
      if (m.required_level) { levelSum += m.required_level; levelCount += 1; }
    }
  });
  const avg = levelCount ? Math.round((levelSum / levelCount) * 10) / 10 : null;
  return { counts: summary, avgLevel: avg };
};

// Save collapsed state when it changes
watch(collapsedCategories, (val) => {
  try { localStorage.setItem(storageKeyCollapsed, JSON.stringify(val)); } catch (e) { /* ignore */ }
}, { deep: true });

// Persist expanded categories as well
watch(expandedCategories, (val) => {
  try { localStorage.setItem(storageKeyExpanded, JSON.stringify(val)); } catch (e) { /* ignore */ }
}, { deep: true });

// When categories are loaded, if there's no persisted collapsed state, default to collapse all but first
watch(categories, (cats) => {
  if (!cats || !cats.length) return;
  const hasPersisted = Object.keys(collapsedCategories.value || {}).length > 0;
  if (!hasPersisted) {
    const initial: Record<string, boolean> = {};
    cats.forEach((c: any, idx: number) => { initial[c.name] = idx !== 0; });
    collapsedCategories.value = initial;
  }
}, { immediate: true });

// Helper for show-more (simpler virtualization)
const VISIBLE_LIMIT = 10;
const compsForRender = (cat: any) => {
  if (expandedCategories.value[cat.name]) return cat.comps;
  return cat.comps.slice(0, VISIBLE_LIMIT);
};

onMounted(async () => {
  await store.loadScenarioData(props.scenarioId);
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

/* Collapse animation */
.collapse-fade-enter-active, .collapse-fade-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.collapse-fade-enter-from, .collapse-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
.collapse-fade-enter-to, .collapse-fade-leave-from {
  opacity: 1;
  transform: translateY(0);
}

.matrix-table .w-32 { min-width: 160px; }
</style>
