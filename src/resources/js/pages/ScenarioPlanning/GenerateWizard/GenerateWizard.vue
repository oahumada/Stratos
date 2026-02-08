<template>
    <div class="generate-wizard">
        <h2>Generar Escenario (Asistido)</h2>

        <v-row class="align-center mb-4">
            <v-col cols="12" md="8">
                <v-progress-linear
                    :value="progress"
                    :color="progressColor"
                    height="16"
                    rounded
                >
                    <template #default>
                        <div class="progress-label">
                            Paso {{ store.step }} de 5 / {{ progress }}% ({{
                                filledFields
                            }}/{{ allFields.length }})
                        </div>
                    </template>
                </v-progress-linear>
            </v-col>
            <v-col cols="12" md="4" class="text-right">
                <v-menu offset-y>
                    <template #activator="{ props }">
                        <v-chip
                            v-bind="props"
                            :color="progressColor"
                            text-color="white"
                            class="cursor-pointer"
                        >
                            {{ progressLabel }}
                        </v-chip>
                    </template>
                    <v-card style="max-width: 320px">
                        <v-card-text>
                            <div class="font-weight-medium">
                                Campos faltantes (Paso {{ store.step }})
                            </div>
                            <div style="margin-top: 6px">
                                <div
                                    v-if="missingCriticalCount > 0"
                                    style="
                                        color: var(--v-theme-error);
                                        font-weight: 600;
                                        margin-bottom: 6px;
                                    "
                                >
                                    Campos críticos faltantes:
                                    {{ missingCriticalCount }}
                                </div>
                                <ul
                                    style="
                                        margin: 0.25rem 0 0;
                                        padding-left: 1rem;
                                    "
                                >
                                    <li
                                        v-for="(m, idx) in missingForCurrent"
                                        :key="idx"
                                    >
                                        {{ m }}
                                    </li>
                                    <li v-if="missingForCurrent.length === 0">
                                        Ninguno: paso completo
                                    </li>
                                </ul>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-menu>
            </v-col>
        </v-row>

        <!-- Instruction editor moved to final step -->

        <component :is="currentStepComponent" />

        <div v-if="store.step === 5">
            <v-card class="pa-3 mb-4">
                <div class="d-flex align-center justify-space-between mb-2">
                    <div>
                        <strong>Instrucciones del generador</strong>
                        <div class="caption">
                            Plantilla que se aplica al prompt generado. Soporta
                            Markdown.
                        </div>
                    </div>
                    <div class="d-flex align-center">
                        <v-select
                            v-model="instructionLang"
                            :items="['es', 'en']"
                            dense
                            hide-details
                            style="width: 90px"
                        />
                        <v-btn
                            small
                            class="ml-2"
                            :loading="instructionLoading"
                            @click="loadInstruction(instructionLang)"
                            >Cargar</v-btn
                        >
                    </div>
                </div>

                <div v-if="instructionItems.length" class="mb-3">
                    <v-radio-group
                        v-model="selectedInstructionIndex"
                        class="mb-2"
                        @change="(v) => selectInstruction(v !== null ? Number(v) : null)"
                    >
                        <div
                            v-for="(it, idx) in instructionItems"
                            :key="'instr-'+(it.id ?? idx)"
                            style="padding: 6px 0; border-bottom: 1px solid rgba(0,0,0,0.04)"
                        >
                            <v-radio :label="(it.editable ? 'Versión editable (Operador)' : 'Versión por defecto (Sistema)') + ' — ' + (it.author_name || 'system') + ' (' + (it.created_at ? new Date(it.created_at).toLocaleString() : '') + ')'"
                                :value="idx"
                            />
                        </div>
                        <v-radio label="Editar/Usar como instrucción personalizada" :value="null" />
                    </v-radio-group>
                </div>

                <v-textarea
                    v-model="instructionContent"
                    :readonly="!instructionEditable"
                    rows="6"
                    outlined
                    dense
                    placeholder="Escribe las instrucciones aquí (Markdown)..."
                />

                <div class="d-flex align-center mt-2">
                    <v-checkbox
                        v-model="instructionEditable"
                        label="Habilitar edición"
                    />
                    <v-spacer />
                    <v-btn
                        small
                        color="secondary"
                        class="mr-2"
                        :loading="instructionLoading"
                        @click="restoreDefault"
                        >Restablecer por defecto</v-btn
                    >
                    <v-btn
                        color="primary"
                        :loading="instructionLoading"
                        @click="saveInstruction"
                        >Guardar instrucciones</v-btn
                    >
                </div>
            </v-card>
        </div>

        <div class="wizard-controls d-flex mt-4">
            <v-btn color="info" @click="prev" :disabled="store.step === 1">
                <v-icon left>mdi-arrow-left</v-icon>
                Atrás
            </v-btn>

            <v-spacer />

            <v-btn
                v-if="store.step < 5"
                color="primary"
                class="mx-2"
                depressed
                @click="next"
            >
                Siguiente
                <v-icon right>mdi-arrow-right</v-icon>
            </v-btn>

            <v-btn
                v-else
                color="success"
                class="mx-2"
                depressed
                @click="onGenerate"
                :loading="store.generating"
            >
                <v-icon left>mdi-rocket-launch</v-icon>
                Generar
            </v-btn>
        </div>

        <div v-if="store.generationId" class="generation-status">
            <p>
                ID: {{ store.generationId }} '-' Estado:
                {{ store.generationStatus }}
            </p>
            <div style="margin-top: 8px">
                <button
                    v-if="store.generationStatus === 'complete'"
                    @click="acceptGeneration"
                    :disabled="accepting"
                >
                    Aceptar y crear escenario
                </button>
            </div>
            <button @click="refreshStatus">Actualizar</button>
            <pre v-if="store.generationResult">{{
                JSON.stringify(store.generationResult, null, 2)
            }}</pre>
        </div>

        <div v-if="showPreview" class="preview-modal-backdrop">
            <div class="preview-modal">
                <PreviewConfirm
                    :promptPreview="store.previewPrompt || ''"
                    @confirm="onConfirmGenerate"
                    @edit="onEditPrompt"
                />
                <button class="close" @click="showPreview = false">
                    Cerrar
                </button>
            </div>
        </div>

        <ErrorModal
            v-model="errorModalShow"
            :title="errorModalTitle"
            :messages="errorModalMessages"
        />
    </div>
