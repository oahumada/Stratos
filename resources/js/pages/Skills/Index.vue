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
import configJson from './skills-form/config.json';
import filtersJson from './skills-form/filters.json';
import itemFormJson from './skills-form/itemForm.json';
import tableConfigJson from './skills-form/tableConfig.json';

defineOptions({ layout: AppLayout });

// State for detail tabs
const detailTab = ref('info');

// State for skill levels
const skillLevels = ref<any[]>([]);

// Load skill levels on mount
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

const curating = ref(false);
const generatingQuestions = ref(false);

const curateSkill = async (id: number, refresh: () => void) => {
    curating.value = true;
    try {
        await axios.post(
            `/api/strategic-planning/assessments/curator/skills/${id}/curate`,
        );
        refresh();
    } catch (error) {
        console.error('Error curating skill:', error);
    } finally {
        curating.value = false;
    }
};

const generateQuestions = async (id: number, refresh: () => void) => {
    generatingQuestions.value = true;
    try {
        await axios.post(
            `/api/strategic-planning/assessments/curator/skills/${id}/generate-questions`,
        );
        refresh();
    } catch (error) {
        console.error('Error generating questions:', error);
    } finally {
        generatingQuestions.value = false;
    }
};

// Helper to get level name (kept for future use)
const _getLevelName = (level: number) => {
    const levelDef = skillLevels.value.find((l) => l.level === level);
    return levelDef ? levelDef.name : `Nivel ${level}`;
};

// Helper to get level display label (kept for future use)
const _getLevelDisplay = (level: number) => {
    const levelDef = skillLevels.value.find((l) => l.level === level);
    return levelDef ? levelDef.display_label : `Nivel ${level}`;
};

// reference to avoid unused-var during iterative refactor
void _getLevelName;
void _getLevelDisplay;

// Computed properties para roles y people basados en el item que viene con las relaciones
const getSkillRoles = (item: any) => {
    if (!item.roles) return [];

    return item.roles.map((role: any) => ({
        id: role.id,
        role: { name: role.name, level: role.level },
        required_level: role.pivot?.required_level || 0,
        is_critical: role.pivot?.is_critical || false,
    }));
};

const getSkillPeople = (item: any) => {
    return item.people_role_skills || [];
};

// Load configs from JSON files
const config: Config = configJson as Config;
const tableConfig: TableConfig = tableConfigJson as unknown as TableConfig;
const itemForm: ItemForm = itemFormJson as unknown as ItemForm;
const filters: FilterConfig[] = filtersJson as unknown as FilterConfig[];

// Skill levels descriptions
const skillLevelDescriptions = [
    {
        level: 1,
        name: 'Básico',
        description:
            'Conocimiento fundamental. Puede realizar tareas simples bajo supervisión. Requiere soporte para decisiones complejas.',
    },
    {
        level: 2,
        name: 'Intermedio',
        description:
            'Competencia establecida. Puede trabajar de forma independiente en tareas estándar. Requiere orientación en situaciones nuevas.',
    },
    {
        level: 3,
        name: 'Avanzado',
        description:
            'Dominio demostrado. Resuelve problemas complejos de forma autónoma. Puede asesorar a otros en la habilidad.',
    },
    {
        level: 4,
        name: 'Experto',
        description:
            'Dominio excepcional. Líder en la habilidad. Desarrolla mejores prácticas y mentoriza equipos.',
    },
    {
        level: 5,
        name: 'Maestría',
        description:
            'Expertise excepcional. Referente organizacional. Impulsa innovación y establece estándares de excelencia.',
    },
];
</script>

