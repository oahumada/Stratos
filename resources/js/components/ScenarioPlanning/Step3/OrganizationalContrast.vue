<template>
    <div class="organizational-contrast">
        <!-- Header Informativo -->
        <div
            class="mb-6 rounded-xl border-l-4 border-indigo-500 bg-white p-6 shadow-sm"
        >
            <div class="flex items-start gap-4">
                <div class="rounded-lg bg-indigo-50 p-2 text-indigo-600">
                    <v-icon icon="mdi-scale-balance" size="large" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        Paso 3: Contraste con la Realidad
                    </h2>
                    <p class="mt-1 text-gray-600">
                        Compara tu diseño estratégico (To-Be) con el inventario
                        actual de la organización (As-Is). Identifica brechas
                        críticas de talento y dotación antes de definir las
                        estrategias de ejecución.
                    </p>
                </div>
            </div>
        </div>

        <v-row>
            <!-- Columna Principal: Brechas de Skills -->
            <v-col cols="12">
                <v-card class="overflow-hidden rounded-xl" elevation="1">
                    <div
                        class="d-flex align-center justify-space-between border-b bg-slate-50 px-6 py-4"
                    >
                        <div class="d-flex align-center gap-2">
                            <v-icon color="primary">mdi-matrix</v-icon>
                            <span class="text-subtitle-1 font-weight-bold"
                                >Matriz de Brechas de Competencias</span
                            >
                        </div>
                    </div>
                    <v-card-text class="pa-0">
                        <SkillGapsMatrix :scenario-id="props.scenarioId" />
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Columna: Pronósticos de Roles -->
            <v-col cols="12" md="7">
                <v-card class="h-100 overflow-hidden rounded-xl" elevation="1">
                    <div class="border-b bg-slate-50 px-6 py-4">
                        <div class="d-flex align-center gap-2">
                            <v-icon color="primary"
                                >mdi-chart-areaspline</v-icon
                            >
                            <span class="text-subtitle-1 font-weight-bold"
                                >Evolución de Dotación (FTE)</span
                            >
                        </div>
                    </div>
                    <v-card-text class="pa-0">
                        <RoleForecastsTable :scenario-id="props.scenarioId" />
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Columna: Matching de Talento Inicial -->
            <v-col cols="12" md="5">
                <v-card class="h-100 overflow-hidden rounded-xl" elevation="1">
                    <div class="border-b bg-slate-50 px-6 py-4">
                        <div class="d-flex align-center gap-2">
                            <v-icon color="primary">mdi-account-search</v-icon>
                            <span class="text-subtitle-1 font-weight-bold"
                                >Alineación de Talento Interno</span
                            >
                        </div>
                    </div>
                    <v-card-text>
                        <MatchingResults :scenario-id="props.scenarioId" />
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script setup lang="ts">
import MatchingResults from '../Step2/MatchingResults.vue';
import RoleForecastsTable from '../Step2/RoleForecastsTable.vue';
import SkillGapsMatrix from '../Step2/SkillGapsMatrix.vue';

const props = defineProps<{
    scenarioId: number;
}>();
</script>

<style scoped>
.organizational-contrast {
    padding: 1rem;
}
</style>
