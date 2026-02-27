<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
} from '@/types/form-schema';
import axios from 'axios';
import { ref } from 'vue';
import FormSchema from '../form-template/FormSchema.vue';

// Import JSON configs
import configJson from './competencies-form/config.json';
import filtersJson from './competencies-form/filters.json';
import itemFormJson from './competencies-form/itemForm.json';
import tableConfigJson from './competencies-form/tableConfig.json';

defineOptions({ layout: AppLayout });

const detailTab = ref('info');

const curating = ref(false);

const curateCompetency = async (id: number, refresh: () => void) => {
    curating.value = true;
    try {
        await axios.post(
            `/api/strategic-planning/curator/competencies/${id}/curate`,
        );
        refresh();
    } catch (error) {
        console.error('Error curating competency:', error);
    } finally {
        curating.value = false;
    }
};
</script>

<template>
    <div class="ma-4">
        <FormSchema
            :config="configJson as Config"
            :tableConfig="tableConfigJson as unknown as TableConfig"
            :itemForm="itemFormJson as unknown as ItemForm"
            :filters="filtersJson as FilterConfig[]"
        >
            <template #item-details="{ item, refresh }">
                <v-card variant="flat">
                    <v-tabs v-model="detailTab" color="primary">
                        <v-tab value="info">
                            <v-icon start>mdi-information</v-icon> Información
                            Base
                        </v-tab>
                        <v-tab value="ai">
                            <v-icon start>mdi-robot-outline</v-icon> Diseño AI
                        </v-tab>
                    </v-tabs>

                    <v-card-text>
                        <v-window v-model="detailTab">
                            <v-window-item value="info">
                                <v-card
                                    variant="outlined"
                                    class="pa-4 bg-grey-lighten-4"
                                >
                                    <div
                                        class="text-subtitle-1 font-weight-bold mb-2 text-primary"
                                    >
                                        {{ item.name }}
                                    </div>
                                    <template v-if="item.description">
                                        <div class="text-body-2 mb-4">
                                            {{ item.description }}
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div
                                            class="text-body-2 text-medium-emphasis font-italic mb-4"
                                        >
                                            Sin descripción
                                        </div>
                                    </template>

                                    <v-divider class="mb-4"></v-divider>

                                    <div class="d-flex align-center gap-4">
                                        <v-chip
                                            size="small"
                                            :color="
                                                item.status === 'active'
                                                    ? 'success'
                                                    : 'grey'
                                            "
                                        >
                                            <v-icon start size="small">
                                                {{
                                                    item.status === 'active'
                                                        ? 'mdi-check-circle'
                                                        : 'mdi-circle-outline'
                                                }}
                                            </v-icon>
                                            {{
                                                item.status === 'active'
                                                    ? 'Activo'
                                                    : 'Borrador'
                                            }}
                                        </v-chip>

                                        <v-chip
                                            v-if="item.skills_count > 0"
                                            size="small"
                                            color="info"
                                            variant="flat"
                                        >
                                            <v-icon start size="small"
                                                >mdi-star-circle</v-icon
                                            >
                                            {{ item.skills_count }} Skills
                                            asociados
                                        </v-chip>
                                    </div>

                                    <div v-if="item.agent" class="mt-4">
                                        <div
                                            class="text-caption text-medium-emphasis mb-1"
                                        >
                                            Agente de Diseño:
                                        </div>
                                        <v-chip
                                            size="small"
                                            color="indigo"
                                            variant="flat"
                                        >
                                            <v-icon start size="small"
                                                >mdi-robot</v-icon
                                            >
                                            {{ item.agent.name }}
                                        </v-chip>
                                    </div>
                                </v-card>
                            </v-window-item>

                            <v-window-item value="ai">
                                <v-card variant="outlined" class="pa-4">
                                    <div
                                        class="d-flex align-center justify-space-between mb-4"
                                    >
                                        <div>
                                            <div
                                                class="text-subtitle-1 font-weight-bold"
                                            >
                                                Curación con IA ({{
                                                    item.agent?.name ||
                                                    'Agente Genérico'
                                                }})
                                            </div>
                                            <div
                                                class="text-caption text-medium-emphasis"
                                            >
                                                Genera los detalles, BARS,
                                                indicadores y learning paths
                                                automáticamente.
                                            </div>
                                        </div>
                                        <v-btn
                                            color="indigo"
                                            variant="flat"
                                            :loading="curating"
                                            @click="
                                                curateCompetency(
                                                    item.id,
                                                    refresh,
                                                )
                                            "
                                        >
                                            <v-icon start
                                                >mdi-magic-staff</v-icon
                                            >
                                            Curar Competencia
                                        </v-btn>
                                    </div>

                                    <v-alert
                                        type="info"
                                        variant="tonal"
                                        class="mb-4"
                                        v-if="!item.skills_count"
                                    >
                                        <template #title>Atención</template>
                                        Esta competencia aún no tiene skills
                                        asociadas. La IA intentará derivar las
                                        sub-habilidades primero.
                                    </v-alert>
                                </v-card>
                            </v-window-item>
                        </v-window>
                    </v-card-text>
                </v-card>
            </template>
        </FormSchema>
    </div>
</template>
