<script setup lang="ts">
import EngineeringBlueprintSheet from '@/components/ScenarioPlanning/Step3/EngineeringBlueprintSheet.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import InfoLegend from '@/components/Ui/InfoLegend.vue';
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';
import { useTransformStore } from '@/stores/transformStore';
import { computed, onMounted, ref, watch } from 'vue';

interface Props {
    visible: boolean;
    roleId: number;
    roleName: string;
    archetype?: string;
    competencyId: number;
    competencyName: string;
    mapping?: any;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (e: 'save', data: any): void;
    (e: 'close'): void;
}>();

const saving = ref(false);
const showTransform = ref(false);
const showLegend = ref(false);

const transformStore = useTransformStore();
const roleCompetencyStore = useRoleCompetencyStore();
const versions = ref<any[]>([]);

const archetypeLabel = computed(() => {
    const map: Record<string, string> = {
        E: 'Strategic',
        T: 'Tactical',
        O: 'Operational',
    };
    return map[props.archetype || 'T'] || 'Tactical';
});

const stateOptions = [
    {
        value: 'maintenance',
        label: 'Maintenance',
        desc: 'Job Stabilization. No structural changes expected.',
        icon: 'mdi-check-circle-outline',
        colorText: 'text-emerald-400',
        colorIcon: 'emerald-400',
        glow: 'shadow-[0_0_20px_rgba(52,211,153,0.15)]',
    },
    {
        value: 'transformation',
        label: 'Transformation',
        desc: 'Vertical Enrichment. DNA mutation & upskilling.',
        icon: 'mdi-auto-fix',
        colorText: 'text-indigo-400',
        colorIcon: 'indigo-400',
        glow: 'shadow-[0_0_20px_rgba(129,140,248,0.15)]',
    },
    {
        value: 'enrichment',
        label: 'Enrichment',
        desc: 'Horizontal Enlargement. Task diversification.',
        icon: 'mdi-trending-up',
        colorText: 'text-blue-400',
        colorIcon: 'blue-400',
        glow: 'shadow-[0_0_20px_rgba(96,165,250,0.15)]',
    },
    {
        value: 'extinction',
        label: 'Legacy',
        desc: 'Strategic Substitution. Sunset plan required.',
        icon: 'mdi-close-circle-outline',
        colorText: 'text-rose-400',
        colorIcon: 'rose-400',
        glow: 'shadow-[0_0_20px_rgba(251,113,133,0.15)]',
    },
];

const consistencyAlert = computed(() => {
    const level = formData.value.required_level;
    const arch = props.archetype || 'O';
    const isReferent = formData.value.is_referent;

    if (arch === 'O' && level > 3 && !isReferent) {
        return {
            bgClass: 'bg-indigo-500/10',
            borderClass: 'border-indigo-500/30',
            glowClass: 'bg-indigo-500',
            iconBoxClass:
                'bg-indigo-500/20 border-indigo-500/30 text-indigo-300',
            icon: 'mdi-lightbulb-on-outline',
            textTitleClass: 'text-indigo-400',
            dotClass: 'bg-indigo-400 animate-pulse',
            message: `High mastery detected for Operational role. This creates technical overload unless marked as a "Referent".`,
        };
    }

    if (arch === 'O' && level > 3 && isReferent) {
        return {
            bgClass: 'bg-emerald-500/10',
            borderClass: 'border-emerald-500/30',
            glowClass: 'bg-emerald-500',
            iconBoxClass:
                'bg-emerald-500/20 border-emerald-500/30 text-emerald-300',
            icon: 'mdi-account-star',
            textTitleClass: 'text-emerald-400',
            dotClass: 'bg-emerald-400',
            message: `Validated architecture. This operational role is designed as a technical mentor.`,
        };
    }

    if ((arch === 'E' && level < 4) || (arch === 'T' && level < 3)) {
        return {
            bgClass: 'bg-blue-500/10',
            borderClass: 'border-blue-500/30',
            glowClass: 'bg-blue-500',
            iconBoxClass: 'bg-blue-500/20 border-blue-500/30 text-blue-300',
            icon: 'mdi-shield-star-outline',
            textTitleClass: 'text-blue-400',
            dotClass: 'bg-blue-400',
            message: `Support Competency. Valid design for ${archetypeLabel.value} roles as non-core capability.`,
        };
    }

    if (arch === 'T' && level > 4 && !isReferent) {
        return {
            bgClass: 'bg-amber-500/10',
            borderClass: 'border-amber-500/30',
            glowClass: 'bg-amber-500',
            iconBoxClass: 'bg-amber-500/20 border-amber-500/30 text-amber-300',
            icon: 'mdi-alert-circle-outline',
            textTitleClass: 'text-amber-400',
            dotClass: 'bg-amber-500 animate-pulse',
            message: `Possible misalignment. Level 5 suggests this should be a Global Referent or Strategic role.`,
        };
    }

    return {
        bgClass: 'bg-emerald-500/5',
        borderClass: 'border-emerald-500/20',
        glowClass: 'bg-emerald-500',
        iconBoxClass:
            'bg-emerald-500/20 border-emerald-500/20 text-emerald-400',
        icon: 'mdi-check-decagram',
        textTitleClass: 'text-emerald-400',
        dotClass: 'bg-emerald-400',
        message: `Plan is consistent within the ${archetypeLabel.value} Archetype boundaries.`,
    };
});

