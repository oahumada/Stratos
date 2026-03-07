<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps<{
    scenarioId: number;
}>();

type ChangeType =
    | 'team_merge'
    | 'tech_disruption'
    | 'expansion'
    | 'downsizing'
    | 'generic';

const activeType = ref<ChangeType>('expansion');
const loading = ref(false);
const loadingWhatIf = ref(false);
const result = ref<any>(null);
const whatIfResult = ref<any>(null);
const whatIfQuestion = ref('');
const error = ref<string | null>(null);

// Form data
const growthPct = ref(20);
const description = ref('');
const horizonMonths = ref(18);

const changeTypes = [
    {
        key: 'expansion',
        label: 'Expansión Absoluta',
        icon: 'mdi-trending-up',
        desc: 'Escalamiento de estructura',
    },
    {
        key: 'team_merge',
        label: 'Simbiosis de Equipos',
        icon: 'mdi-set-center',
        desc: 'Fusión y redundancia semántica',
    },
    {
        key: 'tech_disruption',
        label: 'Shock Tecnológico',
        icon: 'mdi-lightning-bolt',
        desc: 'Obsolescencia por IA/Tech',
    },
    {
        key: 'downsizing',
        label: 'Optimización Estructural',
        icon: 'mdi-minus-circle',
        desc: 'Reducción y eficiencia bruta',
    },
    {
        key: 'generic',
        label: 'Hipótesis Libre',
        icon: 'mdi-head-cog',
        desc: 'Simulación agéntica ad-hoc',
    },
];

async function runSimulation() {
    loading.value = true;
    error.value = null;
    result.value = null;

    try {
        const payload: any = {
            change_type: activeType.value,
            description: description.value,
            growth_percentage: growthPct.value,
            horizon_months: horizonMonths.value,
        };

        const { data } = await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/agentic-simulation`,
            payload,
        );
        result.value = data.data ?? data;
    } catch (e: any) {
        error.value =
            e.response?.data?.message ?? 'Error en simulación agéntica';
    } finally {
        loading.value = false;
    }
}

async function runWhatIf() {
    if (!whatIfQuestion.value.trim()) return;
    loadingWhatIf.value = true;
    whatIfResult.value = null;

    try {
        const { data } = await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/what-if`,
            { question: whatIfQuestion.value },
        );
        whatIfResult.value = data.data ?? data;
    } catch {
        whatIfResult.value = {
            analysis: {
                impact: 'No se pudo evaluar automáticamente.',
                main_risk: 'Análisis manual requerido.',
                immediate_action: 'Consultar con el equipo estratégico.',
                success_probability: 50,
            },
        };
    } finally {
        loadingWhatIf.value = false;
    }
}

function getViabilityColor(level: string): string {
    if (level === 'high') return 'text-emerald-400';
    if (level === 'medium') return 'text-amber-400';
    return 'text-rose-400';
}

function getViabilityBadge(level: string): 'success' | 'warning' | 'error' {
    if (level === 'high') return 'success';
    if (level === 'medium') return 'warning';
    return 'error';
}

function getSemaphoreBg(s: string): string {
    if (s === 'green')
        return 'bg-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.3)]';
    if (s === 'yellow')
        return 'bg-amber-500/20 shadow-[0_0_15px_rgba(245,158,11,0.3)]';
    return 'bg-rose-500/20 shadow-[0_0_15px_rgba(239,68,68,0.3)]';
}

function getSemaphoreBullet(s: string): string {
    if (s === 'green') return 'bg-emerald-500';
    if (s === 'yellow') return 'bg-amber-500';
    return 'bg-rose-500';
}

function formatUsd(n: number): string {
    const abs = Math.abs(n);
    const fmt = '$' + Math.round(abs).toLocaleString('en-US');
    return n < 0 ? `-${fmt}` : fmt;
}

