<script setup lang="ts">
import StBadgeGlass from '@/components/StBadgeGlass.vue';
import StButtonGlass from '@/components/StButtonGlass.vue';
import StCardGlass from '@/components/StCardGlass.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import {
    PhBrain,
    PhCaretDown,
    PhCaretRight,
    PhCheckCircle,
    PhCircleDashed,
    PhClipboardText,
    PhFloppyDisk,
    PhLightning,
    PhMagicWand,
    PhPencilSimple,
    PhPlus,
    PhStar,
    PhTrash,
    PhX,
    PhArrowCounterClockwise,
    PhChatCircleText,
    PhListBullets,
} from '@phosphor-icons/vue';
import { computed, onMounted, reactive, ref } from 'vue';

defineOptions({ layout: AppLayout });

// ─── State ────────────────────────────────────────────────────────────────────
const loading = ref(false);
const saving = ref(false);
const curating = ref<number | null>(null);
const generatingQuestions = ref<number | null>(null);
const competencies = ref<any[]>([]);
const expandedCompetencies = ref<Set<number>>(new Set());
const loadingSkills = ref<Set<number>>(new Set());
const skillsCache = ref<Record<number, any[]>>({});
const searchQuery = ref('');

// Detail panel
const detailOpen = ref(false);
const detailSkill = ref<any>(null);
const detailTab = ref('info');

// Competency form dialog
const compDialogOpen = ref(false);
const compEditing = ref<any>(null);
const compForm = reactive({ name: '', description: '', status: 'active' });

// Skill form dialog
const skillDialogOpen = ref(false);
const skillEditing = ref<any>(null);
const skillParentCompetencyId = ref<number | null>(null);
const skillForm = reactive({
    name: '',
    category: 'technical',
    description: '',
    complexity_level: 'tactical',
    scope_type: 'domain',
    is_critical: false,
});

// ─── Computed ─────────────────────────────────────────────────────────────────
const filteredCompetencies = computed(() => {
    if (!searchQuery.value.trim()) return competencies.value;
    const q = searchQuery.value.toLowerCase();
    return competencies.value.filter(
        (c) =>
            c.name?.toLowerCase().includes(q) ||
            c.description?.toLowerCase().includes(q),
    );
});

// ─── API Calls ────────────────────────────────────────────────────────────────
const loadCompetencies = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/api/competencies');
        competencies.value = res.data?.data || res.data || [];
    } catch (e) {
        console.error('Error loading competencies', e);
    } finally {
        loading.value = false;
    }
};

const loadSkillsForCompetency = async (competencyId: number) => {
    if (skillsCache.value[competencyId]) return;
    loadingSkills.value.add(competencyId);
    try {
        const res = await axios.get(`/api/competencies/${competencyId}/skills`);
        skillsCache.value[competencyId] = res.data?.data || res.data || [];
    } catch (e) {
        console.error('Error loading skills for competency', competencyId, e);
        skillsCache.value[competencyId] = [];
    } finally {
        loadingSkills.value.delete(competencyId);
    }
};

const toggleCompetency = async (id: number) => {
    if (expandedCompetencies.value.has(id)) {
        expandedCompetencies.value.delete(id);
    } else {
        expandedCompetencies.value.add(id);
        await loadSkillsForCompetency(id);
    }
};

// ─── Competency CRUD ──────────────────────────────────────────────────────────
const openCreateComp = () => {
    compEditing.value = null;
    compForm.name = '';
    compForm.description = '';
    compForm.status = 'active';
    compDialogOpen.value = true;
};

const openEditComp = (comp: any) => {
    compEditing.value = comp;
    compForm.name = comp.name;
    compForm.description = comp.description || '';
    compForm.status = comp.status || 'active';
    compDialogOpen.value = true;
};

const saveCompetency = async () => {
    saving.value = true;
    try {
        if (compEditing.value) {
            await axios.patch(`/api/competencies/${compEditing.value.id}`, compForm);
        } else {
            await axios.post('/api/competencies', compForm);
        }
        compDialogOpen.value = false;
        skillsCache.value = {}; // clear cache
        await loadCompetencies();
    } catch (e) {
        console.error('Error saving competency', e);
    } finally {
        saving.value = false;
    }
};

const deleteCompetency = async (id: number) => {
    if (!confirm('¿Eliminar esta competencia?')) return;
    try {
        await axios.delete(`/api/competencies/${id}`);
        delete skillsCache.value[id];
        expandedCompetencies.value.delete(id);
        await loadCompetencies();
    } catch (e) {
        console.error('Error deleting competency', e);
    }
};

