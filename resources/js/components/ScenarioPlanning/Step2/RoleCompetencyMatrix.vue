<template>
    <div class="role-competency-matrix space-y-8 pb-32">
        <!-- Dashboard Header: The Strategic Bridge -->
        <div
            class="matrix-header animate-in duration-700 fade-in slide-in-from-top-4"
        >
            <div
                class="flex flex-col justify-between gap-8 md:flex-row md:items-end"
            >
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                        >
                            <v-icon color="indigo-300" size="28"
                                >mdi-molecule</v-icon
                            >
                        </div>
                        <div>
                            <h2
                                class="text-3xl leading-none font-black tracking-tight text-white"
                            >
                                {{ store.scenarioName || 'Scenario Design' }}
                            </h2>
                            <div class="mt-2 flex items-center gap-3">
                                <StBadgeGlass
                                    variant="primary"
                                    size="sm"
                                    class="!px-3"
                                >
                                    Engineering Phase
                                </StBadgeGlass>
                                <span
                                    class="text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                >
                                    Horizon:
                                    <span class="text-indigo-400"
                                        >{{ store.horizonMonths }} Months</span
                                    >
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <StButtonGlass
                        variant="ghost"
                        icon="mdi-cube-scan"
                        @click="showCubeWizard = true"
                    >
                        AI Cube Architect
                    </StButtonGlass>
                    <StButtonGlass
                        variant="glass"
                        icon="mdi-robot-vacuum-variant"
                        :loading="isDesigning"
                        @click="handleDesignTalent"
                        class="!text-indigo-300"
                    >
                        Agent Consultation
                    </StButtonGlass>
                    <div class="mx-2 h-8 w-px bg-white/10"></div>
                    <StButtonGlass
                        variant="secondary"
                        icon="mdi-plus-thick"
                        @click="showAddRoleDialog = true"
                    >
                        New Role
                    </StButtonGlass>
                    <StButtonGlass
                        variant="primary"
                        icon="mdi-check-decagram"
                        @click="showFinalizeDialog = true"
                    >
                        Commit Design
                    </StButtonGlass>
                </div>
            </div>
        </div>

        <!-- Global Alerts & Feedback -->
        <v-expand-transition>
            <div v-if="store.error" class="mb-6">
                <StCardGlass
                    variant="glass"
                    border-accent="red"
                    class="!bg-red-500/5"
                >
                    <div class="flex items-center gap-4 text-red-200">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-500/20 text-red-400"
                        >
                            <v-icon size="20">mdi-alert-circle</v-icon>
                        </div>
                        <p class="flex-grow text-sm font-medium">
                            {{ store.error }}
                        </p>
                        <StButtonGlass
                            variant="ghost"
                            circle
                            size="sm"
                            icon="mdi-close"
                            @click="store.clearMessages()"
                        />
                    </div>
                </StCardGlass>
            </div>
        </v-expand-transition>

        <v-snackbar
            v-model="showSuccess"
            color="transparent"
            elevation="0"
            timeout="3000"
            location="top right"
        >
            <StCardGlass
                variant="glass"
                border-accent="emerald"
                class="min-w-[300px] !bg-emerald-500/10 shadow-[0_20px_50px_rgba(16,185,129,0.2)] backdrop-blur-3xl"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400"
                    >
                        <v-icon size="20">mdi-check-decagram</v-icon>
                    </div>
                    <div>
                        <div
                            class="mb-0.5 text-[10px] font-black tracking-widest text-emerald-400/60 uppercase"
                        >
                            Operation Successful
                        </div>
                        <div class="text-sm font-bold text-white">
                            {{ store.success || 'Changes saved in the cloud.' }}
                        </div>
                    </div>
                </div>
            </StCardGlass>
        </v-snackbar>

        <!-- Loading State: Neural Analysis -->
        <div
            v-if="store.loading"
            class="flex flex-col items-center justify-center gap-8 py-32"
        >
            <div class="relative">
                <div
                    class="absolute inset-0 animate-ping rounded-full bg-indigo-500/20"
                ></div>
                <v-progress-circular
                    indeterminate
                    color="indigo-400"
                    size="84"
                    width="4"
                    class="relative"
                ></v-progress-circular>
                <div class="absolute inset-0 flex items-center justify-center">
                    <v-icon color="indigo-400" size="32"
                        >mdi-chart-scatter-plot</v-icon
                    >
                </div>
            </div>
            <div class="text-center">
                <h3 class="mb-1 text-lg font-black tracking-tight text-white">
                    Mapping DNA Structures
                </h3>
                <span
                    class="animate-pulse text-[10px] font-black tracking-[0.4em] text-white/30 uppercase"
                >
                    Analyzing Role-Competency Intersection
                </span>
            </div>
        </div>

        <div
            v-else
            class="animate-in duration-1000 fade-in slide-in-from-bottom-6"
        >
            <!-- Design Logic Guide -->
            <StCardGlass
                variant="glass"
                class="mb-10 overflow-hidden !p-2"
                :no-hover="true"
            >
                <div
                    class="relative flex flex-col items-start gap-8 p-6 md:flex-row md:items-center"
                >
                    <div
                        class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10"
                    >
                        <v-icon
                            icon="mdi-head-cog-outline"
                            color="indigo-300"
                            size="32"
                        />
                    </div>
                    <div class="flex-grow">
                        <h3
                            class="mb-2 text-sm font-black tracking-[0.2em] text-white/40 uppercase"
                        >
                            Engineering Principles
                        </h3>
                        <p
                            class="max-w-4xl text-sm leading-relaxed font-medium text-white/70"
                        >
                            Establish the structural links between
                            <span class="font-bold text-indigo-400"
                                >Strategic Assets</span
                            >
                            (Competencies) and
                            <span class="font-bold text-indigo-400"
                                >Execution Nodes</span
                            >
                            (Roles). Define evolution vectors:
                            <span class="font-bold text-white italic"
                                >Stabilization, Upskilling, or
                                Transformation</span
                            >.
                        </p>
                    </div>
                    <div
                        class="flex hidden shrink-0 items-center gap-4 border-l border-white/5 pl-8 lg:flex"
                    >
                        <div class="text-center">
                            <div
                                class="mb-1 text-2xl leading-none font-black text-white"
                            >
                                {{ totalMappings }}
                            </div>
                            <div
                                class="text-[10px] font-black tracking-widest text-white/20 uppercase"
                            >
                                Active Links
                            </div>
                        </div>
                    </div>
                </div>
            </StCardGlass>

            <!-- Main Engineering Matrix Container -->
            <StCardGlass
                variant="glass"
                class="overflow-hidden border-white/5 !p-0 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)]"
                :no-hover="true"
            >
                <!-- Category Navigation (Z-Axis) -->
                <nav
                    class="no-scrollbar flex items-center gap-1 overflow-x-auto border-b border-white/5 bg-white/2 p-3"
                >
                    <button
                        v-for="(cat, idx) in categories"
                        :key="cat.name"
                        @click="activeTab = idx"
                        :class="[
                            'group relative flex items-center gap-4 rounded-xl px-6 py-3.5 transition-all duration-500',
                            activeTab === idx
                                ? 'bg-indigo-500/10 text-white'
                                : 'text-white/40 hover:bg-white/5 hover:text-white/70',
                        ]"
                    >
                        <div
                            v-if="activeTab === idx"
                            class="absolute inset-x-4 -bottom-3 h-0.5 bg-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.8)]"
                        ></div>
                        <span
                            class="text-xs font-black tracking-[0.15em] whitespace-nowrap uppercase"
                            >{{ cat.name }}</span
                        >
                        <StBadgeGlass
                            :variant="activeTab === idx ? 'primary' : 'glass'"
                            size="sm"
                            class="!rounded-lg transition-all duration-500"
                            :class="
                                activeTab === idx
                                    ? 'scale-110 shadow-[0_0_10px_rgba(99,102,241,0.3)]'
                                    : 'bg-white/5 opacity-40'
                            "
                        >
                            {{ countMappedInCategory(cat.name) }}
                        </StBadgeGlass>
                    </button>
                </nav>

                <!-- Matrix Interface -->
                <div
                    class="no-scrollbar custom-scrollbar relative max-h-[70vh] overflow-x-auto"
                >
                    <table class="w-full border-separate border-spacing-0">
                        <thead class="sticky top-0 z-30">
                            <tr class="bg-black/40 backdrop-blur-3xl">
                                <th
                                    class="sticky left-0 z-40 min-w-[300px] border-r border-b border-white/10 bg-black/60 p-0 text-left"
                                >
                                    <div class="px-8 py-6">
                                        <div
                                            class="mb-1 text-[10px] font-black tracking-[0.3em] text-white/20 uppercase"
                                        >
                                            Architecture
                                        </div>
                                        <h4
                                            class="text-sm font-black tracking-tight text-indigo-300"
                                        >
                                            Role Specification
                                        </h4>
                                    </div>
                                </th>
                                <th
                                    v-for="comp in categories[activeTab]?.comps"
                                    :key="comp.id"
                                    class="min-w-[200px] border-r border-b border-white/10 p-6 text-center transition-colors hover:bg-white/5"
                                >
                                    <div class="space-y-3">
                                        <div class="flex justify-center">
                                            <div
                                                class="h-1.5 w-1.5 rounded-full bg-indigo-400/30 transition-all duration-300 group-hover:bg-indigo-400"
                                            ></div>
                                        </div>
                                        <h5
                                            class="line-clamp-2 h-[36px] text-[11px] leading-relaxed font-black tracking-widest text-white/60 uppercase"
                                        >
                                            {{ comp.name }}
                                        </h5>
                                        <div
                                            class="text-[9px] font-black tracking-tighter text-indigo-400/40 uppercase"
                                        >
                                            {{
                                                comp.capability_name ||
                                                comp.category
                                            }}
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in store.matrixRows"
                                :key="row.roleId"
                                class="group transition-all duration-300"
                            >
                                <td
                                    class="sticky left-0 z-20 border-r border-b border-white/5 bg-black/40 p-8 backdrop-blur-2xl transition-colors group-hover:bg-white/[0.03]"
                                >
                                    <div class="space-y-4">
                                        <div
                                            class="role-name-link cursor-pointer transition-transform hover:translate-x-1"
                                        >
                                            <div
                                                class="text-base font-black tracking-tight text-white transition-colors group-hover:text-indigo-300"
                                            >
                                                {{ row.roleName }}
                                            </div>
                                        </div>

                                        <div class="flex flex-col gap-3">
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <StBadgeGlass
                                                    v-if="row.archetype"
                                                    :variant="
                                                        row.archetype === 'E'
                                                            ? 'primary'
                                                            : row.archetype ===
                                                                'T'
                                                              ? 'secondary'
                                                              : 'glass'
                                                    "
                                                    size="sm"
                                                    class="text-[9px] font-black tracking-widest"
                                                >
                                                    <v-icon
                                                        size="10"
                                                        class="mr-1.5"
                                                        :icon="
                                                            row.archetype ===
                                                            'E'
                                                                ? 'mdi-chess-king'
                                                                : row.archetype ===
                                                                    'T'
                                                                  ? 'mdi-account-tie'
                                                                  : 'mdi-wrench'
                                                        "
                                                    />
                                                    {{
                                                        row.archetype === 'E'
                                                            ? 'STRATEGIC'
                                                            : row.archetype ===
                                                                'T'
                                                              ? 'TACTICAL'
                                                              : 'OPERATIONAL'
                                                    }}
                                                </StBadgeGlass>
                                            </div>
                                            <div
                                                class="flex items-center gap-4 text-[10px] font-bold tracking-widest text-white/20 uppercase"
                                            >
                                                <div
                                                    class="flex items-center gap-1.5"
                                                >
                                                    <v-icon
                                                        size="12"
                                                        color="white/20"
                                                        >mdi-account-group</v-icon
                                                    >
                                                    <span
                                                        >{{ row.fte }} FTE</span
                                                    >
                                                </div>
                                                <div
                                                    class="h-1 w-1 rounded-full bg-white/5"
                                                ></div>
                                                <div
                                                    class="flex items-center gap-1.5"
                                                    :class="
                                                        row.status === 'new'
                                                            ? 'text-indigo-400/80'
                                                            : ''
                                                    "
                                                >
                                                    <v-icon
                                                        size="12"
                                                        :color="
                                                            row.status === 'new'
                                                                ? 'indigo-400/60'
                                                                : 'white/20'
                                                        "
                                                    >
                                                        {{
                                                            row.status === 'new'
                                                                ? 'mdi-plus-circle-outline'
                                                                : 'mdi-check-circle-outline'
                                                        }}
                                                    </v-icon>
                                                    <span>{{
                                                        row.status
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    v-for="comp in categories[activeTab]?.comps"
                                    :key="`${row.roleId}-${comp.id}`"
                                    class="group/cell border-r border-b border-white/5 p-0 transition-colors"
                                >
                                    <div
                                        class="relative flex h-36 w-full cursor-pointer items-center justify-center transition-all duration-500 hover:bg-indigo-500/15"
                                        @click="
                                            openEditModal(row.roleId, comp.id)
                                        "
                                    >
                                        <transition name="scale" mode="out-in">
                                            <div
                                                v-if="
                                                    !row.mappings.get(comp.id)
                                                "
                                                class="scale-75 transform opacity-0 transition-all duration-500 group-hover/cell:scale-100 group-hover/cell:opacity-100"
                                            >
                                                <div
                                                    class="flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white/20 shadow-xl group-hover/cell:border-indigo-500/30 group-hover/cell:bg-indigo-500/10 group-hover/cell:text-indigo-300"
                                                >
                                                    <v-icon size="18"
                                                        >mdi-molecule</v-icon
                                                    >
                                                </div>
                                            </div>
                                            <CellContent
                                                v-else
                                                :mapping="
                                                    row.mappings.get(comp.id)
                                                "
                                                :role-id="row.roleId"
                                                :role-name="row.roleName"
                                                :competency-id="comp.id"
                                                :competency-name="comp.name"
                                                @edit="
                                                    openEditModal(
                                                        row.roleId,
                                                        comp.id,
                                                    )
                                                "
                                                @remove="
                                                    removeMapping(
                                                        row.roleId,
                                                        comp.id,
                                                    )
                                                "
                                            />
                                        </transition>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </StCardGlass>
        </div>

        <!-- System Modals & Dialogs -->
        <RoleCompetencyStateModal
            v-if="selectedMapping"
            :visible="showEditModal"
            :role-id="selectedMapping.roleId"
            :role-name="selectedMapping.roleName"
            :archetype="selectedMapping.archetype"
            :competency-id="selectedMapping.competencyId"
            :competency-name="selectedMapping.competencyName"
            :mapping="selectedMapping.mapping"
            @save="saveMapping"
            @close="showEditModal = false"
        />

        <AddRoleDialog
            :visible="showAddRoleDialog"
            @save="handleAddRole"
            @close="showAddRoleDialog = false"
        />

        <AgentProposalsModal
            :visible="showAgentProposals"
            :loading="isDesigning"
            :proposals="agentProposals"
            :scenario-id="props.scenarioId"
            @close="showAgentProposals = false"
            @applied="handleApplied"
        />

        <RoleCubeWizard
            :visible="showCubeWizard"
            :scenario-id="props.scenarioId"
            @close="showCubeWizard = false"
            @created="handleRoleCreated"
        />

        <!-- Finalize Design Stage: Critical confirmation -->
        <v-dialog v-model="showFinalizeDialog" max-width="580" persistent>
            <StCardGlass
                variant="glass"
                border-accent="indigo"
                class="overflow-hidden bg-[#0a0f1d]/95 !p-0 backdrop-blur-3xl"
                :no-hover="true"
            >
                <div
                    class="flex items-center gap-5 border-b border-white/10 bg-indigo-50/10 px-8 py-6"
                >
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-indigo-400/30 bg-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                    >
                        <v-icon color="indigo-300" size="32"
                            >mdi-shield-check-outline</v-icon
                        >
                    </div>
                    <div>
                        <h2 class="text-xl leading-tight font-black text-white">
                            Finalize Design Stage
                        </h2>
                        <div
                            class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                        >
                            Strategic Commitment Protocol
                        </div>
                    </div>
                </div>

                <div class="space-y-6 px-10 py-8">
                    <p
                        class="text-sm leading-relaxed font-medium text-white/70"
                    >
                        You are about to finalize the architectural design phase
                        for
                        <span class="font-black text-white italic"
                            >"{{ store.scenarioName }}"</span
                        >. This operation is structural.
                    </p>

                    <div
                        class="rounded-2xl border border-amber-500/20 bg-amber-500/5 p-6"
                    >
                        <div class="mb-3 flex items-center gap-3">
                            <v-icon color="amber-400" size="20"
                                >mdi-alert-decagram</v-icon
                            >
                            <span
                                class="text-xs font-black tracking-widest text-amber-400 uppercase"
                                >Structural Warning</span
                            >
                        </div>
                        <p
                            class="text-[11px] leading-relaxed font-medium text-amber-200/60"
                        >
                            This transition will migrate all PROPOSED entities
                            (roles, competencies, and skill links) into the
                            <span
                                class="font-bold text-white underline decoration-amber-500/50"
                                >INCUBATION STATE</span
                            >. These records will be promoted to the engineering
                            laboratory for final catalog reconciliation.
                        </p>
                    </div>

                    <div class="flex flex-col gap-2 pt-2">
                        <div
                            class="flex items-center gap-3 text-xs text-white/40"
                        >
                            <v-icon size="16" color="emerald-400"
                                >mdi-check-circle-outline</v-icon
                            >
                            <span
                                >Lock tactical design of current scenario.</span
                            >
                        </div>
                        <div
                            class="flex items-center gap-3 text-xs text-white/40"
                        >
                            <v-icon size="16" color="emerald-400"
                                >mdi-check-circle-outline</v-icon
                            >
                            <span>Generate master records for incubation.</span>
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-3 border-t border-white/5 bg-black/40 px-8 py-6"
                >
                    <StButtonGlass
                        variant="ghost"
                        @click="showFinalizeDialog = false"
                    >
                        Return to Design
                    </StButtonGlass>
                    <StButtonGlass
                        variant="primary"
                        :loading="isFinalizing"
                        @click="handleFinalize"
                    >
                        <template #prepend>
                            <v-icon size="18">mdi-lock-check</v-icon>
                        </template>
                        Confirm & Lock Design
                    </StButtonGlass>
                </div>
            </StCardGlass>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';
import { computed, onMounted, ref, watch } from 'vue';
import AddRoleDialog from './AddRoleDialog.vue';
import AgentProposalsModal from './AgentProposalsModal.vue';
import CellContent from './CellContent.vue';
import RoleCompetencyStateModal from './RoleCompetencyStateModal.vue';
import RoleCubeWizard from './RoleCubeWizard.vue';

interface Props {
    scenarioId: number;
}

const props = defineProps<Props>();
const store = useRoleCompetencyStore();

const showEditModal = ref(false);
const showAddRoleDialog = ref(false);
const showCubeWizard = ref(false);
const activeTab = ref(0);
const selectedMapping = ref<{
    roleId: number;
    roleName: string;
    archetype: string;
    competencyId: number;
    competencyName: string;
    mapping: any;
} | null>(null);
const showSuccess = ref(false);
const isDesigning = ref(false);
const showAgentProposals = ref(false);
const agentProposals = ref<any>(null);

const showFinalizeDialog = ref(false);
const isFinalizing = ref(false);

const totalMappings = computed(() => {
    let count = 0;
    store.matrixRows.forEach((row) => {
        count += row.mappings?.size || 0;
    });
    return count;
});

const handleDesignTalent = async () => {
    isDesigning.value = true;
    showAgentProposals.value = true;
    try {
        const proposals = await store.designTalent();
        if (proposals) {
            agentProposals.value = proposals;
        }
    } finally {
        isDesigning.value = false;
    }
};

const handleApplied = async () => {
    showAgentProposals.value = false;
    agentProposals.value = null;
    await store.loadScenarioData(props.scenarioId);
    showSuccess.value = true;
};

const handleFinalize = async () => {
    isFinalizing.value = true;
    try {
        const result = await store.finalizeStep2();
        if (result.success) {
            showFinalizeDialog.value = false;
            showSuccess.value = true;
        } else {
            showFinalizeDialog.value = false;
        }
    } finally {
        isFinalizing.value = false;
    }
};

const categories = computed(() => {
    const map: Record<string, any[]> = {};
    store.competencyColumns.forEach((c: any) => {
        const cap = c.capability_name || c.category || 'General';
        if (!map[cap]) map[cap] = [];
        map[cap].push(c);
    });
    return Object.keys(map).map((k) => ({
        name: k,
        comps: map[k],
        count: map[k].length,
    }));
});

const countMappedInCategory = (categoryName: string) => {
    let total = 0;
    store.matrixRows.forEach((row: any) => {
        const comps =
            categories.value.find((c: any) => c.name === categoryName)?.comps ||
            [];
        comps.forEach((comp: any) => {
            if (row.mappings && row.mappings.get && row.mappings.get(comp.id)) {
                total += 1;
            }
        });
    });
    return total;
};

onMounted(async () => {
    await store.loadScenarioData(props.scenarioId);
});

watch(
    () => store.success,
    (val) => {
        if (val) showSuccess.value = true;
    },
);

const openEditModal = (roleId: number, competencyId: number) => {
    const role = store.roles.find((r) => r.id === roleId);
    const competency = store.competencies.find((c) => c.id === competencyId);
    const mapping = store.getMapping(roleId, competencyId);

    selectedMapping.value = {
        roleId,
        roleName: role?.role_name || '',
        archetype: role?.archetype || 'T',
        competencyId,
        competencyName: competency?.name || '',
        mapping,
    };

    showEditModal.value = true;
};

const saveMapping = async (mappingData: any) => {
    if (!selectedMapping.value) return;

    const newMapping = {
        id: mappingData.id,
        scenario_id: store.scenarioId!,
        role_id: selectedMapping.value.roleId,
        competency_id: selectedMapping.value.competencyId,
        competency_name: selectedMapping.value.competencyName,
        required_level: mappingData.required_level,
        is_core: mappingData.is_core,
        change_type: mappingData.change_type,
        rationale: mappingData.rationale,
        competency_version_id:
            mappingData.competency_version_id ||
            selectedMapping.value.mapping?.competency_version_id,
        current_level: mappingData.current_level,
        timeline_months: mappingData.timeline_months,
    };

    await store.saveMapping(newMapping);
    showEditModal.value = false;
};

const removeMapping = async (roleId: number, competencyId: number) => {
    const mapping = store.getMapping(roleId, competencyId);
    if (mapping && confirm('Remove this association?')) {
        await store.removeMapping(roleId, competencyId, mapping.id);
    }
};

const handleAddRole = async (roleData: any) => {
    await store.addNewRole(roleData);
    showAddRoleDialog.value = false;
};
const handleRoleCreated = async () => {
    await store.loadScenarioData(props.scenarioId);
};
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}

.scale-enter-active,
.scale-leave-active {
    transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.scale-enter-from,
.scale-leave-to {
    transform: scale(0.9);
    opacity: 0;
}
</style>
