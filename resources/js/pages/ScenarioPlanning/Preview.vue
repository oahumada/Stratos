<template>
    <AppLayout title="Preview de Escenario">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-4">
                        {{ scenario.scenario_metadata.name }}
                    </h1>
                </v-col>
            </v-row>

            <v-row v-if="errorMessage || successMessage" class="mb-4">
                <v-col cols="12">
                    <v-alert v-if="errorMessage" type="error" variant="tonal" dense>
                        {{ errorMessage }}
                    </v-alert>
                    <v-alert v-else-if="successMessage" type="success" variant="tonal" dense>
                        {{ successMessage }}
                    </v-alert>
                </v-col>
            </v-row>

            <v-snackbar v-model="snackbarVisible" :timeout="8000" :color="snackbarType" multi-line>
                <div class="d-flex align-center">
                    <v-icon v-if="snackbarType === 'error'" class="mr-2">mdi-alert-circle</v-icon>
                    <v-icon v-else-if="snackbarType === 'success'" class="mr-2">mdi-check-circle</v-icon>
                    <div class="flex-grow-1">{{ snackbarMessage }}</div>
                    <v-btn v-if="snackbarActionLabel" text @click="performSnackbarAction">{{ snackbarActionLabel }}</v-btn>
                </div>
            </v-snackbar>

            <v-row>
                <v-col cols="12" md="4">
                    <SynthetizationIndexCard :suggestedRoles="displayedRoles" />
                </v-col>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-text>
                            <div class="text-overline">
                                Confianza del Modelo
                            </div>
                            <div class="text-h4">
                                {{
                                    (scenario.confidence_score * 100).toFixed(0)
                                }}%
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-4">
                <v-col cols="12">
                    <h2 class="text-h5 mb-3">Roles Sugeridos</h2>
                </v-col>
                <v-col
                    v-for="(role, index) in displayedRoles"
                    :key="index"
                    cols="12"
                    md="6"
                >
                    <TalentCompositionCard :role="role" />
                </v-col>
            </v-row>

            <v-row class="mt-6">
                <v-col>
                    <v-btn
                        color="primary"
                        size="large"
                        :loading="orchestrating"
                        @click="orchestrateScenario"
                    >
                        Aceptar y Orquestar
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import SynthetizationIndexCard from '@/components/TalentEngineering/SynthetizationIndexCard.vue'
import TalentCompositionCard from '@/components/TalentEngineering/TalentCompositionCard.vue'
import { router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import type { Role } from '../../../../types/talent'

interface Props {
    scenario: {
        id: number
        scenario_metadata: {
            name: string
        }
        confidence_score: number
        suggested_roles: Role[]
    }
}

const props = defineProps<Props>()
const orchestrating = ref(false)
const errorMessage = ref<string | null>(null)
const successMessage = ref<string | null>(null)

// local displayed roles (either passed via props or loaded from compacted endpoint)
const displayedRoles = ref<Role[]>(props.scenario.suggested_roles || [])

// snackbar state for user actions (retry etc.)
const snackbarVisible = ref(false)
const snackbarMessage = ref<string | null>(null)
const snackbarActionLabel = ref<string | null>(null)
const snackbarType = ref<'error' | 'success'>('error')
const lastSnackbarAction = ref<string | null>(null)

function showSnackbar(message: string, actionLabel?: string, actionKey?: string, type: 'error' | 'success' = 'error') {
    snackbarMessage.value = message
    snackbarActionLabel.value = actionLabel ?? null
    lastSnackbarAction.value = actionKey ?? null
    snackbarType.value = type
    snackbarVisible.value = true
}

async function loadCompacted() {
    try {
        const res = await fetch(
            `/api/strategic-planning/scenarios/generate/${props.scenario.id}/compacted`,
            { method: 'GET', headers: { 'Accept': 'application/json' } },
        )
        if (res.ok) {
            const json = await res.json()
            if (json?.suggested_roles) displayedRoles.value = json.suggested_roles
        } else {
            throw new Error(`status:${res.status}`)
        }
    } catch (e) {
        console.warn('failed to load compacted generation', e)
        errorMessage.value = 'No se pudieron cargar las roles sugeridos. Intenta nuevamente.'
        showSnackbar('Error cargando roles sugeridos', 'Reintentar', 'retryLoad')
    }
}

onMounted(() => {
    if (!displayedRoles.value || displayedRoles.value.length === 0) {
        loadCompacted()
    }
})

type BlueprintPayload = {
    role_name: string
    total_fte_required?: number
    human_leverage?: number
    synthetic_leverage?: number
    recommended_strategy?: string
    agent_specs?: string | null
}

function mapRoleToBlueprint(role: Role): BlueprintPayload {
    return {
        role_name: role.name,
        total_fte_required: role.estimated_fte,
        human_leverage: role.talent_composition?.human_percentage,
        synthetic_leverage: role.talent_composition?.synthetic_percentage,
        recommended_strategy: role.talent_composition?.strategy_suggestion,
        agent_specs: role.suggested_agent_type ?? null,
    }
}

const orchestrateScenario = async () => {
    orchestrating.value = true

    const payload = {
        blueprints: displayedRoles.value.map(mapRoleToBlueprint),
    }

    // POST to the acceptance endpoint defined for strategic planning generations
    router.post(
        `/api/strategic-planning/scenarios/generate/${props.scenario.id}/accept`,
        payload,
        {
            onSuccess: () => {
                successMessage.value = 'Escenario aceptado. Redirigiendo a Paso 2...'
                // small delay to show message before redirect
                setTimeout(() => {
                    router.visit(`/scenarios/${props.scenario.id}/step2`)
                }, 300)
            },
            onError: (errs: any) => {
                // Inertia validation or server errors
                console.error('accept error', errs)
                errorMessage.value = 'No se pudo aceptar el escenario. Verifica e intenta de nuevo.'
                showSnackbar('No se pudo aceptar el escenario', 'Reintentar', 'retryAccept')
            },
            onFinish: () => {
                orchestrating.value = false
                // clear messages after a bit
                setTimeout(() => {
                    errorMessage.value = null
                    successMessage.value = null
                }, 6000)
            },
        },
    )
}

function performSnackbarAction() {
    const action = lastSnackbarAction.value
    snackbarVisible.value = false
    if (action === 'retryLoad') {
        // clear prior messages and try again
        errorMessage.value = null
        loadCompacted()
    } else if (action === 'retryAccept') {
        errorMessage.value = null
        // re-run orchestrate
        orchestrateScenario()
    }
}
</script>
