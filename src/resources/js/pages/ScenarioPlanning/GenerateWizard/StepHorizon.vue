<template>
    <div class="pa-4">
        <h3 class="mb-4">Horizonte Temporal</h3>
        <v-row>
            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="store.data.time_horizon"
                    label="Plazo"
                    outlined
                >
                    <template #append>
                        <FieldHelp
                            title="Plazo"
                            description="Periodo objetivo para alcanzar las metas (p.ej. 6 meses, 12 meses)."
                            example="12 meses"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="store.data.urgency_level"
                    label="Urgencia"
                    outlined
                >
                    <template #append>
                        <FieldHelp
                            title="Urgencia"
                            description="Nivel de prioridad temporal para las iniciativas (ej. Crítico, Alto, Medio, Bajo)."
                            example="Crítico: requiere acción inmediata; Medio: plan en 6-12 meses"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" class="d-flex align-center">
                <v-textarea
                    v-model="store.data.milestones"
                    label="Hitos"
                    outlined
                    rows="3"
                    class="full-width"
                >
                    <template #append>
                        <FieldHelp
                            title="Hitos"
                            description="Puntos clave o entregables a lo largo del horizonte temporal."
                            example="Q1: piloto de capacitación; Q3: despliegue regional"
                        />
                    </template>
                </v-textarea>
            </v-col>

            <v-col cols="12" md="4">
                <v-text-field
                    v-model.number="store.data.organization_id"
                    label="Organization ID (opcional)"
                    type="number"
                    outlined
                />
            </v-col>
        </v-row>
    </div>
</template>

<script setup lang="ts">
import FieldHelp from '@/components/Ui/FieldHelp.vue';
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';
const store = useScenarioGenerationStore();
</script>
