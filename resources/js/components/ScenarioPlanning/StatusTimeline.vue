<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { computed, ref } from 'vue';

interface Props {
    scenarioId: number;
}

interface StatusEvent {
    id: number;
    from_decision_status: string | null;
    to_decision_status: string | null;
    from_execution_status: string | null;
    to_execution_status: string | null;
    changed_by: {
        name: string;
        email: string;
    };
    notes: string | null;
    created_at: string;
}

const props = defineProps<Props>();

const api = useApi();
const { showError } = useNotification();

const loading = ref(false);
const events = ref<StatusEvent[]>([]);
const showTimeline = ref(false);

const loadStatusEvents = async () => {
    loading.value = true;
    try {
        const res = await api.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}`,
        );
        events.value = res.data?.status_events || [];
    } catch (e) {
        void e;
        showError('Failed to load status history');
    } finally {
        loading.value = false;
    }
};

const openTimeline = () => {
    showTimeline.value = true;
    if (events.value.length === 0) {
        loadStatusEvents();
    }
};

const sortedEvents = computed(() => {
    return [...events.value].sort(
        (a, b) =>
            new Date(b.created_at).getTime() - new Date(a.created_at).getTime(),
    );
});

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getEventIcon = (event: StatusEvent): string => {
    if (event.to_decision_status === 'approved') return 'mdi-check-circle';
    if (event.to_decision_status === 'rejected') return 'mdi-close-circle';
    if (event.to_decision_status === 'pending_approval')
        return 'mdi-clock-alert';
    if (event.to_execution_status === 'in_progress') return 'mdi-play-circle';
    if (event.to_execution_status === 'paused') return 'mdi-pause-circle';
    if (event.to_execution_status === 'completed') return 'mdi-check-bold';
    return 'mdi-arrow-right-circle';
};

const getEventColor = (event: StatusEvent): string => {
    if (event.to_decision_status === 'approved') return 'emerald-400';
    if (event.to_decision_status === 'rejected') return 'rose-400';
    if (event.to_decision_status === 'pending_approval') return 'amber-400';
    if (event.to_execution_status === 'in_progress') return 'indigo-400';
    if (event.to_execution_status === 'paused') return 'amber-400';
    if (event.to_execution_status === 'completed') return 'emerald-400';
    return 'white/40';
};

const getEventBgColor = (event: StatusEvent): string => {
    if (event.to_decision_status === 'approved') return 'emerald-500/10';
    if (event.to_decision_status === 'rejected') return 'rose-500/10';
    if (event.to_decision_status === 'pending_approval') return 'amber-500/10';
    if (event.to_execution_status === 'in_progress') return 'indigo-500/10';
    if (event.to_execution_status === 'paused') return 'amber-500/10';
    if (event.to_execution_status === 'completed') return 'emerald-500/10';
    return 'white/5';
};

const getEventBorderColor = (event: StatusEvent): string => {
    if (event.to_decision_status === 'approved') return 'emerald-500/30';
    if (event.to_decision_status === 'rejected') return 'rose-500/30';
    if (event.to_decision_status === 'pending_approval') return 'amber-500/30';
    if (event.to_execution_status === 'in_progress') return 'indigo-500/30';
    if (event.to_execution_status === 'paused') return 'amber-500/30';
    if (event.to_execution_status === 'completed') return 'emerald-500/30';
    return 'white/10';
};

const getEventDescription = (event: StatusEvent): string => {
    const formatStatus = (s: string) => {
        return s
            .split('_')
            .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
            .join(' ');
    };

    let description = '';

    if (event.from_decision_status && event.to_decision_status) {
        description += `Decision: ${formatStatus(event.from_decision_status)} → ${formatStatus(event.to_decision_status)}`;
    } else if (event.to_decision_status) {
        description += `Decision: ${formatStatus(event.to_decision_status)}`;
    }

    if (event.from_execution_status && event.to_execution_status) {
        if (description) description += ' | ';
        description += `Execution: ${formatStatus(event.from_execution_status)} → ${formatStatus(event.to_execution_status)}`;
    } else if (event.to_execution_status) {
        if (description) description += ' | ';
        description += `Execution: ${formatStatus(event.to_execution_status)}`;
    }

    return description || 'Status changed';
};

defineExpose({ openTimeline });
</script>

<template>
    <div class="status-timeline">
        <StButtonGlass
            variant="ghost"
            icon="mdi-timeline-clock"
            @click="openTimeline"
            size="sm"
        >
            View History
        </StButtonGlass>

        <v-dialog v-model="showTimeline" max-width="850" scrollable>
            <StCardGlass
                variant="glass"
                class="overflow-hidden border-white/10 bg-[#0d1425]/98 !p-0 backdrop-blur-3xl"
                :no-hover="true"
            >
                <!-- Modal Header -->
                <div
                    class="relative overflow-hidden border-b border-white/5 px-10 py-8"
                >
                    <div
                        class="pointer-events-none absolute inset-x-0 -top-20 h-40 bg-indigo-500/10 blur-[60px]"
                    ></div>
                    <div
                        class="relative z-10 flex items-center justify-between"
                    >
                        <div class="flex items-center gap-5">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/10 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                            >
                                <v-icon color="indigo-400" size="24"
                                    >mdi-timeline-clock</v-icon
                                >
                            </div>
                            <div>
                                <h2
                                    class="mb-1 text-xl font-black tracking-tight text-white"
                                >
                                    Scenario Audit Trail
                                </h2>
                                <p
                                    class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                                >
                                    {{ events.length }} Status Events Recorded
                                </p>
                            </div>
                        </div>
                        <StButtonGlass
                            variant="ghost"
                            circle
                            size="sm"
                            icon="mdi-close"
                            @click="showTimeline = false"
                        />
                    </div>
                </div>

                <div
                    class="custom-scrollbar relative max-h-[70vh] overflow-y-auto px-10 py-10"
                >
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
                            class="mt-4 text-[10px] font-black tracking-[0.3em] text-white/30 uppercase"
                            >Retrieving Audit Logs...</span
                        >
                    </div>

                    <div
                        v-else-if="events.length === 0"
                        class="flex flex-col items-center justify-center py-20 text-center"
                    >
                        <v-icon size="48" color="white/10" class="mb-4"
                            >mdi-clipboard-text-clock-outline</v-icon
                        >
                        <h3 class="mb-1 text-lg font-black text-white/40">
                            No Status Changes
                        </h3>
                        <p class="text-xs text-white/20">
                            The scenario has not undergone any status
                            transitions.
                        </p>
                    </div>

                    <div v-else class="relative space-y-8">
                        <div
                            class="absolute top-4 bottom-4 left-[27px] w-px bg-gradient-to-b from-indigo-500/50 via-white/10 to-transparent"
                        ></div>

                        <div
                            v-for="(event, index) in sortedEvents"
                            :key="event.id"
                            class="group relative pl-20 transition-all duration-300 hover:-translate-y-1"
                        >
                            <!-- Timeline Dot -->
                            <div
                                class="absolute left-6 h-3 w-3 -translate-x-1/2 rounded-full border-2 border-white/10 bg-black/80 shadow-[0_0_10px_rgba(0,0,0,0.5)] transition-colors group-hover:border-indigo-400 group-hover:bg-indigo-500 group-hover:shadow-[0_0_15px_rgba(99,102,241,0.6)]"
                            ></div>

                            <!-- Event Card -->
                            <div
                                class="rounded-2xl border p-5 backdrop-blur-xl transition-colors"
                                :class="[
                                    `border-${getEventBorderColor(event).split('/')[0]}/30`,
                                    getEventBgColor(event),
                                    'hover:border-white/20 hover:bg-white/10',
                                ]"
                                :style="{
                                    borderColor: getEventBorderColor(
                                        event,
                                    ).includes('/')
                                        ? ''
                                        : getEventBorderColor(event),
                                }"
                            >
                                <div
                                    class="mb-4 flex flex-wrap items-start justify-between gap-4"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-black/40"
                                        >
                                            <v-icon
                                                :color="
                                                    getEventColor(event).split(
                                                        '/',
                                                    )[0]
                                                "
                                                size="20"
                                                >{{
                                                    getEventIcon(event)
                                                }}</v-icon
                                            >
                                        </div>
                                        <div>
                                            <h4
                                                class="text-sm font-black text-white"
                                            >
                                                {{ getEventDescription(event) }}
                                            </h4>
                                            <p
                                                class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                            >
                                                By {{ event.changed_by.name }}
                                            </p>
                                        </div>
                                    </div>
                                    <StBadgeGlass variant="glass" size="xs">
                                        {{ formatDate(event.created_at) }}
                                    </StBadgeGlass>
                                </div>

                                <div
                                    v-if="event.notes"
                                    class="rounded-xl border border-white/5 bg-black/40 p-4"
                                >
                                    <div class="mb-2 flex items-center gap-2">
                                        <v-icon size="14" color="white/20"
                                            >mdi-text-box-outline</v-icon
                                        >
                                        <span
                                            class="text-[9px] font-black tracking-widest text-white/40 uppercase"
                                            >Commit Notes</span
                                        >
                                    </div>
                                    <p
                                        class="text-xs leading-relaxed font-medium text-white/70 italic"
                                    >
                                        "{{ event.notes }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex justify-end border-t border-white/5 bg-[#020617]/60 px-10 py-6"
                >
                    <StButtonGlass variant="ghost" @click="showTimeline = false"
                        >Close</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>
    </div>
</template>

<style scoped>
.status-timeline {
    display: inline-block;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.2);
}
</style>