</template>

<script setup lang="ts">
import ErrorModal from '@/components/Ui/ErrorModal.vue';
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';
import PreviewConfirm from './PreviewConfirm.vue';
import StepHorizon from './StepHorizon.vue';
import StepIdentity from './StepIdentity.vue';
import StepIntent from './StepIntent.vue';
import StepResources from './StepResources.vue';
import StepSituation from './StepSituation.vue';

const store = useScenarioGenerationStore();

const stepComponents: Record<number, any> = {
    1: StepIdentity,
    2: StepSituation,
    3: StepIntent,
    4: StepResources,
    5: StepHorizon,
};

const currentStepComponent = computed(() => stepComponents[store.step]);

onMounted(() => {
    loadInstruction(instructionLang.value);
});

// compute completion per step (basic heuristics)
const stepCompleted = (step: number) => {
    const d = store.data as any;
    switch (step) {
        case 1:
            return !!(d.company_name && d.industry && d.company_size);
        case 2:
            return !!(d.current_challenges && d.current_capabilities);
        case 3:
            return !!(d.strategic_goal && d.key_initiatives);
        case 4:
            return !!(d.budget_level && d.talent_availability);
        case 5:
            return !!(d.time_horizon && d.milestones);
        default:
            return false;
    }
};

// finer-grained progress with critical vs optional weighting
const criticalFields = [
    'company_name',
    'industry',
    'company_size',
    'current_challenges',
    'current_capabilities',
    'strategic_goal',
    'key_initiatives',
    'time_horizon',
];

const allFields = [
    ...criticalFields,
    'current_gaps',
    'budget_level',
    'talent_availability',
    'milestones',
];

const totalWeight =
    criticalFields.length * 2 + (allFields.length - criticalFields.length) * 1;

const filledWeight = computed(() => {
    const d = store.data as any;
    let w = 0;
    allFields.forEach((f) => {
        if (d[f]) w += criticalFields.includes(f) ? 2 : 1;
    });
    return w;
});

const filledFields = computed(() => {
    // keep simple count of filled field items for display
    const d = store.data as any;
    return allFields.reduce((acc, f) => (d[f] ? acc + 1 : acc), 0);
});

const progress = computed(() =>
    Math.round((filledWeight.value / totalWeight) * 100),
);

const progressColor = computed(() =>
    progress.value < 40 ? 'error' : progress.value < 80 ? 'warning' : 'success',
);

