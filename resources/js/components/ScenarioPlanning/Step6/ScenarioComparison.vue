<template>
    <div class="scenario-comparison animate-in space-y-8 duration-700 fade-in">
        <!-- Dashboard Header -->
        <div
            class="comparison-header flex flex-col justify-between gap-6 md:flex-row md:items-end"
        >
            <div class="flex items-start gap-5">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.3)]"
                >
                    <v-icon
                        icon="mdi-layers-triple"
                        color="indigo-300"
                        size="24"
                    />
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-white">
                        Version <span class="text-indigo-400">Benchmark</span>
                    </h2>
                    <p class="mt-1 text-sm font-medium text-white/40">
                        Cross-scenario structural and financial delta analysis.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <StButtonGlass
                    variant="primary"
                    icon="mdi-compare-horizontal"
                    :disabled="selectedIds.length < 2"
                    :loading="loadingData"
                    @click="fetchComparisonData"
                >
                    Execute Benchmark
                </StButtonGlass>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
            <!-- Sidebar: Available Versions -->
            <div class="lg:col-span-3">
                <StCardGlass
                    variant="glass"
                    border-accent="indigo"
                    class="sticky top-8 overflow-hidden p-0!"
                >
                    <div
                        class="flex items-center gap-3 border-b border-white/10 bg-indigo-500/10 px-6 py-4"
                    >
                        <v-icon
                            icon="mdi-history"
                            color="indigo-300"
                            size="18"
                        />
                        <h4
                            class="text-xs font-black tracking-widest text-white/80 uppercase"
                        >
                            Scenario History
                        </h4>
                    </div>

                    <div
                        class="custom-scrollbar max-h-[500px] overflow-y-auto p-2"
                    >
                        <div
                            v-if="loadingVersions"
                            class="flex flex-col items-center justify-center py-12"
                        >
                            <v-progress-circular
                                indeterminate
                                color="indigo-400"
                                size="24"
                                width="2"
                            />
                        </div>
                        <div
                            v-else-if="availableVersions.length === 0"
                            class="py-12 text-center"
                        >
                            <p
                                class="text-[10px] font-bold text-white/20 uppercase"
                            >
                                No alternative versions
                            </p>
                        </div>
                        <div v-else class="space-y-1">
                            <div
                                v-for="version in availableVersions"
                                :key="version.id"
                                @click="toggleSelection(version.id)"
                                class="group flex cursor-pointer items-center gap-4 rounded-xl px-4 py-3 transition-all duration-300"
                                :class="
                                    selectedIds.includes(version.id)
                                        ? 'border border-indigo-500/30 bg-indigo-500/20'
                                        : 'border border-transparent hover:bg-white/5'
                                "
                            >
                                <div
                                    class="flex h-4 w-4 items-center justify-center rounded border transition-colors"
                                    :class="
                                        selectedIds.includes(version.id)
                                            ? 'border-indigo-400 bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]'
                                            : 'border-white/20 bg-black/40'
                                    "
                                >
                                    <v-icon
                                        v-if="selectedIds.includes(version.id)"
                                        icon="mdi-check"
                                        color="white"
                                        size="10"
                                    />
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <div
                                        class="mb-0.5 flex items-center justify-between"
                                    >
                                        <span
                                            class="mr-2 truncate text-xs font-black text-white"
                                            >v{{ version.version_number }}:
                                            {{ version.name }}</span
                                        >
                                        <div
                                            v-if="
                                                version.id ===
                                                Number(scenarioId)
                                            "
                                            class="h-1.5 w-1.5 rounded-full bg-indigo-400 shadow-[0_0_8px_rgba(99,102,241,0.8)]"
                                        ></div>
                                    </div>
                                    <div
                                        class="text-[9px] font-bold tracking-widest text-white/30 uppercase"
                                    >
                                        {{ formatDate(version.created_at) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </StCardGlass>
            </div>

            <!-- Main Comparison Table -->
            <div class="lg:col-span-9">
                <transition name="fade-slide" mode="out-in">
                    <StCardGlass
                        v-if="comparisonData.length > 0"
                        key="data"
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
                                Operational Delta Matrix
                            </h4>
                            <div class="flex items-center gap-4">
                                <StButtonGlass
                                    variant="ghost"
                                    size="xs"
                                    icon="mdi-refresh"
                                    @click="fetchComparisonData"
                                />
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table
                                class="w-full min-w-[800px] border-collapse text-left"
                            >
                                <thead>
                                    <tr class="bg-white/2">
                                        <th
                                            class="w-[220px] px-8 py-6 text-[10px] font-black tracking-widest text-white/20 uppercase"
                                        >
                                            Critical Dimension
                                        </th>
                                        <th
                                            v-for="item in comparisonData"
                                            :key="item.id"
                                            class="border-l border-white/5 px-8 py-6 text-center transition-colors"
                                            :class="
                                                item.id === Number(scenarioId)
                                                    ? 'bg-indigo-500/10'
                                                    : ''
                                            "
                                        >
                                            <div
                                                class="flex flex-col items-center gap-2"
                                            >
                                                <StBadgeGlass
                                                    variant="primary"
                                                    size="xs"
                                                    >v{{
                                                        item.version
                                                    }}</StBadgeGlass
                                                >
                                                <div
                                                    class="max-w-[120px] truncate text-[11px] font-black tracking-tight text-white uppercase"
                                                >
                                                    {{ item.name }}
                                                </div>
                                                <div
                                                    v-if="
                                                        item.id ===
                                                        Number(scenarioId)
                                                    "
                                                    class="text-[8px] leading-none font-black tracking-widest text-indigo-400 uppercase"
                                                >
                                                    Primary Ref
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <!-- Scenario IQ -->
                                    <tr
                                        class="group transition-colors hover:bg-white/[0.02]"
                                    >
                                        <td class="px-8 py-6">
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <v-icon
                                                    icon="mdi-brain"
                                                    color="indigo-400/50"
                                                    size="20"
                                                />
                                                <span
                                                    class="text-xs font-black tracking-widest text-white/60 uppercase"
                                                    >Neural Readiness (IQ)</span
                                                >
                                            </div>
                                        </td>
                                        <td
                                            v-for="item in comparisonData"
                                            :key="item.id"
                                            class="border-l border-white/5 px-8 py-6 text-center"
                                            :class="
                                                item.id === Number(scenarioId)
                                                    ? 'bg-indigo-500/[0.03]'
                                                    : ''
                                            "
                                        >
                                            <div
                                                class="flex flex-col items-center gap-3"
                                            >
                                                <div
                                                    class="relative flex h-12 w-12 items-center justify-center"
                                                >
                                                    <svg
                                                        class="h-full w-full -rotate-90 transform"
                                                    >
                                                        <circle
                                                            cx="24"
                                                            cy="24"
                                                            r="20"
                                                            stroke="currentColor"
                                                            stroke-width="3"
                                                            fill="transparent"
                                                            class="text-white/5"
                                                        ></circle>
                                                        <circle
                                                            cx="24"
                                                            cy="24"
                                                            r="20"
                                                            stroke="currentColor"
                                                            stroke-width="3"
                                                            fill="transparent"
                                                            :stroke-dasharray="
                                                                2 * Math.PI * 20
                                                            "
                                                            :stroke-dashoffset="
                                                                2 *
                                                                Math.PI *
                                                                20 *
                                                                (1 -
                                                                    item.iq /
                                                                        100)
                                                            "
                                                            :class="
                                                                getIqClass(
                                                                    item.iq,
                                                                )
                                                            "
                                                        ></circle>
                                                    </svg>
                                                    <span
                                                        class="absolute text-[11px] font-black text-white"
                                                        >{{ item.iq }}</span
                                                    >
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Investment -->
                                    <tr
                                        class="group transition-colors hover:bg-white/[0.02]"
                                    >
                                        <td class="px-8 py-6">
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <v-icon
                                                    icon="mdi-cash"
                                                    color="emerald-400/50"
                                                    size="20"
                                                />
                                                <span
                                                    class="text-xs font-black tracking-widest text-white/60 uppercase"
                                                    >Est. Capex Injection</span
                                                >
                                            </div>
                                        </td>
                                        <td
                                            v-for="item in comparisonData"
                                            :key="item.id"
                                            class="border-l border-white/5 px-8 py-6 text-center font-black text-white"
                                            :class="
                                                item.id === Number(scenarioId)
                                                    ? 'bg-indigo-500/[0.03]'
                                                    : ''
                                            "
                                        >
                                            <span
                                                class="text-base tracking-tight"
                                                >{{
                                                    formatCurrency(
                                                        item.total_cost,
                                                    )
                                                }}</span
                                            >
                                        </td>
                                    </tr>

                                    <!-- FTE Required -->
                                    <tr
                                        class="group transition-colors hover:bg-white/[0.02]"
                                    >
                                        <td class="px-8 py-6">
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <v-icon
                                                    icon="mdi-account-group"
                                                    color="indigo-400/50"
                                                    size="20"
                                                />
                                                <span
                                                    class="text-xs font-black tracking-widest text-white/60 uppercase"
                                                    >Target FTE Bandwidth</span
                                                >
                                            </div>
                                        </td>
                                        <td
                                            v-for="item in comparisonData"
                                            :key="item.id"
                                            class="border-l border-white/5 px-8 py-6 text-center text-white"
                                            :class="
                                                item.id === Number(scenarioId)
                                                    ? 'bg-indigo-500/[0.03]'
                                                    : ''
                                            "
                                        >
                                            <span
                                                class="text-lg font-black tracking-tight"
                                                >{{ item.total_req_fte }}</span
                                            >
                                        </td>
                                    </tr>

                                    <!-- Gap FTE -->
                                    <tr
                                        class="group transition-colors hover:bg-white/[0.02]"
                                    >
                                        <td class="px-8 py-6">
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <v-icon
                                                    icon="mdi-account-alert"
                                                    color="amber-400/50"
                                                    size="20"
                                                />
                                                <span
                                                    class="text-xs font-black tracking-widest text-white/60 uppercase"
                                                    >Capacity Gap Delta</span
                                                >
                                            </div>
                                        </td>
                                        <td
                                            v-for="item in comparisonData"
                                            :key="item.id"
                                            class="border-l border-white/5 px-8 py-6 text-center"
                                            :class="
                                                item.id === Number(scenarioId)
                                                    ? 'bg-indigo-500/[0.03]'
                                                    : ''
                                            "
                                        >
                                            <StBadgeGlass
                                                :variant="
                                                    item.gap_fte > 0
                                                        ? 'secondary'
                                                        : 'success'
                                                "
                                                size="xs"
                                                class="px-4! py-1!"
                                            >
                                                {{ item.gap_fte > 0 ? '+' : ''
                                                }}{{ item.gap_fte }} FTEs
                                            </StBadgeGlass>
                                        </td>
                                    </tr>

                                    <!-- Status -->
                                    <tr
                                        class="group transition-colors hover:bg-white/[0.02]"
                                    >
                                        <td class="px-8 py-6">
                                            <div
                                                class="flex items-center gap-3"
                                            >
                                                <v-icon
                                                    icon="mdi-list-status"
                                                    color="white/20"
                                                    size="20"
                                                />
                                                <span
                                                    class="text-xs font-black tracking-widest text-white/60 uppercase"
                                                    >Execution Status</span
                                                >
                                            </div>
                                        </td>
                                        <td
                                            v-for="item in comparisonData"
                                            :key="item.id"
                                            class="border-l border-white/5 px-8 py-6 text-center"
                                            :class="
                                                item.id === Number(scenarioId)
                                                    ? 'bg-indigo-500/[0.03]'
                                                    : ''
                                            "
                                        >
                                            <StBadgeGlass
                                                :variant="
                                                    getStatusVariant(
                                                        item.status,
                                                    )
                                                "
                                                size="xs"
                                            >
                                                {{
                                                    (
                                                        item.status || 'DRAFT'
                                                    ).toUpperCase()
                                                }}
                                            </StBadgeGlass>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </StCardGlass>

                    <!-- Empty State -->
                    <div
                        v-else
                        key="empty"
                        class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-white/10 bg-white/[0.02] py-32"
                    >
                        <div
                            class="mb-6 flex h-20 w-20 items-center justify-center rounded-full border border-white/5 bg-white/2"
                        >
                            <v-icon size="36" color="white/10"
                                >mdi-compare-horizontal</v-icon
                            >
                        </div>
                        <h3 class="mb-2 text-xl font-black text-white/60">
                            Cross-Version Synthesizer
                        </h3>
                        <p
                            class="mx-auto mb-8 max-w-xs text-center text-sm text-white/20"
                        >
                            Select at least two neural architectures from the
                            history panel to initialize delta comparison.
                        </p>
                        <StButtonGlass
                            v-if="selectedIds.length >= 2"
                            variant="primary"
                            icon="mdi-play"
                            @click="fetchComparisonData"
                        >
                            Run Analysis Now
                        </StButtonGlass>
                    </div>
                </transition>
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

const notification = useNotification();
const loadingVersions = ref(false);
const loadingData = ref(false);
const availableVersions = ref<any[]>([]);
const selectedIds = ref<number[]>([]);
const comparisonData = ref<any[]>([]);

const loadVersions = async () => {
    loadingVersions.value = true;
    try {
        const res = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/versions`,
        );
        availableVersions.value = res.data?.versions || [];

        selectedIds.value = [Number(props.scenarioId)];
        if (availableVersions.value.length > 0) {
            const others = availableVersions.value.filter(
                (v) => v.id !== Number(props.scenarioId),
            );
            if (others.length > 0) {
                selectedIds.value.push(others[0].id);
                fetchComparisonData();
            }
        }
    } catch (error) {
        notification.showError('Version history acquisition failed');
    } finally {
        loadingVersions.value = false;
    }
};

const fetchComparisonData = async () => {
    if (selectedIds.value.length < 2) return;

    loadingData.value = true;
    try {
        const res = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/compare-versions`,
            {
                params: { ids: selectedIds.value },
            },
        );
        comparisonData.value = res.data.data || [];
        comparisonData.value.sort((a, b) => a.version - b.version);
    } catch (error) {
        notification.showError('Comparative synthesis failed');
    } finally {
        loadingData.value = false;
    }
};

const toggleSelection = (id: number) => {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        if (selectedIds.value.length > 2) {
            selectedIds.value.splice(index, 1);
        } else {
            notification.showWarning('Benchmark requires minimum 2 nodes');
        }
    } else {
        if (selectedIds.value.length < 5) {
            selectedIds.value.push(id);
        } else {
            notification.showWarning('Max synthesis limit: 5 versions');
        }
    }
};

const formatDate = (date: string) => new Date(date).toLocaleDateString();

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(val);
};

const getIqClass = (iq: number) => {
    if (iq < 40) return 'text-rose-500';
    if (iq < 70) return 'text-amber-500';
    return 'text-emerald-500';
};

const getStatusVariant = (status: string) => {
    const map: Record<string, string> = {
        draft: 'glass',
        pending_approval: 'secondary',
        approved: 'primary',
        rejected: 'secondary',
    };
    return map[status?.toLowerCase()] || 'glass';
};

onMounted(loadVersions);
</script>

<style scoped>
.scenario-comparison {
    width: 100%;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(20px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
</style>
