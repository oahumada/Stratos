<template>
    <AppLayout v-bind="layoutProps">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2
                        class="text-3xl font-black tracking-tight text-white drop-shadow-md"
                    >
                        <span class="mr-3 text-indigo-400">📊</span>
                        Workforce Planning
                    </h2>
                    <p class="mt-2 text-sm text-white/50">
                        Visualiza escenarios de demanda de talento y recibe
                        recomendaciones automáticas
                    </p>
                </div>
            </div>
        </template>

        <div class="mt-6 space-y-6">
            <!-- KPIs Dashboard -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <StCardGlass
                    class="border-emerald-500/20 bg-emerald-500/5 transition-all duration-300 hover:border-emerald-500/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3
                            class="text-xs font-bold tracking-widest text-emerald-400 uppercase"
                        >
                            Brechas Cerradas
                        </h3>
                        <PhChartPolar class="text-emerald-400" :size="20" />
                    </div>
                    <p class="text-3xl font-black text-white">
                        {{ activeKpis?.gaps_closed_percent ?? 0 }}%
                    </p>
                </StCardGlass>
                <StCardGlass
                    class="border-indigo-500/20 bg-indigo-500/5 transition-all duration-300 hover:border-indigo-500/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3
                            class="text-xs font-bold tracking-widest text-indigo-400 uppercase"
                        >
                            Estrategias Activas
                        </h3>
                        <PhRocketLaunch class="text-indigo-400" :size="20" />
                    </div>
                    <p class="text-3xl font-black text-white">
                        {{ activeKpis?.active_strategies ?? 0 }}
                    </p>
                </StCardGlass>
                <StCardGlass
                    class="border-rose-500/20 bg-rose-500/5 transition-all duration-300 hover:border-rose-500/40"
                >
                    <div class="mb-2 flex items-center justify-between">
                        <h3
                            class="text-xs font-bold tracking-widest text-rose-400 uppercase"
                        >
                            Alertas de Riesgo
                        </h3>
                        <PhPulse class="text-rose-400" :size="20" />
                    </div>
                    <p class="text-3xl font-black text-white">
                        {{ activeKpis?.risk_alerts ?? 0 }}
                    </p>
                </StCardGlass>
            </div>

            <!-- Main Content Area -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Form: Create Scenario -->
                <StCardGlass>
                    <div class="mb-4">
                        <h3 class="mb-1 text-lg font-bold text-white">
                            <PhCompass
                                class="mr-2 inline text-indigo-300"
                                :size="20"
                            />Crear Escenario de Demanda
                        </h3>
                        <p class="text-xs text-white/40">
                            Parámetros del escenario de crecimiento u
                            optimización.
                        </p>
                    </div>

                    <form @submit.prevent="createScenario" class="space-y-4">
                        <div>
                            <label
                                class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                >Nombre</label
                            >
                            <input
                                v-model="form.name"
                                required
                                placeholder="Ej: Expansión Regional 2026"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div>
                            <label
                                class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                >Crecimiento (%)</label
                            >
                            <input
                                v-model="form.growth_percentage"
                                type="number"
                                placeholder="Ej: 15"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                            />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                    >Inicio</label
                                >
                                <input
                                    v-model="form.timeframe_start"
                                    type="date"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-1 block text-xs font-bold tracking-widest text-white/50 uppercase"
                                    >Fin</label
                                >
                                <input
                                    v-model="form.timeframe_end"
                                    type="date"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white focus:ring-2 focus:ring-indigo-500/50 focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <StButtonGlass
                                type="submit"
                                variant="primary"
                                :loading="loading"
                                class="w-full sm:w-auto"
                            >
                                Analizar Demanda
                            </StButtonGlass>
                        </div>
                    </form>
                </StCardGlass>

                <!-- Results: Recommendations -->
                <StCardGlass class="flex flex-col">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="mb-1 text-lg font-bold text-white">
                                <PhRoadHorizon
                                    class="mr-2 inline text-indigo-300"
                                    :size="20"
                                />Estrategias Recomendadas
                            </h3>
                            <p class="text-xs text-white/40">
                                Decisiones automáticas (Build, Buy, Borrow, Bot)
                            </p>
                        </div>
                        <StBadgeGlass
                            v-if="recommendations.length"
                            variant="success"
                            >Actualizado</StBadgeGlass
                        >
                    </div>

                    <div
                        v-if="loadingResults"
                        class="flex grow items-center justify-center p-8"
                    >
                        <div class="flex flex-col items-center gap-3">
                            <div
                                class="h-10 w-10 animate-spin rounded-full border-2 border-indigo-500 border-t-transparent"
                            ></div>
                            <span class="text-sm text-white/50"
                                >Calculando supply vs demand...</span
                            >
                        </div>
                    </div>
                    <div
                        v-else-if="recommendations.length"
                        class="grow space-y-3 overflow-auto pr-2"
                    >
                        <div
                            v-for="(rec, idx) in recommendations"
                            :key="idx"
                            class="flex flex-col justify-between rounded-2xl border border-white/10 bg-white/5 p-4"
                        >
                            <div class="mb-2 flex items-start justify-between">
                                <div class="font-bold text-white">
                                    {{ rec.role }}
                                </div>
                                <StBadgeGlass
                                    :variant="
                                        getBadgeByStrategy(rec.strategy_type)
                                    "
                                    >{{ rec.strategy_type }}</StBadgeGlass
                                >
                            </div>
                            <div
                                class="mb-3 flex justify-between text-xs text-white/40"
                            >
                                <span
                                    >Demanda:
                                    <strong class="text-white/80">{{
                                        rec.demand
                                    }}</strong>
                                    FTE</span
                                >
                                <span
                                    >Oferta int:
                                    <strong class="text-white/80">{{
                                        rec.internal_supply
                                    }}</strong>
                                    FTE</span
                                >
                            </div>
                            <div
                                class="mt-auto rounded-lg border border-indigo-500/20 bg-indigo-500/10 px-3 py-2 text-xs text-indigo-300"
                            >
                                ➥ {{ rec.action }}
                            </div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="flex grow flex-col items-center justify-center rounded-2xl border border-dashed border-white/5 bg-white/5 p-8 text-center"
                    >
                        <PhCompass class="mb-3 text-white/10" :size="48" />
                        <p class="text-sm text-white/40">
                            Crea un escenario a la izquierda para ver las
                            estrategias generadas por Stratos AI.
                        </p>
                    </div>
                </StCardGlass>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    PhChartPolar,
    PhCompass,
    PhPulse,
    PhRoadHorizon,
    PhRocketLaunch,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { ref } from 'vue';

