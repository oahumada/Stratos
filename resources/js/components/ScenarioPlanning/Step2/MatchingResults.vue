<template>
    <div class="matching-results-container relative min-h-[400px]">
        <!-- Background Glows -->
        <div
            class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 bg-indigo-500/10 blur-[100px]"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 bg-emerald-500/10 blur-[100px]"
        ></div>

        <div class="relative z-10 mb-8 flex items-end justify-between">
            <div>
                <h3 class="mb-1 text-2xl font-black tracking-tight text-white">
                    Candidate-Position
                    <span class="text-indigo-400">Matching</span>
                </h3>
                <p class="text-sm font-medium text-white/40">
                    Compatibility assessment between available talent and
                    required roles
                </p>
            </div>
            <StButtonGlass
                variant="ghost"
                circle
                icon="mdi-information-outline"
                @click="showLegendDialog = true"
            />
        </div>

        <!-- Alerts -->
        <transition name="fade">
            <div
                v-if="error"
                class="mb-6 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 backdrop-blur-xl"
            >
                <div class="flex items-center gap-3">
                    <v-icon color="rose-400" size="20">mdi-alert-circle</v-icon>
                    <span class="text-sm font-bold text-rose-200">{{
                        error
                    }}</span>
                    <v-spacer />
                    <button
                        @click="error = null"
                        class="text-rose-400/50 hover:text-rose-400"
                    >
                        <v-icon size="18">mdi-close</v-icon>
                    </button>
                </div>
            </div>
        </transition>

        <!-- Loading State -->
        <div
            v-if="loading"
            class="flex flex-col items-center justify-center py-20"
        >
            <v-progress-circular
                indeterminate
                color="indigo-400"
                size="48"
                width="3"
            />
            <span
                class="mt-4 text-xs font-black tracking-widest text-indigo-400/60 uppercase"
                >Calculating Affinities...</span
            >
        </div>

        <!-- Results Grid -->
        <div v-else class="space-y-6">
            <div
                v-if="results.length === 0"
                class="flex flex-col items-center justify-center rounded-3xl border border-white/5 bg-white/5 py-16 text-center"
            >
                <v-icon size="48" class="mb-4 opacity-20"
                    >mdi-account-search-outline</v-icon
                >
                <p class="text-lg font-bold text-white/30">
                    No matching results available
                </p>
                <p class="text-sm text-white/20">
                    The engine has not yet processed the alignment for this
                    scenario.
                </p>
            </div>

            <div
                v-for="result in results"
                :key="result.id"
                class="group transition-all duration-500"
            >
                <StCardGlass
                    variant="media"
                    class="overflow-hidden border-white/10 bg-white/5 transition-all hover:border-indigo-500/30"
                >
                    <div
                        class="flex flex-col gap-6 p-6 lg:flex-row lg:items-center"
                    >
                        <!-- Candidate Info -->
                        <div class="flex flex-1 items-start gap-4">
                            <v-avatar
                                size="64"
                                class="border-2 border-white/10 bg-indigo-500/20 shadow-xl"
                            >
                                <v-icon size="32" color="indigo-300"
                                    >mdi-account-star</v-icon
                                >
                            </v-avatar>
                            <div class="min-w-0">
                                <h4
                                    class="text-xl font-black tracking-tight text-white transition-colors group-hover:text-indigo-300"
                                >
                                    {{ result.candidate_name }}
                                </h4>
                                <div class="mt-1 flex items-center gap-2">
                                    <StBadgeGlass
                                        variant="glass"
                                        size="sm"
                                        class="px-2!"
                                    >
                                        {{ result.current_role }}
                                    </StBadgeGlass>
                                    <v-icon size="14" class="text-white/20"
                                        >mdi-arrow-right</v-icon
                                    >
                                    <StBadgeGlass
                                        variant="primary"
                                        size="sm"
                                        class="px-2! font-black"
                                    >
                                        {{ result.target_position }}
                                    </StBadgeGlass>
                                </div>
                            </div>
                        </div>

                        <!-- Match Metrics -->
                        <div class="flex flex-col items-end gap-2 lg:w-48">
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Compatibility</span
                                >
                                <span
                                    class="text-2xl leading-none font-black"
                                    :style="{
                                        color: getMatchColor(
                                            result.match_percentage,
                                        ),
                                    }"
                                >
                                    {{ result.match_percentage }}%
                                </span>
                            </div>
                            <div
                                class="h-1.5 w-full overflow-hidden rounded-full border border-white/10 bg-black/20"
                            >
                                <div
                                    class="h-full transition-all duration-1000 ease-out"
                                    :style="{
                                        width: `${result.match_percentage}%`,
                                        backgroundColor: getMatchColor(
                                            result.match_percentage,
                                        ),
                                        boxShadow: `0 0 12px ${getMatchColor(result.match_percentage)}80`,
                                    }"
                                ></div>
                            </div>
                        </div>

                        <!-- Highlights/Summary -->
                        <div class="flex flex-wrap gap-2 lg:w-64">
                            <div
                                class="flex h-12 flex-1 flex-col items-center justify-center rounded-xl border border-white/5 bg-white/5 p-2 text-center"
                            >
                                <span
                                    class="mb-1 text-[9px] leading-none font-black tracking-widest text-white/30 uppercase"
                                    >Timeline</span
                                >
                                <span class="text-xs font-bold text-white"
                                    >{{
                                        result.productivity_timeline
                                    }}
                                    Months</span
                                >
                            </div>
                            <div
                                v-if="result.risk_factors?.length"
                                class="flex h-12 flex-1 flex-col items-center justify-center rounded-xl border border-rose-500/10 bg-rose-500/5 p-2 text-center"
                            >
                                <span
                                    class="mb-1 text-[9px] leading-none font-black tracking-widest text-rose-400/50 uppercase"
                                    >Risks</span
                                >
                                <span class="text-xs font-bold text-rose-300"
                                    >{{
                                        result.risk_factors.length
                                    }}
                                    Active</span
                                >
                            </div>
                        </div>

                        <!-- Actions Quick -->
                        <div
                            class="flex shrink-0 items-center gap-2 border-t border-white/5 pt-4 lg:border-t-0 lg:pt-0"
                        >
                            <StButtonGlass
                                variant="glass"
                                size="sm"
                                @click="viewDetails(result)"
                            >
                                Details
                            </StButtonGlass>
                            <StButtonGlass
                                variant="secondary"
                                size="sm"
                                icon="mdi-check-decagram"
                                @click="acceptMatch(result.id)"
                            >
                                Accept
                            </StButtonGlass>
                        </div>
                    </div>

                    <!-- Expandable Insights (Optional, simple preview for now) -->
                    <div
                        v-if="result.skill_gaps?.length"
                        class="border-t border-white/5 bg-black/20 p-4 px-6"
                    >
                        <div
                            class="custom-scrollbar-hide flex items-center gap-4 overflow-x-auto pb-2"
                        >
                            <span
                                class="text-[9px] font-black tracking-[0.2em] whitespace-nowrap text-white/20 uppercase"
                                >Core Delts:</span
                            >
                            <div
                                v-for="gap in result.skill_gaps.slice(0, 4)"
                                :key="gap.id"
                                class="flex items-center gap-2 whitespace-nowrap opacity-70 transition-opacity hover:opacity-100"
                            >
                                <span
                                    class="text-[11px] font-bold text-white/80"
                                    >{{ gap.skill_name }}</span
                                >
                                <span
                                    class="text-[10px] font-black text-indigo-400"
                                    >L{{ gap.current_level }}→{{
                                        gap.required_level
                                    }}</span
                                >
                            </div>
                            <span
                                v-if="result.skill_gaps.length > 4"
                                class="text-[10px] font-bold text-white/20 italic"
                                >+{{ result.skill_gaps.length - 4 }} More</span
                            >
                        </div>
                    </div>
                </StCardGlass>
            </div>
        </div>

        <!-- Details Modal -->
        <v-dialog
            v-model="showDetailsDialog"
            max-width="650px"
            class="backdrop-blur-sm"
        >
            <StCardGlass
                v-if="selectedResult"
                variant="media"
                class="overflow-hidden border-indigo-500/20"
            >
                <div
                    class="flex items-center justify-between border-b border-white/5 p-6"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20"
                        >
                            <v-icon color="indigo-300" size="24"
                                >mdi-account-details</v-icon
                            >
                        </div>
                        <div>
                            <h2
                                class="mb-1 text-xl leading-none font-black text-white"
                            >
                                Alignment Intelligence
                            </h2>
                            <p
                                class="text-xs font-bold tracking-widest text-white/40 uppercase"
                            >
                                Protocol: Neural Matching v4
                            </p>
                        </div>
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        circle
                        icon="mdi-close"
                        @click="showDetailsDialog = false"
                    />
                </div>

                <div
                    class="custom-scrollbar max-h-[70vh] space-y-8 overflow-y-auto p-8"
                >
                    <!-- Profile Header -->
                    <div
                        class="flex items-center gap-6 rounded-3xl border border-white/5 bg-white/5 p-6"
                    >
                        <v-avatar
                            size="80"
                            class="border-2 border-white/10 bg-indigo-500/20 shadow-2xl"
                        >
                            <v-icon size="40" color="indigo-300"
                                >mdi-account-star</v-icon
                            >
                        </v-avatar>
                        <div>
                            <h3
                                class="text-2xl font-black tracking-tight text-white"
                            >
                                {{ selectedResult.candidate_name }}
                            </h3>
                            <p
                                class="text-xs font-bold tracking-widest text-indigo-400 uppercase"
                            >
                                Target: {{ selectedResult.target_position }}
                            </p>
                        </div>
                        <v-spacer />
                        <div class="text-right">
                            <div
                                class="text-3xl leading-none font-black"
                                :style="{
                                    color: getMatchColor(
                                        selectedResult.match_percentage,
                                    ),
                                }"
                            >
                                {{ selectedResult.match_percentage }}%
                            </div>
                            <span
                                class="text-[10px] font-black tracking-widest text-white/20 uppercase"
                                >Total Match</span
                            >
                        </div>
                    </div>

                    <!-- Risk Factors -->
                    <div
                        v-if="selectedResult.risk_factors?.length"
                        class="space-y-4"
                    >
                        <h4
                            class="text-[10px] font-black tracking-widest text-rose-400 uppercase"
                        >
                            Risk Assessment Vectors
                        </h4>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div
                                v-for="risk in selectedResult.risk_factors"
                                :key="risk.id"
                                class="flex items-center gap-3 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4"
                            >
                                <v-icon color="rose-400" size="20"
                                    >mdi-alert-octagon</v-icon
                                >
                                <span
                                    class="text-sm leading-tight font-bold text-rose-100"
                                    >{{ risk.factor }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Productivity Gradient -->
                    <div
                        class="relative space-y-4 overflow-hidden rounded-3xl border border-amber-500/10 bg-amber-500/5 p-6"
                    >
                        <div
                            class="pointer-events-none absolute -right-4 -bottom-4 h-24 w-24 bg-amber-500/10 blur-3xl"
                        ></div>
                        <div class="mb-4 flex items-center gap-2">
                            <v-icon color="amber-400" size="18"
                                >mdi-clock-fast</v-icon
                            >
                            <h4
                                class="text-[10px] font-black tracking-widest text-amber-400 uppercase"
                            >
                                Productivity Timeline
                            </h4>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">{{
                                selectedResult.productivity_timeline
                            }}</span>
                            <span class="text-xl font-bold text-white/40"
                                >Months</span
                            >
                        </div>
                        <p
                            class="text-sm leading-relaxed font-medium text-white/50"
                        >
                            Estimated period to reach peak operational
                            efficiency based on current skill level
                            differentials.
                        </p>
                    </div>

                    <!-- Full Skills Matrix -->
                    <div
                        v-if="selectedResult.skill_gaps?.length"
                        class="space-y-4"
                    >
                        <div class="flex items-center justify-between">
                            <h4
                                class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                            >
                                Skill Gap Analysis
                            </h4>
                            <StBadgeGlass variant="glass" size="sm"
                                >{{
                                    selectedResult.skill_gaps.length
                                }}
                                Metrics</StBadgeGlass
                            >
                        </div>
                        <div
                            class="overflow-hidden rounded-2xl border border-white/5 bg-black/20"
                        >
                            <table class="w-full text-left">
                                <thead
                                    class="border-b border-white/5 bg-white/5"
                                >
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-[10px] font-black tracking-widest text-white/30 uppercase"
                                        >
                                            Competency
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-[10px] font-black tracking-widest text-white/30 uppercase"
                                        >
                                            Current
                                        </th>
                                        <th
                                            class="px-4 py-3 text-center text-[10px] font-black tracking-widest text-white/30 uppercase"
                                        >
                                            Required
                                        </th>
                                        <th
                                            class="px-4 py-3 text-right text-[10px] font-black tracking-widest text-white/30 uppercase"
                                        >
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <tr
                                        v-for="gap in selectedResult.skill_gaps"
                                        :key="gap.id"
                                        class="transition-colors hover:bg-white/5"
                                    >
                                        <td
                                            class="px-4 py-3 text-sm font-bold text-white"
                                        >
                                            {{ gap.skill_name }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-center font-black text-white/40"
                                        >
                                            L{{ gap.current_level }}
                                        </td>
                                        <td
                                            class="px-4 py-3 text-center font-black text-indigo-400"
                                        >
                                            L{{ gap.required_level }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <v-icon
                                                :color="
                                                    gap.current_level >=
                                                    gap.required_level
                                                        ? 'emerald-400'
                                                        : 'amber-400'
                                                "
                                                size="16"
                                            >
                                                {{
                                                    gap.current_level >=
                                                    gap.required_level
                                                        ? 'mdi-check-circle'
                                                        : 'mdi-trending-up'
                                                }}
                                            </v-icon>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div v-if="selectedResult.notes" class="space-y-4">
                        <h4
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            Strategic Observations
                        </h4>
                        <div
                            class="rounded-2xl border border-white/5 bg-white/5 p-6 leading-relaxed font-medium text-white/70 italic"
                        >
                            "{{ selectedResult.notes }}"
                        </div>
                    </div>
                </div>

                <div
                    class="flex justify-end gap-3 border-t border-white/5 bg-black/40 p-6"
                >
                    <StButtonGlass
                        variant="ghost"
                        @click="showDetailsDialog = false"
                        >Dismiss</StButtonGlass
                    >
                    <StButtonGlass
                        variant="secondary"
                        icon="mdi-check-decagram"
                        @click="acceptMatch(selectedResult.id)"
                        >Accept Assignment</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>

        <!-- Legend Modal -->
        <v-dialog v-model="showLegendDialog" max-width="500px">
            <StCardGlass variant="media">
                <div class="border-b border-white/5 p-6">
                    <h2 class="text-xl font-black text-white">
                        Algorithm Legend
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div
                            v-for="item in legendItems"
                            :key="item.title"
                            class="group"
                        >
                            <h4
                                class="mb-1 text-sm font-black tracking-tight text-indigo-400 uppercase transition-colors group-hover:text-indigo-300"
                            >
                                {{ item.title }}
                            </h4>
                            <p
                                class="text-sm leading-relaxed font-medium text-white/50"
                            >
                                {{ item.description }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end border-t border-white/5 p-6">
                    <StButtonGlass
                        variant="primary"
                        size="sm"
                        @click="showLegendDialog = false"
                        >Understand</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { onMounted, ref, watch } from 'vue';

interface SkillGap {
    id: number;
    skill_name: string;
    current_level: number;
    required_level: number;
}

interface RiskFactor {
    id: number;
    factor: string;
}

interface MatchingResult {
    id: number;
    candidate_name: string;
    current_role: string;
    target_position: string;
    match_percentage: number;
    risk_factors?: RiskFactor[];
    productivity_timeline: number;
    skill_gaps?: SkillGap[];
    notes?: string;
}

interface Props {
    scenarioId: number;
}

const props = defineProps<Props>();

const loading = ref(true);
const error = ref<string | null>(null);
const results = ref<MatchingResult[]>([]);
const showDetailsDialog = ref(false);
const selectedResult = ref<MatchingResult | null>(null);
const showLegendDialog = ref(false);
const legendItems = [
    {
        title: 'Match % (Compatibility)',
        description:
            'Candidate–position alignment score (0–100), computed using skills, experience vectors, and team synergy protocols.',
    },
    {
        title: 'Productivity Timeline',
        description:
            'Estimated months required for the candidate to reach 100% operational velocity in the new role.',
    },
    {
        title: 'Risk Factors',
        description:
            'Detected anomalies that could affect long-term mission success (availability, critical gaps, mobility).',
    },
    {
        title: 'Skill Gap Analysis',
        description:
            'Differential mapping of current candidate mastery vs. required target role proficiency levels.',
    },
];

const api = useApi();

const loadResults = async () => {
    if (!props.scenarioId) return;

    try {
        loading.value = true;
        const response: any = await api.get(
            `/api/scenarios/${props.scenarioId}/step2/matching-results`,
        );

        results.value = (response.data || []).map((r: any) => ({
            ...r,
            match_percentage: r.match_percentage || 0,
            productivity_timeline: r.productivity_timeline || 0,
        }));
    } catch (err: any) {
        error.value =
            err.response?.data?.message || 'Evolutionary mapping failed';
    } finally {
        loading.value = false;
    }
};

const getMatchColor = (percentage: number): string => {
    if (percentage >= 80) return '#10b981'; // emerald-500
    if (percentage >= 60) return '#6366f1'; // indigo-500
    if (percentage >= 40) return '#f59e0b'; // amber-500
    return '#f43f5e'; // rose-500
};

const viewDetails = (result: MatchingResult) => {
    selectedResult.value = result;
    showDetailsDialog.value = true;
};

const acceptMatch = async (id: number) => {
    console.log('Accepting match for result id:', id);
    // Placeholder logic here
};

onMounted(() => {
    loadResults();
});

watch(
    () => props.scenarioId,
    () => {
        loadResults();
    },
);

defineExpose({
    loading,
    error,
    results,
    showDetailsDialog,
    selectedResult,
    showLegendDialog,
    loadResults,
    getMatchColor,
    viewDetails,
    acceptMatch,
});
</script>

<style scoped>
.matching-results-container {
    padding: 0;
}

.custom-scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.custom-scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
