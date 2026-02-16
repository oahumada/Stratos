<template>
    <div class="succession-plan-container">
        <div class="mb-6">
            <h3 class="text-h5 mb-4 font-semibold">Plan de Sucesión</h3>
            <p class="mb-4 text-sm text-gray-600">
                Identificación de sucesores y timeline de transición para
                posiciones críticas
            </p>
        </div>

        <!-- Alerts -->
        <v-alert
            v-if="error"
            type="error"
            closable
            @click:close="error = null"
            class="mb-4"
        >
            {{ error }}
        </v-alert>
        <v-alert
            v-if="success"
            type="success"
            closable
            @click:close="success = null"
            class="mb-4"
        >
            {{ success }}
        </v-alert>

        <!-- Loading -->
        <div v-if="loading" class="flex justify-center py-8">
            <v-progress-circular indeterminate color="primary" />
        </div>

        <!-- Plans -->
        <div v-else class="grid grid-cols-1 gap-6">
            <div
                v-if="plans.length === 0"
                class="col-span-full py-8 text-center text-gray-500"
            >
                No hay planes de sucesión disponibles
            </div>

            <div
                v-for="plan in plans"
                :key="plan.id"
                class="overflow-hidden rounded-lg border transition-shadow hover:shadow-lg"
            >
                <!-- Header -->
                <div
                    class="border-b bg-gradient-to-r from-blue-50 to-blue-100 p-4"
                >
                    <div class="mb-2 flex items-start justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800">
                                {{ plan.position_name }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                {{ plan.department }}
                            </p>
                        </div>
                        <v-chip
                            :color="getCriticalityColor(plan.criticality)"
                            size="small"
                            variant="tonal"
                        >
                            {{ formatCriticality(plan.criticality) }}
                        </v-chip>
                    </div>
                </div>

                <!-- Current Position Holder -->
                <div class="border-b bg-gray-50 p-4">
                    <p class="mb-2 text-xs font-semibold text-gray-600">
                        TITULAR ACTUAL:
                    </p>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ plan.current_holder_name }}
                            </p>
                            <p class="text-sm text-gray-600">
                                Años en posición: {{ plan.years_in_position }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-700">
                                Edad: {{ plan.current_holder_age }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Retiro estimado: {{ plan.estimated_retirement }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Successors -->
                <div class="space-y-3 p-4">
                    <p class="text-xs font-semibold text-gray-600 uppercase">
                        Sucesores Identificados:
                    </p>

                    <div
                        v-if="plan.successors && plan.successors.length > 0"
                        class="space-y-3"
                    >
                        <div
                            v-for="(successor, idx) in plan.successors"
                            :key="successor.id"
                            class="rounded-lg border border-blue-200 bg-blue-50 p-3"
                        >
                            <div class="mb-2 flex items-start justify-between">
                                <div>
                                    <p class="font-semibold text-gray-800">
                                        {{ idx + 1 }}. {{ successor.name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ successor.current_role }}
                                    </p>
                                </div>
                                <v-chip
                                    :color="
                                        getReadinessColor(
                                            successor.readiness_level,
                                        )
                                    "
                                    size="small"
                                >
                                    {{
                                        formatReadiness(
                                            successor.readiness_level,
                                        )
                                    }}
                                </v-chip>
                            </div>

                            <!-- Readiness Bar -->
                            <v-progress-linear
                                :model-value="successor.readiness_percentage"
                                :color="
                                    getReadinessColor(successor.readiness_level)
                                "
                                height="6"
                                class="mb-2"
                            />

                            <!-- Skills Match -->
                            <div class="mb-2 flex flex-wrap gap-2">
                                <v-chip
                                    v-for="gap in successor.skill_gaps.slice(
                                        0,
                                        2,
                                    )"
                                    :key="gap.id"
                                    size="small"
                                    variant="tonal"
                                    color="warning"
                                >
                                    {{ gap.skill_name }}:
                                    {{ gap.current_level }} →
                                    {{ gap.required_level }}
                                </v-chip>
                                <v-chip
                                    v-if="successor.skill_gaps.length > 2"
                                    size="small"
                                    variant="tonal"
                                    color="gray"
                                >
                                    +{{ successor.skill_gaps.length - 2 }}
                                    skills
                                </v-chip>
                            </div>

                            <!-- Timeline -->
                            <div class="text-xs text-gray-600">
                                <p>
                                    <strong>Timeline de Preparación:</strong>
                                    {{ successor.timeline_months }} meses
                                </p>
                                <p v-if="successor.development_plan">
                                    <strong>Plan de Desarrollo:</strong>
                                    {{ successor.development_plan }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded border border-red-200 bg-red-50 p-3"
                    >
                        <p class="flex items-center gap-2 text-sm text-red-700">
                            <v-icon size="small">mdi-alert-circle</v-icon>
                            <span>No hay sucesores identificados</span>
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 border-t bg-gray-50 p-4">
                    <v-btn
                        size="small"
                        variant="tonal"
                        color="primary"
                        @click="editPlan(plan)"
                    >
                        Editar Plan
                    </v-btn>
                    <v-btn
                        size="small"
                        variant="tonal"
                        @click="viewTimeline(plan)"
                    >
                        Ver Timeline
                    </v-btn>
                    <v-spacer />
                    <v-btn
                        size="small"
                        variant="text"
                        color="error"
                        @click="deletePlan(plan.id)"
                    >
                        Eliminar
                    </v-btn>
                </div>
            </div>
        </div>

        <!-- Edit Dialog -->
        <v-dialog v-model="showEditDialog" max-width="600px">
            <v-card v-if="editingPlan">
                <v-card-title>Editar Plan de Sucesión</v-card-title>
                <v-card-text class="space-y-4 py-6">
                    <v-text-field
                        v-model="editingPlan.position_name"
                        label="Posición"
                        readonly
                        density="compact"
                    />
                    <v-select
                        v-model="editingPlan.criticality"
                        :items="criticalities"
                        label="Criticidad"
                        density="compact"
                    />
                    <v-textarea
                        v-model="editingPlan.notes"
                        label="Notas"
                        rows="3"
                        density="compact"
                    />
                </v-card-text>
                <v-card-actions>
                    <v-btn variant="text" @click="showEditDialog = false"
                        >Cancelar</v-btn
                    >
                    <v-btn color="primary" @click="savePlan" :loading="saving"
                        >Guardar</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

interface SkillGap {
    id: number;
    skill_name: string;
    current_level: number;
    required_level: number;
}

interface Successor {
    id: number;
    name: string;
    current_role: string;
    readiness_level:
        | 'ready_now'
        | 'ready_12_months'
        | 'ready_24_months'
        | 'not_ready';
    readiness_percentage: number;
    skill_gaps: SkillGap[];
    timeline_months: number;
    development_plan?: string;
}

interface SuccessionPlan {
    id: number;
    position_name: string;
    department: string;
    criticality: 'critical' | 'high' | 'medium' | 'low';
    current_holder_name: string;
    current_holder_age: number;
    years_in_position: number;
    estimated_retirement: string;
    successors: Successor[];
    notes?: string;
}

interface Props {
    scenarioId: number;
}

const props = defineProps<Props>();

const loading = ref(true);
const saving = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);

const plans = ref<SuccessionPlan[]>([]);
const showEditDialog = ref(false);
const editingPlan = ref<SuccessionPlan | null>(null);

const criticalities = ['critical', 'high', 'medium', 'low'];

const loadPlans = async () => {
    try {
        loading.value = true;
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/succession-plans`,
        );

        if (!response.ok) throw new Error('Error al cargar planes');

        const data = await response.json();
        plans.value = data.data || [];
    } catch (err: any) {
        error.value = err.message || 'Error al cargar planes de sucesión';
    } finally {
        loading.value = false;
    }
};

const getCriticalityColor = (criticality: string): string => {
    const colors: { [key: string]: string } = {
        critical: 'error',
        high: 'warning',
        medium: 'info',
        low: 'success',
    };
    return colors[criticality] || 'gray';
};

const getReadinessColor = (level: string): string => {
    const colors: { [key: string]: string } = {
        ready_now: 'success',
        ready_12_months: 'warning',
        ready_24_months: 'info',
        not_ready: 'error',
    };
    return colors[level] || 'gray';
};

const formatCriticality = (level: string): string => {
    const labels: { [key: string]: string } = {
        critical: 'Crítica',
        high: 'Alta',
        medium: 'Media',
        low: 'Baja',
    };
    return labels[level] || level;
};

const formatReadiness = (level: string): string => {
    const labels: { [key: string]: string } = {
        ready_now: 'Listo Ahora',
        ready_12_months: 'Listo en 12m',
        ready_24_months: 'Listo en 24m',
        not_ready: 'No Listo',
    };
    return labels[level] || level;
};

const editPlan = (plan: SuccessionPlan) => {
    editingPlan.value = { ...plan };
    showEditDialog.value = true;
};

const savePlan = async () => {
    if (!editingPlan.value) return;

    try {
        saving.value = true;
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/succession-plans/${editingPlan.value.id}`,
            {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    notes: editingPlan.value.notes,
                    criticality: editingPlan.value.criticality,
                }),
            },
        );

        if (!response.ok) throw new Error('Error al guardar plan');

        success.value = 'Plan actualizado';
        showEditDialog.value = false;
        await loadPlans();
    } catch (err: any) {
        error.value = err.message;
    } finally {
        saving.value = false;
    }
};

const deletePlan = async (id: number) => {
    if (!confirm('¿Estás seguro?')) return;

    try {
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/succession-plans/${id}`,
            { method: 'DELETE' },
        );

        if (!response.ok) throw new Error('Error al eliminar plan');

        success.value = 'Plan eliminado';
        await loadPlans();
    } catch (err: any) {
        error.value = err.message;
    }
};

const viewTimeline = (plan: SuccessionPlan) => {
    console.log('View timeline for:', plan.position_name);
    // Implementar diálogo de timeline
};

onMounted(() => {
    loadPlans();
});

watch(
    () => props.scenarioId,
    () => {
        loadPlans();
    },
);
</script>

<style scoped>
.succession-plan-container {
    padding: 1.5rem;
}

.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}
</style>
