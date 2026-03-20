<script setup lang="ts">
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { computed, onMounted, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

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
    PhArrowsClockwise,
    PhCube,
    PhChartBar,
    PhAnchor,
    PhIdentificationCard,
} from '@phosphor-icons/vue';
import StGlowDivider from '@/components/StGlowDivider.vue';
import axios from 'axios';

const props = defineProps<{
    modelValue: boolean;
    scenarioId?: number | null;
    roleId?: number | null;
    initialData?: any;
}>();

const { t } = useI18n();

const emit = defineEmits(['update:modelValue', 'close', 'created']);

interface FormType {
    name: string;
    description: string;
    cube: {
        x_archetype: 'Strategic' | 'Tactical' | 'Operational';
        y_mastery_level: number;
        z_business_process: string;
        justification: string;
    };
    purpose: string;
    expected_results: string;
    competencies: any[];
    suggestions: string;
    blueprint: any[];
    bars: {
        behavior: string;
        attitude: string;
        responsibility: string;
        skill: string;
    } | null;
}

const internalVisible = ref(props.modelValue);
const currentStep = ref(1);
const analyzing = ref(false);
const saving = ref(false);
const generatingBlueprint = ref(false);
const expandedCompetency = ref<number | null>(null);
const loadingRole = ref(false);
const roleLoaded = ref(false);

const form = ref<FormType>({
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
    bars: null,
});

const toggleCompetency = (idx: number) => {
    expandedCompetency.value = expandedCompetency.value === idx ? null : idx;
};

