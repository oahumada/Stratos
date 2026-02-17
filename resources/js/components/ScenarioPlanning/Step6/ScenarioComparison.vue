<template>
    <div class="scenario-comparison-container">
        <v-container fluid>
            <v-row>
                <!-- Selector de Versiones -->
                <v-col cols="12" md="3">
                    <v-card elevation="2" class="mb-4">
                        <v-card-title
                            class="text-subtitle-1 bg-primary text-white"
                        >
                            <v-icon start size="small"
                                >mdi-layers-triple</v-icon
                            >
                            Versiones disponibles
                        </v-card-title>
                        <v-card-text class="pa-0">
                            <v-list
                                density="comfortable"
                                select-strategy="leaf"
                            >
                                <v-list-item
                                    v-for="version in availableVersions"
                                    :key="version.id"
                                    :active="selectedIds.includes(version.id)"
                                    @click="toggleSelection(version.id)"
                                >
                                    <template v-slot:prepend>
                                        <v-checkbox-btn
                                            :model-value="
                                                selectedIds.includes(version.id)
                                            "
                                            density="compact"
                                        ></v-checkbox-btn>
                                    </template>
                                    <v-list-item-title class="text-body-2">
                                        v{{ version.version_number }}:
                                        {{ version.name }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle class="text-caption">
                                        {{ formatDate(version.created_at) }}
                                    </v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                            <div
                                v-if="
                                    availableVersions.length === 0 &&
                                    !loadingVersions
                                "
                                class="pa-4 text-caption text-medium-emphasis text-center"
                            >
                                No hay otras versiones para comparar.
                            </div>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn
                                block
                                color="primary"
                                variant="flat"
                                :disabled="selectedIds.length < 2"
                                :loading="loadingData"
                                @click="fetchComparisonData"
                            >
                                Comparar Selección
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>

                <!-- Tabla de Comparación Principal -->
                <v-col cols="12" md="9">
                    <v-card v-if="comparisonData.length > 0" elevation="2">
                        <v-card-title class="d-flex align-center">
                            Comparativa de Escenarios
                            <v-spacer></v-spacer>
                            <v-btn
                                icon="mdi-refresh"
                                variant="text"
                                size="small"
                                @click="fetchComparisonData"
                            ></v-btn>
                        </v-card-title>

                        <v-table density="comfortable" class="comparison-table">
                            <thead>
                                <tr>
                                    <th
                                        scope="col"
                                        class="font-weight-bold text-left"
                                        style="width: 200px"
                                    >
                                        Métrica
                                    </th>
                                    <th
                                        v-for="item in comparisonData"
                                        :key="item.id"
                                        scope="col"
                                        class="text-center"
                                        :class="{
                                            'bg-blue-lighten-5':
                                                item.id === scenarioId,
                                        }"
                                    >
                                        <div class="text-subtitle-2">
                                            {{ item.name }}
                                        </div>
                                        <v-chip
                                            size="x-small"
                                            variant="flat"
                                            color="primary"
                                            >v{{ item.version }}</v-chip
                                        >
                                        <div
                                            v-if="item.id === scenarioId"
                                            class="text-caption font-weight-bold text-primary"
                                        >
                                            (Actual)
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- IQ / Readiness -->
                                <tr>
                                    <td class="font-weight-medium">
                                        <v-icon start size="small" color="blue"
                                            >mdi-brain</v-icon
                                        >
                                        Scenario IQ
                                    </td>
                                    <td
                                        v-for="item in comparisonData"
                                        :key="item.id"
                                        class="text-center"
                                    >
                                        <v-progress-circular
                                            :model-value="item.iq"
                                            :color="getIqColor(item.iq)"
                                            size="40"
                                            width="4"
                                        >
                                            <span
                                                class="text-caption font-weight-bold"
                                                >{{ item.iq }}</span
                                            >
                                        </v-progress-circular>
                                    </td>
                                </tr>

                                <!-- Inversión Total -->
                                <tr>
                                    <td class="font-weight-medium">
                                        <v-icon
                                            start
                                            size="small"
                                            color="success"
                                            >mdi-cash</v-icon
                                        >
                                        Inversión Est.
                                    </td>
                                    <td
                                        v-for="item in comparisonData"
                                        :key="item.id"
                                        class="font-weight-bold text-center"
                                    >
                                        {{ formatCurrency(item.total_cost) }}
                                    </td>
                                </tr>

                                <!-- Headcount Requerido -->
                                <tr>
                                    <td class="font-weight-medium">
                                        <v-icon
                                            start
                                            size="small"
                                            color="indigo"
                                            >mdi-account-group</v-icon
                                        >
                                        FTE Requerido
                                    </td>
                                    <td
                                        v-for="item in comparisonData"
                                        :key="item.id"
                                        class="text-center"
                                    >
                                        {{ item.total_req_fte }}
                                    </td>
                                </tr>

                                <!-- Gap de Headcount -->
                                <tr>
                                    <td class="font-weight-medium">
                                        <v-icon
                                            start
                                            size="small"
                                            color="orange"
                                            >mdi-account-alert</v-icon
                                        >
                                        Gap Headcount
                                    </td>
                                    <td
                                        v-for="item in comparisonData"
                                        :key="item.id"
                                        class="text-center"
                                    >
                                        <v-chip
                                            :color="
                                                item.gap_fte > 0
                                                    ? 'orange-darken-2'
                                                    : 'success'
                                            "
                                            variant="tonal"
                                            size="small"
                                        >
                                            {{ item.gap_fte }} FTEs
                                        </v-chip>
                                    </td>
                                </tr>

                                <!-- Estado -->
                                <tr>
                                    <td class="font-weight-medium">
                                        <v-icon start size="small"
                                            >mdi-list-status</v-icon
                                        >
                                        Estado
                                    </td>
                                    <td
                                        v-for="item in comparisonData"
                                        :key="item.id"
                                        class="text-center"
                                    >
                                        <v-chip
                                            size="x-small"
                                            :color="getStatusColor(item.status)"
                                            variant="flat"
                                            class="text-uppercase"
                                        >
                                            {{ item.status || 'Draft' }}
                                        </v-chip>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card>

                    <!-- Estado Vacío -->
                    <v-card
                        v-else
                        variant="outlined"
                        class="d-flex flex-column align-center justify-center border-dashed py-12 text-center"
                    >
                        <v-icon size="64" color="grey-lighten-1" class="mb-4"
                            >mdi-compare-horizontal</v-icon
                        >
                        <div class="text-h6 text-medium-emphasis">
                            Comparación de Versiones
                        </div>
                        <p class="text-body-2 text-medium-emphasis px-8">
                            Selecciona al menos dos versiones en el panel
                            lateral para comparar sus KPIs técnicos y
                            financieros.
                        </p>
                        <v-btn
                            v-if="selectedIds.length >= 2"
                            color="primary"
                            class="mt-4"
                            prepend-icon="mdi-play"
                            @click="fetchComparisonData"
                        >
                            Ver Comparativa
                        </v-btn>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script setup lang="ts">
