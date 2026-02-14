<template>
  <div class="skill-gaps-matrix-container">
    <div class="mb-6">
      <h3 class="text-h5 font-semibold mb-4">Matriz de Brechas de Skills</h3>
      <p class="text-sm text-gray-600 mb-4">
        Análisis visual de las diferencias entre skills actuales y requeridos
      </p>

      <!-- Filters -->
      <div class="flex gap-4 mb-4 flex-wrap">
        <v-select
          v-model="filterBy"
          :items="['all', 'role', 'competency']"
          label="Filtrar por:"
          density="compact"
          style="width: 150px"
        />
        <v-text-field
          v-model="searchQuery"
          placeholder="Buscar skill..."
          density="compact"
          style="width: 250px"
        />
      </div>
    </div>

    <!-- Alerts -->
    <v-alert v-if="error" type="error" closable @click:close="error = null" class="mb-4">
      {{ error }}
    </v-alert>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-8">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <!-- Heat Map Table -->
    <div v-else class="overflow-x-auto">
      <table class="w-full border-collapse bg-white">
        <thead>
          <tr class="bg-gray-100 sticky top-0 z-10">
            <th class="border px-4 py-3 text-left font-semibold sticky left-0 bg-gray-100 z-20">
              Skill / Rol
            </th>
            <th
              v-for="role in roles"
              :key="role.id"
              class="border px-3 py-3 text-center font-semibold min-w-120"
            >
              <div class="text-sm">{{ role.name }}</div>
              <div class="text-xs text-gray-500">{{ role.fte }} FTE</div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="skill in filteredSkills" :key="skill.id" class="hover:bg-gray-50">
            <!-- Skill Name -->
            <td class="border px-4 py-3 font-medium sticky left-0 bg-white z-10 w-250">
              <div class="font-semibold">{{ skill.name }}</div>
              <div class="text-xs text-gray-500">{{ skill.competency_name }}</div>
            </td>

            <!-- Gap Cells -->
            <td
              v-for="role in roles"
              :key="`${skill.id}-${role.id}`"
              class="border px-3 py-3 text-center"
            >
              <div class="flex flex-col items-center gap-1">
                <!-- Heat Map Cell -->
                <div
                  :style="{
                    backgroundColor: getGapColor(skill.id, role.id),
                    width: '60px',
                    height: '60px',
                    borderRadius: '4px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    cursor: 'pointer',
                  }"
                  class="hover:shadow-md transition-shadow"
                  @click="showGapDetail(skill, role)"
                >
                  <span class="text-white font-bold text-sm">
                    {{ getGapValue(skill.id, role.id) }}
                  </span>
                </div>

                <!-- Levels Info -->
                <div class="text-xs text-gray-600 mt-1">
                  <div>{{ getCurrentLevel(skill.id, role.id) }} → {{ getRequiredLevel(skill.id, role.id) }}</div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Legend -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
      <p class="text-sm font-semibold mb-3">Leyenda de Brechas:</p>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded" style="background-color: #4caf50;"></div>
          <span class="text-sm">Sin Brecha (0)</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded" style="background-color: #81c784;"></div>
          <span class="text-sm">Brecha Leve (1)</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded" style="background-color: #ff9800;"></div>
          <span class="text-sm">Brecha Media (2)</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded" style="background-color: #f44336;"></div>
          <span class="text-sm">Brecha Alta (3+)</span>
        </div>
      </div>
    </div>

    <!-- Detail Dialog -->
    <v-dialog v-model="showDetailDialog" max-width="400px">
      <v-card v-if="selectedGap">
        <v-card-title>{{ selectedGap.skill_name }} - {{ selectedGap.role_name }}</v-card-title>
        <v-card-text class="py-6 space-y-4">
          <div>
            <p class="text-sm font-semibold mb-1">Competencia:</p>
            <p>{{ selectedGap.competency_name }}</p>
          </div>

          <div class="flex gap-8">
            <div>
              <p class="text-xs font-semibold text-gray-600 mb-1">ACTUAL</p>
              <div class="flex items-center gap-2">
                <div class="text-3xl font-bold text-blue-600">{{ selectedGap.current_level }}</div>
                <p class="text-xs text-gray-600">/ 5</p>
              </div>
            </div>
            <div class="text-2xl font-light text-gray-400">→</div>
            <div>
              <p class="text-xs font-semibold text-gray-600 mb-1">REQUERIDO</p>
              <div class="flex items-center gap-2">
                <div class="text-3xl font-bold text-green-600">{{ selectedGap.required_level }}</div>
                <p class="text-xs text-gray-600">/ 5</p>
              </div>
            </div>
          </div>

          <div>
            <p class="text-sm font-semibold mb-2">Brecha: {{ selectedGap.gap }} niveles</p>
            <v-progress-linear
              :model-value="(selectedGap.current_level / selectedGap.required_level) * 100"
              color="blue"
              height="8"
            />
          </div>

          <div v-if="selectedGap.learning_path" class="p-3 bg-blue-50 rounded">
            <p class="text-sm font-semibold mb-2">Ruta de Aprendizaje:</p>
            <p class="text-sm">{{ selectedGap.learning_path }}</p>
          </div>

          <div v-if="selectedGap.timeline_months" class="p-3 bg-amber-50 rounded">
            <p class="text-sm font-semibold">Timeline estimado: {{ selectedGap.timeline_months }} meses</p>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-btn variant="text" @click="showDetailDialog = false">Cerrar</v-btn>
          <v-btn color="primary" @click="suggestLearning">Sugerir Aprendizaje</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface Role {
  id: number;
  name: string;
  fte: number;
}

