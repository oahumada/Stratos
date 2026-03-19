<script setup lang="ts">
import RoleCubeWizard from '@/components/Roles/RoleCubeWizard.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import StDigitalSealAudit from '@/components/StDigitalSealAudit.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Config,
    FilterConfig,
    ItemForm,
    TableConfig,
} from '@/types/form-schema';
import {
    PhCube,
    PhFileText,
    PhInfo,
    PhLightbulb,
    PhMagicWand,
    PhPencil,
   // PhPlus,
    PhRobot,
    PhSealCheck,
    PhStar,
   // PhTrash,
    PhUpload,
    PhUser,
    PhUsers,
    // PhX,
} from '@phosphor-icons/vue';
import { useNotification } from '@kyvg/vue3-notification';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import FormSchema from '../form-template/FormSchema.vue';

const { t } = useI18n();
const { notify } = useNotification();

// Import JSON configs
import configJson from './roles-form/config.json';
import filtersJson from './roles-form/filters.json';
import itemFormJson from './roles-form/itemForm.json';
import tableConfigJson from './roles-form/tableConfig.json';

defineOptions({ layout: AppLayout });

// Wizard state
const showCubeWizard = ref(false);
const selectedRoleId = ref<number | null>(null);
const materializing = ref<number | null>(null);
const requestingApproval = ref(false);
const showApprovalDialog = ref(false);
const roleToApprove = ref<any>(null);
const selectedApproverId = ref<number | null>(null);
const approvers = ref<any[]>([]);

const materializeArchitecture = async (roleId: number) => {
    materializing.value = roleId;
    try {
        await axios.post(`/api/roles/${roleId}/materialize-competencies`);
        notify({ 
            type: 'success', 
            text: 'Arquitectura materializada. Las competencias ahora son visibles en el catálogo.' 
        });
    } catch (e) {
        console.error('Error materializing architecture', e);
        notify({ type: 'error', text: 'Error al materializar arquitectura' });
    } finally {
        materializing.value = null;
    }
};

const openApprovalSelector = async (role: any) => {
    roleToApprove.value = role;
    showApprovalDialog.value = true;
    
    if (approvers.value.length === 0) {
        try {
            const res = await axios.get('/api/catalogs', {
                params: { catalogs: ['people'] }
            });
            approvers.value = (res.data.people || []).map((p: any) => ({
                id: p.id,
                full_name: p.name,
                job_title: p.job_title || 'Responsable'
            }));
        } catch (err) {
            console.error('Error loading approvers:', err);
        }
    }
};

const submitApprovalRequest = async () => {
    if (!selectedApproverId.value) return;
    
    requestingApproval.value = true;
    try {
        await axios.post(`/api/roles/${roleToApprove.value.id}/request-approval`, {
            approver_id: selectedApproverId.value
        });
        
        notify({
            type: 'success',
            title: 'Solicitud Enviada',
            text: 'Se ha enviado un link de aprobación al responsable seleccionado.'
        });
        showApprovalDialog.value = false;
        selectedApproverId.value = null;
    } catch (err) {
        console.error('Error requesting approval:', err);
        notify({ type: 'error', text: 'Error al enviar solicitud de aprobación' });
    } finally {
        requestingApproval.value = false;
    }
};
const formSchemaRef = ref<any>(null);

