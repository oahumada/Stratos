<template>
    <div
        class="budgeting-plan-container animate-in space-y-8 duration-700 fade-in"
    >
        <!-- Dashboard Tabs: Glass Version -->
        <div
            class="flex w-fit items-center gap-2 rounded-2xl border border-white/5 bg-white/5 p-1.5 backdrop-blur-md"
        >
            <button
                v-for="tab in tabs"
                :key="tab.value"
                @click="activeTab = tab.value"
                class="flex items-center gap-2 rounded-xl px-5 py-2.5 text-xs font-black tracking-widest uppercase transition-all duration-300"
                :class="
                    activeTab === tab.value
                        ? 'bg-indigo-500 text-white shadow-[0_0_20px_rgba(99,102,241,0.4)]'
                        : 'text-white/40 hover:bg-white/5 hover:text-white/70'
                "
            >
                <v-icon :icon="tab.icon" size="16" />
                {{ tab.label }}
            </button>
        </div>

        <div class="tab-content min-h-[600px]">
            <transition name="fade-slide" mode="out-in">
                <!-- Impact Analytics View -->
                <div v-if="activeTab === 'impact'" key="impact">
                    <ImpactAnalyticsDashboard :scenario-id="scenarioId" />
                </div>

                <!-- Financial Planning View -->
                <div
                    v-else
                    key="budget"
                    class="grid grid-cols-1 gap-8 lg:grid-cols-12"
                >
                    <!-- Column: Financial Assumptions -->
                    <div class="lg:col-span-4 xl:col-span-3">
                        <StCardGlass
                            variant="glass"
                            border-accent="indigo"
                            class="sticky top-8 overflow-hidden p-0!"
                        >
                            <div
                                class="flex items-center gap-3 border-b border-white/10 bg-indigo-500/10 px-6 py-4"
                            >
                                <v-icon
                                    icon="mdi-calculator"
                                    color="indigo-300"
                                    size="20"
                                />
                                <h4
                                    class="text-xs font-black tracking-widest text-white/80 uppercase"
                                >
                                    Financial Logic
                                </h4>
                            </div>

                            <div class="space-y-6 p-6">
                                <div class="space-y-4">
                                    <div
                                        class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                                    >
                                        Core Parameters
                                    </div>
                                    <div class="space-y-4">
                                        <div
                                            class="space-y-1.5 transition-transform focus-within:translate-x-1"
                                        >
                                            <label
                                                class="ml-1 text-[10px] font-bold text-white/40 uppercase"
                                                >Avg. Annual Salary (USD)</label
                                            >
                                            <div
                                                class="flex items-center gap-2 rounded-xl border border-white/10 bg-black/40 px-3 py-2.5 transition-colors focus-within:border-indigo-500/50"
                                            >
                                                <span
                                                    class="font-bold text-indigo-400"
                                                    >$</span
                                                >
                                                <input
                                                    v-model.number="
                                                        assumptions.avg_annual_salary
                                                    "
                                                    type="number"
                                                    class="w-full bg-transparent text-sm font-bold text-white outline-none"
                                                    @change="saveAssumptions"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <v-divider class="border-white/5"></v-divider>

                                <div class="space-y-4">
                                    <div
                                        class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                                    >
                                        Strategy Unit Costs
                                    </div>
                                    <div class="space-y-5">
                                        <!-- Recruitment Fee -->
                                        <div class="group/input space-y-1.5">
                                            <div
                                                class="ml-1 flex items-center gap-2"
                                            >
                                                <div
                                                    class="h-1 w-1 rounded-full bg-emerald-400"
                                                ></div>
                                                <label
                                                    class="text-[10px] font-bold text-white/40 uppercase"
                                                    >Recruitment Fee
                                                    (BUY)</label
                                                >
                                            </div>
                                            <div
                                                class="flex items-center gap-2 rounded-xl border border-white/10 bg-black/40 px-3 py-2 transition-colors focus-within:border-emerald-500/50"
                                            >
                                                <input
                                                    v-model.number="
                                                        assumptions.buy_hiring_fee_pct
                                                    "
                                                    type="number"
                                                    class="w-full bg-transparent text-sm font-bold text-white outline-none"
                                                    @change="saveAssumptions"
                                                />
                                                <span
                                                    class="font-bold text-emerald-400"
                                                    >%</span
                                                >
                                            </div>
                                        </div>

                                        <!-- Training Cost -->
                                        <div class="group/input space-y-1.5">
                                            <div
                                                class="ml-1 flex items-center gap-2"
                                            >
                                                <div
                                                    class="h-1 w-1 rounded-full bg-indigo-400"
                                                ></div>
                                                <label
                                                    class="text-[10px] font-bold text-white/40 uppercase"
                                                    >Unit Training Cost
                                                    (BUILD)</label
                                                >
                                            </div>
                                            <div
                                                class="flex items-center gap-2 rounded-xl border border-white/10 bg-black/40 px-3 py-2 transition-colors focus-within:border-indigo-500/50"
                                            >
                                                <span
                                                    class="font-bold text-indigo-400"
                                                    >$</span
                                                >
                                                <input
                                                    v-model.number="
                                                        assumptions.build_training_unit_cost
                                                    "
                                                    type="number"
                                                    class="w-full bg-transparent text-sm font-bold text-white outline-none"
                                                    @change="saveAssumptions"
                                                />
                                            </div>
                                        </div>

                                        <!-- Borrow Premium -->
                                        <div class="group/input space-y-1.5">
                                            <div
                                                class="ml-1 flex items-center gap-2"
                                            >
                                                <div
                                                    class="h-1 w-1 rounded-full bg-amber-400"
                                                ></div>
                                                <label
                                                    class="text-[10px] font-bold text-white/40 uppercase"
                                                    >External Premium
                                                    (BORROW)</label
                                                >
                                            </div>
                                            <div
                                                class="flex items-center gap-2 rounded-xl border border-white/10 bg-black/40 px-3 py-2 transition-colors focus-within:border-amber-500/50"
                                            >
                                                <input
                                                    v-model.number="
                                                        assumptions.borrow_premium_pct
                                                    "
                                                    type="number"
                                                    class="w-full bg-transparent text-sm font-bold text-white outline-none"
                                                    @change="saveAssumptions"
                                                />
                                                <span
                                                    class="font-bold text-amber-400"
                                                    >%</span
                                                >
                                            </div>
                                        </div>

                                        <!-- Bot Maintenance -->
                                        <div class="group/input space-y-1.5">
                                            <div
                                                class="ml-1 flex items-center gap-2"
                                            >
                                                <div
                                                    class="h-1 w-1 rounded-full bg-purple-400"
                                                ></div>
                                                <label
                                                    class="text-[10px] font-bold text-white/40 uppercase"
                                                    >Monthly Bot Ops
                                                    (BOT)</label
                                                >
                                            </div>
                                            <div
                                                class="flex items-center gap-2 rounded-xl border border-white/10 bg-black/40 px-3 py-2 transition-colors focus-within:border-purple-500/50"
                                            >
                                                <span
                                                    class="font-bold text-purple-400"
                                                    >$</span
                                                >
                                                <input
                                                    v-model.number="
                                                        assumptions.bot_monthly_cost
                                                    "
                                                    type="number"
                                                    class="w-full bg-transparent text-sm font-bold text-white outline-none"
                                                    @change="saveAssumptions"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="border-t border-white/5 bg-indigo-500/5 p-4 text-center"
                            >
                                <StButtonGlass
                                    variant="primary"
                                    size="sm"
                                    class="w-full"
                                    :loading="saving"
                                    @click="saveAssumptions"
                                >
                                    Commit Parameters
                                </StButtonGlass>
                            </div>
                        </StCardGlass>
                    </div>

                    <!-- Column: Investment Summary & Breakdown -->
                    <div class="space-y-8 lg:col-span-8 xl:col-span-9">
                        <!-- Top Summary Stats -->
                        <StCardGlass
                            variant="glass"
                            class="border-white/10 bg-indigo-500/[0.02]!"
                            :no-hover="true"
                        >
                            <div
                                class="flex flex-col items-center justify-between gap-8 md:flex-row"
                            >
                                <div class="flex items-center gap-5">
                                    <div
                                        class="flex h-16 w-16 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_30px_rgba(99,102,241,0.2)]"
                                    >
                                        <v-icon
                                            icon="mdi-cash-multiple"
                                            color="indigo-300"
                                            size="32"
                                        />
                                    </div>
                                    <div>
                                        <span
                                            class="text-[10px] font-black tracking-[0.4em] text-white/30 uppercase"
                                            >Neural Capex Synthesis</span
                                        >
                                        <h3
                                            class="text-4xl font-black tracking-tighter text-white"
                                        >
                                            {{
                                                formatCurrency(totalInvestment)
                                            }}
                                        </h3>
                                    </div>
                                </div>

                                <div
                                    class="grid w-full max-w-2xl flex-grow grid-cols-2 gap-4 lg:grid-cols-4"
                                >
                                    <div
                                        v-for="stat in strategySummary"
                                        :key="stat.type"
                                        class="group rounded-2xl border border-white/5 bg-white/5 p-4 text-center transition-all hover:bg-white/10"
                                    >
                                        <div
                                            class="mb-1 text-[8px] font-black tracking-widest text-white/20 uppercase"
                                        >
                                            {{ stat.label }}
                                        </div>
                                        <div
                                            class="text-base font-black text-white"
                                            :style="`color: ${getHexColor(stat.color)}`"
                                        >
                                            {{ formatCurrency(stat.total) }}
                                        </div>
                                        <div
                                            class="text-[9px] font-bold text-white/30"
                                        >
                                            {{ stat.count }} Paths
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Composition Bar -->
                            <div
                                class="mt-8 flex h-1.5 w-full overflow-hidden rounded-full bg-white/5"
                            >
                                <div
                                    v-for="stat in strategySummary"
                                    :key="'bar-' + stat.type"
                                    class="h-full transition-all duration-1000 first:rounded-l-full last:rounded-r-full"
                                    :style="{
                                        width:
                                            (totalInvestment > 0
                                                ? (stat.total /
                                                      totalInvestment) *
                                                  100
                                                : 0) + '%',
                                        backgroundColor: getHexColor(
                                            stat.color,
                                        ),
                                        boxShadow: `inset 0 0 10px rgba(0,0,0,0.2)`,
                                    }"
                                ></div>
                            </div>
                        </StCardGlass>

                        <!-- Cost Breakdown Table -->
                        <StCardGlass
                            variant="glass"
                            class="overflow-hidden border-white/10 p-0!"
                            :no-hover="true"
                        >
                            <div
                                class="flex items-center justify-between border-b border-white/10 bg-white/5 px-8 py-5"
                            >
                                <h4
                                    class="text-sm font-black tracking-widest text-white uppercase"
                                >
                                    Tactical Cost Breakdown
                                </h4>
                                <span
                                    class="text-[10px] font-bold tracking-widest text-white/40"
                                    >{{ calculatedStrategies.length }} NODES
                                    ACTIVE</span
                                >
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse text-left">
                                    <thead>
                                        <tr class="bg-white/2">
                                            <th
                                                class="px-8 py-4 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                            >
                                                Tactical Type
                                            </th>
                                            <th
                                                class="px-8 py-4 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                            >
                                                Asset Node
                                            </th>
                                            <th
                                                class="px-8 py-4 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                            >
                                                Execution Logic
                                            </th>
                                            <th
                                                class="px-8 py-4 text-right text-[10px] font-black tracking-widest text-white/20 uppercase"
                                            >
                                                Est. Investment
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        <tr
                                            v-for="item in calculatedStrategies"
                                            :key="item.id"
                                            class="group transition-colors hover:bg-white/[0.02]"
                                        >
                                            <td class="px-8 py-5">
                                                <StBadgeGlass
                                                    :variant="
                                                        getStrategyBadgeVariant(
                                                            item.strategy,
                                                        )
                                                    "
                                                    size="xs"
                                                    class="px-3!"
                                                >
                                                    {{
                                                        item.strategy.toUpperCase()
                                                    }}
                                                </StBadgeGlass>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div
                                                    class="font-bold tracking-tight text-white uppercase transition-colors group-hover:text-indigo-300"
                                                >
                                                    {{
                                                        item.role_name ||
                                                        'Generic Asset'
                                                    }}
                                                </div>
                                                <div
                                                    class="text-[10px] tracking-widest text-white/40 uppercase"
                                                >
                                                    {{ item.skill_name }}
                                                </div>
                                            </td>
                                            <td class="px-8 py-5">
                                                <div
                                                    class="flex flex-col gap-1"
                                                >
                                                    <span
                                                        class="text-[11px] leading-none font-medium text-white/60 italic"
                                                        >{{
                                                            getCalculationBase(
                                                                item,
                                                            )
                                                        }}</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-8 py-5 text-right">
                                                <span
                                                    class="text-base font-black tracking-tight text-white"
                                                    >{{
                                                        formatCurrency(
                                                            item.calculated_cost,
                                                        )
                                                    }}</span
                                                >
                                            </td>
                                        </tr>
                                        <tr
                                            v-if="
                                                calculatedStrategies.length ===
                                                0
                                            "
                                        >
                                            <td
                                                colspan="4"
                                                class="px-8 py-12 text-center text-sm font-medium text-white/20 italic"
                                            >
                                                No tactical strategies active
                                                for financial synthesis.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </StCardGlass>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import ImpactAnalyticsDashboard from './ImpactAnalytics.vue';