// ---------------------------------------------------------
// MOCK DATA FOR LAYOUT TESTING (Avoids using AI tokens)
// ---------------------------------------------------------
const fillWithMockData = () => {
    form.value = {
        name: 'Director de Innovación Disruptiva',
        description: 'Liderar la transformación digital y la creación de nuevos modelos de negocio basados en IA.',
        cube: {
            x_archetype: 'Strategic',
            y_mastery_level: 5,
            z_business_process: 'Nuevos Modelos de Negocio',
            justification: 'La IA sugiere este posicionamiento debido a la alta incertidumbre y el horizonte temporal de impacto.',
        },
        purpose: 'Transformar radicalmente la cadena de valor mediante la integración de inteligencia artificial y automatización avanzada.',
        expected_results: '1. ROI del 25% en proyectos de IA en el primer año.\n2. Digitalización del 100% de los procesos administrativos.\n3. Reducción del ciclo de innovación de 12 a 3 meses.',
        competencies: [
            { 
                name: 'Pensamiento Algorítmico', 
                level: 5, 
                rationale: 'Esencial para entender las capacidades de la IA heredada.',
                bars: [
                    { level: 1, level_name: 'Novato', behavioral_description: 'Identifica conceptos básicos de algoritmos.' },
                    { level: 2, level_name: 'Usuario', behavioral_description: 'Aplica lógica básica en scripts simples.' },
                    { level: 3, level_name: 'Competente', behavioral_description: 'Diseña flujos de datos estructurados.' },
                    { level: 4, level_name: 'Experto', behavioral_description: 'Optimiza modelos complejos de decisión.' },
                    { level: 5, level_name: 'Maestro', behavioral_description: 'Crea arquitecturas de IA escalables a nivel corporativo.' }
                ]
            },
            { 
                name: 'Orquestación de Talento Híbrido', 
                level: 4, 
                rationale: 'Necesario para gestionar equipos humano-máquina.',
                bars: [
                    { level: 1, level_name: 'Novato', behavioral_description: 'Asigna tareas simples.' },
                    { level: 2, level_name: 'Usuario', behavioral_description: 'Coordina comunicaciones básicas.' },
                    { level: 3, level_name: 'Competente', behavioral_description: 'Gestiona flujos de trabajo mixtos.' },
                    { level: 4, level_name: 'Experto', behavioral_description: 'Maximiza el rendimiento del binomio humano-IA.' },
                    { level: 5, level_name: 'Maestro', behavioral_description: 'Evoluciona la cultura hacia la simbiosis tecnológica.' }
                ]
            }
        ],
        suggestions: 'Considerar la creación de una oficina de Ética de Datos supeditada a este rol.',
        bars: {
            behavior: 'Lidera la integración de inteligencia artificial en todos los niveles operativos, asegurando que el arquetipo Estratégico se materialice en decisiones basadas en datos.',
            attitude: 'Mentalidad de crecimiento exponencial y ética inquebrantable frente al avance tecnológico.',
            responsibility: 'Garantizar que la organización no solo adopte IA, sino que evolucione sus capacidades core para un mercado sintético.',
            skill: 'Dominio avanzado de arquitecturas cognitivas y modelos de lenguaje de gran escala aplicados a negocio.'
        },
        blueprint: [
            {
                competency_name: 'Pensamiento Algorítmico',
                levels: [
                    { level: 1, level_name: 'Básico (Ayuda)', description: 'Entiende los fundamentos de los algoritmos and ayuda en la recopilación de datos.' },
                    { level: 2, level_name: 'Intermedio (Aplica)', description: 'Aplica algoritmos estándar para resolver problemas operativos recurrentes.' },
                    { level: 3, level_name: 'Avanzado (Habilita)', description: 'Diseña y habilita flujos algorítmicos que optimizan el trabajo del equipo.' },
                    { level: 4, level_name: 'Experto (Asegura)', description: 'Garantiza la robustez y eficiencia de las soluciones algorítmicas organizacionales.' },
                    { level: 5, level_name: 'Maestro (Inspira)', description: 'Innova el campo del pensamiento algorítmico, creando nuevos paradigmas de solución.' }
                ],
                skills: [
                    {
                        name: 'Análisis de Modelos Predictivos',
                        description: 'Capacidad para identificar, validar y utilizar modelos estadísticos que anticipen resultados de negocio.',
                        levels: [
                            {
                                level: 1,
                                learning_unit: 'Fundamentos de Estadística y Tipos de Modelos (Regresión, Clasificación).',
                                performance_criterion: 'Identifica correctamente qué modelo aplica para un caso de uso sencillo.'
                            },
                            {
                                level: 2,
                                learning_unit: 'Evaluación de modelos y visualización de precisiones.',
                                performance_criterion: 'Interpreta correctamente una matriz de confusión y el score R2.'
                            },
                            {
                                level: 3,
                                learning_unit: 'Técnicas de feature engineering y limpieza de datos avanzada.',
                                performance_criterion: 'Mejora el rendimiento de un modelo existente mediante el ajuste de variables.'
                            },
                            {
                                level: 4,
                                learning_unit: 'A/B Testing y despliegue de modelos en producción.',
                                performance_criterion: 'Garantiza que el despliegue del modelo no degrade la experiencia del usuario.'
                            },
                            {
                                level: 5,
                                learning_unit: 'Diseño de arquitecturas predictivas propietarias.',
                                performance_criterion: 'Crea un modelo innovador que reduce el costo operativo en un 20%.'
                            }
                        ]
                    },
                    {
                        name: 'Integración de LLMs en Flujos de Trabajo',
                        description: 'Habilidad para integrar y optimizar Large Language Models (LLMs) en procesos de negocio existentes.',
                        levels: [
                            { level: 1, learning_unit: 'Prompt Engineering Básico y uso de APIs de LLMs.', performance_criterion: 'Genera textos coherentes para tareas simples usando prompts predefinidos.' },
                            { level: 2, learning_unit: 'Optimización de prompts y fine-tuning básico.', performance_criterion: 'Adapta prompts para mejorar la calidad de las respuestas en contextos específicos.' },
                            { level: 3, learning_unit: 'Diseño de arquitecturas de agentes y orquestación de LLMs.', performance_criterion: 'Implementa un flujo de trabajo automatizado que utiliza un LLM para procesar información.' },
                            { level: 4, learning_unit: 'Seguridad, ética y gobernanza en el uso de LLMs.', performance_criterion: 'Evalúa y mitiga riesgos de sesgo y privacidad en aplicaciones con LLMs.' },
                            { level: 5, learning_unit: 'Investigación y desarrollo de nuevos paradigmas con LLMs.', performance_criterion: 'Propone y lidera la implementación de una solución innovadora basada en LLMs que genera una ventaja competitiva.' }
                        ]
                    }
                ]
            }
        ],
    };
    // Only jump to step 2 if we are currently in step 1
    if (currentStep.value === 1) {
        currentStep.value = 2;
    }
};
// ---------------------------------------------------------

