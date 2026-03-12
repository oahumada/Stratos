<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowLeft,
    PhCheck,
    PhMagicWand,
    PhPlus,
    PhRobot,
    PhSealCheck,
    PhTrash,
    PhX,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    visible: boolean;
    scenarioId?: number | null;
}>();

const { t } = useI18n();

const emit = defineEmits(['close', 'created']);

const internalVisible = ref(props.visible);
const currentStep = ref(1);
const analyzing = ref(false);
const saving = ref(false);

const steps = computed(() => [
    { title: t('role_wizard.step1_title'), desc: t('role_wizard.step1_desc') },
    { title: t('role_wizard.step2_title'), desc: t('role_wizard.step2_desc') },
    { title: t('role_wizard.step3_title'), desc: t('role_wizard.step3_desc') },
]);

const form = ref({
    name: '',
    description: '',
    cube: {
        x_archetype: 'Tactical' as 'Strategic' | 'Tactical' | 'Operational',
        y_mastery_level: 3,
        z_business_process: '',
        justification: '',
    },
    purpose: '',
    expected_results: '',
    competencies: [] as any[],
    suggestions: '',
});

watch(
    () => props.visible,
    (val) => {
        internalVisible.value = val;
        if (val) currentStep.value = 1;
    },
);

const close = () => {
    internalVisible.value = false;
    emit('close');
};

const analyzeRole = async () => {
    analyzing.value = true;
    try {
        const response = await axios.post(
            '/api/strategic-planning/roles/analyze-preview',
            {
                name: form.value.name,
                description: form.value.description,
            },
        );

        const data = response.data.analysis;
        form.value.cube.x_archetype = data.cube_coordinates.x_archetype;
        form.value.cube.y_mastery_level = data.cube_coordinates.y_mastery_level;
        form.value.cube.z_business_process =
            data.cube_coordinates.z_business_process;
        form.value.cube.justification = data.cube_coordinates.justification;
        form.value.competencies = data.core_competencies;
        form.value.suggestions = data.organizational_suggestions;

        currentStep.value = 2;
    } catch (error) {
        console.error('Neural analysis error:', error);
    } finally {
        analyzing.value = false;
    }
};

const removeSkill = (index: number) => {
    form.value.competencies.splice(index, 1);
};

const addSkill = () => {
    form.value.competencies.push({
        name: t('role_wizard.new_capacity'),
        level: 3,
        rationale: t('role_wizard.manual_rationale'),
    });
};

const saveRole = async () => {
    saving.value = true;
    try {
        const payload = {
            name: form.value.name,
            role_name: form.value.name,
            role_description: form.value.description,
            description: form.value.description,
            cube_dimensions: form.value.cube,
            competencies: form.value.competencies,
            ai_archetype_config: {
                cube_coordinates: form.value.cube,
                core_competencies: form.value.competencies,
                organizational_suggestions: form.value.suggestions,
            },
            fte: 1,
            role_change: 'create',
            evolution_type: 'new_role',
            impact_level: 'medium',
        };

        const url = props.scenarioId
            ? `/api/scenarios/${props.scenarioId}/step2/roles`
            : '/api/roles';

        await axios.post(url, payload);

        emit('created');
        close();
    } catch (error) {
        console.error('Saving sequence failed:', error);
    } finally {
        saving.value = false;
    }
};
</script>

