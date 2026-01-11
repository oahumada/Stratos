<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useNotification } from '@kyvg/vue3-notification';
import { Radar } from 'vue-chartjs';
import { Chart as ChartJS, RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend } from 'chart.js';
import { useTheme as useVuetifyTheme } from 'vuetify';

defineOptions({ layout: AppLayout });

ChartJS.register(RadialLinearScale, PointElement, LineElement, Filler, Tooltip, Legend);

const vuetifyTheme = useVuetifyTheme();

const headerGradient = computed(() => {
  const theme = vuetifyTheme.global.current.value;
  return `linear-gradient(135deg, ${theme.colors.primary} 0%, ${theme.colors.secondary} 100%)`;
});

const { notify } = useNotification();

type People = {
  id: number;
  first_name?: string;
  last_name?: string;
  name?: string;
  email?: string;
  role?: { id: number; name: string };
};

type Role = {
  id: number;
  name: string;
};

type Gap = {
  skill_id: number;
  skill_name: string;
  required_level: number;
  current_level: number;
  gap: number;
  status: string;
  is_critical?: boolean;
};

type GapAnalysisResult = {
  people: { id: number; name: string };
  role: { id: number; name: string };
  analysis: {
    match_percentage: number;
    summary?: { category: string; skills_ok: number; total_skills: number };
    gaps: Gap[];
  };
};

const peoples = ref<People[]>([]);
const roles = ref<Role[]>([]);
const selectedPeopleId = ref<number | null>(null);
const selectedRoleId = ref<number | null>(null);
const loading = ref(false);
const analyzing = ref(false);
const generatingPath = ref(false);
const result = ref<GapAnalysisResult | null>(null);

const peopleOptions = computed(() =>
  peoples.value.map((p) => {
    const fullName = `${p.first_name || ''} ${p.last_name || ''}`.trim() || p.name || 'Sin nombre';
    const roleName = p.role?.name || 'Sin rol';
    const label = `${fullName} (${roleName})`;
    return { ...p, label, value: p.id };
  })
);

const roleOptions = computed(() => roles.value.map((r) => ({ ...r, label: r.name, value: r.id })));

const radarData = computed(() => {
  if (!result.value?.analysis?.gaps?.length) return null;
  const labels = result.value.analysis.gaps.map((g) => g.skill_name);
  return {
    labels,
    datasets: [
      {
        label: 'Requerido',
        data: result.value.analysis.gaps.map((g) => g.required_level),
        backgroundColor: 'rgba(103, 58, 183, 0.18)',
        borderColor: '#673AB7',
        pointBackgroundColor: '#673AB7',
        fill: true,
      },
      {
        label: 'Actual',
        data: result.value.analysis.gaps.map((g) => g.current_level),
        backgroundColor: 'rgba(33, 150, 243, 0.18)',
        borderColor: '#2196F3',
        pointBackgroundColor: '#2196F3',
        fill: true,
      },
    ],
  };
});

const radarOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    r: {
      suggestedMin: 0,
      suggestedMax: 5,
      ticks: { stepSize: 1 },
    },
  },
  plugins: {
    legend: {
      position: 'bottom' as const,
    },
  },
};

const loadPeoplesAndRoles = async () => {
  loading.value = true;
  try {
    const [peoplesRes, rolesRes] = await Promise.all([
      axios.get('/api/people', { params: { with: 'role' } }),
      axios.get('/api/roles'),
    ]);
    peoples.value = peoplesRes.data.data || peoplesRes.data;
    roles.value = rolesRes.data.data || rolesRes.data;
  } catch (err) {
    console.error('Failed to load data', err);
    notify({ type: 'error', text: 'Error al cargar personas y roles' });
  } finally {
    loading.value = false;
  }
};

