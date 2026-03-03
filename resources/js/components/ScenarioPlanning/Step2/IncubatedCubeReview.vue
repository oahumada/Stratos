<template>
    <div
        class="incubated-cube-review animate-in space-y-8 duration-700 fade-in slide-in-from-bottom-4"
    >
        <!-- Dashboard Header -->
        <div class="cube-review-header">
            <StCardGlass variant="glass" class="overflow-hidden p-0!">
                <div
                    class="flex flex-col border-b border-white/5 bg-white/5 p-6 md:flex-row md:items-center md:gap-6"
                >
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                    >
                        <v-icon color="indigo-300" size="32"
                            >mdi-cube-scan</v-icon
                        >
                    </div>
                    <div class="mt-4 flex-1 md:mt-0">
                        <h4
                            class="mb-1 text-xl font-black tracking-tight text-white"
                        >
                            Role Engineering & Organizational Cube
                        </h4>
                        <p
                            class="max-w-3xl text-sm leading-relaxed font-medium text-white/50"
                        >
                            Validating tridimensional coherence
                            <span class="font-bold text-indigo-400">
                                (Processes × Archetypes × Mastery)</span
                            >. Elements here are in
                            <span
                                class="font-bold text-white underline decoration-indigo-500/50"
                                >laboratory mode</span
                            >
                            and won't affect the catalog until reconciled.
                        </p>
                    </div>
                    <div class="mt-6 flex shrink-0 items-center gap-3 md:mt-0">
                        <StButtonGlass
                            variant="ghost"
                            circle
                            icon="mdi-help-circle-outline"
                            @click="showMatchHelp = !showMatchHelp"
                            :class="{ 'bg-white/10 text-white': showMatchHelp }"
                        />
                        <StButtonGlass
                            variant="primary"
                            :loading="approving"
                            @click="approveSelection"
                            :disabled="selectedIds.length === 0"
                        >
                            <template #prepend>
                                <v-icon size="18"
                                    >mdi-checkbox-marked-circle-outline</v-icon
                                >
                            </template>
                            Approve for Engineering ({{ selectedIds.length }})
                        </StButtonGlass>
                    </div>
                </div>

                <!-- Help Guide -->
                <v-expand-transition>
                    <div
                        v-if="showMatchHelp"
                        class="border-b border-white/5 bg-black/20 p-6"
                    >
                        <div class="mb-6 flex items-center gap-2">
                            <v-icon color="indigo-400" size="18"
                                >mdi-book-open-variant</v-icon
                            >
                            <h5
                                class="text-[10px] font-black tracking-[0.2em] text-white/40 uppercase"
                            >
                                Organizational Reconciliation Guide
                            </h5>
                        </div>

                        <div
                            class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                        >
                            <!-- Enrichment -->
                            <div
                                class="group rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4 transition-all hover:bg-emerald-500/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-widest text-emerald-400 uppercase"
                                        >New (0%)</span
                                    >
                                    <v-icon size="16" color="emerald-400"
                                        >mdi-trending-up</v-icon
                                    >
                                </div>
                                <div class="mb-1 text-sm font-black text-white">
                                    📈 Enrichment
                                </div>
                                <div
                                    class="text-[11px] leading-snug font-medium text-white/50"
                                >
                                    <strong>Job Enlargement</strong>: Horizontal
                                    growth. Creation of non-existent
                                    capacity/role.
                                </div>
                            </div>

                            <!-- Transformation -->
                            <div
                                class="group rounded-2xl border border-indigo-500/20 bg-indigo-500/5 p-4 transition-all hover:bg-indigo-500/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                                        >Partial (40-85%)</span
                                    >
                                    <v-icon size="16" color="indigo-400"
                                        >mdi-auto-fix</v-icon
                                    >
                                </div>
                                <div class="mb-1 text-sm font-black text-white">
                                    🔄 Transformation
                                </div>
                                <div
                                    class="text-[11px] leading-snug font-medium text-white/50"
                                >
                                    <strong>Job Enrichment</strong>: Vertical
                                    growth. Role evolves in depth (Upskilling).
                                </div>
                            </div>

                            <!-- Maintenance -->
                            <div
                                class="group rounded-2xl border border-white/10 bg-white/5 p-4 transition-all hover:bg-white/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                        >Existing (>85%)</span
                                    >
                                    <v-icon size="16" color="white/30"
                                        >mdi-check-circle-outline</v-icon
                                    >
                                </div>
                                <div class="mb-1 text-sm font-black text-white">
                                    ✅ Maintenance
                                </div>
                                <div
                                    class="text-[11px] leading-snug font-medium text-white/50"
                                >
                                    <strong>Job Stabilization</strong>: Current
                                    role is mature and sufficient for the
                                    design.
                                </div>
                            </div>

                            <!-- Extinction -->
                            <div
                                class="group rounded-2xl border border-rose-500/20 bg-rose-500/5 p-4 transition-all hover:bg-rose-500/10"
                            >
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <span
                                        class="text-[10px] font-black tracking-widest text-rose-400 uppercase"
                                        >Not Proposed</span
                                    >
                                    <v-icon size="16" color="rose-400"
                                        >mdi-close-circle-outline</v-icon
                                    >
                                </div>
                                <div class="mb-1 text-sm font-black text-white">
                                    📉 Legacy
                                </div>
                                <div
                                    class="text-[11px] leading-snug font-medium text-white/50"
                                >
                                    <strong>Job Substitution</strong>: Potential
                                    obsolescence due to strategic model shift.
                                </div>
                            </div>
                        </div>
                    </div>
                </v-expand-transition>
            </StCardGlass>
        </div>

        <!-- Confirmation Dialog -->
        <v-dialog v-model="confirmApproval" max-width="550" persistent>
            <StCardGlass variant="glass" border-accent="indigo" class="p-0!">
                <div
                    class="flex items-center gap-4 border-b border-white/10 bg-indigo-500/10 px-8 py-6"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl border border-indigo-400/30 bg-indigo-500/20"
                    >
                        <v-icon color="indigo-300" size="24"
                            >mdi-shield-alert</v-icon
                        >
                    </div>
                    <div>
                        <h3
                            class="text-xl font-black tracking-tight text-white"
                        >
                            Confirm Reconciliation
                        </h3>
                        <p
                            class="text-[11px] font-black tracking-widest text-white/40 uppercase"
                        >
                            Engineering Phase Transition
                        </p>
                    </div>
                </div>

                <div class="p-8">
                    <p
                        class="text-sm leading-relaxed font-medium text-white/70"
                    >
                        You are about to promote
                        <span class="px-1 font-black text-indigo-400"
                            >{{ selectedIds.length }} elements</span
                        >
                        from laboratory to engineering phase.
                    </p>

                    <div
                        class="mt-6 rounded-2xl border border-amber-500/20 bg-amber-500/10 p-5"
                    >
                        <div class="mb-2 flex items-center gap-3">
                            <v-icon color="amber-400" size="20"
                                >mdi-alert-octagon</v-icon
                            >
                            <span
                                class="text-xs font-black tracking-widest text-amber-400 uppercase"
                                >Engineering Warning</span
                            >
                        </div>
                        <p
                            class="text-[11px] leading-relaxed font-medium text-amber-200/80"
                        >
                            This step creates master records. Once promoted,
                            they cannot be reverted to "incubation" from this
                            view.
                        </p>
                    </div>

                    <div class="mt-8">
                        <div
                            class="mb-3 text-[10px] font-black tracking-widest text-white/30 uppercase"
                        >
                            Entities to promote:
                        </div>
                        <div
                            class="custom-scrollbar max-h-[180px] space-y-2 overflow-y-auto pr-2"
                        >
                            <div
                                v-for="s in selectedIds"
                                :key="s"
                                class="flex items-center gap-3 rounded-xl border border-white/5 bg-white/5 p-3 text-xs font-bold text-white/80"
                            >
                                <v-icon size="14" color="indigo-400"
                                    >mdi-check-decagram</v-icon
                                >
                                <span class="capitalize">{{
                                    getItemLabel(s)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex items-center justify-end gap-3 border-t border-white/5 bg-black/20 p-6"
                >
                    <StButtonGlass
                        variant="ghost"
                        @click="confirmApproval = false"
                        >Cancel</StButtonGlass
                    >
                    <StButtonGlass
                        variant="primary"
                        @click="executeApproval"
                        :loading="approving"
                        >Proceed to Engineering</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>

        <!-- Main Content -->
        <transition name="fade" mode="out-in">
            <div
                v-if="loading"
                key="loading"
                class="flex flex-col items-center justify-center p-32"
            >
                <v-progress-circular
                    indeterminate
                    color="indigo-400"
                    size="64"
                    width="4"
                ></v-progress-circular>
                <span
                    class="mt-6 animate-pulse text-sm font-black tracking-widest text-indigo-400/60 uppercase"
                    >Analyzing Cube Geometry...</span
                >
            </div>

            <div
                v-else-if="!hasData"
                key="empty"
                class="flex flex-col items-center justify-center p-32 text-center"
            >
                <div
                    class="mb-6 flex h-24 w-24 items-center justify-center rounded-full border border-white/10 bg-white/5 shadow-2xl"
                >
                    <v-icon size="48" color="white/20"
                        >mdi-cube-off-outline</v-icon
                    >
                </div>
                <h3 class="mb-2 text-2xl font-black tracking-tight text-white">
                    No Incubated Proposals Found
                </h3>
                <p
                    class="mx-auto max-w-md text-sm leading-relaxed font-medium text-white/40"
                >
                    Use the AI engine to generate roles and competencies
                    proposals for this specific scenario architecture.
                </p>
            </div>

            <div v-else key="content" class="space-y-12">
                <div
                    v-for="cap in capabilities"
                    :key="cap.id"
                    class="group relative"
                >
                    <StCardGlass variant="glass" class="overflow-visible p-0!">
                        <!-- Capability Header -->
                        <div
                            class="flex items-center justify-between border-b border-white/5 bg-white/5 px-8 py-5"
                        >
                            <div class="flex items-center gap-5">
                                <v-checkbox
                                    v-model="groupSelections[cap.id]"
                                    @change="toggleGroup(cap.id)"
                                    hide-details
                                    density="compact"
                                    color="indigo-accent-2"
                                    class="mt-0"
                                ></v-checkbox>
                                <div>
                                    <span
                                        class="text-[10px] font-black tracking-[0.3em] text-indigo-400 uppercase"
                                        >Capability Node</span
                                    >
                                    <h4
                                        class="text-2xl font-black tracking-tight text-white"
                                    >
                                        {{ cap.name }}
                                    </h4>
                                </div>
                            </div>
                            <StBadgeGlass
                                variant="glass"
                                size="md"
                                class="border border-white/10!"
                            >
                                {{ cap.category || 'Core Business' }}
                            </StBadgeGlass>
                        </div>

                        <!-- Role Grid -->
                        <div
                            class="grid grid-cols-1 gap-6 p-8 md:grid-cols-2 lg:grid-cols-3"
                        >
                            <div
                                v-for="role in getRolesForCapability(cap)"
                                :key="role.id"
                                class="role-card-glass group/card relative flex flex-col rounded-2xl border border-white/10 bg-black/40 p-6 transition-all duration-500 hover:-translate-y-2 hover:border-indigo-500/40 hover:bg-white/5 hover:shadow-[0_20px_40px_rgba(0,0,0,0.4),0_0_20px_rgba(99,102,241,0.1)]"
                            >
                                <div
                                    class="absolute top-4 right-4 z-10 opacity-40 transition-opacity group-hover/card:opacity-100"
                                >
                                    <v-checkbox
                                        :model-value="
                                            isSelected(role.id, 'role')
                                        "
                                        @update:model-value="
                                            toggleItem(role.id, 'role')
                                        "
                                        hide-details
                                        density="compact"
                                        color="indigo-accent-2"
                                    ></v-checkbox>
                                </div>

                                <div class="mb-5 flex flex-wrap gap-2">
                                    <StBadgeGlass
                                        :variant="getArchetypeVariant(role)"
                                        size="sm"
                                    >
                                        {{ getArchetypeLabel(role) }}
                                    </StBadgeGlass>

                                    <v-tooltip
                                        v-if="role.similarity_warnings?.length"
                                        location="top"
                                        offset="10"
                                    >
                                        <template v-slot:activator="{ props }">
                                            <div v-bind="props">
                                                <StBadgeGlass
                                                    :variant="
                                                        getSimilarityVariant(
                                                            role
                                                                .similarity_warnings[0]
                                                                .score,
                                                        )
                                                    "
                                                    size="sm"
                                                    class="cursor-help"
                                                >
                                                    {{
                                                        getSimilarityLabel(
                                                            role
                                                                .similarity_warnings[0]
                                                                .score,
                                                        )
                                                    }}
                                                    ({{
                                                        Math.round(
                                                            role
                                                                .similarity_warnings[0]
                                                                .score * 100,
                                                        )
                                                    }}%)
                                                </StBadgeGlass>
                                            </div>
                                        </template>
                                        <StCardGlass
                                            variant="glass"
                                            class="max-w-[280px] border-white/20 p-4! backdrop-blur-xl"
                                        >
                                            <div
                                                class="mb-2 border-b border-white/10 pb-2 text-[10px] font-black tracking-widest text-white uppercase"
                                            >
                                                Similarity Detection
                                            </div>
                                            <div class="space-y-2">
                                                <div
                                                    v-for="w in role.similarity_warnings"
                                                    :key="w.id"
                                                    class="flex items-center justify-between gap-4 text-xs"
                                                >
                                                    <span
                                                        class="truncate text-white/70"
                                                        >{{ w.name }}</span
                                                    >
                                                    <span
                                                        class="font-black text-indigo-400"
                                                        >{{
                                                            Math.round(
                                                                w.score * 100,
                                                            )
                                                        }}%</span
                                                    >
                                                </div>
                                            </div>
                                            <div
                                                class="mt-3 text-[10px] leading-relaxed font-medium text-white/40 italic"
                                            >
                                                {{
                                                    getSimilarityTip(
                                                        role
                                                            .similarity_warnings[0]
                                                            .score,
                                                    )
                                                }}
                                            </div>
                                        </StCardGlass>
                                    </v-tooltip>
                                    <StBadgeGlass
                                        v-else
                                        variant="secondary"
                                        size="sm"
                                        >New Design</StBadgeGlass
                                    >
                                </div>

                                <h5
                                    class="mb-2 text-lg font-black text-white transition-colors group-hover/card:text-indigo-300"
                                >
                                    {{ role.role_name }}
                                </h5>
                                <p
                                    class="mb-6 line-clamp-2 h-10 text-[13px] leading-relaxed font-medium text-white/40"
                                >
                                    {{ role.role_description }}
                                </p>

                                <div class="mt-auto space-y-4">
                                    <div
                                        class="flex items-center justify-between px-1"
                                    >
                                        <span
                                            class="text-[9px] font-black tracking-[0.2em] text-white/20 uppercase"
                                            >Required Competencies</span
                                        >
                                        <v-icon size="12" color="white/10"
                                            >mdi-dots-horizontal</v-icon
                                        >
                                    </div>

                                    <div class="space-y-2">
                                        <div
                                            v-for="(
                                                comp, cIdx
                                            ) in getCompetenciesForRole(role)"
                                            :key="cIdx"
                                            class="flex flex-col gap-1.5 rounded-xl border border-white/5 bg-white/5 p-3 transition-all hover:bg-white/10"
                                        >
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <span
                                                    class="truncate pr-2 text-[11px] font-bold text-white/80"
                                                    >{{ comp.name }}</span
                                                >
                                                <span
                                                    class="text-[10px] font-black text-indigo-400"
                                                    >L{{ comp.level }}</span
                                                >
                                            </div>
                                            <div class="flex gap-1">
                                                <div
                                                    v-for="n in 5"
                                                    :key="n"
                                                    class="h-1 flex-1 rounded-full transition-all duration-500"
                                                    :class="
                                                        n <= comp.level
                                                            ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.4)]'
                                                            : 'bg-white/5'
                                                    "
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </StCardGlass>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    scenarioId: number;
}>();

