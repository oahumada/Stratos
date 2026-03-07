<template>
    <div class="final-dashboard animate-in space-y-8 duration-700 fade-in">
        <!-- Main Stats Orbit -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <StCardGlass
                v-for="kpi in mainKpis"
                :key="kpi.label"
                variant="glass"
                :no-hover="true"
                class="group relative overflow-hidden"
            >
                <!-- Background Icon Glow -->
                <v-icon
                    :icon="kpi.icon"
                    size="120"
                    class="absolute -top-4 -right-4 opacity-5 transition-transform group-hover:scale-110 group-hover:opacity-10"
                    :color="kpi.color"
                />

                <div class="flex items-center gap-5">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border bg-white/5 shadow-lg"
                        :class="`border-${kpi.color}-500/30`"
                    >
                        <v-icon
                            :icon="kpi.icon"
                            :color="`${kpi.color}-300`"
                            size="24"
                        />
                    </div>
                    <div>
                        <div
                            class="text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                        >
                            {{ kpi.label }}
                        </div>
                        <div
                            class="text-2xl font-black tracking-tight text-white"
                        >
                            {{ kpi.value }}
                        </div>
                    </div>
                </div>
            </StCardGlass>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <!-- Left Column: Investment Mix & Synthetization -->
            <div class="space-y-6 lg:col-span-6">
                <!-- Investment Breakdown -->
                <StCardGlass
                    variant="glass"
                    class="border-white/10 p-0!"
                    :no-hover="true"
                >
                    <div
                        class="flex items-center gap-3 border-b border-white/10 bg-white/5 px-6 py-4"
                    >
                        <v-icon
                            icon="mdi-finance"
                            color="indigo-400"
                            size="18"
                        />
                        <h4
                            class="text-xs font-black tracking-widest text-white uppercase"
                        >
                            4B Investment Distribution
                        </h4>
                    </div>

                    <div class="p-6">
                        <div
                            v-if="summary.total_investment > 0"
                            class="space-y-6"
                        >
                            <div class="space-y-4">
                                <div
                                    v-for="item in summary.investment"
                                    :key="item.strategy_type"
                                    class="group/item relative space-y-2"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center gap-3">
                                            <v-icon
                                                :icon="
                                                    getStrategyIcon(
                                                        item.strategy_type,
                                                    )
                                                "
                                                :color="
                                                    getStrategyColor(
                                                        item.strategy_type,
                                                    )
                                                "
                                                size="16"
                                            />
                                            <span
                                                class="text-xs font-bold tracking-tight text-white/70 uppercase"
                                                >{{ item.strategy_type }}</span
                                            >
                                        </div>
                                        <div class="flex items-baseline gap-2">
                                            <span
                                                class="text-sm font-black text-white"
                                                >{{
                                                    formatCurrency(
                                                        item.total_cost,
                                                    )
                                                }}</span
                                            >
                                            <span
                                                class="text-[10px] font-bold text-white/20"
                                                >({{
                                                    Math.round(
                                                        (item.total_cost /
                                                            summary.total_investment) *
                                                            100,
                                                    )
                                                }}%)</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                                    >
                                        <div
                                            class="h-full transition-all duration-1000"
                                            :style="{
                                                width:
                                                    (item.total_cost /
                                                        summary.total_investment) *
                                                        100 +
                                                    '%',
                                                backgroundColor: getHexColor(
                                                    getStrategyColor(
                                                        item.strategy_type,
                                                    ),
                                                ),
                                            }"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="mt-8 rounded-2xl border border-indigo-500/20 bg-indigo-500/10 p-5 shadow-[inset_0_0_20px_rgba(99,102,241,0.1)]"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs font-black tracking-widest text-indigo-300 uppercase"
                                        >Total Capital Expenditure</span
                                    >
                                    <span
                                        class="text-2xl font-black tracking-tighter text-white"
                                        >{{
                                            formatCurrency(
                                                summary.total_investment,
                                            )
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center justify-center py-12 text-center opacity-30"
                        >
                            <v-icon
                                icon="mdi-cash-remove"
                                size="32"
                                class="mb-3"
                            />
                            <p
                                class="text-xs font-bold tracking-widest uppercase"
                            >
                                No Tactical Investment Defined
                            </p>
                        </div>
                    </div>
                </StCardGlass>

                <!-- Synthetization Index -->
                <StCardGlass
                    variant="glass"
                    border-accent="purple"
                    :no-hover="true"
                >
                    <div class="mb-6 flex items-center justify-between">
                        <h4
                            class="text-xs font-black tracking-widest text-purple-300 uppercase"
                        >
                            Neural Synthesis Balance
                        </h4>
                        <StBadgeGlass variant="secondary" size="xs"
                            >AI INTEGRATED</StBadgeGlass
                        >
                    </div>

                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <v-icon
                                    icon="mdi-human-greeting-variant"
                                    color="indigo-400"
                                    size="20"
                                />
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                                    >Human Workforce</span
                                >
                            </div>
                            <span class="text-xs font-black text-white"
                                >{{ 100 - summary.synthetization_index }}%</span
                            >
                        </div>

                        <div
                            class="group relative flex h-4 w-full overflow-hidden rounded-full bg-indigo-500/20 p-1"
                        >
                            <div
                                class="h-full rounded-full bg-linear-to-r from-indigo-500 to-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.5)] transition-all duration-1000"
                                :style="{
                                    width: summary.synthetization_index + '%',
                                }"
                            ></div>
                            <!-- Midpoint Marker -->
                            <div
                                class="absolute top-0 left-1/2 h-full w-[2px] -translate-x-1/2 bg-white/20"
                            ></div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <v-icon
                                    icon="mdi-robot-vacuum-variant"
                                    color="purple-400"
                                    size="20"
                                />
                                <span
                                    class="text-[10px] font-black tracking-widest text-purple-400 uppercase"
                                    >Synthetic (AI/Bot) Index</span
                                >
                            </div>
                            <span class="text-xs font-black text-purple-300"
                                >{{ summary.synthetization_index }}%</span
                            >
                        </div>
                    </div>
                </StCardGlass>
            </div>

            <!-- Right Column: Gaps & Risk -->
            <div class="space-y-6 lg:col-span-6">
                <!-- Critical Gaps -->
                <StCardGlass
                    variant="glass"
                    class="border-white/10 p-0!"
                    :no-hover="true"
                >
                    <div
                        class="flex items-center gap-3 border-b border-white/10 bg-white/5 px-6 py-4"
                    >
                        <v-icon
                            icon="mdi-target-variant"
                            color="rose-400"
                            size="18"
                        />
                        <h4
                            class="text-xs font-black tracking-widest text-white uppercase"
                        >
                            Critical Skill Gaps Resolved
                        </h4>
                    </div>

                    <div class="overflow-hidden p-0">
                        <table class="w-full text-left">
                            <thead class="bg-white/2">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-[9px] font-black tracking-widest text-white/20 uppercase"
                                    >
                                        Neural Dimension
                                    </th>
                                    <th
                                        class="px-6 py-3 text-center text-[9px] font-black tracking-widest text-white/20 uppercase"
                                    >
                                        Priority
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right text-[9px] font-black tracking-widest text-white/20 uppercase"
                                    >
                                        Gap (FTE)
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr
                                    v-for="gap in summary.critical_gaps"
                                    :key="gap.skill"
                                    class="transition-colors hover:bg-white/2"
                                >
                                    <td
                                        class="px-6 py-4 text-xs font-bold text-white/80"
                                    >
                                        {{ gap.skill }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <StBadgeGlass
                                            :variant="
                                                getPriorityVariant(gap.priority)
                                            "
                                            size="xs"
                                            class="px-3! uppercase"
                                        >
                                            {{ gap.priority }}
                                        </StBadgeGlass>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="text-sm font-black text-white"
                                            >{{ gap.gap }}</span
                                        >
                                    </td>
                                </tr>
                                <tr v-if="!summary.critical_gaps?.length">
                                    <td
                                        colspan="3"
                                        class="px-6 py-12 text-center text-xs font-medium text-white/20 italic"
                                    >
                                        No critical gaps detected.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </StCardGlass>

                <!-- Risk Assessment Card -->
                <StCardGlass
                    variant="glass"
                    :border-accent="getRiskColor(summary.risk_level)"
                    class="bg-black/40!"
                    :no-hover="true"
                >
                    <div class="flex items-center gap-5">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border bg-white/5"
                            :class="`border-${getRiskColor(summary.risk_level)}-500/30`"
                        >
                            <v-icon
                                icon="mdi-shield-alert-outline"
                                :color="`${getRiskColor(summary.risk_level)}-300`"
                                size="28"
                            />
                        </div>
                        <div class="grow">
                            <div class="mb-1 flex items-center justify-between">
                                <span
                                    class="text-[10px] font-black tracking-[0.2em] text-white/40 uppercase"
                                    >Execution Vulnerability</span
                                >
                                <StBadgeGlass
                                    :variant="
                                        getRiskBadgeVariant(summary.risk_level)
                                    "
                                    size="xs"
                                    >{{
                                        (
                                            summary.risk_level || 'LOW'
                                        ).toUpperCase()
                                    }}
                                    RISK</StBadgeGlass
                                >
                            </div>
                            <p
                                class="text-[11px] leading-relaxed text-white/50"
                            >
                                Derived from Neural Readiness Index (Scenario
                                IQ) and FTE Bandwidth Gap.
                            </p>
                        </div>
                    </div>
                </StCardGlass>

                <!-- Execution Logic -->
                <div class="space-y-4 pt-4">
                    <StButtonGlass
                        variant="primary"
                        size="lg"
                        icon="mdi-check-decagram"
                        class="w-full py-6!"
                        @click="approveScenario"
                    >
                        Incorporate & Finalize Scenario Architecture
                    </StButtonGlass>

                    <div class="grid grid-cols-2 gap-4">
                        <StButtonGlass
                            variant="ghost"
                            icon="mdi-file-pdf-box"
                            @click="
                                notification.showInfo(
                                    'Neural report export initializing...',
                                )
                            "
                        >
                            Export Strategic PDF
                        </StButtonGlass>
                        <StButtonGlass
                            variant="ghost"
                            icon="mdi-share-variant"
                            @click="
                                notification.showInfo(
                                    'Stakeholder bridge opening...',
                                )
                            "
                        >
                            Sync with Committee
                        </StButtonGlass>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Dialog: Glass Version -->
        <v-dialog v-model="approveDialog" max-width="450" persistent>
            <StCardGlass
                variant="glass"
                border-accent="emerald"
                class="p-8 backdrop-blur-3xl"
            >
                <div class="mb-6 flex flex-col items-center text-center">
                    <div
                        class="mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_30px_rgba(16,185,129,0.2)]"
                    >
                        <v-icon
                            icon="mdi-check-circle-outline"
                            color="emerald-400"
                            size="32"
                        />
                    </div>
                    <h3 class="text-xl font-black tracking-tight text-white">
                        Final Authorization
                    </h3>
                    <p class="mt-2 text-sm font-medium text-white/50">
                        By approving this architecture, you mark this version as
                        the
                        <span class="font-bold text-emerald-400"
                            >Definitive Operational Benchmark</span
                        >
                        for execution. This action is recorded in the immutable
                        strategic log.
                    </p>
                </div>

                <div class="flex flex-col gap-3">
                    <StButtonGlass
                        variant="primary"
                        class="w-full"
                        :loading="approving"
                        @click="confirmApproval"
                    >
                        Confirm Decisive Approval
                    </StButtonGlass>
                    <StButtonGlass
                        variant="ghost"
                        class="w-full"
                        @click="approveDialog = false"
                    >
                        Abort Finalization
                    </StButtonGlass>
                </div>
            </StCardGlass>
        </v-dialog>
        <!-- Advanced Decision Support Row -->
        <div class="space-y-6 pt-12">
            <div class="flex items-center gap-3">
                <v-icon icon="mdi-orbit-variant" color="indigo-400" size="24" />
                <h3
                    class="text-xl font-black tracking-tight text-white uppercase"
                >
                    Advanced Decision Support Systems
                </h3>
            </div>

            <v-tabs
                v-model="advancedTab"
                class="border-b border-white/5"
                bg-color="transparent"
                density="comfortable"
                color="indigo-400"
            >
                <v-tab value="crisis">Stress Simulation</v-tab>
                <v-tab value="agentic">Stratos Radar</v-tab>
            </v-tabs>

            <div class="mt-6">
                <v-window v-model="advancedTab">
                    <v-window-item value="crisis">
                        <CrisisSimulator :scenario-id="scenarioId" />
                    </v-window-item>
                    <v-window-item value="agentic">
                        <AgenticScenarioPlanner :scenario-id="scenarioId" />
                    </v-window-item>
                </v-window>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useNotification } from '@/composables/useNotification';
import AgenticScenarioPlanner from '@/pages/ScenarioPlanning/AgenticScenarioPlanner.vue';
import CrisisSimulator from '@/pages/ScenarioPlanning/CrisisSimulator.vue';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const advancedTab = ref('crisis');

const props = defineProps({
    scenarioId: {
        type: [Number, String],
        required: true,
    },
});

const emit = defineEmits(['approved']);

const notification = useNotification();
const loading = ref(true);
const approving = ref(false);
const approveDialog = ref(false);
const summary = ref<any>({
    iq: 0,
    total_investment: 0,
    investment: [],
    fte: { required: 0, current: 0, gap: 0 },
    critical_gaps: [],
    synthetization_index: 0,
    risk_level: 'Low',
});

const mainKpis = computed(() => [
    {
        label: 'Neural Readiness (IQ)',
        value: `${summary.value.iq}%`,
        icon: 'mdi-brain-freeze',
        color: getIqColor(summary.value.iq),
    },
    {
        label: 'Tactical Capex (4B)',
        value: formatCurrency(summary.value.total_investment),
        icon: 'mdi-shield-crown-outline',
        color: 'emerald',
    },
    {
        label: 'Capability Deficit',
        value: `${summary.value.fte.gap} FTEs`,
        icon: 'mdi-lightning-bolt-outline',
        color: 'amber',
    },
]);

const fetchSummary = async () => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/summary`,
        );
        summary.value = res.data.data;
    } catch (error) {
        console.error('Core dump failure:', error);
        notification.showError('Executive summary retrieval error');
    } finally {
        loading.value = false;
    }
};

const approveScenario = () => (approveDialog.value = true);

const confirmApproval = async () => {
    approving.value = true;
    try {
        await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/finalize`,
        );
        notification.showSuccess(
            'Neural architecture incorporated successfully',
        );
        approveDialog.value = false;
        emit('approved');
    } catch (error) {
        console.error('Finalization failure:', error);
        notification.showError('Execution bridge failed to close');
    } finally {
        approving.value = false;
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(val);
};

