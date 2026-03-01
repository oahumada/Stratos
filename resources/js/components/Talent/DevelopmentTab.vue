<template>
    <div class="development-tab">
        <div v-if="loading" class="flex items-center justify-center p-8">
            <div
                class="h-8 w-8 animate-spin rounded-full border-2 border-indigo-500/20 border-t-indigo-500"
            />
        </div>

        <div
            v-else-if="paths.length === 0"
            class="flex flex-col items-center justify-center rounded-3xl border border-white/5 bg-white/5 py-12 text-center"
        >
            <div
                class="mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-white/10 text-white/20"
            >
                <PhRoadHorizon :size="32" />
            </div>
            <div class="mb-6 max-w-sm text-sm font-medium text-white/40">
                {{ t('talent_development.empty_state') }}
            </div>
            <StButtonGlass
                variant="primary"
                :icon="PhRoadHorizon"
                @click="openCreateDialog"
            >
                {{ t('talent_development.generate_btn') }}
            </StButtonGlass>
        </div>

        <div v-else class="space-y-4">
            <div
                v-for="path in paths"
                :key="path.id"
                class="overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md transition-all duration-300"
                :class="{
                    'bg-white/10 ring-1 ring-indigo-500/30':
                        activePathId === path.id,
                }"
            >
                <div
                    class="flex cursor-pointer items-center justify-between p-4"
                    @click="togglePath(path.id)"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white/40"
                            :class="{
                                'border-indigo-500/30 bg-indigo-500/10 text-indigo-400':
                                    activePathId === path.id,
                            }"
                        >
                            <PhRoadHorizon :size="20" />
                        </div>
                        <div>
                            <div
                                class="text-sm font-bold tracking-tight text-white"
                            >
                                {{ path.action_title }}
                            </div>
                            <div
                                class="text-[10px] font-bold tracking-widest text-white/30 uppercase"
                            >
                                {{ path.estimated_duration_months }}
                                {{ t('talent_development.months') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <StBadgeGlass :variant="getStatusVariant(path.status)">
                            {{ path.status }}
                        </StBadgeGlass>
                        <div
                            class="transition-transform duration-300"
                            :class="{ 'rotate-180': activePathId === path.id }"
                        >
                            <PhPlay
                                :size="12"
                                class="rotate-90 text-white/20"
                            />
                        </div>
                    </div>
                </div>

                <div
                    v-if="activePathId === path.id"
                    class="animate-in border-t border-white/10 p-6 duration-300 fade-in slide-in-from-top-2"
                >
                    <h4
                        class="mb-6 text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                    >
                        {{ t('talent_development.actions_title') }} (70-20-10)
                    </h4>

                    <div
                        class="relative space-y-8 pl-6 before:absolute before:top-2 before:bottom-2 before:left-[11px] before:w-px before:bg-white/10"
                    >
                        <div
                            v-for="action in path.actions"
                            :key="action.id"
                            class="relative"
                        >
                            <div
                                class="absolute top-1.5 -left-[23px] h-3 w-3 rounded-full border-2 border-slate-950 shadow-[0_0_0_2px_rgba(255,255,255,0.05)]"
                                :class="getActionColorClass(action.type)"
                            />

                            <div
                                class="group relative rounded-2xl border border-white/5 bg-white/5 p-4 transition-all hover:border-white/10"
                            >
                                <div
                                    class="flex items-start justify-between gap-4"
                                >
                                    <div class="flex-1">
                                        <div
                                            class="mb-1 flex items-center gap-2"
                                        >
                                            <span
                                                class="text-sm font-bold tracking-tight text-white"
                                            >
                                                {{ action.title }}
                                            </span>
                                            <StBadgeGlass
                                                variant="glass"
                                                size="sm"
                                                class="text-[8px] opacity-50"
                                            >
                                                {{ action.strategy }}
                                            </StBadgeGlass>
                                        </div>
                                        <p
                                            class="mb-4 text-xs leading-relaxed text-white/50"
                                        >
                                            {{ action.description }}
                                        </p>

                                        <!-- Mentor Section -->
                                        <div v-if="action.mentor" class="mb-4">
                                            <MentorCard
                                                :mentor="
                                                    formatMentor(action.mentor)
                                                "
                                                @request="
                                                    openSessionDialog(action)
                                                "
                                            />
                                            <div class="flex justify-end pr-2">
                                                <StButtonGlass
                                                    variant="ghost"
                                                    size="sm"
                                                    :icon="PhArrowsClockwise"
                                                    @click="
                                                        openSessionDialog(
                                                            action,
                                                        )
                                                    "
                                                >
                                                    {{
                                                        t(
                                                            'talent_development.session_log',
                                                        )
                                                    }}
                                                </StButtonGlass>
                                            </div>
                                        </div>

                                        <div
                                            v-else-if="
                                                action.type === 'mentoring' ||
                                                action.type === 'mentorship'
                                            "
                                            class="mb-4 flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 p-4"
                                        >
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-400"
                                            >
                                                <PhUsers :size="20" />
                                            </div>
                                            <div>
                                                <div
                                                    class="text-xs font-bold text-white"
                                                >
                                                    {{
                                                        t(
                                                            'talent_development.searching_mentor',
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                                >
                                                    {{
                                                        t(
                                                            'talent_development.pending_assignment',
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Footer -->
                                        <div
                                            class="flex flex-wrap items-center gap-4 border-t border-white/5 pt-4"
                                        >
                                            <div
                                                class="flex items-center gap-1.5 text-xs font-medium text-white/30"
                                            >
                                                <PhClock :size="14" />
                                                {{ action.estimated_hours }}h
                                            </div>

                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <div class="relative">
                                                    <select
                                                        class="cursor-pointer appearance-none rounded-lg border border-white/10 bg-white/5 py-1 pr-8 pl-2 text-[10px] font-black tracking-widest text-white/60 uppercase transition-all hover:bg-white/10 focus:ring-1 focus:ring-indigo-500/50 focus:outline-none"
                                                        :value="action.status"
                                                        @change="
                                                            updateActionStatus(
                                                                action.id,
                                                                $event.target
                                                                    .value,
                                                            )
                                                        "
                                                        :disabled="
                                                            updatingActionId ===
                                                            action.id
                                                        "
                                                    >
                                                        <option value="pending">
                                                            {{
                                                                t(
                                                                    'talent_development.status.pending',
                                                                )
                                                            }}
                                                        </option>
                                                        <option
                                                            value="in_progress"
                                                        >
                                                            {{
                                                                t(
                                                                    'talent_development.status.in_progress',
                                                                )
                                                            }}
                                                        </option>
                                                        <option
                                                            value="completed"
                                                        >
                                                            {{
                                                                t(
                                                                    'talent_development.status.completed',
                                                                )
                                                            }}
                                                        </option>
                                                        <option
                                                            value="cancelled"
                                                        >
                                                            {{
                                                                t(
                                                                    'talent_development.status.cancelled',
                                                                )
                                                            }}
                                                        </option>
                                                    </select>
                                                    <div
                                                        class="pointer-events-none absolute top-1.5 right-2 text-white/40"
                                                    >
                                                        <div
                                                            v-if="
                                                                updatingActionId ===
                                                                action.id
                                                            "
                                                            class="h-3 w-3 animate-spin rounded-full border border-white/20 border-t-white"
                                                        />
                                                        <PhPlay
                                                            v-else
                                                            :size="8"
                                                            class="rotate-90"
                                                        />
                                                    </div>
                                                </div>
                                                <StBadgeGlass
                                                    variant="glass"
                                                    size="sm"
                                                    class="text-[8px] opacity-40"
                                                >
                                                    {{ action.type }}
                                                </StBadgeGlass>
                                            </div>

                                            <div
                                                class="ml-auto flex items-center gap-2"
                                            >
                                                <StButtonGlass
                                                    variant="ghost"
                                                    size="sm"
                                                    :icon="PhPaperclip"
                                                    circle
                                                    @click="
                                                        openEvidenceDialog(
                                                            action,
                                                        )
                                                    "
                                                    :title="
                                                        t(
                                                            'talent_development.manage_evidence',
                                                        )
                                                    "
                                                />

                                                <template
                                                    v-if="action.lms_course_id"
                                                >
                                                    <StButtonGlass
                                                        variant="primary"
                                                        size="sm"
                                                        :icon="PhPlayCircle"
                                                        @click="
                                                            launchLms(action.id)
                                                        "
                                                        :title="
                                                            t(
                                                                'talent_development.launch_lms',
                                                            )
                                                        "
                                                    >
                                                        {{
                                                            t(
                                                                'talent_development.launch_btn',
                                                            )
                                                        }}
                                                    </StButtonGlass>
                                                    <StButtonGlass
                                                        variant="ghost"
                                                        size="sm"
                                                        :icon="
                                                            PhArrowsClockwise
                                                        "
                                                        circle
                                                        @click="
                                                            syncLmsProgress(
                                                                action.id,
                                                            )
                                                        "
                                                        :loading="
                                                            syncingLmsId ===
                                                            action.id
                                                        "
                                                        :title="
                                                            t(
                                                                'talent_development.sync_lms',
                                                            )
                                                        "
                                                    />
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <CreatePathDialog
        ref="createDialog"
        :person-id="personId"
        :skills="skills"
        @generated="fetchPaths"
    />

    <MentorshipSessionDialog
        ref="sessionDialog"
        :action-id="selectedAction?.id"
        :action-title="selectedAction?.title"
    />

    <EvidenceDialog ref="evidenceDialog" :action-id="selectedAction?.id" />
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import {
    PhArrowsClockwise,
    PhClock,
    PhPaperclip,
    PhPlay,
    PhPlayCircle,
    PhRoadHorizon,
    PhUsers,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import CreatePathDialog from './CreatePathDialog.vue';
import EvidenceDialog from './EvidenceDialog.vue';
import MentorCard from './MentorCard.vue';
import MentorshipSessionDialog from './MentorshipSessionDialog.vue';

const { t } = useI18n();

const props = defineProps({
    personId: {
        type: Number,
        required: true,
    },
    skills: {
        type: Array,
        default: () => [],
    },
});

const paths = ref([]);
const loading = ref(false);
const updatingActionId = ref<number | null>(null);
const syncingLmsId = ref<number | null>(null);
const activePathId = ref<number | null>(null);
const createDialog = ref<InstanceType<typeof CreatePathDialog> | null>(null);
const sessionDialog = ref<InstanceType<typeof MentorshipSessionDialog> | null>(
    null,
);
const evidenceDialog = ref<InstanceType<typeof EvidenceDialog> | null>(null);
const selectedAction = ref<any>(null);

const togglePath = (id: number) => {
    activePathId.value = activePathId.value === id ? null : id;
};

const fetchPaths = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/development-paths', {
            params: { people_id: props.personId },
        });
        paths.value = response.data.data;
        if (paths.value.length > 0 && !activePathId.value) {
            activePathId.value = paths.value[0].id;
        }
    } catch (e) {
        console.error('Error fetching development paths', e);
    } finally {
        loading.value = false;
    }
};

const updateActionStatus = async (actionId, newStatus) => {
    updatingActionId.value = actionId;
    try {
        await axios.patch(`/api/development-actions/${actionId}/status`, {
            status: newStatus,
        });
        await fetchPaths();
    } catch (e) {
        console.error('Error updating action status', e);
    } finally {
        updatingActionId.value = null;
    }
};

const openCreateDialog = () => {
    createDialog.value?.open();
};

const formatMentor = (mentorData: any) => {
    return {
        id: mentorData.id,
        full_name:
            mentorData.full_name ||
            `${mentorData.first_name} ${mentorData.last_name}`,
        role: mentorData.role?.name || 'Experto',
        match_score: 95, // Mock score
        expertise_level: 5,
        avatar: mentorData.photo_url || null,
    };
};

const openSessionDialog = (action: any) => {
    selectedAction.value = action;
    setTimeout(() => {
        sessionDialog.value?.open();
    }, 50);
};

const openEvidenceDialog = (action: any) => {
    selectedAction.value = action;
    setTimeout(() => {
        evidenceDialog.value?.open();
    }, 50);
};

const launchLms = async (actionId: number) => {
    try {
        const response = await axios.post(
            `/api/development-actions/${actionId}/launch-lms`,
        );
        if (response.data.url) {
            window.open(response.data.url, '_blank');
        }
    } catch (e) {
        console.error('Error launching LMS', e);
    }
};

const syncLmsProgress = async (actionId: number) => {
    syncingLmsId.value = actionId;
    try {
        await axios.post(`/api/development-actions/${actionId}/sync-lms`);
        await fetchPaths();
    } catch (e) {
        console.error('Error syncing LMS progress', e);
    } finally {
        syncingLmsId.value = null;
    }
};

onMounted(() => {
    if (props.personId) {
        fetchPaths();
    }
});

watch(
    () => props.personId,
    (newId) => {
        if (newId) fetchPaths();
    },
);

const getStatusVariant = (status) => {
    switch (status) {
        case 'active':
            return 'success';
        case 'draft':
            return 'glass';
        case 'completed':
            return 'primary';
        default:
            return 'glass';
    }
};

const getActionColorClass = (type) => {
    switch (type) {
        case 'training':
            return 'bg-blue-400';
        case 'mentoring':
            return 'bg-emerald-400';
        case 'project':
            return 'bg-amber-400';
        default:
            return 'bg-slate-400';
    }
};
</script>

<style scoped>
.development-tab {
    min-height: 200px;
}
</style>