const generateBlueprint = async () => {
    generatingBlueprint.value = true;
    try {
        const response = await axios.post('/api/strategic-planning/roles/generate-skill-blueprint', {
            competencies: form.value.competencies,
        });
        form.value.blueprint = response.data.competency_blueprint;
        // Mocking BARS for now as we don't have it in the API yet
        form.value.bars = {
            behavior: 'Demuestra una actitud proactiva ante los desafíos técnicos, liderando con el ejemplo y fomentando la colaboración.',
            attitude: 'Mantiene una mentalidad de crecimiento continuo y resiliencia ante la incertidumbre del mercado.',
            responsibility: 'Asegura la calidad y escalabilidad de las soluciones tecnológicas alineadas con la visión de la empresa.',
            skill: 'Posee un dominio profundo de arquitecturas modernas y herramientas de inteligencia artificial aplicada.'
        };
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
    const lower = (val || '').toLowerCase().normalize('NFD').replaceAll(/[\u0300-\u036f]/g, '');
    if (lower.includes('estrat')) return 'Strategic';
    if (lower.includes('tact')) return 'Tactical';
    return 'Operational';
};

const steps = computed(() => [
    { title: t('role_wizard.step1_title'), description: t('role_wizard.step1_desc') },
    { title: t('role_wizard.step2_title'), description: t('role_wizard.step2_desc') },
    { title: t('role_wizard.step3_title'), description: t('role_wizard.step3_desc') },
    { title: t('role_wizard.step4_title'), description: t('role_wizard.step4_desc') },
    { title: t('role_wizard.step5_title'), description: t('role_wizard.step5_desc') },
]);

const populateForm = (role: any) => {
    form.value.name = role.name || '';
    form.value.description = role.description || '';
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
};

const resetForm = () => {
    form.value = {
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
        bars: null,
    };
    roleLoaded.value = false;
};

const fetchRoleData = async (id: number) => {
    loadingRole.value = true;
    try {
        const response = await axios.get(`/api/roles/${id}`);
        const role = response.data;
        populateForm(role);
        roleLoaded.value = true;
    } catch (error) {
        console.error('Error fetching role for wizard:', error);
    } finally {
        loadingRole.value = false;
    }
};

watch(
    () => props.modelValue,
    (val) => {
        internalVisible.value = val;
        if (val) {
            currentStep.value = 1;
            if (props.initialData) {
                populateForm(props.initialData);
                roleLoaded.value = true;
            } else if (props.roleId) {
                fetchRoleData(props.roleId);
            } else {
                resetForm();
            }
        }
    },
    { immediate: true }
);

onMounted(() => {
    if (props.modelValue) {
        if (props.initialData) {
            populateForm(props.initialData);
            roleLoaded.value = true;
        } else if (props.roleId) {
            fetchRoleData(props.roleId);
        }
    }
});



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
                bars: form.value.bars,
            },
            fte: 1,
            role_change: props.roleId ? 'update' : 'create',
            evolution_type: props.roleId ? 'optimized' : 'new_role',
            impact_level: 'medium',
            status: 'pending_approval',
        };

        let url = '/api/roles';
        if (props.scenarioId) {
            url = `/api/scenarios/${props.scenarioId}/step2/roles`;
        } else if (props.roleId) {
            url = `/api/roles/${props.roleId}`;
        }

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
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-indigo-500/30 bg-indigo-500/20 shadow-[0_0_15px_rgba(99,102,241,0.2)] cursor-pointer hover:bg-indigo-500/40 transition-colors"
                        @click="fillWithMockData"
                        title="Debug: Prellenar con datos de prueba"
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
                                    {{ step.description }}
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
                                        for="role-name-input"
                                        class="ml-1 text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80"
                                        >{{
                                            $t(
                                                'role_wizard.architectural_label',
                                            )
                                        }}</label
                                    >
                                    <input
                                        id="role-name-input"
                                        v-model="form.name"
                                        type="text"
                                        :placeholder="
                                            $t(
                                                'role_wizard.architectural_placeholder',
                                            )
                                        "
                                        class="w-full rounded-2xl border border-white/20 bg-white/5 backdrop-blur-md px-6 py-5 text-xl font-bold text-white placeholder-white/25 transition-all focus:border-indigo-400 focus:bg-white/8 focus:outline-none focus:ring-1 focus:ring-indigo-400/30"
                                    />
                                </div>

                                <div class="space-y-2">
                                    <label
                                        for="role-description-input"
                                        class="ml-1 text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80"
                                        >Misión de Negocio (El "Qué")</label
                                    >
                                    <textarea
                                        id="role-description-input"
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
                            <div class="flex items-end justify-between" style="padding:12px">
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

                            <div class="flex flex-col gap-12" style="padding: 12px">
                                <div class="grid grid-cols-1 gap-12 lg:grid-cols-1">
                                    <!-- Axis X & Axis Y -->
                                    <div class="space-y-10">
                                        <!-- Axis X -->
                                        <div class="space-y-6">
                                            <div class="flex items-center gap-2">
                                                <PhCube :size="16" weight="duotone" class="text-indigo-400" />
                                                <label for="axis-x-control" class="text-[10px] font-black tracking-[0.3em] text-white/30 uppercase">{{ $t('role_wizard.axis_x') }}</label>
                                            </div>
                                            <div id="axis-x-control" class="grid grid-cols-3 gap-3" style="padding:12px">
                                                <button
                                                    v-for="arc in (['Strategic', 'Tactical', 'Operational'] as const)"
                                                    :key="arc"
                                                    @click="form.cube.x_archetype = arc"
                                                    class="flex flex-col items-center gap-3 rounded-2xl border p-5 transition-all duration-500"
                                                    :class="form.cube.x_archetype === arc ? 'shadow-glow border-indigo-500/50 bg-indigo-500/20 ring-1 ring-indigo-500/40' : 'border-white/5 bg-white/2 hover:bg-white/5'"
                                                >
                                                    <component
                                                        :is="arc === 'Strategic' ? PhCrown : arc === 'Tactical' ? PhCrosshair : PhNavigationArrow"
                                                        :size="24"
                                                        :weight="form.cube.x_archetype === arc ? 'duotone' : 'regular'"
                                                        :class="form.cube.x_archetype === arc ? 'text-indigo-300' : 'text-white/20'"
                                                    />
                                                    <div class="text-center">
                                                        <div class="mb-1 text-[11px] font-black text-white">
                                                            {{ arc === 'Strategic' ? $t('role_wizard.axis_x_strategic') : arc === 'Tactical' ? $t('role_wizard.axis_x_tactical') : $t('role_wizard.axis_x_operational') }}
                                                        </div>
                                                        <div class="text-[8px] font-bold text-white/30 uppercase tracking-tighter">
                                                            {{ arc === 'Strategic' ? $t('role_wizard.horizon_3') : arc === 'Tactical' ? $t('role_wizard.horizon_2') : $t('role_wizard.horizon_1') }}
                                                        </div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Axis Y -->
                                        <div class="space-y-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <PhChartBar :size="16" weight="duotone" class="text-indigo-400" />
                                                    <label for="mastery-level-slider" class="text-[10px] font-black tracking-[0.3em] text-white/30 uppercase">{{ $t('role_wizard.axis_y') }}</label>
                                                </div>
                                                <span class="text-xl font-black text-indigo-400 font-mono tracking-tighter">{{ $t('role_wizard.tier', { level: form.cube.y_mastery_level }) }}</span>
                                            </div>
                                            <div class="px-2">
                                                <v-slider
                                                    id="mastery-level-slider"
                                                    v-model="form.cube.y_mastery_level"
                                                    :min="1" :max="5" :step="1"
                                                    show-ticks="always"
                                                    thumb-label="always"
                                                    color="indigo-500"
                                                    track-color="white/5"
                                                />
                                            </div>
                                        </div>
