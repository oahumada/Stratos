<script setup lang="ts">
import SkillLevelChip from '@/components/SkillLevelChip.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
} from '@/types/form-schema';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import FormSchema from '../form-template/FormSchema.vue';

// Import JSON configs
import configJson from './roles-form/config.json';
import filtersJson from './roles-form/filters.json';
import itemFormJson from './roles-form/itemForm.json';
import tableConfigJson from './roles-form/tableConfig.json';

defineOptions({ layout: AppLayout });

// Detail tab state
const detailTab = ref('info');

// Skill levels for chips
const skillLevels = ref<any[]>([]);

onMounted(async () => {
    try {
        const response = await axios.get('/api/catalogs', {
            params: { catalogs: ['skill_levels'] },
        });
        skillLevels.value = response.data.skill_levels || [];
    } catch (error) {
        console.error('Error loading skill levels:', error);
    }
});

const designing = ref(false);
const designRole = async (id: number, refresh: () => void) => {
    designing.value = true;
    try {
        await axios.post(`/api/strategic-planning/roles/${id}/design`);
        refresh();
    } catch (error) {
        console.error('Error designing role:', error);
    } finally {
        designing.value = false;
    }
};

// Helpers to map relations safely
const getRoleSkills = (item: any) => {
    if (!item?.skills) return [];

    return item.skills.map((skill: any) => ({
        id: skill.id,
        name: skill.name,
        category: skill.category,
        required_level: skill.pivot?.required_level || 0,
        is_critical: skill.pivot?.is_critical || false,
    }));
};

const getRolePeople = (item: any) => {
    return Array.isArray(item?.people) ? item.people : [];
};

const getPersonName = (person: any) => {
    if (!person) return 'Sin nombre';
    const fullName =
        `${person.first_name || ''} ${person.last_name || ''}`.trim();
    return fullName || person.name || 'Sin nombre';
};

const getPersonDepartment = (person: any) => {
    return (
        person?.department?.name ||
        person?.department_full_name ||
        person?.department_name ||
        null
    );
};

// Load configs from JSON files
const config: Config = configJson as Config;
const tableConfig: TableConfig = tableConfigJson as unknown as TableConfig;
const itemForm: ItemForm = itemFormJson as unknown as ItemForm;
const filters: FilterConfig[] = filtersJson as unknown as FilterConfig[];
</script>