// ─── Skill CRUD ───────────────────────────────────────────────────────────────
const openCreateSkill = (competencyId: number) => {
    skillEditing.value = null;
    skillParentCompetencyId.value = competencyId;
    skillForm.name = '';
    skillForm.category = 'technical';
    skillForm.description = '';
    skillForm.complexity_level = 'tactical';
    skillForm.scope_type = 'domain';
    skillForm.is_critical = false;
    skillDialogOpen.value = true;
};

const openEditSkill = (skill: any, competencyId: number) => {
    skillEditing.value = skill;
    skillParentCompetencyId.value = competencyId;
    skillForm.name = skill.name;
    skillForm.category = skill.category || 'technical';
    skillForm.description = skill.description || '';
    skillForm.complexity_level = skill.complexity_level || 'tactical';
    skillForm.scope_type = skill.scope_type || 'domain';
    skillForm.is_critical = skill.is_critical || false;
    skillDialogOpen.value = true;
};

const saveSkill = async () => {
    saving.value = true;
    try {
        if (skillEditing.value) {
            await axios.patch(`/api/skills/${skillEditing.value.id}`, skillForm);
        } else {
            // Create skill then attach to competency
            const res = await axios.post('/api/skills', skillForm);
            const newSkill = res.data?.data || res.data;
            if (skillParentCompetencyId.value && newSkill?.id) {
                await axios.post(`/api/competencies/${skillParentCompetencyId.value}/skills`, {
                    skill_id: newSkill.id,
                });
            }
        }
        skillDialogOpen.value = false;
        // Refresh only the affected competency's skills
        if (skillParentCompetencyId.value) {
            delete skillsCache.value[skillParentCompetencyId.value];
            await loadSkillsForCompetency(skillParentCompetencyId.value);
        }
    } catch (e) {
        console.error('Error saving skill', e);
    } finally {
        saving.value = false;
    }
};

const removeSkillFromCompetency = async (competencyId: number, skillId: number) => {
    if (!confirm('¿Remover esta skill de la competencia?')) return;
    try {
        await axios.delete(`/api/competencies/${competencyId}/skills/${skillId}`);
        delete skillsCache.value[competencyId];
        await loadSkillsForCompetency(competencyId);
    } catch (e) {
        console.error('Error removing skill', e);
    }
};

// ─── AI Actions ───────────────────────────────────────────────────────────────
const curateSkill = async (skillId: number, competencyId: number) => {
    curating.value = skillId;
    try {
        await axios.post(`/api/strategic-planning/assessments/curator/skills/${skillId}/curate`);
        delete skillsCache.value[competencyId];
        await loadSkillsForCompetency(competencyId);
        // Refresh detail if open
        if (detailSkill.value?.id === skillId) {
            const res = await axios.get(`/api/skills/${skillId}`);
            detailSkill.value = res.data?.data || res.data;
        }
    } catch (e) {
        console.error('Error curating skill', e);
    } finally {
        curating.value = null;
    }
};

const generateQuestions = async (skillId: number, _competencyId: number) => {
    generatingQuestions.value = skillId;
    try {
        await axios.post(`/api/strategic-planning/assessments/curator/skills/${skillId}/generate-questions`);
        if (detailSkill.value?.id === skillId) {
            const res = await axios.get(`/api/skills/${skillId}`);
            detailSkill.value = res.data?.data || res.data;
        }
    } catch (e) {
        console.error('Error generating questions', e);
    } finally {
        generatingQuestions.value = null;
    }
};

// ─── Detail Panel ─────────────────────────────────────────────────────────────
const openSkillDetail = async (skill: any) => {
    detailTab.value = 'info';
    try {
        const res = await axios.get(`/api/skills/${skill.id}`);
        detailSkill.value = res.data?.data || res.data || skill;
    } catch {
        detailSkill.value = skill;
    }
    detailOpen.value = true;
};

// ─── Helpers ──────────────────────────────────────────────────────────────────
const categoryConfig: Record<string, { label: string; variant: 'primary' | 'success' | 'warning' | 'error' | 'glass' }> = {
    technical: { label: 'Técnica', variant: 'primary' },
    soft: { label: 'Blanda', variant: 'success' },
    business: { label: 'Negocio', variant: 'warning' },
    language: { label: 'Idioma', variant: 'glass' },
};

