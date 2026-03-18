<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import {
    PhArrowLeft,
    PhBrain,
    PhCheck,
    PhCrown,
    PhCrosshair,
    PhInfo,
    PhMagicWand,
    PhNavigationArrow,
    PhPlus,
    PhRobot,
    PhSealCheck,
    PhTrash,
    PhX,
    PhCaretDown,
    PhArrowsClockwise,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    modelValue: boolean;
    scenarioId?: number | null;
    roleId?: number | null;
}>();

const { t } = useI18n();

const emit = defineEmits(['update:modelValue', 'close', 'created']);

const internalVisible = ref(props.modelValue);
const currentStep = ref(1);
const analyzing = ref(false);
const saving = ref(false);
const generatingBlueprint = ref(false);
const expandedCompetency = ref<number | null>(null);
const expandedSkillBlueprint = ref<string | null>(null);
const loadingRole = ref(false);
const roleLoaded = ref(false);

const toggleCompetency = (idx: number) => {
    expandedCompetency.value = expandedCompetency.value === idx ? null : idx;
};

const toggleBlueprintSkill = (skillName: string) => {
    expandedSkillBlueprint.value = expandedSkillBlueprint.value === skillName ? null : skillName;
};

const generateBlueprint = async () => {
    generatingBlueprint.value = true;
    try {
        const response = await axios.post('/api/strategic-planning/roles/generate-skill-blueprint', {
            competencies: form.value.competencies,
        });
        form.value.blueprint = response.data.competency_blueprint;
        currentStep.value = 5;
    } catch (error) {
        console.error('Blueprint generation failed:', error);
    } finally {
        generatingBlueprint.value = false;
    }
};

/**
 * Normalizes archetype values from AI responses.
 * AI may return Spanish ('Estratégico') or English ('Strategic').
 */