const { showSuccess, showError } = useNotification();

interface Capability {
    id: number;
    name: string;
    category: string;
    llm_id?: string;
}

interface Role {
    id: number;
    role_name: string;
    role_description: string;
    human_leverage: number;
    key_competencies: any;
    similarity_warnings?: Array<{ id: number; name: string; score: number }>;
}

interface Competency {
    id: number;
    name: string;
    capability_id?: number;
}

const loading = ref(false);
const approving = ref(false);
const confirmApproval = ref(false);
const showMatchHelp = ref(false);
const capabilities = ref<Capability[]>([]);
const roles = ref<Role[]>([]);
const competencies = ref<Competency[]>([]);
const selectedIds = ref<string[]>([]);
const groupSelections = ref<Record<number, boolean>>({});

const hasData = computed(
    () => capabilities.value.length > 0 || roles.value.length > 0,
);

const getItemLabel = (key: string) => {
    const [type, id] = key.split(':');
    const numericId = Number(id);
    if (type === 'role')
        return roles.value.find((r) => r.id === numericId)?.role_name || 'Role';
    if (type === 'capability')
        return (
            capabilities.value.find((c) => c.id === numericId)?.name ||
            'Capability'
        );
    return `ID: ${id}`;
};

const fetchData = async () => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/scenarios/${props.scenarioId}/step2/cube`,
        );
        const remoteData = res.data.data || res.data;
        capabilities.value = remoteData.capabilities || [];
        roles.value = remoteData.roles || [];
        competencies.value = remoteData.competencies || [];
    } catch (e: any) {
        showError('Could not load Cube data.');
    } finally {
        loading.value = false;
    }
};

const approveSelection = () => {
    confirmApproval.value = true;
};

const executeApproval = async () => {
    approving.value = true;
    try {
        await axios.post(
            `/api/scenarios/${props.scenarioId}/step2/approve-cube`,
            {
                selection: selectedIds.value,
            },
        );
        showSuccess('Elements promoted to engineering phase');
        confirmApproval.value = false;
        selectedIds.value = [];
        await fetchData();
    } catch (e: any) {
        showError('Failed to promote elements');
    } finally {
        approving.value = false;
    }
};

const getArchetypeLabel = (role: Role) => {
    if (role.human_leverage > 70) return 'Strategic (E)';
    if (role.human_leverage > 40) return 'Tactical (T)';
    return 'Operational (O)';
};

const getArchetypeVariant = (role: Role) => {
    if (role.human_leverage > 70) return 'primary';
    if (role.human_leverage > 40) return 'secondary';
    return 'success';
};

const getSimilarityLabel = (score: number) => {
    if (score > 0.85) return 'Exists';
    return 'Partial Match';
};

const getSimilarityVariant = (score: number) => {
    if (score > 0.85) return 'glass';
    return 'warning';
};

const getSimilarityTip = (score: number) => {
    if (score > 0.85)
        return 'Suggestion: Maintenance. This role is already covered in the catalog.';
    return 'Suggestion: Transformation / Upskilling. Role evolves relative to catalog.';
};

const getRolesForCapability = (cap: Capability) => {
    const filtered = roles.value.filter((role) => {
        const comps = role.key_competencies;
        return (
            Array.isArray(comps) &&
            comps.some((c: any) => c.capability_id === cap.id)
        );
    });

    if (filtered.length === 0 && cap.id === capabilities.value[0]?.id) {
        return roles.value.filter(
            (r) =>
                !Array.isArray(r.key_competencies) ||
                r.key_competencies.length === 0,
        );
    }
    return filtered;
};

const getCompetenciesForRole = (role: Role) => {
    let raw = role.key_competencies;
    if (typeof raw === 'string') {
        try {
            raw = JSON.parse(raw);
        } catch {
            raw = [];
        }
    }
    if (!Array.isArray(raw)) return [];
    return raw.map((c) => ({
        name: c.name || c.key || 'Competency',
        level: c.level || c.score || 3,
    }));
};

const isSelected = (id: number, type: string) =>
    selectedIds.value.includes(`${type}:${id}`);

const toggleItem = (id: number, type: string) => {
    const key = `${type}:${id}`;
    const idx = selectedIds.value.indexOf(key);
    if (idx > -1) selectedIds.value.splice(idx, 1);
    else selectedIds.value.push(key);
};

const toggleGroup = (capId: number) => {
    const isChecked = groupSelections.value[capId];
    const rolesInGroup = getRolesForCapability(
        capabilities.value.find((c) => c.id === capId)!,
    );
    rolesInGroup.forEach((r) => {
        const key = `role:${r.id}`;
        const hasKey = selectedIds.value.includes(key);
        if (isChecked && !hasKey) selectedIds.value.push(key);
        if (!isChecked && hasKey)
            selectedIds.value.splice(selectedIds.value.indexOf(key), 1);
    });
};

onMounted(fetchData);
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.5);
    border-radius: 4px;
}
</style>
