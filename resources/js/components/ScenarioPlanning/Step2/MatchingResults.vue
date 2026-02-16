<template>
    <div class="matching-results-container">
        <div class="mb-6">
            <h3 class="text-h5 mb-4 font-semibold">
                Resultados de Matching Candidato-Posición
            </h3>
            <p class="mb-4 text-sm text-gray-600">
                Evaluación de compatibilidad entre candidatos y posiciones
                requeridas
            </p>
        </div>

        <!-- Alerts -->
        <v-alert
            v-if="error"
            type="error"
            closable
            @click:close="error = null"
            class="mb-4"
        >
            {{ error }}
        </v-alert>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center py-8">
            <v-progress-circular indeterminate color="primary" />
        </div>

        <!-- Results -->
        <div v-else class="space-y-4">
            <div
                v-if="results.length === 0"
                class="py-8 text-center text-gray-500"
            >
                No hay resultados de matching disponibles
            </div>

            <div
                v-for="result in results"
                :key="result.id"
                class="rounded-lg border p-4"
            >
                <div class="mb-3 flex items-start justify-between">
                    <div>
                        <h4 class="text-lg font-semibold">
                            {{ result.candidate_name }}
                        </h4>
                        <p class="text-sm text-gray-600">
                            {{ result.current_role }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div
                            class="text-3xl font-bold"
                            :style="{
                                color: getMatchColor(result.match_percentage),
                            }"
                        >
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
                <div class="mb-4 rounded bg-blue-50 p-3">
                    <p class="text-sm font-semibold text-gray-700">
                        Posición Objetivo:
                    </p>
                    <p class="text-base font-medium text-blue-700">
                        {{ result.target_position }}
                    </p>
                </div>

                <!-- Risk Factors -->
                <div
                    class="mb-4"
                    v-if="result.risk_factors && result.risk_factors.length > 0"
                >
                    <p class="mb-2 text-sm font-semibold text-gray-700">
                        Factores de Riesgo:
                    </p>
                    <div class="space-y-2">
                        <div
                            v-for="risk in result.risk_factors"
                            :key="risk.id"
                            class="flex items-center gap-2 rounded bg-red-50 p-2"
                        >
                            <v-icon size="small" color="error"
                                >mdi-alert-circle</v-icon
                            >
                            <span class="text-sm text-red-700">{{
                                risk.factor
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="mb-4 rounded bg-amber-50 p-3">
                    <p class="mb-1 text-sm font-semibold text-gray-700">
                        Timeline de Productividad:
                    </p>
                    <p class="text-base font-medium text-amber-700">
                        {{ result.productivity_timeline }} meses
                    </p>
                    <p class="mt-1 text-xs text-gray-600">
                        Tiempo estimado para alcanzar productividad total
                    </p>
                </div>

                <!-- Skills Gap -->
                <div
                    v-if="result.skill_gaps && result.skill_gaps.length > 0"
                    class="mb-4"
                >
                    <p class="mb-2 text-sm font-semibold text-gray-700">
                        Brechas de Skills:
                    </p>
                    <div class="space-y-1 text-sm">
                        <div
                            v-for="gap in result.skill_gaps.slice(0, 3)"
                            :key="gap.id"
                            class="flex justify-between"
                        >
                            <span>{{ gap.skill_name }}</span>
                            <span class="text-gray-600"
                                >{{ gap.current_level }} →
                                {{ gap.required_level }}</span
                            >
                        </div>
                        <div
                            v-if="result.skill_gaps.length > 3"
                            class="text-gray-500 italic"
                        >
                            +{{ result.skill_gaps.length - 3 }} más...
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 border-t pt-3">
                    <v-btn
                        size="small"
                        variant="tonal"
                        color="primary"
                        @click="viewDetails(result)"
                    >
                        Ver Detalles
                    </v-btn>
                    <v-btn
                        size="small"
                        variant="tonal"
                        @click="viewGaps(result)"
                    >
                        Brechas Completas
                    </v-btn>
                    <v-btn
                        size="small"
                        variant="tonal"
                        color="success"
                        @click="acceptMatch(result.id)"
                    >
                        Aceptar Match
                    </v-btn>
                </div>
            </div>
        </div>

        <!-- Details Dialog -->
        <v-dialog v-model="showDetailsDialog" max-width="600px">
            <v-card v-if="selectedResult">
                <v-card-title
                    >{{ selectedResult.candidate_name }} →
                    {{ selectedResult.target_position }}</v-card-title
                >
                <v-card-text class="space-y-4 py-6">
                    <div>
                        <p class="mb-2 text-sm font-semibold">Rol Actual:</p>
                        <p>{{ selectedResult.current_role }}</p>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-semibold">
                            Compatibilidad:
                        </p>
                        <div class="flex items-center gap-2">
                            <v-progress-linear
                                :model-value="selectedResult.match_percentage"
                                :color="
                                    getMatchColor(
                                        selectedResult.match_percentage,
                                    )
                                "
                                class="flex-1"
                            />
                            <span class="font-bold"
                                >{{ selectedResult.match_percentage }}%</span
                            >
                        </div>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-semibold">
                            Timeline de Productividad:
                        </p>
                        <p>{{ selectedResult.productivity_timeline }} meses</p>
                    </div>
                    <div v-if="selectedResult.notes">
                        <p class="mb-2 text-sm font-semibold">Notas:</p>
                        <p class="text-sm">{{ selectedResult.notes }}</p>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-btn variant="text" @click="showDetailsDialog = false"
                        >Cerrar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

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

const loading = ref(true);
const error = ref<string | null>(null);
const results = ref<MatchingResult[]>([]);
const showDetailsDialog = ref(false);
const selectedResult = ref<MatchingResult | null>(null);

const page = usePage(); // Declare page variable

const loadResults = async () => {
    try {
        loading.value = true;
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/matching-results`,
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
            { method: 'POST' },
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

watch(
    () => props.scenarioId,
    () => {
        loadResults();
    },
);
</script>

<style scoped>
.matching-results-container {
    padding: 1.5rem;
}
</style>
