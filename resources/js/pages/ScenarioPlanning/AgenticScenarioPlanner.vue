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
        label: 'Expansión',
        icon: '📈',
        desc: 'Crecimiento del headcount',
    },
    {
        key: 'team_merge',
        label: 'Fusión de Equipos',
        icon: '🔗',
        desc: 'Merge de departamentos',
    },
    {
        key: 'tech_disruption',
        label: 'Disrupción Tech',
        icon: '⚡',
        desc: 'Cambio tecnológico radical',
    },
    {
        key: 'downsizing',
        label: 'Downsizing',
        icon: '📉',
        desc: 'Reducción de estructura',
    },
    {
        key: 'generic',
        label: 'Escenario Libre',
        icon: '🔮',
        desc: 'Análisis agéntico libre',
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
                impact: 'No se pudo evaluar.',
                main_risk: 'Análisis manual requerido.',
                immediate_action: 'Consultar con el equipo.',
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

function getSemaphoreColor(s: string): string {
    if (s === 'green') return 'bg-emerald-500';
    if (s === 'yellow') return 'bg-amber-500';
    return 'bg-rose-500';
}

function formatUsd(n: number): string {
    const abs = Math.abs(n);
    const fmt = '$' + Math.round(abs).toLocaleString('en-US');
    return n < 0 ? `-${fmt}` : fmt;
}
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-white">
                <span class="mr-2">🤖</span> Agentic Scenario Planner
            </h2>
            <p class="mt-1 text-sm text-white/50">
                Simulaciones autónomas con IA — El agente analiza, simula
                impacto y genera planes de acción
            </p>
        </div>

        <!-- Change Type Selector -->
        <div class="grid grid-cols-2 gap-3 md:grid-cols-5">
            <button
                v-for="ct in changeTypes"
                :key="ct.key"
                @click="
                    activeType = ct.key as ChangeType;
                    result = null;
                "
                class="rounded-xl border p-3 text-center transition-all duration-300"
                :class="
                    activeType === ct.key
                        ? 'border-indigo-500/40 bg-indigo-500/10 ring-1 ring-indigo-500/20'
                        : 'border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/8'
                "
            >
                <span class="text-2xl">{{ ct.icon }}</span>
                <p class="mt-1 text-xs font-bold text-white">{{ ct.label }}</p>
                <p class="text-[0.55rem] text-white/30">{{ ct.desc }}</p>
            </button>
        </div>

        <!-- Configuration -->
        <StCardGlass>
            <h3
                class="mb-4 text-sm font-bold tracking-widest text-white/60 uppercase"
            >
                Parámetros
            </h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <!-- Expansion Growth -->
                <div v-if="activeType === 'expansion'">
                    <label class="mb-1 block text-xs text-white/50"
                        >Crecimiento (%)</label
                    >
                    <input
                        v-model.number="growthPct"
                        type="range"
                        min="5"
                        max="100"
                        step="5"
                        class="w-full accent-indigo-500"
                    />
                    <div
                        class="mt-1 flex justify-between text-xs text-white/30"
                    >
                        <span>5%</span>
                        <span class="font-bold text-indigo-300"
                            >{{ growthPct }}%</span
                        >
                        <span>100%</span>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs text-white/50"
                        >Horizonte (meses)</label
                    >
                    <select
                        v-model.number="horizonMonths"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white"
                    >
                        <option :value="6">6 meses</option>
                        <option :value="12">12 meses</option>
                        <option :value="18">18 meses</option>
                        <option :value="24">24 meses</option>
                        <option :value="36">36 meses</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs text-white/50"
                        >Descripción del cambio</label
                    >
                    <input
                        v-model="description"
                        type="text"
                        placeholder="Describe el escenario..."
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white placeholder-white/20"
                    />
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <StButtonGlass
                    variant="primary"
                    @click="runSimulation"
                    :loading="loading"
                >
                    🤖 Ejecutar Simulación Agéntica
                </StButtonGlass>
            </div>
        </StCardGlass>

        <!-- Error -->
        <StCardGlass v-if="error" class="border-rose-500/30 bg-rose-500/5">
            <p class="text-sm text-rose-300">{{ error }}</p>
        </StCardGlass>

        <!-- Simulation Results -->
        <Transition name="fade-slide">
            <div v-if="result" class="space-y-4">
                <!-- Viability Banner -->
                <StCardGlass
                    v-if="result.viability"
                    class="overflow-hidden"
                    :class="{
                        'border-emerald-500/20':
                            result.viability.semaphore === 'green',
                        'border-amber-500/20':
                            result.viability.semaphore === 'yellow',
                        'border-rose-500/20':
                            result.viability.semaphore === 'red',
                    }"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                :class="[
                                    'h-4 w-4 animate-pulse rounded-full',
                                    getSemaphoreColor(
                                        result.viability.semaphore,
                                    ),
                                ]"
                            ></div>
                            <div>
                                <p class="text-lg font-bold text-white">
                                    Viabilidad Estratégica
                                </p>
                                <p class="text-xs text-white/50">
                                    {{ result.viability.recommendation }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p
                                :class="[
                                    'text-4xl font-black',
                                    getViabilityColor(result.viability.level),
                                ]"
                            >
                                {{ result.viability.score }}
                            </p>
                            <StBadgeGlass
                                :variant="
                                    getViabilityBadge(result.viability.level)
                                "
                                size="sm"
                            >
                                {{ result.viability.level?.toUpperCase() }}
                            </StBadgeGlass>
                        </div>
                    </div>
                </StCardGlass>

                <!-- KPI Impact -->
                <StCardGlass v-if="result.kpi_impact">
                    <h3
                        class="mb-3 text-sm font-bold tracking-widest text-white/60 uppercase"
                    >
                        <span class="mr-1">📊</span> Impacto en KPIs
                    </h3>
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p
                                class="text-2xl font-black"
                                :class="
                                    result.kpi_impact.headcount_delta >= 0
                                        ? 'text-emerald-400'
                                        : 'text-rose-400'
                                "
                            >
                                {{
                                    result.kpi_impact.headcount_delta > 0
                                        ? '+'
                                        : ''
                                }}{{ result.kpi_impact.headcount_delta }}
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Headcount Delta
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p class="text-2xl font-black text-white">
                                {{
                                    formatUsd(result.kpi_impact.cost_impact_usd)
                                }}
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Impacto Costo
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p
                                class="text-2xl font-black"
                                :class="
                                    result.kpi_impact.productivity_impact_pct >=
                                    0
                                        ? 'text-emerald-400'
                                        : 'text-rose-400'
                                "
                            >
                                {{
                                    result.kpi_impact.productivity_impact_pct >
                                    0
                                        ? '+'
                                        : ''
                                }}{{
                                    result.kpi_impact.productivity_impact_pct
                                }}%
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Productividad
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p class="text-2xl font-black text-indigo-400">
                                {{
                                    result.kpi_impact.time_to_stabilize_months
                                }}m
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Estabilización
                            </p>
                        </div>
                    </div>
                </StCardGlass>

                <!-- Simulation Detail -->
                <StCardGlass v-if="result.simulation_result">
                    <h3
                        class="mb-3 text-sm font-bold tracking-widest text-white/60 uppercase"
                    >
                        <span class="mr-1">🔍</span> Detalle de Simulación
                    </h3>
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                        <div
                            v-for="(value, key) in result.simulation_result"
                            :key="String(key)"
                            class="rounded-lg border border-white/5 bg-white/3 p-2"
                        >
                            <p
                                class="text-[0.6rem] tracking-widest text-white/30 uppercase"
                            >
                                {{ String(key).replace(/_/g, ' ') }}
                            </p>
                            <p class="mt-0.5 text-sm font-bold text-white">
                                {{
                                    typeof value === 'number'
                                        ? String(key).includes('cost') ||
                                          String(key).includes('usd')
                                            ? formatUsd(value)
                                            : value
                                        : value
                                }}
                            </p>
                        </div>
                    </div>
                </StCardGlass>

                <!-- Action Plan -->
                <StCardGlass v-if="result.action_plan?.length">
                    <h3
                        class="mb-3 text-sm font-bold tracking-widest text-white/60 uppercase"
                    >
                        <span class="mr-1">📋</span> Plan de Acción Agéntico
                    </h3>
                    <div class="relative">
                        <!-- Timeline line -->
                        <div
                            class="absolute top-0 bottom-0 left-4 w-px bg-white/10"
                        ></div>

                        <div class="space-y-4">
                            <div
                                v-for="(action, i) in result.action_plan"
                                :key="i"
                                class="relative pl-10"
                            >
                                <!-- Timeline dot -->
                                <div
                                    class="absolute top-2 left-2.5 h-3 w-3 rounded-full border-2"
                                    :class="{
                                        'border-rose-400 bg-rose-500':
                                            action.phase === 'Immediate',
                                        'border-amber-400 bg-amber-500':
                                            action.phase === 'Short-term',
                                        'border-indigo-400 bg-indigo-500':
                                            action.phase === 'Medium-term',
                                    }"
                                ></div>

                                <div
                                    class="rounded-xl border border-white/8 bg-white/3 p-3"
                                >
                                    <div class="mb-1 flex items-center gap-2">
                                        <StBadgeGlass
                                            :variant="
                                                action.phase === 'Immediate'
                                                    ? 'error'
                                                    : action.phase ===
                                                        'Short-term'
                                                      ? 'warning'
                                                      : 'primary'
                                            "
                                            size="sm"
                                        >
                                            {{ action.phase }}
                                        </StBadgeGlass>
                                        <span
                                            class="text-[0.6rem] text-white/30"
                                            >{{ action.deadline_days }}d</span
                                        >
                                    </div>
                                    <p class="text-sm text-white/80">
                                        {{ action.action }}
                                    </p>
                                    <p class="mt-1 text-[0.6rem] text-white/40">
                                        → {{ action.responsible }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </StCardGlass>
            </div>
        </Transition>

        <!-- Divider -->
        <div class="my-2 border-t border-white/5"></div>

        <!-- Quick What-If -->
        <StCardGlass>
            <div class="mb-3 flex items-center gap-2">
                <span class="text-lg">💭</span>
                <h3
                    class="text-sm font-bold tracking-widest text-white/60 uppercase"
                >
                    Quick What-If
                </h3>
            </div>
            <p class="mb-3 text-xs text-white/40">
                Haz una pregunta hipotética y la IA analizará el impacto
                rápidamente
            </p>
            <div class="flex gap-2">
                <input
                    v-model="whatIfQuestion"
                    @keyup.enter="runWhatIf"
                    placeholder="¿Qué pasaría si migramos a cloud native en 6 meses?"
                    class="flex-1 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white placeholder-white/20 focus:border-indigo-500/40 focus:outline-none"
                />
                <StButtonGlass
                    variant="primary"
                    size="sm"
                    @click="runWhatIf"
                    :loading="loadingWhatIf"
                >
                    Analizar
                </StButtonGlass>
            </div>

            <Transition name="fade-slide">
                <div
                    v-if="whatIfResult?.analysis"
                    class="mt-4 space-y-3 rounded-xl border border-white/8 bg-white/3 p-4"
                >
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-white/40">
                            Probabilidad de éxito
                        </p>
                        <span
                            class="text-lg font-black"
                            :class="
                                whatIfResult.analysis.success_probability >= 70
                                    ? 'text-emerald-400'
                                    : whatIfResult.analysis
                                            .success_probability >= 50
                                      ? 'text-amber-400'
                                      : 'text-rose-400'
                            "
                            >{{
                                whatIfResult.analysis.success_probability
                            }}%</span
                        >
                    </div>
                    <div>
                        <p
                            class="text-[0.6rem] tracking-widest text-white/30 uppercase"
                        >
                            Impacto
                        </p>
                        <p class="text-sm text-white/70">
                            {{ whatIfResult.analysis.impact }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-[0.6rem] tracking-widest text-white/30 uppercase"
                        >
                            Riesgo Principal
                        </p>
                        <p class="text-sm text-rose-300">
                            {{ whatIfResult.analysis.main_risk }}
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-[0.6rem] tracking-widest text-white/30 uppercase"
                        >
                            Acción Inmediata
                        </p>
                        <p class="text-sm text-emerald-300">
                            {{ whatIfResult.analysis.immediate_action }}
                        </p>
                    </div>
                </div>
            </Transition>
        </StCardGlass>
    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(16px);
}
select option {
    background: #1e1b4b;
    color: #fff;
}
</style>
