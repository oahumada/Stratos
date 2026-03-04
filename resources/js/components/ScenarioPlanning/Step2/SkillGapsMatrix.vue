<template>
    <div class="skill-gaps-matrix-container relative min-h-[500px]">
        <!-- Background Elements -->
        <div
            class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 bg-indigo-500/10 blur-[120px]"
        ></div>
        <div
            class="pointer-events-none absolute -bottom-24 -left-24 h-96 w-96 bg-emerald-500/10 blur-[120px]"
        ></div>

        <!-- Header -->
        <div
            class="relative z-10 mb-8 flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
        >
            <div>
                <h3 class="mb-1 text-2xl font-black tracking-tight text-white">
                    Skill Gaps <span class="text-indigo-400">Heatmap</span>
                </h3>
                <p class="text-sm font-medium text-white/40">
                    Visual differential analysis between current inventory and
                    strategic requirements
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div
                    class="flex items-center gap-2 rounded-2xl border border-white/5 bg-white/5 p-1 px-3"
                >
                    <v-icon size="16" class="text-white/20">mdi-magnify</v-icon>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search skill..."
                        class="bg-transparent text-sm font-bold text-white outline-none placeholder:text-white/20"
                    />
                </div>
                <StButtonGlass
                    variant="ghost"
                    circle
                    icon="mdi-information-outline"
                    @click="showLegendDialog = true"
                />
            </div>
        </div>

        <!-- Alerts -->
        <transition name="fade">
            <div
                v-if="error"
                class="mb-6 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4"
            >
                <div class="flex items-center gap-3">
                    <v-icon color="rose-400" size="20">mdi-alert-circle</v-icon>
                    <span class="text-sm font-bold text-rose-200">{{
                        error
                    }}</span>
                </div>
            </div>
        </transition>

        <!-- Main Heatmap -->
        <div
            v-if="loading"
            class="flex flex-col items-center justify-center py-24"
        >
            <v-progress-circular
                indeterminate
                color="indigo-400"
                size="64"
                width="3"
            />
            <span
                class="mt-4 text-xs font-black tracking-widest text-indigo-400/60 uppercase"
                >Mapping Talent Topology...</span
            >
        </div>

        <div
            v-else
            class="relative overflow-hidden rounded-3xl border border-white/10 bg-black/20 backdrop-blur-md"
        >
            <div class="custom-scrollbar overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-white/5">
                            <th
                                class="sticky left-0 z-20 border-r border-b border-white/5 bg-black/60 px-6 py-4 text-left backdrop-blur-xl"
                            >
                                <span
                                    class="text-[10px] font-black tracking-widest text-white/30 uppercase"
                                    >Skill Architecture</span
                                >
                            </th>
                            <th
                                v-for="role in roles"
                                :key="role.id"
                                class="min-w-[140px] border-b border-white/5 px-4 py-4 text-center"
                            >
                                <div
                                    class="mb-1 text-sm leading-tight font-black text-white"
                                >
                                    {{ role.name }}
                                </div>
                                <StBadgeGlass variant="glass" size="sm"
                                    >{{ role.fte }} FTE</StBadgeGlass
                                >
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <tr
                            v-for="skill in filteredSkills"
                            :key="skill.id"
                            class="group hover:bg-white-[0.02] transition-colors"
                        >
                            <!-- Skill Name Header (Sticky) -->
                            <td
                                class="sticky left-0 z-10 border-r border-white/5 bg-black/60 px-6 py-4 backdrop-blur-xl"
                            >
                                <div
                                    class="font-black text-white transition-colors group-hover:text-indigo-300"
                                >
                                    {{ skill.name }}
                                </div>
                                <div
                                    class="mt-0.5 text-[9px] font-bold tracking-widest text-white/20 uppercase"
                                >
                                    {{ skill.competency_name }}
                                </div>
                            </td>

                            <!-- Gap Cells -->
                            <td
                                v-for="role in roles"
                                :key="`${skill.id}-${role.id}`"
                                class="p-4 text-center"
                            >
                                <div class="flex flex-col items-center gap-2">
                                    <!-- Cell with Dynamic Glow -->
                                    <button
                                        class="relative flex h-14 w-14 items-center justify-center rounded-xl border transition-all duration-300 hover:scale-105 active:scale-95"
                                        :style="getGapStyle(skill.id, role.id)"
                                        @click="showGapDetail(skill, role)"
                                    >
                                        <div
                                            class="absolute inset-0 rounded-xl opacity-40 blur-lg transition-transform group-hover:scale-110"
                                            :style="{
                                                backgroundColor: getGapColor(
                                                    skill.id,
                                                    role.id,
                                                ),
                                            }"
                                        ></div>
                                        <span
                                            class="relative text-sm font-black text-white"
                                        >
                                            {{ getGapValue(skill.id, role.id) }}
                                        </span>
                                    </button>

                                    <!-- Delta Indicator -->
                                    <div
                                        class="text-[10px] font-black tracking-tighter text-white/30 transition-colors group-hover:text-white/60"
                                    >
                                        L{{
                                            getCurrentLevel(skill.id, role.id)
                                        }}
                                        <v-icon size="8" class="mx-0.5"
                                            >mdi-arrow-right</v-icon
                                        >
                                        L{{
                                            getRequiredLevel(skill.id, role.id)
                                        }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Compact Legend -->
        <div
            class="relative z-10 mt-8 flex flex-wrap items-center gap-8 rounded-3xl border border-white/5 bg-white/5 px-8 py-6"
        >
            <span
                class="text-[10px] font-black tracking-[0.2em] text-white/20 uppercase italic"
                >Anomaly Index:</span
            >
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div
                        class="h-3 w-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"
                    ></div>
                    <span class="text-[10px] font-bold text-white/60 uppercase"
                        >Aligned (0)</span
                    >
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="h-3 w-3 rounded-full bg-amber-400 shadow-[0_0_8px_rgba(251,191,36,0.5)]"
                    ></div>
                    <span class="text-[10px] font-bold text-white/60 uppercase"
                        >Mild (-1)</span
                    >
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="h-3 w-3 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.5)]"
                    ></div>
                    <span class="text-[10px] font-bold text-white/60 uppercase"
                        >Medium (-2)</span
                    >
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="h-3 w-3 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]"
                    ></div>
                    <span class="text-[10px] font-bold text-white/60 uppercase"
                        >Critical (-3+)</span
                    >
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <v-dialog
            v-model="showDetailDialog"
            max-width="500px"
            class="backdrop-blur-sm"
        >
            <StCardGlass
                v-if="selectedGap"
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
                                >mdi-crosshairs-gps</v-icon
                            >
                        </div>
                        <div>
                            <h2
                                class="mb-1 text-xl leading-none font-black text-white"
                            >
                                Gap Analytics
                            </h2>
                            <p
                                class="text-xs font-bold tracking-widest text-white/40 uppercase"
                            >
                                {{ selectedGap.role_name }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8 p-8">
                    <div class="text-center">
                        <h3
                            class="text-2xl leading-tight font-black tracking-tight text-white"
                        >
                            {{ selectedGap.skill_name }}
                        </h3>
                        <StBadgeGlass
                            variant="glass"
                            size="sm"
                            class="mt-2 tracking-widest text-indigo-400 uppercase"
                            >{{ selectedGap.competency_name }}</StBadgeGlass
                        >
                    </div>

                    <div
                        class="flex items-center justify-around rounded-3xl border border-white/5 bg-black/40 p-8 shadow-2xl"
                    >
                        <div class="flex flex-col items-center gap-2">
                            <span
                                class="text-[9px] font-black tracking-widest text-white/30 uppercase"
                                >Current Level</span
                            >
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-black text-white"
                                    >L{{ selectedGap.current_level }}</span
                                >
                                <span class="text-xs font-bold text-white/20"
                                    >/ 5</span
                                >
                            </div>
                        </div>
                        <v-icon class="animate-pulse text-white/10"
                            >mdi-arrow-right-bold-outline</v-icon
                        >
                        <div class="flex flex-col items-center gap-2">
                            <span
                                class="text-[9px] font-black tracking-widest text-white/30 uppercase"
                                >Required Level</span
                            >
                            <div class="flex items-baseline gap-1">
                                <span
                                    class="text-5xl font-black text-indigo-400"
                                    >L{{ selectedGap.required_level }}</span
                                >
                                <span class="text-xs font-bold text-white/20"
                                    >/ 5</span
                                >
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="selectedGap.gap > 0"
                        class="flex items-start gap-4 rounded-3xl border border-rose-500/20 bg-rose-500/10 p-6"
                    >
                        <v-icon color="rose-400">mdi-alert-decagram</v-icon>
                        <div>
                            <h4 class="text-sm font-black text-rose-200">
                                Deficiency Detected
                            </h4>
                            <p
                                class="mt-1 text-xs font-medium text-rose-200/60"
                            >
                                This talent vector requires
                                {{ selectedGap.gap }} mastery levels to achieve
                                strategic alignment for the current role
                                definition.
                            </p>
                        </div>
                    </div>

                    <div v-if="selectedGap.learning_path" class="space-y-4">
                        <h4
                            class="text-[10px] font-black tracking-widest text-indigo-400 uppercase"
                        >
                            Acceleration Protocol
                        </h4>
                        <div
                            class="rounded-2xl border border-white/10 bg-white/5 p-6 text-sm leading-relaxed text-white/70 italic"
                        >
                            "{{ selectedGap.learning_path }}"
                        </div>
                    </div>
                </div>

                <div
                    class="flex justify-end gap-3 border-t border-white/5 bg-black/40 p-6"
                >
                    <StButtonGlass
                        variant="ghost"
                        @click="showDetailDialog = false"
                        >Dismiss</StButtonGlass
                    >
                    <StButtonGlass
                        variant="secondary"
                        icon="mdi-head-lightbulb-outline"
                        @click="suggestLearning"
                        >Generate Plan</StButtonGlass
                    >
                </div>
            </StCardGlass>
        </v-dialog>

        <!-- Legend Sidebar or Dialog -->
        <v-dialog v-model="showLegendDialog" max-width="600px">
            <StCardGlass variant="media">
                <div
                    class="flex items-center gap-3 border-b border-white/5 p-6"
                >
                    <v-icon color="indigo-400">mdi-map-legend</v-icon>
                    <h2
                        class="text-xl font-black tracking-tighter text-white uppercase"
                    >
                        Heatmap Logic
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div
                            v-for="item in legendItems"
                            :key="item.title"
                            class="space-y-1"
                        >
                            <h4
                                class="text-xs font-black tracking-widest text-indigo-300 uppercase"
                            >
                                {{ item.title }}
                            </h4>
                            <p
                                class="text-[11px] leading-tight font-medium text-white/40"
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
                        >Close</StButtonGlass
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
import { computed, onMounted, ref } from 'vue';

interface Role {
    id: number;
    name: string;
    fte: number;
}

interface Skill {
    id: number;
    name: string;
    competency_name: string;
    competency_id: number;
}

interface SkillGap {
    skill_id: number;
    role_id: number;
    skill_name: string;
    role_name: string;
    competency_name: string;
    current_level: number;
    required_level: number;
    gap: number;
    learning_path?: string;
    timeline_months?: number;
}

interface GapMatrix {
    [skillId: number]: {
        [roleId: number]: SkillGap;
    };
}

interface Props {
    scenarioId: number;
}

const props = defineProps<Props>();

const loading = ref(true);
const error = ref<string | null>(null);

const showLegendDialog = ref(false);
const legendItems = [
    {
        title: 'Gap Differental',
        description:
            'Arithmetic difference between target proficiency and current mastery (required - current).',
    },
    {
        title: 'Talent Level (L)',
        description:
            'Normalized mastery scale from 1 (Novice) to 5 (Domain Expert).',
    },
    {
        title: 'Heatmap Cells',
        description:
            'Interactive nodes containing discrete gap values and trend vectors.',
    },
    {
        title: 'Architecture Node',
        description:
            'Specific skills mapped to organizational core competencies.',
    },
    {
        title: 'Full-Time Equiv (FTE)',
        description:
            'Total human/synthetic bandwidth required for the specific role node.',
    },
    {
        title: 'Acceleration Protocol',
        description: 'AI-generated pathway to mitigate proficiency deficits.',
    },
];

const roles = ref<Role[]>([]);
const skills = ref<Skill[]>([]);
const gapMatrix = ref<GapMatrix>({});

const searchQuery = ref('');

const showDetailDialog = ref(false);
const selectedGap = ref<SkillGap | null>(null);

const filteredSkills = computed(() => {
    return skills.value.filter((skill) => {
        const matchesSearch = skill.name
            .toLowerCase()
            .includes(searchQuery.value.toLowerCase());
        return matchesSearch;
    });
});

const api = useApi();

const loadData = async () => {
    if (!props.scenarioId) return;

    try {
        loading.value = true;
        const response: any = await api.get(
            `/api/scenarios/${props.scenarioId}/step2/skill-gaps-matrix`,
        );

        const data = response.data || response;
        roles.value = data.roles || [];
        skills.value = data.skills || [];

        const matrix: any = {};
        if (data.gaps) {
            for (const gap of data.gaps) {
                if (!matrix[gap.skill_id]) matrix[gap.skill_id] = {};
                matrix[gap.skill_id][gap.role_id] = gap;
            }
        }
        gapMatrix.value = matrix;
    } catch (err: any) {
        error.value =
            err.response?.data?.message ||
            'Spectral synthesis of gap matrix failed';
    } finally {
        loading.value = false;
    }
};

const getGapColor = (skillId: number, roleId: number): string => {
    const gap = gapMatrix.value[skillId]?.[roleId];
    if (!gap) return 'rgba(255, 255, 255, 0.05)';

    const gapSize = gap.required_level - gap.current_level;
    if (gapSize <= 0) return '#10b981'; // emerald-500
    if (gapSize === 1) return '#fbbf24'; // amber-400
    if (gapSize === 2) return '#f97316'; // orange-500
    return '#f43f5e'; // rose-500
};

const getGapStyle = (skillId: number, roleId: number) => {
    const color = getGapColor(skillId, roleId);
    const isEmpty = !gapMatrix.value[skillId]?.[roleId];

    return {
        backgroundColor: `${color}${isEmpty ? '' : '15'}`,
        borderColor: `${color}${isEmpty ? '10' : '40'}`,
    };
};

const getGapValue = (skillId: number, roleId: number): string => {
    const gap = gapMatrix.value[skillId]?.[roleId];
    if (!gap) return '-';
    const gapSize = gap.required_level - gap.current_level;
    return gapSize <= 0 ? '✓' : `-${gapSize}`;
};

const getCurrentLevel = (skillId: number, roleId: number): number => {
    const gap = gapMatrix.value[skillId]?.[roleId];
    return gap?.current_level ?? 0;
};

const getRequiredLevel = (skillId: number, roleId: number): number => {
    const gap = gapMatrix.value[skillId]?.[roleId];
    return gap?.required_level ?? 0;
};

const showGapDetail = (skill: any, role: any) => {
    const gap = gapMatrix.value[skill.id]?.[role.id];
    if (gap) {
        selectedGap.value = {
            ...gap,
            skill_name: skill.name,
            role_name: role.name,
            competency_name: skill.competency_name,
        };
        showDetailDialog.value = true;
    }
};

const suggestLearning = () => {
    console.log(
        'Synthesizing learning path for:',
        selectedGap.value?.skill_name,
    );
};

onMounted(() => {
    loadData();
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.02);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.4);
}
</style>
