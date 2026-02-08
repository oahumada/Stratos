<template>
    <div class="pa-4">
        <h3 class="mb-4">Intención Estratégica</h3>
        <v-row>
            <v-col cols="12" class="d-flex align-center">
                <v-textarea
                    v-model="store.data.strategic_goal"
                    label="Objetivo principal"
                    outlined
                    rows="3"
                    class="full-width"
                >
                    <template #append>
                        <FieldHelp
                            title="Objetivo principal"
                            description="Meta estratégica que la organización busca alcanzar con este plan."
                            example="Aumentar retención de talento en 20% en 12 meses"
                        />
                    </template>
                </v-textarea>
            </v-col>

            <v-col cols="12" md="6">
                <v-text-field
                    v-model="store.data.target_markets"
                    label="Mercados objetivo"
                    outlined
                />
            </v-col>

            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="store.data.expected_growth"
                    label="Crecimiento esperado"
                    outlined
                >
                    <template #append>
                        <FieldHelp
                            title="Crecimiento esperado"
                            description="Cuánto crecimiento se espera como resultado de las iniciativas (puede ser en % o descripción cualitativa)."
                            example="10% de crecimiento en ingresos o expansión a 2 nuevos mercados"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="transformation"
                    label="Tipo de transformación (coma-separado)"
                    outlined
                    @input="onTransformationChange"
                >
                    <template #append>
                        <FieldHelp
                            title="Tipo de transformación"
                            description="Categorías de transformación que aplican (p.ej. digitalización, reorganización, cultural). Separar por comas."
                            example="Digitalización, Reestructuración organizacional"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" class="d-flex align-center">
                <v-textarea
                    v-model="store.data.key_initiatives"
                    label="Iniciativas clave"
                    outlined
                    rows="3"
                    class="full-width"
                >
                    <template #append>
                        <FieldHelp
                            title="Iniciativas clave"
                            description="Proyectos o acciones principales previstas para alcanzar el objetivo."
                            example="Programa de desarrollo de líderes, plan de retención por competencias."
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
import { ref } from 'vue';
const store = useScenarioGenerationStore();

const transformation = ref((store.data.transformation_type || []).join(','));
function onTransformationChange() {
    store.data.transformation_type = transformation.value
        .split(',')
        .map((s) => s.trim())
        .filter(Boolean);
}
</script>