<template>
    <FormSchema
        :config="config"
        :table-config="tableConfig"
        :item-form="itemForm"
        :filters="filters"
        enable-row-detail
    >
        <template #detail="{ item, refresh }">
            <v-tabs v-model="detailTab">
                <v-tab value="info">
                    <v-icon start>mdi-information</v-icon>
                    Información
                </v-tab>
                <v-tab value="skills">
                    <v-icon start>mdi-star-circle</v-icon>
                    Skills ({{ item.skills_count || item.skills?.length || 0 }})
                </v-tab>
                <v-tab value="people">
                    <v-icon start>mdi-account-group</v-icon>
                    Personas ({{
                        item.People_count ||
                        item.people_count ||
                        item.people?.length ||
                        0
                    }})
                </v-tab>
                <v-tab value="ai-design">
                    <v-icon start>mdi-cube-outline</v-icon>
                    Diseño de Rol (AI)
                </v-tab>
            </v-tabs>

            <v-window v-model="detailTab" class="mt-4">
                <!-- Info Tab -->
                <v-window-item value="info">
                    <v-card flat border class="pa-3">
                        <div class="text-subtitle-2 mb-3">
                            Información del Rol
                        </div>
                        <v-list density="compact">
                            <v-list-item>
                                <v-list-item-title class="text-body-2">
                                    <strong>Nombre:</strong> {{ item.name }}
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.description">
                                <v-list-item-title class="text-body-2">
                                    <strong>Descripción:</strong>
                                    {{ item.description }}
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>

                        <v-divider class="my-3"></v-divider>

                        <v-btn
                            color="indigo"
                            prepend-icon="mdi-auto-fix"
                            :loading="designing"
                            @click="designRole(item.id, refresh)"
                            variant="tonal"
                            size="small"
                        >
                            Diseñar con AI (Modelo Cubo)
                        </v-btn>
                    </v-card>
                </v-window-item>

                <!-- Skills Tab -->
                <v-window-item value="skills">
                    <v-card flat border class="pa-3">
                        <div class="text-subtitle-2 mb-3">
                            Skills requeridas
                        </div>
                        <div
                            v-if="getRoleSkills(item).length === 0"
                            class="py-4 text-center text-secondary"
                        >
                            No hay skills asignadas
                        </div>
                        <v-list v-else density="compact">
                            <v-list-item
                                v-for="skill in getRoleSkills(item)"
                                :key="skill.id"
                                class="mb-2"
                                border
                            >
                                <template #prepend>
                                    <v-avatar color="primary" size="32">
                                        <v-icon size="small"
                                            >mdi-star-circle</v-icon
                                        >
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="text-body-2 font-weight-medium"
                                >
                                    {{ skill.name }}
                                </v-list-item-title>
                                <v-list-item-subtitle class="text-caption">
                                    Nivel requerido:
                                    <SkillLevelChip
                                        :level="skill.required_level"
                                        :skill-levels="skillLevels"
                                        color="black"
                                        class="ml-1"
                                        size="small"
                                    />
                                    <v-chip
                                        v-if="skill.is_critical"
                                        size="small"
                                        color="error"
                                        class="ml-2"
                                    >
                                        Crítica
                                    </v-chip>
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-window-item>

                <!-- People Tab -->
                <v-window-item value="people">
                    <v-card flat border class="pa-3">
                        <div class="text-subtitle-2 mb-3">
                            Personas en este rol
                        </div>
                        <div
                            v-if="getRolePeople(item).length === 0"
                            class="py-4 text-center text-secondary"
                        >
                            No hay personas asignadas
                        </div>
                        <v-list v-else density="compact">
                            <v-list-item
                                v-for="person in getRolePeople(item)"
                                :key="person.id"
                                class="mb-2"
                                border
                            >
                                <template #prepend>
                                    <v-avatar color="secondary" size="32">
                                        <v-icon size="small"
                                            >mdi-account</v-icon
                                        >
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="text-body-2 font-weight-medium"
                                >
                                    {{ getPersonName(person) }}
                                </v-list-item-title>
                                <v-list-item-subtitle class="text-caption">
                                    {{
                                        person.email
                                            ? `(${person.email})`
                                            : '(Sin correo)'
                                    }}
                                    <span v-if="getPersonDepartment(person)">
                                        ·
                                        {{ getPersonDepartment(person) }}</span
                                    >
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-window-item>

                <!-- AI Design Tab -->
                <v-window-item value="ai-design">
                    <v-card flat border class="pa-3">
                        <div
                            class="d-flex justify-space-between align-center mb-4"
                        >
                            <div class="text-subtitle-2">
                                Análisis de Diseño (Role Cube Methodology)
                            </div>
                            <v-btn
                                v-if="item.ai_archetype_config"
                                color="indigo"
                                size="small"
                                variant="text"
                                prepend-icon="mdi-refresh"
                                :loading="designing"
                                @click="designRole(item.id, refresh)"
                            >
                                Volver a analizar
                            </v-btn>
                        </div>

                        <div
                            v-if="!item.ai_archetype_config"
                            class="bg-grey-lighten-4 rounded py-8 text-center"
                        >
                            <v-icon
                                size="64"
                                color="grey-lighten-1"
                                class="mb-4"
                                >mdi-cube-scan</v-icon
                            >
                            <div class="text-h6 text-grey-darken-1">
                                Diseño no analizado
                            </div>
                            <p class="text-body-2 mb-6 text-secondary">
                                Usa la Inteligencia Artificial para definir las
                                coordenadas de este rol en la organización.
                            </p>
                            <v-btn
                                color="indigo"
                                size="large"
                                prepend-icon="mdi-auto-fix"
                                :loading="designing"
                                @click="designRole(item.id, refresh)"
                            >
                                Analizar con IA
                            </v-btn>
                        </div>

                        <div v-else>
                            <v-row>
                                <!-- Cube Visualization -->
                                <v-col cols="12" md="4">
                                    <v-card
                                        variant="outlined"
                                        class="pa-4 bg-indigo-lighten-5 border-indigo"
                                    >
                                        <div
                                            class="text-overline text-indigo font-weight-bold mb-2"
                                        >
                                            COORDENADAS DEL CUBO
                                        </div>

                                        <div class="mb-4">
                                            <div
                                                class="text-caption text-grey-darken-1 text-uppercase mb-1 italic"
                                            >
                                                Eje X: Arquetipo
                                            </div>
                                            <div class="d-flex align-center">
                                                <v-chip
                                                    color="indigo"
                                                    size="small"
                                                    class="mr-2"
                                                    >{{
                                                        item.ai_archetype_config
                                                            .cube_coordinates
                                                            .x_archetype
                                                    }}</v-chip
                                                >
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div
                                                class="text-caption text-grey-darken-1 text-uppercase mb-1 italic"
                                            >
                                                Eje Y: Maestría
                                            </div>
                                            <div class="d-flex align-center">
                                                <v-rating
                                                    :model-value="
                                                        item.ai_archetype_config
                                                            .cube_coordinates
                                                            .y_mastery_level
                                                    "
                                                    length="5"
                                                    readonly
                                                    density="compact"
                                                    color="indigo"
                                                    active-color="indigo"
                                                ></v-rating>
                                                <span
                                                    class="font-weight-bold ml-2"
                                                    >{{
                                                        item.ai_archetype_config
                                                            .cube_coordinates
                                                            .y_mastery_level
                                                    }}/5</span
                                                >
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <div
                                                class="text-caption text-grey-darken-1 text-uppercase mb-1 italic"
                                            >
                                                Eje Z: Proceso
                                            </div>
                                            <div
                                                class="text-body-2 font-weight-medium text-indigo-darken-2"
                                            >
                                                {{
                                                    item.ai_archetype_config
                                                        .cube_coordinates
                                                        .z_business_process
                                                }}
                                            </div>
                                        </div>
                                    </v-card>

                                    <v-card
                                        variant="flat"
                                        class="pa-4 bg-grey-lighten-4 mt-4 border"
                                    >
                                        <div
                                            class="text-caption font-weight-bold text-grey-darken-2 mb-2"
                                        >
                                            JUSTIFICACIÓN DEL DISEÑO
                                        </div>
                                        <div
                                            class="text-body-2 text-grey-darken-3 italic"
                                        >
                                            "{{
                                                item.ai_archetype_config
                                                    .cube_coordinates
                                                    .justification
                                            }}"
                                        </div>
                                    </v-card>
                                </v-col>

                                <!-- Competencies & Suggestions -->
                                <v-col cols="12" md="8">
                                    <div class="mb-6">
                                        <div
                                            class="text-subtitle-2 font-weight-bold d-flex align-center mb-3"
                                        >
                                            <v-icon start color="teal"
                                                >mdi-check-decagram</v-icon
                                            >
                                            Competencias Core Sugeridas
                                        </div>
                                        <v-list
                                            border
                                            rounded
                                            density="compact"
                                            class="bg-transparent"
                                        >
                                            <v-list-item
                                                v-for="(comp, i) in item
                                                    .ai_archetype_config
                                                    .core_competencies"
                                                :key="i"
                                                border
                                                class="mb-2"
                                            >
                                                <v-list-item-title
                                                    class="font-weight-bold text-body-2 d-flex align-center"
                                                >
                                                    {{ comp.name }}
                                                    <v-chip
                                                        size="x-small"
                                                        class="ml-2"
                                                        color="teal"
                                                        variant="outlined"
                                                        >Nivel
                                                        {{ comp.level }}</v-chip
                                                    >
                                                </v-list-item-title>
                                                <v-list-item-subtitle
                                                    class="text-caption mt-1"
                                                    >{{
                                                        comp.rationale
                                                    }}</v-list-item-subtitle
                                                >
                                            </v-list-item>
                                        </v-list>
                                    </div>

                                    <div>
                                        <div
                                            class="text-subtitle-2 font-weight-bold d-flex align-center mb-3"
                                        >
                                            <v-icon start color="amber-darken-2"
                                                >mdi-lightbulb-on</v-icon
                                            >
                                            Nitidez Organizacional
                                        </div>
                                        <v-alert
                                            color="amber-lighten-5"
                                            border="start"
                                            border-color="amber-darken-2"
                                            class="text-body-2 text-grey-darken-3"
                                        >
                                            {{
                                                item.ai_archetype_config
                                                    .organizational_suggestions
                                            }}
                                        </v-alert>
                                    </div>
                                </v-col>
                            </v-row>
                        </div>
                    </v-card>
                </v-window-item>
            </v-window>
        </template>
    </FormSchema>
</template>

<style scoped>
.italic {
    font-style: italic;
}
</style>
