<template>
    <div class="role-forecasts-container">
        <div class="mb-6">
            <h3 class="text-h5 mb-4 font-semibold">Pronósticos de Roles</h3>
            <p class="mb-4 text-sm text-gray-600">
                Visualiza el cambio proyectado en cantidad de FTE y evolución de
                roles
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

        <!-- Table -->
        <div v-else class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="sticky top-0 z-10 bg-gray-100">
                    <tr>
                        <th class="border px-4 py-3 text-left font-semibold">
                            Rol
                        </th>
                        <th class="border px-4 py-3 text-center font-semibold">
                            FTE Actual
                        </th>
                        <th class="border px-4 py-3 text-center font-semibold">
                            FTE Futuro
                        </th>
                        <th class="border px-4 py-3 text-center font-semibold">
                            Δ FTE
                        </th>
                        <th class="border px-4 py-3 text-left font-semibold">
                            Tipo Evolución
                        </th>
                        <th class="border px-4 py-3 text-left font-semibold">
                            Impacto
                        </th>
                        <th class="border px-4 py-3 text-center font-semibold">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="forecasts.length === 0">
                        <td
                            colspan="7"
                            class="py-8 text-center text-gray-500 italic"
                        >
                            No hay pronósticos definidos
                        </td>
                    </tr>
                    <tr
                        v-for="forecast in forecasts"
                        :key="forecast.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="border px-4 py-3 font-medium">
                            {{ forecast.role_name }}
                        </td>
                        <td class="border px-4 py-3 text-center">
                            {{ forecast.fte_current }}
                        </td>
                        <td
                            class="border px-4 py-3 text-center font-semibold text-blue-600"
                        >
                            {{ forecast.fte_future }}
                        </td>
                        <td class="border px-4 py-3 text-center">
                            <span
                                :class="{
                                    'text-green-600': forecast.fte_delta > 0,
                                    'text-red-600': forecast.fte_delta < 0,
                                    'text-gray-600': forecast.fte_delta === 0,
                                }"
                            >
                                {{ forecast.fte_delta > 0 ? '+' : ''
                                }}{{ forecast.fte_delta }}
                            </span>
                        </td>
                        <td class="border px-4 py-3">
                            <v-chip
                                :color="
                                    getEvolutionColor(forecast.evolution_type)
                                "
                                size="small"
                                variant="tonal"
                            >
                                {{
                                    formatEvolutionType(forecast.evolution_type)
                                }}
                            </v-chip>
                        </td>
                        <td class="border px-4 py-3">
                            <v-chip
                                :color="getImpactColor(forecast.impact_level)"
                                size="small"
                                variant="tonal"
                            >
                                {{ formatImpact(forecast.impact_level) }}
                            </v-chip>
                        </td>
                        <td class="border px-4 py-3 text-center">
                            <v-btn
                                size="small"
                                variant="text"
                                icon="mdi-pencil"
                                @click="editForecast(forecast)"
                            />
                            <v-btn
                                size="small"
                                variant="text"
                                icon="mdi-delete"
                                color="error"
                                @click="deleteForecast(forecast.id)"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Edit Dialog -->
        <v-dialog v-model="showEditDialog" max-width="500px">
            <v-card>
                <v-card-title>Editar Pronóstico</v-card-title>
                <v-card-text v-if="editData" class="py-6">
                    <div class="space-y-4">
                        <v-text-field
                            v-model="editData.role_name"
                            label="Rol"
                            readonly
                            density="compact"
                        />
                        <v-text-field
                            v-model.number="editData.fte_current"
                            type="number"
                            label="FTE Actual"
                            density="compact"
                        />
                        <v-text-field
                            v-model.number="editData.fte_future"
                            type="number"
                            label="FTE Futuro"
                            density="compact"
                        />
                        <v-select
                            v-model="editData.evolution_type"
                            :items="evolutionTypes"
                            label="Tipo de Evolución"
                            density="compact"
                        />
                        <v-select
                            v-model="editData.impact_level"
                            :items="impactLevels"
                            label="Nivel de Impacto"
                            density="compact"
                        />
                        <v-textarea
                            v-model="editData.rationale"
                            label="Justificación"
                            rows="3"
                            density="compact"
                        />
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-btn variant="text" @click="showEditDialog = false"
                        >Cancelar</v-btn
                    >
                    <v-btn
                        color="primary"
                        @click="saveForecast"
                        :loading="saving"
                    >
                        Guardar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