const normalizeArchetype = (val: string): 'Strategic' | 'Tactical' | 'Operational' => {
    const lower = (val || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    if (lower.includes('estrat')) return 'Strategic';
    if (lower.includes('tact')) return 'Tactical';
    return 'Operational';
};

const steps = computed(() => [
    { title: t('role_wizard.step1_title'), desc: t('role_wizard.step1_desc') },
    { title: t('role_wizard.step2_title'), desc: t('role_wizard.step2_desc') },
    { title: t('role_wizard.step3_title'), desc: t('role_wizard.step3_desc') },
    { title: t('role_wizard.step4_title'), desc: t('role_wizard.step4_desc') },
    { title: 'Skill Blueprint', desc: 'Habilidades técnicas' },
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
    blueprint: [] as any[],
});

watch(
    () => props.modelValue,
    (val) => {
        internalVisible.value = val;
        if (val) {
            currentStep.value = 1;
            if (props.roleId) {
                fetchRoleData(props.roleId);
            } else {
                resetForm();
            }
        }
    },
);

const resetForm = () => {
    form.value = {
        name: '',
        description: '',
        cube: {
            x_archetype: 'Tactical',
            y_mastery_level: 3,
            z_business_process: '',
            justification: '',
        },
        purpose: '',
        expected_results: '',
        competencies: [],
        suggestions: '',
        blueprint: [],
    };
    roleLoaded.value = false;
};

const fetchRoleData = async (id: number) => {
    loadingRole.value = true;
    try {
        const response = await axios.get(`/api/roles/${id}`);
        const role = response.data;
        
        form.value.name = role.name;
        form.value.description = role.description;
        form.value.purpose = role.purpose || '';
        form.value.expected_results = role.expected_results || '';
        
        if (role.cube_dimensions) {
            form.value.cube = { ...role.cube_dimensions };
        }
        
        if (role.ai_archetype_config) {
            form.value.suggestions = role.ai_archetype_config.organizational_suggestions || '';
            form.value.blueprint = role.ai_archetype_config.skill_blueprint || [];
            if (role.ai_archetype_config.core_competencies) {
                form.value.competencies = role.ai_archetype_config.core_competencies;
            }
        } else if (role.skills) {
            // Fallback: translate legacy skills to competency format
            form.value.competencies = role.skills.map((s: any) => ({
                name: s.name,
                level: s.pivot?.required_level || 3,
                rationale: s.description || 'Competencia base del catálogo',
            }));
        }
        
        roleLoaded.value = true;
    } catch (error) {
        console.error('Error fetching role for wizard:', error);
    } finally {
        loadingRole.value = false;
    }
};

watch(internalVisible, (val) => {
    emit('update:modelValue', val);
});

const close = () => {
    internalVisible.value = false;
    emit('update:modelValue', false);
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
        form.value.cube.x_archetype = normalizeArchetype(data.cube_coordinates.x_archetype);
        form.value.cube.y_mastery_level = Number(data.cube_coordinates.y_mastery_level) || 3;
        form.value.cube.z_business_process =
            data.cube_coordinates.z_business_process;
        form.value.cube.justification = data.cube_coordinates.justification;
        form.value.competencies = data.core_competencies;
        form.value.suggestions = data.organizational_suggestions;

        // If the AI returns suggested purpose/results, we map them here
        if (data.purpose) form.value.purpose = data.purpose;
        if (data.expected_results)
            form.value.expected_results = data.expected_results;

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
                skill_blueprint: form.value.blueprint,
                organizational_suggestions: form.value.suggestions,
            },
            fte: 1,
            role_change: props.roleId ? 'update' : 'create',
            evolution_type: props.roleId ? 'optimized' : 'new_role',
            impact_level: 'medium',
        };

        const url = props.scenarioId
            ? `/api/scenarios/${props.scenarioId}/step2/roles`
            : (props.roleId ? `/api/roles/${props.roleId}` : '/api/roles');

        if (props.roleId && !props.scenarioId) {
            await axios.put(url, payload);
        } else {
            await axios.post(url, payload);
        }

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

            <!-- Loading Overlay for Existing Roles -->
            <div 
                v-if="loadingRole" 
                class="absolute inset-0 z-100 flex flex-col items-center justify-center bg-[#020617]/80 backdrop-blur-md"
            >
                <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-indigo-500/10 ring-1 ring-indigo-500/20">
                    <PhArrowsClockwise :size="40" class="animate-spin text-indigo-400" />
                </div>
                <h2 class="text-2xl font-black text-white">Cargando Inteligencia Actual...</h2>
                <p class="mt-2 text-slate-400">Recuperando coordenadas y configuración del catálogo</p>
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
                        {{ props.roleId ? 'Refinamiento Estratégico' : $t('role_wizard.title') }}
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
                                    class="max-w-2xl text-lg leading-relaxed font-medium text-white/70"
                                >
                                    {{ $t('role_wizard.define_node_desc') }}
                                </p>
                            </div>

                            <div class="space-y-8">
                                <div class="space-y-2">
                                    <label
                                        class="ml-1 text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80"
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
                                        class="w-full rounded-2xl border border-white/20 bg-white/5 backdrop-blur-md px-6 py-5 text-xl font-bold text-white placeholder-white/25 transition-all focus:border-indigo-400 focus:bg-white/[0.08] focus:outline-none focus:ring-1 focus:ring-indigo-400/30"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="ml-1 text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80"
                                        >Misión de Negocio (El "Qué")</label
                                    >
                                    <textarea
                                        v-model="form.description"
                                        rows="4"
                                        placeholder="Escribe una breve idea de qué hará este rol..."
                                        class="w-full resize-none rounded-2xl border border-white/20 bg-white/5 backdrop-blur-md px-6 py-4 text-lg text-white placeholder-white/25 transition-all focus:border-indigo-400 focus:outline-none focus:ring-1 focus:ring-indigo-400/30"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end pt-8">
                                <StButtonGlass
                                    variant="primary"
                                    size="lg"
                                    :disabled="
                                        !form.name ||
                                        !form.description
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
                                    <PhRobot
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
                                            <PhBrain
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

                        <!-- Step 3: Impact (Purpose & Results) -->
                        <div
                            v-else-if="currentStep === 3"
                            :key="3"
                            class="mx-auto max-w-4xl space-y-12"
                        >
                            <div class="space-y-4">
                                <h2
                                    class="text-4xl leading-tight font-black tracking-tight text-white"
                                >
                                    Arquitectura de Impacto
                                </h2>
                                <p
                                    class="max-w-2xl text-lg leading-relaxed font-medium text-white/50"
                                >
                                    Define la razón de ser y los éxitos tangibles del rol.
                                </p>
                            </div>

                            <div class="space-y-8">
                                <StCardGlass class="pa-8! bg-white/2! border-white/5">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-10 h-10 rounded-xl bg-pink-500/20 flex items-center justify-center text-pink-400 border border-pink-500/30">
                                            <PhInfo :size="20" />
                                        </div>
                                        <div>
                                            <div class="text-[10px] font-black tracking-widest text-pink-400 uppercase">Propósito Sugerido</div>
                                            <div class="text-xs text-white/40">La razón última de la existencia de este rol.</div>
                                        </div>
                                    </div>
                                    <textarea
                                        v-model="form.purpose"
                                        rows="4"
                                        class="w-full resize-none rounded-2xl border border-white/10 bg-white/5 px-6 py-4 text-base text-white transition-all focus:border-pink-500/50 focus:outline-none"
                                    ></textarea>
                                </StCardGlass>

                                <StCardGlass class="pa-8! bg-white/2! border-white/5">
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
                                            <PhSealCheck :size="20" />
                                        </div>
                                        <div>
                                            <div class="text-[10px] font-black tracking-widest text-emerald-400 uppercase">Resultados de Valor</div>
                                            <div class="text-xs text-white/40">Qué se espera entregar y lograr en el tiempo.</div>
                                        </div>
                                    </div>
                                    <textarea
                                        v-model="form.expected_results"
                                        rows="6"
                                        class="w-full resize-none rounded-2xl border border-white/10 bg-white/5 px-6 py-4 text-base text-white transition-all focus:border-emerald-500/50 focus:outline-none"
                                    ></textarea>
                                </StCardGlass>
                            </div>

                            <div class="flex justify-end gap-4 pt-8">
                                <StButtonGlass
                                    variant="ghost"
                                    :icon="PhArrowLeft"
                                    @click="currentStep--"
                                    >Etapa Anterior</StButtonGlass
                                >
                                <StButtonGlass
                                    variant="primary"
                                    :icon="PhCheck"
                                    @click="currentStep++"
                                    class="px-12!"
                                    >Confirmar Perfil</StButtonGlass
                                >
                            </div>
                        </div>

                        <!-- Step 4: DNA (Competencies) -->
                        <div
                            v-else-if="currentStep === 4"
                            :key="4"
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

                            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03] backdrop-blur-xl">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="border-b border-white/10 bg-white/[0.04]">
                                            <th
                                                class="px-8 py-5 text-left text-[10px] font-black tracking-[0.25em] text-white/40 uppercase"
                                            >
                                                {{
                                                    $t(
                                                        'role_wizard.strategic_capacity',
                                                    )
                                                }}
                                            </th>
                                            <th
                                                class="px-4 py-5 text-center text-[10px] font-black tracking-[0.25em] text-white/40 uppercase"
                                            >
                                                {{
                                                    $t(
                                                        'role_wizard.mastery_req',
                                                    )
                                                }}
                                            </th>
                                            <th
                                                class="px-4 py-5 text-left text-[10px] font-black tracking-[0.25em] text-white/40 uppercase"
                                            >
                                                {{
                                                    $t(
                                                        'role_wizard.ai_rationale',
                                                    )
                                                }}
                                            </th>
                                            <th
                                                class="px-8 py-5 text-right text-[10px] font-black tracking-[0.25em] text-white/40 uppercase"
                                            >
                                                {{ $t('role_wizard.ops') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template
                                            v-for="(
                                                skill, idx
                                            ) in form.competencies"
                                            :key="idx"
                                        >
                                            <tr
                                                class="group border-b border-white/5 transition-colors duration-300 hover:bg-white/[0.04] cursor-pointer"
                                                @click="toggleCompetency(idx)"
                                            >
                                                <td class="px-8 py-5">
                                                    <div class="flex items-center gap-4">
                                                        <div
                                                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-lg text-indigo-400 transition-transform duration-300"
                                                            :class="{ 'rotate-180': expandedCompetency === idx }"
                                                        >
                                                            <PhCaretDown :size="14" weight="bold" />
                                                        </div>
                                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-indigo-500/20 bg-indigo-500/10 text-[10px] font-black text-indigo-400">
                                                            {{ idx + 1 }}
                                                        </div>
                                                        <input
                                                            v-model="skill.name"
                                                            class="w-full border-none bg-transparent text-sm font-bold text-white placeholder:text-white/20 transition-colors focus:text-indigo-300 focus:outline-none"
                                                            :placeholder="$t('role_wizard.new_capacity')"
                                                            @click.stop
                                                        />
                                                    </div>
                                                </td>
                                            <td class="px-4 py-5">
                                                <div
                                                    class="flex items-center justify-center gap-1"
                                                >
                                                    <button
                                                        v-for="star in 5"
                                                        :key="star"
                                                        @click.stop="skill.level = star"
                                                        class="transition-all duration-200 hover:scale-125"
                                                    >
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            viewBox="0 0 24 24"
                                                            :fill="star <= skill.level ? '#fbbf24' : 'none'"
                                                            :stroke="star <= skill.level ? '#fbbf24' : 'rgba(255,255,255,0.15)'"
                                                            stroke-width="1.5"
                                                            class="h-5 w-5"
                                                        >
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                                        </svg>
                                                    </button>
                                                    <span class="ml-2 text-xs font-black text-white/30">{{ skill.level }}/5</span>
                                                </div>
                                            </td>
                                            <td
                                                class="max-w-xs px-4 py-5"
                                            >
                                                <p class="line-clamp-2 text-[11px] leading-relaxed font-medium text-white/40 italic">
                                                    {{ skill.rationale }}
                                                </p>
                                            </td>
                                                <td class="px-8 py-5 text-right">
                                                    <StButtonGlass
                                                        variant="ghost"
                                                        circle
                                                        size="sm"
                                                        :icon="PhTrash"
                                                        class="text-rose-500/30! hover:text-rose-500! hover:bg-rose-500/10!"
                                                        @click.stop="removeSkill(idx)"
                                                    />
                                                </td>
                                            </tr>

                                            <!-- BARS Panel -->
                                            <tr v-if="expandedCompetency === idx">
                                                <td colspan="4" class="px-8 py-0 border-b border-indigo-500/10 bg-indigo-500/2">
                                                    <div class="pb-10 pt-4">
                                                        <div class="mb-4 flex items-center justify-between">
                                                            <div class="flex items-center gap-2">
                                                                <PhMagicWand :size="14" class="text-indigo-400" weight="duotone" />
                                                                <span class="text-[10px] font-black tracking-[0.2em] text-indigo-400 uppercase">Alineación de Niveles BARS</span>
                                                            </div>
                                                            <div class="text-[9px] font-medium text-white/20 italic">
                                                                * Selecciona el nivel de estrellas para actualizar automáticamente el Nivel Requerido
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="grid grid-cols-5 gap-3">
                                                            <div 
                                                                v-for="bar in (skill.bars || [])" 
                                                                :key="bar.level"
                                                                class="relative flex flex-col rounded-xl border p-4 transition-all duration-500"
                                                                :class="[
                                                                    bar.level === skill.level 
                                                                        ? 'shadow-glow border-indigo-500/40 bg-indigo-500/10' 
                                                                        : 'border-white/5 bg-white/2'
                                                                ]"
                                                            >
                                                                <div class="mb-3 flex items-center gap-2">
                                                                    <div 
                                                                        class="flex h-6 w-6 items-center justify-center rounded-lg text-[10px] font-black"
                                                                        :class="bar.level === skill.level ? 'bg-indigo-500 text-white' : 'bg-white/5 text-white/30'"
                                                                    >
                                                                        {{ bar.level }}
                                                                    </div>
                                                                    <div class="text-[10px] font-black tracking-widest uppercase" :class="bar.level === skill.level ? 'text-indigo-400' : 'text-white/20'">
                                                                        {{ bar.level_name }}
                                                                    </div>
                                                                </div>
                                                                
                                                                <p class="mb-4 text-[11px] leading-relaxed font-medium" :class="bar.level === skill.level ? 'text-white/90' : 'text-white/40'">
                                                                    {{ bar.behavioral_description }}
                                                                </p>

                                                                <v-spacer></v-spacer>

                                                                <div 
                                                                    v-if="bar.level === skill.level"
                                                                    class="flex items-center gap-2 rounded-lg border border-indigo-500/20 bg-indigo-500/20 px-2 py-1.5"
                                                                >
                                                                    <PhSealCheck size="12" weight="duotone" class="text-indigo-300" />
                                                                    <span class="text-[8px] font-black tracking-widest text-indigo-300 uppercase">Nivel Requerido</span>
                                                                </div>
                                                                <div 
                                                                    v-else
                                                                    class="px-2 py-1.5 opacity-0 group-hover:opacity-100"
                                                                >
                                                                    <span class="text-[8px] font-black tracking-widest text-white/10 uppercase italic">Descriptor de Nivel</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Fallback if no bars are present (e.g. manually added skill) -->
                                                            <div v-if="!(skill.bars && skill.bars.length)" colspan="5" class="col-span-5 rounded-xl border border-dashed border-white/10 bg-white/2 p-6 text-center">
                                                                <p class="text-xs text-white/30 font-medium italic">Esta capacidad fue agregada manualmente y no cuenta con descriptores conductuales AI.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <div
                                    class="border-t border-white/10 bg-white/[0.02] p-5"
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
                            </div>

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
                                    :loading="generatingBlueprint"
                                    :icon="PhMagicWand"
                                    @click="generateBlueprint"
                                    class="px-12!"
                                    >{{
                                        'Generar Blueprint de Habilidades'
                                    }}</StButtonGlass
                                >
                            </div>
                        </div>

                        <!-- Step 5: Skill Blueprint (Detailed) -->
                        <div
                            v-else-if="currentStep === 5"
                            :key="5"
                            class="mx-auto max-w-6xl space-y-12"
                        >
                            <div class="flex items-end justify-between">
                                <div class="space-y-4">
                                    <h2 class="text-4xl font-black tracking-tight text-white">
                                        Blueprint de Habilidades
                                    </h2>
                                    <p class="text-base font-medium text-white/50">
                                        Desglose técnico de skills, niveles BARS y unidades de aprendizaje.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-8">
                                <div 
                                    v-for="(comp, cIdx) in form.blueprint" 
                                    :key="cIdx"
                                    class="rounded-3xl border border-white/5 bg-white/2 overflow-hidden"
                                >
                                    <div class="bg-white/4 px-8 py-5 border-b border-white/5 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20">
                                                <PhBrain :size="20" class="text-indigo-400" weight="duotone" />
                                            </div>
                                            <div>
                                                <div class="text-[10px] font-black tracking-widest text-white/30 uppercase">Competencia Estratégica</div>
                                                <div class="text-lg font-black text-white">{{ comp.competency_name }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-8">
                                        <div class="grid grid-cols-1 gap-6">
                                            <div 
                                                v-for="(skill, sIdx) in comp.skills" 
                                                :key="sIdx"
                                                class="rounded-2xl border border-white/10 bg-white/3 overflow-hidden transition-all duration-300"
                                                :class="{ 'border-indigo-500/30 ring-1 ring-indigo-500/20': expandedSkillBlueprint === skill.name }"
                                            >
                                                <div 
                                                    class="px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-white/2"
                                                    @click="toggleBlueprintSkill(skill.name)"
                                                >
                                                    <div class="flex items-center gap-4">
                                                        <div class="h-8 w-8 rounded-lg bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-400">
                                                            <PhSealCheck :size="16" weight="duotone" />
                                                        </div>
                                                        <span class="font-bold text-white">{{ skill.name }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <PhCaretDown 
                                                            :size="16" 
                                                            class="text-white/20 transition-transform duration-300"
                                                            :class="{ 'rotate-180': expandedSkillBlueprint === skill.name }"
                                                        />
                                                    </div>
                                                </div>

                                                <div v-if="expandedSkillBlueprint === skill.name" class="px-6 pb-6 pt-2 border-t border-white/5 bg-white/1">
                                                    <div class="grid grid-cols-1 gap-4 mt-4 lg:grid-cols-5">
                                                        <div 
                                                            v-for="level in skill.levels" 
                                                            :key="level.level"
                                                            class="flex flex-col rounded-xl border p-4 transition-all duration-500 bg-white/2 border-white/5 hover:bg-white/5"
                                                        >
                                                            <div class="mb-3 flex items-center gap-2">
                                                                <div class="flex h-5 w-5 items-center justify-center rounded-md bg-white/5 text-[9px] font-black text-white/30">
                                                                    {{ level.level }}
                                                                </div>
                                                                <div class="text-[9px] font-black tracking-widest text-indigo-400 uppercase">
                                                                    {{ level.level_name }}
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="space-y-4">
                                                                <div>
                                                                    <div class="text-[8px] font-black tracking-widest text-white/20 uppercase mb-1">Conductas</div>
                                                                    <p class="text-[10px] leading-relaxed text-white/60">{{ level.behavioral_description }}</p>
                                                                </div>
                                                                <div>
                                                                    <div class="text-[8px] font-black tracking-widest text-white/20 uppercase mb-1">Aprendizaje</div>
                                                                    <p class="text-[10px] leading-relaxed text-white/40 italic">{{ level.learning_units }}</p>
                                                                </div>
                                                                <div>
                                                                    <div class="text-[8px] font-black tracking-widest text-white/20 uppercase mb-1">Criterio Éxito</div>
                                                                    <p class="text-[10px] leading-relaxed text-emerald-400/50">{{ level.performance_criteria }}</p>
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

                            <div class="flex justify-end gap-4 pt-12">
                                <StButtonGlass
                                    variant="ghost"
                                    :icon="PhArrowLeft"
                                    @click="currentStep--"
                                    >Ajustar Competencias</StButtonGlass
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
