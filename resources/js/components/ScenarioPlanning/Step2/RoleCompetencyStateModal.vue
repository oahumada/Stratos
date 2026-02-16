<template>
    <v-dialog
        :model-value="props.visible"
        max-width="700px"
        @update:model-value="$emit('close')"
    >
        <v-card>
            <v-card-title class="text-lg font-bold">
                {{ props.competencyName }} - {{ props.roleName }}
            </v-card-title>

            <v-card-text class="py-6">
                <div v-if="notify.message" class="mb-4">
                    <v-alert
                        :color="notify.color"
                        variant="tonal"
                        density="comfortable"
                        dismissible
                    >
                        {{ notify.message }}
                    </v-alert>
                </div>
                <!-- Current State Display -->
                <div class="mb-6 rounded bg-gray-50 p-4">
                    <p class="mb-2 text-sm text-gray-600">
                        Estado actual del rol:
                    </p>
                    <p class="font-semibold">
                        {{
                            props.mapping
                                ? changeTypeLabel(props.mapping.change_type)
                                : 'Sin asociación'
                        }}
                    </p>
                </div>

                <!-- If transform created skills in incubation, show them here -->
                <div
                    v-if="incubatedSkills.length"
                    class="mb-4 rounded border-l-4 border-yellow-400 bg-yellow-50 p-3"
                >
                    <label class="mb-2 block text-sm font-semibold"
                        >Skills creadas (incubación):</label
                    >
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="s in incubatedSkills"
                            :key="s.id || s.name"
                            class="rounded-full bg-yellow-100 px-2 py-1 text-sm"
                            >{{ s.name }}
                            <span
                                class="text-xs text-gray-600"
                                style="margin-left: 6px"
                                >(incubation)</span
                            ></span
                        >
                    </div>
                </div>

                <!-- State Selection -->
                <div class="mb-6">
                    <div class="mb-3 flex items-center gap-2">
                        <label class="block text-sm font-semibold">
                            Seleccionar estado (una única opción):
                        </label>
                        <div>
                            <v-btn
                                variant="text"
                                size="medium"
                                class="text-info"
                                aria-label="Leyenda tipo de asociación"
                                title="ver información"
                                @click="showLegend = true"
                            >
                                <v-icon
                                    class="text-info-darken-1 align-middle text-3xl"
                                    >mdi-information-variant-circle</v-icon
                                >
                            </v-btn>

                            <InfoLegend
                                v-model="showLegend"
                                title="Metodología: Tipos de Asociación Estratégica"
                                :items="legendItems"
                                icon="mdi-book-open-variant"
                                width="650"
                            />
                        </div>
                    </div>
                    <v-radio-group
                        v-model="formData.change_type"
                        column
                        class="mt-4"
                    >
                        <div
                            class="mb-2 cursor-pointer rounded border border-gray-200 p-3 hover:bg-gray-50"
                        >
                            <v-radio value="maintenance" class="mb-1">
                                <template #label>
                                    <span class="font-medium">Mantención</span>
                                    <span class="ml-2 text-xs text-gray-500"
                                        >(Sin cambios esperados)</span
                                    >
                                </template>
                            </v-radio>
                        </div>
                        <div
                            class="mb-2 cursor-pointer rounded border border-gray-200 p-3 hover:bg-gray-50"
                        >
                            <v-radio value="transformation" class="mb-1">
                                <template #label>
                                    <span class="font-medium"
                                        >Transformación</span
                                    >
                                    <span class="ml-2 text-xs text-gray-500"
                                        >(Requiere upskilling)</span
                                    >
                                </template>
                            </v-radio>
                        </div>
                        <div
                            class="mb-2 cursor-pointer rounded border border-gray-200 p-3 hover:bg-gray-50"
                        >
                            <v-radio value="enrichment" class="mb-1">
                                <template #label>
                                    <span class="font-medium"
                                        >Enriquecimiento</span
                                    >
                                    <span class="ml-2 text-xs text-gray-500"
                                        >(Nueva o mejorada)</span
                                    >
                                </template>
                            </v-radio>
                        </div>
                        <div
                            class="mb-2 cursor-pointer rounded border border-gray-200 p-3 hover:bg-gray-50"
                        >
                            <v-radio value="extinction" class="mb-1">
                                <template #label>
                                    <span class="font-medium">Extinción</span>
                                    <span class="ml-2 text-xs text-gray-500"
                                        >(Desaparecerá)</span
                                    >
                                </template>
                            </v-radio>
                        </div>
                    </v-radio-group>
                </div>

                <!-- Arquetipo vs Nivel Visualization -->
                <div
                    class="mb-4 flex items-center justify-between rounded-lg border border-gray-100 bg-white p-3 shadow-sm"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 flex-col items-center justify-center rounded bg-gray-100"
                        >
                            <span class="text-[10px] font-bold text-gray-400"
                                >ROL</span
                            >
                            <span
                                class="text-lg leading-none font-black text-gray-700"
                                >{{ props.archetype }}</span
                            >
                        </div>
                        <div>
                            <div
                                class="text-[10px] font-bold text-gray-400 uppercase"
                            >
                                Arquetipo
                            </div>
                            <div class="text-sm font-bold text-gray-700">
                                {{ archetypeLabel }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <div
                                class="text-[10px] font-bold text-gray-400 uppercase"
                            >
                                Nivel Objetivo
                            </div>
                            <div class="text-sm font-bold text-gray-700">
                                Maestría {{ formData.required_level }}
                            </div>
                        </div>
                        <div
                            class="bg-primary-lighten-4 flex h-10 w-10 flex-col items-center justify-center rounded"
                        >
                            <span class="text-[10px] font-bold text-primary"
                                >LVL</span
                            >
                            <span
                                class="text-lg leading-none font-black text-primary"
                                >{{ formData.required_level }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Consistency Traffic Light (Arquetipo vs Nivel) -->
                <div v-if="consistencyAlert" class="mb-6">
                    <v-alert
                        :color="consistencyAlert.color"
                        :icon="consistencyAlert.icon"
                        variant="tonal"
                        border="start"
                        class="rounded-lg shadow-sm"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <div
                                    class="mb-1 text-xs font-black tracking-wider uppercase"
                                >
                                    Diagnóstico de Ingeniería:
                                    {{ consistencyAlert.title }}
                                </div>
                                <div class="text-sm leading-snug">
                                    {{ consistencyAlert.message }}
                                </div>
                            </div>
                        </div>
                    </v-alert>
                </div>

                <!-- Strategic Rationale for Level Decrease -->
                <div
                    v-if="showLevelDecreaseRationale"
                    class="animate-pulse-slow mb-6"
                >
                    <div
                        class="mb-2 flex items-center gap-2 text-xs font-black tracking-wider text-amber-700 uppercase"
                    >
                        <v-icon size="16" color="amber-darken-2"
                            >mdi-chart-line-variant</v-icon
                        >
                        Racional Estratégico de Reducción
                    </div>
                    <v-select
                        v-model="formData.reduced_level_rationale"
                        :items="[
                            {
                                title: 'Efficiency Gain (IA/Automation)',
                                value: 'efficiency',
                            },
                            {
                                title: 'Reduced Scope (Job Simplification)',
                                value: 'scope',
                            },
                            {
                                title: 'Capacity Loss (Strategic Risk)',
                                value: 'risk',
                            },
                        ]"
                        label="¿Por qué disminuye el nivel?"
                        variant="outlined"
                        density="comfortable"
                        color="amber-darken-2"
                        hint="Este motivo se usará en el Gap Analysis para evaluar el riesgo organizacional."
                        persistent-hint
                    ></v-select>
                </div>

                <!-- Referent/Mentorship Flag (for Operational roles with high mastery) -->
                <div v-if="showReferentOption" class="mb-6">
                    <v-checkbox
                        v-model="formData.is_referent"
                        color="purple-darken-2"
                        hide-details
                    >
                        <template #label>
                            <div class="flex items-center gap-2">
                                <v-icon size="18" color="purple-darken-2"
                                    >mdi-account-star</v-icon
                                >
                                <span class="text-sm font-semibold"
                                    >Rol de Referencia / Mentoría</span
                                >
                            </div>
                        </template>
                    </v-checkbox>
                    <div class="mt-1 ml-8 text-xs text-purple-700">
                        Este rol actúa como <strong>mentor técnico</strong> o
                        referente interno. El nivel alto es coherente con su
                        función de mentoría.
                    </div>
                </div>

                <!-- Conditional Fields -->

                <!-- TRANSFORMATION -->
                <div
                    v-if="formData.change_type === 'transformation'"
                    class="mb-6"
                >
                    <div
                        class="rounded border-l-4 border-blue-500 bg-blue-50 p-4"
                    >
                        <label class="mb-3 block text-sm font-semibold">
                            Nivel actual → Futuro:
                        </label>
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <label class="mb-1 block text-xs text-gray-600">
                                    Actual
                                </label>
                                <v-select
                                    v-model="formData.current_level"
                                    :items="[1, 2, 3, 4, 5]"
                                    density="compact"
                                    label="Nivel"
                                />
                            </div>
                            <div class="pt-4">→</div>
                            <div class="flex-1">
                                <label class="mb-1 block text-xs text-gray-600"
                                    >Nivel Objetivo</label
                                >
                                <v-select
                                    v-model="formData.required_level"
                                    :items="[1, 2, 3, 4, 5]"
                                    density="compact"
                                    label="Nivel Objetivo"
                                    hint="Nivel de maestría esperado según el diseño del puesto"
                                    persistent-hint
                                />
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs text-gray-600">
                                Timeline (meses)
                            </label>
                            <v-select
                                v-model="formData.timeline_months"
                                :items="[6, 12, 18, 24]"
                                density="compact"
                            />
                        </div>
                        <div class="mt-3">
                            <v-checkbox
                                v-model="formData.suggest_learning_path"
                                label="Proponer learning path automático"
                            />
                        </div>
                        <div
                            class="mt-3"
                            v-if="
                                formData.change_type === 'transformation' ||
                                formData.change_type === 'enrichment'
                            "
                        >
                            <v-btn text @click="handleOpenTransform"
                                >Editar Transformación (Crear versión)</v-btn
                            >
                            <span
                                v-if="formData.competency_version_id"
                                class="ml-2 text-sm text-gray-600"
                                >Versión:
                                {{ formData.competency_version_id }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- ENRICHMENT -->
                <div v-if="formData.change_type === 'enrichment'" class="mb-6">
                    <div
                        class="rounded border-l-4 border-green-500 bg-green-50 p-4"
                    >
                        <label class="mb-3 block text-sm font-semibold">
                            Competencia Nueva:
                        </label>
                        <div>
                            <label class="mb-1 block text-xs text-gray-600">
                                Nivel Objetivo de Maestría
                            </label>
                            <v-select
                                v-model="formData.required_level"
                                :items="[1, 2, 3, 4, 5]"
                                density="compact"
                                hint="Expectativa de diseño organizacional para este rol"
                                persistent-hint
                            />
                        </div>
                        <div class="mt-3">
                            <label class="mb-1 block text-xs text-gray-600">
                                Timeline (meses)
                            </label>
                            <v-select
                                v-model="formData.timeline_months"
                                :items="[6, 12, 18, 24]"
                                density="compact"
                            />
                        </div>
                        <div class="mt-3">
                            <v-checkbox
                                v-model="formData.suggest_learning_path"
                                label="Proponer learning path automático"
                            />
                        </div>
                    </div>
                </div>

                <!-- EXTINCTION -->
                <div v-if="formData.change_type === 'extinction'" class="mb-6">
                    <div
                        class="rounded border-l-4 border-red-500 bg-red-50 p-4"
                    >
                        <label class="mb-3 block text-sm font-semibold">
                            Plan de Transición:
                        </label>
                        <div class="mb-3">
                            <label class="mb-2 block text-xs text-gray-600">
                                Timeline desaparición (meses)
                            </label>
                            <v-select
                                v-model="formData.extinction_timeline"
                                :items="[6, 12, 18, 24]"
                                density="compact"
                            />
                        </div>
                        <label class="mb-2 block text-xs text-gray-600">
                            Plan de transición:
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <v-radio
                                    v-model="formData.transition_plan"
                                    value="reskilling"
                                    label="Reskilling a otra competencia"
                                />
                            </div>
                            <div class="flex items-center">
                                <v-radio
                                    v-model="formData.transition_plan"
                                    value="role_change"
                                    label="Cambio de rol"
                                />
                            </div>
                            <div class="flex items-center">
                                <v-radio
                                    v-model="formData.transition_plan"
                                    value="devinculacion"
                                    label="Desvincular"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MAINTENANCE -->
                <div v-if="formData.change_type === 'maintenance'" class="mb-6">
                    <div
                        class="rounded border-l-4 border-blue-500 bg-blue-50 p-4"
                    >
                        <p class="text-sm text-gray-600">
                            ✅ Esta competencia se mantiene en el mismo nivel.
                            No requiere cambios de entrenamiento.
                        </p>
                    </div>
                </div>

                <!-- Common Fields -->
                <div class="mb-4">
                    <label class="mb-2 block text-sm font-semibold">
                        ¿Es crítica para el rol?
                    </label>
                    <v-checkbox
                        v-model="formData.is_core"
                        label="Sí, es crítica"
                    />
                </div>

                <div class="mb-6">
                    <label class="mb-2 block text-sm font-semibold"
                        >Notas:</label
                    >
                    <v-textarea
                        v-model="formData.rationale"
                        placeholder="Justificación de esta asociación..."
                        density="compact"
                        rows="3"
                    />
                </div>
                <!-- TransformModal moved to a separate dialog to avoid nested dialogs and show BARS in its own modal -->
            </v-card-text>

            <v-card-actions class="flex justify-end gap-3 px-6 pb-4">
                <v-btn variant="text" @click="$emit('close')"> Cancelar </v-btn>
                <v-btn
                    color="primary"
                    @click="handleSave"
                    :loading="saving"
                    variant="elevated"
                >
                    Guardar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Separate dialog for transformation (contains Bars/Transform form) -->
    <v-dialog
        :model-value="showTransform"
        max-width="900px"
        @update:model-value="(val) => (showTransform = val)"
    >
        <v-card>
            <v-card-text>
                <TransformModal
                    :competencyId="props.competencyId"
                    @transformed="handleTransformed"
                    @close="showTransform = false"
                />
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import InfoLegend from '@/components/Ui/InfoLegend.vue';
import TransformModal from '@/pages/ScenarioPlanning/TransformModal.vue';
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';
import { useTransformStore } from '@/stores/transformStore';
import { computed, onMounted, ref, watch } from 'vue';

interface Props {
    visible: boolean;
    roleId: number;
    roleName: string;
    archetype?: string; // E, T, O
    competencyId: number;
    competencyName: string;
    mapping?: any;
}

interface Emits {
    (e: 'save', data: any): void;
    (e: 'close'): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const saving = ref(false);

const showTransform = ref(false);
const showLegend = ref(false);

const transformStore = useTransformStore();
const roleCompetencyStore = useRoleCompetencyStore();
const versions = ref<any[]>([]);

// Arquetipo Label
const archetypeLabel = computed(() => {
    const map: Record<string, string> = {
        E: 'Estratégico',
        T: 'Táctico',
        O: 'Operacional',
    };
    return map[props.archetype || 'T'] || 'Táctico';
});

// Consistency Logic (Semáforo de Arquitectura)
const consistencyAlert = computed(() => {
    const level = formData.value.required_level;
    const arch = props.archetype || 'O';
    const isReferent = formData.value.is_referent;

    // Rules from REGLAS_ARQUITECTURA_COHERENCIA.md (Refined)

    // 1. Sobrecarga Técnica (High Mastery in Low Accountability)
    if (arch === 'O' && level > 3 && !isReferent) {
        return {
            color: 'info',
            icon: 'mdi-lightbulb-on-outline',
            title: 'Sobrecarga Técnica Detectada',
            message: `Nivel ${level} es inusualmente alto para un Rol Operacional. Si este rol no es un Referente Técnico, considera reducir la exigencia o marcarlo como Referente.`,
        };
    }

    // 2. Referente Validado
    if (arch === 'O' && level > 3 && isReferent) {
        return {
            color: 'success',
            icon: 'mdi-account-star',
            title: 'Ingeniería Coherente: Referente',
            message: `Diseño validado. El rol operacional actúa como mentor técnico de nivel ${level}.`,
        };
    }

    // 3. Competencia de Apoyo (Bajo nivel en Arquetipos Altos)
    if ((arch === 'E' && level < 4) || (arch === 'T' && level < 3)) {
        return {
            color: 'blue-darken-1',
            icon: 'mdi-shield-star-outline',
            title: 'Competencia de Apoyo',
            message: `Nivel ${level} es válido para un Rol ${archetypeLabel.value} como competencia de soporte (no-core).`,
        };
    }

    // 4. Inconsistencia Alta (Nivel 5 en Táctico sin referencia)
    if (arch === 'T' && level > 4 && !isReferent) {
        return {
            color: 'warning',
            icon: 'mdi-alert-circle-outline',
            title: 'Posible Desalineación',
            message: `Un nivel 5 en un Rol Táctico sugiere que el rol debería ser Estratégico o actuar como Referente Global.`,
        };
    }

    // Default: Coherente
    return {
        color: 'success',
        icon: 'mdi-check-decagram',
        title: 'Diseño Coherente',
        message: `El nivel objetivo ${level} es consistente con la banda del Arquetipo ${archetypeLabel.value}.`,
    };
});

const legendItems = [
    {
        title: '✅ Mantención (Job Stabilization)',
        text: 'El diseño del puesto es maduro. El foco es la sostenibilidad del nivel de maestría actual. No requiere cambios estructurales.',
    },
    {
        title: '🔄 Transformación (Job Enrichment)',
        text: '<strong>Aumento Vertical (Herzberg)</strong>: El rol crece en complejidad y autonomía. Requiere un salto de nivel o <strong>Upskilling</strong> para mutar el ADN del rol.',
    },
    {
        title: '📈 Enriquecimiento (Job Enlargement)',
        text: '<strong>Aumento Horizontal (Hackman & Oldham)</strong>: Se añaden nuevas tareas/competencias al mismo nivel de responsabilidad para diversificar el rol.',
    },
    {
        title: '📉 Extinción (Job Substitution)',
        text: '<strong>Destrucción Creativa (Schumpeter)</strong>: El rol es desplazado por la automatización o cambio de modelo. Requiere plan de transición o reskilling.',
    },
];

// Load versions for the competency and set sensible defaults
async function loadVersions() {
    try {
        const v = await transformStore.getVersions(props.competencyId);
        versions.value = v || [];
        // default selection logic
        if (props.mapping) {
            // Mapping exists: if versions exist prefer transformation default
            if (!props.mapping.change_type && versions.value.length > 0) {
                formData.value.change_type = 'transformation';
            }
        } else {
            // No mapping yet: if no versions -> creation/enrichment, else transformation
            formData.value.change_type =
                versions.value.length > 0 ? 'transformation' : 'enrichment';
        }
    } catch {
        versions.value = [];
    }
}

// Run on mount and when competencyId changes
onMounted(() => loadVersions());
watch(
    () => props.competencyId,
    () => loadVersions(),
);
const notify = ref({ message: '', color: '' });
const incubatedSkills = ref<any[]>([]);

const formData = ref({
    id: null as number | null,
    change_type: 'maintenance' as
        | 'maintenance'
        | 'transformation'
        | 'enrichment'
        | 'extinction',
    required_level: 3,
    current_level: 1,
    is_core: true,
    is_referent: false,
    rationale: '',
    timeline_months: 12,
    extinction_timeline: 12,
    transition_plan: 'reskilling' as
        | 'reskilling'
        | 'role_change'
        | 'devinculacion',
    suggest_learning_path: false,
    competency_version_id: null as number | null,
    reduced_level_rationale: 'efficiency' as 'efficiency' | 'scope' | 'risk',
});

// Show level decrease rationale
const showLevelDecreaseRationale = computed(() => {
    if (!props.mapping) return false;
    return formData.value.required_level < props.mapping.required_level;
});

// Show referent option (when Operational role has high mastery level)
const showReferentOption = computed(() => {
    const arch = props.archetype || 'T';
    const level = formData.value.required_level;
    // Show for Operational roles with level > 3, or Tactical with level > 4
    return (arch === 'O' && level > 3) || (arch === 'T' && level > 4);
});

const changeTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        maintenance: '✅ Mantención',
        transformation: '🔄 Transformación',
        enrichment: '📈 Enriquecimiento',
        extinction: '📉 Extinción',
    };
    return labels[type] || type;
};

const handleSave = async () => {
    // If user requested a transformation but no version exists yet, open transform modal first
    if (
        formData.value.change_type === 'transformation' &&
        !formData.value.competency_version_id
    ) {
        showTransform.value = true;
        return;
    }

    saving.value = true;
    try {
        emit('save', {
            ...formData.value,
            id: props.mapping?.id,
        });
    } finally {
        saving.value = false;
    }
};

watch(
    () => props.mapping,
    (mapping) => {
        if (mapping) {
            formData.value = {
                id: mapping.id,
                change_type: mapping.change_type || 'maintenance',
                required_level: mapping.required_level || 3,
                current_level: mapping.current_level || 1,
                is_core: mapping.is_core || false,
                is_referent: mapping.is_referent || false,
                rationale: mapping.rationale || '',
                timeline_months: mapping.timeline_months || 12,
                extinction_timeline: mapping.extinction_timeline || 12,
                transition_plan: mapping.transition_plan || 'reskilling',
                suggest_learning_path: mapping.suggest_learning_path || false,
                competency_version_id: mapping.competency_version_id || null,
                reduced_level_rationale: 'efficiency',
            };
        }
    },
    { immediate: true },
);

const handleOpenTransform = () => {
    showTransform.value = true;
};

const handleTransformed = async (data: any) => {
    const newVersionId = data?.id ?? data?.version_id ?? null;
    if (newVersionId) {
        formData.value.competency_version_id = newVersionId;
    }
    // capture any created skills returned by the transform endpoint
    const created = data?.created_skills ?? data?.createdSkills ?? [];
    incubatedSkills.value = Array.isArray(created)
        ? created.filter(
              (s: any) =>
                  s && (s.is_incubated === true || s.status === 'incubation'),
          )
        : [];
    showTransform.value = false;
    // Auto-save mapping using current formData and props
    try {
        const payload = {
            id: formData.value.id,
            role_id: props.roleId,
            competency_id: props.competencyId,
            required_level: formData.value.required_level,
            is_core: formData.value.is_core,
            change_type: formData.value.change_type,
            rationale: formData.value.rationale,
            competency_version_id: formData.value.competency_version_id,
            timeline_months: formData.value.timeline_months,
            current_level: formData.value.current_level,
        };
        emit('save', payload);
        // Also perform the save here to get immediate feedback
        try {
            await roleCompetencyStore.saveMapping(payload as any);
            notify.value = {
                message: 'Mapeo guardado correctamente',
                color: 'success',
            };
            setTimeout(() => (notify.value.message = ''), 3000);
        } catch (err: any) {
            notify.value = {
                message: err?.message || 'Error guardando el mapeo',
                color: 'error',
            };
            setTimeout(() => (notify.value.message = ''), 5000);
        }
    } catch (err) {
        // swallowing; UI save will still be available
        console.error('Auto-save mapping failed', err);
    }
};
</script>
