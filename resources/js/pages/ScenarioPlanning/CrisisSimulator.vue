<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps<{
    scenarioId: number;
}>();

type CrisisType = 'attrition' | 'obsolescence' | 'restructuring';

const activeTab = ref<CrisisType>('attrition');
const loading = ref(false);
const simulation = ref<any>(null);
const error = ref<string | null>(null);

// Form inputs
const attritionRate = ref(15);
const timeframeMonths = ref(12);
const obsoleteSkillIds = ref<number[]>([]);
const emergingSkills = ref('');
const horizonMonths = ref(18);
const mergeDepartments = ref<number[]>([]);
const eliminateRoleIds = ref<number[]>([]);

const crisisTypes = [
    {
        key: 'attrition',
        label: 'Retiro Masivo',
        icon: '🔥',
        description: 'Simula pérdida masiva de talento',
    },
    {
        key: 'obsolescence',
        label: 'Obsolescencia Tech',
        icon: '⚡',
        description: 'Skills que se vuelven irrelevantes',
    },
    {
        key: 'restructuring',
        label: 'Restructuración',
        icon: '🔄',
        description: 'Fusiones y eliminación de roles',
    },
];

async function runSimulation() {
    loading.value = true;
    error.value = null;
    simulation.value = null;

    const baseUrl = `/api/strategic-planning/scenarios/${props.scenarioId}/crisis`;

    try {
        let endpoint = '';
        let payload: any = {};

        switch (activeTab.value) {
            case 'attrition':
                endpoint = `${baseUrl}/attrition`;
                payload = {
                    attrition_rate: attritionRate.value,
                    timeframe_months: timeframeMonths.value,
                };
                break;
            case 'obsolescence':
                endpoint = `${baseUrl}/obsolescence`;
                payload = {
                    obsolete_skill_ids: obsoleteSkillIds.value,
                    emerging_skills: emergingSkills.value
                        .split(',')
                        .map((s) => s.trim())
                        .filter(Boolean),
                    horizon_months: horizonMonths.value,
                };
                break;
            case 'restructuring':
                endpoint = `${baseUrl}/restructuring`;
                payload = {
                    merge_departments: mergeDepartments.value,
                    eliminate_role_ids: eliminateRoleIds.value,
                };
                break;
        }

        const { data } = await axios.post(endpoint, payload);
        simulation.value = data.data ?? data;
    } catch (e: any) {
        error.value = e.response?.data?.message ?? 'Error en simulación';
    } finally {
        loading.value = false;
    }
}

function getRiskColor(score: number): string {
    if (score >= 70) return 'text-rose-400';
    if (score >= 40) return 'text-amber-400';
    return 'text-emerald-400';
}

function getRiskBg(level: string): string {
    const l = (level || '').toLowerCase();
    if (l === 'high' || l === 'critical')
        return 'bg-rose-500/10 border-rose-500/20 text-rose-300';
    if (l === 'medium')
        return 'bg-amber-500/10 border-amber-500/20 text-amber-300';
    return 'bg-emerald-500/10 border-emerald-500/20 text-emerald-300';
}

