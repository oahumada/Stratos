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
                            }}/{{ totalFields }})
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

        <component :is="currentStepComponent" />

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
                ID: {{ store.generationId }} — Estado:
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
    </div>
</template>

<script setup lang="ts">
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';
import { computed, ref } from 'vue';
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

// finer-grained progress: count individual required fields across steps
const totalFields = 12;
const filledFields = computed(() => {
    const d = store.data as any;
    let c = 0;
    if (d.company_name) c++;
    if (d.industry) c++;
    if (d.company_size) c++;
    if (d.current_challenges) c++;
    if (d.current_capabilities) c++;
    if (d.current_gaps) c++;
    if (d.strategic_goal) c++;
    if (d.key_initiatives) c++;
    if (d.budget_level) c++;
    if (d.talent_availability) c++;
    if (d.time_horizon) c++;
    if (d.milestones) c++;
    return c;
});

const progress = computed(() =>
    Math.round((filledFields.value / totalFields) * 100),
);

const progressColor = computed(() =>
    progress.value < 40 ? 'error' : progress.value < 80 ? 'warning' : 'success',
);

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

// expose filled/total for template
const totalFieldsConst = totalFields;

function next() {
    store.next();
}
function prev() {
    store.prev();
}

const showPreview = ref(false);

async function onGenerate() {
    try {
        // First get prompt preview from server
        await store.preview();
        showPreview.value = true;
    } catch (e) {
        console.error(e);
    }
}

async function onConfirmGenerate(importAfter = false) {
    showPreview.value = false;
    try {
        // persist operator choice to the generation store so accept flow can include it
        store.importAfterAccept = !!importAfter;
        await store.generate();
    } catch (e) {
        console.error(e);
    }
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
        alert(
            'Error al aceptar generación: ' +
                (error?.response?.data?.message || error?.message || String(e)),
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
