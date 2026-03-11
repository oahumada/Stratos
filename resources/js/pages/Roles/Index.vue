<script setup lang="ts">
import RoleCubeWizard from '@/components/Roles/RoleCubeWizard.vue';
import SkillLevelChip from '@/components/SkillLevelChip.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
} from '@/types/form-schema';
import {
    PhArrowsClockwise,
    PhCube,
    PhFileText,
    PhInfo,
    PhLightbulb,
    PhMagicWand,
    PhRobot,
    PhSealCheck,
    PhStar,
    PhUser,
    PhUsers,
} from '@phosphor-icons/vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import FormSchema from '../form-template/FormSchema.vue';

const { t } = useI18n();

// Import JSON configs
import configJson from './roles-form/config.json';
import filtersJson from './roles-form/filters.json';
import itemFormJson from './roles-form/itemForm.json';
import tableConfigJson from './roles-form/tableConfig.json';

defineOptions({ layout: AppLayout });

// Wizard state
const showCubeWizard = ref(false);
const formSchemaRef = ref<any>(null);

const onRoleCreated = () => {
    formSchemaRef.value?.loadItems();
    showCubeWizard.value = false;
};

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
        ref="formSchemaRef"
        :config="config"
        :table-config="tableConfig"
        :item-form="itemForm"
        :filters="filters"
        enable-row-detail
    >
        <template #extra-actions>
            <StButtonGlass
                :icon="PhCube"
                variant="primary"
                size="lg"
                class="ml-2"
                @click="showCubeWizard = true"
            >
                {{ t('roles_module.ai_cube_design') }}
            </StButtonGlass>
        </template>

        <template #detail="{ item, refresh }">
            <v-tabs v-model="detailTab" color="indigo-accent-2" class="mb-4">
                <v-tab value="info" class="text-none">
                    <component :is="PhInfo" :size="18" class="mr-2" />
                    {{ t('roles_module.tabs.info') }}
                </v-tab>
                <v-tab value="skills" class="text-none">
                    <component :is="PhStar" :size="18" class="mr-2" />
                    {{
                        t('roles_module.tabs.skills', {
                            count:
                                item.skills_count || item.skills?.length || 0,
                        })
                    }}
                </v-tab>
                <v-tab value="people" class="text-none">
                    <component :is="PhUsers" :size="18" class="mr-2" />
                    {{
                        t('roles_module.tabs.people', {
                            count:
                                item.People_count ||
                                item.people_count ||
                                item.people?.length ||
                                0,
                        })
                    }}
                </v-tab>
                <v-tab value="ai-design" class="text-none">
                    <component :is="PhCube" :size="18" class="mr-2" />
                    {{ t('roles_module.tabs.ai_design') }}
                </v-tab>
            </v-tabs>

            <v-window v-model="detailTab">
                <!-- Info Tab -->
                <v-window-item value="info">
                    <v-card flat border class="pa-4 glass-card rounded-xl">
                        <div
                            class="text-subtitle-1 font-weight-bold font-premium d-flex align-center mb-4 text-white"
                        >
                            <component
                                :is="PhInfo"
                                :size="20"
                                class="text-indigo-accent-1 mr-2"
                            />
                            {{ t('roles_module.info_section.title') }}
                        </div>
                        <v-list density="compact" class="pa-0 bg-transparent">
                            <v-list-item class="px-0">
                                <v-list-item-title class="text-body-2">
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t('roles_module.info_section.name')
                                        }}:</strong
                                    >
                                    {{ item.name }}
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.description" class="px-0">
                                <v-list-item-title class="text-body-2">
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t(
                                                'roles_module.info_section.description',
                                            )
                                        }}:</strong
                                    >
                                    {{ item.description }}
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.purpose" class="px-0">
                                <v-list-item-title class="text-body-2">
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t(
                                                'roles_module.info_section.purpose',
                                            )
                                        }}:</strong
                                    >
                                    <div
                                        class="pa-3 mt-1 rounded-lg border border-indigo-500/20 bg-indigo-900/10 text-slate-200 italic"
                                    >
                                        {{ item.purpose }}
                                    </div>
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                v-if="item.expected_results"
                                class="px-0"
                            >
                                <v-list-item-title class="text-body-2">
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t(
                                                'roles_module.info_section.expected_results',
                                            )
                                        }}:</strong
                                    >
                                    <div
                                        class="pa-3 mt-1 rounded-lg border border-emerald-500/20 bg-emerald-900/10 text-slate-200"
                                    >
                                        {{ item.expected_results }}
                                    </div>
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.agent" class="px-0">
                                <v-list-item-title
                                    class="text-body-2 d-flex align-center"
                                >
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t(
                                                'roles_module.info_section.ai_agent',
                                            )
                                        }}:</strong
                                    >
                                    <v-chip
                                        size="small"
                                        color="indigo"
                                        variant="flat"
                                        class="ml-2 rounded-lg"
                                    >
                                        <component
                                            :is="PhRobot"
                                            start
                                            size="14"
                                            class="mr-1"
                                        />
                                        {{ item.agent.name }}
                                    </v-chip>
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.blueprint" class="px-0">
                                <v-list-item-title
                                    class="text-body-2 d-flex align-center"
                                >
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t(
                                                'roles_module.info_section.blueprint',
                                            )
                                        }}:</strong
                                    >
                                    <v-chip
                                        size="small"
                                        color="blue-grey"
                                        variant="outlined"
                                        class="ml-2 rounded-lg"
                                    >
                                        <component
                                            :is="PhFileText"
                                            start
                                            size="14"
                                            class="mr-1"
                                        />
                                        {{ item.blueprint.name }}
                                    </v-chip>
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>

                        <v-divider
                            class="my-4"
                            style="opacity: 0.05"
                        ></v-divider>

                        <StButtonGlass
                            :icon="PhMagicWand"
                            variant="primary"
                            size="sm"
                            :loading="designing"
                            @click="designRole(item.id, refresh)"
                        >
                            {{ t('roles_module.info_section.design_btn') }}
                        </StButtonGlass>
                    </v-card>
                </v-window-item>

                <!-- Skills Tab -->
                <v-window-item value="skills">
                    <v-card flat border class="pa-4 glass-card rounded-xl">
                        <div
                            class="text-subtitle-1 font-weight-bold font-premium d-flex align-center mb-4 text-white"
                        >
                            <component
                                :is="PhStar"
                                :size="20"
                                class="text-indigo-accent-1 mr-2"
                            />
                            {{ t('roles_module.skills_section.title') }}
                        </div>
                        <div
                            v-if="getRoleSkills(item).length === 0"
                            class="rounded-xl border border-dashed py-8 text-center text-slate-400"
                        >
                            {{ t('roles_module.skills_section.empty') }}
                        </div>
                        <v-list
                            v-else
                            density="comfortable"
                            class="pa-0 bg-transparent"
                        >
                            <v-list-item
                                v-for="skill in getRoleSkills(item)"
                                :key="skill.id"
                                class="glass-card mb-3 rounded-xl border"
                            >
                                <template #prepend>
                                    <v-avatar color="indigo/10" size="36">
                                        <component
                                            :is="PhStar"
                                            size="20"
                                            class="text-indigo-accent-1"
                                        />
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="text-body-2 font-weight-bold text-white"
                                >
                                    {{ skill.name }}
                                </v-list-item-title>
                                <v-list-item-subtitle
                                    class="text-caption d-flex align-center mt-1"
                                >
                                    <span class="mr-1 text-slate-400"
                                        >{{
                                            t(
                                                'roles_module.skills_section.level',
                                            )
                                        }}:</span
                                    >
                                    <SkillLevelChip
                                        :level="skill.required_level"
                                        :skill-levels="skillLevels"
                                        color="white"
                                        size="x-small"
                                    />
                                    <v-chip
                                        v-if="skill.is_critical"
                                        size="x-small"
                                        color="error"
                                        variant="flat"
                                        class="ml-2 rounded-lg"
                                    >
                                        {{
                                            t(
                                                'roles_module.skills_section.critical',
                                            )
                                        }}
                                    </v-chip>
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-window-item>

                <!-- People Tab -->
                <v-window-item value="people">
                    <v-card flat border class="pa-4 glass-card rounded-xl">
                        <div
                            class="text-subtitle-1 font-weight-bold font-premium d-flex align-center mb-4 text-white"
                        >
                            <component
                                :is="PhUsers"
                                :size="20"
                                class="text-indigo-accent-1 mr-2"
                            />
                            {{ t('roles_module.people_section.title') }}
                        </div>
                        <div
                            v-if="getRolePeople(item).length === 0"
                            class="rounded-xl border border-dashed py-8 text-center text-slate-400"
                        >
                            {{ t('roles_module.people_section.empty') }}
                        </div>
                        <v-list
                            v-else
                            density="comfortable"
                            class="pa-0 bg-transparent"
                        >
                            <v-list-item
                                v-for="person in getRolePeople(item)"
                                :key="person.id"
                                class="glass-card mb-3 rounded-xl border"
                            >
                                <template #prepend>
                                    <v-avatar color="slate/10" size="36">
                                        <component
                                            :is="PhUser"
                                            size="20"
                                            class="text-slate-300"
                                        />
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="text-body-2 font-weight-bold text-white"
                                >
                                    {{ getPersonName(person) }}
                                </v-list-item-title>
                                <v-list-item-subtitle
                                    class="text-caption mt-1 text-slate-400"
                                >
                                    {{
                                        person.email
                                            ? `(${person.email})`
                                            : t(
                                                  'roles_module.people_section.no_email',
                                              )
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
                    <v-card flat border class="pa-4 glass-card rounded-xl">
                        <div
                            class="d-flex justify-space-between align-center mb-6"
                        >
                            <div
                                class="text-subtitle-1 font-weight-bold font-premium text-white"
                            >
                                {{ t('roles_module.ai_section.title') }}
                            </div>
                            <StButtonGlass
                                v-if="item.ai_archetype_config"
                                variant="ghost"
                                size="sm"
                                :icon="PhArrowsClockwise"
                                :loading="designing"
                                @click="designRole(item.id, refresh)"
                            >
                                {{ t('roles_module.ai_section.reanalyze') }}
                            </StButtonGlass>
                        </div>

                        <div
                            v-if="!item.ai_archetype_config"
                            class="rounded-xl border border-dashed border-white/10 bg-white/5 py-12 text-center"
                        >
                            <component
                                :is="PhCube"
                                :size="64"
                                class="text-indigo-accent-2 mb-4 opacity-50"
                            />
                            <div
                                class="text-h6 font-weight-bold font-premium mb-2 text-white"
                            >
                                {{
                                    t(
                                        'roles_module.ai_section.not_analyzed_title',
                                    )
                                }}
                            </div>
                            <p
                                class="text-body-2 mx-auto mb-8 max-w-md text-slate-400"
                            >
                                {{
                                    t(
                                        'roles_module.ai_section.not_analyzed_desc',
                                    )
                                }}
                            </p>
                            <StButtonGlass
                                variant="primary"
                                size="lg"
                                :icon="PhMagicWand"
                                :loading="designing"
                                @click="designRole(item.id, refresh)"
                            >
                                {{ t('roles_module.ai_section.analyze_btn') }}
                            </StButtonGlass>
                        </div>

                        <div v-else>
                            <v-row>
                                <!-- Cube Visualization -->
                                <v-col cols="12" md="4">
                                    <v-card
                                        variant="outlined"
                                        class="pa-5 rounded-xl border-indigo-500/30 bg-indigo-900/20"
                                    >
                                        <div
                                            class="text-overline text-indigo-accent-1 font-weight-black mb-4 tracking-widest"
                                        >
                                            {{
                                                t(
                                                    'roles_module.ai_section.coordinates',
                                                )
                                            }}
                                        </div>

                                        <div class="mb-5">
                                            <div
                                                class="text-caption text-uppercase font-weight-bold mb-2 text-slate-400"
                                            >
                                                {{
                                                    t(
                                                        'roles_module.ai_section.axis_x',
                                                    )
                                                }}
                                            </div>
                                            <div class="d-flex align-center">
                                                <v-chip
                                                    color="indigo-accent-1"
                                                    size="small"
                                                    variant="flat"
                                                    class="font-weight-black rounded-lg"
                                                    >{{
                                                        item.ai_archetype_config
                                                            .cube_coordinates
                                                            .x_archetype
                                                    }}</v-chip
                                                >
                                            </div>
                                        </div>

                                        <div class="mb-5">
                                            <div
                                                class="text-caption text-uppercase font-weight-bold mb-2 text-slate-400"
                                            >
                                                {{
                                                    t(
                                                        'roles_module.ai_section.axis_y',
                                                    )
                                                }}
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
                                                    color="amber-accent-1"
                                                    active-color="amber-accent-1"
                                                ></v-rating>
                                                <span
                                                    class="text-h6 font-weight-black font-premium ml-3 text-white"
                                                    >{{
                                                        item.ai_archetype_config
                                                            .cube_coordinates
                                                            .y_mastery_level
                                                    }}{{
                                                        t(
                                                            'roles_module.ai_section.mastery_suffix',
                                                        )
                                                    }}</span
                                                >
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <div
                                                class="text-caption text-uppercase font-weight-bold mb-2 text-slate-400"
                                            >
                                                {{
                                                    t(
                                                        'roles_module.ai_section.axis_z',
                                                    )
                                                }}
                                            </div>
                                            <div
                                                class="text-body-1 font-weight-black text-indigo-accent-1 font-premium"
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
                                        class="pa-5 mt-5 rounded-xl border border-white/5 bg-white/5"
                                    >
                                        <div
                                            class="text-caption font-weight-bold mb-2 tracking-tighter text-slate-400 uppercase"
                                        >
                                            {{
                                                t(
                                                    'roles_module.ai_section.justification',
                                                )
                                            }}
                                        </div>
                                        <div
                                            class="text-body-2 leading-relaxed text-slate-300 italic"
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
                                    <div class="mb-8">
                                        <div
                                            class="text-subtitle-1 font-weight-bold font-premium d-flex align-center mb-4 text-white"
                                        >
                                            <component
                                                :is="PhSealCheck"
                                                :size="24"
                                                class="text-emerald-accent-2 mr-2"
                                            />
                                            {{
                                                t(
                                                    'roles_module.ai_section.core_competencies',
                                                )
                                            }}
                                        </div>
                                        <v-list
                                            class="pa-0 bg-transparent"
                                            density="compact"
                                        >
                                            <v-list-item
                                                v-for="(comp, i) in item
                                                    .ai_archetype_config
                                                    .core_competencies"
                                                :key="i"
                                                class="glass-card pa-4 mb-3 rounded-xl border border-white/5"
                                            >
                                                <v-list-item-title
                                                    class="font-weight-black text-body-1 d-flex align-center font-premium text-white"
                                                >
                                                    {{ comp.name }}
                                                    <v-chip
                                                        size="x-small"
                                                        class="font-weight-bold ml-3 rounded-lg"
                                                        color="emerald-accent-2"
                                                        variant="flat"
                                                        >LVL
                                                        {{ comp.level }}</v-chip
                                                    >
                                                </v-list-item-title>
                                                <v-list-item-subtitle
                                                    class="text-body-2 line-height-relaxed mt-2 text-slate-400"
                                                    >{{
                                                        comp.rationale
                                                    }}</v-list-item-subtitle
                                                >
                                            </v-list-item>
                                        </v-list>
                                    </div>

                                    <div>
                                        <div
                                            class="text-subtitle-1 font-weight-bold font-premium d-flex align-center mb-4 text-white"
                                        >
                                            <component
                                                :is="PhLightbulb"
                                                :size="24"
                                                class="text-amber-accent-1 mr-2"
                                            />
                                            {{
                                                t(
                                                    'roles_module.ai_section.org_clarity',
                                                )
                                            }}
                                        </div>
                                        <v-alert
                                            color="amber-accent-1"
                                            variant="tonal"
                                            class="rounded-xl border border-amber-500/20"
                                        >
                                            <div
                                                class="text-body-1 text-amber-accent-1 leading-relaxed"
                                            >
                                                {{
                                                    item.ai_archetype_config
                                                        .organizational_suggestions
                                                }}
                                            </div>
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

    <RoleCubeWizard v-model="showCubeWizard" @created="onRoleCreated" />
</template>

<style scoped>
.italic {
    font-style: italic;
}
</style>