function formatUsd(n: number): string {
    return '$' + Math.round(n).toLocaleString('en-US');
}
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h2 class="text-2xl font-bold text-white">
                <span class="mr-2">🛡️</span> Crisis Simulator
            </h2>
            <p class="mt-1 text-sm text-white/50">
                War-gaming engine — Simula escenarios de crisis para evaluar la
                resiliencia organizacional
            </p>
        </div>

        <!-- Crisis Type Tabs -->
        <div class="flex gap-3">
            <button
                v-for="ct in crisisTypes"
                :key="ct.key"
                @click="
                    activeTab = ct.key as CrisisType;
                    simulation = null;
                "
                class="flex-1 rounded-xl border p-4 text-left transition-all duration-300"
                :class="
                    activeTab === ct.key
                        ? 'border-indigo-500/40 bg-indigo-500/10 ring-1 ring-indigo-500/20'
                        : 'border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/8'
                "
            >
                <span class="text-xl">{{ ct.icon }}</span>
                <p class="mt-1 text-sm font-bold text-white">{{ ct.label }}</p>
                <p class="text-[0.65rem] text-white/40">{{ ct.description }}</p>
            </button>
        </div>

        <!-- Configuration Forms -->
        <StCardGlass>
            <h3
                class="mb-4 text-sm font-bold tracking-widest text-white/60 uppercase"
            >
                Parámetros de Simulación
            </h3>

            <!-- Attrition Form -->
            <div
                v-if="activeTab === 'attrition'"
                class="grid grid-cols-1 gap-4 md:grid-cols-2"
            >
                <div>
                    <label class="mb-1 block text-xs text-white/50"
                        >Tasa de Attrition (%)</label
                    >
                    <input
                        v-model.number="attritionRate"
                        type="range"
                        min="5"
                        max="50"
                        step="5"
                        class="w-full accent-indigo-500"
                    />
                    <div
                        class="mt-1 flex justify-between text-xs text-white/30"
                    >
                        <span>5%</span>
                        <span class="font-bold text-indigo-300"
                            >{{ attritionRate }}%</span
                        >
                        <span>50%</span>
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs text-white/50"
                        >Horizonte (meses)</label
                    >
                    <select
                        v-model="timeframeMonths"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white"
                    >
                        <option :value="6">6 meses</option>
                        <option :value="12">12 meses</option>
                        <option :value="18">18 meses</option>
                        <option :value="24">24 meses</option>
                    </select>
                </div>
            </div>

            <!-- Obsolescence Form -->
            <div
                v-else-if="activeTab === 'obsolescence'"
                class="grid grid-cols-1 gap-4 md:grid-cols-2"
            >
                <div>
                    <label class="mb-1 block text-xs text-white/50"
                        >Skills Emergentes (separar con coma)</label
                    >
                    <input
                        v-model="emergingSkills"
                        type="text"
                        placeholder="AI Engineering, Cloud Native, Quantum Computing"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white placeholder-white/20"
                    />
                </div>
                <div>
                    <label class="mb-1 block text-xs text-white/50"
                        >Horizonte de obsolescencia (meses)</label
                    >
                    <select
                        v-model="horizonMonths"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white"
                    >
                        <option :value="6">6 meses</option>
                        <option :value="12">12 meses</option>
                        <option :value="18">18 meses</option>
                        <option :value="24">24 meses</option>
                        <option :value="36">36 meses</option>
                    </select>
                </div>
            </div>

            <!-- Restructuring Form -->
            <div v-else class="space-y-4">
                <p class="text-xs text-white/40">
                    Configura los departamentos a fusionar y los roles a
                    eliminar. Los IDs se cargarán desde el catálogo de la
                    organización.
                </p>
            </div>

            <div class="mt-4 flex justify-end">
                <StButtonGlass
                    variant="primary"
                    @click="runSimulation"
                    :loading="loading"
                >
                    🚀 Ejecutar Simulación
                </StButtonGlass>
            </div>
        </StCardGlass>

        <!-- Error -->
        <StCardGlass v-if="error" class="border-rose-500/30 bg-rose-500/5">
            <p class="text-sm text-rose-300">{{ error }}</p>
        </StCardGlass>

        <!-- Results -->
        <Transition name="fade-slide">
            <div v-if="simulation" class="space-y-4">
                <!-- Impact Header -->
                <StCardGlass class="border-indigo-500/20">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="text-lg">📊</span>
                        <h3
                            class="text-sm font-bold tracking-widest text-indigo-300 uppercase"
                        >
                            Resultado de Simulación
                        </h3>
                        <StBadgeGlass
                            :variant="
                                simulation.risk_score >= 50
                                    ? 'error'
                                    : 'warning'
                            "
                            size="md"
                        >
                            Risk Score:
                            {{
                                Math.round(
                                    simulation.risk_score ??
                                        simulation.feasibility_score ??
                                        0,
                                )
                            }}
                        </StBadgeGlass>
                    </div>

                    <!-- KPI Cards -->
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                        <div
                            v-if="simulation.impact?.people_at_risk != null"
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p class="text-2xl font-black text-rose-400">
                                {{ simulation.impact.people_at_risk }}
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Personas en riesgo
                            </p>
                        </div>
                        <div
                            v-if="simulation.impact?.people_affected != null"
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p class="text-2xl font-black text-amber-400">
                                {{ simulation.impact.people_affected }}
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Personas afectadas
                            </p>
                        </div>
                        <div
                            v-if="simulation.impact?.replacement_cost_usd"
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p class="text-2xl font-black text-white">
                                {{
                                    formatUsd(
                                        simulation.impact.replacement_cost_usd,
                                    )
                                }}
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Costo de reemplazo
                            </p>
                        </div>
                        <div
                            v-if="simulation.impact?.total_reskill_cost_usd"
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p class="text-2xl font-black text-white">
                                {{
                                    formatUsd(
                                        simulation.impact
                                            .total_reskill_cost_usd,
                                    )
                                }}
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Costo de reskilling
                            </p>
                        </div>
                        <div
                            v-if="simulation.impact?.time_to_recover_months"
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p class="text-2xl font-black text-indigo-400">
                                {{ simulation.impact.time_to_recover_months }}m
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Tiempo de recuperación
                            </p>
                        </div>
                        <div
                            v-if="simulation.impact?.critical_roles_exposed"
                            class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"
                        >
                            <p class="text-2xl font-black text-rose-400">
                                {{ simulation.impact.critical_roles_exposed }}
                            </p>
                            <p
                                class="text-[0.6rem] tracking-widest text-white/40 uppercase"
                            >
                                Roles críticos expuestos
                            </p>
                        </div>
                    </div>
                </StCardGlass>

                <!-- Skills Impact -->
                <StCardGlass
                    v-if="
                        simulation.impact?.skills_impact &&
                        Object.keys(simulation.impact.skills_impact).length
                    "
                >
                    <h3
                        class="mb-3 text-sm font-bold tracking-widest text-white/60 uppercase"
                    >
                        <span class="mr-1">🧬</span> Impacto en Skills
                    </h3>
                    <div class="space-y-2">
                        <div
                            v-for="(data, skillName) in simulation.impact
                                .skills_impact"
                            :key="String(skillName)"
                            class="flex items-center justify-between border-b border-white/5 py-1.5 last:border-0"
                        >
                            <span class="text-sm text-white/70">{{
                                skillName
                            }}</span>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-white/40"
                                    >Nivel prom: {{ data.avg_level }}</span
                                >
                                <StBadgeGlass variant="error" size="sm"
                                    >-{{ data.count }} personas</StBadgeGlass
                                >
                            </div>
                        </div>
                    </div>
                </StCardGlass>

                <!-- Mitigation Strategies -->
                <StCardGlass v-if="simulation.mitigation_strategies?.length">
                    <h3
                        class="mb-3 text-sm font-bold tracking-widest text-white/60 uppercase"
                    >
                        <span class="mr-1">🛡️</span> Estrategias de Mitigación
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="(
                                strat, i
                            ) in simulation.mitigation_strategies"
                            :key="i"
                            class="rounded-xl border border-white/10 bg-white/5 p-4"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="mb-1 flex items-center gap-2">
                                        <StBadgeGlass
                                            :variant="
                                                strat.strategy === 'retention'
                                                    ? 'success'
                                                    : strat.strategy === 'build'
                                                      ? 'primary'
                                                      : strat.strategy === 'buy'
                                                        ? 'warning'
                                                        : 'glass'
                                            "
                                            size="sm"
                                        >
                                            {{ strat.strategy.toUpperCase() }}
                                        </StBadgeGlass>
                                    </div>
                                    <p class="text-sm text-white/80">
                                        {{ strat.action }}
                                    </p>
                                    <p class="mt-1 text-xs text-emerald-400/70">
                                        {{ strat.impact }}
                                    </p>
                                </div>
                                <p
                                    class="ml-4 text-sm font-bold whitespace-nowrap text-white/60"
                                >
                                    {{ formatUsd(strat.cost_estimate) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </StCardGlass>

                <!-- Transition Plan (for obsolescence) -->
                <StCardGlass v-if="simulation.transition_plan">
                    <h3
                        class="mb-3 text-sm font-bold tracking-widest text-white/60 uppercase"
                    >
                        <span class="mr-1">📋</span> Plan de Transición
                    </h3>
                    <div class="flex gap-4 overflow-x-auto pb-2">
                        <div
                            v-for="(phase, key) in simulation.transition_plan"
                            :key="String(key)"
                            class="min-w-[200px] flex-shrink-0 rounded-xl border border-white/10 bg-white/5 p-4"
                        >
                            <p class="mb-1 text-xs font-bold text-indigo-300">
                                {{ phase.name }}
                            </p>
                            <p class="text-[0.6rem] text-white/40">
                                {{ phase.duration_weeks }} semanas
                            </p>
                            <ul class="mt-2 space-y-1">
                                <li
                                    v-for="(action, j) in phase.actions"
                                    :key="j"
                                    class="flex items-start gap-1 text-xs text-white/60"
                                >
                                    <span class="mt-0.5 text-emerald-400"
                                        >•</span
                                    >
                                    <span>{{ action }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </StCardGlass>
            </div>
        </Transition>
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
