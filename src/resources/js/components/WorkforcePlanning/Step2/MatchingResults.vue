<template>
  <div class="matching-results-container">
    <div class="mb-6">
      <h3 class="text-h5 font-semibold mb-4">Resultados de Matching Candidato-Posición</h3>
      <p class="text-sm text-gray-600 mb-4">
        Evaluación de compatibilidad entre candidatos y posiciones requeridas
      </p>
    </div>

    <!-- Alerts -->
    <v-alert v-if="error" type="error" closable @click:close="error = null" class="mb-4">
      {{ error }}
    </v-alert>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-8">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <!-- Results -->
    <div v-else class="space-y-4">
      <div v-if="results.length === 0" class="text-center py-8 text-gray-500">
        No hay resultados de matching disponibles
      </div>

      <div v-for="result in results" :key="result.id" class="border rounded-lg p-4">
        <div class="flex items-start justify-between mb-3">
          <div>
            <h4 class="font-semibold text-lg">{{ result.candidate_name }}</h4>
            <p class="text-sm text-gray-600">{{ result.current_role }}</p>
          </div>
          <div class="text-right">
            <div class="text-3xl font-bold" :style="{ color: getMatchColor(result.match_percentage) }">
              {{ result.match_percentage }}%
            </div>
            <p class="text-xs text-gray-600">Compatibilidad</p>
          </div>
        </div>

        <!-- Barra de progreso -->
        <v-progress-linear
          :model-value="result.match_percentage"
          :color="getMatchColor(result.match_percentage)"
          class="mb-4"
        />

        <!-- Target Position -->
        <div class="mb-4 p-3 bg-blue-50 rounded">
          <p class="text-sm font-semibold text-gray-700">Posición Objetivo:</p>
          <p class="text-base font-medium text-blue-700">{{ result.target_position }}</p>
        </div>

        <!-- Risk Factors -->
        <div class="mb-4" v-if="result.risk_factors && result.risk_factors.length > 0">
          <p class="text-sm font-semibold text-gray-700 mb-2">Factores de Riesgo:</p>
          <div class="space-y-2">
            <div
              v-for="risk in result.risk_factors"
              :key="risk.id"
              class="flex items-center gap-2 p-2 bg-red-50 rounded"
            >
              <v-icon size="small" color="error">mdi-alert-circle</v-icon>
              <span class="text-sm text-red-700">{{ risk.factor }}</span>
            </div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="mb-4 p-3 bg-amber-50 rounded">
          <p class="text-sm font-semibold text-gray-700 mb-1">Timeline de Productividad:</p>
          <p class="text-base font-medium text-amber-700">
            {{ result.productivity_timeline }} meses
          </p>
          <p class="text-xs text-gray-600 mt-1">Tiempo estimado para alcanzar productividad total</p>
        </div>

        <!-- Skills Gap -->
        <div v-if="result.skill_gaps && result.skill_gaps.length > 0" class="mb-4">
          <p class="text-sm font-semibold text-gray-700 mb-2">Brechas de Skills:</p>
          <div class="space-y-1 text-sm">
            <div v-for="gap in result.skill_gaps.slice(0, 3)" :key="gap.id" class="flex justify-between">
              <span>{{ gap.skill_name }}</span>
              <span class="text-gray-600">{{ gap.current_level }} → {{ gap.required_level }}</span>
            </div>
            <div v-if="result.skill_gaps.length > 3" class="text-gray-500 italic">
              +{{ result.skill_gaps.length - 3 }} más...
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2 pt-3 border-t">
          <v-btn size="small" variant="tonal" color="primary" @click="viewDetails(result)">
            Ver Detalles
          </v-btn>
          <v-btn size="small" variant="tonal" @click="viewGaps(result)">
            Brechas Completas
          </v-btn>
          <v-btn size="small" variant="tonal" color="success" @click="acceptMatch(result.id)">
            Aceptar Match
          </v-btn>
        </div>
      </div>
    </div>

    <!-- Details Dialog -->
    <v-dialog v-model="showDetailsDialog" max-width="600px">
      <v-card v-if="selectedResult">
        <v-card-title>{{ selectedResult.candidate_name }} → {{ selectedResult.target_position }}</v-card-title>
        <v-card-text class="py-6 space-y-4">
          <div>
            <p class="text-sm font-semibold mb-2">Rol Actual:</p>
            <p>{{ selectedResult.current_role }}</p>
          </div>
          <div>
            <p class="text-sm font-semibold mb-2">Compatibilidad:</p>
            <div class="flex items-center gap-2">
              <v-progress-linear
                :model-value="selectedResult.match_percentage"
                :color="getMatchColor(selectedResult.match_percentage)"
                class="flex-1"
              />
              <span class="font-bold">{{ selectedResult.match_percentage }}%</span>
            </div>
          </div>
          <div>
            <p class="text-sm font-semibold mb-2">Timeline de Productividad:</p>
            <p>{{ selectedResult.productivity_timeline }} meses</p>
          </div>
          <div v-if="selectedResult.notes">
            <p class="text-sm font-semibold mb-2">Notas:</p>
            <p class="text-sm">{{ selectedResult.notes }}</p>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-btn variant="text" @click="showDetailsDialog = false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface SkillGap {
  id: number;
  skill_name: string;
  current_level: number;
  required_level: number;
}

interface RiskFactor {
  id: number;
  factor: string;
}

interface MatchingResult {
  id: number;
  candidate_name: string;
  current_role: string;
  target_position: string;
  match_percentage: number;
  risk_factors?: RiskFactor[];
  productivity_timeline: number;
  skill_gaps?: SkillGap[];
  notes?: string;
}

interface Props {
  scenarioId: number;
}

const props = defineProps<Props>();

const page = usePage();
const loading = ref(true);
const error = ref<string | null>(null);
const results = ref<MatchingResult[]>([]);
const showDetailsDialog = ref(false);
const selectedResult = ref<MatchingResult | null>(null);

const loadResults = async () => {
  try {
    loading.value = true;
    const response = await fetch(
      `/api/scenarios/${props.scenarioId}/step2/matching-results`
    );

    if (!response.ok) throw new Error('Error al cargar resultados');

    const data = await response.json();
    results.value = data.data || [];
  } catch (err: any) {
    error.value = err.message || 'Error al cargar resultados de matching';
  } finally {
    loading.value = false;
  }
};

const getMatchColor = (percentage: number): string => {
  if (percentage >= 80) return '#4caf50'; // green
  if (percentage >= 60) return '#ff9800'; // orange
  if (percentage >= 40) return '#ff5252'; // red
  return '#b71c1c'; // dark red
};

const viewDetails = (result: MatchingResult) => {
  selectedResult.value = result;
  showDetailsDialog.value = true;
};

const viewGaps = (result: MatchingResult) => {
  // Aquí se abrirá un modal con el SkillGapsMatrix para este candidato
  console.log('View gaps for:', result.candidate_name);
};

const acceptMatch = async (id: number) => {
  try {
    const apiUrl = (page.props as any).apiUrl || '/api';
    const response = await fetch(
      `${apiUrl}/matching-results/${id}/accept`,
      { method: 'POST' }
    );

    if (!response.ok) throw new Error('Error al aceptar match');

    await loadResults();
  } catch (err: any) {
    error.value = err.message || 'Error al aceptar match';
  }
};

onMounted(() => {
  loadResults();
});
</script>

<style scoped>
.matching-results-container {
  padding: 1.5rem;
}
</style>