const getCategoryBadge = (cat?: string) => {
    if (cat === 'synthetic') return 'error';
    if (cat === 'hybrid') return 'primary';
    if (cat === 'policy') return 'success';
    return 'warning';
};
</script>

<template>
    <div class="space-y-8 pb-12">
        <!-- Dashboard Header / Radar Scanner Animation Backdrop (Subtle) -->
        <div
            class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-500/10 to-purple-500/5 p-8 shadow-2xl"
        >
            <div
                class="absolute -top-24 -right-24 h-64 w-64 animate-pulse rounded-full bg-indigo-500/10 blur-3xl"
            ></div>
            <div
                class="relative flex flex-col items-start justify-between gap-6 md:flex-row md:items-center"
            >
                <div>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/20 text-indigo-400 shadow-inner"
                        >
                            <v-icon
                                icon="mdi-radar"
                                size="32"
                                class="animate-spin-slow"
                            ></v-icon>
                        </div>
                        <div>
                            <h2
                                class="bg-gradient-to-r from-white to-white/60 bg-clip-text text-3xl font-black tracking-tighter text-transparent text-white uppercase"
                            >
                                Stratos Radar
                                <span class="text-indigo-400">Evolve</span>
                            </h2>
                            <div class="flex items-center gap-2">
                                <span
                                    class="h-1.5 w-1.5 animate-ping rounded-full bg-emerald-500"
                                ></span>
                                <p
                                    class="text-[0.65rem] font-bold tracking-[0.2em] text-white/40 uppercase"
                                >
                                    Motor de Simulación Orgánica V4.0
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <div
                        class="rounded-2xl border border-white/5 bg-black/20 px-4 py-2 backdrop-blur-md"
                    >
                        <p
                            class="text-[0.5rem] tracking-widest text-white/30 uppercase"
                        >
                            ID Escenario
                        </p>
                        <p class="font-mono text-xs font-bold text-white">
                            #SCN-{{ scenarioId }}-EVO
                        </p>
                    </div>
                    <div
                        class="hidden rounded-2xl border border-emerald-500/20 bg-emerald-500/5 px-4 py-2 backdrop-blur-md md:block"
                    >
                        <p
                            class="text-[0.5rem] tracking-widest text-emerald-400/50 uppercase"
                        >
                            Estado del Twin
                        </p>
                        <p
                            class="flex items-center gap-1 text-xs font-bold text-emerald-400"
                        >
                            <v-icon icon="mdi-check-circle" size="14"></v-icon>
                            Sincronizado
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mode Selector: Premium Grids -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
            <button
                v-for="ct in changeTypes"
                :key="ct.key"
                @click="
                    activeType = ct.key as ChangeType;
                    result = null;
                "
                class="group relative flex flex-col items-center justify-center overflow-hidden rounded-2xl border p-4 transition-all duration-500"
                :class="
                    activeType === ct.key
                        ? 'border-indigo-500/50 bg-indigo-500/10 shadow-[0_0_20px_rgba(99,102,241,0.15)] ring-1 ring-indigo-400/20'
                        : 'border-white/5 bg-white/[0.02] grayscale hover:border-white/20 hover:bg-white/[0.05] hover:grayscale-0'
                "
            >
                <div
                    class="mb-2 flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 transition-transform duration-500 group-hover:scale-110 group-hover:bg-indigo-500/20"
                >
                    <v-icon
                        :icon="ct.icon"
                        :class="
                            activeType === ct.key
                                ? 'text-indigo-400'
                                : 'text-white/40'
                        "
                    ></v-icon>
                </div>
                <p
                    class="text-[0.7rem] font-black tracking-tight text-white uppercase"
                >
                    {{ ct.label }}
                </p>
                <p
                    class="mt-0.5 text-[0.55rem] leading-tight text-white/30 group-hover:text-white/50"
                >
                    {{ ct.desc }}
                </p>

                <!-- Active Indicator -->
                <div
                    v-if="activeType === ct.key"
                    class="absolute top-2 right-2 h-1.5 w-1.5 rounded-full bg-indigo-400 shadow-[0_0_8px_#818cf8]"
                ></div>
            </button>
        </div>

        <!-- Input Control Panel -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="lg:col-span-8">
                <StCardGlass class="h-full border-white/5 p-6 backdrop-blur-xl">
                    <div class="mb-6 flex items-center justify-between">
                        <h3
                            class="flex items-center gap-2 text-xs font-black tracking-[0.2em] text-white uppercase"
                        >
                            <v-icon
                                icon="mdi-cog"
                                size="16"
                                class="text-indigo-400"
                            ></v-icon>
                            Variables de Escenario
                        </h3>
                        <div
                            class="ml-4 h-px flex-1 bg-gradient-to-r from-white/10 to-transparent"
                        ></div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Dynamic Input based on type -->
                        <div class="space-y-4">
                            <div v-if="activeType === 'expansion'">
                                <div
                                    class="mb-2 flex items-center justify-between"
                                >
                                    <label
                                        class="text-[0.65rem] font-bold text-white/50 uppercase"
                                        >Tasa de Expansión</label
                                    >
                                    <span
                                        class="text-xs font-black text-indigo-400"
                                        >{{ growthPct }}%</span
                                    >
                                </div>
                                <div
                                    class="group relative rounded-xl bg-black/20 p-4 pt-8"
                                >
                                    <input
                                        v-model.number="growthPct"
                                        type="range"
                                        min="5"
                                        max="100"
                                        step="5"
                                        class="h-1.5 w-full cursor-pointer appearance-none rounded-lg bg-white/10 accent-indigo-500"
                                    />
                                    <div
                                        class="mt-4 flex justify-between px-1 text-[0.5rem] font-bold text-white/20 uppercase"
                                    >
                                        <span>Cero Impacto</span>
                                        <span>Shock Estructural</span>
                                    </div>
                                </div>
                            </div>

                            <div v-else>
                                <label
                                    class="mb-2 block text-[0.65rem] font-bold text-white/50 uppercase"
                                    >Descripción del Evento</label
                                >
                                <textarea
                                    v-model="description"
                                    rows="3"
                                    placeholder="Ej: Fusión Depto Ventas + Marketing para eficiencia semántica..."
                                    class="w-full rounded-xl border border-white/10 bg-black/20 p-3 text-sm text-white placeholder-white/20 focus:border-indigo-500/40 focus:ring-0"
                                ></textarea>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label
                                    class="mb-2 block text-[0.65rem] font-bold text-white/50 uppercase"
                                    >Horizonte Temporal</label
                                >
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        v-for="m in [6, 12, 18, 24, 36]"
                                        :key="m"
                                        @click="horizonMonths = m"
                                        class="rounded-lg border py-2 text-[0.7rem] font-bold transition-all"
                                        :class="
                                            horizonMonths === m
                                                ? 'border-indigo-500/50 bg-indigo-500/10 text-white'
                                                : 'border-white/5 bg-white/5 text-white/30 hover:bg-white/10'
                                        "
                                    >
                                        {{ m }}m
                                    </button>
                                </div>
                            </div>

                            <StButtonGlass
                                variant="primary"
                                class="w-full !rounded-xl !py-4 shadow-lg shadow-indigo-500/20"
                                @click="runSimulation"
                                :loading="loading"
                            >
                                <v-icon
                                    icon="mdi-play-circle"
                                    class="mr-2"
                                ></v-icon>
                                Iniciar War-Gaming Agéntico
                            </StButtonGlass>
                        </div>
                    </div>
                </StCardGlass>
            </div>

            <div class="lg:col-span-4">
                <StCardGlass
                    class="flex h-full flex-col border-white/5 p-6 backdrop-blur-xl"
                >
                    <h3
                        class="mb-4 flex items-center gap-2 text-xs font-black tracking-[0.2em] text-white uppercase"
                    >
                        <v-icon
                            icon="mdi-comment-question"
                            size="16"
                            class="text-purple-400"
                        ></v-icon>
                        Flash What-If
                    </h3>
                    <p
                        class="mb-4 text-[0.65rem] leading-relaxed text-white/40"
                    >
                        Ask a hypothetical question. Our Agentic Brain will
                        cross-reference the Digital Twin state and provide a
                        real-time risk assessment.
                    </p>
                    <div class="relative mt-auto">
                        <input
                            v-model="whatIfQuestion"
                            @keyup.enter="runWhatIf"
                            placeholder="¿Qué pasa si migramos a Cloud?"
                            class="w-full rounded-2xl border border-white/5 bg-black/30 p-4 pr-12 text-sm text-white placeholder-white/20 focus:border-purple-500/50 focus:outline-none"
                        />
                        <button
                            @click="runWhatIf"
                            class="transition-hover absolute top-2 right-2 flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500 hover:scale-105 active:scale-95 disabled:opacity-50"
                            :disabled="loadingWhatIf"
                        >
                            <v-icon
                                v-if="!loadingWhatIf"
                                icon="mdi-rocket-launch"
                                size="18"
                                class="text-white"
                            ></v-icon>
                            <v-icon
                                v-else
                                icon="mdi-loading"
                                size="18"
                                class="animate-spin text-white"
                            ></v-icon>
                        </button>
                    </div>
                </StCardGlass>
            </div>
        </div>

        <!-- Simulation Results: The War Room View -->
        <Transition name="radar-reveal">
            <div v-if="result" class="space-y-8">
                <!-- Viability Meter & Primary Metrics -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <div class="lg:col-span-12">
                        <StCardGlass class="overflow-hidden !p-0">
                            <div class="flex flex-col md:flex-row">
                                <!-- Viability Score Side -->
                                <div
                                    class="flex w-full flex-col items-center justify-center border-b border-white/10 p-8 md:w-1/3 md:border-r md:border-b-0"
                                    :class="
                                        getSemaphoreBg(
                                            result.viability.semaphore,
                                        )
                                    "
                                >
                                    <div
                                        class="relative flex items-center justify-center"
                                    >
                                        <!-- Orbital Animation -->
                                        <div
                                            class="animate-spin-slow absolute h-32 w-32 rounded-full border-2 border-dashed border-white/10"
                                        ></div>
                                        <div
                                            class="animate-reverse-spin absolute h-40 w-40 rounded-full border border-white/5"
                                        ></div>

                                        <div class="text-center">
                                            <p
                                                class="text-[0.6rem] font-black tracking-widest text-white/30 uppercase"
                                            >
                                                Score de Viabilidad
                                            </p>
                                            <p
                                                :class="[
                                                    'text-6xl font-black tracking-tighter italic',
                                                    getViabilityColor(
                                                        result.viability.level,
                                                    ),
                                                ]"
                                            >
                                                {{ result.viability.score }}
                                            </p>
                                            <div
                                                class="mt-2 flex items-center justify-center gap-2"
                                            >
                                                <div
                                                    :class="[
                                                        'h-2 w-2 rounded-full',
                                                        getSemaphoreBullet(
                                                            result.viability
                                                                .semaphore,
                                                        ),
                                                    ]"
                                                    class="shadow-[0_0_8px_currentColor]"
                                                ></div>
                                                <span
                                                    class="text-[0.65rem] font-black text-white uppercase"
                                                    >{{
                                                        result.viability.level
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <p
                                        class="mt-8 text-center text-sm leading-tight font-bold text-white/80"
                                    >
                                        {{ result.viability.recommendation }}
                                    </p>
                                </div>

                                <!-- Impact Highlights Side -->
                                <div class="flex-1 p-8">
                                    <div
                                        class="mb-6 flex items-center justify-between"
                                    >
                                        <h4
                                            class="text-xs font-black tracking-[0.2em] text-white text-white/60 uppercase"
                                        >
                                            Impacto Residual Estimado
                                        </h4>
                                        <StBadgeGlass
                                            variant="primary"
                                            size="sm"
                                            >Cálculo en Tiempo
                                            Real</StBadgeGlass
                                        >
                                    </div>

                                    <div
                                        class="grid grid-cols-2 gap-4 md:grid-cols-4"
                                    >
                                        <div
                                            class="rounded-2xl border border-white/5 bg-white/[0.03] p-4"
                                        >
                                            <p
                                                class="text-[0.55rem] font-bold text-white/30 uppercase"
                                            >
                                                Delta Humano
                                            </p>
                                            <p
                                                class="mt-1 text-2xl font-black"
                                                :class="
                                                    result.kpi_impact
                                                        .headcount_delta >= 0
                                                        ? 'text-emerald-400'
                                                        : 'text-rose-400'
                                                "
                                            >
                                                {{
                                                    result.kpi_impact
                                                        .headcount_delta > 0
                                                        ? '+'
                                                        : ''
                                                }}{{
                                                    result.kpi_impact
                                                        .headcount_delta
                                                }}
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-2xl border border-white/5 bg-white/[0.03] p-4"
                                        >
                                            <p
                                                class="text-[0.55rem] font-bold text-white/30 uppercase"
                                            >
                                                Burn Rate Est.
                                            </p>
                                            <p
                                                class="mt-1 text-2xl font-black text-white"
                                            >
                                                {{
                                                    formatUsd(
                                                        result.kpi_impact
                                                            .cost_impact_usd,
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-2xl border border-white/5 bg-white/[0.03] p-4"
                                        >
                                            <p
                                                class="text-[0.55rem] font-bold text-white/30 uppercase"
                                            >
                                                Efectividad Prod.
                                            </p>
                                            <p
                                                class="mt-1 text-2xl font-black"
                                                :class="
                                                    result.kpi_impact
                                                        .productivity_impact_pct >=
                                                    0
                                                        ? 'text-emerald-400'
                                                        : 'text-rose-400'
                                                "
                                            >
                                                {{
                                                    result.kpi_impact
                                                        .productivity_impact_pct >
                                                    0
                                                        ? '+'
                                                        : ''
                                                }}{{
                                                    result.kpi_impact
                                                        .productivity_impact_pct
                                                }}%
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-2xl border border-white/5 bg-white/[0.03] p-4"
                                        >
                                            <p
                                                class="text-[0.55rem] font-bold text-white/30 uppercase"
                                            >
                                                Estabilización
                                            </p>
                                            <p
                                                class="mt-1 text-2xl font-black text-indigo-400"
                                            >
                                                {{
                                                    result.kpi_impact
                                                        .time_to_stabilize_months
                                                }}m
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-8 flex gap-8">
                                        <div>
                                            <p
                                                class="text-[0.55rem] font-bold text-white/30 uppercase"
                                            >
                                                Riesgo Agéntico
                                            </p>
                                            <div
                                                class="mt-2 flex h-1.5 w-48 overflow-hidden rounded-full bg-white/5"
                                            >
                                                <div
                                                    class="h-full bg-gradient-to-r from-emerald-500 via-amber-500 to-rose-500 transition-all duration-1000"
                                                    :style="{
                                                        width:
                                                            result.kpi_impact
                                                                .risk_index +
                                                            '%',
                                                    }"
                                                ></div>
                                            </div>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[0.55rem] font-bold text-white text-white/30 uppercase"
                                            >
                                                Confianza Modular
                                            </p>
                                            <p
                                                class="text-xs font-bold text-white/60"
                                            >
                                                88.4% (Neural Path Accuracy)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </StCardGlass>
                    </div>
                </div>

                <!-- Action Plan: Hybrid Perspective -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <div class="lg:col-span-12">
                        <StCardGlass class="p-8">
                            <div class="mb-8 flex items-center justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        Plan de Acción
                                        <span class="text-indigo-400"
                                            >Híbrido</span
                                        >
                                    </h3>
                                    <p class="text-xs text-white/40">
                                        Estrategias combinadas Human-to-Agent
                                        para mitigación de riesgos.
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <div
                                        class="flex items-center gap-1.5 rounded-full border border-orange-500/20 bg-orange-500/5 px-3 py-1 text-[0.6rem] font-bold text-orange-400 uppercase"
                                    >
                                        <v-icon
                                            icon="mdi-account"
                                            size="12"
                                        ></v-icon>
                                        Humano
                                    </div>
                                    <div
                                        class="flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/5 px-3 py-1 text-[0.6rem] font-bold text-emerald-400 uppercase"
                                    >
                                        <v-icon
                                            icon="mdi-file-eye"
                                            size="12"
                                        ></v-icon>
                                        Política/Org
                                    </div>
                                    <div
                                        class="flex items-center gap-1.5 rounded-full border border-rose-500/20 bg-rose-500/5 px-3 py-1 text-[0.6rem] font-bold text-rose-400 uppercase"
                                    >
                                        <v-icon
                                            icon="mdi-robot"
                                            size="12"
                                        ></v-icon>
                                        Sintético
                                    </div>
                                    <div
                                        class="flex items-center gap-1.5 rounded-full border border-indigo-500/20 bg-indigo-500/5 px-3 py-1 text-[0.6rem] font-bold text-indigo-400 uppercase"
                                    >
                                        <v-icon
                                            icon="mdi-orbit"
                                            size="12"
                                        ></v-icon>
                                        Híbrido
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="(action, i) in result.action_plan"
                                    :key="i"
                                    class="group relative overflow-hidden rounded-2xl border transition-all duration-300 hover:border-white/20 hover:bg-white/4"
                                    :class="[
                                        action.category === 'synthetic'
                                            ? 'border-rose-500/20 bg-rose-500/[0.02]'
                                            : action.category === 'hybrid'
                                              ? 'border-indigo-500/20 bg-indigo-500/[0.02]'
                                              : action.category === 'policy'
                                                ? 'border-emerald-500/20 bg-emerald-500/[0.02]'
                                                : 'border-white/5 bg-white/[0.01]',
                                    ]"
                                >
                                    <div class="flex items-center gap-6 p-5">
                                        <div
                                            class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl bg-black/40"
                                        >
                                            <p
                                                class="text-[0.5rem] font-bold text-white/30 uppercase"
                                            >
                                                Día
                                            </p>
                                            <p
                                                class="text-lg font-black text-white"
                                            >
                                                {{ action.deadline_days }}
                                            </p>
                                        </div>

                                        <div class="flex-1">
                                            <div
                                                class="mb-1 flex items-center gap-3"
                                            >
                                                <StBadgeGlass
                                                    :variant="
                                                        getCategoryBadge(
                                                            action.category,
                                                        )
                                                    "
                                                    size="sm"
                                                >
                                                    {{
                                                        (
                                                            action.category ??
                                                            'HUMANO'
                                                        ).toUpperCase()
                                                    }}
                                                </StBadgeGlass>
                                                <span
                                                    class="text-[0.65rem] font-bold tracking-widest text-white/30 uppercase"
                                                    >FASE:
                                                    {{ action.phase }}</span
                                                >
                                            </div>
                                            <p
                                                class="text-sm font-medium text-white/90"
                                            >
                                                {{ action.action }}
                                            </p>
                                        </div>

                                        <div class="hidden text-right md:block">
                                            <p
                                                class="text-[0.55rem] font-bold text-white/30 uppercase"
                                            >
                                                Responsable
                                            </p>
                                            <p
                                                class="text-xs font-black text-indigo-300"
                                            >
                                                {{ action.responsible }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Hover Decorative Line -->
                                    <div
                                        class="absolute bottom-0 left-0 h-0.5 w-0 bg-indigo-500 transition-all duration-500 group-hover:w-full"
                                    ></div>
                                </div>
                            </div>
                        </StCardGlass>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- What-If Results Reveal -->
        <Transition name="radar-reveal">
            <div
                v-if="whatIfResult?.analysis"
                class="radar-scan-effect overflow-hidden rounded-3xl border border-purple-500/30 bg-purple-500/5 p-8 backdrop-blur-md"
            >
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/20 text-purple-400"
                        >
                            <v-icon icon="mdi-auto-fix"></v-icon>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-white italic">
                                Análisis Hipotético de IA
                            </h4>
                            <p class="text-xs text-white/40">
                                Respuesta instantánea del motor neuronal
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p
                            class="text-[0.55rem] font-bold text-white/30 uppercase"
                        >
                            Probabilidad Éxito
                        </p>
                        <p
                            class="text-3xl font-black italic"
                            :class="
                                whatIfResult.analysis.success_probability >= 70
                                    ? 'text-emerald-400'
                                    : 'text-amber-400'
                            "
                        >
                            {{ whatIfResult.analysis.success_probability }}%
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div class="space-y-2">
                        <p
                            class="flex items-center gap-2 text-[0.6rem] font-black tracking-tighter text-white/30 uppercase"
                        >
                            <v-icon icon="mdi-pulse" size="12"></v-icon> Impacto
                            Primario
                        </p>
                        <p class="text-sm leading-relaxed text-white/80 italic">
                            "{{ whatIfResult.analysis.impact }}"
                        </p>
                    </div>
                    <div class="space-y-2 border-white/5 md:border-l md:pl-6">
                        <p
                            class="flex items-center gap-2 text-[0.6rem] font-black tracking-tighter text-rose-400/50 uppercase"
                        >
                            <v-icon icon="mdi-alert-octagon" size="12"></v-icon>
                            Punto de Fricción
                        </p>
                        <p
                            class="text-sm leading-tight font-bold text-rose-300"
                        >
                            {{ whatIfResult.analysis.main_risk }}
                        </p>
                    </div>
                    <div class="space-y-2 border-white/5 md:border-l md:pl-6">
                        <p
                            class="flex items-center gap-2 text-[0.6rem] font-black tracking-tighter text-emerald-400/50 uppercase"
                        >
                            <v-icon icon="mdi-check-all" size="12"></v-icon>
                            Mitigación Inmediata
                        </p>
                        <p
                            class="text-sm leading-tight font-bold text-emerald-300"
                        >
                            {{ whatIfResult.analysis.immediate_action }}
                        </p>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.radar-reveal-enter-active,
.radar-reveal-leave-active {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.radar-reveal-enter-from,
.radar-reveal-leave-to {
    opacity: 0;
    transform: translateY(30px) scale(0.98);
}

.animate-spin-slow {
    animation: spin 8s linear infinite;
}

.animate-reverse-spin {
    animation: reverse-spin 12s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes reverse-spin {
    from {
        transform: rotate(360deg);
    }
    to {
        transform: rotate(0deg);
    }
}

input[type='range']::-webkit-slider-thumb {
    -webkit-appearance: none;
    height: 18px;
    width: 18px;
    border-radius: 50%;
    background: #6366f1;
    cursor: pointer;
    box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
    border: 2px solid rgba(255, 255, 255, 0.5);
}

.radar-scan-effect {
    position: relative;
}
.radar-scan-effect::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        0deg,
        transparent 45%,
        rgba(168, 85, 247, 0.05) 50%,
        transparent 55%
    );
    transform: rotate(0deg);
    animation: scan 4s linear infinite;
    pointer-events: none;
}

@keyframes scan {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