interface Skill {
  id: number;
  name: string;
  competency_name: string;
  competency_id: number;
}

interface SkillGap {
  skill_id: number;
  role_id: number;
  skill_name: string;
  role_name: string;
  competency_name: string;
  current_level: number;
  required_level: number;
  gap: number;
  learning_path?: string;
  timeline_months?: number;
}

interface GapMatrix {
  [skillId: number]: {
    [roleId: number]: SkillGap;
  };
}

interface Props {
  scenarioId: number;
}

const props = defineProps<Props>();

const page = usePage();
const loading = ref(true);
const error = ref<string | null>(null);

const roles = ref<Role[]>([]);
const skills = ref<Skill[]>([]);
const gapMatrix = ref<GapMatrix>({});

const filterBy = ref('all');
const searchQuery = ref('');

const showDetailDialog = ref(false);
const selectedGap = ref<SkillGap | null>(null);

const filteredSkills = computed(() => {
  return skills.value.filter((skill) => {
    const matchesSearch = skill.name.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchesSearch;
  });
});

const loadData = async () => {
  try {
    loading.value = true;
    const response = await fetch(
      `/api/scenarios/${props.scenarioId}/step2/skill-gaps-matrix`
    );

    if (!response.ok) throw new Error('Error al cargar matriz de brechas');

    const data = await response.json();
    roles.value = data.roles || [];
    skills.value = data.skills || [];

    // Construir matriz de brechas
    const matrix: GapMatrix = {};
    if (data.gaps) {
      for (const gap of data.gaps) {
        if (!matrix[gap.skill_id]) matrix[gap.skill_id] = {};
        matrix[gap.skill_id][gap.role_id] = gap;
      }
    }
    gapMatrix.value = matrix;
  } catch (err: any) {
    error.value = err.message || 'Error al cargar datos';
  } finally {
    loading.value = false;
  }
};

const getGapColor = (skillId: number, roleId: number): string => {
  const gap = gapMatrix.value[skillId]?.[roleId];
  if (!gap) return '#ccc';

  const gapSize = gap.required_level - gap.current_level;
  if (gapSize <= 0) return '#4caf50'; // No gap
  if (gapSize === 1) return '#81c784'; // Light
  if (gapSize === 2) return '#ff9800'; // Medium
  return '#f44336'; // High
};

const getGapValue = (skillId: number, roleId: number): string => {
  const gap = gapMatrix.value[skillId]?.[roleId];
  if (!gap) return '-';
  const gapSize = gap.required_level - gap.current_level;
  return gapSize <= 0 ? '✓' : `${gapSize}`;
};

const getCurrentLevel = (skillId: number, roleId: number): number => {
  const gap = gapMatrix.value[skillId]?.[roleId];
  return gap?.current_level || 0;
};

const getRequiredLevel = (skillId: number, roleId: number): number => {
  const gap = gapMatrix.value[skillId]?.[roleId];
  return gap?.required_level || 0;
};

const showGapDetail = (skill: Skill, role: Role) => {
  const gap = gapMatrix.value[skill.id]?.[role.id];
  if (gap) {
    selectedGap.value = gap;
    showDetailDialog.value = true;
  }
};

const suggestLearning = () => {
  if (selectedGap.value) {
    console.log('Suggest learning for:', selectedGap.value.skill_name);
    // Implementar lógica de sugerencia de aprendizaje
  }
};

onMounted(() => {
  loadData();
});
</script>

<style scoped>
.skill-gaps-matrix-container {
  padding: 1.5rem;
}

table {
  border-collapse: collapse;
}

.min-w-120 {
  min-width: 120px;
}

.w-250 {
  width: 250px;
}

.grid {
  display: grid;
}

.grid-cols-2 {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

@media (min-width: 768px) {
  .md\:grid-cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}

.gap-4 {
  gap: 1rem;
}
</style>