<template>
    <div class="mb-6">
        <v-card class="mb-6" variant="elevated">
            <v-card-title class="text-h6 text-primary">
                <v-icon start>mdi-lightbulb-outline</v-icon>
                Módulo de Habilidades (Skills)
            </v-card-title>
            <v-card-text>
                <p class="text-body-2 mb-4">
                    <strong>Objetivo del Módulo:</strong> Este módulo permite
                    gestionar y catalogar las habilidades (skills) de la
                    organización. Las habilidades pueden ser técnicas o blandas,
                    y se relacionan con los roles disponibles y las personas que
                    las poseen. Cada habilidad tiene un nivel asociado que
                    indica el grado de dominio requerido.
                </p>

                <div>
                    <p class="text-body-2 font-weight-bold mb-3">
                        Niveles de Habilidades (1-5):
                    </p>
                    <v-row dense>
                        <v-col
                            v-for="level in skillLevelDescriptions"
                            :key="level.level"
                            cols="12"
                            md="6"
                            lg="4"
                            xl="3"
                        >
                            <v-card variant="outlined" class="h-100">
                                <v-card-title class="text-subtitle-2">
                                    <v-chip
                                        :color="
                                            level.level === 1
                                                ? 'blue'
                                                : level.level === 2
                                                  ? 'cyan'
                                                  : level.level === 3
                                                    ? 'green'
                                                    : level.level === 4
                                                      ? 'orange'
                                                      : 'red'
                                        "
                                        text-color="white"
                                        size="small"
                                        class="mr-2"
                                    >
                                        Nivel {{ level.level }}
                                    </v-chip>
                                    {{ level.name }}
                                </v-card-title>
                                <v-card-text class="text-caption">
                                    {{ level.description }}
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </div>
            </v-card-text>
        </v-card>
    </div>

    <FormSchema
        :config="config"
        :table-config="tableConfig"
        :item-form="itemForm"
        :filters="filters"
    >
        <template #detail="{ item, refresh }">
            <v-tabs v-model="detailTab">
                <v-tab value="info">
                    <v-icon start>mdi-information</v-icon>
                    Información
                </v-tab>
                <v-tab value="people">
                    <v-icon start>mdi-account-group</v-icon>
                    Personas ({{ item.people_count || 0 }})
                </v-tab>
                <v-tab value="bars">
                    <v-icon start>mdi-stairs</v-icon>
                    BARS / Niveles
                </v-tab>
                <v-tab value="questions">
                    <v-icon start>mdi-database-search</v-icon>
                    Preguntas
                </v-tab>
            </v-tabs>

            <v-window v-model="detailTab" class="mt-4">
                <!-- Info Tab -->
                <v-window-item value="info">
                    <v-card flat border class="pa-3">
                        <div class="text-subtitle-2 mb-3">
                            Información de la Skill
                        </div>
                        <v-list density="compact">
                            <v-list-item>
                                <v-list-item-title class="text-body-2">
                                    <strong>Nombre:</strong> {{ item.name }}
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-title class="text-body-2">
                                    <strong>Categoría:</strong>
                                    <v-chip
                                        size="small"
                                        class="ml-2"
                                        :color="
                                            item.category === 'technical'
                                                ? 'primary'
                                                : item.category === 'soft'
                                                  ? 'success'
                                                  : 'warning'
                                        "
                                    >
                                        {{ item.category }}
                                    </v-chip>
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.description">
                                <v-list-item-title class="text-body-2">
                                    <strong>Descripción:</strong>
                                    {{ item.description }}
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-title class="text-body-2">
                                    <strong>Crítica:</strong>
                                    <v-chip
                                        size="small"
                                        class="ml-2"
                                        :color="
                                            item.is_critical
                                                ? 'error'
                                                : 'default'
                                        "
                                    >
                                        {{ item.is_critical ? 'Sí' : 'No' }}
                                    </v-chip>
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>

                        <v-divider class="my-3"></v-divider>

                        <div class="d-flex gap-2">
                            <v-btn
                                color="indigo"
                                prepend-icon="mdi-auto-fix"
                                :loading="curating"
                                @click="curateSkill(item.id, refresh)"
                                variant="tonal"
                                size="small"
                            >
                                Curar con IA (BARS)
                            </v-btn>
                            <v-btn
                                v-if="
                                    item.bars_levels &&
                                    item.bars_levels.length > 0
                                "
                                color="teal"
                                prepend-icon="mdi-chat-question"
                                :loading="generatingQuestions"
                                @click="generateQuestions(item.id, refresh)"
                                variant="tonal"
                                size="small"
                            >
                                Generar Preguntas
                            </v-btn>
                        </div>
                    </v-card>
                </v-window-item>

                <!-- Roles Tab -->
                <v-window-item value="roles">
                    <v-card flat border class="pa-3">
                        <div class="text-subtitle-2 mb-3">
                            Roles que requieren esta skill
                        </div>
                        <div
                            v-if="getSkillRoles(item).length === 0"
                            class="py-4 text-center text-secondary"
                        >
                            No hay roles asignados
                        </div>
                        <v-list v-else density="compact">
                            <v-list-item
                                v-for="roleSkill in getSkillRoles(item)"
                                :key="roleSkill.id"
                                class="mb-2"
                                border
                            >
                                <template #prepend>
                                    <v-avatar color="primary" size="32">
                                        <v-icon size="small"
                                            >mdi-account-tie</v-icon
                                        >
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="text-body-2 font-weight-medium"
                                >
                                    {{ roleSkill.role?.name || 'N/A' }}
                                </v-list-item-title>
                                <v-list-item-subtitle
                                    class="text-body-2 d-flex align-center mt-2 gap-2"
                                >
                                    <span class="font-weight-medium"
                                        >Nivel requerido:</span
                                    >
                                    <SkillLevelChip
                                        :level="roleSkill.required_level"
                                        :skill-levels="skillLevels"
                                        color="primary"
                                        size="large"
                                    />
                                    <v-chip
                                        v-if="roleSkill.is_critical"
                                        size="small"
                                        color="error"
                                        text-color="white"
                                        class="ml-auto"
                                    >
                                        <v-icon start size="small"
                                            >mdi-alert</v-icon
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
                            Empleados con esta skill
                        </div>
                        <div
                            v-if="getSkillPeople(item).length === 0"
                            class="py-4 text-center text-secondary"
                        >
                            No hay empleados con esta skill
                        </div>
                        <v-list v-else density="compact">
                            <v-list-item
                                v-for="peopleSkill in getSkillPeople(item)"
                                :key="peopleSkill.id"
                                class="mb-2"
                                border
                            >
                                <template #prepend>
                                    <v-avatar color="success" size="32">
                                        <v-icon size="small"
                                            >mdi-account</v-icon
                                        >
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="text-body-2 font-weight-medium"
                                >
                                    {{
                                        peopleSkill.person
                                            ? `${peopleSkill.person.first_name} ${peopleSkill.person.last_name}`
                                            : 'N/A'
                                    }}
                                </v-list-item-title>
                                <v-list-item-subtitle class="text-caption">
                                    <span class="font-weight-medium"
                                        >Actual:</span
                                    >
                                    <SkillLevelChip
                                        :level="peopleSkill.current_level"
                                        :skill-levels="skillLevels"
                                        :color="
                                            peopleSkill.current_level >=
                                            peopleSkill.required_level
                                                ? 'success'
                                                : 'warning'
                                        "
                                        class="ml-1"
                                    />
                                    <span class="mx-2">•</span>
                                    <span class="font-weight-medium"
                                        >Requerido:</span
                                    >
                                    <SkillLevelChip
                                        :level="peopleSkill.required_level"
                                        :skill-levels="skillLevels"
                                        color="primary"
                                        class="ml-1"
                                    />
                                    <v-chip
                                        v-if="
                                            peopleSkill.current_level >=
                                            peopleSkill.required_level
                                        "
                                        size="x-small"
                                        color="success"
                                        class="ml-2"
                                    >
                                        <v-icon start size="x-small"
                                            >mdi-check</v-icon
                                        >
                                        Cumple
                                    </v-chip>
                                    <v-chip
                                        v-else
                                        size="x-small"
                                        color="warning"
                                        class="ml-2"
                                    >
                                        <v-icon start size="x-small"
                                            >mdi-alert</v-icon
                                        >
                                        Gap:
                                        {{
                                            peopleSkill.required_level -
                                            peopleSkill.current_level
                                        }}
                                        nivel(es)
                                    </v-chip>
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-window-item>

                <!-- BARS Tab -->
                <v-window-item value="bars">
                    <v-card flat border class="pa-3">
                        <div
                            class="d-flex justify-space-between align-center mb-3"
                        >
                            <div class="text-subtitle-2">
                                Definiciones BARS (Behaviorally Anchored Rating
                                Scales)
                            </div>
                        </div>

                        <div
                            v-if="
                                !item.bars_levels ||
                                item.bars_levels.length === 0
                            "
                            class="py-8 text-center"
                            border
                        >
                            <v-icon
                                size="48"
                                color="grey-lighten-1"
                                class="mb-2"
                                >mdi-stairs</v-icon
                            >
                            <div class="text-body-2 text-secondary">
                                No hay definiciones de niveles curadas para esta
                                skill.
                            </div>
                            <v-btn
                                color="indigo"
                                variant="tonal"
                                class="mt-4"
                                prepend-icon="mdi-auto-fix"
                                :loading="curating"
                                @click="curateSkill(item.id, refresh)"
                            >
                                Generar Definiciones con AI
                            </v-btn>
                        </div>

                        <div v-else border>
                            <v-expansion-panels variant="accordion">
                                <v-expansion-panel
                                    v-for="level in item.bars_levels"
                                    :key="level.id"
                                >
                                    <v-expansion-panel-title>
                                        <div class="d-flex align-center gap-4">
                                            <v-chip
                                                :color="
                                                    level.level > 3
                                                        ? 'success'
                                                        : level.level === 3
                                                          ? 'primary'
                                                          : 'warning'
                                                "
                                                size="small"
                                            >
                                                Nivel {{ level.level }}
                                            </v-chip>
                                            <span class="font-weight-medium">{{
                                                level.level_name
                                            }}</span>
                                        </div>
                                    </v-expansion-panel-title>
                                    <v-expansion-panel-text>
                                        <div class="mb-4">
                                            <div
                                                class="text-caption font-weight-bold text-uppercase text-grey-darken-1 mb-1"
                                            >
                                                Comportamiento Esperado
                                            </div>
                                            <div class="text-body-2">
                                                {{
                                                    level.behavioral_description
                                                }}
                                            </div>
                                        </div>
                                        <v-divider class="mb-4"></v-divider>
                                        <v-row>
                                            <v-col cols="12" md="6">
                                                <div
                                                    class="text-caption font-weight-bold text-uppercase text-indigo mb-1"
                                                >
                                                    Contenido de Aprendizaje
                                                </div>
                                                <div class="text-body-2">
                                                    {{ level.learning_content }}
                                                </div>
                                            </v-col>
                                            <v-col cols="12" md="6">
                                                <div
                                                    class="text-caption font-weight-bold text-uppercase text-teal mb-1"
                                                >
                                                    Indicador de Desempeño (KPI)
                                                </div>
                                                <div class="text-body-2">
                                                    {{
                                                        level.performance_indicator
                                                    }}
                                                </div>
                                            </v-col>
                                        </v-row>
                                    </v-expansion-panel-text>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </div>
                    </v-card>
                </v-window-item>

                <!-- Questions Tab -->
                <v-window-item value="questions">
                    <v-card flat border class="pa-3">
                        <div
                            class="d-flex justify-space-between align-center mb-3"
                        >
                            <div class="text-subtitle-2">
                                Banco de Preguntas de Validación
                            </div>
                            <v-btn
                                v-if="
                                    item.questions && item.questions.length > 0
                                "
                                color="teal"
                                size="small"
                                variant="text"
                                prepend-icon="mdi-refresh"
                                :loading="generatingQuestions"
                                @click="generateQuestions(item.id, refresh)"
                            >
                                Regenerar
                            </v-btn>
                        </div>

                        <div
                            v-if="
                                !item.questions || item.questions.length === 0
                            "
                            class="py-8 text-center"
                            border
                        >
                            <v-icon
                                size="48"
                                color="grey-lighten-1"
                                class="mb-2"
                                >mdi-database-search</v-icon
                            >
                            <div class="text-body-2 text-secondary">
                                No hay preguntas generadas para esta skill.
                            </div>
                            <v-btn
                                :disabled="
                                    !item.bars_levels ||
                                    item.bars_levels.length === 0
                                "
                                color="teal"
                                variant="tonal"
                                class="mt-4"
                                prepend-icon="mdi-chat-question"
                                :loading="generatingQuestions"
                                @click="generateQuestions(item.id, refresh)"
                            >
                                Generar Preguntas con AI
                            </v-btn>
                            <div
                                v-if="
                                    !item.bars_levels ||
                                    item.bars_levels.length === 0
                                "
                                class="text-caption text-error mt-2"
                            >
                                Requiere curar los niveles BARS primero.
                            </div>
                        </div>

                        <v-list v-else density="compact">
                            <v-list-item
                                v-for="q in item.questions"
                                :key="q.id"
                                class="mb-3"
                                border
                                rounded
                            >
                                <template #prepend>
                                    <v-avatar
                                        :color="
                                            q.level > 3
                                                ? 'success-lighten-4'
                                                : 'warning-lighten-4'
                                        "
                                        class="mr-3"
                                    >
                                        <span
                                            class="text-body-2 font-weight-bold"
                                            :class="
                                                q.level > 3
                                                    ? 'text-success-darken-2'
                                                    : 'text-warning-darken-2'
                                            "
                                            >L{{ q.level }}</span
                                        >
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="text-body-2 text-wrap"
                                    >{{ q.question }}</v-list-item-title
                                >
                                <template #append>
                                    <v-chip
                                        size="x-small"
                                        variant="outlined"
                                        color="grey"
                                        >{{ q.question_type }}</v-chip
                                    >
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-window-item>
            </v-window>
        </template>
    </FormSchema>
</template>

<style scoped>
/* Custom styles */
</style>