<!-- Axis Z & Rationale -->
                                    <div class="space-y-6">
                                        <!-- Axis Z -->
                                        <div class="space-y-6">
                                            <div class="flex items-center gap-2">
                                                <PhAnchor :size="16" weight="duotone" class="text-indigo-400" />
                                                <label for="business-process-input" class="text-[10px] font-black tracking-[0.3em] text-white/30 uppercase">{{ $t('role_wizard.axis_z') }}</label>
                                            </div>
                                            <input
                                                id="business-process-input"
                                                v-model="form.cube.z_business_process"
                                                type="text"
                                                :placeholder="$t('role_wizard.anchor_placeholder')"
                                                class="w-full rounded-2xl border border-white/5 bg-white/2 px-6 py-5 text-base font-medium text-white placeholder-white/10 transition-all focus:border-indigo-500/50 focus:bg-white/5 focus:outline-none"
                                            />
                                        </div>
                                    </div>

                                    <!-- Technoglow Divider (Reusable Component) -->
                                    <StGlowDivider class="my-12 h-px w-full" />
                                        <!-- Chuleta / Rationale -->
                                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-1">
                                            <div class="rounded-2xl border border-indigo-500/10 bg-indigo-500/5 p-6 space-y-3" style="padding:12px">
                                                <div class="flex items-center gap-2">
                                                    <PhBrain :size="14" weight="duotone" class="text-indigo-400" />
                                                    <h4 class="text-[9px] font-black tracking-[0.2em] text-indigo-400 uppercase leading-none">{{ $t('role_wizard.synthesis_rationale') }}</h4>
                                                </div>
                                                <p class="text-[11px] leading-relaxed text-white/60 italic font-medium">
                                                    "{{ form.cube.justification }}"
                                                </p>
                                            </div>

                                            <div class="rounded-2xl border border-emerald-500/10 bg-emerald-500/5 p-6 space-y-3" style="padding:12px">
                                                <div class="flex items-center gap-2">
                                                    <PhMagicWand :size="14" weight="duotone" class="text-emerald-400" />
                                                    <h4 class="text-[9px] font-black tracking-[0.2em] text-emerald-400 uppercase leading-none">{{ $t('role_wizard.optimization_tip') }}</h4>
                                                </div>
                                                <div class="text-[11px] leading-relaxed font-medium text-emerald-100/60 line-clamp-4">
                                                    {{ form.suggestions }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

                            <div class="space-y-8" >
                                <StCardGlass class="pa-10! bg-indigo-500/5! border-indigo-500/10 shadow-[0_0_50px_rgba(99,102,241,0.05)]" style="padding:12px; margin:12px">
                                    <div class="flex items-center gap-6 mb-8">
                                        <div class="w-14 h-14 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 border border-indigo-500/30">
                                            <PhInfo :size="28" weight="duotone" />
                                        </div>
                                        <div>
                                            <label for="role-purpose-textarea" class="text-xs font-black tracking-widest text-indigo-400 uppercase">Propósito Sugerido</label>
                                            <div class="text-sm text-white/30 font-medium">La razón última de la existencia de este rol en la red organizacional.</div>
                                        </div>
                                    </div>
                                    <textarea
                                        id="role-purpose-textarea"
                                        v-model="form.purpose"
                                        rows="4"
                                        class="w-full resize-none rounded-2xl border border-white/5 bg-white/2 px-8 py-6 text-xl leading-relaxed font-medium text-white transition-all focus:border-indigo-500/50 focus:bg-white/5 focus:outline-none placeholder-white/10"
                                        placeholder="Define el propósito general de este nodo..."
                                    ></textarea>
                                </StCardGlass>

                                <StCardGlass class="pa-10! bg-emerald-500/5! border-emerald-500/10 shadow-[0_0_50px_rgba(52,211,153,0.05)]" style="padding:12px; margin:12px">
                                    <div class="flex items-center gap-6 mb-8">
                                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
                                            <PhSealCheck :size="28" weight="duotone" />
                                        </div>
                                        <div>
                                            <label for="role-results-textarea" class="text-xs font-black tracking-widest text-emerald-400 uppercase">Resultados de Valor</label>
                                            <div class="text-sm text-white/30 font-medium">Qué se espera entregar y lograr en el tiempo (OKRs, KPIs, Metas).</div>
                                        </div>
                                    </div>
                                    <textarea
                                        id="role-results-textarea"
                                        v-model="form.expected_results"
                                        rows="6"
                                        class="w-full resize-none rounded-2xl border border-white/5 bg-white/2 px-8 py-6 text-base leading-loose font-medium text-emerald-100/70 transition-all focus:border-emerald-500/50 focus:bg-white/5 focus:outline-none placeholder-white/10"
                                        placeholder="Enumera los resultados tangibles que este rol debe asegurar..."
                                    ></textarea>
                                    <div class="mt-4 flex gap-2">
                                        <div class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-black text-emerald-400 uppercase tracking-widest">SMART GOALS</div>
                                        <div class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[9px] font-black text-emerald-400 uppercase tracking-widest">IMPACT DRIVEN</div>
                                    </div>
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
                            style="padding:12px; margin:12px"
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

                            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/3 backdrop-blur-xl">
                                <table class="w-full border-collapse">
                                    <thead>
                                        <tr class="border-b border-white/10 bg-white/4">
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
                                                class="group border-b border-white/5 transition-colors duration-300 hover:bg-white/4 cursor-pointer"
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
                                                            :id="`capacity-input-${idx}`"
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
                                                <TooltipProvider :delay-duration="300">
                                                    <Tooltip>
                                                        <TooltipTrigger as-child>
                                                            <p class="line-clamp-2 text-[11px] leading-relaxed font-medium text-white/40 italic cursor-help">
                                                                {{ skill.rationale }}
                                                            </p>
                                                        </TooltipTrigger>
                                                        <TooltipContent class="max-w-[400px] border-indigo-500/20 bg-slate-900/95 p-4 text-xs leading-relaxed text-indigo-100 shadow-xl backdrop-blur-xl">
                                                            <div class="space-y-2">
                                                                <div class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Justificación Original AI</div>
                                                                <p>{{ skill.rationale }}</p>
                                                            </div>
                                                        </TooltipContent>
                                                    </Tooltip>
                                                </TooltipProvider>
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
                                                                <div class="flex flex-col">
                                                                    <span class="text-[10px] font-black tracking-[0.2em] text-indigo-400 uppercase">Alineación de Niveles SFIA</span>
                                                                    <span class="text-[8px] font-bold text-white/20 uppercase">Global Competency Framework</span>
                                                                </div>
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
                                                                
                                                                <TooltipProvider :delay-duration="300">
                                                                    <Tooltip>
                                                                        <TooltipTrigger as-child>
                                                                            <p class="mb-4 line-clamp-3 text-[11px] leading-relaxed font-medium cursor-help" :class="bar.level === skill.level ? 'text-white/90' : 'text-white/40'">
                                                                                {{ bar.behavioral_description }}
                                                                            </p>
                                                                        </TooltipTrigger>
                                                                        <TooltipContent class="max-w-[300px] border-white/10 bg-slate-900/95 p-4 text-xs leading-relaxed text-white shadow-xl backdrop-blur-xl">
                                                                            <div class="space-y-2">
                                                                                <div class="text-[10px] font-black uppercase tracking-widest text-indigo-400">{{ bar.level_name }} (Nivel {{ bar.level }})</div>
                                                                                <p>{{ bar.behavioral_description }}</p>
                                                                            </div>
                                                                        </TooltipContent>
                                                                    </Tooltip>
                                                                </TooltipProvider>

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
                                    class="border-t border-white/10 bg-white/2 p-5"
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
                                    variant="primary"
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

                        <!-- Step 5: Role BARS -->
                        <div v-else-if="currentStep === 5" :key="5" class="mx-auto max-w-6xl space-y-12">
                            <div class="flex items-end justify-between">
                                <div class="space-y-4">
                                    <h2 class="text-4xl font-black tracking-tight text-white italic">
                                        {{ $t('role_wizard.step5_title') }}
                                    </h2>
                                    <p class="text-base font-medium text-white/50">
                                        {{ $t('role_wizard.step5_desc') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Role-wide BARS Section -->
                            <div v-if="form.bars" class="relative group">
                                <div class="absolute -inset-1 bg-linear-to-r from-indigo-500/20 to-purple-500/20 rounded-4xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                                <div class="relative rounded-3xl border border-white/10 bg-black/40 backdrop-blur-xl p-8 overflow-hidden">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="h-12 w-12 rounded-2xl bg-indigo-500/20 flex items-center justify-center border border-indigo-500/30">
                                            <PhIdentificationCard :size="24" class="text-indigo-400" weight="duotone" />
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-black text-white italic tracking-tight uppercase">BARS del Rol (Behavior, Attitude, Responsibility, Skill)</h3>
                                            <p class="text-[10px] font-bold text-white/30 tracking-[0.2em] uppercase mt-0.5">Definición de comportamiento transversal para el nodo arquitectónico</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-indigo-500/30 transition-colors">
                                            <div class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-3">Behavior (Conducta)</div>
                                            <p class="text-xs leading-relaxed text-white/70">{{ form.bars.behavior }}</p>
                                        </div>
                                        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-indigo-500/30 transition-colors">
                                            <div class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-3">Attitude (Actitud)</div>
                                            <p class="text-xs leading-relaxed text-white/70 italic">{{ form.bars.attitude }}</p>
                                        </div>
                                        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-indigo-500/30 transition-colors">
                                            <div class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-3">Responsibility (Responsabilidad)</div>
                                            <p class="text-xs leading-relaxed text-white/70">{{ form.bars.responsibility }}</p>
                                        </div>
                                        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 hover:border-indigo-500/30 transition-colors">
                                            <div class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-3">Skill (Saber Especializado)</div>
                                            <p class="text-xs leading-relaxed text-white/70">{{ form.bars.skill }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Competency Summary Table -->
                            <div class="space-y-6">
                                <div class="flex items-center gap-2 px-2">
                                    <PhShieldCheck :size="16" class="text-indigo-400" weight="duotone" />
                                    <h3 class="text-xs font-black tracking-widest text-white/30 uppercase">Resumen de Competencias & Niveles SFIA</h3>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div 
                                        v-for="(comp, cIdx) in form.blueprint" 
                                        :key="cIdx"
                                        class="p-6 rounded-3xl border border-white/5 bg-white/2 hover:bg-white/4 transition-colors"
                                    >
                                        <div class="flex items-center gap-4 mb-4">
                                            <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 text-indigo-400">
                                                <PhBrain :size="20" weight="duotone" />
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-white italic tracking-tight uppercase">{{ comp.competency_name }}</div>
                                                <div class="text-[9px] font-bold text-white/20 uppercase">Alineación Estratégica OK</div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-5 gap-1.5 pt-2 border-t border-white/5 mt-4">
                                            <div v-for="l in comp.levels" :key="l.level" class="h-1 rounded-full" :class="l.level === (form.competencies[cIdx]?.level || 3) ? 'bg-indigo-500 shadow-glow' : 'bg-white/5'"></div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-between">
                                            <div class="text-[9px] font-black text-indigo-400 uppercase">Nivel Requerido: {{ form.competencies[cIdx]?.level || 3 }}</div>
                                            <div class="text-[9px] font-bold text-white/30 uppercase italic">{{ comp.levels.find((l: { level: any; }) => l.level === (form.competencies[cIdx]?.level || 3))?.level_name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-4 pt-12">
                                <StButtonGlass
                                    variant="ghost"
                                    :icon="PhArrowLeft"
                                    @click="currentStep--"
                                    >{{
                                        $t('role_wizard.step4_title')
                                    }}</StButtonGlass
                                >
                                <StButtonGlass
                                    variant="secondary"
                                    :loading="saving"
                                    :icon="PhNavigationArrow"
                                    @click="saveRole"
                                    class="px-12!"
                                    >{{
                                        $t('role_wizard.request_approval')
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