<template>
    <v-dialog
        v-model="internalVisible"
        fullscreen
        transition="dialog-bottom-transition"
        persistent
    >
        <div
            class="st-glass-container relative flex h-full w-full flex-col overflow-hidden bg-[#020617] selection:bg-indigo-500/30"
        >
            <!-- Background Glows -->
            <div class="pointer-events-none fixed inset-0">
                <div
                    class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-indigo-500/5 blur-[120px]"
                ></div>
                <div
                    class="absolute top-[20%] -right-[10%] h-[35%] w-[35%] rounded-full bg-purple-500/5 blur-[120px]"
                ></div>
            </div>

            <!-- Header -->
            <header
                class="z-10 flex items-center border-b border-white/5 bg-white/2 px-8 py-4 backdrop-blur-xl"
            >
                <StButtonGlass
                    variant="ghost"
                    circle
                    :icon="PhX"
                    @click="close"
                />
                <div class="ml-6">
                    <h1
                        class="flex items-center gap-3 text-xl font-black tracking-tight text-white"
                    >
                        {{ $t('role_wizard.title') }}
                        <StBadgeGlass
                            variant="glass"
                            size="sm"
                            class="border border-white/10! px-2! text-[9px] tracking-widest text-white/40 uppercase"
                            >{{ $t('role_wizard.ai_powered') }}</StBadgeGlass
                        >
                    </h1>
                </div>
                <v-spacer></v-spacer>
                <div class="flex items-center gap-4">
                    <div class="hidden text-right sm:block">
                        <div
                            class="text-[9px] font-black tracking-[0.2em] text-white/20 uppercase"
                        >
                            {{ $t('role_wizard.core_orchestrator') }}
                        </div>
                        <div class="text-xs font-bold text-indigo-400">
                            {{ $t('role_wizard.cerbero_engine') }}
                        </div>
                    </div>
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_15px_rgba(99,102,241,0.2)]"
                    >
                        <PhRobot color="white" :size="20" weight="duotone" />
                    </div>
                </div>
            </header>

            <div
                class="relative z-10 flex grow flex-col overflow-hidden md:flex-row"
            >
                <!-- Navigation -->
                <aside
                    class="pa-8 flex w-full flex-col border-r border-white/5 bg-white/2 md:w-80"
                >
                    <nav class="space-y-4">
                        <div
                            v-for="(step, i) in steps"
                            :key="i"
                            class="flex items-start gap-4 rounded-2xl p-4 transition-all duration-500"
                            :class="[
                                currentStep === i + 1
                                    ? 'border border-indigo-500/20 bg-indigo-500/10 shadow-lg'
                                    : 'pointer-events-none opacity-40 grayscale',
                            ]"
                        >
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-lg text-sm font-black"
                                :class="
                                    currentStep === i + 1
                                        ? 'shadow-glow bg-indigo-500 text-white'
                                        : 'bg-white/5 text-white/50'
                                "
                            >
                                {{ i + 1 }}
                            </div>
                            <div>
                                <div
                                    class="mb-1 text-sm leading-tight font-black text-white"
                                >
                                    {{ step.title }}
                                </div>
                                <div
                                    class="text-[10px] font-medium tracking-widest text-white/40 uppercase"
                                >
                                    {{ step.desc }}
                                </div>
                            </div>
                        </div>
                    </nav>

                    <v-spacer></v-spacer>

                    <StCardGlass
                        variant="glass"
                        class="border-white/5 bg-white/2! p-5!"
                    >
                        <div
                            class="mb-2 text-[9px] font-black tracking-[0.2em] text-indigo-400 uppercase"
                        >
                            {{ $t('role_wizard.protocol_note') }}
                        </div>
                        <p
                            class="text-[11px] leading-relaxed text-white/40 italic"
                        >
                            {{ $t('role_wizard.protocol_desc') }}
                        </p>
                    </StCardGlass>
                </aside>

                <!-- Step Content -->
                <main
                    class="custom-scrollbar flex-1 overflow-y-auto p-12 lg:p-20"
                >
                    <transition name="fade-slide" mode="out-in">
                        <!-- Step 1: Definition -->
                        <div
                            v-if="currentStep === 1"
                            :key="1"
                            class="mx-auto max-w-3xl space-y-12"
                        >
                            <div class="space-y-4">
                                <h2
                                    class="text-4xl leading-tight font-black tracking-tight text-white"
                                >
                                    {{ $t('role_wizard.define_node') }}
                                </h2>
                                <p
                                    class="max-w-2xl text-lg leading-relaxed font-medium text-white/50"
                                >
                                    {{ $t('role_wizard.define_node_desc') }}
                                </p>
                            </div>

                            <div class="space-y-8">
                                <div class="space-y-2">
                                    <label
                                        class="ml-1 text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                        >{{
                                            $t(
                                                'role_wizard.architectural_label',
                                            )
                                        }}</label
                                    >
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        :placeholder="
                                            $t(
                                                'role_wizard.architectural_placeholder',
                                            )
                                        "
                                        class="w-full rounded-2xl border border-white/10 bg-white/5 px-6 py-5 text-lg text-white placeholder-white/10 transition-all focus:border-indigo-500/50 focus:bg-white/[0.07] focus:outline-none"
                                    />
                                </div>

                                <div
                                    class="grid grid-cols-1 gap-8 md:grid-cols-2"
                                >
                                    <div class="space-y-2">
                                        <label
                                            class="ml-1 text-[10px] font-black tracking-[0.2em] text-pink-400 uppercase"
                                            >[ CIMIENTOS ] Propósito del
                                            Rol</label
                                        >
                                        <textarea
                                            v-model="form.purpose"
                                            rows="4"
                                            placeholder="¿Para qué existe este edificio/rol en la organización?"
                                            class="w-full resize-none rounded-2xl border border-pink-500/10 bg-pink-500/5 px-6 py-4 text-base text-white placeholder-white/10 transition-all focus:border-pink-500/50 focus:outline-none"
                                        ></textarea>
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="ml-1 text-[10px] font-black tracking-[0.2em] text-emerald-400 uppercase"
                                            >[ FUNCIÓN ] Resultados
                                            Esperados</label
                                        >
                                        <textarea
                                            v-model="form.expected_results"
                                            rows="4"
                                            placeholder="¿Qué impactos concretos genera este rol?"
                                            class="w-full resize-none rounded-2xl border border-emerald-500/10 bg-emerald-500/5 px-6 py-4 text-base text-white placeholder-white/10 transition-all focus:border-emerald-500/50 focus:outline-none"
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="ml-1 text-[10px] font-black tracking-[0.2em] text-white/30 uppercase"
                                        >Síntesis de Misión (Opcional)</label
                                    >
                                    <textarea
                                        v-model="form.description"
                                        rows="3"
                                        :placeholder="
                                            $t(
                                                'role_wizard.mission_placeholder',
                                            )
                                        "
                                        class="w-full resize-none rounded-2xl border border-white/10 bg-white/5 px-6 py-4 text-base text-white/60 placeholder-white/10 transition-all focus:border-indigo-500/50 focus:outline-none"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end pt-8">
                                <StButtonGlass
                                    variant="primary"
                                    size="lg"
                                    :disabled="
                                        !form.name ||
                                        !form.purpose ||
                                        !form.expected_results
                                    "
                                    :loading="analyzing"
                                    @click="analyzeRole"
                                    :icon="PhMagicWand"
                                    class="px-12!"
                                >
                                    {{ $t('role_wizard.initiate_synthesis') }}
                                </StButtonGlass>
                            </div>
                        </div>

                        <!-- Step 2: Cube Mapping -->
                        <div
                            v-else-if="currentStep === 2"
                            :key="2"
                            class="mx-auto max-w-5xl space-y-12"
                        >
                            <div class="flex items-end justify-between">
                                <div class="space-y-4">
                                    <h2
                                        class="text-4xl font-black tracking-tight text-white"
                                    >
                                        {{ $t('role_wizard.cube_dimensions') }}
                                    </h2>
                                    <p
                                        class="text-base font-medium text-white/50"
                                    >
                                        {{ $t('role_wizard.cube_desc') }}
                                    </p>
                                </div>
                                <div
                                    class="flex items-center gap-2 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-2"
                                >
                                    <Robot
                                        color="#34d399"
                                        :size="14"
                                        weight="duotone"
                                    />
                                    <span
                                        class="text-[10px] font-black tracking-widest text-emerald-400 uppercase"
                                        >{{
                                            $t('role_wizard.analysis_complete')
                                        }}</span
                                    >
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-1 gap-12 lg:grid-cols-12"
                            >
                                <div class="space-y-10 lg:col-span-7">
                                    <!-- Axis X -->
                                    <div class="space-y-6">
                                        <label
                                            class="ml-1 text-[10px] font-black tracking-[0.3em] text-white/30 uppercase"
                                            >{{
                                                $t('role_wizard.axis_x')
                                            }}</label
                                        >
                                        <div class="grid grid-cols-3 gap-4">
                                            <button
                                                v-for="arc in [
                                                    'Strategic',
                                                    'Tactical',
                                                    'Operational',
                                                ]"
                                                :key="arc"
                                                @click="
                                                    form.cube.x_archetype =
                                                        arc as any
                                                "
                                                class="flex flex-col items-center gap-4 rounded-2xl border p-6 transition-all duration-500"
                                                :class="
                                                    form.cube.x_archetype ===
                                                    arc
                                                        ? 'shadow-glow border-indigo-500/50 bg-indigo-500/20'
                                                        : 'border-white/5 bg-white/2 hover:bg-white/5'
                                                "
                                            >
                                                <component
                                                    :is="
                                                        arc === 'Strategic'
                                                            ? PhCrown
                                                            : arc === 'Tactical'
                                                              ? PhCrosshair
                                                              : PhNavigationArrow
                                                    "
                                                    :size="32"
                                                    :weight="
                                                        form.cube
                                                            .x_archetype === arc
                                                            ? 'duotone'
                                                            : 'regular'
                                                    "
                                                    :class="
                                                        form.cube
                                                            .x_archetype === arc
                                                            ? 'text-indigo-300'
                                                            : 'text-white/20'
                                                    "
                                                />
                                                <div class="text-center">
                                                    <div
                                                        class="mb-1 text-sm font-black text-white"
                                                    >
                                                        {{
                                                            arc === 'Strategic'
                                                                ? $t(
                                                                      'role_wizard.axis_x_strategic',
                                                                  )
                                                                : arc ===
                                                                    'Tactical'
                                                                  ? $t(
                                                                        'role_wizard.axis_x_tactical',
                                                                    )
                                                                  : $t(
                                                                        'role_wizard.axis_x_operational',
                                                                    )
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-[9px] font-bold text-white/30 uppercase"
                                                    >
                                                        {{
                                                            arc === 'Strategic'
                                                                ? $t(
                                                                      'role_wizard.horizon_3',
                                                                  )
                                                                : arc ===
                                                                    'Tactical'
                                                                  ? $t(
                                                                        'role_wizard.horizon_2',
                                                                    )
                                                                  : $t(
                                                                        'role_wizard.horizon_1',
                                                                    )
                                                        }}
                                                    </div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Axis Y -->
                                    <div class="space-y-6">
                                        <div
                                            class="mb-2 flex items-center justify-between"
                                        >
                                            <label
                                                class="ml-1 text-[10px] font-black tracking-[0.3em] text-white/30 uppercase"
                                                >{{
                                                    $t('role_wizard.axis_y')
                                                }}</label
                                            >
                                            <span
                                                class="text-2xl font-black text-indigo-400"
                                                >{{
                                                    $t('role_wizard.tier', {
                                                        level: form.cube
                                                            .y_mastery_level,
                                                    })
                                                }}</span
                                            >
                                        </div>
                                        <div class="px-4">
                                            <v-slider
                                                v-model="
                                                    form.cube.y_mastery_level
                                                "
                                                :min="1"
                                                :max="5"
                                                :step="1"
                                                show-ticks="always"
                                                thumb-label="always"
                                                color="indigo-500"
                                                track-color="white/10"
                                            />
                                        </div>
                                    </div>

                                    <!-- Axis Z -->
                                    <div class="space-y-4">
                                        <label
                                            class="ml-1 text-[10px] font-black tracking-[0.3em] text-white/30 uppercase"
                                            >{{
                                                $t('role_wizard.axis_z')
                                            }}</label
                                        >
                                        <input
                                            v-model="
                                                form.cube.z_business_process
                                            "
                                            type="text"
                                            :placeholder="
                                                $t(
                                                    'role_wizard.anchor_placeholder',
                                                )
                                            "
                                            class="w-full rounded-2xl border border-white/10 bg-white/2 px-6 py-5 text-base text-white placeholder-white/10 transition-all focus:border-indigo-500/50 focus:outline-none"
                                        />
                                    </div>
                                </div>

                                <div class="space-y-6 lg:col-span-5">
                                    <StCardGlass
                                        variant="glass"
                                        class="border-indigo-400/20 bg-indigo-500/5! p-8!"
                                    >
                                        <div
                                            class="mb-6 flex items-center gap-3"
                                        >
                                            <Brain
                                                color="#818cf8"
                                                :size="18"
                                                weight="duotone"
                                            />
                                            <h4
                                                class="text-[10px] font-black tracking-[0.2em] text-indigo-400 uppercase"
                                            >
                                                {{
                                                    $t(
                                                        'role_wizard.synthesis_rationale',
                                                    )
                                                }}
                                            </h4>
                                        </div>
                                        <p
                                            class="mb-8 text-base leading-relaxed text-white/70 italic"
                                        >
                                            "{{ form.cube.justification }}"
                                        </p>

                                        <v-divider
                                            class="mb-8 border-white/5"
                                        ></v-divider>

                                        <div
                                            class="mb-6 flex items-center gap-3"
                                        >
                                            <PhMagicWand
                                                color="#34d399"
                                                :size="18"
                                                weight="duotone"
                                            />
                                            <h4
                                                class="text-[10px] font-black tracking-[0.2em] text-emerald-400 uppercase"
                                            >
                                                {{
                                                    $t(
                                                        'role_wizard.optimization_tip',
                                                    )
                                                }}
                                            </h4>
                                        </div>
                                        <div
                                            class="rounded-xl border border-emerald-500/10 bg-emerald-500/5 p-5 text-xs leading-relaxed font-medium text-emerald-100/60"
                                        >
                                            {{ form.suggestions }}
                                        </div>
                                    </StCardGlass>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4 pt-8">
                                <StButtonGlass
                                    variant="ghost"
                                    :icon="PhArrowLeft"
                                    @click="currentStep--"
                                    >{{
                                        $t('role_wizard.back_stage')
                                    }}</StButtonGlass
                                >
                                <StButtonGlass
                                    variant="primary"
                                    :icon="PhCheck"
                                    @click="currentStep++"
                                    class="px-12!"
                                    >{{
                                        $t('role_wizard.confirm_arch')
                                    }}</StButtonGlass
                                >
                            </div>
                        </div>

                        <!-- Step 3: Blueprint -->
                        <div
                            v-else-if="currentStep === 3"
                            :key="3"
                            class="mx-auto max-w-5xl space-y-12"
                        >
                            <div class="space-y-4">
                                <h2
                                    class="text-4xl font-black tracking-tight text-white"
                                >
                                    {{ $t('role_wizard.capacity_blueprint') }}
                                </h2>
                                <p class="text-base font-medium text-white/50">
                                    {{ $t('role_wizard.capacity_desc') }}
                                </p>
                            </div>

                            <StCardGlass
                                variant="glass"
                                class="overflow-hidden border-white/10 p-0!"
                            >
                                <v-table class="st-glass-table w-full">
                                    <thead>
                                        <tr class="bg-white/5">
                                            <th
                                                class="px-8 py-6 text-[10px]! font-black! tracking-widest! text-white/30! uppercase"
                                            >
                                                {{
                                                    $t(
                                                        'role_wizard.strategic_capacity',
                                                    )
                                                }}
                                            </th>
                                            <th
                                                class="px-4 py-6 text-center text-[10px]! font-black! tracking-widest! text-white/30! uppercase"
                                            >
                                                {{
                                                    $t(
                                                        'role_wizard.mastery_req',
                                                    )
                                                }}
                                            </th>
                                            <th
                                                class="px-4 py-6 text-[10px]! font-black! tracking-widest! text-white/30! uppercase"
                                            >
                                                {{
                                                    $t(
                                                        'role_wizard.ai_rationale',
                                                    )
                                                }}
                                            </th>
                                            <th
                                                class="px-8 py-6 text-right text-[10px]! font-black! tracking-widest! text-white/30! uppercase"
                                            >
                                                {{ $t('role_wizard.ops') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        <tr
                                            v-for="(
                                                skill, idx
                                            ) in form.competencies"
                                            :key="idx"
                                            class="transition-colors hover:bg-white/5"
                                        >
                                            <td class="px-8 py-6">
                                                <input
                                                    v-model="skill.name"
                                                    class="w-full border-none bg-transparent font-bold text-white transition-colors focus:text-indigo-300 focus:outline-none"
                                                />
                                            </td>
                                            <td class="px-4 py-6">
                                                <div
                                                    class="flex justify-center"
                                                >
                                                    <v-rating
                                                        v-model="skill.level"
                                                        density="compact"
                                                        color="amber-lighten-2"
                                                        active-color="amber-lighten-2"
                                                        size="small"
                                                    />
                                                </div>
                                            </td>
                                            <td
                                                class="max-w-xs truncate px-4 py-6 text-[11px] font-medium text-white/40 italic"
                                            >
                                                {{ skill.rationale }}
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <StButtonGlass
                                                    variant="ghost"
                                                    circle
                                                    size="sm"
                                                    :icon="PhTrash"
                                                    class="text-rose-500/40! hover:text-rose-500!"
                                                    @click="removeSkill(idx)"
                                                />
                                            </td>
                                        </tr>
                                    </tbody>
                                </v-table>
                                <div
                                    class="border-t border-white/10 bg-white/2 p-6"
                                >
                                    <StButtonGlass
                                        variant="ghost"
                                        size="sm"
                                        :icon="PhPlus"
                                        @click="addSkill"
                                        >{{
                                            $t('role_wizard.add_manual')
                                        }}</StButtonGlass
                                    >
                                </div>
                            </StCardGlass>

                            <div class="flex justify-end gap-4 pt-12">
                                <StButtonGlass
                                    variant="ghost"
                                    :icon="PhArrowLeft"
                                    @click="currentStep--"
                                    >{{
                                        $t('role_wizard.adjustment_phase')
                                    }}</StButtonGlass
                                >
                                <StButtonGlass
                                    variant="secondary"
                                    :loading="saving"
                                    :icon="PhSealCheck"
                                    @click="saveRole"
                                    class="px-12!"
                                    >{{
                                        $t('role_wizard.deploy_arch')
                                    }}</StButtonGlass
                                >
                            </div>
                        </div>
                    </transition>
                </main>
            </div>
        </div>
    </v-dialog>
</template>

<style scoped>
.st-glass-container {
    background: radial-gradient(circle at top left, #0f172a 0%, #020617 100%);
}

.shadow-glow {
    box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(20px) scale(0.98);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-20px) scale(1.02);
}

.st-glass-table :deep(th) {
    border-bottom: 2px solid rgba(255, 255, 255, 0.05) !important;
}
.st-glass-table :deep(td) {
    border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
}

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
