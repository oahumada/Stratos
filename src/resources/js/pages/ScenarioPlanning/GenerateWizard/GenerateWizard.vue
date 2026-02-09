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
                <div class="mt-2">
                    <v-btn small color="secondary" @click="prefillDemo">
                        Cargar demo: Adopción IA generativa
                    </v-btn>
                </div>
            </v-col>
        </v-row>

        <!-- Instruction editor moved to final step -->

        <component :is="currentStepComponent" />

        <div v-if="store.step === 5">
            <v-card class="pa-3 mb-4">
                <div class="d-flex align-center justify-space-between mb-2">
                    <div>
                        <strong>Instrucciones del generador</strong>
                        <div class="caption d-flex align-center">
                            <div>
                                Plantilla que se aplica al prompt generado.
                                Soporta Markdown.
                            </div>
                            <v-tooltip>
                                <template #activator="{ props }">
                                    <v-icon
                                        v-bind="props"
                                        class="ml-2"
                                        small
                                        color="primary"
                                    >
                                        mdi-information
                                    </v-icon>
                                </template>
                                <span>
                                    Recomendado: la instrucción debe exigir
                                    salida JSON única para integridad del
                                    import.
                                </span>
                            </v-tooltip>
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
                        @change="
                            (v: null) =>
                                selectInstruction(v !== null ? Number(v) : null)
                        "
                    >
                        <div
                            v-for="(it, idx) in instructionItems"
                            :key="'instr-' + (it.id ?? idx)"
                            style="
                                padding: 6px 0;
                                border-bottom: 1px solid rgba(0, 0, 0, 0.04);
                            "
                        >
                            <v-radio
                                :label="
                                    (it.editable
                                        ? 'Versión editable (Operador)'
                                        : 'Versión por defecto (Sistema)') +
                                    ' / ' +
                                    (it.author_name || 'system') +
                                    ' (' +
                                    (it.created_at
                                        ? new Date(
                                              it.created_at,
                                          ).toLocaleString()
                                        : '') +
                                    ')'
                                "
                                :value="idx"
                            />
                        </div>
                        <v-radio
                            label="Editar/Usar como instrucción personalizada"
                            :value="null"
                        />
                    </v-radio-group>
                </div>

                <v-textarea
                    v-model="instructionContent"
                    :readonly="!instructionEditable"
                    rows="6"
                    outlined
                    dense
                    placeholder="Escribe las instrucciones aquí (Markdown)..."
                    :class="{
                        'instruction-error': !instructionValidation.valid,
                    }"
                />

                <div
                    v-if="!instructionValidation.valid"
                    class="instruction-error-text caption"
                >
                    {{ instructionValidation.message }}
                </div>

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
                    @click="openResponseModal"
                >
                    Ver respuesta
                </button>
                <button
                    v-if="store.generationStatus === 'complete'"
                    @click="
                        () => {
                            store.importAfterAccept = false;
                            acceptGeneration();
                        }
                    "
                    :disabled="accepting"
                    style="margin-left: 8px"
                >
                    Aceptar y crear escenario
                </button>
            </div>
            <button @click="refreshStatus">Actualizar</button>
            <pre v-if="store.generationResult">{{
                JSON.stringify(store.generationResult, null, 2)
            }}</pre>
        </div>

        <v-dialog
            v-model="responseModalOpen"
            max-width="900"
            @click:outside="closeResponseModal"
        >
            <v-card>
                <v-card-title>Respuesta del LLM</v-card-title>
                <v-card-text>
                    <div
                        v-if="responseLoading"
                        class="d-flex align-center"
                        style="min-height: 120px"
                    >
                        <v-progress-circular
                            indeterminate
                            color="primary"
                            class="mr-4"
                        />
                        <div>
                            <div>
                                <strong>Esperando respuesta del LLM…</strong>
                            </div>
                            <div class="caption">
                                La generación está en progreso. Esto puede
                                tardar unos segundos.
                            </div>
                            <div v-if="chunkCount !== null" class="caption">
                                Chunks recibidos: {{ chunkCount }}
                            </div>
                        </div>
                    </div>

                    <div v-else>
                        <div class="mb-2">
                            <strong>Resumen:</strong>
                            <div>
                                Nombre:
                                {{
                                    store.generationResult?.scenario_metadata
                                        ?.name || '—'
                                }}
                            </div>
                            <div>
                                Confianza:
                                {{
                                    store.generationResult?.confidence_score ??
                                    store.generationResult?.scenario_metadata
                                        ?.confidence_score ??
                                    '—'
                                }}
                            </div>
                            <div>
                                Capacidades:
                                {{
                                    (store.generationResult?.capabilities || [])
                                        .length
                                }}
                            </div>
                        </div>
                        <pre
                            style="
                                max-height: 400px;
                                overflow: auto;
                                background: #f6f6f6;
                                padding: 8px;
                            "
                            >{{
                                JSON.stringify(store.generationResult, null, 2)
                            }}</pre
                        >
                    </div>
                    <div
                        v-if="
                            responseValidationErrors &&
                            responseValidationErrors.length
                        "
                        class="mb-3"
                    >
                        <div
                            class="subtitle-2"
                            style="
                                color: var(--v-theme-error);
                                font-weight: 600;
                            "
                        >
                            {{
                                responseValidationTitle ||
                                'Errores de validación'
                            }}
                        </div>
                        <ul
                            style="
                                margin-top: 6px;
                                padding-left: 1rem;
                                color: var(--v-theme-error);
                            "
                        >
                            <li
                                v-for="(m, i) in responseValidationErrors"
                                :key="i"
                            >
                                {{ m }}
                            </li>
                        </ul>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer />
                    <v-btn color="secondary" text @click="closeResponseModal"
                        >Cerrar</v-btn
                    >
                    <v-btn
                        color="primary"
                        @click="onModalAccept(false)"
                        :loading="accepting"
                        :disabled="
                            responseLoading ||
                            store.generationStatus !== 'complete'
                        "
                        >Aceptar y crear escenario</v-btn
                    >
                    <v-btn
                        color="success"
                        @click="onModalAccept(true)"
                        :loading="accepting"
                        :disabled="
                            responseLoading ||
                            store.generationStatus !== 'complete'
                        "
                        >Aceptar y crear + Importar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>

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