const onRoleCreated = () => {
    formSchemaRef.value?.loadItems();
    showCubeWizard.value = false;
    selectedRoleId.value = null;
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

// No refresh needed here as Wizard handles its own lifecycle

const designRole = async (id: number, _refresh: () => Promise<void>) => {
    openWizardForRole(id);
};

const autoFillWithAI = async (id: number, currentFormData: any) => {
    if (!id) return;
    designing.value = true;
    try {
        console.log('[AutoFill] Starting AI design for role:', id);
        const res = await axios.post(`/api/strategic-planning/roles/${id}/design`);
        const aiData = res.data;
        
        console.log('[AutoFill] AI Data received:', aiData);

        if (aiData && currentFormData) {
            // Update fields directly on the reactive object
            currentFormData.purpose = aiData.purpose || currentFormData.purpose;
            currentFormData.description = aiData.description || currentFormData.description;
            currentFormData.expected_results = aiData.expected_results || currentFormData.expected_results;
            
            // Just in case, try Object.assign as well for deep safety if currentFormData is a Proxy
            Object.assign(currentFormData, {
                purpose: aiData.purpose || currentFormData.purpose,
                description: aiData.description || currentFormData.description,
                expected_results: aiData.expected_results || currentFormData.expected_results
            });

            console.log('[AutoFill] currentFormData updated:', currentFormData);
            
            notify({ 
                type: 'success', 
                text: 'Inteligencia de rol cargada: Propósito y Resultados sugeridos.' 
            });
        }
    } catch (err) {
        notify({ type: 'error', text: 'Error al generar sugerencia IA para este rol' });
        console.error('Error auto-filling role:', err);
    } finally {
        designing.value = false;
    }
};

const openWizardForRole = (id: number) => {
    selectedRoleId.value = id;
    showCubeWizard.value = true;
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

        <!-- Header Actions in Detail Modal -->
        <template #detail-header-actions="{ item, edit }">
            <StButtonGlass
                v-if="item"
                variant="ghost"
                size="sm"
                :icon="PhPencil"
                @click="edit()"
            >
                Editar
            </StButtonGlass>
        </template>

        <!-- Extra AI Actions in Editing Modal -->
        <template #form-footer-actions="{ isEditing, data }">
            <StButtonGlass
                v-if="isEditing"
                variant="primary"
                size="md"
                :icon="PhMagicWand"
                :loading="designing"
                class="mr-auto"
                @click="autoFillWithAI(data.id, data)"
            >
                Optimizar con IA
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
                             <div class="mb-4 d-flex align-center justify-space-between">
                                 <h4 class="text-h6 font-weight-bold text-white uppercase tracking-tighter">
                                     Resumen Técnico
                                 </h4>
                                 <!-- Sello Digital ISO Compliance -->
                                 <StDigitalSealAudit :item="item" type="role" />
                             </div>
                            <v-list-item class="px-0">
                                <v-list-item-title class="text-body-2 text-white">
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t('roles_module.info_section.name')
                                        }}:</strong
                                    >
                                    <span class="ml-2 text-slate-100">{{ item.name }}</span>
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.description" class="px-0">
                                <v-list-item-title class="text-body-2 text-white">
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t(
                                                'roles_module.info_section.description',
                                            )
                                        }}:</strong
                                    >
                                    <div class="pa-3 mt-1 rounded-lg border border-white/5 bg-white/3 text-slate-200 text-body-2 leading-relaxed">
                                        {{ item.description }}
                                    </div>
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.purpose" class="px-0">
                                <v-list-item-title class="text-body-2 text-white">
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t(
                                                'roles_module.info_section.purpose',
                                            )
                                        }}:</strong
                                    >
                                    <div
                                        class="pa-3 mt-1 rounded-lg border border-indigo-500/20 bg-indigo-900/10 text-slate-200 italic text-body-2 leading-relaxed"
                                    >
                                        {{ item.purpose }}
                                    </div>
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                v-if="item.expected_results"
                                class="px-0"
                            >
                                <v-list-item-title class="text-body-2 text-white">
                                    <strong class="text-indigo-accent-1"
                                        >{{
                                            t(
                                                'roles_module.info_section.expected_results',
                                            )
                                        }}:</strong
                                    >
                                    <div
                                        class="pa-3 mt-1 rounded-lg border border-emerald-500/20 bg-emerald-900/10 text-slate-200 text-body-2 leading-relaxed"
                                    >
                                        {{ item.expected_results }}
                                    </div>
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="item.agent" class="px-0">
                                <v-list-item-title
                                    class="text-body-2 d-flex align-center text-white"
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
                                    class="text-body-2 d-flex align-center text-white"
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

                        <div class="d-flex gap-2">
                            <StButtonGlass
                                :icon="PhMagicWand"
                                variant="primary"
                                size="sm"
                                :loading="designing"
                                @click="designRole(item.id, refresh)"
                            >
                                {{ t('roles_module.info_section.design_btn') }}
                            </StButtonGlass>

                            <StButtonGlass
                                :icon="PhCube"
                                variant="primary"
                                size="sm"
                                @click="openWizardForRole(item.id)"
                            >
                                Refinar con Wizard
                            </StButtonGlass>
                        </div>
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
                                variant="primary"
                                size="sm"
                                :icon="PhCube"
                                @click="openWizardForRole(item.id)"
                            >
                                Re-arquitectar con Wizard
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
                                 {{ t('roles_module.ai_section.not_analyzed_title') }}
                             </div>
                             <p
                                 class="text-body-2 mx-auto mb-8 max-w-md text-slate-400"
                             >
                                 {{ t('roles_module.ai_section.not_analyzed_desc') }}
                             </p>
                             <StButtonGlass
                                 variant="primary"
                                 size="lg"
                                 :icon="PhCube"
                                 @click="openWizardForRole(item.id)"
                             >
                                 Iniciar Diseño con Cube Wizard
                             </StButtonGlass>
                         </div>

                        <div v-else class="ai-report-container">
                            <!-- Premium Summary Header -->
                            <StCardGlass variant="glow" class="mb-6 pa-6 d-flex align-center flex-wrap gap-6">
                                <div class="grow">
                                    <div class="text-overline text-indigo-accent-1 font-weight-black mb-1 tracking-widest uppercase">
                                        {{ t('roles_module.ai_section.axis_x') }}
                                    </div>
                                    <div class="text-h4 font-weight-bold font-premium text-white d-flex align-center">
                                        {{ item.ai_archetype_config.cube_coordinates.x_archetype }}
                                        <StBadgeGlass variant="primary" size="md" class="ml-4">
                                            {{ item.ai_archetype_config.cube_coordinates.z_business_process }}
                                        </StBadgeGlass>
                                    </div>
                                </div>
                                <v-divider vertical class="mx-6 hidden-sm-and-down" />
                                <div class="text-center">
                                    <div class="text-overline text-amber-accent-1 font-weight-black mb-1 tracking-widest uppercase text-center">
                                        {{ t('roles_module.ai_section.axis_y') }}
                                    </div>
                                    <div class="d-flex align-center justify-center">
                                        <v-rating
                                            :model-value="item.ai_archetype_config.cube_coordinates.y_mastery_level"
                                            length="5"
                                            readonly
                                            density="compact"
                                            color="amber-accent-1"
                                            active-color="amber-accent-1"
                                        />
                                        <span class="text-h5 font-weight-black font-premium ml-3 text-white">
                                            {{ item.ai_archetype_config.cube_coordinates.y_mastery_level }}
                                        </span>
                                    </div>
                                </div>
                            </StCardGlass>

                            <v-row>
                                <!-- Left Column: Context & Justification -->
                                <v-col cols="12" md="4">
                                    <StCardGlass class="pa-5 fill-height">
                                        <div class="d-flex align-center mb-4">
                                            <PhInfo :size="20" class="text-indigo-accent-1 mr-2" />
                                            <span class="text-subtitle-2 font-weight-bold text-white uppercase tracking-tighter">
                                                {{ t('roles_module.ai_section.justification') }}
                                            </span>
                                        </div>
                                        <div class="text-body-2 leading-relaxed text-slate-300 italic-quote">
                                            "{{ item.ai_archetype_config.cube_coordinates.justification }}"
                                        </div>
                                    </StCardGlass>
                                </v-col>

                                <!-- Right Column: Competencies -->
                                <v-col cols="12" md="8">
                                    <StCardGlass class="pa-6">
                                        <div class="text-subtitle-1 font-weight-bold font-premium d-flex align-center mb-6 text-white">
                                            <PhSealCheck :size="24" class="text-emerald-accent-2 mr-2" />
                                            {{ t('roles_module.ai_section.core_competencies') }}
                                        </div>
                                        
                                        <div class="competency-grid">
                                            <div 
                                                v-for="(comp, i) in item.ai_archetype_config.core_competencies" 
                                                :key="i"
                                                class="competency-item glass-item pa-4 rounded-xl"
                                            >
                                                <div class="d-flex align-center justify-space-between mb-2">
                                                    <span class="text-body-1 font-weight-bold text-white font-premium">
                                                        {{ comp.name }}
                                                    </span>
                                                    <StBadgeGlass variant="success" size="md">
                                                        LVL {{ comp.level }}
                                                    </StBadgeGlass>
                                                </div>
                                                <div class="text-caption text-slate-400 line-clamp-2 hover-expand">
                                                    {{ comp.rationale }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6 d-flex justify-end gap-3 align-center">
                                            <div class="materialized-indicator">
                                                <PhSealCheck :size="12" class="mr-1" /> Arquitectura Diseñada
                                            </div>
                                            <StButtonGlass
                                                variant="secondary"
                                                size="sm"
                                                :icon="PhUpload"
                                                :loading="materializing === item.id"
                                                @click="materializeArchitecture(item.id)"
                                            >
                                                Materializar en Catálogo
                                            </StButtonGlass>

                                            <StButtonGlass
                                                variant="primary"
                                                size="sm"
                                                :icon="PhSealCheck"
                                                @click="openApprovalSelector(item)"
                                            >
                                                Solicitar Aprobación
                                            </StButtonGlass>
                                        </div>
                                    </StCardGlass>
                                </v-col>

                                <v-col cols="12">
                                    <StCardGlass class="pa-6 border-amber-500/20 glow-amber-sm">
                                        <div class="text-subtitle-1 font-weight-bold font-premium d-flex align-center mb-4 text-white">
                                            <PhLightbulb :size="24" class="text-amber-accent-1 mr-2" />
                                            {{ t('roles_module.ai_section.org_clarity') }}
                                        </div>
                                        <div class="text-body-1 text-slate-200 leading-relaxed pl-8 border-l-2 border-amber-500/30">
                                            {{ item.ai_archetype_config.organizational_suggestions }}
                                        </div>
                                    </StCardGlass>
                                </v-col>
                            </v-row>
                        </div>
                    </v-card>
                </v-window-item>
            </v-window>
        </template>
    </FormSchema>

    <RoleCubeWizard 
        v-model="showCubeWizard" 
        :role-id="selectedRoleId"
        @created="onRoleCreated" 
        @close="selectedRoleId = null"
    />

    <!-- Approval Dialog -->
    <v-dialog v-model="showApprovalDialog" max-width="500">
        <StCardGlass class="pa-6 border-indigo-500/30">
            <div class="d-flex align-center mb-6">
                <div class="pa-3 rounded-lg bg-indigo-500/10 mr-4">
                    <PhSealCheck :size="24" class="text-indigo-400" />
                </div>
                <div>
                    <h3 class="text-h5 font-weight-bold text-white">Solicitar Aprobación</h3>
                    <p class="text-caption text-slate-400">Seleccione al responsable de validar el rol</p>
                </div>
            </div>

            <p class="text-body-2 text-slate-300 mb-6">
                Se enviará un <span class="text-indigo-accent-1 font-weight-bold italic">Link Mágico</span> al responsable. 
                Él podrá editar los datos finales, firmar digitalmente y materializar el rol.
            </p>

            <v-select
                v-model="selectedApproverId"
                :items="approvers"
                item-title="full_name"
                item-value="id"
                label="Responsable"
                variant="outlined"
                color="indigo-accent-2"
                density="comfortable"
                class="mb-6"
                placeholder="Seleccione una persona..."
            >
                <template #item="{ props, item }">
                    <v-list-item v-bind="props" :title="item.raw.full_name" :subtitle="item.raw.job_title">
                        <template #prepend>
                            <v-avatar size="32" class="mr-2 border border-white/10">
                                <v-img v-if="item.raw.avatar_url" :src="item.raw.avatar_url" />
                                <span v-else class="text-caption">{{ item.raw.full_name?.[0] }}</span>
                            </v-avatar>
                        </template>
                    </v-list-item>
                </template>
            </v-select>

            <div class="d-flex justify-end gap-3">
                <StButtonGlass variant="ghost" @click="showApprovalDialog = false">
                    Cancelar
                </StButtonGlass>
                <StButtonGlass 
                    variant="primary" 
                    :loading="requestingApproval"
                    :disabled="!selectedApproverId"
                    @click="submitApprovalRequest"
                >
                    Enviar Solicitud
                </StButtonGlass>
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<style scoped>
.italic {
    font-style: italic;
}

.italic-quote {
    font-style: italic;
    line-height: 1.6;
    position: relative;
    padding-left: 1rem;
    border-left: 3px solid rgba(99, 102, 241, 0.3);
}

.competency-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

.glass-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    transition: all 0.3s ease;
}

.glass-item:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(99, 102, 241, 0.3);
    transform: translateY(-2px);
}

.materialized-indicator {
    text-transform: uppercase;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: 0.1em;
    color: #34d399; /* emerald-400 */
    background: rgba(52, 211, 153, 0.05);
    border: 1px solid rgba(52, 211, 153, 0.2);
    padding: 4px 12px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
}

.glow-amber-sm {
    box-shadow: 0 0 20px rgba(251, 191, 36, 0.05) !important;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.hover-expand:hover {
    -webkit-line-clamp: unset;
    line-clamp: unset;
}

/* Fix "white sub-screens" — applying deep glassmorphism to internal cards */
:deep(.v-window-item .v-card.glass-card) {
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    backdrop-filter: blur(12px) !important;
    box-shadow: none !important;
}

:deep(.v-list) {
    background: transparent !important;
}

:deep(.v-list-item) {
    background: rgba(255, 255, 255, 0.02) !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

/* Ensure headings stand out in dark mode */
.font-premium {
    letter-spacing: -0.01em;
}

.text-slate-400 {
    color: #94a3b8 !important;
}
</style>
