<script setup lang="ts">
import { Config, FilterConfig, ItemForm, TableConfig } from '@/types/form-schema';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    PhLightning,
    PhPaperPlaneTilt,
    PhPencil,
    PhSealCheck,
    PhLink,
    PhCopy,
    PhShare,
    PhCube,
} from '@phosphor-icons/vue';


import RoleCubeWizard from '@/components/Roles/RoleCubeWizard.vue';
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
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
const selectedRoleId = ref<number | null>(null);
const selectedRoleData = ref<any>(null);
// Detail tab state

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

const selectedSkill = ref<any>(null);
const showSkillDetail = ref(false);


const openWizardForRole = (id: number, data?: any) => {
    selectedRoleId.value = id;
    selectedRoleData.value = data || null;
    showCubeWizard.value = true;
};
const formSchemaRef = ref<any>(null);

const fetchData = () => {
    formSchemaRef.value?.loadItems();
};

const showApprovalDialog = ref(false);
const roleToApprove = ref<any>(null);
const selectedApproverId = ref<number | null>(null);
const approvers = ref<any[]>([]);
const magicLinkGenerated = ref<string | null>(null);
const requestingApproval = ref(false);

const openApprovalSelector = async (role: any) => {
    roleToApprove.value = role;
    magicLinkGenerated.value = null;
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
        const response = await axios.post(`/api/roles/${roleToApprove.value.id}/request-approval`, {
            approver_id: selectedApproverId.value
        });
        
        magicLinkGenerated.value = response.data.magic_link;
    } catch (err) {
        console.error('Error requesting approval:', err);
    } finally {
        requestingApproval.value = false;
    }
};

const copyMagicLink = () => {
    if (magicLinkGenerated.value) {
        navigator.clipboard.writeText(magicLinkGenerated.value);
    }
};

const getStatusVariant = (item: any) => {
    if (!item?.ai_archetype_config) return 'sky';

    const status = item.status;
    switch (status) {
        case 'active':
        case 'approved':
        case 'baseline':
            return 'success';
        case 'pending_signature':
            return 'secondary';
        case 'pending_review':
        case 'pending':
        case 'pending_approval':
            return 'warning';
        case 'proposed':
            return 'warning';
        case 'draft':
            return 'info';
        case 'in_incubation':
            return 'fuchsia';
        default:
            return 'glass';
    }
};

const getStatusLabel = (item: any) => {
    if (!item?.ai_archetype_config) {
        return 'Diseño Pendiente';
    }

    const status = item.status;
    switch (status) {
        case 'active':
        case 'approved':
        case 'baseline':
            return 'Aprobada';
        case 'pending_signature':
            return 'Pendiente de Firma';
        case 'pending_review':
        case 'pending':
        case 'pending_approval':
            return 'Por Confirmar';
        case 'proposed':
            return 'Requiere Skills';
        case 'draft':
            return 'En Proceso';
        case 'in_incubation':
            return 'En Incubación';
        default:
            return status || 'Desconocido';
    }
};