// fields per step to compute critical missing count
const stepFieldKeys: Record<number, string[]> = {
    1: ['company_name', 'industry', 'company_size'],
    2: ['current_challenges', 'current_capabilities', 'current_gaps'],
    3: ['strategic_goal', 'key_initiatives'],
    4: ['budget_level', 'talent_availability'],
    5: ['time_horizon', 'milestones'],
};

const missingCriticalCount = computed(() => {
    const keys = stepFieldKeys[store.step] || [];
    const d = store.data as any;
    return keys.filter((k) => criticalFields.includes(k) && !d[k]).length;
});

const progressLabel = computed(() =>
    progress.value < 40
        ? 'Insuficiente'
        : progress.value < 80
          ? 'Parcial'
          : 'Completado',
);

const missingFieldsForStep = (step: number) => {
    const d = store.data as any;
    const missing: string[] = [];
    switch (step) {
        case 1:
            if (!d.company_name) missing.push('Nombre de la organización');
            if (!d.industry) missing.push('Industria');
            if (!d.company_size) missing.push('Tamaño (personas)');
            break;
        case 2:
            if (!d.current_challenges) missing.push('Desafíos');
            if (!d.current_capabilities) missing.push('Capacidades existentes');
            if (!d.current_gaps) missing.push('Brechas');
            break;
        case 3:
            if (!d.strategic_goal) missing.push('Objetivo principal');
            if (!d.key_initiatives) missing.push('Iniciativas clave');
            break;
        case 4:
            if (!d.budget_level) missing.push('Nivel de presupuesto');
            if (!d.talent_availability)
                missing.push('Disponibilidad de talento');
            break;
        case 5:
            if (!d.time_horizon) missing.push('Plazo');
            if (!d.milestones) missing.push('Hitos');
            break;
    }
    return missing;
};

const missingForCurrent = computed(() => missingFieldsForStep(store.step));

// expose filled/total for template (use `allFields.length` and `filledFields`)

function next() {
    store.next();
}
function prev() {
    store.prev();
}

const showPreview = ref(false);

// Error modal state
const instructionLang = ref('es');
const errorModalShow = ref(false);
const errorModalTitle = ref('');
const errorModalMessages = ref<string[]>([]);

function showError(messages: string[] | string, title = 'Error') {
    errorModalTitle.value = title;
    errorModalMessages.value = Array.isArray(messages) ? messages : [messages];
    errorModalShow.value = true;
}
const instructionContent = ref('');
const instructionEditable = ref(false);
const instructionLoading = ref(false);
const instructionItems = ref<any[]>([]);
const selectedInstructionIndex = ref<number | null>(null);
const instructionChoice = ref<'db' | 'client'>('db');

async function loadInstruction(lang = 'es') {
    instructionLoading.value = true;
    try {
        const res = await axios.get(
            '/api/strategic-planning/scenarios/instructions',
            { params: { language: lang } },
        );
        const items = res.data.data || [];
        instructionItems.value = items;
        if (items.length) {
            // default select first item (editable if present)
            selectedInstructionIndex.value = 0;
            instructionContent.value = items[0].content || '';
            instructionEditable.value = !!items[0].editable;
            instructionLang.value = items[0].language || lang;
            instructionChoice.value = 'db';
        } else {
            instructionItems.value = [];
            selectedInstructionIndex.value = null;
            instructionContent.value = '';
            instructionEditable.value = false;
            instructionLang.value = lang;
            instructionChoice.value = 'client';
        }
    } catch (e) {
        console.error('Failed loading instruction', e);
    } finally {
        instructionLoading.value = false;
    }
}

function selectInstruction(idx: number | null) {
    selectedInstructionIndex.value = idx;
    if (idx === null) {
        // custom
        instructionChoice.value = 'client';
        instructionEditable.value = true;
        return;
    }
    const item = instructionItems.value[idx];
    instructionContent.value = item?.content || '';
    instructionEditable.value = !!item?.editable;
    instructionChoice.value = 'db';
}

async function saveInstruction() {
    instructionLoading.value = true;
    try {
        const payload = {
            language: instructionLang.value,
            content: instructionContent.value,
            editable: !!instructionEditable.value,
        };
        const res = await axios.post(
            '/api/strategic-planning/scenarios/instructions',
            payload,
        );
        if (res.data && res.data.success) {
            // reload latest
            await loadInstruction(instructionLang.value);
        }
    } catch (e) {
        console.error('Save instruction failed', e);
        showError(
            'No se pudo guardar la instrucción: ' +
                ((e as any)?.response?.data?.message || (e as any)?.message),
        );
    } finally {
        instructionLoading.value = false;
    }
}

