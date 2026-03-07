<script setup lang="ts">
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';
import { Head, router } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

// Components
import ScenarioStepperComponent from '@/components/ScenarioPlanning/ScenarioStepperComponent.vue';
import SentinelHealthWidget from '@/components/SentinelHealthWidget.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';

// Step Components
import PrototypeMap from '@/components/ScenarioPlanning/Step1/PrototypeMap.vue';
import IncubatedCubeReview from '@/components/ScenarioPlanning/Step2/IncubatedCubeReview.vue';
import RoleCompetencyMatrix from '@/components/ScenarioPlanning/Step2/RoleCompetencyMatrix.vue';
import OrganizationalContrast from '@/components/ScenarioPlanning/Step3/OrganizationalContrast.vue';
import ClosingStrategies from '@/components/ScenarioPlanning/Step4/ClosingStrategies.vue';
import BudgetingPlan from '@/components/ScenarioPlanning/Step5/BudgetingPlan.vue';
import ScenarioComparison from '@/components/ScenarioPlanning/Step6/ScenarioComparison.vue';
import FinalDashboard from '@/components/ScenarioPlanning/Step7/FinalDashboard.vue';

// Modals
import IncubatedReviewModal from '@/components/IncubatedReviewModal.vue';
import NodeEditModal from '@/components/ScenarioPlanning/Modals/NodeEditModal.vue';
import StatusTimeline from '@/components/ScenarioPlanning/StatusTimeline.vue';
import VersionHistoryModal from '@/components/ScenarioPlanning/VersionHistoryModal.vue';

const props = defineProps<{
    scenarioId: number;
    initialStep?: number;
}>();

const api = useApi();
const { showSuccess, showError } = useNotification();

// State
const scenario = ref<any>(null);
const loading = ref(true);
const currentStep = ref(props.initialStep || 1);
const showIncubatedReview = ref(false);
const selectedIncubated = ref<any>(null);
const loadingTree = ref(false);
const incubatedTree = ref<any[]>([]);

const versionHistoryRef = ref<any>(null);
const statusTimelineRef = ref<any>(null);

const showNodeEditModal = ref(false);
const selectedNodeForEdit = ref<any>(null);

// Computed
const scenarioStatus = computed(() => scenario.value?.status || 'draft');

const statusConfig = computed<{
    color: 'primary' | 'secondary' | 'error' | 'warning' | 'success' | 'glass';
    icon: string;
}>(() => {
    switch (scenarioStatus.value) {
        case 'active':
            return { color: 'success', icon: 'mdi-check-circle' };
        case 'review':
            return { color: 'warning', icon: 'mdi-eye-outline' };
        case 'completed':
            return { color: 'primary', icon: 'mdi-star' };
        default:
            return { color: 'glass', icon: 'mdi-circle-edit-outline' };
    }
});

// Methods
const loadScenario = async () => {
    if (!props.scenarioId) {
        console.warn('[loadScenario] scenarioId is missing', props.scenarioId);
        loading.value = false;
        return;
    }
    loading.value = true;
    try {
        const res: any = await api.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}`,
        );
        scenario.value = res?.data ?? res;
        if (currentStep.value === 1) {
            await Promise.all([loadIncubatedTree(), loadCapabilityTree()]);
        }
    } catch {
        showError('Neural sync failed');
    } finally {
        loading.value = false;
    }
};

const loadCapabilityTree = async () => {
    if (!props.scenarioId) return;
    try {
        const res: any = await api.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/capability-tree`,
        );
        if (scenario.value) {
            scenario.value.capabilities = res?.data ?? res ?? [];
        }
    } catch (e) {
        console.error('[loadCapabilityTree] Error:', e);
    }
};

const loadIncubatedTree = async () => {
    if (!props.scenarioId) return;
    loadingTree.value = true;
    try {
        const res: any = await api.get(
            `/api/scenarios/${props.scenarioId}/step1/incubated-tree`,
        );
        incubatedTree.value = res?.data ?? res ?? [];
    } catch (e) {
        console.error(e);
    } finally {
        loadingTree.value = false;
    }
};

const handleEditNode = (node: any) => {
    if (node.type === 'scenario') return; // Cannot edit scenario node here
    selectedNodeForEdit.value = node;
    showNodeEditModal.value = true;
};

const nextStep = () => {
    if (currentStep.value < 7) currentStep.value++;
};
const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};

