<template>
    <div class="pa-4">
        <h3 class="mb-4">Situación Actual</h3>
        <v-row>
            <v-col cols="12" class="d-flex align-center">
                <v-textarea
                    v-model="store.data.current_challenges"
                    label="Desafíos"
                    outlined
                    rows="4"
                    class="full-width"
                >
                    <template #append>
                        <FieldHelp
                            title="Desafíos actuales"
                            description="Problemas o limitaciones clave que enfrenta la organización hoy."
                            example="Alta rotación en roles técnicos, silos entre equipos."
                        />
                    </template>
                </v-textarea>
            </v-col>

            <v-col cols="12" class="d-flex align-center">
                <v-textarea
                    v-model="store.data.current_capabilities"
                    label="Capacidades existentes"
                    outlined
                    rows="3"
                    class="full-width"
                >
                    <template #append>
                        <FieldHelp
                            title="Capacidades existentes"
                            description="Habilidades, procesos y recursos con los que cuenta actualmente la organización."
                            example="Equipo de data science interno, procesos de onboarding definidos."
                        />
                    </template>
                </v-textarea>
            </v-col>

            <v-col cols="12" class="d-flex align-center">
                <v-textarea
                    v-model="store.data.current_gaps"
                    label="Brechas"
                    outlined
                    rows="3"
                    class="full-width"
                >
                    <template #append>
                        <FieldHelp
                            title="Brechas"
                            description="Diferencias identificadas entre las capacidades actuales y las requeridas para alcanzar los objetivos."
                            example="Falta experiencia en cloud, carencia de perfiles de liderazgo técnico."
                        />
                    </template>
                </v-textarea>
            </v-col>

            <v-col cols="12" md="4">
                <v-text-field
                    v-model.number="store.data.current_roles_count"
                    type="number"
                    label="Roles formalizados (cantidad)"
                    outlined
                />
            </v-col>

            <v-col cols="12" md="8" class="d-flex align-center">
                <v-checkbox
                    v-model="store.data.has_formal_competency_model"
                    label="Modelo de competencias"
                />
                <FieldHelp
                    title="Modelo de competencias"
                    description="Indica si la organización tiene un modelo formal de competencias que define niveles y comportamientos por rol."
                    example="Sí: niveles 1-5 por competencia, con descriptores y criterios de evaluación."
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
