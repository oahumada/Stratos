<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
} from '@/types/form-schema';
import { ref } from 'vue';
import FormSchema from '../form-template/FormSchema.vue';
import configJson from './people-form/config.json';
import filtersJson from './people-form/filters.json';
import itemFormJson from './people-form/itemForm.json';

import AssessmentChat from '@/components/Assessments/AssessmentChat.vue';
import FeedbackRequestDialog from '@/components/Assessments/FeedbackRequestDialog.vue';
import DevelopmentTab from '@/components/Talent/DevelopmentTab.vue';
import tableConfigJson from './people-form/tableConfig.json';

defineOptions({ layout: AppLayout });

// Load configs from JSON files
const config: Config = configJson as Config;
const tableConfig: TableConfig = tableConfigJson as unknown as TableConfig;
const itemForm: ItemForm = itemFormJson as unknown as ItemForm;
const filters: FilterConfig[] = filtersJson as unknown as FilterConfig[];

const formatDate = (value: any): string => {
    if (!value) return '';
    try {
        const date = new Date(value);
        if (!isNaN(date.getTime())) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    } catch (e) {
        void e;
        return value as string;
    }
    return value as string;
};

const formRef = ref(null);

const handleRefresh = () => {
    if (formRef.value) {
        (formRef.value as any).loadItems();
    }
};
</script>

<template>
    <FormSchema
        ref="formRef"
        :config="config"
        :table-config="tableConfig"
        :item-form="itemForm"
        :filters="filters"
        :enable-row-detail="true"
    >
        <template #detail="{ item, tab, setTab, sync, close }">
            <v-card flat border class="pa-3">
                <div class="text-h6 font-weight-bold">
                    {{ item.first_name }} {{ item.last_name }}
                </div>
                <div class="text-body-2 text-secondary">{{ item.email }}</div>
                <div class="text-body-2">
                    Depto: {{ item.department?.name || item.department_id }}
                </div>
                <div class="text-body-2">
                    Rol: {{ item.role?.name || item.role_id }}
                </div>
                <div class="text-body-2">
                    Hired: {{ formatDate(item.hire_date) }}
                </div>
            </v-card>

            <div class="d-flex align-center justify-space-between mt-4">
                <v-tabs
                    :model-value="tab"
                    density="compact"
                    color="primary"
                    @update:modelValue="setTab"
                >
                    <v-tab value="active">Skills Activas</v-tab>
                    <v-tab value="psychometric">Potencial AI</v-tab>
                    <v-tab value="development">Desarrollo</v-tab>
                    <v-tab value="history">Historial</v-tab>
                </v-tabs>
                <div class="d-flex align-center gap-2">
                    <v-btn
                        size="small"
                        color="primary"
                        variant="tonal"
                        prepend-icon="mdi-sync"
                        @click="sync"
                    >
                        Sincronizar con rol
                    </v-btn>
                    <v-btn
                        size="small"
                        color="primary"
                        variant="elevated"
                        prepend-icon="mdi-account-details"
                        @click="$inertia.visit(`/people/${item.id}`)"
                    >
                        Ver Perfil Completo
                    </v-btn>
                    <v-btn
                        size="small"
                        variant="text"
                        icon="mdi-close"
                        @click="close"
                    />
                </div>
            </div>

            <v-window :model-value="tab" class="mt-3">
                <v-window-item value="active">
                    <v-card flat border class="pa-3" v-if="item.skills?.length">
                        <div class="text-subtitle-2 mb-2">Skills activas</div>
                        <div class="d-flex flex-wrap gap-2">
                            <v-chip
                                v-for="skill in item.skills"
                                :key="skill.id"
                                size="small"
                                class="text-capitalize"
                                :color="
                                    skill.pivot?.current_level >=
                                    skill.pivot?.required_level
                                        ? 'success'
                                        : 'warning'
                                "
                                variant="tonal"
                            >
                                {{ skill.name }} ({{
                                    skill.pivot?.current_level
                                }}/{{ skill.pivot?.required_level }})
                            </v-chip>
                        </div>
                    </v-card>
                    <v-alert
                        v-else
                        type="info"
                        density="comfortable"
                        variant="tonal"
                    >
                        Sin skills activas en el pivote `people_role_skills`.
                    </v-alert>
                </v-window-item>

                <v-window-item value="psychometric">
                    <div v-if="item.psychometric_profiles?.length" class="pa-1">
                        <v-alert
                            type="success"
                            variant="tonal"
                            class="mb-4"
                            density="compact"
                        >
                            {{
                                item.metadata?.summary_report ||
                                'Perfil analizado satisfactoriamente.'
                            }}
                        </v-alert>

                        <v-alert
                            v-if="item.metadata?.blind_spots?.length"
                            type="warning"
                            variant="tonal"
                            class="mb-4"
                            density="compact"
                            icon="mdi-eye-off"
                        >
                            <template #title
                                >Visualización de Puntos Ciegos</template
                            >
                            <ul class="mt-2 pl-4">
                                <li
                                    v-for="(spot, i) in item.metadata
                                        .blind_spots"
                                    :key="i"
                                >
                                    {{ spot }}
                                </li>
                            </ul>
                        </v-alert>

                        <div class="text-subtitle-2 mb-3">
                            Rasgos y Aptitudes Detectadas
                        </div>
                        <v-row>
                            <v-col
                                v-for="trait in item.psychometric_profiles"
                                :key="trait.id"
                                cols="12"
                                md="6"
                            >
                                <v-card
                                    flat
                                    border
                                    class="pa-3 bg-light-blue-lighten-5"
                                >
                                    <div
                                        class="d-flex justify-space-between align-center mb-1"
                                    >
                                        <div
                                            class="text-caption font-weight-bold text-uppercase"
                                        >
                                            {{ trait.trait_name }}
                                        </div>
                                        <div class="text-h6 font-weight-black">
                                            {{
                                                (trait.score * 100).toFixed(0)
                                            }}%
                                        </div>
                                    </div>
                                    <v-progress-linear
                                        :model-value="trait.score * 100"
                                        color="primary"
                                        height="6"
                                        rounded
                                    ></v-progress-linear>
                                    <div
                                        class="text-caption mt-2 line-clamp-2 text-secondary"
                                    >
                                        {{ trait.rationale }}
                                    </div>
                                </v-card>
                            </v-col>
                        </v-row>

                        <v-divider class="my-4"></v-divider>
                        <div class="d-flex justify-center gap-3">
                            <v-btn
                                size="small"
                                variant="text"
                                color="primary"
                                prepend-icon="mdi-refresh"
                            >
                                Re-evaluar Potencial
                            </v-btn>
                            <FeedbackRequestDialog
                                :subject-id="item.id"
                                @requested="handleRefresh"
                            />
                        </div>
                    </div>
                    <div v-else>
                        <AssessmentChat
                            :person-id="item.id"
                            @completed="handleRefresh"
                        />
                    </div>
                </v-window-item>

                <v-window-item value="development">
                    <DevelopmentTab
                        :person-id="item.id"
                        :skills="item.skills || []"
                    />
                </v-window-item>

                <v-window-item value="history">
                    <v-alert type="info" density="comfortable" variant="tonal">
                        Historial no cargado (requiere endpoint de
                        `people_role_skills` inactivos).
                    </v-alert>
                </v-window-item>
            </v-window>
        </template>
    </FormSchema>
</template>

<style scoped>
/* Custom styles */
</style>
