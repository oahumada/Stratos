<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    PhBrain,
    PhChatTeardropDots,
    PhCheckCircle,
    PhCurrencyDollar,
    PhFire,
    PhSparkle,
    PhTarget,
    PhWarning,
    PhX,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import StBadgeGlass from '../../components/StBadgeGlass.vue';
import StButtonGlass from '../../components/StButtonGlass.vue';
import StCardGlass from '../../components/StCardGlass.vue';

const pulses = ref<any[]>([]);
const heatmapData = ref<any[]>([]);
const loading = ref(true);

const fetchData = async () => {
    loading.value = true;
    try {
        const [pulsesRes, heatmapRes] = await Promise.all([
            axios.get('/api/people-experience/employee-pulses'),
            axios.get('/api/people-experience/turnover-heatmap'),
        ]);
        pulses.value = pulsesRes.data.data;
        heatmapData.value = heatmapRes.data.data;
    } catch (e) {
        console.error('Error fetching data:', e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchData();
});

const highRiskPulses = computed(() => {
    return pulses.value.filter((p) => p.ai_turnover_risk === 'high');
});

const averageEnps = computed(() => {
    if (pulses.value.length === 0) return 0;
    const total = pulses.value.reduce((acc, p) => acc + (p.e_nps || 0), 0);
    return (total / pulses.value.length).toFixed(1);
});

const getRiskVariant = (risk: string) => {
    switch (risk) {
        case 'high':
            return 'error';
        case 'medium':
            return 'warning';
        default:
            return 'success';
    }
};

// Retention Plan Modal
const showPlanModal = ref(false);
const selectedPerson = ref<any>(null);
const detailedPlan = ref<any>(null);
const loadingPlan = ref(false);

const openRetentionPlan = async (person: any) => {
    selectedPerson.value = person;
    showPlanModal.value = true;
    loadingPlan.value = true;
    detailedPlan.value = null;

    try {
        const { data } = await axios.get(
            `/api/retention-forecast/${person.id}`,
        );
        detailedPlan.value = data.data;
    } catch (e) {
        console.error('Error fetching retention plan:', e);
    } finally {
        loadingPlan.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-950 p-8 text-white">
        <Head title="Comando Px - Alerta Temprana" />

        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-12 flex items-center justify-between">
                <div>
                    <h1
                        class="flex items-center gap-4 text-4xl font-black tracking-tight text-white"
                    >
                        <PhBrain
                            :size="40"
                            class="text-indigo-400"
                            weight="duotone"
                        />
                        Comando
                        <span
                            class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent"
                            >People Experience</span
                        >
                    </h1>
                    <p class="mt-2 font-medium text-white/40">
                        Panel de Control de Salud Organizacional y Alerta
                        Temprana de IA.
                    </p>
                </div>

                <div class="flex gap-4">
                    <div
                        class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-center backdrop-blur-xl"
                    >
                        <div
                            class="mb-1 text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            eNPS Promedio
                        </div>
                        <div class="text-2xl font-black text-emerald-400">
                            {{ averageEnps }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats & Critical Alerts -->
            <div class="mb-12 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Critical Alerts Card -->
                <StCardGlass
                    class="border-rose-500/20 bg-rose-500/5 p-8 lg:col-span-1"
                >
                    <div class="mb-6 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/20 text-rose-400"
                        >
                            <PhWarning :size="24" weight="fill" />
                        </div>
                        <h2 class="text-xl font-bold">Riesgo Crítico</h2>
                    </div>

                    <div v-if="highRiskPulses.length > 0" class="space-y-4">
                        <div
                            v-for="pulse in highRiskPulses"
                            :key="pulse.id"
                            class="group rounded-2xl border border-white/10 bg-white/5 p-4 transition-all hover:border-rose-500/30"
                        >
                            <div class="mb-2 flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500/20 text-xs font-bold text-indigo-300"
                                >
                                    {{
                                        (
                                            pulse.person?.first_name || '?'
                                        ).charAt(0)
                                    }}
                                </div>
                                <span
                                    class="font-bold text-white transition-colors group-hover:text-rose-300"
                                    >{{ pulse.person?.first_name }}
                                    {{ pulse.person?.last_name }}</span
                                >
                            </div>
                            <p
                                class="line-clamp-2 text-xs text-white/40 italic"
                            >
                                "{{ pulse.ai_turnover_reason }}"
                            </p>
                        </div>
                    </div>
                    <div v-else class="py-8 text-center">
                        <PhCheckCircle
                            :size="48"
                            class="mx-auto mb-4 text-emerald-500/20"
                        />
                        <p class="text-sm text-white/30">
                            No se detectan riesgos críticos por el momento.
                        </p>
                    </div>
                </StCardGlass>

                <!-- Activity Feed -->
                <StCardGlass class="p-8 lg:col-span-2">
                    <div class="mb-8 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-400"
                            >
                                <PhChatTeardropDots
                                    :size="24"
                                    weight="duotone"
                                />
                            </div>
                            <h2 class="text-xl font-bold">
                                Feedback en Tiempo Real
                            </h2>
                        </div>
                        <button
                            class="text-xs font-black tracking-widest text-white/30 uppercase transition-colors hover:text-white"
                            @click="fetchData"
                        >
                            Actualizar
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="border-b border-white/5 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >
                                    <th class="px-2 pb-4">Colaborador</th>
                                    <th class="px-2 pb-4">Pulso (eNPS)</th>
                                    <th class="px-2 pb-4">Estrés</th>
                                    <th class="px-2 pb-4">IA Predictor</th>
                                    <th class="px-2 pb-4 text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="pulse in pulses"
                                    :key="pulse.id"
                                    class="group border-b border-white/5 transition-all hover:bg-white/[0.02]"
                                >
                                    <td class="px-2 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/5 text-xs font-bold"
                                            >
                                                {{
                                                    (
                                                        pulse.person
                                                            ?.first_name || '?'
                                                    ).charAt(0)
                                                }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold">
                                                    {{
                                                        pulse.person?.first_name
                                                    }}
                                                </div>
                                                <div
                                                    class="text-[10px] text-white/30"
                                                >
                                                    {{
                                                        pulse.person?.role_name
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-2 w-12 overflow-hidden rounded-full bg-white/5"
                                            >
                                                <div
                                                    class="h-full bg-emerald-500"
                                                    :style="{
                                                        width:
                                                            pulse.e_nps * 10 +
                                                            '%',
                                                    }"
                                                ></div>
                                            </div>
                                            <span class="font-bold">{{
                                                pulse.e_nps
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 py-4">
                                        <StBadgeGlass
                                            :variant="
                                                pulse.stress_level > 3
                                                    ? 'error'
                                                    : 'glass'
                                            "
                                            size="sm"
                                        >
                                            {{ pulse.stress_level }}/5
                                        </StBadgeGlass>
                                    </td>
                                    <td class="px-2 py-4">
                                        <StBadgeGlass
                                            :variant="
                                                getRiskVariant(
                                                    pulse.ai_turnover_risk,
                                                )
                                            "
                                            size="sm"
                                        >
                                            {{
                                                pulse.ai_turnover_risk ||
                                                'Analizando...'
                                            }}
                                        </StBadgeGlass>
                                    </td>
                                    <td
                                        class="px-2 py-4 text-right text-xs text-white/30"
                                    >
                                        {{
                                            new Date(
                                                pulse.created_at,
                                            ).toLocaleDateString()
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </StCardGlass>
            </div>

            <!-- Heatmap Section -->
            <div class="mb-12">
                <div class="mb-8 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-500/20 text-orange-400"
                    >
                        <PhFire :size="24" weight="fill" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">
                            Heatmap de Rotación Profunda
                        </h2>
                        <p class="text-xs text-white/40">
                            Basado en Sentimiento, Tenencia de Rol e IA
                            Predictive Tracker.
                        </p>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        v-for="person in heatmapData"
                        :key="person.id"
                        class="relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl transition-all hover:border-white/20"
                        :class="{
                            'border-rose-500/30 bg-rose-500/5':
                                person.risk_score > 70,
                        }"
                    >
                        <!-- Heat Intensity Background -->
                        <div
                            class="absolute top-0 right-0 -mt-16 -mr-16 h-32 w-32 rounded-full opacity-10 blur-3xl transition-opacity group-hover:opacity-20"
                            :class="[
                                person.risk_score > 70
                                    ? 'bg-rose-500'
                                    : person.risk_score > 40
                                      ? 'bg-amber-500'
                                      : 'bg-emerald-500',
                            ]"
                        ></div>

                        <div class="relative z-10">
                            <div class="mb-4 flex items-start justify-between">
                                <div>
                                    <div class="text-sm font-black text-white">
                                        {{ person.name }}
                                    </div>
                                    <div
                                        class="text-[10px] tracking-widest text-white/40 uppercase"
                                    >
                                        {{ person.department }}
                                    </div>
                                </div>
                                <div
                                    class="text-lg font-black"
                                    :class="[
                                        person.risk_score > 70
                                            ? 'text-rose-400'
                                            : person.risk_score > 40
                                              ? 'text-amber-400'
                                              : 'text-emerald-400',
                                    ]"
                                >
                                    {{ person.risk_score }}%
                                </div>
                            </div>

                            <div class="mb-4 space-y-2">
                                <div class="flex justify-between text-[10px]">
                                    <span
                                        class="font-bold tracking-tighter text-white/30 uppercase"
                                        >Primary Driver</span
                                    >
                                    <span class="font-medium text-white">{{
                                        person.primary_driver
                                    }}</span>
                                </div>
                                <div class="flex justify-between text-[10px]">
                                    <span
                                        class="font-bold tracking-tighter text-white/30 uppercase"
                                        >Impacto (ROI)</span
                                    >
                                    <span class="font-medium text-white"
                                        >${{
                                            person.financial_impact.toLocaleString()
                                        }}</span
                                    >
                                </div>
                            </div>

                            <!-- Visualization Bar -->
                            <div
                                class="h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                            >
                                <div
                                    class="h-full transition-all duration-1000"
                                    :class="[
                                        person.risk_score > 70
                                            ? 'bg-rose-500'
                                            : person.risk_score > 40
                                              ? 'bg-amber-500'
                                              : 'bg-emerald-500',
                                    ]"
                                    :style="{ width: person.risk_score + '%' }"
                                ></div>
                            </div>

                            <!-- Action Button -->
                            <div class="mt-6 flex justify-end">
                                <StButtonGlass
                                    variant="glass"
                                    size="sm"
                                    class="w-full text-[10px] font-black tracking-widest uppercase"
                                    @click="openRetentionPlan(person)"
                                >
                                    <PhSparkle class="mr-2" :size="14" />
                                    Generar Plan IA
                                </StButtonGlass>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Retention Plan Modal Overlay -->
            <div
                v-if="showPlanModal"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-md"
            >
                <StCardGlass
                    class="max-h-[90vh] w-full max-w-2xl overflow-y-auto border-white/20 p-0"
                >
                    <div
                        class="sticky top-0 z-20 flex items-center justify-between border-b border-white/10 bg-slate-900/50 p-6 backdrop-blur-xl"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/20 text-indigo-400"
                            >
                                <PhBrain :size="28" weight="duotone" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold">
                                    Plan de Retención IA
                                </h3>
                                <p class="text-xs text-white/40">
                                    Estrategia personalizada para
                                    {{ selectedPerson?.name }}
                                </p>
                            </div>
                        </div>
                        <button
                            @click="showPlanModal = false"
                            class="p-2 text-white/40 transition-colors hover:text-white"
                        >
                            <PhX :size="24" />
                        </button>
                    </div>

                    <div class="p-8">
                        <div v-if="loadingPlan" class="py-20 text-center">
                            <div
                                class="mx-auto mb-4 h-12 w-12 animate-spin rounded-full border-4 border-indigo-500/20 border-t-indigo-500"
                            ></div>
                            <p class="text-sm font-medium text-white/40 italic">
                                Stratos Córtex está analizando variables de
                                retención...
                            </p>
                        </div>

                        <div
                            v-else-if="detailedPlan"
                            class="animate-in space-y-8 fade-in slide-in-from-bottom-4"
                        >
                            <!-- High Level Insights -->
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="rounded-2xl border border-white/10 bg-white/5 p-4"
                                >
                                    <div
                                        class="mb-2 flex items-center gap-2 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        <PhTarget :size="14" /> Riesgo de Fuga
                                    </div>
                                    <div
                                        class="text-2xl font-black"
                                        :class="
                                            getRiskVariant(
                                                detailedPlan.risk_level,
                                            ) === 'error'
                                                ? 'text-rose-400'
                                                : 'text-amber-400'
                                        "
                                    >
                                        {{ detailedPlan.flight_risk_score }}%
                                    </div>
                                </div>
                                <div
                                    class="rounded-2xl border border-white/10 bg-white/5 p-4"
                                >
                                    <div
                                        class="mb-2 flex items-center gap-2 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        <PhCurrencyDollar :size="14" /> Impacto
                                        ROI
                                    </div>
                                    <div class="text-2xl font-black text-white">
                                        ${{
                                            detailedPlan.financial_impact.replacement_cost_usd.toLocaleString()
                                        }}
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Strategic Metrics -->
                            <div class="grid grid-cols-3 gap-3">
                                <div
                                    class="rounded-xl border border-white/5 bg-white/5 p-3"
                                >
                                    <div
                                        class="mb-1 text-[8px] font-black tracking-widest text-white/20 uppercase"
                                    >
                                        Market Value
                                    </div>
                                    <div
                                        class="text-xs font-bold"
                                        :class="
                                            detailedPlan.strategic_metrics
                                                .market_position === 'Underpaid'
                                                ? 'text-rose-400'
                                                : 'text-emerald-400'
                                        "
                                    >
                                        {{
                                            detailedPlan.strategic_metrics
                                                .market_position
                                        }}
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl border border-white/5 bg-white/5 p-3"
                                >
                                    <div
                                        class="mb-1 text-[8px] font-black tracking-widest text-white/20 uppercase"
                                    >
                                        Continuity Risk
                                    </div>
                                    <div
                                        class="text-xs font-bold"
                                        :class="
                                            detailedPlan.strategic_metrics
                                                .business_continuity_risk ===
                                            'Critical'
                                                ? 'animate-pulse text-rose-500'
                                                : 'text-blue-400'
                                        "
                                    >
                                        {{
                                            detailedPlan.strategic_metrics
                                                .business_continuity_risk
                                        }}
                                    </div>
                                </div>
                                <div
                                    class="rounded-xl border border-white/5 bg-white/5 p-3"
                                >
                                    <div
                                        class="mb-1 text-[8px] font-black tracking-widest text-white/20 uppercase"
                                    >
                                        Leadership Friction
                                    </div>
                                    <div
                                        class="text-xs font-bold"
                                        :class="
                                            detailedPlan.strategic_metrics
                                                .leadership_friction === 'High'
                                                ? 'text-rose-400'
                                                : 'text-emerald-400'
                                        "
                                    >
                                        {{
                                            detailedPlan.strategic_metrics
                                                .leadership_friction
                                        }}
                                    </div>
                                </div>
                            </div>

                            <!-- Driver Analysis & Simulation -->
                            <div
                                class="relative overflow-hidden rounded-2xl border border-indigo-500/20 bg-indigo-500/10 p-6"
                            >
                                <div
                                    class="absolute -top-4 -right-4 opacity-10"
                                >
                                    <PhBrain :size="80" />
                                </div>
                                <h4
                                    class="mb-4 text-sm font-black tracking-widest text-indigo-300 uppercase"
                                >
                                    Análisis Cortex & Simulación
                                </h4>
                                <p
                                    class="mb-4 text-lg leading-relaxed font-medium text-white italic"
                                >
                                    "{{ detailedPlan.primary_driver }}"
                                </p>
                                <div
                                    class="flex items-center gap-2 rounded-lg bg-emerald-500/20 px-3 py-2 text-[10px] font-bold text-emerald-400"
                                >
                                    <PhTrendUp :size="14" />
                                    {{ detailedPlan.predicted_impact }}
                                </div>
                            </div>

                            <!-- Retention Plan Actions -->
                            <div class="space-y-8">
                                <!-- Individual -->
                                <div>
                                    <h4
                                        class="mb-4 flex items-center gap-2 text-[10px] font-black tracking-widest text-white/40 uppercase"
                                    >
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-indigo-500"
                                        ></div>
                                        Acciones Individuales
                                    </h4>
                                    <div class="space-y-3">
                                        <div
                                            v-for="(
                                                action, index
                                            ) in detailedPlan.retention_plan
                                                .individual"
                                            :key="index"
                                            class="rounded-xl border border-white/5 bg-white/5 p-4"
                                        >
                                            <p
                                                class="text-sm leading-relaxed text-white/80"
                                            >
                                                {{ action }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Team -->
                                <div
                                    v-if="
                                        detailedPlan.retention_plan.team?.length
                                    "
                                >
                                    <h4
                                        class="mb-4 flex items-center gap-2 text-[10px] font-black tracking-widest text-emerald-400/60 uppercase"
                                    >
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                        ></div>
                                        Intervenciones de Equipo
                                    </h4>
                                    <div class="space-y-3">
                                        <div
                                            v-for="(
                                                action, index
                                            ) in detailedPlan.retention_plan
                                                .team"
                                            :key="index"
                                            class="rounded-xl border border-emerald-500/10 bg-emerald-500/5 p-4"
                                        >
                                            <p
                                                class="text-sm leading-relaxed text-emerald-100/80"
                                            >
                                                {{ action }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Organizational -->
                                <div
                                    v-if="
                                        detailedPlan.retention_plan
                                            .organizational?.length
                                    "
                                >
                                    <h4
                                        class="mb-4 flex items-center gap-2 text-[10px] font-black tracking-widest text-amber-400/60 uppercase"
                                    >
                                        <div
                                            class="h-1.5 w-1.5 rounded-full bg-amber-500"
                                        ></div>
                                        Recomendaciones Organizacionales
                                    </h4>
                                    <div class="space-y-3">
                                        <div
                                            v-for="(
                                                action, index
                                            ) in detailedPlan.retention_plan
                                                .organizational"
                                            :key="index"
                                            class="rounded-xl border border-amber-500/10 bg-amber-500/5 p-4"
                                        >
                                            <p
                                                class="text-sm leading-relaxed text-amber-100/80"
                                            >
                                                {{ action }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Stats -->
                            <div
                                class="flex gap-6 overflow-x-auto border-t border-white/5 pt-6"
                            >
                                <div
                                    class="flex items-center gap-2 whitespace-nowrap"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-tighter text-white/20 uppercase"
                                        >Sentiment</span
                                    >
                                    <span
                                        class="text-xs font-bold text-emerald-400"
                                        >{{
                                            detailedPlan.indicators.sentiment *
                                            10
                                        }}%</span
                                    >
                                </div>
                                <div
                                    class="flex items-center gap-2 whitespace-nowrap"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-tighter text-white/20 uppercase"
                                        >Stress</span
                                    >
                                    <span
                                        class="text-xs font-bold"
                                        :class="
                                            detailedPlan.indicators.stress > 3
                                                ? 'text-rose-400'
                                                : 'text-emerald-400'
                                        "
                                        >{{
                                            detailedPlan.indicators.stress
                                        }}/5</span
                                    >
                                </div>
                                <div
                                    class="flex items-center gap-2 whitespace-nowrap"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-tighter text-white/20 uppercase"
                                        >Stagnation</span
                                    >
                                    <span
                                        class="text-xs font-bold text-amber-400"
                                        >{{
                                            Math.round(
                                                detailedPlan.indicators
                                                    .stagnation * 100,
                                            )
                                        }}%</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div v-else class="py-20 text-center text-white/30">
                            No se pudo cargar el plan detallado. Reintente en
                            unos momentos.
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between border-t border-white/10 bg-white/5 p-6"
                    >
                        <p class="text-[10px] text-white/30 italic">
                            Recomendación generada por el agente Stratos
                            Sentinel Fase 5.
                        </p>
                        <StButtonGlass
                            @click="showPlanModal = false"
                            variant="primary"
                            >Entendido</StButtonGlass
                        >
                    </div>
                </StCardGlass>
            </div>
        </div>
    </div>
</template>