import { useNotification } from '@/composables/useNotification';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const props = defineProps({
    scenarioId: {
        type: [Number, String],
        required: true,
    },
});

const notification = useNotification();
const loadingVersions = ref(false);
const loadingData = ref(false);
const availableVersions = ref<any[]>([]);
const selectedIds = ref<number[]>([]);
const comparisonData = ref<any[]>([]);

const loadVersions = async () => {
    loadingVersions.value = true;
    try {
        const res = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/versions`,
        );
        availableVersions.value = res.data?.versions || [];

        // Seleccionamos por defecto el actual y la versión más reciente anterior
        selectedIds.value = [Number(props.scenarioId)];
        if (availableVersions.value.length > 0) {
            const others = availableVersions.value.filter(
                (v) => v.id !== Number(props.scenarioId),
            );
            if (others.length > 0) {
                selectedIds.value.push(others[0].id);
                // Si tenemos al menos 2, disparamos la comparación inicial
                fetchComparisonData();
            }
        }
    } catch (error) {
        notification.showError('Error al cargar historial de versiones');
    } finally {
        loadingVersions.value = false;
    }
};

const fetchComparisonData = async () => {
    if (selectedIds.value.length < 2) return;

    loadingData.value = true;
    try {
        const res = await axios.get(
            `/api/strategic-planning/scenarios/${props.scenarioId}/compare-versions`,
            {
                params: { ids: selectedIds.value },
            },
        );
        comparisonData.value = res.data.data || [];
        // Ordenamos por versión para que la tabla tenga sentido temporal
        comparisonData.value.sort((a, b) => a.version - b.version);
    } catch (error) {
        notification.showError('Error al obtener datos comparativos');
    } finally {
        loadingData.value = false;
    }
};

const toggleSelection = (id: number) => {
    const index = selectedIds.value.indexOf(id);
    if (index > -1) {
        if (selectedIds.value.length > 2) {
            selectedIds.value.splice(index, 1);
        } else {
            notification.showWarning(
                'Debes mantener al menos 2 escenarios para comparar',
            );
        }
    } else {
        if (selectedIds.value.length < 5) {
            selectedIds.value.push(id);
        } else {
            notification.showWarning('Límite de comparación: 5 versiones');
        }
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString();
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(val);
};

const getIqColor = (iq: number) => {
    if (iq < 40) return 'error';
    if (iq < 70) return 'warning';
    return 'success';
};

const getStatusColor = (status: string) => {
    const map: Record<string, string> = {
        draft: 'grey',
        pending_approval: 'amber',
        approved: 'success',
        rejected: 'error',
    };
    return map[status] || 'grey';
};

onMounted(loadVersions);
</script>

<style scoped>
.comparison-table {
    border-radius: 8px;
}
.comparison-table :deep(th) {
    background-color: #f8fafc;
    border-bottom: 2px solid #e2e8f0 !important;
}
.comparison-table :deep(tr:hover td) {
    background-color: #f1f5f9;
}
.border-dashed {
    border: 2px dashed #cbd5e1 !important;
}
</style>
