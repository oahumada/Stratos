<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';

interface Recommendation {
    id: number;
    lever: string;
    rationale: string;
    cost_estimate_weeks: number;
    estimated_cost_usd: number;
    success_probability_pct: number;
    priority_score: number;
}

const scenarioId = ref<string>('');
const recommendations = ref<Recommendation[]>([]);
const loading = ref(false);
const generating = ref(false);
const error = ref<string | null>(null);

const leverColors: Record<string, string> = {
    HIRE: 'blue',
    RESKILL: 'green',
    ROTATE: 'teal',
    TRANSFER: 'orange',
    CONTINGENT: 'amber',
    AUTOMATE: 'purple',
    HYBRID_TALENT: 'pink',
};

function leverColor(lever: string): string {
    return leverColors[lever] ?? 'grey';
}

async function loadRecommendations() {
    if (!scenarioId.value) return;
    loading.value = true;
    error.value = null;
    try {
        const res = await fetch(
            `/api/strategic-planning/scenarios/${scenarioId.value}/recommendations`,
            { headers: { 'X-Requested-With': 'XMLHttpRequest' } },
        );
        if (!res.ok) throw new Error(`Error ${res.status}`);
        const data = await res.json();
        recommendations.value = data.data ?? data ?? [];
    } catch (e: any) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
}

async function generateRecommendations() {
    if (!scenarioId.value) return;
    generating.value = true;
    error.value = null;
    try {
        const res = await fetch(
            `/api/strategic-planning/scenarios/${scenarioId.value}/recommendations/generate`,
            {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        (
                            document.querySelector(
                                'meta[name="csrf-token"]',
                            ) as HTMLMetaElement
                        )?.content ?? '',
                },
            },
        );
        if (!res.ok) throw new Error(`Error ${res.status}`);
        const data = await res.json();
        recommendations.value = data.data ?? data ?? [];
    } catch (e: any) {
        error.value = e.message;
    } finally {
        generating.value = false;
    }
}

const hasRecommendations = computed(() => recommendations.value.length > 0);
</script>

<template>
    <AppLayout title="Recomendaciones de Cierre">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-3xl font-black tracking-tight text-white drop-shadow-md"
                    >
                        <span class="mr-3">🚀</span>Recomendaciones de Cierre
                    </h2>
                    <p class="mt-2 text-sm text-white/50">
                        Motor de recomendaciones de palancas AIHR — Fase 2
                    </p>
                </div>
            </div>
        </template>

        <div class="mt-6 space-y-6 p-4">
            <!-- Scenario selector -->
            <v-card class="dark:bg-gray-800" elevation="2">
                <v-card-text>
                    <div class="flex flex-wrap items-center gap-4">
                        <v-text-field
                            v-model="scenarioId"
                            label="ID de Escenario"
                            placeholder="Ingresa el ID del escenario"
                            variant="outlined"
                            density="compact"
                            hide-details
                            class="max-w-xs"
                        />
                        <v-btn
                            color="primary"
                            variant="tonal"
                            :loading="loading"
                            :disabled="!scenarioId"
                            @click="loadRecommendations"
                        >
                            Cargar
                        </v-btn>
                        <v-btn
                            color="success"
                            variant="elevated"
                            :loading="generating"
                            :disabled="!scenarioId"
                            prepend-icon="mdi-auto-fix"
                            @click="generateRecommendations"
                        >
                            Generar Recomendaciones
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Error alert -->
            <v-alert
                v-if="error"
                type="error"
                variant="tonal"
                closable
                @click:close="error = null"
            >
                {{ error }}
            </v-alert>

            <!-- Loading state -->
            <div v-if="loading || generating" class="flex justify-center py-12">
                <v-progress-circular indeterminate color="primary" size="48" />
            </div>

            <!-- Empty state -->
            <v-card
                v-else-if="!hasRecommendations && scenarioId"
                class="py-12 text-center dark:bg-gray-800"
                elevation="1"
            >
                <v-icon size="64" color="grey-lighten-1"
                    >mdi-lightbulb-off-outline</v-icon
                >
                <p class="mt-4 text-lg font-medium dark:text-white">
                    No hay recomendaciones aún
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Haz clic en "Generar Recomendaciones" para crearlas
                    automáticamente.
                </p>
            </v-card>

            <!-- Recommendations grid -->
            <div
                v-else-if="hasRecommendations"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
            >
                <v-card
                    v-for="rec in recommendations"
                    :key="rec.id"
                    class="dark:bg-gray-800"
                    elevation="3"
                >
                    <v-card-title
                        class="flex items-center justify-between pb-1"
                    >
                        <v-chip
                            :color="leverColor(rec.lever)"
                            variant="flat"
                            size="small"
                            class="font-bold"
                        >
                            {{ rec.lever }}
                        </v-chip>
                        <v-chip
                            color="secondary"
                            variant="outlined"
                            size="small"
                        >
                            Score: {{ rec.priority_score }}
                        </v-chip>
                    </v-card-title>

                    <v-card-text class="space-y-3">
                        <p class="text-sm leading-relaxed dark:text-gray-300">
                            {{ rec.rationale }}
                        </p>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div class="dark:text-gray-400">
                                <span class="font-semibold">Plazo:</span>
                                {{ rec.cost_estimate_weeks }} semanas
                            </div>
                            <div class="dark:text-gray-400">
                                <span class="font-semibold">Costo:</span>
                                ${{ rec.estimated_cost_usd?.toLocaleString() }}
                            </div>
                        </div>

                        <div>
                            <div
                                class="mb-1 flex justify-between text-xs dark:text-gray-400"
                            >
                                <span>Probabilidad de éxito</span>
                                <span>{{ rec.success_probability_pct }}%</span>
                            </div>
                            <v-progress-linear
                                :model-value="rec.success_probability_pct"
                                :color="
                                    rec.success_probability_pct >= 70
                                        ? 'success'
                                        : rec.success_probability_pct >= 40
                                          ? 'warning'
                                          : 'error'
                                "
                                rounded
                                height="6"
                            />
                        </div>
                    </v-card-text>
                </v-card>
            </div>
        </div>
    </AppLayout>
</template>