interface RoleForecast {
    id: number;
    scenario_id: number;
    role_id: number;
    role_name: string;
    fte_current: number;
    fte_future: number;
    fte_delta: number;
    evolution_type: string;
    impact_level: string;
    rationale?: string;
}

interface Props {
    scenarioId: number;
}

const props = defineProps<Props>();

const loading = ref(true);
const saving = ref(false);
const error = ref<string | null>(null);
const success = ref<string | null>(null);

const forecasts = ref<RoleForecast[]>([]);
const showEditDialog = ref(false);
const editData = ref<RoleForecast | null>(null);

const evolutionTypes = [
    'new_role',
    'upgrade_skills',
    'transformation',
    'downsize',
    'elimination',
];
const impactLevels = ['critical', 'high', 'medium', 'low'];

const loadForecasts = async () => {
    try {
        loading.value = true;
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/role-forecasts`,
        );

        if (!response.ok) throw new Error('Error al cargar pronósticos');

        const data = await response.json();
        forecasts.value = (data.data || []).map((forecast: RoleForecast) => ({
            ...forecast,
            fte_delta: forecast.fte_future - forecast.fte_current,
        }));
    } catch (err: any) {
        error.value = err.message || 'Error al cargar pronósticos';
    } finally {
        loading.value = false;
    }
};

const editForecast = (forecast: RoleForecast) => {
    editData.value = { ...forecast };
    showEditDialog.value = true;
};

const saveForecast = async () => {
    if (!editData.value) return;

    try {
        saving.value = true;
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/role-forecasts/${editData.value.id}`,
            {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(editData.value),
            },
        );

        if (!response.ok) throw new Error('Error al guardar pronóstico');

        success.value = 'Pronóstico actualizado exitosamente';
        showEditDialog.value = false;
        await loadForecasts();
    } catch (err: any) {
        error.value = err.message || 'Error al guardar pronóstico';
    } finally {
        saving.value = false;
    }
};

const deleteForecast = async (id: number) => {
    if (!confirm('¿Estás seguro?')) return;

    try {
        const response = await fetch(
            `/api/scenarios/${props.scenarioId}/step2/role-forecasts/${id}`,
            { method: 'DELETE' },
        );

        if (!response.ok) throw new Error('Error al eliminar pronóstico');

        success.value = 'Pronóstico eliminado';
        await loadForecasts();
    } catch (err: any) {
        error.value = err.message || 'Error al eliminar pronóstico';
    }
};

const getEvolutionColor = (type: string) => {
    const colors: { [key: string]: string } = {
        new_role: 'blue',
        upgrade_skills: 'green',
        transformation: 'orange',
        downsize: 'red',
        elimination: 'error',
    };
    return colors[type] || 'gray';
};

const getImpactColor = (level: string) => {
    const colors: { [key: string]: string } = {
        critical: 'error',
        high: 'warning',
        medium: 'info',
        low: 'success',
    };
    return colors[level] || 'gray';
};

const formatEvolutionType = (type: string) => {
    const labels: { [key: string]: string } = {
        new_role: 'Nuevo Rol',
        upgrade_skills: 'Actualizar Skills',
        transformation: 'Transformación',
        downsize: 'Reducción',
        elimination: 'Eliminación',
    };
    return labels[type] || type;
};

const formatImpact = (level: string) => {
    const labels: { [key: string]: string } = {
        critical: 'Crítico',
        high: 'Alto',
        medium: 'Medio',
        low: 'Bajo',
    };
    return labels[level] || level;
};

onMounted(() => {
    loadForecasts();
});

watch(
    () => props.scenarioId,
    () => {
        loadForecasts();
    },
);
</script>

<style scoped>
.role-forecasts-container {
    padding: 1.5rem;
}
</style>