const layoutProps = {
    title: 'Workforce Planning',
};

const loading = ref(false);
const loadingResults = ref(false);

const form = ref({
    name: '',
    growth_percentage: null,
    timeframe_start: '',
    timeframe_end: '',
});

const recommendations = ref<any[]>([]);
const activeKpis = ref<any>(null);

async function createScenario() {
    loading.value = true;
    try {
        const res = await axios.post(
            '/api/workforce-planning/scenarios',
            form.value,
        );
        if (res.data.scenario) {
            await fetchRecommendations(res.data.scenario.id);
        }
    } catch (err) {
        console.error('Error creating scenario', err);
    } finally {
        loading.value = false;
    }
}

async function fetchRecommendations(scenarioId: string) {
    loadingResults.value = true;
    try {
        const res = await axios.get(
            `/api/workforce-planning/scenarios/${scenarioId}/recommendations`,
        );
        if (res.data.recommendations) {
            recommendations.value = res.data.recommendations;
        }
        if (res.data.kpis) {
            activeKpis.value = res.data.kpis;
        }
    } catch (err) {
        console.error('Error fetching recommendations', err);
    } finally {
        loadingResults.value = false;
    }
}

function getBadgeByStrategy(strategy: string) {
    switch (strategy.toUpperCase()) {
        case 'BUILD':
            return 'success';
        case 'BUY':
            return 'primary';
        case 'BORROW':
            return 'warning';
        case 'BOT':
            return 'error';
        default:
            return 'glass';
    }
}
</script>

<style scoped>
/* Scoped styles */
</style>
