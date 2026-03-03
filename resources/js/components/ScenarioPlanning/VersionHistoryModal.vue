<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { computed, ref } from 'vue';

interface Props {
    scenarioId: number;
    versionGroupId: string;
    currentVersion: number;
}

interface Version {
    id: number;
    version_number: number;
    name: string;
    description?: string;
    decision_status: string;
    execution_status: string;
    is_current_version: boolean;
    created_at: string;
    owner?: {
        name: string;
    };
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'versionSelected', versionId: number): void;
}>();

const api = useApi();
const { showSuccess, showError } = useNotification();

const showDialog = ref(false);
const loading = ref(false);
const versions = ref<Version[]>([]);
const selectedVersions = ref<number[]>([]);
const showComparison = ref(false);

const openDialog = () => {
    showDialog.value = true;
    loadVersions();
};

const loadVersions = async () => {
    loading.value = true;
    try {
        const res = await api.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/versions`,
        );
        versions.value = res.data?.versions || [];
    } catch (e) {
        void e;
        showError('Failed to load versions');
    } finally {
        loading.value = false;
    }
};

const sortedVersions = computed(() => {
    return [...versions.value].sort(
        (a, b) => b.version_number - a.version_number,
    );
});

const getVersionBadgeColor = (status: string): string => {
    const map: Record<string, string> = {
        draft: 'glass',
        pending_approval: 'secondary',
        approved: 'primary',
        rejected: 'secondary',
    };
    return map[status] || 'glass';
};

const getExecutionBadgeColor = (status: string): string => {
    const map: Record<string, string> = {
        planned: 'glass',
        in_progress: 'primary',
        paused: 'secondary',
        completed: 'success',
    };
    return map[status] || 'glass';
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const selectVersion = (versionId: number) => {
    emit('versionSelected', versionId);
    showDialog.value = false;
};

const toggleCompare = (versionId: number) => {
    const index = selectedVersions.value.indexOf(versionId);
    if (index > -1) {
        selectedVersions.value.splice(index, 1);
    } else {
        if (selectedVersions.value.length < 2) {
            selectedVersions.value.push(versionId);
        }
    }
};

const compareVersions = () => {
    if (selectedVersions.value.length === 2) {
        showComparison.value = true;
        showSuccess('Initializing Neural Benchmark...');
    }
};

const canCompare = computed(() => selectedVersions.value.length === 2);

defineExpose({ openDialog });
</script>

<template>
    <v-dialog v-model="showDialog" max-width="900" scrollable>
        <StCardGlass
            variant="glass"
            class="overflow-hidden border-white/10 bg-[#0d1425]/98 p-0! backdrop-blur-3xl"
            :no-hover="true"
        >
            <!-- Modal Header -->
            <div
                class="relative overflow-hidden border-b border-white/5 px-10 py-8"
            >
                <div
                    class="pointer-events-none absolute inset-x-0 -top-20 h-40 bg-indigo-500/10 blur-[60px]"
                ></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div class="flex items-center gap-5">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/10 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                        >
                            <v-icon color="indigo-400" size="24"
                                >mdi-history</v-icon
                            >
                        </div>
                        <div>
                            <h2
                                class="mb-1 text-xl font-black tracking-tight text-white"
                            >
                                Architecture
                                <span class="text-indigo-400">History</span>
                            </h2>
                            <p
                                class="text-[10px] font-black tracking-widest text-white/40 uppercase"
                            >
                                {{ versions.length }} Neural Versions Available
                            </p>
                        </div>
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        circle
                        size="sm"
                        icon="mdi-close"
                        @click="showDialog = false"
                    />
                </div>
            </div>

            <!-- Content -->
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
                        >Synchronizing Repository...</span
                    >
                </div>

                <div
                    v-else-if="versions.length === 0"
                    class="flex flex-col items-center justify-center py-20 text-center"
                >
                    <v-icon size="48" color="white/10" class="mb-4"
                        >mdi-source-branch</v-icon
                    >
                    <h3 class="mb-1 text-lg font-black text-white/40">
                        No Alternative Versions
                    </h3>
                    <p class="text-xs text-white/20">
                        This is the genesis architecture of this scenario.
                    </p>
                </div>

                <div v-else class="space-y-6">
                    <!-- Comparison Notification -->
                    <div
                        v-if="selectedVersions.length > 0"
                        class="flex items-center justify-between rounded-xl border border-indigo-500/30 bg-indigo-500/10 px-6 py-4 backdrop-blur-md"
                    >
                        <div class="flex items-center gap-3">
                            <v-icon color="indigo-400" size="20"
                                >mdi-compare-horizontal</v-icon
                            >
                            <span class="text-sm font-bold text-white">
                                {{ selectedVersions.length }} architectures
                                selected for benchmark
                            </span>
                        </div>
                        <StButtonGlass
                            v-if="canCompare"
                            variant="primary"
                            size="sm"
                            icon="mdi-brain"
                            @click="compareVersions"
                        >
                            Execute Benchmark
                        </StButtonGlass>
                    </div>

                    <!-- Timeline -->
                    <div class="relative pl-8">
                        <div
                            class="absolute top-4 bottom-4 left-3 w-px bg-gradient-to-b from-indigo-500/50 via-white/10 to-transparent"
                        ></div>

                        <div
                            v-for="version in sortedVersions"
                            :key="version.id"
                            class="group relative mb-8 transition-all duration-300 hover:-translate-y-1"
                        >
                            <!-- Dot -->
                            <div
                                class="absolute -left-[27px] z-10 mt-6 h-3 w-3 rounded-full border-2 transition-colors"
                                :class="
                                    version.is_current_version
                                        ? 'border-primary bg-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.6)]'
                                        : 'border-white/20 bg-black/80 group-hover:border-indigo-400 group-hover:shadow-[0_0_15px_rgba(99,102,241,0.6)]'
                                "
                            ></div>

                            <!-- Card -->
                            <div
                                class="rounded-2xl border p-6 backdrop-blur-xl transition-colors"
                                :class="
                                    version.is_current_version
                                        ? 'border-indigo-500/50 bg-indigo-500/5'
                                        : 'border-white/5 bg-white/[0.02] hover:border-white/20 hover:bg-white/[0.05]'
                                "
                            >
                                <div class="flex flex-col gap-4">
                                    <div
                                        class="flex items-start justify-between"
                                    >
                                        <div class="mr-4 w-full">
                                            <div
                                                class="mb-1 flex items-center gap-3"
                                            >
                                                <StBadgeGlass
                                                    :variant="
                                                        version.is_current_version
                                                            ? 'primary'
                                                            : 'glass'
                                                    "
                                                    size="sm"
                                                >
                                                    v{{
                                                        version.version_number
                                                    }}
                                                </StBadgeGlass>
                                                <h3
                                                    class="text-lg font-black tracking-tight text-white"
                                                >
                                                    {{ version.name }}
                                                </h3>
                                                <StBadgeGlass
                                                    v-if="
                                                        version.is_current_version
                                                    "
                                                    variant="primary"
                                                    size="xs"
                                                    class="ml-2 shadow-[0_0_10px_rgba(99,102,241,0.4)]"
                                                >
                                                    Active
                                                </StBadgeGlass>
                                            </div>
                                            <p
                                                class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                            >
                                                Committed:
                                                {{
                                                    formatDate(
                                                        version.created_at,
                                                    )
                                                }}
                                                <span v-if="version.owner">
                                                    | Architect:
                                                    {{
                                                        version.owner.name
                                                    }}</span
                                                >
                                            </p>
                                        </div>

                                        <div class="flex h-full items-center">
                                            <v-checkbox
                                                hide-details
                                                density="compact"
                                                :model-value="
                                                    selectedVersions.includes(
                                                        version.id,
                                                    )
                                                "
                                                :disabled="
                                                    !canCompare &&
                                                    !selectedVersions.includes(
                                                        version.id,
                                                    )
                                                "
                                                @update:model-value="
                                                    toggleCompare(version.id)
                                                "
                                            ></v-checkbox>
                                        </div>
                                    </div>

                                    <div
                                        v-if="version.description"
                                        class="rounded-xl border border-white/5 bg-black/40 p-4"
                                    >
                                        <p
                                            class="text-xs leading-relaxed font-medium text-white/60 italic"
                                        >
                                            "{{ version.description }}"
                                        </p>
                                    </div>

                                    <div
                                        class="mt-2 flex flex-wrap items-center justify-between gap-4 border-t border-white/5 pt-4"
                                    >
                                        <div class="flex gap-2">
                                            <StBadgeGlass
                                                :variant="
                                                    getVersionBadgeColor(
                                                        version.decision_status,
                                                    )
                                                "
                                                size="xs"
                                            >
                                                {{
                                                    (
                                                        version.decision_status ||
                                                        'draft'
                                                    ).toUpperCase()
                                                }}
                                            </StBadgeGlass>
                                            <StBadgeGlass
                                                :variant="
                                                    getExecutionBadgeColor(
                                                        version.execution_status,
                                                    )
                                                "
                                                size="xs"
                                            >
                                                {{
                                                    (
                                                        version.execution_status ||
                                                        'planned'
                                                    ).toUpperCase()
                                                }}
                                            </StBadgeGlass>
                                        </div>

                                        <StButtonGlass
                                            v-if="version.id !== scenarioId"
                                            variant="ghost"
                                            size="sm"
                                            icon="mdi-arrow-right"
                                            @click="selectVersion(version.id)"
                                        >
                                            Switch to Version
                                        </StButtonGlass>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="flex justify-end border-t border-white/5 bg-[#020617]/60 px-10 py-6"
                >
                    <StButtonGlass variant="ghost" @click="showDialog = false"
                        >Close</StButtonGlass
                    >
                </div>
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<style scoped>
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