const statusConfig: Record<string, { label: string; color: string }> = {
    active: { label: 'Activa', color: 'emerald' },
    draft: { label: 'Borrador', color: 'amber' },
    archived: { label: 'Archivada', color: 'slate' },
};

// ─── Lifecycle ────────────────────────────────────────────────────────────────
onMounted(loadCompetencies);
</script>

<template>
    <div class="competencies-page pa-6">
        <!-- ── Header ── -->
        <header class="mb-8">
            <div class="d-flex align-center justify-space-between flex-wrap gap-4">
                <div>
                    <div class="d-flex align-center gap-3 mb-1">
                        <component :is="PhBrain" :size="28" class="text-indigo-accent-1" />
                        <h1 class="text-h4 font-weight-black text-white">
                            Competencias & Habilidades
                        </h1>
                    </div>
                    <p class="text-slate-400 text-body-2 ml-10">
                        Catálogo organizacional de capacidades y sus habilidades asociadas
                    </p>
                </div>
                <StButtonGlass
                    variant="primary"
                    :icon="PhPlus"
                    size="md"
                    @click="openCreateComp"
                >
                    Nueva Competencia
                </StButtonGlass>
            </div>
        </header>

        <!-- ── Search ── -->
        <StCardGlass class="mb-6 pa-4">
            <v-text-field
                v-model="searchQuery"
                placeholder="Buscar competencias..."
                variant="outlined"
                density="comfortable"
                rounded="lg"
                hide-details
                class="glass-input"
            >
                <template #prepend-inner>
                    <component :is="PhListBullets" :size="18" class="text-slate-400 mr-1" />
                </template>
            </v-text-field>
        </StCardGlass>

        <!-- ── Loading ── -->
        <div v-if="loading" class="text-center py-16">
            <v-progress-circular indeterminate color="indigo-accent-2" size="48" />
            <div class="text-slate-400 mt-4 text-body-2">Cargando competencias...</div>
        </div>

        <!-- ── Empty ── -->
        <div v-else-if="filteredCompetencies.length === 0 && !loading" class="text-center py-16">
            <component :is="PhBrain" :size="64" class="text-white/10 mb-4" />
            <div class="text-slate-400 text-body-1">No hay competencias registradas</div>
            <StButtonGlass variant="primary" :icon="PhPlus" class="mt-4" @click="openCreateComp">
                Crear primera competencia
            </StButtonGlass>
        </div>

        <!-- ── Competency List ── -->
        <div v-else class="competency-list space-y-4">
            <div
                v-for="comp in filteredCompetencies"
                :key="comp.id"
                class="competency-block"
            >
                <!-- Competency Header Row -->
                <StCardGlass
                    :no-hover="true"
                    class="competency-header pa-0"
                    :class="{ 'is-expanded': expandedCompetencies.has(comp.id) }"
                >
                    <div
                        class="d-flex align-center px-5 py-4 cursor-pointer"
                        @click="toggleCompetency(comp.id)"
                    >
                        <!-- Expand toggle -->
                        <div class="toggle-icon mr-3 transition-transform" :class="{ 'rotated': expandedCompetencies.has(comp.id) }">
                            <component
                                :is="expandedCompetencies.has(comp.id) ? PhCaretDown : PhCaretRight"
                                :size="18"
                                class="text-indigo-accent-1"
                            />
                        </div>

                        <!-- Name & Description -->
                        <div class="grow">
                            <div class="text-subtitle-1 font-weight-bold text-white font-premium">
                                {{ comp.name }}
                            </div>
                            <div v-if="comp.description" class="text-caption text-slate-400 mt-0.5 line-clamp-1">
                                {{ comp.description }}
                            </div>
                        </div>

                        <!-- Badges -->
                        <div class="d-flex align-center gap-3 ml-4" @click.stop>
                            <StBadgeGlass
                                v-if="comp.skills_count !== undefined"
                                variant="glass"
                                size="md"
                            >
                                <component :is="PhStar" :size="12" class="mr-1" />
                                {{ comp.skills_count ?? (skillsCache[comp.id]?.length ?? '…') }} skills
                            </StBadgeGlass>

                            <v-chip
                                size="x-small"
                                :color="statusConfig[comp.status]?.color || 'slate'"
                                variant="tonal"
                                class="font-weight-bold rounded-lg text-uppercase tracking-wider"
                            >
                                {{ statusConfig[comp.status]?.label || comp.status }}
                            </v-chip>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex align-center gap-1 ml-4" @click.stop>
                            <v-btn
                                icon
                                variant="text"
                                size="small"
                                @click="openEditComp(comp)"
                            >
                                <component :is="PhPencilSimple" :size="16" class="text-slate-400" />
                            </v-btn>
                            <v-btn
                                icon
                                variant="text"
                                size="small"
                                @click="deleteCompetency(comp.id)"
                            >
                                <component :is="PhTrash" :size="16" class="text-rose-400/70" />
                            </v-btn>
                        </div>
                    </div>

                    <!-- ── Skills Panel (expanded) ── -->
                    <div v-if="expandedCompetencies.has(comp.id)" class="skills-panel border-t border-white/5">
                        <!-- Loading skills -->
                        <div v-if="loadingSkills.has(comp.id)" class="py-6 text-center">
                            <v-progress-circular indeterminate color="indigo-accent-1" size="28" />
                        </div>

                        <!-- Skills list -->
                        <div v-else class="px-6 py-4">
                            <!-- Add skill button -->
                            <div class="d-flex justify-space-between align-center mb-4">
                                <span class="text-overline font-weight-black tracking-widest text-white/30 uppercase">
                                    Habilidades de esta competencia
                                </span>
                                <StButtonGlass
                                    variant="ghost"
                                    :icon="PhPlus"
                                    size="sm"
                                    @click="openCreateSkill(comp.id)"
                                >
                                    Agregar Skill
                                </StButtonGlass>
                            </div>

                            <!-- Empty state -->
                            <div
                                v-if="!skillsCache[comp.id] || skillsCache[comp.id].length === 0"
                                class="py-8 text-center rounded-xl border border-dashed border-white/10"
                            >
                                <component :is="PhStar" :size="36" class="text-white/10 mb-3" />
                                <div class="text-slate-500 text-body-2">No hay habilidades en esta competencia</div>
                                <StButtonGlass
                                    variant="ghost"
                                    :icon="PhPlus"
                                    size="sm"
                                    class="mt-3"
                                    @click="openCreateSkill(comp.id)"
                                >
                                    Agregar primera skill
                                </StButtonGlass>
                            </div>

                            <!-- Skills grid -->
                            <div v-else class="skills-grid">
                                <div
                                    v-for="skill in skillsCache[comp.id]"
                                    :key="skill.id"
                                    class="skill-card rounded-xl border border-white/8 bg-white/3 pa-4 cursor-pointer transition-all hover:border-indigo-500/30 hover:bg-white/5"
                                    @click="openSkillDetail(skill)"
                                >
                                    <div class="d-flex align-start justify-space-between mb-2">
                                        <div class="text-body-2 font-weight-bold text-white grow pr-2">
                                            {{ skill.name }}
                                        </div>
                                        <div class="d-flex align-center gap-1 shrink-0" @click.stop>
                                            <v-tooltip text="Editar" location="top">
                                                <template #activator="{ props }">
                                                    <v-btn
                                                        v-bind="props"
                                                        icon
                                                        variant="text"
                                                        size="x-small"
                                                        @click="openEditSkill(skill, comp.id)"
                                                    >
                                                        <component :is="PhPencilSimple" :size="14" class="text-slate-400" />
                                                    </v-btn>
                                                </template>
                                            </v-tooltip>
                                            <v-tooltip text="Quitar de competencia" location="top">
                                                <template #activator="{ props }">
                                                    <v-btn
                                                        v-bind="props"
                                                        icon
                                                        variant="text"
                                                        size="x-small"
                                                        @click="removeSkillFromCompetency(comp.id, skill.id)"
                                                    >
                                                        <component :is="PhX" :size="14" class="text-rose-400/70" />
                                                    </v-btn>
                                                </template>
                                            </v-tooltip>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <StBadgeGlass
                                            :variant="categoryConfig[skill.category]?.variant || 'glass'"
                                            size="sm"
                                        >
                                            {{ categoryConfig[skill.category]?.label || skill.category }}
                                        </StBadgeGlass>
                                        <StBadgeGlass
                                            v-if="skill.is_critical"
                                            variant="error"
                                            size="sm"
                                        >
                                            <component :is="PhLightning" :size="10" class="mr-1" />
                                            Crítica
                                        </StBadgeGlass>
                                        <StBadgeGlass
                                            v-if="skill.bars_levels_count > 0 || (skill.bars_levels && skill.bars_levels.length > 0)"
                                            variant="success"
                                            size="sm"
                                        >
                                            <component :is="PhCheckCircle" :size="10" class="mr-1" />
                                            BARS
                                        </StBadgeGlass>
                                        <StBadgeGlass
                                            v-else
                                            variant="glass"
                                            size="sm"
                                        >
                                            <component :is="PhCircleDashed" :size="10" class="mr-1" />
                                            Sin BARS
                                        </StBadgeGlass>
                                    </div>

                                    <div v-if="skill.description" class="text-caption text-slate-400 line-clamp-2">
                                        {{ skill.description }}
                                    </div>

                                    <!-- Quick AI action -->
                                    <div class="d-flex gap-2 mt-3" @click.stop>
                                        <v-btn
                                            v-if="!skill.bars_levels_count && !(skill.bars_levels && skill.bars_levels.length > 0)"
                                            size="x-small"
                                            variant="tonal"
                                            color="indigo"
                                            :loading="curating === skill.id"
                                            @click="curateSkill(skill.id, comp.id)"
                                        >
                                            <component :is="PhMagicWand" :size="12" class="mr-1" />
                                            Curar BARS
                                        </v-btn>
                                        <v-btn
                                            v-else-if="!skill.questions_count && !(skill.questions && skill.questions.length > 0)"
                                            size="x-small"
                                            variant="tonal"
                                            color="teal"
                                            :loading="generatingQuestions === skill.id"
                                            @click="generateQuestions(skill.id, comp.id)"
                                        >
                                            <component :is="PhChatCircleText" :size="12" class="mr-1" />
                                            Generar Preguntas
                                        </v-btn>
                                        <v-btn
                                            v-else
                                            size="x-small"
                                            variant="text"
                                            color="slate"
                                            :loading="generatingQuestions === skill.id"
                                            @click="generateQuestions(skill.id, comp.id)"
                                        >
                                            <component :is="PhArrowCounterClockwise" :size="12" class="mr-1" />
                                            Regenerar
                                        </v-btn>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </StCardGlass>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         Competency Dialog
    ═══════════════════════════════════════════════════════════ -->
    <v-dialog v-model="compDialogOpen" max-width="540" persistent>
        <StCardGlass class="pa-0" :no-hover="true" style="margin: 2rem;">
            <div class="d-flex align-center justify-space-between px-6 py-5 border-b border-white/5">
                <div class="text-subtitle-1 font-weight-bold text-white font-premium">
                    {{ compEditing ? 'Editar Competencia' : 'Nueva Competencia' }}
                </div>
                <v-btn icon variant="text" @click="compDialogOpen = false">
                    <component :is="PhX" :size="20" />
                </v-btn>
            </div>
            <div class="pa-6 space-y-5">
                <div class="space-y-1">
                    <label for="comp-name" class="text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80">Nombre</label>
                    <input
                        id="comp-name"
                        v-model="compForm.name"
                        type="text"
                        placeholder="Ej: Liderazgo Estratégico"
                        class="comp-input w-full"
                    />
                </div>
                <div class="space-y-1">
                    <label for="comp-desc" class="text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80">Descripción</label>
                    <textarea
                        id="comp-desc"
                        v-model="compForm.description"
                        rows="3"
                        placeholder="Describe el propósito de esta competencia..."
                        class="comp-input w-full resize-none"
                    ></textarea>
                </div>
                <div class="space-y-1">
                    <label for="comp-status" class="text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80">Estado</label>
                    <select id="comp-status" v-model="compForm.status" class="comp-input w-full">
                        <option value="active">Activa</option>
                        <option value="draft">Borrador</option>
                        <option value="archived">Archivada</option>
                    </select>
                </div>
            </div>
            <div class="px-6 pb-6 d-flex justify-end gap-3">
                <StButtonGlass variant="ghost" @click="compDialogOpen = false">Cancelar</StButtonGlass>
                <StButtonGlass variant="primary" :icon="PhFloppyDisk" :loading="saving" @click="saveCompetency">
                    Guardar
                </StButtonGlass>
            </div>
        </StCardGlass>
    </v-dialog>

    <!-- ══════════════════════════════════════════════════════════
         Skill Dialog
    ═══════════════════════════════════════════════════════════ -->
    <v-dialog v-model="skillDialogOpen" max-width="580" persistent>
        <StCardGlass class="pa-0" :no-hover="true" style="margin: 2rem;">
            <div class="d-flex align-center justify-space-between px-6 py-5 border-b border-white/5">
                <div class="text-subtitle-1 font-weight-bold text-white font-premium">
                    {{ skillEditing ? 'Editar Habilidad' : 'Nueva Habilidad' }}
                </div>
                <v-btn icon variant="text" @click="skillDialogOpen = false">
                    <component :is="PhX" :size="20" />
                </v-btn>
            </div>
            <div class="pa-6 grid grid-cols-2 gap-4">
                <div class="col-span-2 space-y-1">
                    <label for="skill-name" class="text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80">Nombre de la Habilidad</label>
                    <input id="skill-name" v-model="skillForm.name" type="text" placeholder="Ej: Gestión del Cambio" class="comp-input w-full" />
                </div>
                <div class="space-y-1">
                    <label for="skill-category" class="text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80">Categoría</label>
                    <select id="skill-category" v-model="skillForm.category" class="comp-input w-full">
                        <option value="technical">Técnica</option>
                        <option value="soft">Blanda</option>
                        <option value="business">Negocio</option>
                        <option value="language">Idioma</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label for="skill-complexity" class="text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80">Complejidad</label>
                    <select id="skill-complexity" v-model="skillForm.complexity_level" class="comp-input w-full">
                        <option value="operative">Operativa</option>
                        <option value="tactical">Táctica</option>
                        <option value="strategic">Estratégica</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label for="skill-scope" class="text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80">Alcance</label>
                    <select id="skill-scope" v-model="skillForm.scope_type" class="comp-input w-full">
                        <option value="transversal">Transversal</option>
                        <option value="domain">Dominio</option>
                        <option value="specific">Específica</option>
                    </select>
                </div>
                <div class="space-y-1 d-flex align-center gap-3 pt-4">
                    <v-switch
                        id="skill-critical"
                        v-model="skillForm.is_critical"
                        color="rose-accent-2"
                        hide-details
                        density="compact"
                    />
                    <label for="skill-critical" class="text-[11px] font-black tracking-[0.2em] text-rose-300 uppercase opacity-80 cursor-pointer">Crítica</label>
                </div>
                <div class="col-span-2 space-y-1">
                    <label for="skill-desc" class="text-[11px] font-black tracking-[0.2em] text-indigo-300 uppercase opacity-80">Descripción</label>
                    <textarea id="skill-desc" v-model="skillForm.description" rows="3" placeholder="Describe el propósito de esta habilidad..." class="comp-input w-full resize-none"></textarea>
                </div>
            </div>
            <div class="px-6 pb-6 d-flex justify-end gap-3">
                <StButtonGlass variant="ghost" @click="skillDialogOpen = false">Cancelar</StButtonGlass>
                <StButtonGlass variant="primary" :icon="PhFloppyDisk" :loading="saving" @click="saveSkill">
                    Guardar
                </StButtonGlass>
            </div>
        </StCardGlass>
    </v-dialog>

    <!-- ══════════════════════════════════════════════════════════
         Skill Detail Dialog
    ═══════════════════════════════════════════════════════════ -->
    <v-dialog v-model="detailOpen" max-width="760" transition="dialog-transition">
        <StCardGlass class="pa-0" :no-hover="true" style="margin: 2rem; max-height: 85vh; overflow-y: auto;">
            <div v-if="detailSkill">
                <!-- Header -->
                <div class="d-flex align-start justify-space-between px-6 py-5 border-b border-white/5">
                    <div>
                        <div class="text-subtitle-1 font-weight-bold text-white font-premium mb-1">
                            {{ detailSkill.name }}
                        </div>
                        <div class="d-flex gap-2">
                            <StBadgeGlass :variant="categoryConfig[detailSkill.category]?.variant || 'glass'" size="sm">
                                {{ categoryConfig[detailSkill.category]?.label || detailSkill.category }}
                            </StBadgeGlass>
                            <StBadgeGlass v-if="detailSkill.is_critical" variant="error" size="sm">
                                <component :is="PhLightning" :size="10" class="mr-1" /> Crítica
                            </StBadgeGlass>
                        </div>
                    </div>
                    <v-btn icon variant="text" @click="detailOpen = false">
                        <component :is="PhX" :size="20" />
                    </v-btn>
                </div>

                <!-- Tabs -->
                <v-tabs v-model="detailTab" color="indigo-accent-2" class="px-4 border-b border-white/5">
                    <v-tab value="info" class="text-none">
                        <component :is="PhClipboardText" :size="16" class="mr-2" /> Información
                    </v-tab>
                    <v-tab value="bars" class="text-none">
                        <component :is="PhStar" :size="16" class="mr-2" />
                        BARS / Niveles
                        <v-chip v-if="detailSkill.bars_levels?.length" size="x-small" color="emerald" class="ml-2">
                            {{ detailSkill.bars_levels.length }}
                        </v-chip>
                    </v-tab>
                    <v-tab value="questions" class="text-none">
                        <component :is="PhChatCircleText" :size="16" class="mr-2" />
                        Preguntas
                        <v-chip v-if="detailSkill.questions?.length" size="x-small" color="teal" class="ml-2">
                            {{ detailSkill.questions.length }}
                        </v-chip>
                    </v-tab>
                </v-tabs>

                <v-window v-model="detailTab" class="pa-6">
                    <!-- Info -->
                    <v-window-item value="info">
                        <div class="space-y-4">
                            <div v-if="detailSkill.description" class="pa-4 rounded-xl border border-white/8 bg-white/3 text-slate-200 text-body-2 leading-relaxed">
                                {{ detailSkill.description }}
                            </div>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="info-chip">
                                    <span class="text-slate-500 text-caption">Complejidad:</span>
                                    <span class="text-white ml-1 text-caption font-weight-bold capitalize">{{ detailSkill.complexity_level }}</span>
                                </div>
                                <div class="info-chip">
                                    <span class="text-slate-500 text-caption">Alcance:</span>
                                    <span class="text-white ml-1 text-caption font-weight-bold capitalize">{{ detailSkill.scope_type }}</span>
                                </div>
                                <div v-if="detailSkill.domain_tag" class="info-chip">
                                    <span class="text-slate-500 text-caption">Dominio:</span>
                                    <span class="text-white ml-1 text-caption font-weight-bold">{{ detailSkill.domain_tag }}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-3 pt-2">
                                <StButtonGlass
                                    v-if="!detailSkill.bars_levels?.length"
                                    variant="primary"
                                    :icon="PhMagicWand"
                                    size="sm"
                                    :loading="curating === detailSkill.id"
                                    @click="curateSkill(detailSkill.id, -1)"
                                >
                                    Curar BARS con IA
                                </StButtonGlass>
                                <StButtonGlass
                                    v-if="detailSkill.bars_levels?.length && !detailSkill.questions?.length"
                                    variant="glass"
                                    :icon="PhChatCircleText"
                                    size="sm"
                                    :loading="generatingQuestions === detailSkill.id"
                                    @click="generateQuestions(detailSkill.id, -1)"
                                >
                                    Generar Preguntas
                                </StButtonGlass>
                            </div>
                        </div>
                    </v-window-item>

                    <!-- BARS -->
                    <v-window-item value="bars">
                        <div v-if="!detailSkill.bars_levels?.length" class="py-10 text-center">
                            <component :is="PhStar" :size="48" class="text-white/10 mb-3" />
                            <div class="text-slate-400 text-body-2 mb-4">No hay niveles BARS curados aún</div>
                            <StButtonGlass
                                variant="primary"
                                :icon="PhMagicWand"
                                :loading="curating === detailSkill.id"
                                @click="curateSkill(detailSkill.id, -1)"
                            >
                                Generar con IA
                            </StButtonGlass>
                        </div>
                        <v-expansion-panels v-else variant="accordion" class="elevation-0">
                            <v-expansion-panel
                                v-for="level in detailSkill.bars_levels"
                                :key="level.id"
                                class="glass-panel mb-2 rounded-xl!"
                            >
                                <v-expansion-panel-title class="px-4 py-3">
                                    <div class="d-flex align-center gap-3">
                                        <StBadgeGlass
                                            :variant="level.level >= 4 ? 'success' : level.level >= 3 ? 'primary' : 'warning'"
                                            size="md"
                                        >
                                            Nivel {{ level.level }}
                                        </StBadgeGlass>
                                        <span class="font-weight-medium text-white text-body-2">{{ level.level_name }}</span>
                                    </div>
                                </v-expansion-panel-title>
                                <v-expansion-panel-text class="pa-4">
                                    <div class="space-y-3">
                                        <div>
                                            <div class="text-[10px] font-black tracking-widest text-white/30 uppercase mb-1">Comportamiento Esperado</div>
                                            <div class="text-body-2 text-slate-300 leading-relaxed">{{ level.behavioral_description }}</div>
                                        </div>
                                        <v-divider style="opacity: 0.05" />
                                        <div class="d-flex gap-6">
                                            <div class="flex-1" v-if="level.learning_content">
                                                <div class="text-[10px] font-black tracking-widest text-indigo-300/60 uppercase mb-1">Contenido de Aprendizaje</div>
                                                <div class="text-body-2 text-slate-300 leading-relaxed">{{ level.learning_content }}</div>
                                            </div>
                                            <div class="flex-1" v-if="level.performance_indicator">
                                                <div class="text-[10px] font-black tracking-widest text-emerald-300/60 uppercase mb-1">Indicador (KPI)</div>
                                                <div class="text-body-2 text-slate-300 leading-relaxed">{{ level.performance_indicator }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </v-expansion-panel-text>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-window-item>

                    <!-- Questions -->
                    <v-window-item value="questions">
                        <div v-if="!detailSkill.questions?.length" class="py-10 text-center">
                            <component :is="PhChatCircleText" :size="48" class="text-white/10 mb-3" />
                            <div class="text-slate-400 text-body-2 mb-4">
                                {{ !detailSkill.bars_levels?.length ? 'Necesitas curar los niveles BARS primero' : 'No hay preguntas generadas aún' }}
                            </div>
                            <StButtonGlass
                                variant="primary"
                                :icon="PhChatCircleText"
                                :loading="generatingQuestions === detailSkill.id"
                                :disabled="!detailSkill.bars_levels?.length"
                                @click="generateQuestions(detailSkill.id, -1)"
                            >
                                Generar Preguntas con IA
                            </StButtonGlass>
                        </div>
                        <div v-else class="space-y-3">
                            <div class="d-flex justify-end mb-2">
                                <StButtonGlass
                                    variant="ghost"
                                    size="sm"
                                    :icon="PhArrowCounterClockwise"
                                    :loading="generatingQuestions === detailSkill.id"
                                    @click="generateQuestions(detailSkill.id, -1)"
                                >
                                    Regenerar
                                </StButtonGlass>
                            </div>
                            <div
                                v-for="q in detailSkill.questions"
                                :key="q.id"
                                class="pa-4 rounded-xl border border-white/8 bg-white/3 d-flex align-start gap-3"
                            >
                                <StBadgeGlass
                                    :variant="q.level >= 4 ? 'success' : q.level >= 3 ? 'primary' : 'warning'"
                                    size="sm"
                                    class="shrink-0 mt-0.5"
                                >
                                    L{{ q.level }}
                                </StBadgeGlass>
                                <div>
                                    <div class="text-body-2 text-slate-200 leading-relaxed">{{ q.question }}</div>
                                    <div class="text-caption text-slate-500 mt-1 uppercase tracking-wider">{{ q.question_type }}</div>
                                </div>
                            </div>
                        </div>
                    </v-window-item>
                </v-window>
            </div>
        </StCardGlass>
    </v-dialog>
</template>

<style scoped>
.competencies-page {
    min-height: 100vh;
}

.competency-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.competency-header {
    transition: all 0.2s ease;
}

.competency-header.is-expanded {
    border-color: rgba(99, 102, 241, 0.25) !important;
    box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.15);
}