const getRoleCompetencies = (item: any) => {
    let result = [];

    // 1. Try direct competencies relation
    if (item?.competencies && item.competencies.length > 0) {
        result = item.competencies.map((comp: any) => ({
            id: comp.id,
            name: comp.name,
            category: comp.category,
            description: comp.description || 'Sin descripción disponible',
            status: comp.status || 'proposed',
            required_level: comp.pivot?.required_level || 3,
            criticity: comp.pivot?.criticity || 'medium',
            is_core: comp.pivot?.is_core || false,
            rationale: comp.pivot?.rationale,
        }));
    } 
    // 2. Fallback: Extract from skills.competencies
    else if (item?.skills && item.skills.length > 0) {
        const compMap = new Map();
        
        item.skills.forEach((skill: any) => {
            if (skill.competencies && skill.competencies.length > 0) {
                skill.competencies.forEach((comp: any) => {
                    if (!compMap.has(comp.id)) {
                        compMap.set(comp.id, {
                            id: comp.id,
                            name: comp.name,
                            category: skill.category, // Use skill's category as fallback
                            description: comp.description || skill.description,
                            status: comp.status || 'proposed',
                            required_level: skill.pivot?.required_level || 3,
                            criticity: skill.pivot?.is_critical ? 'high' : 'medium',
                            is_core: skill.pivot?.is_critical || false,
                            rationale: skill.pivot?.rationale,
                        });
                    }
                });
            }
        });
        
        result = Array.from(compMap.values());
    }
    // 3. Fallback: Check AI suggested competencies in config
    else if (item?.ai_archetype_config?.core_competencies && item.ai_archetype_config.core_competencies.length > 0) {
        result = item.ai_archetype_config.core_competencies.map((comp: any, index: number) => ({
            id: `ai-${index}`,
            name: comp.name,
            category: 'IA Suggestion',
            description: comp.rationale || 'Sugerencia generada por el Diseñador IA',
            status: 'proposed',
            required_level: comp.level || 3,
            criticity: 'medium',
            is_core: true,
            rationale: comp.rationale,
        }));
    }

    return result;
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
        :enable-row-detail="false"
        @row-click="openWizardForRole($event.id, $event)"
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

        <template #[`item.actions`]="{ item }">
            <div class="d-flex gap-2">
                <!-- Open Wizard (Unified View/Edit) -->
                <v-btn
                    icon
                    size="small"
                    color="indigo-accent-1"
                    variant="tonal"
                    class="rounded-lg mr-1"
                    @click.stop="openWizardForRole(item.id, item)"
                >
                    <component :is="PhPencil" :size="16" />
                </v-btn>

                <v-btn
                    v-if="['pending_review', 'pending_approval', 'review'].includes(item.status)"
                    icon
                    size="small"
                    color="indigo-accent-2"
                    variant="tonal"
                    class="rounded-lg mr-1"
                    @click.stop="openApprovalSelector(item)"
                    v-tooltip="t('role_wizard.send_for_approval')"
                >
                    <component :is="PhPaperPlaneTilt" :size="16" />
                </v-btn>
                <v-btn
                    v-if="item.status === 'pending_signature'"
                    icon
                    size="small"
                    color="secondary"
                    variant="tonal"
                    class="rounded-lg mr-1"
                    @click.stop="openApprovalSelector(item)"
                    v-tooltip="t('role_wizard.resend_for_signature')"
                >
                    <component :is="PhSealCheck" :size="16" />
                </v-btn>
            </div>
        </template>


        <!-- Table Columns -->
        <template #[`item.status`]="{ item }">
            <StBadgeGlass :variant="getStatusVariant(item)" size="sm">
                {{ getStatusLabel(item) }}
            </StBadgeGlass>
        </template>

        <template #[`item.competencies_count`]="{ item }">
            <div class="d-flex align-center">
                <v-chip size="small" variant="tonal" color="indigo-accent-1" class="rounded-lg">
                    <component :is="PhLightning" :size="14" class="mr-1" />
                    {{ getRoleCompetencies(item).length }}
                </v-chip>
            </div>
        </template>
    </FormSchema>

    <RoleCubeWizard
        v-if="showCubeWizard"
        v-model="showCubeWizard"
        :role-id="selectedRoleId"
        :initial-data="selectedRoleData"
        @created="fetchData"
    />

    <!-- Competency Detail Dialog -->
    <v-dialog v-model="showSkillDetail" max-width="600px">
        <StCardGlass v-if="selectedSkill" class="pa-6" variant="premium">
            <div class="d-flex align-center justify-space-between mb-6">
                <div class="d-flex align-center">
                    <v-avatar color="indigo/20" size="48" class="mr-4 shadow-indigo">
                        <PhStar :size="28" class="text-indigo-accent-1" weight="fill" />
                    </v-avatar>
                    <div>
                        <div class="text-h5 font-weight-bold text-white font-premium">
                            {{ selectedSkill.name }}
                        </div>
                        <div class="text-caption text-indigo-accent-1 uppercase tracking-widest font-weight-black">
                            {{ selectedSkill.category || 'Competencia Institucional' }}
                        </div>
                    </div>
                </div>
                <v-btn icon="mdi-close" variant="text" color="white" @click="showSkillDetail = false" />
            </div>

            <v-divider class="mb-6 opacity-10" />

            <div class="mb-6">
                <div class="text-overline text-indigo-accent-1 mb-2 font-weight-black tracking-widest">
                    DESCRIPCIÓN
                </div>
                <div class="text-body-1 text-slate-200 leading-relaxed">
                    {{ selectedSkill.description }}
                </div>
            </div>

            <div class="d-flex align-center gap-4 mb-6">
                <div class="grow">
                    <div class="text-overline text-emerald-accent-1 mb-2 font-weight-black tracking-widest">
                        NIVEL REQUERIDO
                    </div>
                    <div class="d-flex align-center">
                        <SkillLevelChip
                            :level="selectedSkill.required_level"
                            :skill-levels="skillLevels"
                            size="large"
                        />
                    </div>
                </div>
                <div v-if="selectedSkill.is_critical">
                    <div class="text-overline text-error mb-2 font-weight-black tracking-widest">
                        PRIORIDAD
                    </div>
                    <StBadgeGlass variant="error" size="md">
                        CRÍTICA
                    </StBadgeGlass>
                </div>
            </div>

            <div class="mt-8 d-flex justify-end">
                <StButtonGlass variant="primary" size="md" @click="showSkillDetail = false">
                    Entendido
                </StButtonGlass>
            </div>
        </StCardGlass>
    </v-dialog>

    <!-- Approval Dialog -->
    <v-dialog v-model="showApprovalDialog" max-width="500">
        <StCardGlass class="pa-6 border-indigo-500/30" :no-hover="true">
            <div v-if="!magicLinkGenerated">
                <div class="d-flex align-center mb-6">
                    <div class="pa-3 rounded-lg bg-indigo-500/10 mr-4">
                        <component :is="PhShare" :size="24" class="text-indigo-400" />
                    </div>
                    <div>
                        <h3 class="text-h5 font-weight-bold text-white">Solicitar Aprobación</h3>
                        <p class="text-caption text-slate-400">Seleccione al responsable de validar el rol</p>
                    </div>
                </div>

                <p class="text-body-2 text-slate-300 mb-6">
                    Se generará un <span class="text-indigo-accent-1 font-weight-bold italic">Link Mágico</span> para el responsable. 
                    Podrá editar los datos estratégicos del rol y realizar la firma digital.
                </p>

                <v-select
                    v-model="selectedApproverId"
                    :items="approvers"
                    item-title="full_name"
                    item-value="id"
                    label="Responsable de Firma"
                    variant="outlined"
                    color="indigo-accent-2"
                    density="comfortable"
                    class="mb-6 glass-field"
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

                <div class="d-flex justify-end gap-3 pt-2">
                    <StButtonGlass variant="ghost" @click="showApprovalDialog = false">
                        Cancelar
                    </StButtonGlass>
                    <StButtonGlass 
                        variant="primary" 
                        :loading="requestingApproval"
                        :disabled="!selectedApproverId"
                        @click="submitApprovalRequest"
                    >
                        Generar Solicitud
                    </StButtonGlass>
                </div>
            </div>

            <div v-else class="text-center py-2">
                <div class="icon-pulse mx-auto mb-6">
                    <div class="pa-5 rounded-circle bg-indigo-500/20">
                        <component :is="PhLink" :size="48" class="text-indigo-accent-1" />
                    </div>
                </div>
                
                <h3 class="text-h5 font-weight-black text-white mb-2 font-premium">¡Solicitud Generada!</h3>
                <p class="text-slate-400 mb-8 max-w-xs mx-auto">
                    La solicitud de firma está lista. Copia el enlace y compártelo con el responsable.
                </p>

                <div class="magic-link-box d-flex align-center gap-3 pa-3 bg-black/40 rounded-xl mb-8 border border-white/10 group">
                    <div class="grow truncate text-caption text-indigo-300 font-mono text-left pl-2">
                        {{ magicLinkGenerated }}
                    </div>
                    <v-btn 
                        v-tooltip="'Copiar enlace'"
                        icon 
                        size="small" 
                        variant="tonal" 
                        color="indigo-accent-1"
                        class="rounded-lg shadow-lg hover:scale-110 transition-transform"
                        @click="copyMagicLink"
                    >
                        <component :is="PhCopy" :size="16" />
                    </v-btn>
                </div>

                <StButtonGlass variant="primary" class="w-full shadow-lg" @click="showApprovalDialog = false">
                    Cerrar y Continuar
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