const stateIcon = computed(() => {
    const opt = stateOptions.find(
        (o) => o.value === formData.value.change_type,
    );
    return opt ? opt.icon : 'mdi-help-circle-outline';
});

const legendItems = [
    {
        title: '✅ Maintenance',
        text: 'Job design is mature. Focus on sustainability. No structural changes needed.',
    },
    {
        title: '🔄 Transformation',
        text: 'Vertical Growth. The role grows in complexity and autonomy. Mutation of role DNA.',
    },
    {
        title: '📈 Enrichment',
        text: 'Horizontal Expansion. New tasks or competencies added at the same layer.',
    },
    {
        title: '📉 Legacy',
        text: 'Creative Destruction. Role is displaced by automation/change. Transition plan required.',
    },
];

async function loadVersions() {
    try {
        const v = await transformStore.getVersions(props.competencyId);
        versions.value = v || [];
        if (!props.mapping) {
            formData.value.change_type =
                versions.value.length > 0 ? 'transformation' : 'enrichment';
        } else if (!props.mapping.change_type && versions.value.length > 0) {
            formData.value.change_type = 'transformation';
        }
    } catch {
        versions.value = [];
    }
}

onMounted(() => loadVersions());
watch(
    () => props.competencyId,
    () => loadVersions(),
);

const formData = ref({
    id: null as number | null,
    change_type: 'maintenance' as any,
    required_level: 3,
    current_level: 1,
    is_core: true,
    is_referent: false,
    rationale: '',
    timeline_months: 12,
    extinction_timeline: 12,
    transition_plan: 'reskilling' as any,
    suggest_learning_path: false,
    competency_version_id: null as number | null,
    reduced_level_rationale: 'efficiency' as any,
});

const showLevelDecreaseRationale = computed(() => {
    if (!props.mapping) return false;
    return formData.value.required_level < props.mapping.required_level;
});

const showReferentOption = computed(() => {
    const arch = props.archetype || 'T';
    const level = formData.value.required_level;
    return (arch === 'O' && level > 3) || (arch === 'T' && level > 4);
});

const changeTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        maintenance: 'Maintain',
        transformation: 'Transform',
        enrichment: 'Enrich',
        extinction: 'Legacy',
    };
    return labels[type] || type;
};

const handleSave = async () => {
    if (
        formData.value.change_type === 'transformation' &&
        !formData.value.competency_version_id
    ) {
        showTransform.value = true;
        return;
    }
    saving.value = true;
    try {
        emit('save', { ...formData.value, id: props.mapping?.id });
    } finally {
        saving.value = false;
    }
};

const handleTransformed = (data: any) => {
    formData.value.competency_version_id = data.version_id;
    showTransform.value = false;
};

const handleOpenTransform = () => {
    showTransform.value = true;
};

watch(
    () => props.mapping,
    (mapping) => {
        if (mapping) {
            formData.value = {
                ...formData.value,
                id: mapping.id,
                change_type: mapping.change_type || 'maintenance',
                required_level: mapping.required_level,
                current_level: mapping.current_level || 1,
                is_core: !!mapping.is_core,
                is_referent: !!mapping.is_referent,
                rationale: mapping.rationale || '',
                competency_version_id: mapping.competency_version_id,
            };
        }
    },
    { immediate: true },
);
</script>