.cursor-pointer {
    cursor: pointer;
}

.toggle-icon {
    transition: transform 0.2s ease;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.04);
    flex-shrink: 0;
}

.skills-panel {
    background: rgba(0, 0, 0, 0.15);
}

.skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 0.75rem;
}

.skill-card {
    transition: all 0.25s ease;
}

.skill-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.1);
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Form inputs */
.comp-input {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    color: white;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.comp-input::placeholder {
    color: rgba(255, 255, 255, 0.25);
}

.comp-input:focus {
    outline: none;
    border-color: rgba(99, 102, 241, 0.5);
    background: rgba(255, 255, 255, 0.07);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.comp-input option {
    background: #1e1b4b;
    color: white;
}

.info-chip {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 8px;
    padding: 4px 12px;
}

/* Glass panels for expansion panels */
.glass-panel {
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

/* Glass input override */
.glass-input :deep(.v-field__outline) {
    --v-field-border-opacity: 0.1;
}

.glass-input :deep(.v-label) {
    color: rgba(255, 255, 255, 0.6) !important;
}

.glass-input :deep(input) {
    color: white !important;
}

.grid {
    display: grid;
}

.grid-cols-2 {
    grid-template-columns: repeat(2, 1fr);
}

.col-span-2 {
    grid-column: span 2;
}

.gap-4 {
    gap: 1rem;
}

.space-y-1 > * + * {
    margin-top: 0.25rem;
}

.space-y-4 > * + * {
    margin-top: 1rem;
}

.space-y-5 > * + * {
    margin-top: 1.25rem;
}

.space-y-3 > * + * {
    margin-top: 0.75rem;
}
</style>
