<template>
    <div class="pa-4">
        <h3 class="mb-4">Recursos y Restricciones</h3>
        <v-row>
            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="store.data.budget_level"
                    label="Nivel de presupuesto"
                    outlined
                >
                    <template #append>
                        <FieldHelp
                            title="Nivel de presupuesto"
                            description="Indicación general del presupuesto disponible para iniciativas."
                            example="Bajo / Medio / Alto"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="store.data.talent_availability"
                    label="Disponibilidad de talento"
                    outlined
                >
                    <template #append>
                        <FieldHelp
                            title="Disponibilidad de talento"
                            description="Nivel de acceso a perfiles críticos internamente o en el mercado."
                            example="Escaso / Moderado / Abundante"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="store.data.training_capacity"
                    label="Capacidad de capacitación"
                    outlined
                >
                    <template #append>
                        <FieldHelp
                            title="Capacidad de capacitación"
                            description="Capacidad interna para ejecutar programas de formación (recursos, instructores, budget)."
                            example="Capacidad para formar 50 empleados/año en habilidades técnicas"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="store.data.technology_maturity"
                    label="Madurez tecnológica"
                    outlined
                >
                    <template #append>
                        <FieldHelp
                            title="Madurez tecnológica"
                            description="Nivel de adopción y estabilidad de la tecnología usada por la organización."
                            example="Madurez Alta: infraestructura en cloud, pipelines automatizados"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" class="d-flex align-center">
                <v-textarea
                    v-model="store.data.critical_constraints"
                    label="Restricciones críticas"
                    outlined
                    rows="3"
                    class="full-width"
                >
                    <template #append>
                        <FieldHelp
                            title="Restricciones críticas"
                            description="Condiciones que limitan la ejecución de las iniciativas (regulatorias, tecnológicas, etc.)."
                            example="Plazo regulatorio, dependencia de proveedor X."
                        />
                    </template>
                </v-textarea>
            </v-col>
        </v-row>
    </div>
</template>

<script setup lang="ts">
import FieldHelp from '@/components/Ui/FieldHelp.vue';
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';
const store = useScenarioGenerationStore();
</script>