const analyzeGap = async () => {
  if (!selectedPeopleId.value || !selectedRoleId.value) {
    notify({ type: 'warning', text: 'Selecciona persona y rol' });
    return;
  }

  analyzing.value = true;
  try {
    const payload = {
      people_id: Number(selectedPeopleId.value),
      role_id: selectedRoleId.value ? Number(selectedRoleId.value) : null,
    }
    console.debug('Gap analysis payload', payload)
    const response = await axios.post('/api/gap-analysis', payload)
    // support different response shapes
    result.value = response?.data?.data ?? response?.data ?? response
    console.debug('Gap analysis response', response)
    notify({ type: 'success', text: 'Análisis completado' })
  } catch (err: any) {
    console.error('Gap analysis failed', err)
    const serverMessage = err?.response?.data?.message || err?.response?.data?.error || null
    if (serverMessage) {
      notify({ type: 'error', text: serverMessage })
    } else {
      notify({ type: 'error', text: 'Error en el análisis' })
    }
    // clear previous result on error
    result.value = null
  } finally {
    analyzing.value = false
  }
};

const getStatusColor = (status: string): string => {
  const statusMap: Record<string, string> = {
    critical: 'error',
    developing: 'warning',
    ok: 'success',
  };
  return statusMap[status] || 'info-darken-2';
};

const getMatchColor = (percentage: number): string => {
  if (percentage >= 80) return 'success';
  if (percentage >= 60) return 'warning';
  return 'error';
};

const generateLearningPath = async () => {
  if (!selectedPeopleId.value || !selectedRoleId.value) {
    notify({ type: 'warning', text: 'Selecciona persona y rol' });
    return;
  }

  if (!result.value) {
    notify({ type: 'warning', text: 'Realiza primero un análisis de brechas' });
    return;
  }

  generatingPath.value = true;
  try {
    console.log('Generating learning path for people:', selectedPeopleId.value, 'role:', selectedRoleId.value);
    const response = await axios.post('/api/development-paths/generate', {
      people_id: selectedPeopleId.value,
      role_id: selectedRoleId.value,
    });
    console.log('Learning path generated:', response.data);
    notify({ 
      type: 'success', 
      text: 'Ruta de aprendizaje generada exitosamente'
    });
    // Redirect to learning paths
    window.location.href = '/learning-paths';
  } catch (err: any) {
    console.error('Failed to generate learning path', err);
    notify({ 
      type: 'error', 
      text: err.response?.data?.message || 'Error al generar la ruta de aprendizaje' 
    });
  } finally {
    generatingPath.value = false;
  }
};

onMounted(() => {
  loadPeoplesAndRoles();
});
</script>