<template>
    <v-dialog
        :model-value="props.visible"
        max-width="850px"
        persistent
        @update:model-value="$emit('close')"
    >
        <StCardGlass
            variant="glass"
            class="pa-0 overflow-hidden border-white/10 bg-[#0d1425]/98 backdrop-blur-3xl"
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
                            class="flex h-14 w-14 items-center justify-center rounded-2xl border border-indigo-500/30 bg-indigo-500/10 shadow-[0_0_20px_rgba(99,102,241,0.2)]"
                        >
                            <v-icon color="indigo-400" size="28">{{
                                stateIcon
                            }}</v-icon>
                        </div>
                        <div>
                            <h2
                                class="mb-1 text-2xl font-black tracking-tight text-white"
                            >
                                {{ props.competencyName }}
                            </h2>
                            <div class="flex items-center gap-3">
                                <span
                                    class="text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                    >Protocol Alignment</span
                                >
                                <div
                                    class="h-1 w-1 rounded-full bg-white/10"
                                ></div>
                                <span
                                    class="text-[11px] font-bold tracking-widest text-indigo-400 uppercase"
                                    >{{ props.roleName }}</span
                                >
                            </div>
                        </div>
                    </div>
                    <StButtonGlass
                        variant="ghost"
                        circle
                        size="sm"
                        icon="mdi-close"
                        @click="$emit('close')"
                    />
                </div>
            </div>

            <div
                class="custom-scrollbar max-h-[75vh] overflow-y-auto px-10 py-10"
            >
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
                    <!-- Left Column: Strategy & Core Settings -->
                    <div class="space-y-10 lg:col-span-12">
                        <!-- Strategy Grid -->
                        <section>
                            <div class="mb-6 flex items-center justify-between">
                                <h3
                                    class="text-xs font-black tracking-[0.3em] text-white/30 uppercase"
                                >
                                    Neural Strategic Directives
                                </h3>
                                <button
                                    @click="showLegend = true"
                                    class="flex items-center gap-2 text-[10px] font-black text-indigo-400 uppercase transition-colors hover:text-indigo-300"
                                >
                                    <v-icon size="14"
                                        >mdi-book-open-outline</v-icon
                                    >
                                    Technical Guide
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div
                                    v-for="option in stateOptions"
                                    :key="option.value"
                                    @click="formData.change_type = option.value"
                                    class="group relative flex cursor-pointer flex-col gap-2 overflow-hidden rounded-2xl border p-5 transition-all duration-500"
                                    :class="
                                        formData.change_type === option.value
                                            ? `border-white/20 bg-white/5 ${option.glow}`
                                            : 'border-white/5 bg-white/[0.02] hover:border-white/10 hover:bg-white/[0.04]'
                                    "
                                >
                                    <div
                                        class="relative z-10 flex items-center justify-between"
                                    >
                                        <span
                                            class="text-xs font-black tracking-widest uppercase transition-colors"
                                            :class="
                                                formData.change_type ===
                                                option.value
                                                    ? option.colorText
                                                    : 'text-white/40 group-hover:text-white/80'
                                            "
                                        >
                                            {{ option.label }}
                                        </span>
                                        <v-icon
                                            size="18"
                                            :color="
                                                formData.change_type ===
                                                option.value
                                                    ? option.colorIcon
                                                    : 'white/10'
                                            "
                                        >
                                            {{ option.icon }}
                                        </v-icon>
                                    </div>
                                    <p
                                        class="relative z-10 pr-8 text-[11px] leading-relaxed font-medium text-white/30 transition-colors group-hover:text-white/50"
                                    >
                                        {{ option.desc }}
                                    </p>
                                    <div
                                        v-if="
                                            formData.change_type ===
                                            option.value
                                        "
                                        class="pointer-events-none absolute inset-0 bg-white/[0.02]"
                                    ></div>
                                </div>
                            </div>
                        </section>

                        <!-- Level & Logic -->
                        <div class="grid grid-cols-1 gap-10 md:grid-cols-2">
                            <!-- Mastery Selection -->
                            <section class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <h3
                                        class="text-xs font-black tracking-[0.3em] text-white/30 uppercase"
                                    >
                                        Mastery Specification
                                    </h3>
                                    <div class="h-px flex-1 bg-white/5"></div>
                                </div>

                                <div
                                    class="group relative overflow-hidden rounded-3xl border border-white/5 bg-white/2 p-8"
                                >
                                    <!-- Level Selector Component -->
                                    <div
                                        v-if="
                                            formData.change_type ===
                                            'transformation'
                                        "
                                        class="relative z-10 flex items-center justify-between gap-6"
                                    >
                                        <div class="text-center">
                                            <div
                                                class="mb-3 text-[9px] font-black tracking-widest text-white/20 uppercase"
                                            >
                                                Current
                                            </div>
                                            <div
                                                class="flex items-center rounded-2xl border border-white/5 bg-black/20 p-2"
                                            >
                                                <StButtonGlass
                                                    variant="ghost"
                                                    circle
                                                    size="xs"
                                                    icon="mdi-minus"
                                                    @click="
                                                        formData.current_level =
                                                            Math.max(
                                                                1,
                                                                formData.current_level -
                                                                    1,
                                                            )
                                                    "
                                                />
                                                <span
                                                    class="w-12 text-2xl font-black text-white/40 tabular-nums"
                                                    >{{
                                                        formData.current_level
                                                    }}</span
                                                >
                                                <StButtonGlass
                                                    variant="ghost"
                                                    circle
                                                    size="xs"
                                                    icon="mdi-plus"
                                                    @click="
                                                        formData.current_level =
                                                            Math.min(
                                                                5,
                                                                formData.current_level +
                                                                    1,
                                                            )
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <v-icon size="20" color="white/10"
                                            >mdi-chevron-double-right</v-icon
                                        >
                                        <div class="text-center">
                                            <div
                                                class="mb-3 text-[9px] font-black tracking-widest text-indigo-400 uppercase"
                                            >
                                                Target
                                            </div>
                                            <div
                                                class="flex items-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10 p-2"
                                            >
                                                <StButtonGlass
                                                    variant="ghost"
                                                    circle
                                                    size="xs"
                                                    icon="mdi-minus"
                                                    @click="
                                                        formData.required_level =
                                                            Math.max(
                                                                1,
                                                                formData.required_level -
                                                                    1,
                                                            )
                                                    "
                                                />
                                                <span
                                                    class="w-12 text-2xl font-black text-indigo-400 tabular-nums"
                                                    >{{
                                                        formData.required_level
                                                    }}</span
                                                >
                                                <StButtonGlass
                                                    variant="ghost"
                                                    circle
                                                    size="xs"
                                                    icon="mdi-plus"
                                                    @click="
                                                        formData.required_level =
                                                            Math.min(
                                                                5,
                                                                formData.required_level +
                                                                    1,
                                                            )
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-else
                                        class="relative z-10 flex flex-wrap justify-center gap-3"
                                    >
                                        <button
                                            v-for="l in 5"
                                            :key="l"
                                            @click="formData.required_level = l"
                                            class="h-12 w-12 rounded-2xl border text-lg font-black transition-all duration-500"
                                            :class="
                                                formData.required_level === l
                                                    ? 'scale-110 border-indigo-400 bg-indigo-500 text-white shadow-[0_0_25px_rgba(99,102,241,0.4)]'
                                                    : 'border-white/5 bg-white/5 text-white/30 hover:border-white/20 hover:bg-white/10'
                                            "
                                        >
                                            {{ l }}
                                        </button>
                                    </div>

                                    <div class="relative z-10 mt-8 space-y-4">
                                        <v-checkbox
                                            v-model="formData.is_core"
                                            color="indigo-accent-2"
                                            hide-details
                                            class="custom-checkbox"
                                        >
                                            <template #label
                                                ><span
                                                    class="text-xs font-bold text-white/60"
                                                    >Mission Critical
                                                    Capability</span
                                                ></template
                                            >
                                        </v-checkbox>
                                        <v-checkbox
                                            v-if="showReferentOption"
                                            v-model="formData.is_referent"
                                            color="indigo-accent-2"
                                            hide-details
                                            class="custom-checkbox"
                                        >
                                            <template #label
                                                ><span
                                                    class="text-xs font-bold text-white/60"
                                                    >Organizational Referent
                                                    Design</span
                                                ></template
                                            >
                                        </v-checkbox>
                                    </div>
                                </div>
                            </section>

                            <!-- Diagnostic Dashboard -->
                            <section class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <h3
                                        class="text-xs font-black tracking-[0.3em] text-white/30 uppercase"
                                    >
                                        Neural Diagnostic
                                    </h3>
                                    <div class="h-px flex-1 bg-white/5"></div>
                                </div>

                                <div
                                    class="relative h-full overflow-hidden rounded-3xl border p-8 transition-all duration-700"
                                    :class="`${consistencyAlert.bgClass} ${consistencyAlert.borderClass}`"
                                >
                                    <div
                                        class="pointer-events-none absolute -bottom-16 -left-16 h-40 w-40 rounded-full opacity-10 blur-3xl"
                                        :class="consistencyAlert.glowClass"
                                    ></div>

                                    <div
                                        class="relative z-10 flex h-full flex-col justify-between"
                                    >
                                        <div
                                            class="mb-6 flex items-start gap-5"
                                        >
                                            <div
                                                class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/10 bg-white/5 shadow-lg backdrop-blur-xl"
                                                :class="
                                                    consistencyAlert.iconBoxClass
                                                "
                                            >
                                                <v-icon size="28">{{
                                                    consistencyAlert.icon
                                                }}</v-icon>
                                            </div>
                                            <div>
                                                <span
                                                    class="mb-1 block text-[9px] font-black tracking-[0.2em] uppercase opacity-40"
                                                    >Architecture
                                                    Compliance</span
                                                >
                                                <h4
                                                    class="text-sm leading-snug font-black text-white/90"
                                                >
                                                    {{
                                                        consistencyAlert.message
                                                    }}
                                                </h4>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-auto flex items-center justify-between border-t border-white/5 pt-6"
                                        >
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[8px] font-black tracking-[0.2em] text-white/20 uppercase"
                                                    >Design Archetype</span
                                                >
                                                <span
                                                    class="text-sm font-bold text-white/70"
                                                    >{{ archetypeLabel }}</span
                                                >
                                            </div>
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5"
                                            >
                                                <span
                                                    class="text-lg font-black text-white/90"
                                                    >{{
                                                        props.archetype || 'T'
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Rationale Textarea -->
                        <section class="space-y-6">
                            <div class="flex items-center gap-3">
                                <h3
                                    class="text-xs font-black tracking-[0.3em] text-white/30 uppercase"
                                >
                                    Strategic Rationale
                                </h3>
                                <div class="h-px flex-1 bg-white/5"></div>
                            </div>

                            <div class="group relative">
                                <div
                                    class="pointer-events-none absolute -inset-1 bg-indigo-500/5 opacity-0 blur-xl transition-opacity group-hover:opacity-100"
                                ></div>
                                <textarea
                                    v-model="formData.rationale"
                                    rows="4"
                                    placeholder="Briefly justify this design decision for transparency..."
                                    class="relative z-10 w-full rounded-3xl border border-white/5 bg-white/2 p-6 text-sm font-medium text-white/80 transition-all placeholder:text-white/10 focus:border-indigo-500/30 focus:outline-none"
                                ></textarea>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex items-center justify-between border-t border-white/5 bg-[#020617]/60 px-10 py-8"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"
                    ></div>
                    <span
                        class="text-[10px] font-black tracking-widest text-white/20 uppercase"
                        >Core Integrity Validated</span
                    >
                </div>
                <div class="flex items-center gap-4">
                    <StButtonGlass variant="ghost" @click="$emit('close')"
                        >Cancel</StButtonGlass
                    >
                    <div class="mx-2 h-8 w-px bg-white/5"></div>
                    <StButtonGlass
                        variant="primary"
                        @click="handleSave"
                        :loading="saving"
                        class="px-10!"
                    >
                        Commit Blueprint
                    </StButtonGlass>
                </div>
            </div>
        </StCardGlass>

        <!-- Tooltip Explanations -->
        <InfoLegend
            v-model="showLegend"
            title="Strategic Association Methodology"
            :items="legendItems"
            icon="mdi-book-open-variant"
            width="650"
        />
    </v-dialog>

    <EngineeringBlueprintSheet
        v-if="showTransform"
        :competency-id="props.competencyId"
        :competency-name="props.competencyName"
        :role-name="props.roleName"
        :archetype="props.archetype || 'T'"
        :required-level="formData.required_level"
        :initial-bars="props.mapping?.metadata?.bars"
        @transformed="handleTransformed"
        @close="showTransform = false"
    />
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

.custom-checkbox :deep(.v-label) {
    padding-inline-start: 12px;
}
</style>
