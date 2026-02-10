<template>
    <div class="step-identity pa-4">
        <v-row class="g-4">
            <v-col cols="12" md="6" class="d-flex align-center">
                <v-text-field
                    v-model="local.company_name"
                    label="Nombre de la organización"
                    outlined
                    dense
                    class="full-width"
                >
                    <template #append>
                        <FieldHelp
                            title="Nombre de la organización"
                            description="Nombre legal o comercial de la organización para la cual se genera el escenario."
                            example="Acme Corp"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="3" class="d-flex align-center">
                <v-text-field
                    v-model="local.industry"
                    label="Industria"
                    outlined
                    dense
                >
                    <template #append>
                        <FieldHelp
                            title="Industria"
                            description="Sector económico principal (p.ej. 'Software', 'Salud', 'Manufactura')."
                            example="Software"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="3">
                <v-text-field
                    v-model="local.sub_industry"
                    label="Sub-industria"
                    outlined
                    dense
                />
            </v-col>

            <v-col cols="12" md="6">
                <v-text-field
                    v-model="local.geographic_scope"
                    label="Ámbito geográfico"
                    outlined
                    dense
                >
                    <template #append>
                        <FieldHelp
                            title="Ámbito geográfico"
                            description="Alcance geográfico del escenario (p.ej. 'Nacional', 'Región', 'Global')."
                            example="Región: Latinoamérica"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="6">
                <v-text-field
                    v-model="local.organizational_cycle"
                    label="Ciclo organizacional"
                    outlined
                    dense
                >
                    <template #append>
                        <FieldHelp
                            title="Ciclo organizacional"
                            description="Periodo o fase del ciclo organizacional (p.ej. 'Anual', 'Trimestral', 'Plan estratégico 3 años')."
                            example="Anual"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="4" class="d-flex align-center">
                <v-text-field
                    v-model="local.company_size"
                    type="number"
                    label="Tamaño (personas)"
                    outlined
                    dense
                >
                    <template #append>
                        <FieldHelp
                            title="Tamaño"
                            description="Número aproximado de empleados. Usado para dimensionar impacto y recursos."
                            example="120"
                        />
                    </template>
                </v-text-field>
            </v-col>

            <v-col cols="12" md="8" class="d-flex align-center">
                <v-checkbox
                    v-model="local.auto_generate"
                    label="Generación automática (IA)"
                    class="ma-0"
                />
                <p class="legend ml-2">
                    Activar para que la IA genere un escenario sugerido con los
                    datos ingresados. Podrás revisar y editar el resultado antes
                    de aceptarlo.
                </p>
            </v-col>
        </v-row>
    </div>
</template>

<script setup lang="ts">
import FieldHelp from '@/components/Ui/FieldHelp.vue';
import { useScenarioGenerationStore } from '@/stores/scenarioGenerationStore';
import { reactive, watch } from 'vue';

const store = useScenarioGenerationStore();

const local = reactive({
    company_name: store.data.company_name || '',
    industry: store.data.industry || '',
    sub_industry: store.data.sub_industry || '',
    geographic_scope: store.data.geographic_scope || '',
    organizational_cycle: store.data.organizational_cycle || '',
    company_size: store.data.company_size || null,
    auto_generate: store.data.auto_generate || false,
});

watch(
    local,
    (nv) => {
        Object.keys(nv).forEach((k) => store.setField(k, (nv as any)[k]));
    },
    { deep: true },
);
</script>

<style scoped>
.legend {
    font-size: 0.95rem;
    color: #546;
    margin: 0 0 0 0.5rem;
}
.step-identity .full-width {
    width: 100%;
}
</style>