<template>
  <div class="pa-4">
    <!-- Header -->
    <div class="d-flex justify-space-between align-center mb-4" :style="{ background: headerGradient }" style="padding: 1.5rem; border-radius: 8px;">
      <div>
        <h1 class="text-h4 font-weight-bold mb-2" style="color: white;">Gap Analysis</h1>
        <p class="text-subtitle-2" style="color: rgba(255,255,255,0.85);">
          Identifica las brechas de competencias entre el perfil actual de una persona y los requisitos de un rol específico.
        </p>
      </div>
    </div>

    <!-- Form Section -->
    <v-card class="mb-6">
      <v-card-title>Selecciona persona y rol</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" sm="6">
            <v-autocomplete
              v-model="selectedPeopleId"
              :items="peopleOptions"
              item-title="label"
              item-value="value"
              label="Persona"
              :loading="loading"
              outlined
              clearable
              placeholder="Escribe para buscar..."
            />
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="selectedRoleId"
              :items="roleOptions"
              item-title="label"
              item-value="value"
              label="Rol"
              :loading="loading"
              outlined
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-btn
              color="primary"
              @click="analyzeGap"
              :loading="analyzing"
              :disabled="!selectedPeopleId || !selectedRoleId"
            >
              <v-icon left>mdi-analysis</v-icon>
              Analizar brechas
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Results Section -->
    <div v-if="result">
      <v-card class="mb-4">
        <v-card-text>
          <div class="d-flex flex-wrap justify-space-between align-center gap-4 mb-4">
            <div>
              <div class="text-caption text-medium-emphasis">Persona</div>
              <div class="text-subtitle-1 font-weight-medium">{{ result.people?.name }}</div>
            </div>
            <div>
              <div class="text-caption text-medium-emphasis">Rol</div>
              <div class="text-subtitle-1 font-weight-medium">{{ result.role?.name }}</div>
            </div>
            <div class="text-right">
              <div class="text-caption text-medium-emphasis">Match general</div>
              <div :class="`text-h6 text-${getMatchColor(result.analysis.match_percentage)}`">{{ result.analysis.match_percentage }}%</div>
              <v-progress-linear
                class="mt-1"
                :value="result.analysis.match_percentage"
                :color="getMatchColor(result.analysis.match_percentage)"
                height="6"
              />
            </div>
          </div>
          <div class="d-flex flex-wrap gap-2 mt-3">
            <v-chip size="small" color="primary" variant="flat">
              {{ result.analysis.summary?.skills_ok ?? 0 }}/{{ result.analysis.summary?.total_skills ?? result.analysis.gaps.length }} skills al nivel
            </v-chip>
            <v-chip size="small" :color="getStatusColor(result.analysis.summary?.category || 'info')" variant="flat" text-color="white">
              {{ result.analysis.summary?.category || 'sin categoría' }}
            </v-chip>
            <v-chip size="small" color="info" variant="outlined" v-if="result.analysis.gaps.length === 0">
              Sin brechas
            </v-chip>
          </div>
          <v-divider class="my-4" />
          <div class="d-flex justify-end">
            <v-btn
              color="success"
              variant="tonal"
              @click="generateLearningPath"
              :loading="generatingPath"
              prepend-icon="mdi-map-marker-path"
            >
              Generar ruta de aprendizaje
            </v-btn>
          </div>
        </v-card-text>
      </v-card>

      <v-row>
        <v-col cols="12" md="7">
          <v-card class="mb-4">
            <v-card-title>Detalle de brechas por skill</v-card-title>
            <v-card-text>
              <v-table v-if="result.analysis.gaps.length > 0" class="elevation-1">
                <thead>
                  <tr>
                    <th class="text-left">Skill</th>
                    <th class="text-center">Requerido</th>
                    <th class="text-center">Actual</th>
                    <th class="text-center">Brecha</th>
                    <th class="text-left">Estado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="gap in result.analysis.gaps" :key="`${gap.skill_id}`">
                    <td>{{ gap.skill_name }}</td>
                    <td class="text-center">
                      <v-chip size="small" variant="outlined">{{ gap.required_level }}</v-chip>
                    </td>
                    <td class="text-center">
                      <v-chip size="small" variant="outlined">{{ gap.current_level }}</v-chip>
                    </td>
                    <td class="text-center">
                      <span :class="`text-${getStatusColor(gap.status)}`">
                        {{ gap.gap }}
                      </span>
                    </td>
                    <td>
                      <v-chip
                        :color="getStatusColor(gap.status)"
                        text-color="white"
                        size="small"
                        variant="flat"
                      >
                        {{ gap.status }}
                      </v-chip>
                    </td>
                  </tr>
                </tbody>
              </v-table>
              <div v-else class="text-center py-6 text-medium-emphasis">
                Sin brechas: match perfecto.
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="5">
          <v-card class="mb-4" style="min-height: 360px;">
            <v-card-title>Radar de niveles</v-card-title>
            <v-card-text style="height: 300px;">
              <Radar v-if="radarData" :data="radarData" :options="radarOptions" />
              <div v-else class="text-center py-8 text-medium-emphasis">No hay datos para graficar</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <!-- Empty State -->
    <div v-if="!result" class="text-center py-12">
      <v-icon size="64" class="mb-4 text-medium-emphasis">mdi-chart-box-outline</v-icon>
      <p class="text-body1 text-medium-emphasis">Selecciona una persona y un rol para analizar brechas</p>
    </div>
  </div>
</template>

<style scoped>
</style>