const props = defineProps({
    scenarioId: {
        type: [Number, String],
        required: true,
    },
});

const notification = useNotification();
const loading = ref(false);
const saving = ref(false);
const strategies = ref<any[]>([]);
const activeTab = ref('impact');

const tabs = [
    { label: 'Impact Vectors', value: 'impact', icon: 'mdi-chart-radar' },
    { label: 'Financial Matrix', value: 'budget', icon: 'mdi-cash-multiple' },
];

const assumptions = ref({
    avg_annual_salary: 48000,
    buy_hiring_fee_pct: 15,
    build_training_unit_cost: 1200,
    borrow_premium_pct: 30,
    bot_monthly_cost: 250,
});

const loadData = async () => {
    loading.value = true;
    try {
        const [scenarioRes, strategyRes] = await Promise.all([
            axios.get(`/api/strategic-planning/scenarios/${props.scenarioId}`),
            axios.get(
                `/api/strategic-planning/scenarios/${props.scenarioId}/strategies`,
            ),
        ]);

        const scenario = scenarioRes.data.data || scenarioRes.data;
        if (scenario.assumptions?.financial) {
            assumptions.value = {
                ...assumptions.value,
                ...scenario.assumptions.financial,
            };
        }
        strategies.value = strategyRes.data.data || [];
    } catch (error) {
        console.error('Data load failure:', error);
        notification.showError('Financial synthesis failed');
    } finally {
        loading.value = false;
    }
};

