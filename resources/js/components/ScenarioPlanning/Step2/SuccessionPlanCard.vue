<template>
    <div class="succession-plan-container relative min-h-[400px]">
        <!-- Glows -->
        <div
            class="pointer-events-none absolute -top-24 -right-24 h-64 w-64 bg-indigo-500/10 blur-[100px]"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-24 -left-24 h-64 w-64 bg-emerald-500/10 blur-[100px]"
        ></div>

        <div class="relative z-10 mb-8">
            <h3 class="mb-1 text-2xl font-black tracking-tight text-white">
                Succession <span class="text-indigo-400">Planning</span>
            </h3>
            <p class="text-sm font-medium text-white/40">
                Identify key successors and transition timelines for critical
                organizational nodes
            </p>
        </div>

        <!-- Feedback Alerts -->
        <transition-group name="fade">
            <div
                v-if="error"
                key="error"
                class="mb-4 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 backdrop-blur-xl"
            >
                <div class="flex items-center gap-3">
                    <v-icon color="rose-400" size="20">mdi-alert-circle</v-icon>
                    <span class="text-sm font-bold text-rose-200">{{
                        error
                    }}</span>
                </div>
            </div>
            <div
                v-if="success"
                key="success"
                class="mb-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-4 backdrop-blur-xl"
            >
                <div class="flex items-center gap-3">
                    <v-icon color="emerald-400" size="20"
                        >mdi-check-circle</v-icon
                    >
                    <span class="text-sm font-bold text-emerald-200">{{
                        success
                    }}</span>
                </div>
            </div>
        </transition-group>

        <!-- Loading -->
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
                >Analyzing Continuity...</span
            >
        </div>

        <!-- Plans Grid -->
        <div v-else class="grid grid-cols-1 gap-8">
            <div
                v-if="plans.length === 0"
                class="flex flex-col items-center justify-center rounded-3xl border border-white/5 bg-white/5 py-16 text-center"
            >
                <v-icon size="48" class="mb-4 opacity-20"
                    >mdi-account-switch-outline</v-icon
                >
                <p class="text-lg font-bold text-white/30">
                    No succession plans active
                </p>
                <p class="text-sm text-white/20">
                    Critical positions have not been mapped for this scenario.
                </p>
            </div>

            <div
                v-for="plan in plans"
                :key="plan.id"
                class="group transition-all duration-500"
            >
                <StCardGlass
                    variant="media"
                    class="overflow-hidden border-white/10 bg-white/5 transition-all hover:border-indigo-500/30"
                >
                    <!-- Critical Node Header -->
                    <div
                        class="relative flex h-36 flex-col justify-end overflow-hidden border-b border-white/5 bg-white/5 p-6"
                    >
                        <!-- Background Position Badge -->
                        <div
                            class="pointer-events-none absolute -top-8 -right-4 text-[120px] leading-none font-black text-white/[0.03] uppercase select-none"
                        >
                            {{ plan.criticality }}
                        </div>

                        <div
                            class="relative z-10 flex items-start justify-between"
                        >
                            <div>
                                <StBadgeGlass
                                    :variant="
                                        getCriticalityBadge(plan.criticality)
                                    "
                                    size="sm"
                                    class="mb-2"
                                >
                                    {{ plan.criticality.toUpperCase() }} NODE
                                </StBadgeGlass>
                                <h4
                                    class="text-2xl font-black tracking-tight text-white transition-colors group-hover:text-indigo-300"
                                >
                                    {{ plan.position_name }}
                                </h4>
                                <div class="mt-1 flex items-center gap-2">
                                    <v-icon size="14" class="text-white/20"
                                        >mdi-office-building-outline</v-icon
                                    >
                                    <span
                                        class="text-xs font-bold tracking-widest text-white/40 uppercase"
                                        >{{ plan.department }}</span
                                    >
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <StButtonGlass
                                    variant="ghost"
                                    circle
                                    icon="mdi-pencil-outline"
                                    size="sm"
                                    @click="editPlan(plan)"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Current Asset Details -->
                    <div
                        class="grid grid-cols-1 divide-y divide-white/5 border-b border-white/5 md:grid-cols-2 md:divide-x md:divide-y-0"
                    >
                        <div class="p-6">
                            <span
                                class="mb-4 block text-[9px] leading-none font-black tracking-[0.2em] text-white/20 uppercase"
                                >Current Strategic Asset</span
                            >
                            <div class="flex items-center gap-4">
                                <v-avatar
                                    size="48"
                                    class="border border-white/10 bg-white/5"
                                >
                                    <v-icon color="white/30"
                                        >mdi-account-tie</v-icon
                                    >
                                </v-avatar>
                                <div>
                                    <p
                                        class="text-lg leading-tight font-black text-white"
                                    >
                                        {{ plan.current_holder_name }}
                                    </p>
                                    <p
                                        class="text-xs font-medium text-white/40"
                                    >
                                        Tenure:
                                        {{ plan.years_in_position }} Years
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-6">
                            <div class="space-y-1">
                                <span
                                    class="block text-[9px] leading-none font-black tracking-[0.2em] text-white/20 uppercase"
                                    >Lifecycle Risk</span
                                >
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xl font-bold text-white"
                                        >Retirement:
                                        {{ plan.estimated_retirement }}</span
                                    >
                                </div>
                            </div>
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-amber-500/20 bg-amber-500/10"
                            >
                                <v-icon color="amber-400" size="20"
                                    >mdi-calendar-clock</v-icon
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Succession Intelligence -->
                    <div class="p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <span
                                class="text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                >Identified Successors</span
                            >
                            <StBadgeGlass
                                v-if="plan.successors?.length"
                                variant="glass"
                                size="xs"
                            >
                                {{ plan.successors.length }} Protocols Ready
                            </StBadgeGlass>
                        </div>

                        <div v-if="plan.successors?.length" class="space-y-4">
                            <div
                                v-for="(successor, idx) in plan.successors"
                                :key="successor.id"
                                class="group/item relative overflow-hidden rounded-2xl border border-white/10 bg-black/40 p-5 transition-all hover:border-indigo-500/30"
                            >
                                <div
                                    class="pointer-events-none absolute -right-8 -bottom-8 h-24 w-24 bg-indigo-500/5 opacity-0 blur-3xl transition-opacity group-hover/item:opacity-100"
                                ></div>

                                <div
                                    class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center"
                                >
                                    <!-- Readiness Hex -->
                                    <div
                                        class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10"
                                    >
                                        <span
                                            class="mb-1 text-xs leading-none font-black text-indigo-300"
                                            >Vector</span
                                        >
                                        <span
                                            class="text-lg leading-none font-black text-white"
                                            >{{ idx + 1 }}</span
                                        >
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="flex items-start justify-between gap-2"
                                        >
                                            <div>
                                                <p
                                                    class="text-base leading-tight font-black text-white"
                                                >
                                                    {{ successor.name }}
                                                </p>
                                                <p
                                                    class="mt-0.5 text-xs font-bold text-white/40"
                                                >
                                                    {{ successor.current_role }}
                                                </p>
                                            </div>
                                            <StBadgeGlass
                                                :variant="
                                                    getReadinessBadge(
                                                        successor.readiness_level,
                                                    )
                                                "
                                                size="sm"
                                            >
                                                {{
                                                    formatReadiness(
                                                        successor.readiness_level,
                                                    ).toUpperCase()
                                                }}
                                            </StBadgeGlass>
                                        </div>

                                        <!-- Progress Mapping -->
                                        <div class="mt-4 space-y-2">
                                            <div
                                                class="flex items-center justify-between text-[10px] font-black tracking-widest text-indigo-400/60 uppercase"
                                            >
                                                <span>Readiness Velocity</span>
                                                <span
                                                    >{{
                                                        successor.readiness_percentage
                                                    }}%</span
                                                >
                                            </div>
                                            <div
                                                class="h-1.5 w-full overflow-hidden rounded-full border border-white/5 bg-white/5 p-[1px]"
                                            >
                                                <div
                                                    class="h-full rounded-full transition-all duration-1000 ease-in-out"
                                                    :style="{
                                                        width: `${successor.readiness_percentage}%`,
                                                        backgroundColor:
                                                            getReadinessColor(
                                                                successor.readiness_level,
                                                            ),
                                                        boxShadow: `0 0 10px ${getReadinessColor(successor.readiness_level)}80`,
                                                    }"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Development Footer -->
                                <div
                                    class="mt-4 flex flex-wrap items-center gap-4 border-t border-white/5 pt-4"
                                >
                                    <div class="flex items-center gap-1.5">
                                        <v-icon size="14" class="text-amber-400"
                                            >mdi-timer-sand</v-icon
                                        >
                                        <span
                                            class="text-[10px] font-bold text-white/60 uppercase"
                                            >{{
                                                successor.timeline_months
                                            }}
                                            Months to Ready</span
                                        >
                                    </div>
                                    <div
                                        v-if="successor.development_plan"
                                        class="flex min-w-0 flex-1 items-center gap-1.5"
                                    >
                                        <v-icon
                                            size="14"
                                            class="text-indigo-400"
                                            >mdi-auto-fix</v-icon
                                        >
                                        <span
                                            class="truncate text-[10px] font-bold text-white/40 italic"
                                            >"{{
                                                successor.development_plan
                                            }}"</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="flex items-center gap-4 rounded-2xl border border-rose-500/20 bg-rose-500/5 p-6"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl border border-rose-500/30 bg-rose-500/20"
                            >
                                <v-icon color="rose-400"
                                    >mdi-account-alert-outline</v-icon
                                >
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-rose-200">
                                    Critical Gap Flagged
                                </h4>
                                <p class="text-xs font-medium text-rose-200/50">
                                    No viable successors identified for this
                                    strategic node. Immediate recruitment or
                                    talent development required.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="flex items-center gap-3 border-t border-white/5 bg-black/20 p-6"
                    >
                        <StButtonGlass
                            variant="glass"
                            size="sm"
                            icon="mdi-timeline-outline"
                            @click="viewTimeline(plan)"
                        >
                            View Roadmap
                        </StButtonGlass>
                        <v-spacer />
                        <StButtonGlass
                            variant="ghost"
                            size="sm"
                            class="text-rose-400/60 hover:text-rose-400"
                            icon="mdi-delete-outline"
                            @click="deletePlan(plan.id)"
                        >
                            Retire Plan
                        </StButtonGlass>
                    </div>
                </StCardGlass>
            </div>
        </div>

        <!-- Edit Dialog -->
        <v-dialog
            v-model="showEditDialog"
            max-width="550px"
            class="backdrop-blur-sm"
        >
            <StCardGlass
                v-if="editingPlan"
                variant="media"
                class="overflow-hidden border-indigo-500/20"
            >
                <div
                    class="flex items-center justify-between border-b border-white/5 p-6"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-white/5"
                        >
                            <v-icon color="indigo-300" size="24"
                                >mdi-file-edit-outline</v-icon
                            >
                        </div>
                        <h2 class="text-xl font-black text-white">
                            Modify Succession Plan
                        </h2>
                    </div>
                </div>

                <div class="space-y-6 p-8">
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Position Node</label
                        >
                        <div
                            class="rounded-xl border border-white/5 bg-white/5 p-4 font-black text-white"
                        >
                            {{ editingPlan.position_name }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Criticality Vector</label
                        >
                        <v-select
                            v-model="editingPlan.criticality"
                            :items="criticalities"
                            variant="outlined"
                            density="comfortable"
                            base-color="white/10"
                            color="indigo-400"
                            class="custom-glass-input"
                        />
                    </div>

                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                            >Intelligence Notes</label
                        >
                        <v-textarea
                            v-model="editingPlan.notes"
                            placeholder="Add strategic context..."
                            variant="outlined"
                            rows="4"
                            density="comfortable"
                            base-color="white/10"
                            color="indigo-400"
                        />
                    </div>
                </div>

                <div
                    class="flex justify-end gap-3 border-t border-white/5 bg-black/40 p-6"
                >
                    <StButtonGlass
                        variant="ghost"
                        @click="showEditDialog = false"
                        >Cancel</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        :loading="saving"
                        @click="savePlan"
                        >Commit Changes</StButtonGlass
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
import { onMounted, ref, watch } from 'vue';

interface SkillGap {
    id: number;
    skill_name: string;
    current_level: number;
    required_level: number;
}

interface Successor {
    id: number;
    name: string;
    current_role: string;
    readiness_level:
        | 'ready_now'
        | 'ready_12_months'
        | 'ready_24_months'
        | 'not_ready';
    readiness_percentage: number;
    skill_gaps: SkillGap[];
    timeline_months: number;
    development_plan?: string;
}

interface SuccessionPlan {
    id: number;
    position_name: string;
    department: string;
    criticality: 'critical' | 'high' | 'medium' | 'low';
    current_holder_name: string;
    current_holder_age: number;
    years_in_position: number;
    estimated_retirement: string;
    successors: Successor[];
    notes?: string;
}

interface Props {
    scenarioId: number;
}

const props = defineProps<Props>();

const loading = ref(true);
const saving = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);

