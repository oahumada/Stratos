<template>
    <div class="closing-strategies space-y-10 pb-20">
        <!-- Header: The Tactical Bridge -->
        <div
            class="strategies-header animate-in duration-700 fade-in slide-in-from-top-4"
        >
            <div
                class="flex flex-col justify-between gap-6 md:flex-row md:items-end"
            >
                <div class="flex items-start gap-5">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                    >
                        <v-icon
                            icon="mdi-strategy"
                            color="indigo-300"
                            size="24"
                        />
                    </div>
                    <div>
                        <h2
                            class="text-2xl font-black tracking-tight text-white"
                        >
                            Gap
                            <span class="text-indigo-400">Closing</span>
                            Strategies
                        </h2>
                        <p class="mt-1 text-sm font-medium text-white/40">
                            Neural tactical recommendations for structural gap
                            mitigation.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <StButtonGlass
                        variant="primary"
                        icon="mdi-auto-fix"
                        :loading="loading"
                        @click="generateStrategies"
                    >
                        Neural Strategy Synthesis
                    </StButtonGlass>
                </div>
            </div>
        </div>

        <!-- Strategy Logic Guide -->
        <StCardGlass
            variant="glass"
            class="bg-indigo-500/[0.02]!"
            :no-hover="true"
        >
            <div class="flex flex-col items-center gap-8 md:flex-row">
                <div
                    class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-white/5 bg-white/5"
                >
                    <v-icon
                        icon="mdi-lightbulb-on-outline"
                        color="indigo-300"
                        size="32"
                    />
                </div>
                <div class="flex-grow">
                    <h3
                        class="mb-2 text-[10px] font-black tracking-[0.3em] text-white/20 uppercase"
                    >
                        Tactical Methodology
                    </h3>
                    <p
                        class="text-sm leading-relaxed font-medium text-white/60"
                    >
                        Based on the Step 3 diagnostics, the system proposes
                        optimized paths for:
                        <span class="font-bold text-indigo-400">Build</span>
                        (Capability Dev),
                        <span class="font-bold text-emerald-400">Buy</span>
                        (Market Acquisition),
                        <span class="font-bold text-amber-400">Borrow</span>
                        (External Talent), and
                        <span class="font-bold text-purple-400">Bot</span>
                        (Synthetic Execution).
                    </p>
                </div>
            </div>
        </StCardGlass>

        <!-- No Strategies State -->
        <transition name="fade">
            <div
                v-if="strategies.length === 0 && !loading"
                class="flex flex-col items-center justify-center py-20 text-center"
            >
                <div
                    class="mb-6 flex h-20 w-20 items-center justify-center rounded-full border border-white/5 bg-white/2"
                >
                    <v-icon size="32" color="white/10">mdi-strategy-off</v-icon>
                </div>
                <h3 class="mb-2 text-xl font-black text-white/40">
                    No Simulations Active
                </h3>
                <p class="mx-auto max-w-xs text-sm text-white/20">
                    Synthesize neural strategies to visualize tactical
                    mitigation paths.
                </p>
            </div>
        </transition>

        <!-- Loading State -->
        <div
            v-if="loading"
            class="flex flex-col items-center justify-center py-32"
        >
            <v-progress-circular
                indeterminate
                color="indigo-400"
                size="64"
                width="4"
            />
            <span
                class="mt-6 animate-pulse text-[10px] font-black tracking-[0.4em] text-white/20 uppercase"
                >Optimizing Mitigation Mix...</span
            >
        </div>

        <!-- Strategies Grid -->
        <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div
                v-for="strategy in strategies"
                :key="strategy.id"
                class="animate-in duration-500 fade-in slide-in-from-bottom-4"
            >
                <StCardGlass
                    variant="media"
                    class="group flex h-full flex-col overflow-hidden border-white/10"
                >
                    <div class="flex-grow space-y-6 p-6">
                        <!-- Strategy Header -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl border transition-all duration-500 group-hover:scale-110 group-hover:shadow-[0_0_20px_rgba(var(--accent-rgb),0.3)]"
                                    :style="`border-color: ${getStrategyColorHex(strategy.strategy)}30; background: ${getStrategyColorHex(strategy.strategy)}15; --accent-rgb: ${getStrategyColorRGB(strategy.strategy)}`"
                                >
                                    <v-icon
                                        :color="
                                            getStrategyColorHex(
                                                strategy.strategy,
                                            )
                                        "
                                        size="24"
                                        >{{
                                            getStrategyIcon(strategy.strategy)
                                        }}</v-icon
                                    >
                                </div>
                                <div>
                                    <h4
                                        class="text-lg font-black text-white transition-colors group-hover:text-indigo-200"
                                    >
                                        {{ strategy.strategy_name }}
                                    </h4>
                                    <div
                                        class="text-[10px] font-black tracking-widest text-indigo-400/60 uppercase"
                                    >
                                        {{
                                            strategy.skill_name ||
                                            'Generic Asset'
                                        }}
                                    </div>
                                </div>
                            </div>
                            <StBadgeGlass
                                :variant="
                                    getStrategyBadgeVariant(strategy.strategy)
                                "
                                size="sm"
                            >
                                {{
                                    (
                                        strategy.strategy || 'UNKNOWN'
                                    ).toUpperCase()
                                }}
                            </StBadgeGlass>
                        </div>

                        <!-- Description & Blueprint -->
                        <p
                            class="text-[13px] leading-relaxed font-medium text-white/50"
                        >
                            {{ strategy.description }}
                        </p>

                        <!-- Talent Mix Visualization -->
                        <div
                            v-if="strategy.blueprint"
                            class="space-y-3 rounded-2xl border border-white/5 bg-black/40 p-5"
                        >
                            <div
                                class="flex items-center justify-between text-[10px] font-black tracking-widest uppercase"
                            >
                                <span class="text-white/40"
                                    >Execution Mix ({{
                                        strategy.role_name
                                    }})</span
                                >
                                <span class="text-indigo-300"
                                    >{{ strategy.blueprint.human_leverage }}% H
                                    /
                                    {{ strategy.blueprint.synthetic_leverage }}%
                                    S</span
                                >
                            </div>
                            <div
                                class="relative h-1.5 w-full overflow-hidden rounded-full bg-purple-500/20"
                            >
                                <div
                                    class="absolute top-0 left-0 h-full bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)] transition-all duration-1000"
                                    :style="`width: ${strategy.blueprint.human_leverage}%`"
                                ></div>
                            </div>
                        </div>

                        <!-- Metrics Grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div
                                class="rounded-xl border border-white/5 bg-white/5 p-3 text-center transition-colors hover:bg-white/10"
                            >
                                <div
                                    class="mb-1 text-[8px] font-black tracking-widest text-white/20 uppercase"
                                >
                                    Est. Investment
                                </div>
                                <div class="text-sm font-black text-white">
                                    {{
                                        formatCurrency(strategy.estimated_cost)
                                    }}
                                </div>
                            </div>
                            <div
                                class="rounded-xl border border-white/5 bg-white/5 p-3 text-center transition-colors hover:bg-white/10"
                            >
                                <div
                                    class="mb-1 text-[8px] font-black tracking-widest text-white/20 uppercase"
                                >
                                    Time Horizon
                                </div>
                                <div class="text-sm font-black text-white">
                                    {{ strategy.estimated_time_weeks }} Weeks
                                </div>
                            </div>
                        </div>

                        <!-- IA Insights -->
                        <div
                            v-if="strategy.ia_strategy_rationale"
                            class="rounded-2xl border border-indigo-500/20 bg-indigo-500/10 p-4"
                        >
                            <div class="mb-2 flex items-center gap-2">
                                <v-icon size="14" color="indigo-300"
                                    >mdi-brain</v-icon
                                >
                                <span
                                    class="text-[9px] font-black tracking-[0.2em] text-indigo-300 uppercase"
                                    >Neural Context</span
                                >
                            </div>
                            <p
                                class="text-[11px] leading-snug font-medium text-white/60"
                            >
                                {{ strategy.ia_strategy_rationale }}
                            </p>
                        </div>
                    </div>

                    <!-- Card Actions -->
                    <div
                        class="flex items-center justify-between border-t border-white/5 bg-black/40 px-6 py-4"
                    >
                        <StButtonGlass
                            variant="ghost"
                            size="sm"
                            @click="handleDetails(strategy)"
                            >Details</StButtonGlass
                        >
                        <StBadgeGlass
                            :variant="getStatusVariant(strategy.status)"
                            size="xs"
                        >
                            {{ strategy.status.toUpperCase() }}
                        </StBadgeGlass>
                    </div>
                </StCardGlass>
            </div>
        </div>

        <!-- Succession Planning Area -->
        <div
            v-if="successionPlans.length > 0"
            class="mt-20 animate-in space-y-8 duration-1000 fade-in slide-in-from-bottom-8"
        >
            <div class="flex items-center gap-6">
                <div
                    class="h-px flex-grow bg-gradient-to-r from-transparent to-white/10"
                ></div>
                <div class="flex items-center gap-3">
                    <v-icon color="amber-300" size="24"
                        >mdi-account-switch</v-icon
                    >
                    <h3
                        class="text-xl font-black tracking-[0.3em] tracking-tight text-white uppercase"
                    >
                        Critical Succession Nodes
                    </h3>
                </div>
                <div
                    class="h-px flex-grow bg-gradient-to-l from-transparent to-white/10"
                ></div>
            </div>

            <div class="grid grid-cols-1 gap-8">
                <StCardGlass
                    v-for="plan in successionPlans"
                    :key="plan.role_name"
                    variant="glass"
                    class="overflow-hidden border-amber-500/20 p-0! shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)]"
                >
                    <div
                        class="flex items-center justify-between border-b border-white/5 bg-amber-500/5 px-8 py-5"
                    >
                        <div>
                            <span
                                class="text-[9px] font-black tracking-[0.4em] text-amber-500/60 uppercase"
                                >Strategic Continuity</span
                            >
                            <h4 class="text-xl font-black text-white">
                                {{ plan.role_name }}
                            </h4>
                        </div>
                        <v-icon color="amber-500/40" size="32"
                            >mdi-shield-check-outline</v-icon
                        >
                    </div>

                    <div class="overflow-x-auto p-0">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr class="bg-white/2">
                                    <th
                                        class="px-8 py-4 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        Priority
                                    </th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        Successor
                                    </th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        Availability
                                    </th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        Readiness Index
                                    </th>
                                    <th
                                        class="px-8 py-4 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >
                                        Est. Timeline
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr
                                    v-for="suc in [
                                        plan.primary_successor,
                                        plan.secondary_successor,
                                    ].filter((s) => !!s)"
                                    :key="suc.name"
                                    class="transition-colors hover:bg-white/[0.02]"
                                >
                                    <td class="px-8 py-5">
                                        <StBadgeGlass
                                            :variant="
                                                suc === plan.primary_successor
                                                    ? 'primary'
                                                    : 'secondary'
                                            "
                                            size="xs"
                                            class="px-3! text-[9px] font-black"
                                        >
                                            {{
                                                suc === plan.primary_successor
                                                    ? 'PRIMARY'
                                                    : 'RESERVE'
                                            }}
                                        </StBadgeGlass>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-[10px] font-black text-white shadow-lg"
                                            >
                                                {{ suc.name.charAt(0) }}
                                            </div>
                                            <span
                                                class="font-bold text-white"
                                                >{{ suc.name }}</span
                                            >
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div
                                            class="flex items-center gap-2 font-medium text-white/60"
                                        >
                                            <v-icon
                                                size="14"
                                                :color="
                                                    suc.availability ===
                                                    'Immediate'
                                                        ? 'emerald-400'
                                                        : 'amber-400'
                                                "
                                                >mdi-clock-check-outline</v-icon
                                            >
                                            {{ suc.availability }}
                                        </div>
                                    </td>
                                    <td class="min-w-[200px] px-8 py-5">
                                        <div class="space-y-2">
                                            <div
                                                class="flex justify-between text-[10px] font-black text-white/40"
                                            >
                                                <span
                                                    >{{
                                                        Math.ceil(
                                                            suc.readiness_percentage,
                                                        )
                                                    }}% READY</span
                                                >
                                            </div>
                                            <div
                                                class="h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                                            >
                                                <div
                                                    class="h-full bg-gradient-to-r from-emerald-500 to-emerald-400 shadow-[0_0_10px_rgba(16,185,129,0.5)] transition-all duration-1000"
                                                    :style="`width: ${suc.readiness_percentage}%`"
                                                ></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-8 py-5 text-sm font-black text-white/80"
                                    >
                                        {{ suc.timeline }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </StCardGlass>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const props = defineProps({
    scenarioId: {
        type: [Number, String],
        required: true,
    },
});

const strategies = ref<any[]>([]);
const successionPlans = ref<any[]>([]);
const loading = ref(false);
const { showSuccess, showError } = useNotification();

const loadStrategies = async () => {
    loading.value = true;
    try {
        const [stratRes, succRes] = await Promise.all([
            axios.get(
                `/api/strategic-planning/scenarios/${props.scenarioId}/strategies`,
            ),
            axios.get(
                `/api/scenarios/${props.scenarioId}/step2/succession-plans`,
            ),
        ]);
        strategies.value = stratRes.data.data || [];
        successionPlans.value = succRes.data.data || [];
    } catch (e) {
        console.error('Failed to load strategies:', e);
    } finally {
        loading.value = false;
    }
};

const generateStrategies = async () => {
    loading.value = true;
    try {
        const response = await axios.post(
            `/api/strategic-planning/scenarios/${props.scenarioId}/refresh-suggested-strategies`,
        );
        if (response.data.success) {
            showSuccess('Tactical strategies synthesized successfully');
            await loadStrategies();
        }
    } catch (e) {
        console.error('Generation failure:', e);
        showError('Strategy synthesis failed');
    } finally {
        loading.value = false;
    }
};

const getStrategyColorHex = (type: string | null) => {
    switch (type?.toLowerCase()) {
        case 'build':
            return '#818cf8'; // Indigo 400
        case 'buy':
            return '#34d399'; // Emerald 400
        case 'borrow':
            return '#fbbf24'; // Amber 400
        case 'bot':
            return '#a78bfa'; // Violet 400
        default:
            return '#94a3b8'; // Slate 400
    }
};

const getStrategyColorRGB = (type: string | null) => {
    switch (type?.toLowerCase()) {
        case 'build':
            return '129, 140, 248';
        case 'buy':
            return '52, 211, 153';
        case 'borrow':
            return '251, 191, 36';
        case 'bot':
            return '167, 139, 250';
        default:
            return '148, 163, 184';
    }
};

const getStrategyBadgeVariant = (type: string | null) => {
    switch (type?.toLowerCase()) {
        case 'build':
            return 'primary';
        case 'buy':
            return 'success';
        case 'borrow':
            return 'secondary';
        case 'bot':
            return 'secondary';
        default:
            return 'glass';
    }
};

const getStrategyIcon = (type: string | null) => {
    switch (type?.toLowerCase()) {
        case 'build':
            return 'mdi-school-outline';
        case 'buy':
            return 'mdi-account-plus-outline';
        case 'borrow':
            return 'mdi-handshake-outline';
        case 'bot':
            return 'mdi-robot-outline';
        default:
            return 'mdi-help-circle-outline';
    }
};

const getStatusVariant = (status: string | null) => {
    switch (status?.toLowerCase()) {
        case 'proposed':
            return 'glass';
        case 'approved':
            return 'primary';
        case 'rejected':
            return 'secondary';
        default:
            return 'glass';
    }
};

const formatCurrency = (value: number | string | null) => {
    if (!value) return '$0';
    const num = typeof value === 'string' ? Number.parseFloat(value) : value;
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(num);
};

const handleDetails = (strategy: any) => {
    console.log('Navigating to details for strategy:', strategy.id);
};

onMounted(() => {
    loadStrategies();
});
</script>

<style scoped>
.strategies-header {
    width: 100%;
}
</style>