const saveAssumptions = async () => {
    saving.value = true;
    try {
        await axios.patch(
            `/api/strategic-planning/scenarios/${props.scenarioId}`,
            {
                assumptions: { financial: assumptions.value },
            },
        );
        notification.showSuccess('Neural parameters updated');
    } catch (error) {
        console.error('Save failure:', error);
        notification.showError('Persistence failed');
    } finally {
        saving.value = false;
    }
};

const calculatedStrategies = computed(() => {
    return strategies.value.map((s) => {
        let cost = 0;
        const monthlySalary = assumptions.value.avg_annual_salary / 12;

        switch (s.strategy?.toLowerCase()) {
            case 'buy':
                cost =
                    assumptions.value.avg_annual_salary *
                    (assumptions.value.buy_hiring_fee_pct / 100);
                break;
            case 'build':
                cost = assumptions.value.build_training_unit_cost;
                break;
            case 'borrow':
                cost =
                    monthlySalary *
                    6 *
                    (1 + assumptions.value.borrow_premium_pct / 100);
                break;
            case 'bot':
                cost = 2000 + assumptions.value.bot_monthly_cost * 12;
                break;
            default:
                cost = s.estimated_cost || 0;
        }

        return { ...s, calculated_cost: cost };
    });
});

const strategySummary = computed(() => {
    const types = [
        { type: 'build', label: 'Build', color: 'indigo' },
        { type: 'buy', label: 'Buy', color: 'emerald' },
        { type: 'borrow', label: 'Borrow', color: 'amber' },
        { type: 'bot', label: 'Bot', color: 'purple' },
    ];

    return types.map((t) => {
        const filtered = calculatedStrategies.value.filter(
            (s) => s.strategy?.toLowerCase() === t.type,
        );
        return {
            ...t,
            count: filtered.length,
            total: filtered.reduce(
                (acc, curr) => acc + curr.calculated_cost,
                0,
            ),
        };
    });
});

const totalInvestment = computed(() =>
    calculatedStrategies.value.reduce(
        (acc, curr) => acc + curr.calculated_cost,
        0,
    ),
);

const getCalculationBase = (item: any) => {
    switch (item.strategy?.toLowerCase()) {
        case 'buy':
            return `${assumptions.value.buy_hiring_fee_pct}% of Annual Salary`;
        case 'build':
            return `Neural Upskilling Unit Cost`;
        case 'borrow':
            return `6m @ ${assumptions.value.borrow_premium_pct}% premium`;
        case 'bot':
            return `Setup + 12m Maintenance`;
        default:
            return 'Manual Override';
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(val);
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

const getStrategyBadgeVariant = (type: string | null) => {
    switch (type?.toLowerCase()) {
        case 'buy':
            return 'success';
        case 'build':
            return 'primary';
        case 'borrow':
            return 'secondary';
        case 'bot':
            return 'secondary';
        default:
            return 'glass';
    }
};

onMounted(loadData);
</script>

<style scoped>
.tab-content {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.3s ease;
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateX(20px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateX(-20px);
}
</style>