const getIqColor = (iq: number) => {
    if (iq < 40) return 'rose';
    if (iq < 70) return 'amber';
    return 'indigo';
};

const getStrategyColor = (type: string) => {
    const map: any = {
        build: 'indigo',
        buy: 'emerald',
        borrow: 'amber',
        bot: 'purple',
    };
    return map[type?.toLowerCase()] || 'slate';
};

const getHexColor = (color: string) => {
    const hex: Record<string, string> = {
        indigo: '#818cf8',
        emerald: '#34d399',
        amber: '#fbbf24',
        purple: '#a78bfa',
    };
    return hex[color] || '#94a3b8';
};

const getStrategyIcon = (type: string) => {
    const map: any = {
        build: 'mdi-neural',
        buy: 'mdi-account-plus-outline',
        borrow: 'mdi-account-switch-outline',
        bot: 'mdi-robot-outline',
    };
    return map[type?.toLowerCase()] || 'mdi-help-circle-outline';
};

const getPriorityVariant = (p: string) => {
    const map: any = {
        critical: 'secondary',
        high: 'secondary',
        medium: 'primary',
        low: 'glass',
    };
    return map[p?.toLowerCase()] || 'glass';
};

const getRiskColor = (level: string) => {
    const map: any = { High: 'rose', Medium: 'amber', Low: 'indigo' };
    return map[level] || 'slate';
};

const getRiskBadgeVariant = (level: string) => {
    switch (level?.toLowerCase()) {
        case 'high':
            return 'secondary';
        case 'medium':
            return 'success';
        case 'low':
            return 'primary';
        default:
            return 'glass';
    }
};

onMounted(fetchSummary);
</script>

<style scoped>
.final-dashboard {
    width: 100%;
}
</style>
