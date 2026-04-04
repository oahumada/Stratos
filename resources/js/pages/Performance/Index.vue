<template>
    <AppLayout title="Performance AI">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1
                        class="text-2xl font-bold text-gray-900 dark:text-white"
                    >
                        Performance AI
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Ciclos de evaluación · Scores ponderados · Calibración
                        IA
                    </p>
                </div>
                <v-btn
                    color="primary"
                    prepend-icon="mdi-plus"
                    @click="showCycleDialog = true"
                >
                    Nuevo Ciclo
                </v-btn>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left: Cycles list -->
                <div class="lg:col-span-1">
                    <v-card elevation="1">
                        <v-card-title class="text-base"
                            >Ciclos de Evaluación</v-card-title
                        >
                        <v-divider />
                        <v-list v-if="cycles.length > 0" lines="two">
                            <v-list-item
                                v-for="cycle in cycles"
                                :key="cycle.id"
                                :class="{
                                    'bg-indigo-50':
                                        selectedCycle?.id === cycle.id,
                                }"
                                @click="selectCycle(cycle)"
                            >
                                <template #title>{{ cycle.name }}</template>
                                <template #subtitle>{{
                                    cycle.period
                                }}</template>
                                <template #append>
                                    <v-chip
                                        size="x-small"
                                        :color="statusColor(cycle.status)"
                                        variant="tonal"
                                    >
                                        {{ cycle.status }}
                                    </v-chip>
                                </template>
                            </v-list-item>
                        </v-list>
                        <div
                            v-else
                            class="pa-4 text-center text-sm text-gray-400"
                        >
                            No hay ciclos creados
                        </div>
                    </v-card>
                </div>

                <!-- Main: Cycle detail + reviews -->
                <div class="lg:col-span-2">
                    <v-card v-if="selectedCycle" elevation="1">
                        <v-card-title class="flex items-center justify-between">
                            <span
                                >{{ selectedCycle.name }} —
                                {{ selectedCycle.period }}</span
                            >
                            <div class="flex gap-2">
                                <v-btn
                                    v-if="selectedCycle.status === 'draft'"
                                    size="small"
                                    color="success"
                                    variant="tonal"
                                    @click="activateCycle(selectedCycle.id)"
                                >
                                    Activar
                                </v-btn>
                                <v-btn
                                    v-if="selectedCycle.status === 'active'"
                                    size="small"
                                    color="warning"
                                    variant="tonal"
                                    @click="closeCycle(selectedCycle.id)"
                                >
                                    Cerrar
                                </v-btn>
                                <v-btn
                                    size="small"
                                    color="indigo"
                                    variant="tonal"
                                    :loading="calibrating"
                                    @click="runCalibration(selectedCycle.id)"
                                >
                                    Calibrar IA
                                </v-btn>
                                <v-btn
                                    size="small"
                                    color="teal"
                                    variant="tonal"
                                    @click="loadInsights(selectedCycle.id)"
                                >
                                    Insights
                                </v-btn>
                            </div>
                        </v-card-title>
                        <v-divider />

                        <!-- Calibration result -->
                        <v-alert
                            v-if="calibrationResult"
                            type="success"
                            variant="tonal"
                            class="ma-3"
                            closable
                            @click:close="calibrationResult = null"
                        >
                            Calibración completada:
                            {{ calibrationResult.adjusted }} revisiones
                            ajustadas · Media: {{ calibrationResult.mean }} ·
                            SD: {{ calibrationResult.std_dev }}
                        </v-alert>

                        <!-- Reviews table -->
                        <v-card-title class="pt-0 text-sm">
                            Evaluaciones
                            <v-btn
                                size="x-small"
                                class="ml-2"
                                color="primary"
                                variant="tonal"
                                @click="showReviewDialog = true"
                            >
                                + Nueva Evaluación
                            </v-btn>
                        </v-card-title>
                        <v-data-table
                            :headers="reviewHeaders"
                            :items="reviews"
                            :loading="loadingReviews"
                            item-value="id"
                            density="compact"
                        >
                            <template #item.status="{ item }">
                                <v-chip
                                    size="x-small"
                                    :color="reviewStatusColor(item.status)"
                                    variant="tonal"
                                >
                                    {{ item.status }}
                                </v-chip>
                            </template>
                            <template #item.final_score="{ item }">
                                <span :class="scoreClass(item.final_score)">
                                    {{ item.final_score ?? '–' }}
                                </span>
                            </template>
                        </v-data-table>
                    </v-card>

                    <div
                        v-else
                        class="flex h-48 items-center justify-center text-gray-400"
                    >
                        Selecciona un ciclo para ver los detalles
                    </div>
                </div>
            </div>

            <!-- Insights section -->
            <v-card v-if="insights" elevation="1">
                <v-card-title
                    >Insights — {{ selectedCycle?.name }}</v-card-title
                >
                <v-divider />
                <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-3">
                    <!-- Avg score -->
                    <div class="text-center">
                        <div class="text-xs text-gray-500">Score Promedio</div>
                        <div
                            class="text-3xl font-black"
                            :class="scoreClass(insights.avg_score)"
                        >
                            {{ insights.avg_score }}
                        </div>
                    </div>

                    <!-- Distribution chart (3 bars) -->
                    <div>
                        <div class="mb-2 text-xs text-gray-500">
                            Distribución
                        </div>
                        <div class="flex h-16 items-end gap-2">
                            <div class="flex flex-1 flex-col items-center">
                                <div
                                    class="w-full rounded-t bg-green-400"
                                    :style="{
                                        height:
                                            barHeight(
                                                insights.distribution.high,
                                            ) + 'px',
                                    }"
                                />
                                <span class="mt-1 text-xs"
                                    >Alto ({{
                                        insights.distribution.high
                                    }})</span
                                >
                            </div>
                            <div class="flex flex-1 flex-col items-center">
                                <div
                                    class="w-full rounded-t bg-yellow-400"
                                    :style="{
                                        height:
                                            barHeight(
                                                insights.distribution.mid,
                                            ) + 'px',
                                    }"
                                />
                                <span class="mt-1 text-xs"
                                    >Medio ({{
                                        insights.distribution.mid
                                    }})</span
                                >
                            </div>
                            <div class="flex flex-1 flex-col items-center">
                                <div
                                    class="w-full rounded-t bg-red-400"
                                    :style="{
                                        height:
                                            barHeight(
                                                insights.distribution.low,
                                            ) + 'px',
                                    }"
                                />
                                <span class="mt-1 text-xs"
                                    >Bajo ({{
                                        insights.distribution.low
                                    }})</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Top performers -->
                    <div>
                        <div class="mb-1 text-xs text-gray-500">
                            Top Performers (≥80)
                        </div>
                        <div
                            v-if="insights.top_performers.length === 0"
                            class="text-xs text-gray-400"
                        >
                            —
                        </div>
                        <div
                            v-for="p in insights.top_performers"
                            :key="p.people_id"
                            class="text-xs"
                        >
                            ID {{ p.people_id }}:
                            <strong>{{ p.final_score }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Needs attention -->
                <div
                    v-if="insights.needs_attention.length > 0"
                    class="px-4 pb-4"
                >
                    <div class="mb-1 text-xs text-gray-500">
                        Necesitan Atención (&lt;50)
                    </div>
                    <div
                        v-for="p in insights.needs_attention"
                        :key="p.people_id"
                        class="text-xs text-red-600"
                    >
                        ID {{ p.people_id }}: {{ p.final_score }}
                    </div>
                </div>
            </v-card>

            <!-- Dialog: New Cycle -->
            <v-dialog v-model="showCycleDialog" max-width="500">
                <v-card>
                    <v-card-title>Nuevo Ciclo de Evaluación</v-card-title>
                    <v-card-text>
                        <v-text-field
                            v-model="cycleForm.name"
                            label="Nombre"
                            class="mb-2"
                        />
                        <v-text-field
                            v-model="cycleForm.period"
                            label="Período (ej. 2026-Q1)"
                            class="mb-2"
                        />
                        <v-text-field
                            v-model="cycleForm.starts_at"
                            label="Fecha inicio"
                            type="date"
                            class="mb-2"
                        />
                        <v-text-field
                            v-model="cycleForm.ends_at"
                            label="Fecha fin"
                            type="date"
                        />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn @click="showCycleDialog = false">Cancelar</v-btn>
                        <v-btn
                            color="primary"
                            :loading="savingCycle"
                            @click="createCycle"
                            >Crear</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Dialog: New Review -->
            <v-dialog v-model="showReviewDialog" max-width="500">
                <v-card>
                    <v-card-title>Nueva Evaluación</v-card-title>
                    <v-card-text>
                        <v-text-field
                            v-model.number="reviewForm.people_id"
                            label="ID Persona"
                            type="number"
                            class="mb-2"
                        />
                        <v-text-field
                            v-model.number="reviewForm.self_score"
                            label="Score Auto (0-100)"
                            type="number"
                            class="mb-2"
                        />
                        <v-text-field
                            v-model.number="reviewForm.manager_score"
                            label="Score Manager (0-100)"
                            type="number"
                            class="mb-2"
                        />
                        <v-text-field
                            v-model.number="reviewForm.peer_score"
                            label="Score Peers (0-100)"
                            type="number"
                        />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn @click="showReviewDialog = false"
                            >Cancelar</v-btn
                        >
                        <v-btn
                            color="primary"
                            :loading="savingReview"
                            @click="createReview"
                            >Guardar</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';

interface Cycle {
    id: number;
    name: string;
    period: string;
    status: 'draft' | 'active' | 'review' | 'closed';
    starts_at: string;
    ends_at: string;
    reviews_count?: number;
}

interface Review {
    id: number;
    cycle_id: number;
    people_id: number;
    self_score: number | null;
    manager_score: number | null;
    peer_score: number | null;
    final_score: number | null;
    calibration_score: number | null;
    status: 'pending' | 'in_progress' | 'completed' | 'calibrated';
}

interface Insights {
    top_performers: { people_id: number; final_score: number }[];
    needs_attention: { people_id: number; final_score: number }[];
    avg_score: number;
    distribution: { high: number; mid: number; low: number };
}

const cycles = ref<Cycle[]>([]);
const selectedCycle = ref<Cycle | null>(null);
const reviews = ref<Review[]>([]);
const insights = ref<Insights | null>(null);
const calibrationResult = ref<{
    adjusted: number;
    mean: number;
    std_dev: number;
} | null>(null);

const loadingReviews = ref(false);
const calibrating = ref(false);
const showCycleDialog = ref(false);
const showReviewDialog = ref(false);
const savingCycle = ref(false);
const savingReview = ref(false);

const cycleForm = ref({ name: '', period: '', starts_at: '', ends_at: '' });
const reviewForm = ref({
    people_id: 0,
    self_score: null as number | null,
    manager_score: null as number | null,
    peer_score: null as number | null,
});

const reviewHeaders = [
    { title: 'ID Persona', key: 'people_id' },
    { title: 'Auto', key: 'self_score' },
    { title: 'Manager', key: 'manager_score' },
    { title: 'Peers', key: 'peer_score' },
    { title: 'Score Final', key: 'final_score' },
    { title: 'Calibrado', key: 'calibration_score' },
    { title: 'Estado', key: 'status' },
];

function headers() {
    return {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        Accept: 'application/json',
    };
}

async function loadCycles() {
    const res = await fetch('/api/performance/cycles', { headers: headers() });
    const json = await res.json();
    cycles.value = json.data ?? [];
}

async function selectCycle(cycle: Cycle) {
    selectedCycle.value = cycle;
    insights.value = null;
    calibrationResult.value = null;
    await loadReviews(cycle.id);
}

async function loadReviews(cycleId: number) {
    loadingReviews.value = true;
    const res = await fetch(`/api/performance/cycles/${cycleId}/reviews`, {
        headers: headers(),
    });
    const json = await res.json();
    reviews.value = json.data ?? [];
    loadingReviews.value = false;
}

async function createCycle() {
    savingCycle.value = true;
    await fetch('/api/performance/cycles', {
        method: 'POST',
        headers: headers(),
        body: JSON.stringify(cycleForm.value),
    });
    savingCycle.value = false;
    showCycleDialog.value = false;
    cycleForm.value = { name: '', period: '', starts_at: '', ends_at: '' };
    await loadCycles();
}

async function createReview() {
    if (!selectedCycle.value) return;
    savingReview.value = true;
    await fetch(`/api/performance/cycles/${selectedCycle.value.id}/reviews`, {
        method: 'POST',
        headers: headers(),
        body: JSON.stringify(reviewForm.value),
    });
    savingReview.value = false;
    showReviewDialog.value = false;
    reviewForm.value = {
        people_id: 0,
        self_score: null,
        manager_score: null,
        peer_score: null,
    };
    await loadReviews(selectedCycle.value.id);
}

async function activateCycle(id: number) {
    await fetch(`/api/performance/cycles/${id}/activate`, {
        method: 'POST',
        headers: headers(),
    });
    await loadCycles();
    if (selectedCycle.value?.id === id) {
        selectedCycle.value =
            cycles.value.find((c) => c.id === id) ?? selectedCycle.value;
    }
}

async function closeCycle(id: number) {
    await fetch(`/api/performance/cycles/${id}/close`, {
        method: 'POST',
        headers: headers(),
    });
    await loadCycles();
    if (selectedCycle.value?.id === id) {
        selectedCycle.value =
            cycles.value.find((c) => c.id === id) ?? selectedCycle.value;
    }
}

async function runCalibration(id: number) {
    calibrating.value = true;
    const res = await fetch(`/api/performance/cycles/${id}/calibrate`, {
        method: 'POST',
        headers: headers(),
    });
    const json = await res.json();
    calibrationResult.value = json.data;
    calibrating.value = false;
    if (selectedCycle.value) await loadReviews(selectedCycle.value.id);
}

async function loadInsights(id: number) {
    const res = await fetch(`/api/performance/cycles/${id}/insights`, {
        headers: headers(),
    });
    const json = await res.json();
    insights.value = json.data;
}

function statusColor(status: string) {
    return (
        {
            draft: 'grey',
            active: 'success',
            review: 'warning',
            closed: 'error',
        }[status] ?? 'grey'
    );
}

function reviewStatusColor(status: string) {
    return (
        {
            pending: 'grey',
            in_progress: 'blue',
            completed: 'success',
            calibrated: 'teal',
        }[status] ?? 'grey'
    );
}

function scoreClass(score: number | null) {
    if (score === null) return '';
    if (score >= 80) return 'text-green-600 font-bold';
    if (score >= 50) return 'text-yellow-600';
    return 'text-red-600';
}

function barHeight(count: number): number {
    return Math.max(4, Math.min(60, count * 10));
}

onMounted(loadCycles);
</script>