const goBack = () => router.visit('/strategic-planning');

const handleVersionSelected = (vId: number) => {
    router.visit(`/strategic-planning/${vId}`);
};

const handleStatusChanged = () => loadScenario();

const openIncubatedReview = (item: any) => {
    selectedIncubated.value = item;
    showIncubatedReview.value = true;
};

const onPromoted = () => {
    showIncubatedReview.value = false;
    loadScenario();
};

const promoteIncubated = async () => {
    try {
        await api.post(`/api/scenarios/${props.scenarioId}/step1/promote-all`);
        showSuccess('All incubated entities promoted to architecture');
        loadScenario();
    } catch {
        showError('Promotion failure');
    }
};

onMounted(loadScenario);

watch(
    () => currentStep.value,
    (val) => {
        if (val === 1) {
            loadIncubatedTree();
            loadCapabilityTree();
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
);
</script>

<template>
    <div
        class="scenario-detail-root min-h-screen overflow-x-hidden bg-[#020617] text-white selection:bg-indigo-500/30"
    >
        <Head :title="scenario?.name || 'Scenario Detail'" />

        <!-- Background Elements -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div
                class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-indigo-500/10 blur-[120px]"
            ></div>
            <div
                class="absolute top-[20%] -right-[10%] h-[35%] w-[35%] rounded-full bg-purple-500/10 blur-[120px]"
            ></div>
            <div
                class="absolute -bottom-[10%] left-[20%] h-[30%] w-[30%] rounded-full bg-blue-500/10 blur-[120px]"
            ></div>
            <!-- <div
                class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-overlay brightness-100 contrast-150"
            ></div> -->
        </div>

        <!-- App Bar / Header -->
        <header
            class="sticky top-0 z-[100] w-full border-b border-white/5 bg-[#020617]/40 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-20 max-w-[1600px] items-center justify-between px-6"
            >
                <!-- Left: Title & Status -->
                <div class="flex items-center gap-6">
                    <StButtonGlass
                        variant="ghost"
                        circle
                        icon="mdi-arrow-left"
                        @click="goBack"
                    />
                    <div class="flex flex-col">
                        <div class="flex items-center gap-3">
                            <h1 class="text-xl font-black tracking-tight">
                                {{
                                    scenario?.name || 'Loading Architecture...'
                                }}
                            </h1>
                            <StBadgeGlass
                                :variant="statusConfig.color"
                                size="sm"
                                class="flex items-center gap-1.5 px-3! text-[10px] tracking-widest uppercase"
                            >
                                <v-icon :icon="statusConfig.icon" size="12" />
                                {{ scenarioStatus }}
                            </StBadgeGlass>
                        </div>
                        <div class="mt-0.5 flex items-center gap-2">
                            <span
                                class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >Protocol Version</span
                            >
                            <span class="text-[10px] font-bold text-indigo-400"
                                >v{{ scenario?.version_number || '1.0' }}</span
                            >
                            <span class="mx-1 text-white/10">|</span>
                            <span
                                class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                >Last Sync</span
                            >
                            <span class="text-[10px] font-bold text-white/50">{{
                                scenario?.updated_at_human || 'Just now'
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-3">
                    <StButtonGlass
                        variant="ghost"
                        size="sm"
                        icon="mdi-history"
                        @click="versionHistoryRef?.open()"
                        >Timeline</StButtonGlass
                    >
                    <StButtonGlass
                        variant="ghost"
                        size="sm"
                        icon="mdi-list-status"
                        @click="statusTimelineRef?.open()"
                        >Lifecycle</StButtonGlass
                    >
                    <div class="mx-2 h-6 w-px bg-white/10"></div>
                    <StButtonGlass
                        variant="primary"
                        size="sm"
                        icon="mdi-shield-edit-outline"
                        >Commit Design</StButtonGlass
                    >
                </div>
            </div>
        </header>

        <!-- Stepper Section (full width, always on top) -->
        <div class="mx-auto max-w-[1600px] px-8 pt-8">
            <ScenarioStepperComponent v-model:current-step="currentStep" />
        </div>

        <!-- Main Layout: Sidebar Widgets + Content -->
        <div
            class="mx-auto flex max-w-[1600px] items-start gap-8 px-8 pt-6 pb-8"
        >
            <!-- Left Sidebar: Health Monitor & Metrics -->
            <aside class="sticky top-28 z-10 w-80 shrink-0">
                <!-- System Health Monitor -->
                <div>
                    <SentinelHealthWidget />
                </div>

                <!-- Tactical Stats Card -->
                <StCardGlass
                    variant="glass"
                    class="mt-8 border-white/5 bg-white/2 p-6!"
                >
                    <h4
                        class="mb-4 text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                    >
                        Neural Metrics
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-end justify-between">
                            <span class="text-xs font-medium text-white/50"
                                >Convergence</span
                            >
                            <span class="text-lg font-black text-indigo-400"
                                >94.2%</span
                            >
                        </div>
                        <div
                            class="h-1 w-full overflow-hidden rounded-full bg-white/5"
                        >
                            <div
                                class="h-full w-[94.2%] bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"
                            ></div>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div
                                class="rounded-xl border border-white/5 bg-white/5 p-3"
                            >
                                <div
                                    class="mb-1 text-[9px] font-black tracking-widest text-white/20 uppercase"
                                >
                                    Entities
                                </div>
                                <div class="text-lg font-black">
                                    {{ scenario?.entities_count || 24 }}
                                </div>
                            </div>
                            <div
                                class="rounded-xl border border-white/5 bg-white/5 p-3"
                            >
                                <div
                                    class="mb-1 text-[9px] font-black tracking-widest text-white/20 uppercase"
                                >
                                    Alerts
                                </div>
                                <div class="text-lg font-black text-rose-400">
                                    02
                                </div>
                            </div>
                        </div>
                    </div>
                </StCardGlass>
            </aside>

            <!-- Content Area -->
            <main class="min-w-0 flex-1 pb-32">
                <transition name="fade-slide" mode="out-in" appear>
                    <div
                        :key="currentStep"
                        class="animate-in duration-700 fade-in slide-in-from-bottom-4"
                    >
                        <template v-if="scenario">
                            <!-- Step 1 -->
                            <div v-if="currentStep === 1" class="space-y-8">
                                <PrototypeMap
                                    :scenario="scenario"
                                    @edit-node="handleEditNode"
                                />

                                <StCardGlass
                                    variant="glass"
                                    class="overflow-hidden border-white/10 bg-white/5 p-0!"
                                >
                                    <div
                                        class="flex items-center justify-between border-b border-white/5 bg-white/5 px-8 py-5"
                                    >
                                        <div class="flex items-center gap-3">
                                            <v-icon color="indigo-400" size="20"
                                                >mdi-seed-outline</v-icon
                                            >
                                            <h2
                                                class="text-lg text-sm font-black tracking-tight tracking-widest text-white uppercase"
                                            >
                                                Incubated Entities
                                            </h2>
                                        </div>
                                        <StButtonGlass
                                            variant="ghost"
                                            size="sm"
                                            @click="promoteIncubated"
                                            icon="mdi-rocket-launch"
                                            >Promote All</StButtonGlass
                                        >
                                    </div>
                                    <div class="p-8">
                                        <div
                                            v-if="loadingTree"
                                            class="flex justify-center py-12"
                                        >
                                            <v-progress-circular
                                                indeterminate
                                                color="indigo-400"
                                            />
                                        </div>
                                        <div
                                            v-else-if="!incubatedTree.length"
                                            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 bg-white/2 py-12"
                                        >
                                            <v-icon
                                                size="32"
                                                color="white/10"
                                                class="mb-4"
                                                >mdi-cube-off-outline</v-icon
                                            >
                                            <p
                                                class="text-sm font-medium text-white/30"
                                            >
                                                Architecture foundation is
                                                stable. No new elements pending.
                                            </p>
                                        </div>
                                        <div
                                            v-else
                                            class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                        >
                                            <div
                                                v-for="cap in incubatedTree"
                                                :key="cap.id"
                                                class="group rounded-2xl border border-white/5 bg-white/5 p-5 transition-all hover:border-indigo-500/30"
                                            >
                                                <div
                                                    class="mb-4 flex items-center justify-between"
                                                >
                                                    <div>
                                                        <h4
                                                            class="text-base font-black text-white transition-colors group-hover:text-indigo-300"
                                                        >
                                                            {{ cap.name }}
                                                        </h4>
                                                        <p
                                                            class="line-clamp-1 truncate text-[11px] text-white/40"
                                                        >
                                                            {{
                                                                cap.description
                                                            }}
                                                        </p>
                                                    </div>
                                                    <StButtonGlass
                                                        size="sm"
                                                        variant="glass"
                                                        circle
                                                        icon="mdi-eye"
                                                        @click="
                                                            openIncubatedReview(
                                                                cap,
                                                            )
                                                        "
                                                    />
                                                </div>
                                                <div
                                                    class="flex flex-wrap gap-2"
                                                >
                                                    <StBadgeGlass
                                                        v-for="c in cap.competencies"
                                                        :key="c.id"
                                                        variant="secondary"
                                                        size="sm"
                                                        >{{
                                                            c.name
                                                        }}</StBadgeGlass
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </StCardGlass>
                            </div>

                            <!-- Step 2 -->
                            <div v-if="currentStep === 2" class="space-y-12">
                                <RoleCompetencyMatrix
                                    :scenario-id="scenarioId"
                                />
                                <div class="border-t border-white/5 pt-8">
                                    <IncubatedCubeReview
                                        :scenario-id="scenarioId"
                                        @approved="loadScenario"
                                    />
                                </div>
                            </div>

                            <!-- Step 3-7 components remain the same as they were already refactored -->
                            <OrganizationalContrast
                                v-if="currentStep === 3"
                                :scenario-id="scenarioId"
                            />
                            <ClosingStrategies
                                v-if="currentStep === 4"
                                :scenario-id="scenarioId"
                            />
                            <BudgetingPlan
                                v-if="currentStep === 5"
                                :scenario-id="scenarioId"
                            />
                            <ScenarioComparison
                                v-if="currentStep === 6"
                                :scenario-id="scenarioId"
                            />
                            <FinalDashboard
                                v-if="currentStep === 7"
                                :scenario-id="scenarioId"
                            />
                        </template>
                    </div>
                </transition>
            </main>
        </div>

        <!-- Sticky Footer Navigation -->
        <footer
            class="fixed right-0 bottom-0 left-0 z-[100] border-t border-white/5 bg-[#020617]/60 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-20 max-w-[1600px] items-center justify-between px-8"
            >
                <div>
                    <StButtonGlass
                        v-if="currentStep > 1"
                        variant="ghost"
                        icon="mdi-chevron-left"
                        @click="prevStep"
                        >Back Stage</StButtonGlass
                    >
                </div>

                <div class="flex items-center gap-1">
                    <div
                        v-for="i in 7"
                        :key="i"
                        class="h-1.5 rounded-full transition-all duration-500"
                        :class="[
                            i === currentStep
                                ? 'w-8 bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.6)]'
                                : i < currentStep
                                  ? 'w-4 bg-emerald-500/50'
                                  : 'w-2 bg-white/10',
                        ]"
                    ></div>
                </div>

                <div class="flex items-center gap-4">
                    <StButtonGlass
                        v-if="currentStep < 7"
                        variant="primary"
                        icon="mdi-chevron-right"
                        @click="nextStep"
                        class="px-12!"
                        >Next Protocol</StButtonGlass
                    >
                    <StButtonGlass
                        v-else
                        variant="secondary"
                        icon="mdi-check-all"
                        @click="goBack"
                        class="px-12!"
                        >Finalize Design</StButtonGlass
                    >
                </div>
            </div>
        </footer>

        <!-- Modals -->
        <VersionHistoryModal
            ref="versionHistoryRef"
            :scenario-id="scenarioId"
            :version-group-id="scenario?.version_group_id"
            :current-version="scenario?.version_number"
            @version-selected="handleVersionSelected"
        />
        <StatusTimeline
            ref="statusTimelineRef"
            :scenario-id="scenarioId"
            @status-changed="handleStatusChanged"
        />
        <IncubatedReviewModal
            v-model="showIncubatedReview"
            :item="selectedIncubated"
            :scenario-id="scenarioId"
            @close="showIncubatedReview = false"
            @promoted="onPromoted"
        />

        <NodeEditModal
            v-if="showNodeEditModal"
            v-model="showNodeEditModal"
            :node="selectedNodeForEdit"
            :scenario-id="scenarioId"
            @saved="loadScenario"
        />
    </div>
</template>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(30px) scale(0.98);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-30px) scale(1.02);
}

.scenario-detail-root :deep(.prototype-map-root) {
    height: 70vh;
    min-height: 500px;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.05);
}
</style>