async function restoreDefault() {
    if (
        !confirm(
            '¿Restablecer la instrucción por defecto? Esto creará una nueva versión editable con el contenido por defecto.',
        )
    )
        return;
    instructionLoading.value = true;
    try {
        const res = await axios.post(
            '/api/strategic-planning/scenarios/instructions/restore-default',
            {
                language: instructionLang.value,
            },
        );
        if (res.data && res.data.success) {
            await loadInstruction(instructionLang.value);
        } else {
            showError('No se pudo restaurar la instrucción por defecto');
        }
    } catch (e) {
        console.error('Restore default failed', e);
        showError(
            (e as any)?.response?.data?.message ||
                (e as any)?.message ||
                'Error al restaurar por defecto',
        );
    } finally {
        instructionLoading.value = false;
    }
}

async function onGenerate() {
    try {
        // validate required fields before contacting server
        const validation = await store.validate();
        if (!validation.valid) {
            showError(validation.errors, 'Faltan campos críticos');
            return;
        }

        // First get prompt preview from server
        await store.preview();

        // Append current editable instruction content to the preview so operator sees the exact instruction
        if (instructionContent.value) {
            store.previewPrompt =
                (((store.previewPrompt as unknown as string) ?? '') +
                    '\n\n' +
                    instructionContent.value) as any;
        }

        showPreview.value = true;
    } catch (e) {
        const errMsg = formatAxiosError(e);
        console.error('Preview failed', e);
        showError(errMsg.split('\n'), 'Error al generar preview');
    }
}

async function onConfirmGenerate(importAfter = false) {
    showPreview.value = false;
    try {
        // validate again before final generate
        const validation = await store.validate();
        if (!validation.valid) {
            showError(validation.errors, 'Faltan campos críticos');
            return;
        }

        // persist operator choice to the generation store so accept flow can include it
        store.importAfterAccept = !!importAfter;
        // If operator edited/entered a custom instruction, send it in payload
        if (instructionChoice.value === 'client' && instructionContent.value) {
            store.setField('instruction', instructionContent.value);
            store.setField('instruction_id', null);
        } else if (instructionChoice.value === 'db' && selectedInstructionIndex.value !== null) {
            // operator selected a DB instruction version: send the explicit id so server uses it
            const it = instructionItems.value[selectedInstructionIndex.value];
            const id = it?.id ?? null;
            store.setField('instruction', null);
            store.setField('instruction_id', id);
        } else {
            // fallback: clear both and let server choose default behavior
            store.setField('instruction', null);
            store.setField('instruction_id', null);
        }

        await store.generate();
    } catch (e) {
        const errMsg = formatAxiosError(e);
        console.error('Generate failed', e);
        showError(errMsg.split('\n'), 'Error al generar escenario');
    }
}

function formatAxiosError(e: any) {
    if (!e) return 'Unknown error';
    if (e.response && e.response.data) {
        const d = e.response.data;
        if (d.errors) {
            return Object.entries(d.errors)
                .map(([k, v]) => `${k}: ${Array.isArray(v) ? v.join(', ') : v}`)
                .join('\n');
        }
        return d.message || JSON.stringify(d);
    }
    return e.message || String(e);
}

function onEditPrompt() {
    // Close preview and allow user to edit wizard fields
    showPreview.value = false;
    store.step = 1;
}

async function refreshStatus() {
    await store.fetchStatus();
}

const accepting = ref(false);
async function acceptGeneration() {
    if (!store.generationId) return;
    accepting.value = true;
    try {
        const res = await store.accept(store.generationId);
        // if backend returns scenario id, navigate to it
        const sid =
            res?.data?.scenario_id ||
            res?.data?.id ||
            (res?.data && res.data.id);
        if (sid) {
            // navigate to scenario detail
            window.location.href = `/scenario-planning/${sid}`;
            return;
        }
        // fallback: close dialog by reloading page
        window.location.reload();
    } catch (e) {
        console.error('Accept failed', e);
        const error = e as any;
        showError(
            error?.response?.data?.message || error?.message || String(e),
            'Error al aceptar generación',
        );
    } finally {
        accepting.value = false;
    }
}
</script>

<style scoped>
.generate-wizard {
    padding: 1rem;
}
.wizard-controls {
    margin-top: 1rem;
}
.wizard-controls button {
    margin-right: 0.5rem;
}
.generation-status {
    margin-top: 1rem;
    background: #f7f7f7;
    padding: 0.5rem;
}
</style>