// (step completion logic moved to computed helpers)

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

// Client-side compatibility helpers
function containsMarkdownDirective(text: string) {
    if (!text) return false;
    const lower = text.toLowerCase();
    return (
        lower.includes('formato: markdown') ||
        lower.includes('format: markdown') ||
        lower.includes('markdown.') ||
        lower.includes('\nmarkdown')
    );
}

function clientInstructionIsCompatible() {
    // If operator is using a custom/client instruction, check for obvious conflicts
    if (instructionChoice.value === 'client' && instructionContent.value) {
        if (containsMarkdownDirective(instructionContent.value)) {
            return {
                valid: false,
                message:
                    'La instrucción contiene indicaciones para formato Markdown. Cambie la instrucción a salida JSON única, o seleccione una instrucción de la base de datos.',
            };
        }
    }
    return { valid: true };
}
// reactive validation object for template binding
const instructionValidation = computed(() => clientInstructionIsCompatible());
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

        // Client-side instruction compatibility check to avoid server 422s
        const compat = clientInstructionIsCompatible();
        if (!compat.valid) {
            showError(
                compat.message || 'Instrucción incompatible',
                'Instrucción incompatible',
            );
            // open editor so operator can fix
            instructionEditable.value = true;
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
        const error = e as any;
        console.error('Preview failed', e);
        if (error?.response?.status === 422) {
            const d = error.response.data || {};
            const messages = d.errors
                ? Object.entries(d.errors).map(
                      ([k, v]) =>
                          `${k}: ${Array.isArray(v) ? v.join(', ') : v}`,
                  )
                : [d.message || JSON.stringify(d)];
            // allow operator to edit instruction
            instructionEditable.value = true;
            showError(messages, 'Validación de instrucción (422)');
            return;
        }
        const errMsg = formatAxiosError(e);
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

        // Client-side instruction compatibility check before final submit
        const compat = clientInstructionIsCompatible();
        if (!compat.valid) {
            showError(
                compat.message || 'Instrucción incompatible',
                'Instrucción incompatible',
            );
            instructionEditable.value = true;
            return;
        }

        // persist operator choice to the generation store so accept flow can include it
        store.importAfterAccept = !!importAfter;
        // If operator edited/entered a custom instruction, send it in payload
        if (instructionChoice.value === 'client' && instructionContent.value) {
            store.setField('instruction', instructionContent.value);
            store.setField('instruction_id', null);
        } else if (
            instructionChoice.value === 'db' &&
            selectedInstructionIndex.value !== null
        ) {
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

        const res = await store.generate();
        // provide immediate feedback and start polling for completion
        try {
            await store.fetchStatus();
        } catch (e) {
            // ignore fetch errors for now
        }

        // If not complete, poll until completion (timeout 120s)
        const start = Date.now();
        const timeoutMs = 120000;
        while (
            store.generationStatus !== 'complete' &&
            Date.now() - start < timeoutMs
        ) {
            // wait a bit then refresh
            // show a small visual cue by setting generating (already managed by store)
            await new Promise((r) => setTimeout(r, 2000));
            try {
                await store.fetchStatus();
            } catch (e) {
                // ignore intermittent errors
            }
        }

        // If generation completed, open response modal so operator can accept/import
        if (store.generationStatus === 'complete') {
            // ensure result is current
            await store.fetchStatus();
            responseModalOpen.value = true;
        } else {
            // not completed within timeout: notify operator to monitor status
            showError(
                'Generación encolada. Verifique el estado en la sección de generación.',
                'Encolada',
            );
        }
    } catch (e) {
        const error = e as any;
        console.error('Generate failed', e);
        if (error?.response?.status === 422) {
            const d = error.response.data || {};
            const messages = d.errors
                ? Object.entries(d.errors).map(
                      ([k, v]) =>
                          `${k}: ${Array.isArray(v) ? v.join(', ') : v}`,
                  )
                : [d.message || JSON.stringify(d)];
            instructionEditable.value = true;
            showError(messages, 'Validación de instrucción (422)');
            return;
        }
        const errMsg = formatAxiosError(e);
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
const responseValidationErrors = ref<string[] | null>(null);
const responseValidationTitle = ref('');
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

const responseModalOpen = ref(false);
let responsePollTimer: any = null;
const responseLoading = ref(false);
const chunkCount = ref<number | null>(null);

function openResponseModal() {
    responseModalOpen.value = true;
    // fetch current status and if not complete start polling
    startResponsePolling();
}

async function onModalAccept(importAfter = false) {
    if (!store.generationId) return;
    store.importAfterAccept = !!importAfter;
    // clear previous inline validation errors
    responseValidationErrors.value = null;
    responseValidationTitle.value = '';

    // keep modal open while attempting accept so we can show inline 422 errors
    accepting.value = true;
    try {
        const res = await store.accept(store.generationId);
        const sid =
            res?.data?.scenario_id ||
            res?.data?.id ||
            (res?.data && res.data.id);
        if (sid) {
            window.location.href = `/scenario-planning/${sid}`;
            return;
        }
        window.location.reload();
    } catch (e) {
        const err = e as any;
        // If server returns 422, surface errors inline in modal so operator can edit instruction
        if (err?.response?.status === 422) {
            const d = err.response.data || {};
            const messages = d.errors
                ? Object.entries(d.errors).map(
                      ([k, v]) =>
                          `${k}: ${Array.isArray(v) ? v.join(', ') : v}`,
                  )
                : [d.message || JSON.stringify(d)];
            responseValidationTitle.value = 'Validación del servidor (422)';
            responseValidationErrors.value = messages;
            // re-open modal if it was closed
            responseModalOpen.value = true;
            // enable instruction editing to allow operator fix
            instructionEditable.value = true;
            return;
        }
        // fallback: use global error modal
        console.error('Accept failed', e);
        showError(
            err?.response?.data?.message || err?.message || String(e),
            'Error al aceptar generación',
        );
    } finally {
        accepting.value = false;
    }
}

function closeResponseModal() {
    responseModalOpen.value = false;
    stopResponsePolling();
}

function stopResponsePolling() {
    responseLoading.value = false;
    if (responsePollTimer) {
        clearInterval(responsePollTimer);
        responsePollTimer = null;
    }
}

async function startResponsePolling() {
    // prevent double polling
    if (responsePollTimer) return;
    responseLoading.value = true;
    // try immediate fetch
    try {
        await store.fetchStatus();
        // also fetch chunk count immediately
        await fetchChunkCount();
    } catch (e) {
        // ignore
    }

    if (store.generationStatus === 'complete') {
        responseLoading.value = false;
        responseModalOpen.value = true;
        return;
    }

    responsePollTimer = setInterval(async () => {
        try {
            await store.fetchStatus();
            await fetchChunkCount();
            if (store.generationStatus === 'complete') {
                // stop polling and show result
                stopResponsePolling();
                responseModalOpen.value = true;
            }
        } catch (e) {
            // ignore intermittent errors
        }
    }, 2000);
}

async function fetchChunkCount() {
    try {
        if (!store.generationId) {
            chunkCount.value = null;
            return;
        }
        const res = await axios.get(
            `/api/strategic-planning/scenarios/generate/${store.generationId}/chunks`,
        );
        if (res.data && Array.isArray(res.data.data)) {
            chunkCount.value = res.data.data.length;
        } else {
            chunkCount.value = null;
        }
    } catch (e) {
        chunkCount.value = null;
    }
}

function prefillDemo() {
    store.prefillDemo();
    // set a recommended instruction forcing JSON output for import integrity
    instructionContent.value = `Por favor, genera UN SOLO objeto JSON que describa el escenario generado con las siguientes claves: \n- scenario_metadata: { name, description, scenario_type, horizon_months, fiscal_year }\n- capabilities: [ { name, competencies: [ { name, required_level } ], importance } ]\n- notes: string\nDevuelve únicamente JSON válido sin texto adicional.`;
    instructionEditable.value = true;
    // mark instruction as client-provided so it is sent to the server
    instructionChoice.value = 'client';
    selectedInstructionIndex.value = null;
    // persist instruction into payload so generate() will include it even if operator skips preview
    store.setField('instruction', instructionContent.value);
    // navigate operator to final step so they can review instruction and generate immediately
    store.step = 5;
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

.instruction-error {
    box-shadow: none !important;
    border: 1px solid var(--v-theme-error) !important;
}
.instruction-error-text {
    color: var(--v-theme-error);
    margin-top: 6px;
    font-weight: 600;
}
</style>