const plans = ref<SuccessionPlan[]>([]);
const showEditDialog = ref(false);
const editingPlan = ref<SuccessionPlan | null>(null);

const criticalities = ['critical', 'high', 'medium', 'low'];

const loadPlans = async () => {
    try {
        loading.value = true;
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/succession-plans`,
        );

        if (!response.ok) throw new Error('Data sync failure');

        const data = await response.json();
        plans.value = data.data || [];
    } catch (err: any) {
        error.value = 'Neural link to succession data failed';
    } finally {
        loading.value = false;
    }
};

const getCriticalityBadge = (criticality: string) => {
    const map: Record<string, string> = {
        critical: 'primary',
        high: 'secondary',
        medium: 'glass',
        low: 'glass',
    };
    return map[criticality] || 'glass';
};

const getReadinessColor = (level: string): string => {
    const colors: Record<string, string> = {
        ready_now: '#10b981',
        ready_12_months: '#6366f1',
        ready_24_months: '#fbbf24',
        not_ready: '#f43f5e',
    };
    return colors[level] || '#ffffff';
};

const getReadinessBadge = (level: string) => {
    const map: Record<string, string> = {
        ready_now: 'secondary',
        ready_12_months: 'primary',
        ready_24_months: 'glass',
        not_ready: 'secondary', // Using custom color instead
    };
    return map[level] || 'glass';
};

const formatReadiness = (level: string): string => {
    const labels: Record<string, string> = {
        ready_now: 'Ready Now',
        ready_12_months: '12 Months',
        ready_24_months: '24 Months',
        not_ready: 'Not Ready',
    };
    return labels[level] || level;
};

const editPlan = (plan: SuccessionPlan) => {
    editingPlan.value = { ...plan };
    showEditDialog.value = true;
};

const savePlan = async () => {
    if (!editingPlan.value) return;

    try {
        saving.value = true;
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/succession-plans/${editingPlan.value.id}`,
            {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    notes: editingPlan.value.notes,
                    criticality: editingPlan.value.criticality,
                }),
            },
        );

        if (!response.ok) throw new Error('Encryption of modified data failed');

        success.value = 'Strategic plan committed successfully';
        showEditDialog.value = false;
        setTimeout(() => (success.value = null), 3000);
        await loadPlans();
    } catch (err: any) {
        error.value = err.message;
    } finally {
        saving.value = false;
    }
};

const deletePlan = async (id: number) => {
    if (!confirm('Permanent deletion of succession protocol?')) return;

    try {
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/succession-plans/${id}`,
            { method: 'DELETE' },
        );

        if (!response.ok) throw new Error('Protocol decommissioning failed');

        success.value = 'Protocol terminated';
        setTimeout(() => (success.value = null), 3000);
        await loadPlans();
    } catch (err: any) {
        error.value = err.message;
    }
};

const viewTimeline = (plan: SuccessionPlan) => {
    console.log('Projecting timeline for:', plan.position_name);
};

onMounted(() => {
    loadPlans();
});

watch(
    () => props.scenarioId,
    () => {
        loadPlans();
    },
);
</script>

<style scoped>
.succession-plan-container {
    padding: 0;
}
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
