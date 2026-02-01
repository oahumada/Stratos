<script setup lang="ts">
/* eslint-disable @typescript-eslint/no-unused-vars */
import { useApi } from '@/composables/useApi';
import { useCompetencySkills } from '@/composables/useCompetencySkills';
import { useNotification } from '@/composables/useNotification';
import * as d3 from 'd3';
import { onMounted, ref, watch, onBeforeUnmount, computed, nextTick } from 'vue';
import { computeMatrixPositions } from '@/composables/useNodeNavigation';
import { chooseMatrixVariant, computeCompetencyMatrixPositions, computeSidesPositions, decideCompetencyLayout } from '@/composables/useCompetencyLayout';
import type { CSSProperties } from 'vue';
import type { NodeItem, Edge, ConnectionPayload } from '@/types/brain';
interface Props {
    scenario?: {
        id?: number;
        name?: string;
        description?: string;
        status?: string;
        fiscal_year?: number | string;
        organization_id?: number | string;
        capabilities?: any[];
        connections?: any[];
        created_at?: string | null;
        updated_at?: string | null;
    };
    // optional: number of columns for child competencies layout
    childColumns?: number;
    // optional: layout overrides for competencies (rows, cols, spacing)
    competencyLayout?: {
        rows?: number;
        cols?: number;
        hSpacing?: number;
        vSpacing?: number;
        // vertical offset (px) from parent capability to first row of competencies
        parentOffset?: number;
        // small drop/padding applied specifically to skill rows under competencies
        skillDrop?: number;
    };
    // optional: explicit top-level override (px) for parent->children distance
    capabilityChildrenOffset?: number;
    // optional: curvature depth (px) for scenario->capability curved edges
    scenarioEdgeCurveDepth?: number;
    // visual configuration overrides for layout and edges
    visualConfig?: {
        nodeRadius?: number;
        focusRadius?: number;
        // scenario node offsets
        scenarioOffset?: number; // used when following a node
        scenarioOffsetSwap?: number; // used when swapping focused nodes
        // small drops to push children/skills lower
        childDrop?: number;
        skillDrop?: number;
        // optional layout selection specifically for skills ('auto' | 'radial' | 'matrix' | 'sides')
        skillLayout?: 'auto' | 'radial' | 'matrix' | 'sides';
        // explicit override for capability->children offset (pixels)
        capabilityChildrenOffset?: number;
        // edge config
        edge?: {
            baseDepth?: number;
            curveFactor?: number;
            spreadOffset?: number;
        };
        // separate edge parameters for skill-level (grandchild) connectors
        edgeSkill?: {
            baseDepth?: number;
            curveFactor?: number;
            spreadOffset?: number;
        };
    };
}

function restoreView() {
    // Capture current focused node (if any) before clearing state so we can reuse it for restoration
    const parentNode = focusedNode.value as NodeItem | null;
    // Clear focused state and visibility flags; restore original positions if available
    focusedNode.value = null;
    childNodes.value = [];
    childEdges.value = [];
    selectedChild.value = null;
    // ensure any expanded skills are collapsed as well
    try { collapseGrandChildren(); } catch (err: unknown) { void err; }
    // clear render flags
    nodes.value = nodes.value.map((n: any) => ({ ...n, visible: true }));
    // If we had a parent node, decide layout based on its competency count and expand
    if (parentNode) {
        try {
            const compCount = Array.isArray(parentNode?.competencies) ? parentNode!.competencies.length : 0;
            if (compCount >= 4) {
                // use matrix layout for 4 or more nodes (matrixVariants will select 4x2 or 5x2)
                expandCompetencies(parentNode as NodeItem, { x: parentNode.x ?? 0, y: parentNode.y ?? 0 }, { layout: 'matrix' as any });
            } else {
                expandCompetencies(parentNode as NodeItem, { x: parentNode.x ?? 0, y: parentNode.y ?? 0 });
            }
        } catch (err: unknown) {
            // fallback to default behaviour
            expandCompetencies(parentNode as NodeItem, { x: parentNode.x ?? 0, y: parentNode.y ?? 0 });
        }
    }
    viewX.value = 0;
    viewY.value = 0;
}
const tickLabelImportance = {
    1: '1',
    2: '2',
    3: '3',
  }
const tickLabelStrategic ={
    1: '1',
    2: '2',
    3: '3',
    4: '4',
    5: '5',
    6: '6',
    7: '7',
    8: '8',
    9: '9',
    10: '10',
}
const tickLabelPriority = {
    1: '1',
    2: '2',
    3: '3',
    4: '4',
    5: '5',
}
const tickLabelRequiredLevel = {
    1: '1',
    2: '2',
    3: '3',
    4: '4',
    5: '5',
}
// Dev helper: calcular el nivel (profundidad) de un nodo.
// Nivel 0 = escenario, Nivel 1 = capacidad, Nivel 2 = competencia, etc.
function nodeLevel(nodeOrId: any) {
    if (!nodeOrId) return null;
    const id = typeof nodeOrId === 'number' ? nodeOrId : nodeOrId.id;
    if (id == null) return null;
    const scenarioId = scenarioNode.value ? scenarioNode.value.id : null;
    // if it's the scenario node
    if (scenarioId != null && id === scenarioId) return 0;

    let currentId: number | null = id;
    let depth = 0;
    // walk parent links via childEdges until we either hit the scenario or no parent exists
    while (currentId != null) {
        // if there's a child-edge pointing to currentId, move to its source (parent)
        const pe = childEdges.value.find((e) => e.target === currentId);
        if (pe) {
            currentId = pe.source;
            depth++;
            // protect against infinite loops
            if (depth > 10) break;
            // continue walking
            continue;
        }
        // otherwise check if scenario directly references this id (scenario -> capability)
        const se = scenarioEdges.value.find((e) => e.target === currentId);
        if (se) {
            depth++;
            break;
        }
        // if currentId corresponds to a top-level capability (in nodes) and we haven't counted it yet
        const isCap = nodes.value.find((n) => n.id === currentId);
        if (isCap) {
            // capability under scenario
            depth = Math.max(depth, 1);
        }
        break;
    }
    return depth;
}

const props = withDefaults(defineProps<Props>(), {
    competencyLayout: () => ({ parentOffset: 80 }),
    capabilityChildrenOffset: 150,
    scenarioEdgeCurveDepth: 90,
    visualConfig: () => ({
        nodeRadius: 34,
        focusRadius: 34,
        scenarioOffset: 80,
        scenarioOffsetSwap: 150,
        childDrop: 18,
        skillDrop: 18,
        edge: { baseDepth: 40, curveFactor: 0.35, spreadOffset: 18 },
        // overrides specifically for skill (grandchild) connectors
        edgeSkill: { baseDepth: 20, curveFactor: 0.25, spreadOffset: 10 },
    }),
});
    
const emit = defineEmits<{
    (e: 'createCapability'): void;
}>();

// Create-capability modal state
const createModalVisible = ref(false);
const availableCapabilities = ref<any[]>([]);
const newCapName = ref('');
const newCapDescription = ref('');
const newCapType = ref('');
const newCapCategory = ref('');
const newCapImportance = ref<number>(3);
// pivot fields
const pivotStrategicRole = ref('target');
const pivotStrategicWeight = ref<number | undefined>(10);
const pivotPriority = ref<number | undefined>(1);
const pivotRationale = ref('');
const pivotRequiredLevel = ref<number | undefined>(3);
const pivotIsCritical = ref(false);
const creating = ref(false);
const api = useApi();
// Ensure CSRF cookie for Laravel Sanctum is present before mutating requests
async function ensureCsrf() {
    try {
        const hasXsrf = document.cookie.includes('XSRF-TOKEN=');
        console.debug('[ensureCsrf] has XSRF-TOKEN cookie?', hasXsrf);
        if (!hasXsrf) {
            // call Sanctum endpoint to set cookie
            try {
                // support both shapes returned by tests/mocks: either `api.api.get` (axios instance)
                // or `api.get` (composable wrapper). Prefer `api.api.get` when available.
                const axiosGet = (api as any)?.api?.get ?? (api as any)?.get;
                if (typeof axiosGet === 'function') {
                    await axiosGet('/sanctum/csrf-cookie');
                }
                console.debug('[ensureCsrf] fetched /sanctum/csrf-cookie');
            } catch (e) {
                console.warn('[ensureCsrf] failed to fetch csrf-cookie', e);
            }
        }
    } catch (err) {
        void err;
    }
}
const loaded = ref(false);
const nodes = ref<Array<NodeItem>>([]);
const edges = ref<Array<Edge>>([]);
const dragging = ref<any>(null);
const dragOffset = ref({ x: 0, y: 0 });
const positionsDirty = ref(false);
const { showSuccess, showError } = useNotification();
const width = ref(900);
const height = ref(600);
const mapRoot = ref<HTMLElement | null>(null);
let lastItems: any[] = [];
const focusedNode = ref<NodeItem | null>(null);
const tooltipX = ref(0);
const tooltipY = ref(0);
const childNodes = ref<Array<any>>([]);
const childEdges = ref<Array<Edge>>([]);
const scenarioEdges = ref<Array<Edge>>([]);
const grandChildNodes = ref<Array<any>>([]);
const grandChildEdges = ref<Array<Edge>>([]);
const showSidebar = ref(false);
// selectedChild: when a competency (level-2) is clicked we keep it here
// while `focusedNode` remains the parent capability (so layout treats parent as focused).
const selectedChild = ref<any>(null);
const loadingSkills = ref(false);
// Breadcrumb computed: construye la ruta completa disponible
const breadcrumbTitle = computed(() => {
    const parts: string[] = [];
    try {
        const sName = props.scenario?.name || '—';
        parts.push(`Escenario: ${sName}`);

        if (focusedNode.value) {
            const fName = (focusedNode.value as any)?.name || '—';
            parts.push(`Capacidad: ${fName}`);
        }

        if (selectedChild.value) {
            const c = selectedChild.value as any;
            const cName = c?.name || c?.raw?.name || c?.compName || '—';
            parts.push(`Competencia: ${cName}`);
        }
    } catch (err: unknown) {
        // fallback to scenario only
        return `Escenario: ${props.scenario?.name || '—'}`;
    }
    return parts.join('  ›  ');
});
// Breadcrumb parts as an array for multi-line rendering
const breadcrumbParts = computed(() => {
    const parts: string[] = [];
    try {
        const sName = props.scenario?.name || '—';
        parts.push(`Escenario: ${sName}`);
        if (focusedNode.value) {
            const fName = (focusedNode.value as any)?.name || '—';
            parts.push(`Capacidad: ${fName}`);
        }
        if (selectedChild.value) {
            const c = selectedChild.value as any;
            const cName = c?.name || c?.raw?.name || c?.compName || '—';
            parts.push(`Competencia: ${cName}`);
        }
    } catch (err: unknown) {
        return [`Escenario: ${props.scenario?.name || '—'}`];
    }
    return parts;
});
// raw capability-tree fetched from API (for debugging / inspection)
const capabilityTreeRaw = ref<any | null>(null);
const showScenarioRaw = ref(false);
// editing focused node / pivot
const editCapName = ref('');
const editCapDescription = ref('');
const editCapImportance = ref<number | undefined>(undefined);
const editCapLevel = ref<number | null>(null);
const editCapType = ref('');
const editCapCategory = ref('');
const ee = ref<number | null>(null);



const editPivotStrategicRole = ref('target');
const editPivotStrategicWeight = ref<number | undefined>(10);
const editPivotPriority = ref<number | undefined>(1);
const editPivotRationale = ref('');
const editPivotRequiredLevel = ref<number | undefined>(3);
const editPivotIsCritical = ref(false);
const savingNode = ref(false);
// Competency creation/attach UI state
const createCompDialogVisible = ref(false);
const addExistingCompDialogVisible = ref(false);
const creatingComp = ref(false);
const availableExistingCompetencies = ref<any[]>([]);
const newCompName = ref('');
const newCompDescription = ref('');
const newCompReadiness = ref<number | undefined>(undefined);
const newCompSkills = ref('');
const addExistingSelection = ref<number | null>(null);

// Helper function to reset competency form fields
function resetCompetencyForm() {
    newCompName.value = '';
    newCompDescription.value = '';
    newCompReadiness.value = undefined;
    newCompSkills.value = '';
}

// Skill modal state (creación / selección)
const createSkillDialogVisible = ref(false);
const selectSkillDialogVisible = ref(false);
const availableSkills = ref<any[]>([]);
// Skill detail modal state
const skillDetailDialogVisible = ref(false);
const selectedSkillDetail = ref<any>(null);
// Editable fields for skill modal
const skillEditName = ref('');
const skillEditDescription = ref('');
const skillEditCategory = ref('technical');
const skillEditComplexityLevel = ref('tactical');
const skillEditScopeType = ref('domain');
const skillEditDomainTag = ref('');
const skillEditIsCritical = ref(false);

// Pivot editable fields (capability_competencies) when skill viewed within a competency/capability
const skillPivotRequiredLevel = ref<number | undefined>(undefined);
const skillPivotPriority = ref<number | undefined>(undefined);
const skillPivotWeight = ref<number | undefined>(undefined);
const skillPivotRationale = ref('');
const skillPivotIsRequired = ref(false);
const savingSkillDetail = ref(false);
const selectedSkillId = ref<number | null>(null);
const newSkillName = ref('');
const newSkillCategory = ref('');
const newSkillDescription = ref('');
const savingSkill = ref(false);
const attachingSkill = ref(false);

// Context menu state (right-click on nodes)
const contextMenuVisible = ref(false);
const contextMenuLeft = ref(0);
const contextMenuTop = ref(0);
const contextMenuTarget = ref<any | null>(null);
const contextMenuIsChild = ref(false);
const contextMenuEl = ref<HTMLElement | null>(null);

// Modo de renderizado de aristas hijo: 0=offset pequeño,1=offset grande,2=curva,3=separación horizontal
const childEdgeMode = ref(2);
const childEdgeModeLabels = ['offset','gap-large','curve','spread'];

function nextChildEdgeMode() {
    childEdgeMode.value = (childEdgeMode.value + 1) % childEdgeModeLabels.length;
}

function openNodeContextMenu(node: any, ev: MouseEvent) {
    try {
        ev.preventDefault();
        ev.stopPropagation();
    } catch (err: unknown) { void err; }
    // compute container rect (map root) and clamp menu to stay inside
    const rect = mapRoot.value ? mapRoot.value.getBoundingClientRect() : { left: 0, top: 0, width: width.value, height: height.value } as DOMRect;
    const MENU_W = 260;
    const MENU_H = 300; // conservative height for clamping
    const relX = (ev.clientX - rect.left);
    const relY = (ev.clientY - rect.top);
    const clampedX = Math.max(8, Math.min(relX, (rect.width ?? width.value) - MENU_W - 8));
    const clampedY = Math.max(8, Math.min(relY, (rect.height ?? height.value) - 24));
    contextMenuLeft.value = Math.round(clampedX);
    contextMenuTop.value = Math.round(clampedY);
    contextMenuTarget.value = node;
    contextMenuIsChild.value = !!(node && node.id != null && node.id < 0);
    contextMenuVisible.value = true;
}

function closeContextMenu() {
    contextMenuVisible.value = false;
    contextMenuTarget.value = null;
}

function contextViewEdit() {
    const node = contextMenuTarget.value;
    if (!node) return closeContextMenu();
    // If child, set selectedChild and parent focus
    if (contextMenuIsChild.value) {
        selectedChild.value = node as any;
        const parentEdge = childEdges.value.find((e) => e.target === node.id);
        const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
        if (parentNode) {
            // prefer canonical node from nodes[] to ensure `raw` and attributes are present
            focusedNode.value = nodeById(parentNode.id) || parentNode;
        }
    } else {
        // prefer canonical node from nodes[] to avoid missing fields when opened from context menu
        focusedNode.value = nodeById((node as any).id) || (node as NodeItem);
        selectedChild.value = null;
    }
    // ensure sidebar opens with the selected/display node
    // populate edit fields for focused node and load skills for child if needed
    nextTick(async () => {
        try {
            resetFocusedEdits();
            if (contextMenuIsChild.value && selectedChild.value) {
                const compId = (selectedChild.value as any)?.compId ?? (selectedChild.value as any)?.raw?.id ?? Math.abs((selectedChild.value as any)?.id || 0);
                if (compId) {
                    const skills = await fetchSkillsForCompetency(Number(compId));
                    try { (selectedChild.value as any).skills = Array.isArray(skills) ? skills : []; } catch (err: unknown) { void err; }
                }
            }
        } catch (err: unknown) { void err; }
        showSidebar.value = true;
        closeContextMenu();
    });
}

function contextCreateChild() {
    const node = contextMenuTarget.value;
    if (!node) return closeContextMenu();
    // If clicked on a child, try to use its parent as target
    let targetParent = node;
    if (node.id != null && node.id < 0) {
        const parentEdge = childEdges.value.find((e) => e.target === node.id);
        targetParent = parentEdge ? nodeById(parentEdge.source) : node;
    }
    if (targetParent) {
        focusedNode.value = targetParent as NodeItem;
    }
    // clear any previously selected child to ensure displayNode uses focusedNode (the target parent)
    selectedChild.value = null;
    // reset form fields before opening dialog
    resetCompetencyForm();
    // open create-competency dialog
    createCompDialogVisible.value = true;
    closeContextMenu();
}

function contextCreateSkill() {
    const node = contextMenuTarget.value;
    if (!node) return closeContextMenu();
    // if clicked on a child competency, set it as selected and open skill creation
    if (node.id != null && node.id < 0) {
        selectedChild.value = node as any;
    } else {
        // if user somehow invoked on a capability, try to focus the first child or the focused node
        selectedChild.value = null;
    }
    createSkillDialogVisible.value = true;
    closeContextMenu();
}

async function contextAttachExistingSkill() {
    const node = contextMenuTarget.value;
    if (!node) return closeContextMenu();
    // If a child competency was clicked, use it
    if (node.id != null && node.id < 0) {
        selectedChild.value = node as any;
    } else {
        // try to pick a child competency of the focused node if available
        const child = childNodes.value.find((c: any) => c.__parentId === node.id || c.parentId === node.id);
        if (child) selectedChild.value = child;
    }
    try {
        await loadAvailableSkills();
    } catch (err: unknown) { /* ignore */ }
    selectSkillDialogVisible.value = true;
    closeContextMenu();
}

async function contextDeleteNode() {
    const node = contextMenuTarget.value;
    if (!node) return closeContextMenu();
    // focus the node and call existing deletion flow
    if (node.id != null && node.id < 0) {
        // if child, set selectedChild
        selectedChild.value = node as any;
        const parentEdge = childEdges.value.find((e) => e.target === node.id);
        if (parentEdge) focusedNode.value = nodeById(parentEdge.source);
    } else {
        focusedNode.value = node as NodeItem;
        selectedChild.value = null;
    }
    try {
        await deleteFocusedNode();
    } catch (err: unknown) { void err; }
    closeContextMenu();
}

onMounted(async () => {
    const handler = (ev: MouseEvent) => {
        if (!contextMenuVisible.value) return;
        try {
            const el = contextMenuEl.value as HTMLElement | null;
            if (el && ev.target && (ev.target instanceof Node) && el.contains(ev.target as Node)) {
                // click happened inside the context menu — ignore
                return;
            }
        } catch (err: unknown) { void err; }
        if (contextMenuVisible.value) closeContextMenu();
    };
    document.addEventListener('pointerdown', handler);
    onBeforeUnmount(() => document.removeEventListener('pointerdown', handler));
});

async function loadAvailableSkills() {
    try {
        const res: any = await api.get('/api/skills');
        availableSkills.value = Array.isArray(res) ? res : (res?.data ?? []);
    } catch (err: unknown) {
        availableSkills.value = [];
    }
}

const { createAndAttachSkill: createAndAttachSkillForComp } = useCompetencySkills();

async function createAndAttachSkill() {
    // If we're creating a competency (createComp dialog open) and user opened the create-skill
    // modal from there, don't call the API yet — store the skill name to `newCompSkills`
    if (createCompDialogVisible.value) {
        if (!newSkillName.value || !newSkillName.value.trim()) return showError('El nombre es obligatorio');
        // append to newCompSkills as comma-separated list
        const names = String(newCompSkills.value || '').split(',').map(s => s.trim()).filter(Boolean);
        names.push(newSkillName.value.trim());
        newCompSkills.value = names.join(', ');
        createSkillDialogVisible.value = false;
        newSkillName.value = '';
        newSkillCategory.value = '';
        newSkillDescription.value = '';
        showSuccess('Skill añadida (se guardará junto con la competencia)');
        return;
    }

    if (!selectedChild.value) return showError('Seleccione una competencia');
    if (!newSkillName.value || !newSkillName.value.trim()) return showError('El nombre es obligatorio');
    savingSkill.value = true;
    try {
        const payload: any = { name: newSkillName.value.trim() };
        payload.category = (newSkillCategory.value && String(newSkillCategory.value).trim() !== '') ? newSkillCategory.value : 'technical';
        if (newSkillDescription.value && String(newSkillDescription.value).trim() !== '') {
            payload.description = newSkillDescription.value;
        }
        const compId = (selectedChild.value as any).compId ?? (selectedChild.value as any).raw?.id ?? Math.abs((selectedChild.value as any).id || 0);
        if (!compId) throw new Error('No competency target available');

        const created = await createAndAttachSkillForComp(compId, payload);
        if (created) {
            if (!Array.isArray((selectedChild.value as any).skills)) (selectedChild.value as any).skills = [];
            (selectedChild.value as any).skills.push(created);
        }
        createSkillDialogVisible.value = false;
        newSkillName.value = '';
        newSkillCategory.value = '';
        newSkillDescription.value = '';
        showSuccess('Skill creada y asociada');
    } catch (err: unknown) {
        console.error('createAndAttachSkill error', err);
        showError('Error creando y asociando skill');
    } finally {
        savingSkill.value = false;
    }
}

async function attachExistingSkill() {
    if (!selectedChild.value) return showError('Seleccione una competencia');
    if (!selectedSkillId.value) return showError('Seleccione una skill');
    attachingSkill.value = true;
    try {
        // try backend attach endpoint, fall back to local optimistic attach
        const compId = (selectedChild.value as any).compId ?? (selectedChild.value as any).raw?.id ?? Math.abs((selectedChild.value as any).id || 0);
        try {
            await api.post(`/api/competencies/${compId}/skills`, { skill_id: selectedSkillId.value });
        } catch (err: unknown) {
            const found = availableSkills.value.find((s: any) => s.id === selectedSkillId.value);
            if (found) {
                if (!Array.isArray((selectedChild.value as any).skills)) (selectedChild.value as any).skills = [];
                (selectedChild.value as any).skills.push(found);
            }
        }
        selectSkillDialogVisible.value = false;
        selectedSkillId.value = null;
        showSuccess('Skill asociada');
    } catch (err: unknown) {
        showError('Error asociando skill');
    } finally {
        attachingSkill.value = false;
    }
}

// Remove a skill from the currently selected competency (best-effort)
async function removeSkillFromCompetency(skill: any) {
    if (!selectedChild.value) return showError('Seleccione una competencia');
    const compId = (selectedChild.value as any).compId ?? (selectedChild.value as any).raw?.id ?? Math.abs((selectedChild.value as any).id || 0);
    const skillId = skill?.id ?? skill?.raw?.id ?? null;
    // try to find pivot id on the skill object
    const pivotId = skill?.pivot?.id ?? skill?.raw?.pivot?.id ?? skill?.raw?.pivot_id ?? null;
    try {
        if (pivotId) {
            await api.delete(`/api/competency-skills/${pivotId}`);
        } else if (compId && skillId) {
            // try delete via competency-scoped endpoint if backend exposes it
            try {
                await api.delete(`/api/competencies/${compId}/skills/${skillId}`);
            } catch (e: unknown) {
                // ignore and fallback to local remove
            }
        }
        // remove locally if present
        if (Array.isArray((selectedChild.value as any).skills)) {
            (selectedChild.value as any).skills = (selectedChild.value as any).skills.filter((s: any) => (s.id ?? s.raw?.id ?? s) !== (skillId ?? skill));
        }
        // also remove from grandChildNodes if present
        grandChildNodes.value = grandChildNodes.value.filter((g) => (g.id ?? g.raw?.id ?? g) !== (skillId ?? skill));
        showSuccess('Skill eliminada de la competencia');
    } catch (err: unknown) {
        console.error('removeSkillFromCompetency error', err);
        showError('Error eliminando skill');
    }
}

// selectedChild edit fields (competency + pivot)
const editChildName = ref('');
const editChildDescription = ref('');
const editChildReadiness = ref<number | null>(null);
const editChildSkills = ref('');
const editChildPivotStrategicWeight = ref<number | undefined>(10);
const editChildPivotPriority = ref<number | undefined>(1);
const editChildPivotRequiredLevel = ref<number | undefined>(3);
const editChildPivotIsCritical = ref(false);
const editChildPivotRationale = ref('');
// Temporarily disable CSS animations for level-2 clicks
const noAnimations = ref(false);
// slider / scroll sync for edit form
const editFormScrollEl = ref<HTMLElement | null>(null);
const editFormScrollPercent = ref<number>(0); // 0..100
let editFormScrollHandler: ((ev: Event) => void) | null = null;

function onEditSliderInput(val: number) {
    const el = editFormScrollEl.value;
    if (!el) return;
    const max = Math.max(0, el.scrollHeight - el.clientHeight);
    const target = Math.round((val / 100) * max);
    el.scrollTop = target;
}

// Show only a selected competency and its parent (optionally keep scenario visible)
function showOnlySelectedAndParent(childId: number, keepScenario = true) {
    try {
        const parentEdge = childEdges.value.find((e) => e.target === childId);
        const parentId = parentEdge ? parentEdge.source : null;
        const scenarioId = scenarioNode.value?.id ?? null;
        nodes.value = nodes.value.map((n: any) => {
            if (n.id === parentId) return { ...n, visible: true };
            if (keepScenario && n.id === scenarioId) return { ...n, visible: true };
            return { ...n, visible: false };
        });

        childNodes.value = childNodes.value.map((c: any) => {
            if (c.id === childId) return { ...c, visible: true };
            // keep siblings of same parent visible (optional) — hide them to show only selected
            if (c.__parentId === parentId || c.parentId === parentId) return { ...c, visible: false };
            return { ...c, visible: false };
        });
    } catch (err: unknown) { void err; }
}

function syncSliderFromScroll() {
    const el = editFormScrollEl.value;
    if (!el) return;
    const max = Math.max(0, el.scrollHeight - el.clientHeight);
    const percent = max === 0 ? 0 : Math.round((el.scrollTop / max) * 100);
    editFormScrollPercent.value = percent;
}
// localStorage keys
const LS_KEYS = {
    collapsed: 'stratos:scenario:nodeSidebarCollapsed',
    lastView: 'stratos:scenario:lastView',
    lastFocusedId: 'stratos:scenario:lastFocusedNodeId',
};
const savedFocusedNodeId = ref<number | null>(null);

// viewport transform for pan/zoom
const viewX = ref(0);
const viewY = ref(0);
const viewScale = ref(1);
const viewportStyle = computed(() => ({
    transform: `translate(${viewX.value}px, ${viewY.value}px) scale(${viewScale.value})`,
    transformOrigin: '0 0',
}));

// displayNode: prefer `selectedChild` (a competency) for sidebar details, otherwise the focused capability
const displayNode = computed(() => selectedChild.value ?? focusedNode.value);

// scenario/origin node that can follow a selected child
const scenarioNode = ref<any>(null);
const followScenario = ref<boolean>(false);

// Transition timing used by `.node-group` CSS (keep in sync with CSS)
const TRANSITION_MS = 420;
const TRANSITION_BUFFER = 60; // small buffer to ensure browser finished

// ===== CENTRALIZED LAYOUT CONFIG - Tuneable from one place =====
const LAYOUT_CONFIG = {
    // ===== CAPABILITY NODE LAYOUT (parent level) =====
    capability: {
        spacing: {
            hSpacing: 100, // horizontal spacing in matrix layout
            vSpacing: 80, // vertical spacing in matrix layout
        },
        // Force simulation for D3 layout engine
        forces: {
            linkDistance: 100, // distance between connected nodes
            linkStrength: 0.5, // 0-1, how much links pull nodes
            chargeStrength: -220, // negative = repulsion, pushes nodes apart
        },
        // Scenario -> Capability curved edge
        scenarioEdgeDepth: 90, // curvature depth (px)
    },

    // ===== COMPETENCY NODE LAYOUT (child level) =====
    // Radial mode activates when >5 nodes with one selected
    competency: {
        radial: {
            radius: 150, // distance from center to other competencies
            selectedOffsetY:10, // vertical offset for selected node to leave room for skills
            startAngle: -Math.PI / 4, // -45° (bottom-left)
            endAngle: (5 * Math.PI) / 4, // 225° (covers lower 3/4 of circle)
        },
        // Sides-specific tuning (used when layout === 'sides')
        sides: {
            // multiplier applied to the selected node vertical offset when using `sides` layout
            // value <1 moves the selected node closer to the parent; default 0.75 (25% closer)
            selectedOffsetMultiplier: 0.75,
        },
        // default layout: 'auto' = use heuristic, or 'radial'|'matrix'|'sides'
        defaultLayout: 'auto',
        // default vertical offset (px) from parent capability to competencies when no prop overrides
        parentOffset: 10,
        // maximum number of competency nodes to display (extra are truncated)
        maxDisplay: 10,
        // matrix sizing rules based on number of nodes
        matrixVariants: [
            // 2..3 nodes -> 3 cols x 1 row
            { min: 2, max: 3, rows: 1, cols: 3 },
            // 4..8 nodes -> 4 cols x 2 rows
            { min: 4, max: 8, rows: 2, cols: 4 },
            // 9..10 nodes -> 5 cols x 2 rows
            { min: 9, max: 10, rows: 2, cols: 5 },
        ],
        spacing: {
            hSpacing: 100, // matrix layout horizontal
            vSpacing: 20, // matrix layout vertical
            parentOffset: 20, // distance below parent capability
        },
        // Capability -> Competency curved edge
        edge: {
            baseDepth: 40, // base curve depth (px)
            curveFactor: 0.45, // multiplier: curve = baseDepth + (distance * curveFactor)
            spreadOffset: 18, // offset for parallel curves
        },
    },

    // ===== SKILL NODE LAYOUT (grandchild level) =====
    // Radial mode activates when >4 skills
    skill: {
        maxDisplay: 10, // maximum skills to show
        radial: {
            radius: 100, // distance from competency center
            startAngle: -Math.PI / 6, // -30°
            endAngle: (7 * Math.PI) / 6, // 210° (covers lower 2/3 of circle)
            // multiplier to scale offsetY (simple relative adjustment). 0 = absolute, 0.2 = +20%
            offsetFactor: 0.6,
            offsetY: 120, // vertical offset from competency
        },
        // default layout selection for skills: 'auto'|'radial'|'matrix'|'sides'
        defaultLayout: 'auto',
        // matrix sizing rules for skills (mirror competencies but with 2..4 -> 1x4)
        matrixVariants: [
            { min: 2, max: 3, rows: 1, cols: 3 },
            { min: 4, max: 8, rows: 2, cols: 4 },
            { min: 9, max: 10, rows: 2, cols: 5 },
        ],
        // sides tuning for skills (used when layout === 'sides')
        sides: {
            hSpacing: 120,
            vSpacing: 70,
            parentOffset: 80,
            selectedOffsetMultiplier: 0.75,
        },
        linear: {
            hSpacing: 100, // Used for <=4 skills (horizontal)
            vSpacing: 60, // Used for <=4 skills (vertical)
        },
        // Competency -> Skill curved edge
        edge: {
            baseDepth: 20, // base curve depth (px)
            curveFactor: 0.45, // multiplier: curve = baseDepth + (distance * curveFactor)
            spreadOffset: 10, // offset for parallel curves
        },
    },

    // ===== Animations, node defaults and clamps (centralized) =====
    animations: {
        // JS timing values (ms)
        competencyEntryFinalize: 80,
        skillEntryFinalize: 70,
        collapseDuration: 10,
        // stagger / sequencing
        competencyStaggerRow: 30,
        competencyStaggerCol: 12,
        competencyStaggerRandom: 30,
        skillStaggerRow: 20,
        skillStaggerCol: 8,
        // fallback lead factor used when racing transition vs wait
        leadFactor: 0.6,
    },

    node: {
        radius: 34,
        focusRadius: 44,
    },

    clamp: {
        minY: 40,
        bottomPadding: 40,
        minViewportHeight: 120,
    },
};
// ===== END LAYOUT CONFIG ===== All layout parameters centralized here for easy tuning
// ===== END LAYOUT CONFIG ===== All layout parameters centralized here for easy tuning

// Animations, node defaults and clamps have been moved into the main LAYOUT_CONFIG object above.

function wait(ms: number) {
    return new Promise((res) => setTimeout(res, ms));
}

// Wait until the element for a node finishes its CSS transition (transform).
// Resolves true if transitionend fired, false if timed out or not found.
function waitForTransitionForNode(nodeId: number | string, timeoutMs = TRANSITION_MS * 2 + TRANSITION_BUFFER) {
    return new Promise<boolean>((resolve) => {
        const sel = `[data-node-id="${nodeId}"]`;
        let el: Element | null = document.querySelector(sel);
        let timer: ReturnType<typeof setTimeout> | null = null;
        const cleanup = () => {
            if (el) el.removeEventListener('transitionend', onEnd as EventListener);
            if (timer) clearTimeout(timer);
        };
        const onEnd = (ev: Event) => {
            const te = ev as TransitionEvent;
            if (!te.propertyName || te.propertyName === 'transform') {
                cleanup();
                resolve(true);
            }
        };

        timer = setTimeout(() => {
            cleanup();
            resolve(false);
        }, timeoutMs);

        if (!el) {
            // element may not be in DOM yet; wait a tick then try again
            nextTick().then(() => {
                el = document.querySelector(sel);
                if (!el) {
                    if (timer) clearTimeout(timer);
                    resolve(false);
                    return;
                }
                el.addEventListener('transitionend', onEnd as EventListener);
            });
        } else {
            el.addEventListener('transitionend', onEnd as EventListener);
        }
    });
}

// Devuelve la posición (coordenadas del mapa) del centro del nodo leyendo el DOM
function getNodeMapCenter(nodeId: number | string) {
    try {
        if (!mapRoot.value) return undefined;
        const sel = `[data-node-id="${nodeId}"]`;
        const el = document.querySelector(sel) as Element | null;
        if (!el) return undefined;
        const mapRect = mapRoot.value.getBoundingClientRect();
        const nodeRect = el.getBoundingClientRect();
        const xScreen = (nodeRect.left + nodeRect.width / 2) - mapRect.left;
        const yScreen = (nodeRect.top + nodeRect.height / 2) - mapRect.top;
        const scale = (viewScale.value || 1);
        const xMap = Math.round((xScreen - (viewX.value || 0)) / scale);
        const yMap = Math.round((yScreen - (viewY.value || 0)) / scale);
        return { x: xMap, y: yMap };
    } catch (err: unknown) { void err; }
    return undefined;
}

const originalPositions = ref<Map<number, { x: number; y: number }>>(new Map());

function centerOnNode(node: NodeItem, prev?: NodeItem) {
    if (!node) return;
    // If we're focusing a different node, immediately clear any existing child nodes/edges
    // so previous child animations don't continue while the parent moves.
    if (prev && prev.id !== node.id) {
        childNodes.value = [];
        childEdges.value = [];
    }
    // Save original positions the first time we focus a node so we can restore later
    if (originalPositions.value.size === 0) {
        nodes.value.forEach((n) => {
            originalPositions.value.set(n.id, { x: n.x ?? 0, y: n.y ?? 0 });
        });
        // also save scenario node if present
        if (scenarioNode.value) originalPositions.value.set(scenarioNode.value.id, { x: scenarioNode.value.x, y: scenarioNode.value.y });
    }

    // If there was a previously focused node, swap positions with it and keep others unchanged.
    if (prev && prev.id !== node.id) {
        const prevNode = nodes.value.find((n) => n.id === prev.id);
        const newNode = nodes.value.find((n) => n.id === node.id);
        if (prevNode && newNode) {
            const tx = prevNode.x ?? 0;
            const ty = prevNode.y ?? 0;
            prevNode.x = newNode.x ?? tx;
            prevNode.y = newNode.y ?? ty;
            newNode.x = tx;
            newNode.y = ty;
            // apply updated nodes (keep rest intact)
            nodes.value = nodes.value.map((n) => {
                if (!n) return n;
                if (n.id === newNode.id) return { ...newNode } as any;
                if (n.id === prevNode.id) return { ...prevNode } as any;
                return n;
            });
            // update scenario node if following (keep previous behavior)
            if (followScenario.value && scenarioNode.value) {
                // place scenario relative to the currently focused (new) node
                const centerX = Math.round(width.value / 2);
                const VERTICAL_FOCUS_RATIO = 0.45;
                const centerY = Math.round(height.value * VERTICAL_FOCUS_RATIO);
                const offsetY = props.visualConfig?.scenarioOffsetSwap ?? 150;
                scenarioNode.value.x = centerX;
                scenarioNode.value.y = Math.round(centerY - offsetY);
            }
            return;
        }
    }

    // compute absolute center position for focused node
    const centerX = Math.round(width.value / 2);
    const VERTICAL_FOCUS_RATIO = 0.25;
    const centerY = Math.round(height.value * VERTICAL_FOCUS_RATIO);

    // fixed side columns (absolute x coords)
    const leftX = Math.round(width.value * 0.18);
    const rightX = Math.round(width.value * 0.82);

    // separate other nodes into balanced left/right groups.
    // Sort remaining nodes by their original X (fallback to current x), then split into two halves
    const others = nodes.value.filter((n) => n && n.id !== node.id);
    const othersSorted = others
        .map((n) => ({ n, origX: n.x ?? originalPositions.value.get(n.id)?.x ?? width.value / 2 }))
        .sort((a, b) => a.origX - b.origX)
        .map((o) => o.n);
    const mid = Math.ceil(othersSorted.length / 2);
    const leftGroup = othersSorted.slice(0, mid);
    const rightGroup = othersSorted.slice(mid);

    // sort by original Y to keep visual order
    const getOrigY = (n: any) => n.y ?? originalPositions.value.get(n.id)?.y ?? centerY;
    leftGroup.sort((a, b) => getOrigY(a) - getOrigY(b));
    rightGroup.sort((a, b) => getOrigY(a) - getOrigY(b));

    // compute vertical distribution bounds
    const minY = 64;
    const maxY = Math.max(120, height.value - 64);

    const distribute = (group: any[], targetX: number, side: 'left' | 'right') => {
        if (group.length === 0) return;
        const len = group.length;
        // compute spacing dynamically based on available vertical space to avoid overlaps
        const available = Math.max(0, maxY - minY);
        // node visual sizes: main node radius ~34, ensure center-to-center spacing avoids overlap
        const FOCUS_RADIUS = 34;
        const minSpacing = Math.max(48, FOCUS_RADIUS * 2 + 8); // safe minimum spacing between centers
        const maxSpacing = 140; // cap spacing to avoid overly spread columns
        const spacing = len > 1 ? Math.min(maxSpacing, Math.max(minSpacing, Math.floor(available / (len - 1)))) : 0;

        // protect a vertical band around the focused node so distributed nodes don't overlap it
        const focusBand = Math.round(FOCUS_RADIUS + 12);
        const protectedTop = Math.max(minY, centerY - focusBand);
        const protectedBottom = Math.min(maxY, centerY + focusBand);

        // if group is large, split into multiple parallel columns to avoid vertical crowding
        const maxPerColumn = 5;
        if (len <= maxPerColumn) {
            let startY = Math.round(centerY - ((len - 1) * spacing) / 2);
            const endY = startY + (len - 1) * spacing;
            // If this span intersects the protected focused band, shift up or down to avoid overlap
            const intersectsProtected = !(endY < protectedTop || startY > protectedBottom);
            if (intersectsProtected) {
                // prefer shifting up if there is more room above, otherwise shift down
                const roomAbove = protectedTop - minY;
                const roomBelow = maxY - protectedBottom;
                if (roomAbove >= roomBelow) {
                    const shift = Math.min(roomAbove, protectedBottom - startY + focusBand);
                    startY = Math.max(minY, startY - shift);
                } else {
                    const shift = Math.min(roomBelow, endY - protectedTop + focusBand);
                    startY = Math.min(maxY - (len - 1) * spacing, startY + shift);
                }
            }

            for (let i = 0; i < len; i++) {
                const n = group[i];
                const proposedY = startY + i * spacing;
                n.x = targetX;
                n.y = clampY(proposedY);
            }
            return;
        }

        // multiple columns: compute number of columns and distribute items into them
        const cols = Math.ceil(len / maxPerColumn);
        const perCol = Math.ceil(len / cols);
        const colGap = 56; // horizontal gap between sub-columns

        for (let c = 0; c < cols; c++) {
            const colItems = group.slice(c * perCol, c * perCol + perCol);
            const colLen = colItems.length;
            const colSpacing = colLen > 1 ? Math.min(spacing, Math.floor(available / (colLen - 1))) : 0;
            const startY = Math.round(centerY - ((colLen - 1) * colSpacing) / 2);
            // compute x offset for this sub-column
            const offsetMult = (c - (cols - 1) / 2);
            const xOffset = Math.round(offsetMult * colGap) * (side === 'left' ? -1 : 1);
            const colX = targetX + xOffset;
            for (let i = 0; i < colLen; i++) {
                const n = colItems[i];
                const proposedY = startY + i * colSpacing;
                n.x = Math.min(Math.max(32, colX), Math.max(48, width.value - 32));
                n.y = clampY(proposedY);
            }
        }
    };

    // apply distribution
    distribute(leftGroup, leftX, 'left');
    distribute(rightGroup, rightX, 'right');

    // set focused node at center
    nodes.value = nodes.value.map((n) => {
        if (!n) return n;
        if (n.id === node.id) return { ...n, x: centerX, y: centerY } as any;
        const matched = leftGroup.find((m) => m.id === n.id) || rightGroup.find((m) => m.id === n.id);
        if (matched) return { ...n, x: matched.x, y: matched.y } as any;
        // fallback: clamp existing
        return { ...n, x: Math.min(Math.max(48, n.x ?? centerX), Math.max(160, width.value - 48)), y: clampY(n.y ?? centerY) } as any;
    });

    // Position scenario node (if following) relative to focused node
    if (followScenario.value && scenarioNode.value) {
        const offsetY = props.visualConfig?.scenarioOffset ?? 80;
        scenarioNode.value.x = centerX;
        scenarioNode.value.y = Math.round(centerY - offsetY);
    }

    // keep tooltip/layout responsibilities handled elsewhere; we no longer pan the viewport
}

function setScenarioInitial() {
    scenarioNode.value = {
        id: 0,
        name: (props.scenario && (props.scenario.name || 'Escenario')) as string,
        x: Math.round(width.value / 2),
        y: Math.round(height.value * 0.1),
    };
}

// (Using CSS scale for visual shrinking; radii kept constant)

const toggleSidebar = () => {
    showSidebar.value = !showSidebar.value;
};

function openScenarioInfo() {
    // close any focused node and its children, then reset view and show scenario info
    closeTooltip();
    viewScale.value = 1;
    viewX.value = 0;
    viewY.value = 0;
    showSidebar.value = true;
}

async function handleScenarioClick() {
    // Ejecuta el reordenamiento y luego restaura la vista al nivel inicial
    try {
        await reorderNodes();
    } catch (err: unknown) {
        void err;
    }
    try {
        restoreView();
    } catch (err: unknown) {
        void err;
    }
}

// initialize edit fields when focusedNode changes
watch(focusedNode, (nv) => {
    try {
        if (nv && (window as any).__DEBUG__) {
            const parentId = ((nv as any).id != null && (nv as any).id < 0)
                ? (childEdges.value.find((e) => e.target === (nv as any).id)?.source ?? null)
                : null;
            console.debug('[focusedNode.change] id=', (nv as any).id, 'level=', nodeLevel((nv as any).id), 'isChild=', !!(((nv as any).skills) || (nv as any).compId), 'parentId=', parentId);
        }
    } catch (err: unknown) { void err; }
    if (!nv) {
        editCapName.value = '';
        editCapDescription.value = '';
        editCapImportance.value = undefined;
        editCapLevel.value = null;
        editPivotStrategicRole.value = 'target';
        editPivotStrategicWeight.value = 10;
        editPivotPriority.value = 1;
        editPivotRationale.value = '';
        editPivotRequiredLevel.value = 3;
        editPivotIsCritical.value = false;
        return;
    }
    // populate from focused node and its raw payload if present
    editCapName.value = (nv as any).name ?? '';
    editCapDescription.value = (nv as any).description ?? (nv as any).raw?.description ?? '';
    editCapImportance.value = (nv as any).importance ?? (nv as any).raw?.importance ?? undefined;
    editCapLevel.value = (nv as any).level ?? null;

    // robust resolver (mirror behaviour in `resetFocusedEdits`) to handle
    // different backend payload shapes so `type`/`category` are preserved
    const resolveField = (key: string) => {
        return (
            (nv as any)[key] ??
            (nv as any).raw?.[key] ??
            (nv as any).raw?.attributes?.[key] ??
            (nv as any).raw?.capability?.[key] ??
            (nv as any).raw?.data?.[key] ??
            (nv as any).raw?.entity?.[key] ??
            ''
        );
    };

    try {
        const _t = resolveField('type');
        editCapType.value = typeof _t === 'string' ? _t.toLowerCase() : (_t != null ? String(_t).toLowerCase() : '');
    } catch (err: unknown) { editCapType.value = '' }
    try {
        const _c = resolveField('category');
        editCapCategory.value = typeof _c === 'string' ? _c.toLowerCase() : (_c != null ? String(_c).toLowerCase() : '');
    } catch (err: unknown) { editCapCategory.value = '' }

    // pivot values: try several locations
    editPivotStrategicRole.value = (nv as any).strategic_role ?? (nv as any).raw?.strategic_role ?? 'target';
    editPivotStrategicWeight.value = (nv as any).raw?.strategic_weight ?? 10;
    editPivotPriority.value = (nv as any).raw?.priority ?? 1;
    editPivotRationale.value = (nv as any).raw?.rationale ?? '';
    editPivotRequiredLevel.value = (nv as any).raw?.required_level ?? (nv as any).required ?? 3;
    editPivotIsCritical.value = !!((nv as any).raw?.is_critical || (nv as any).is_critical);
    // Ensure grandChild nodes (skills) are collapsed when focus changes to a different capability
    try {
        const sel = selectedChild.value;
        if (!nv) {
            // no focused node => clear grandchildren (animated)
            collapseGrandChildren();
            selectedChild.value = null;
        } else if (sel) {
            // if the selectedChild does not belong to the new focused node, collapse skills
            const parentEdge = childEdges.value.find((e) => e.target === (sel as any).id);
            const parentId = parentEdge ? parentEdge.source : null;
            if (parentId !== (nv as any).id) {
                collapseGrandChildren();
                selectedChild.value = null;
            }
        }
    } catch (err: unknown) { void err; }
});

// populate selectedChild edit fields when selection changes
watch(selectedChild, (nv) => {
    if (!nv) {
        // clear any expanded skills when selection cleared
        try { collapseGrandChildren(); } catch (err: unknown) { void err; }
        editChildName.value = '';
        editChildDescription.value = '';
        editChildReadiness.value = null;
        editChildSkills.value = '';
        editChildPivotStrategicWeight.value = 10;
        editChildPivotPriority.value = 1;
        editChildPivotRequiredLevel.value = 3;
        editChildPivotIsCritical.value = false;
        editChildPivotRationale.value = '';
        return;
    }
    // Recompute competency child positions using existing positions as start
    try {
        if (focusedNode.value) {
            expandCompetencies(focusedNode.value as NodeItem, { x: focusedNode.value.x ?? 0, y: focusedNode.value.y ?? 0 }, { layout: 'auto' });
        }
    } catch (err: unknown) { void err; }
    editChildName.value = nv.name ?? nv.raw?.name ?? '';
    editChildDescription.value = nv.description ?? nv.raw?.description ?? '';
    editChildReadiness.value = nv.readiness ?? nv.raw?.readiness ?? null;
    // skills may come as array
    const skillsArr = Array.isArray(nv.skills) ? nv.skills.map((s: any) => s.name ?? s) : [];
    editChildSkills.value = skillsArr.join(', ');
    // try to obtain pivot values from raw.pivot or raw.pivot_data
    const pivot = nv.raw?.pivot ?? nv.raw?.capability_pivot ?? {};
    editChildPivotStrategicWeight.value = pivot?.strategic_weight ?? 10;
    editChildPivotPriority.value = pivot?.priority ?? 1;
    editChildPivotRequiredLevel.value = pivot?.required_level ?? 3;
    editChildPivotIsCritical.value = !!pivot?.is_critical;
    editChildPivotRationale.value = pivot?.rationale ?? '';
});

// Watch createCompDialogVisible to reset form when modal closes
watch(createCompDialogVisible, (isVisible) => {
    if (!isVisible) {
        resetCompetencyForm();
    }
});

function resetFocusedEdits() {
    // reset edits to current focusedNode state
        if (focusedNode.value) {
        const f = focusedNode.value as any;
        // helper to robustly resolve a field from node or raw payload
        const resolveField = (key: string) => {
            return (
                f[key] ??
                f.raw?.[key] ??
                f.raw?.attributes?.[key] ??
                f.raw?.capability?.[key] ??
                f.raw?.data?.[key] ??
                f.raw?.entity?.[key] ??
                ''
            );
        };

        editCapName.value = f.name ?? '';
        editCapDescription.value = f.description ?? f.raw?.description ?? '';
        editCapImportance.value = f.importance ?? f.raw?.importance ?? undefined;
        editCapLevel.value = f.level ?? null;
        // normalize to match the v-select items (lowercase string values)
        try {
            const _t = resolveField('type');
            editCapType.value = typeof _t === 'string' ? _t.toLowerCase() : (_t != null ? String(_t).toLowerCase() : '');
        } catch (err: unknown) { editCapType.value = '' }
        try {
            const _c = resolveField('category');
            editCapCategory.value = typeof _c === 'string' ? _c.toLowerCase() : (_c != null ? String(_c).toLowerCase() : '');
        } catch (err: unknown) { editCapCategory.value = '' }
        try {
            // Provide more detailed inspection to diagnose missing type/category after tree refresh
            const raw = f.raw ?? {};
            let rawKeys: string[] = [];
            try { rawKeys = Object.keys(raw); } catch (err: unknown) { rawKeys = []; }
            console.debug('[resetFocusedEdits] resolved type=', editCapType.value, 'category=', editCapCategory.value);
            console.debug('[resetFocusedEdits] raw keys=', rawKeys);
            try { console.debug('[resetFocusedEdits] raw.capability=', raw?.capability); } catch (err: unknown) { void err; }
            try { console.debug('[resetFocusedEdits] raw.scenario_capabilities=', raw?.scenario_capabilities); } catch (err: unknown) { void err; }
            try { console.debug('[resetFocusedEdits] raw.pivot=', raw?.pivot); } catch (err: unknown) { void err; }
            try { console.debug('[resetFocusedEdits] capabilityTreeRaw sample=', capabilityTreeRaw?.value ? (Array.isArray(capabilityTreeRaw.value) ? capabilityTreeRaw.value.find((x: any) => x.id == f.id) : capabilityTreeRaw.value) : null); } catch (err: unknown) { void err; }
        } catch (err: unknown) { void err; }
        editPivotStrategicRole.value = f.strategic_role ?? f.raw?.strategic_role ?? 'target';
        // Normalize pivot sources: pivot may live under several keys depending on backend
        const raw = f.raw ?? {};
        let pivotSrc: any = raw.pivot ?? raw.scenario_capabilities ?? raw.scenario_capability ?? raw._pivot ?? null;
        if (Array.isArray(pivotSrc)) pivotSrc = pivotSrc[0] ?? null;
        editPivotStrategicWeight.value =
            (f.strategic_weight ?? pivotSrc?.strategic_weight ?? raw.strategic_weight) ?? 10;
        editPivotPriority.value = (f.priority ?? pivotSrc?.priority ?? raw.priority) ?? 1;
        editPivotRationale.value = (f.rationale ?? pivotSrc?.rationale ?? raw.rationale) ?? '';
        editPivotRequiredLevel.value = (f.required_level ?? pivotSrc?.required_level ?? raw.required_level ?? f.required) ?? 3;
        editPivotIsCritical.value = !!(f.is_critical ?? pivotSrc?.is_critical ?? raw.is_critical ?? f.is_critical);
    }
}

async function saveFocusedNode() {
    if (!focusedNode.value) return;
    const id = (focusedNode.value as any).id;
    savingNode.value = true;
    await ensureCsrf();
    try {
        // 1) attempt to update capability entity (if endpoint exists)
        const capPayload: any = {
            name: editCapName.value,
            description: editCapDescription.value,
            importance: typeof editCapImportance.value !== 'undefined' ? Number(editCapImportance.value) : undefined,
            position_x: (focusedNode.value as any).x ?? undefined,
            position_y: (focusedNode.value as any).y ?? undefined,
            type: editCapType.value || undefined,
            category: editCapCategory.value || undefined,
        };

        try {
            console.debug('[saveFocusedNode] PATCH /api/capabilities/' + id, capPayload);
            const capRes: any = await api.patch(`/api/capabilities/${id}`, capPayload);
            console.debug('[saveFocusedNode] PATCH /api/capabilities response', capRes);
            showSuccess('Capacidad actualizada');
            // optimistic local update
            try {
                const fd = focusedNode.value as any;
                if (fd) {
                    fd.type = editCapType.value || fd.type;
                    fd.category = editCapCategory.value || fd.category;
                    fd.raw = { ...(fd.raw || {}), ...(capRes?.data ?? {}) };
                }
            } catch (err: unknown) { void err; }
        } catch (errCap: unknown) {
            try {
                const _err: any = errCap as any;
                const status = _err?.response?.status ?? null;
                console.error('[saveFocusedNode] error PATCH /api/capabilities/' + id, _err?.response?.data ?? _err);
                if (status !== 404) {
                    showError(_err?.response?.data?.message || 'Error actualizando capacidad');
                }
            } catch (errInner: unknown) { void errInner; }
        }

        // 2) attempt to update pivot via best-effort PATCH endpoint
        const pivotPayload = {
            strategic_role: editPivotStrategicRole.value,
            strategic_weight: typeof editPivotStrategicWeight.value !== 'undefined' ? Number(editPivotStrategicWeight.value) : undefined,
            priority: typeof editPivotPriority.value !== 'undefined' ? Number(editPivotPriority.value) : undefined,
            rationale: editPivotRationale.value,
            required_level: typeof editPivotRequiredLevel.value !== 'undefined' ? Number(editPivotRequiredLevel.value) : undefined,
            is_critical: !!editPivotIsCritical.value,
        };

        try {
            if (!props.scenario || !props.scenario.id) {
                console.warn('[saveFocusedNode] no scenario context available; skipping pivot update');
            } else {
                console.debug('[saveFocusedNode] PATCH pivot', { scenarioId: props.scenario?.id, id, pivotPayload });
                let pivotResp: any = null;
                try {
                    pivotResp = await api.patch(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities/${id}`, pivotPayload);
                    showSuccess('Relación escenario–capacidad actualizada');
                } catch (errPivot: unknown) {
                    console.error('[saveFocusedNode] error PATCH pivot', (errPivot as any)?.response?.data ?? errPivot);
                    try {
                        // fallback: POST to create association (if missing)
                        pivotResp = await api.post(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities`, {
                            name: editCapName.value,
                            description: editCapDescription.value || '',
                            importance: editCapImportance.value ?? 3,
                            type: editCapType.value ?? null,
                            category: editCapCategory.value ?? null,
                            strategic_role: pivotPayload.strategic_role,
                            strategic_weight: pivotPayload.strategic_weight,
                            priority: pivotPayload.priority,
                            rationale: pivotPayload.rationale,
                            required_level: pivotPayload.required_level,
                            is_critical: pivotPayload.is_critical,
                        });
                        showSuccess('Relación actualizada (fallback)');
                    } catch (err2: unknown) {
                        console.error('[saveFocusedNode] error POST pivot fallback', (err2 as any)?.response?.data ?? err2);
                        showError('No se pudo actualizar la relación. Verifica el backend.');
                    }
                }

                // Merge pivot updates into local node state if backend returned something useful
                try {
                    const updated = (pivotResp?.data?.updated ?? pivotResp?.data ?? pivotResp) || null;
                    if (updated && typeof updated === 'object') {
                        // The backend may return only the changed fields in `updated`, or the whole relation.
                        const pivotUpdates = updated.updated ?? updated;
                        const pivotObj = pivotUpdates || updated;
                        // Update nodes array and focusedNode.raw.scenario_capabilities (best-effort)
                        nodes.value = nodes.value.map((n: any) => {
                            if (n.id === id) {
                                const raw = { ...(n.raw ?? {}), scenario_capabilities: Array.isArray(n.raw?.scenario_capabilities) ? n.raw.scenario_capabilities : [n.raw.scenario_capabilities].filter(Boolean) };
                                // merge pivot fields into the first pivot entry
                                raw.scenario_capabilities = raw.scenario_capabilities || [];
                                if (raw.scenario_capabilities.length === 0) raw.scenario_capabilities.push({});
                                raw.scenario_capabilities[0] = { ...(raw.scenario_capabilities[0] || {}), ...(pivotObj || {}) };
                                return { ...n, raw } as any;
                            }
                            return n;
                        });
                        if (focusedNode.value && (focusedNode.value as any).id === id) {
                            const fnRaw = { ...(((focusedNode.value as any).raw) ?? {}) };
                            fnRaw.scenario_capabilities = fnRaw.scenario_capabilities || [];
                            if (fnRaw.scenario_capabilities.length === 0) fnRaw.scenario_capabilities.push({});
                            fnRaw.scenario_capabilities[0] = { ...(fnRaw.scenario_capabilities[0] || {}), ...(pivotObj || {}) };
                            focusedNode.value = { ...(focusedNode.value as any), raw: fnRaw } as any;
                        }
                    }
                } catch (err: unknown) { void err; }
            }
        } catch (outerErr: unknown) {
            console.error('[saveFocusedNode] unexpected error in pivot update flow', outerErr);
        }

        // update local node immediately so UI reflects changes
        try {
            const nid = (focusedNode.value as any)?.id ?? id;
            nodes.value = nodes.value.map((n: any) => {
                if (n.id === nid) {
                    const updatedRaw = { ...(n.raw ?? {}), type: editCapType.value ?? (n.raw?.type ?? null), category: editCapCategory.value ?? (n.raw?.category ?? null) };
                    return { ...n, type: editCapType.value ?? n.type, category: editCapCategory.value ?? n.category, raw: updatedRaw } as any;
                }
                return n;
            });
            if (focusedNode.value && (focusedNode.value as any).id === id) {
                focusedNode.value = {
                    ...(focusedNode.value as any),
                    type: editCapType.value ?? (focusedNode.value as any).type,
                    category: editCapCategory.value ?? (focusedNode.value as any).category,
                    raw: { ...((focusedNode.value as any).raw ?? {}), type: editCapType.value ?? (focusedNode.value as any).raw?.type, category: editCapCategory.value ?? (focusedNode.value as any).raw?.category },
                } as any;
            }
        } catch (err: unknown) { void err; }

        // fetch authoritative capability entity from API (if available). We'll merge
        // it into local nodes AFTER reloading the capability-tree to avoid the
        // tree refresh overwriting the authoritative fields.
        let freshCap: any = null;
        try {
            const capResp: any = await api.get(`/api/capabilities/${id}`);
            freshCap = capResp?.data ?? capResp;
        } catch (err: unknown) { void err; }

        const focusedId = focusedNode.value ? (focusedNode.value as any).id : null;
        await loadTreeFromApi(props.scenario?.id);
        // After reloading canonical tree, merge authoritative entity fields if we fetched them
        try {
            if (freshCap && typeof freshCap.id !== 'undefined') {
                nodes.value = nodes.value.map((n: any) => (n.id === Number(freshCap.id) ? { ...n, type: freshCap.type ?? n.type, category: freshCap.category ?? n.category, raw: { ...(n.raw ?? {}), ...(freshCap ?? {}) } } : n));
                if (focusedNode.value && (focusedNode.value as any).id === Number(freshCap.id)) {
                    focusedNode.value = { ...((focusedNode.value as any) || {}), type: freshCap.type ?? (focusedNode.value as any).type, category: freshCap.category ?? (focusedNode.value as any).category, raw: { ...((focusedNode.value as any).raw ?? {}), ...(freshCap ?? {}) } } as any;
                    try { resetFocusedEdits(); } catch (err: unknown) { void err; }
                }
            }
        } catch (err: unknown) { void err; }
        try { console.debug('[saveFocusedNode] after refresh, focusedId=', focusedId, 'nodeById=', nodeById(focusedId)); } catch (err: unknown) { void err; }
        if (focusedId != null) {
            const restored = nodeById(focusedId);
            if (restored) {
                focusedNode.value = restored as any;
                try { resetFocusedEdits(); } catch (err: unknown) { void err; }
                await nextTick();
                centerOnNode(restored as NodeItem);
            }
        }
    } finally {
        savingNode.value = false;
    }
}

async function deleteFocusedNode() {
    if (!focusedNode.value && !selectedChild.value) return;
    
    // Determine which node to delete
    const nodeToDelete = selectedChild.value || focusedNode.value;
    if (!nodeToDelete) return;
    
    const id = (nodeToDelete as any).id;
    const isChild = typeof id === 'number' && id < 0;
    
    // Debug log
    console.debug('[deleteFocusedNode] selectedChild:', selectedChild.value ? 'YES' : 'NO', 'id:', id, 'isChild:', isChild);
    
    // For child nodes, use the compId or absolute value, NOT the parent
    let compId: number | null = null;
    if (isChild) {
        compId = (nodeToDelete as any).compId ?? Math.abs(id);
    }
    
    // Determine what we're deleting
    const isCompetency = isChild || selectedChild.value !== null;
    const nodeName = isCompetency ? 'competencia' : 'capacidad';
    const confirmMsg = isCompetency 
        ? '¿Eliminar esta competencia? Esta acción es irreversible.'
        : '¿Eliminar esta capacidad y su relación con el escenario? Esta acción es irreversible.';
    
    console.debug('[deleteFocusedNode] isCompetency:', isCompetency, 'nodeName:', nodeName, 'compId:', compId);
    
    // Confirm destructive action
    const ok = window.confirm(confirmMsg);
    if (!ok) return;
    
    savingNode.value = true;
    try {
        if (isCompetency) {
            // DELETE COMPETENCY (child node)
            const deleteId = compId || Math.abs(id);
            console.debug('[deleteFocusedNode] Deleting competency with ID:', deleteId);
            
            // Get the parent capability ID
            const parentEdge = childEdges.value.find((e) => e.target === id);
            const capabilityId = parentEdge ? parentEdge.source : focusedNode.value?.id;
            
            console.debug('[deleteFocusedNode] Parent capability ID:', capabilityId);
            
            if (!capabilityId) {
                showError('No se puede determinar la capacidad padre');
                savingNode.value = false;
                return;
            }
            
            try {
                const res = await api.delete(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities/${capabilityId}/competencies/${deleteId}`);
                console.debug('[deleteFocusedNode] DELETE competency response:', res);
            } catch (e: unknown) {
                const _e: any = e as any;
                console.debug('[deleteFocusedNode] DELETE competency error status:', _e?.response?.status);
                if (_e?.response?.status !== 404) throw e;
                // 404 = backend doesn't expose delete, remove locally
            }
            
            // Remove from local state
            childNodes.value = childNodes.value.filter((c) => {
                const cId = c.compId ?? Math.abs(c.id);
                return cId !== deleteId;
            });
            childEdges.value = childEdges.value.filter((e) => e.target !== id && e.source !== id);
            grandChildNodes.value = grandChildNodes.value.filter((g) => {
                // Remove skills associated with this competency
                return g.parentId !== deleteId && g.parentId !== id;
            });
            grandChildEdges.value = grandChildEdges.value.filter((e) => e.source !== id && e.target !== id);
            
            console.debug('[deleteFocusedNode] Competency deleted locally');
            showSuccess('Competencia eliminada');
        } else {
            // DELETE CAPABILITY (parent node)
            console.debug('[deleteFocusedNode] Deleting capability with ID:', id);
            let pivotErrStatus: number | null = null;
            let capErrStatus: number | null = null;
            
            // 1) attempt to delete pivot relation first (best-effort)
            try {
                await api.delete(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities/${id}`);
            } catch (e: unknown) {
                const _e: any = e as any;
                pivotErrStatus = _e?.response?.status ?? null;
            }

            // 2) attempt to delete capability entity
            try {
                await api.delete(`/api/capabilities/${id}`);
                // remove locally if present
                nodes.value = nodes.value.filter((n) => n.id !== id);
                // remove any childNodes and edges referencing this capability
                childNodes.value = childNodes.value.filter((c) => !(c.__parentId === id || c.parentId === id || (c.raw && c.raw.capability_id === id)));
                edges.value = edges.value.filter((e) => e.source !== id && e.target !== id);
                childEdges.value = childEdges.value.filter((e) => e.source !== id && e.target !== id);
            } catch (e: unknown) {
                const _e: any = e as any;
                capErrStatus = _e?.response?.status ?? null;
            }

            // If both endpoints returned 404 (not found), assume backend doesn't expose delete and remove locally
            if ((pivotErrStatus === 404 || pivotErrStatus === null) && (capErrStatus === 404 || capErrStatus === null)) {
                // remove locally anyway
                nodes.value = nodes.value.filter((n) => n.id !== id);
                childNodes.value = childNodes.value.filter((c) => !(c.__parentId === id || c.parentId === id || (c.raw && c.raw.capability_id === id)));
                edges.value = edges.value.filter((e) => e.source !== id && e.target !== id);
                childEdges.value = childEdges.value.filter((e) => e.source !== id && e.target !== id);
                showError('Eliminado localmente. El backend no expone endpoints DELETE; implementar API para eliminación permanente.');
            } else {
                showSuccess('Capacidad y relación eliminadas');
            }
        }

        // Refresh tree (best-effort)
        await loadTreeFromApi(props.scenario?.id);
        
        // Clear focus and selection
        focusedNode.value = null;
        selectedChild.value = null;
    } catch (err: unknown) {
        console.error('[deleteFocusedNode] Error:', err);
        showError(`Error al eliminar ${nodeName}`);
    } finally {
        savingNode.value = false;
    }
}

function createCapabilityClicked() {
    // open the create-capability modal (prefer modal over sidebar for quick create)
    // initialize defaults for the form
    newCapName.value = '';
    newCapDescription.value = '';
    newCapType.value = '';
    newCapCategory.value = '';
    newCapImportance.value = 3;
    pivotStrategicRole.value = 'target';
    pivotStrategicWeight.value = 10;
    pivotPriority.value = 1;
    pivotRationale.value = '';
    pivotRequiredLevel.value = 3;
    pivotIsCritical.value = false;
    // ensure scenario/sidebar is closed when opening the create-capability modal
    showSidebar.value = false;
    createModalVisible.value = true;
}

// Dialog helpers to centralize open/close logic
function showCreateCompDialog() {
    try {
        const dn: any = displayNode.value;
        // If displayNode is a competency, ensure we focus its parent capability
        if (dn && (dn.compId || (typeof dn.id === 'number' && dn.id < 0))) {
            const parentEdge = childEdges.value.find((e) => e.target === dn.id);
            const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
            if (parentNode) focusedNode.value = parentNode as any;
        } else if (dn && (dn.id != null)) {
            focusedNode.value = nodeById(dn.id) || (dn as any);
        } else if (selectedChild.value) {
            const childId = (selectedChild.value as any)?.id ?? null;
            const parentEdge = childId != null ? childEdges.value.find((e) => e.target === childId) : null;
            const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
            if (parentNode) focusedNode.value = parentNode as any;
        }
    } catch (err: unknown) { void err; }
    // Force capability context for creation
    selectedChild.value = null;
    createCompDialogVisible.value = true;
}
function showCreateSkillDialog() {
    // Ensure we have a selectedChild when opening the create-skill dialog.
    // If `displayNode` is a competency, keep it. If it's a capability with competencies,
    // default to the first competency so the created skill has a target.
    try {
        if (displayNode.value && displayNode.value) {
            const dn: any = displayNode.value;
            if (dn.compId || (typeof dn.id === 'number' && dn.id < 0)) {
                selectedChild.value = dn as any;
            } else if (Array.isArray(dn.competencies) && dn.competencies.length > 0) {
                // try to find existing child node representation
                const first = dn.competencies[0];
                const existing = childNodes.value.find((c: any) => c.compId === first.id);
                selectedChild.value = existing || { compId: first.id, raw: first, id: -(dn.id * 1000 + 1) } as any;
            }
        }
    } catch (err: unknown) { void err; }
    createSkillDialogVisible.value = true;
}
async function openSelectSkillDialog() {
    await loadAvailableSkills();
    selectSkillDialogVisible.value = true;
}
async function openAddExistingCompDialog() {
    await fetchAvailableCompetencies();
    addExistingCompDialogVisible.value = true;
}

async function saveNewCapability() {
    if (!props.scenario || !props.scenario.id) return showError('Escenario no seleccionado');
    if (!newCapName.value || !newCapName.value.trim()) return showError('El nombre es obligatorio');
    creating.value = true;
    await ensureCsrf();
    try {
        const payload: any = {
            name: newCapName.value.trim(),
            description: newCapDescription.value || null,
            importance: typeof newCapImportance.value !== 'undefined' ? Number(newCapImportance.value) : 3,
            type: newCapType.value || null,
            category: newCapCategory.value || null,
            // pivot attributes
            strategic_role: pivotStrategicRole.value,
            strategic_weight: typeof pivotStrategicWeight.value !== 'undefined' ? Number(pivotStrategicWeight.value) : 10,
            priority: typeof pivotPriority.value !== 'undefined' ? Number(pivotPriority.value) : 1,
            rationale: pivotRationale.value || null,
            required_level: typeof pivotRequiredLevel.value !== 'undefined' ? Number(pivotRequiredLevel.value) : 3,
            is_critical: !!pivotIsCritical.value,
        };

        console.debug('[saveNewCapability] POST /api/strategic-planning/scenarios/' + props.scenario.id + '/capabilities', payload);
        const res: any = await api.post(`/api/strategic-planning/scenarios/${props.scenario.id}/capabilities`, payload);
        console.debug('[saveNewCapability] POST response', res);
        const created = res?.data ?? res;
        showSuccess('Capacidad creada y asociada al escenario');
        // Optimistic update: add the created capability to the local nodes immediately
        try {
            if (created && typeof created.id !== 'undefined') {
                const fallback = computeInitialPosition(nodes.value.length, (nodes.value.length || 0) + 1);
                const newNode = {
                    id: Number(created.id),
                    name: created.name,
                    displayName: wrapLabel(created.name, 14),
                    x: Math.round(fallback.x),
                    y: Math.round(fallback.y),
                    is_critical: !!created.is_critical,
                    description: created.description ?? null,
                    competencies: [],
                    importance: created.importance ?? 3,
                    level: null,
                    required: null,
                    raw: created,
                } as any;
                nodes.value = [...nodes.value, newNode];
                // rebuild edges to include new node
                buildEdgesFromItems((props.scenario as any).capabilities || []);
                positionsDirty.value = true;
            }
        } catch (optErr: unknown) {
            void optErr;
        }
        // Refresh canonical tree from API to include pivot attributes and avoid drift
        await loadTreeFromApi(props.scenario.id);
        // close modal and reset
        createModalVisible.value = false;
        newCapName.value = '';
        newCapDescription.value = '';
        newCapType.value = '';
        newCapCategory.value = '';
        newCapImportance.value = 3;
        pivotStrategicRole.value = 'target';
        pivotStrategicWeight.value = 10;
        pivotPriority.value = 1;
        pivotRationale.value = '';
        pivotRequiredLevel.value = 3;
        pivotIsCritical.value = false;
    } catch (err: unknown) {
        const _err: any = err as any;
        showError(_err?.response?.data?.message || 'Error creando capacidad');
    } finally {
        creating.value = false;
    }
}

function truncateLabel(s: any, max = 14) {
    if (s == null) return '';
    const str = String(s);
    return str.length > max ? str.slice(0, max - 1) + '…' : str;
}

function wrapLabel(s: any, max = 14) {
    // Wrap into at most two lines, each up to `max` chars. If text exceeds two lines,
    // truncate the second line and add an ellipsis.
    if (s == null) return '';
    const str = String(s).trim();
    if (str.length <= max) return str;

    // Helper to cut a line at word boundary if possible
    const cutLine = (text: string, limit: number) => {
        if (text.length <= limit) return { line: text, rest: '' };
        // Try to break at last space within limit
        const slice = text.slice(0, limit + 1);
        const lastSpace = slice.lastIndexOf(' ');
        if (lastSpace > 0) {
            const line = text.slice(0, lastSpace);
            const rest = text.slice(lastSpace + 1).trim();
            return { line, rest };
        }
        // No space found: hard cut
        return { line: text.slice(0, limit), rest: text.slice(limit).trim() };
    };

    const first = cutLine(str, max);
    if (!first.rest) return first.line;

    // build second line (truncate with ellipsis if needed)
    const secondRaw = first.rest;
    if (secondRaw.length <= max) return first.line + '\n' + secondRaw;
    // try to cut second at word boundary
    const secondCut = cutLine(secondRaw, max);
    let second = secondCut.line;
    if (secondCut.rest && second.length >= max) {
        // ensure room for ellipsis
        second = second.slice(0, Math.max(0, max - 1));
        second = second.replace(/\s+$/,'');
        second = second + '…';
    } else if (secondCut.rest) {
        // append ellipsis if anything remains
        second = second + '…';
    }
    return first.line + '\n' + second;
}

function computeInitialPosition(idx: number, total: number) {
    // Grid-based centered layout: place nodes in a nearly square grid centered
    const centerX = width.value / 2;
    const centerY = height.value / 2 - 30;

    if (total <= 1) return { x: Math.round(centerX), y: Math.round(centerY) };

    // compute grid dimensions: choose a balanced grid (near-square) up to 5 columns
    // previous behaviour used `columns = total` which produced a single row for small totals
    // (e.g. 4 nodes -> 4 columns). Use sqrt-based heuristic so 4 -> 2x2, 3 -> 2x2, 5 -> 3x2, etc.
    const columns = Math.min(5, Math.max(1, Math.ceil(Math.sqrt(total))));
    const rows = Math.max(1, Math.ceil(total / columns));

    // margins and spacing (more compact vertical spacing)
    const margin = 24;
    const availableW = Math.max(120, width.value - margin * 2);
    const availableH = Math.max(120, height.value - margin * 2);
    const spacingX = columns > 1 ? Math.min(160, Math.floor(availableW / columns)) : 0;
    // increase vertical spacing cap to provide more room between rows
    const spacingY = rows > 1 ? Math.min(140, Math.floor(availableH / rows)) : 0;

    const col = idx % columns;
    const row = Math.floor(idx / columns);

    // center the whole grid around centerX/centerY
    const totalGridW = (columns - 1) * spacingX;
    const totalGridH = (rows - 1) * spacingY;
    const offsetX = col * spacingX - totalGridW / 2;
    const offsetY = row * spacingY - totalGridH / 2;

    return { x: Math.round(centerX + offsetX), y: Math.round(centerY + offsetY) };
}

/**
 * Reorder main nodes according to simple layout rules requested by the user:
 * - 1: centered
 * - 2: side-by-side
 * - 3: center + two sides
 * - 4..6: single row evenly spaced
 * - >=7: grid with up to 6 columns (rows = ceil(total/cols))
 * After reordering nodes are updated in `nodes.value` and `positionsDirty` is set.
 */
import reorderNodesHelper from '@/utils/reorderNodesHelper';

async function reorderNodes() {
    const total = nodes.value.length;
    if (!total) return;

    // Delegate base layout computation to helper for easier testing
    const scenario = scenarioNode.value ? { id: scenarioNode.value.id, x: scenarioNode.value.x, y: scenarioNode.value.y } : undefined;
    // Diagnostic logs: print node ids/count before reorder to help debug unexpected layouts
    try {
        // Use console.debug so logs are easy to filter in browser devtools
        console.debug('[reorderNodes] before - count:', nodes.value.length, 'ids:', nodes.value.map((n: any) => n && n.id));
    } catch (err: unknown) { void err; }

    // ensure each item has a `name` and cast the helper result to the expected NodeItem[] type
    nodes.value = reorderNodesHelper(
        nodes.value.map((n: any) => ({ ...n, name: n.name ?? n.raw?.name ?? '' } as any)),
        width.value,
        height.value,
        scenario,
    ) as unknown as Array<NodeItem>;

    try {
        console.debug('[reorderNodes] after - count:', nodes.value.length, 'ids:', nodes.value.map((n: any) => n && n.id));
    } catch (err: unknown) { void err; }


    positionsDirty.value = true;
    // Clear focus/children so renderNodeX doesn't snap nodes into side columns
    try {
        focusedNode.value = null;
        childNodes.value = [];
        childEdges.value = [];
        showSidebar.value = false;
    } catch (err: unknown) {
        void err;
    }

    // Persist positions immediately after reordering so the change is durable.
    try {
        await savePositions();
        positionsDirty.value = false;
    } catch (err: unknown) {
        void err;
        showError('No se pudieron guardar las posiciones tras reordenar');
        // leave positionsDirty=true to indicate unsaved changes
        positionsDirty.value = true;
    }
}

// Note: initial ordering uses `reorderNodes()` which also persists positions.

function nodeRenderShift(n: any) {
    // New behavior: when a node is focused, place other nodes in fixed left/right columns
    if (!focusedNode.value) return 0;
    if (!n || n.id === focusedNode.value.id) return 0;
    // Decide side based on original x relative to focused pivot
    const pivotX = focusedNode.value.x ?? width.value / 2;
    const originalX = n.x ?? width.value / 2;
    const leftX = Math.round(width.value * 0.18);
    const rightX = Math.round(width.value * 0.82);
    return originalX < pivotX ? leftX - (n.x ?? 0) : rightX - (n.x ?? 0);
}

// Prevent linter false-positives for locally declared but template-used helpers
void truncateLabel;
void nodeRenderShift;
void startPanelDrag;

function renderNodeX(n: any) {
    const minX = 48;
    const maxX = Math.max(160, width.value - 48);
    // When a node is focused, non-selected nodes should snap to fixed side columns
    if (focusedNode.value && n && n.id !== focusedNode.value.id) {
        const pivotX = focusedNode.value.x ?? width.value / 2;
        const originalX = n.x ?? width.value / 2;
        const leftX = Math.round(width.value * 0.18);
        const rightX = Math.round(width.value * 0.82);
        const target = originalX < pivotX ? leftX : rightX;
        return Math.min(Math.max(minX, target), maxX);
    }
    const base = (n.x ?? 0);
    return Math.min(Math.max(minX, base), maxX);
}

function renderedNodeById(id: number) {
    if (id == null) return null;
    // special-case: scenarioNode
    if (scenarioNode.value && id === scenarioNode.value.id) {
        return { x: scenarioNode.value.x, y: scenarioNode.value.y } as any;
    }
    if (id < 0) {
        // grandChild nodes also use negative ids; prefer returning a grandChild node
        const g = grandChildNodeById(id);
        if (g) return g;
        return childNodeById(id);
    }
    const n = nodeById(id);
    if (!n) return null;
    // ensure we pass a number to clampY (avoid undefined)
    return { x: renderNodeX(n), y: clampY(n.y ?? 0) } as any;
}

// Returns true if the given edge's target node is approximately centered horizontally
function edgeTargetIsCentered(e: Edge) {
    try {
        const tgt = renderedNodeById(e.target);
        if (!tgt || typeof tgt.x !== 'number') return false;
        const centerX = Math.round(width.value / 2);
        return Math.abs((tgt.x ?? 0) - centerX) <= 12; // 12px tolerance
    } catch (err: unknown) { void err; }
    return false;
}

// Devuelve coordenadas ajustadas para el extremo de una arista.
// Si forTarget es true y el target es un child node, devolvemos y ligeramente por encima
// del centro del nodo para que la línea no quede oculta por el círculo del nodo.
function edgeEndpoint(e: Edge, forTarget = true) {
    try {
        const id = forTarget ? e.target : e.source;
        const n = renderedNodeById(id);
        if (!n) return { x: undefined, y: undefined } as any;
        const x = n.x;
        let y = n.y;
        // si es el target y corresponde a un child node (id negativo), ajustar para evitar solapamiento
        if (forTarget && id < 0) {
            // Distinguimos entre nodo child (competency) y grandChild (skill)
            const isGrand = !!grandChildNodeById(id);
            const childRadius = isGrand ? 14 : 24; // skills son más pequeños
            const extraGap = isGrand ? 4 : 6; // separación visual adicional menor para skills
            y = (y ?? 0) - (childRadius + extraGap); // dejar un gap suficiente para que la línea no quede oculta
        }
        return { x, y } as any;
    } catch (err: unknown) { void err; }
    return { x: undefined, y: undefined } as any;
}

// Devuelve el índice dentro de un grupo de aristas que comparten mismo source y target aproximado
function groupedIndexForEdge(e: Edge) {
    try {
        const tgt = renderedNodeById(e.target);
        if (!tgt) return 0;
        const candidates = childEdges.value.filter((ed) => {
            const rt = renderedNodeById(ed.target);
            return ed.source === e.source && rt && Math.abs((rt.x ?? 0) - (tgt.x ?? 0)) <= 8;
        });
        candidates.sort((a, b) => (a.target - b.target));
        return Math.max(0, candidates.findIndex((c) => c === e));
    } catch (err: unknown) { void err; }
    return 0;
}

// Helper to safely read optional animation opacity from edge objects (some edges carry runtime-only props)
function edgeAnimOpacity(e: Edge) {
    return ((e as any).animOpacity ?? 0.98) as number;
}

// Construye los puntos o path para una arista según el modo seleccionado
    function edgeRenderFor(e: Edge) {
    try {
        const start = edgeEndpoint(e, false);
        const end = edgeEndpoint(e, true);
        const x1 = start.x; const y1 = start.y; const x2 = end.x; const y2 = end.y;
        const mode = childEdgeMode.value;
        // detectar si la arista apunta a un grandChild (skill) para parámetros específicos
        const isGrand = !!grandChildNodeById(e.target) || !!grandChildNodeById(e.source) || grandChildEdges.value.includes(e as any);
            // modo curva
            if (mode === 2 && typeof x1 === 'number' && typeof x2 === 'number') {
                // control point adaptativo para curvas más pronunciadas, configurable via LAYOUT_CONFIG
                const baseDepth = isGrand ? LAYOUT_CONFIG.skill.edge.baseDepth : LAYOUT_CONFIG.competency.edge.baseDepth;
                const curveFactor = isGrand ? LAYOUT_CONFIG.skill.edge.curveFactor : LAYOUT_CONFIG.competency.edge.curveFactor;
                const distance = Math.abs((y2 ?? 0) - (y1 ?? 0));
                const depth = Math.max(baseDepth, Math.round(distance * curveFactor) + baseDepth);
                const cpY = Math.min((y1 ?? 0), (y2 ?? 0)) + depth;
                const d = `M ${x1} ${y1} C ${x1} ${cpY} ${x2} ${cpY} ${x2} ${y2}`;
                return { isPath: true, d } as any;
            }
        // modo spread: desplazar X del target según índice en grupo
        if (mode === 3 && typeof x1 === 'number' && typeof x2 === 'number') {
            const idx = groupedIndexForEdge(e);
            // use candidates from both childEdges and grandChildEdges so grouping keeps consistent spacing
            const candidates = childEdges.value.concat(grandChildEdges.value).filter((ed) => {
                const rt = renderedNodeById(ed.target);
                const r = renderedNodeById(e.target);
                return ed.source === e.source && rt && r && Math.abs((rt.x ?? 0) - (r.x ?? 0)) <= 8;
            });
            const centerOffset = ((idx - (candidates.length - 1) / 2) * (isGrand ? LAYOUT_CONFIG.skill.edge.spreadOffset : LAYOUT_CONFIG.competency.edge.spreadOffset));
            return { isPath: false, x1, y1, x2: (x2 ?? 0) + centerOffset, y2 } as any;
        }
        // modo gap grande: aumentar el desplazamiento vertical del target
        if (mode === 1 && typeof x1 === 'number' && typeof x2 === 'number') {
            // reduce the gap adjustment for skill nodes (smaller radius)
            const childRadius = isGrand ? 14 : 20;
            const y2adj = (y2 ?? 0) - (childRadius - 2);
            return { isPath: false, x1, y1, x2, y2: y2adj } as any;
        }
        // modo por defecto: offset pequeño
        return { isPath: false, x1, y1, x2, y2 } as any;
    } catch (err: unknown) {
        void err;
        // On error return a safe fallback shape indicating no valid geometry
        return { isPath: false, x1: undefined, y1: undefined, x2: undefined, y2: undefined } as any;
    }
}

// Devuelve un path curvo para aristas scenario->capability (configurable por LAYOUT_CONFIG.capability.scenarioEdgeDepth)
function scenarioEdgePath(e: Edge) {
    try {
        const s = renderedNodeById(e.source);
        const t = renderedNodeById(e.target);
        if (!s || !t || typeof s.x !== 'number' || typeof t.x !== 'number') return '';
        const depth = LAYOUT_CONFIG.capability.scenarioEdgeDepth;
        const cpY = Math.min((s.y ?? 0), (t.y ?? 0)) + depth;
        return `M ${s.x} ${s.y} C ${s.x} ${cpY} ${t.x} ${cpY} ${t.x} ${t.y}`;
    } catch (err: unknown) { void err; }
    return '';
}

// Debug helpers removed

function buildNodesFromItems(items: any[]) {
    if (!Array.isArray(items) || items.length === 0) {
        nodes.value = [];
        return;
    }
    // prefer provided position_x/position_y or numeric x/y; if missing leave undefined so force layout can compute
    const centerX = width.value / 2;
    const centerY = height.value / 2 - 30;
    const radius = Math.min(width.value, height.value) / 3;
    const angleStep = (2 * Math.PI) / items.length;
    // avoid lint 'assigned but never used' for debugging helpers
    void centerX;
    void centerY;
    void radius;
    void angleStep;
    const mapped = items.map((it: any, idx: number) => {
        const rawX = it.position_x ?? it.x ?? it.cx ?? null;
        const rawY = it.position_y ?? it.y ?? it.cy ?? null;
            const parsedX = rawX != null ? parseFloat(String(rawX)) : NaN;
            const parsedY = rawY != null ? parseFloat(String(rawY)) : NaN;
            // If stored coordinates are valid numbers, prefer them so nodes remain fixed where the user left them.
            // Heuristic: legacy `capabilities.position_x/position_y` were stored as percentages (0..100).
            // Newer pivot `scenario_capabilities.position_x` may be absolute pixels (>100). Detect small values
            // and convert from percentage -> pixels using current canvas size to avoid overlaps with the scenario origin.
            const hasPos = !Number.isNaN(parsedX) && !Number.isNaN(parsedY);
            let x: number | undefined = undefined;
            let y: number | undefined = undefined;
            if (hasPos) {
                const looksLikePercent = parsedX >= 0 && parsedX <= 100 && parsedY >= 0 && parsedY <= 100;
                if (looksLikePercent) {
                    x = Math.round((parsedX / 100) * width.value);
                    y = Math.round((parsedY / 100) * height.value);
                } else {
                    x = Math.round(parsedX);
                    y = Math.round(parsedY);
                }
            }
        // if missing position, place roughly on a circle initially (helps force start) but mark undefined so we can re-run force
        const fallbackPos = computeInitialPosition(idx, items.length);
        const fallbackX = Math.round(fallbackPos.x);
        const fallbackY = Math.round(fallbackPos.y);
        return {
            id: Number(it.id),
            name: it.name,
            x: x ?? fallbackX,
            y: y ?? fallbackY,
            _hasCoords: hasPos,
            is_critical: it.is_critical ?? it.isCritical ?? false,
            description: it.description ?? it.desc ?? null,
            competencies: Array.isArray(it.competencies)
                ? it.competencies
                : Array.isArray(it.competency)
                ? it.competency
                : [],
            importance: it.importance ?? it.rank ?? null,
            level: it.level ?? null,
            required: it.required ?? null,
            type: it.type ?? it.raw?.type ?? null,
            category: it.category ?? it.raw?.category ?? null,
            raw: it,
        } as any;
    });
    nodes.value = mapped.map((m: any) => ({
        id: m.id,
        name: m.name,
        displayName: wrapLabel(m.name, 14),
        x: m.x,
        y: m.y,
        is_critical: !!m.is_critical,
        description: m.description,
        competencies: m.competencies,
        importance: m.importance,
        level: m.level,
        required: m.required,
        type: m.type,
        category: m.category,
        raw: m.raw,
    }));
    // initialize scenario node after building nodes
    setScenarioInitial();
    // Ensure no node is placed on top of the scenario origin. If a stored/initial
    // position is too close to the scenario node, push it outward beneath the origin
    // to avoid visual overlap. This handles legacy coords and saved positions.
    try {
        const MIN_ORIGIN_SEPARATION = 140; // pixels
        if (scenarioNode.value) {
            const sx = scenarioNode.value.x ?? Math.round(width.value / 2);
            const sy = scenarioNode.value.y ?? Math.round(height.value * 0.1);
            nodes.value = nodes.value.map((n: any) => {
                if (!n || n.x == null || n.y == null) return n;
                const dx = n.x - sx;
                const dy = n.y - sy;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < MIN_ORIGIN_SEPARATION) {
                    // push node downwards preferentially; if directly above, nudge down
                    if (dist === 0) {
                        return { ...n, x: n.x, y: clampY(sy + MIN_ORIGIN_SEPARATION) } as any;
                    }
                    const scale = MIN_ORIGIN_SEPARATION / Math.max(1, dist);
                    const newX = Math.round(sx + dx * scale);
                    const newY = Math.round(sy + dy * scale);
                    return { ...n, x: newX, y: clampY(newY) } as any;
                }
                return n;
            });
        }
    } catch (err: unknown) {
        void err;
    }
    // build scenario->capability edges so initial view shows connections from scenario to capabilities
    scenarioEdges.value = nodes.value.map((n: any) => ({ source: scenarioNode.value?.id ?? 0, target: n.id, isScenarioEdge: true } as Edge));
    // build edges before attempting a force layout
    buildEdgesFromItems(items);
    // Only run force layout if some nodes originally had real coordinates.
    // When we intentionally ignore stored coords (default grid), do not run the simulation
    const hadAnyCoords = mapped.some((m: any) => !!m._hasCoords);
    if (hadAnyCoords) runForceLayout();
    // store last items so we can recompute layout on resize
    lastItems = items;
}

function runForceLayout() {
    try {
        // prepare mutable nodes/links for simulation
        const simNodes = nodes.value.map((n) => ({
            id: n.id,
            x: n.x || 0,
            y: n.y || 0,
        }));
        const simLinks = edges.value.map((l) => ({
            source: l.source,
            target: l.target,
        }));
        const simulation = d3
            .forceSimulation(simNodes as any)
            .force(
                'link',
                (d3 as any)
                    .forceLink(simLinks)
                    .id((d: any) => d.id)
                    .distance(LAYOUT_CONFIG.capability.forces.linkDistance)
                    .strength(LAYOUT_CONFIG.capability.forces.linkStrength),
            )
            .force('charge', (d3 as any).forceManyBody().strength(LAYOUT_CONFIG.capability.forces.chargeStrength))
            .force('center', (d3 as any).forceCenter(width.value / 2, height.value / 2));

        // run a fixed number of synchronous ticks to stabilise layout
        for (let i = 0; i < 300; i++) simulation.tick();
        simulation.stop();

        const pos = new Map(
            simNodes.map((n: any) => [n.id, { x: Math.round(n.x), y: Math.round(n.y) }]),
        );
        // Preserve existing node metadata (competencies, description, flags) when applying positions
        nodes.value = nodes.value.map((n: any) => {
            const p = pos.get(n.id);
            return { ...n, x: p?.x ?? n.x, y: p?.y ?? n.y } as any;
        });
    } catch (err: unknown) {
        void err;
        // if simulation fails, silently skip (fallback positions already set)
        // console.warn('[PrototypeMap] force layout failed', err)
    }
}

function buildEdgesFromItems(items: any[]) {
    const result: Edge[] = [];
    // support explicit connections list from scenario.connections
    const conns = (props as any).scenario?.connections;
    if (Array.isArray(conns) && conns.length > 0) {
        (conns as ConnectionPayload[]).forEach((c) => {
            const s = c.source ?? c.source_id ?? null;
            const t = c.target ?? c.target_id ?? null;
            if (s != null && t != null) {
                result.push({ source: Number(s), target: Number(t), isCritical: !!c.is_critical });
            }
        });
    } else {
        // if items have linked_to, connected_to, or links arrays
        items.forEach((it: any) => {
            if (Array.isArray(it.connected_to)) {
                it.connected_to.forEach((t: any) => {
                    result.push({ source: Number(it.id), target: Number(t) });
                });
            }
            if (Array.isArray(it.links)) {
                it.links.forEach((t: any) =>
                    result.push({ source: Number(it.id), target: Number(t) }),
                );
            }
            // support nested connections on item (source/target or source_id/target_id)
            if (Array.isArray(it.connections)) {
                it.connections.forEach((c: ConnectionPayload) => {
                    const s = c.source ?? c.source_id ?? null;
                    const t = c.target ?? c.target_id ?? null;
                    if (s != null && t != null)
                        result.push({ source: Number(s), target: Number(t), isCritical: !!c.is_critical });
                });
            }
        });
    }
    edges.value = result;
}

function nodeById(id: number) {
    return nodes.value.find((n) => n.id === id) || null;
}

function childNodeById(id: number) {
    return childNodes.value.find((n) => n.id === id) || null;
}

const handleNodeClick = async (node: NodeItem, event?: MouseEvent) => {
    try {
        if ((window as any).__DEBUG__) {
            console.debug('[node.click] id=', (node as any)?.id, 'level=', nodeLevel((node as any)?.id), 'isTrusted=', !!(event as any)?.isTrusted, 'time=', Date.now());
            console.debug(new Error('click-stack').stack?.split('\n').slice(1,6).join('\n'));
        }
    } catch (err: unknown) { void err; }
    // If this is a level-2 node (competency), short-circuit: only log and do not run animations/expansions
    try {
        const lvl = nodeLevel((node as any)?.id);
        // Toggle collapse: if clicking again on an already-expanded node, collapse its children
        try {
            // If capability is focused and its competencies are shown, collapse on second click
            if ((node as any)?.id != null && (node as any).id >= 0 && focusedNode.value && focusedNode.value.id === (node as any).id && childNodes.value.length > 0) {
                closeTooltip();
                return;
            }
            // If clicking a competency and its skills are shown, collapse them
            if (lvl === 2 && selectedChild.value && selectedChild.value.id === (node as any).id && grandChildNodes.value.length > 0) {
                collapseGrandChildren();
                // keep selectedChild focused but clear skills
                return;
            }
        } catch (err: unknown) { void err; }
        if (lvl === 2) {
            try { if ((window as any).__DEBUG__) console.debug('[node.click.level2] id=', (node as any)?.id, 'level=', lvl); } catch (err: unknown) { void err; }
            try {
                noAnimations.value = true;
                setTimeout(() => { noAnimations.value = false; }, Math.max(300, TRANSITION_MS));
            } catch (err: unknown) { void err; }

            // For left-click on a competency, expand its skills (do not open modal)
            try {
                const parentEdge = childEdges.value.find((e) => e.target === (node as any).id);
                const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
                if (parentNode) {
                    const prev = focusedNode.value ? { ...focusedNode.value } : undefined;
                    // Immediately clear any existing expanded skills so they don't linger
                    try { collapseGrandChildren(); } catch (err: unknown) { void err; }
                    centerOnNode(parentNode, prev);
                    const parentLead = Math.max(0, Math.round(TRANSITION_MS * 0.6));
                    await Promise.race([waitForTransitionForNode(parentNode.id), wait(parentLead)]);
                    // Assign selectedChild BEFORE expandCompetencies so radial layout can detect it
                    selectedChild.value = node as any;
                    try {
                        if ((window as any).__DEBUG__) console.debug('[expandCompetencies.call] source=handleNodeClick, target=parentNode', { nodeId: parentNode?.id, comps: Array.isArray(parentNode?.competencies) ? parentNode!.competencies.length : 0, opts: { x: parentNode?.x ?? 0, y: parentNode?.y ?? 0 }, visualConfigLayout: props.visualConfig?.capabilityChildrenOffset, configDefault: LAYOUT_CONFIG.competency?.defaultLayout });
                    } catch (err: unknown) { void err; }
                    // If parent has 4 or more competencies, show matrix layout (matrixVariants chooses 4x2 or 5x2)
                    try {
                        const compCount = Array.isArray(parentNode?.competencies) ? parentNode!.competencies.length : 0;
                        if (compCount >= 4) {
                            console.debug && console.debug('[expandCompetencies.call] source=handleNodeClick (sides)', { nodeId: parentNode?.id, comps: compCount });
                            // TEST: Force 'sides' layout to compare distribution vs radial/matrix
                            expandCompetencies(parentNode as NodeItem, { x: parentNode.x ?? 0, y: parentNode.y ?? 0 }, { layout: 'sides' });
                        } else {
                            // keep positions for small counts
                            console.debug && console.debug('[expandCompetencies.call] skipped (<4 competencies)', { nodeId: parentNode?.id, comps: compCount });
                        }
                    } catch (err: unknown) {
                        expandCompetencies(parentNode as NodeItem, { x: parentNode.x ?? 0, y: parentNode.y ?? 0 });
                    }
                }

                if (parentNode) focusedNode.value = parentNode; else focusedNode.value = node as any;

                try { const cid = (selectedChild.value as any)?.id ?? (node as any)?.id; if (cid != null) showOnlySelectedAndParent(cid, true); } catch (err: unknown) { void err; }

                try {
                    const fid = (selectedChild.value as any)?.id ?? (focusedNode.value as any).id;
                    if (fid != null) {
                        childNodes.value = childNodes.value.map((ch: any) => ch.id === fid ? { ...ch, animOpacity: 1, animScale: ch.animScale ?? 1, visible: true } as any : ch);
                        const found = childNodeById(fid);
                        if (found) {
                            const others = childNodes.value.filter((ch: any) => ch.id !== fid);
                            childNodes.value = others.concat(childNodes.value.filter((ch: any) => ch.id === fid));
                        }
                    }
                } catch (err: unknown) { void err; }

                // expand skills for this competency
                try {
                    const comp = selectedChild.value as any;
                    const compId = comp.compId ?? comp.raw?.id ?? Math.abs(comp.id || 0);
                    const existingSkills = Array.isArray(comp.skills) ? comp.skills : (comp.raw?.skills ?? []);
                    if ((existingSkills && existingSkills.length > 0) || compId) {
                            if (existingSkills && existingSkills.length > 0) {
                            try {
                            // Ensure parent finished its CSS transition and DOM reflects final position
                            if (parentNode && parentNode.id != null) {
                                const pid = parentNode.id;
                                await waitForTransitionForNode(pid);
                                await wait(10);
                                const domPos = getNodeMapCenter(pid);
                                const renderedPos = renderedNodeById(pid) ?? { x: parentNode?.x ?? 0, y: parentNode?.y ?? 0 };
                                console.debug && console.debug('[expandSkills.debug] parentId=', pid, 'domPos=', domPos, 'renderedPos=', renderedPos, 'modelPos=', { x: parentNode?.x ?? 0, y: parentNode?.y ?? 0 });
                                // Prefer rendered X (align with render pipeline) but DOM Y (visual position)
                                const preferred = (renderedPos && domPos) ? { x: renderedPos.x, y: domPos.y } : (renderedPos ?? domPos);
                                expandSkills(comp, preferred);
                            } else {
                                // fallback when parentNode is not available
                                expandSkills(comp, { x: parentNode?.x ?? 0, y: parentNode?.y ?? 0 });
                            }
                            } catch (err: unknown) {
                            expandSkills(comp, { x: parentNode?.x ?? 0, y: parentNode?.y ?? 0 });
                            }
                        } else if (compId) {
                            try {
                                const skills = await fetchSkillsForCompetency(Number(compId));
                                try { (selectedChild.value as any).skills = Array.isArray(skills) ? skills : []; } catch (err: unknown) { void err; }
                                try {
                                    // capture parent id before awaiting so TypeScript knows it can't become null across await
                                    const pid = parentNode && parentNode.id != null ? parentNode.id : null;
                                    if (pid == null) throw new Error('missing parent id');
                                    await waitForTransitionForNode(pid);
                                    await wait(40);
                                    const domPos = getNodeMapCenter(pid);
                                    const renderedPos = renderedNodeById(pid) ?? { x: parentNode?.x, y: parentNode?.y };
                                    console.debug && console.debug('[expandSkills.debug] parentId=', pid, 'domPos=', domPos, 'renderedPos=', renderedPos, 'modelPos=', { x: parentNode?.x, y: parentNode?.y });
                                    // Prefer rendered X (align with render pipeline) but DOM Y (visual position)
                                    const preferred = (renderedPos && domPos) ? { x: renderedPos.x, y: domPos.y } : (renderedPos ?? domPos);
                                    expandSkills(selectedChild.value, preferred);
                                } catch (err: unknown) {
                                    expandSkills(selectedChild.value, { x: parentNode?.x ?? 0, y: parentNode?.y ?? 0 });
                                }
                            } catch (err: unknown) { void err; }
                        }
                    }
                } catch (err: unknown) { void err; }
            } catch (err: unknown) { void err; }

            return;
        }
    } catch (err: unknown) { void err; }
    // debounce/guard: avoid processing duplicate rapid calls for the same node
    try {
        const now = Date.now();
        if ((window as any).__lastClick && (window as any).__lastClick.id === (node as any)?.id && now - (window as any).__lastClick.time < 300) {
            if ((window as any).__DEBUG__) console.debug('[node.click] ignored duplicate click id=', (node as any)?.id);
            return;
        }
        (window as any).__lastClick = { id: (node as any)?.id, time: now };
    } catch (err: unknown) { void err; }

    // capture previous focused node so we can swap positions when appropriate
    const prev = focusedNode.value ? { ...focusedNode.value } : undefined;
    let updated: NodeItem | null = null;

    // If the clicked node is a child (id < 0), first locate its parent and center on the parent
    if (node && node.id != null && node.id < 0) {
        // find parent edge linking to this child
        const parentEdge = childEdges.value.find((e) => e.target === node.id);
        const parentNode = parentEdge ? nodeById(parentEdge.source) : null;
        if (parentNode) {
            // If this clicked node is at depth 2 (competency under capability), hide all level-1 nodes
            try {
                const lvl = nodeLevel((node as any).id);
                if (lvl === 2) {
                    try { noAnimations.value = true; } catch (err: unknown) { void err; }
                    const parentId = parentNode.id;
                    nodes.value = nodes.value.map((n: any) => {
                        if (n.id === parentId) return { ...n, visible: true };
                        return { ...n, visible: false };
                    });
                    // advance to display:none shortly to free space (keep for compatibility with any code reading __displayNone)
                    setTimeout(() => {
                        nodes.value = nodes.value.map((n: any) => (n.id === parentId ? { ...n, visible: true } : { ...n, visible: false }));
                    }, Math.max(40, TRANSITION_MS - 120));
                }
            } catch (err: unknown) {
                void err;
            }
            // capture parent's previous position so children can animate from there in sync
            const parentPrevPos = { x: parentNode.x ?? 0, y: parentNode.y ?? 0 };
            // center parent first so child positions are computed relative to it
            centerOnNode(parentNode, prev);
            // start expanding children slightly before the parent's full transition ends
            // (race between transitionend and a lead timeout = 60% of TRANSITION_MS)
            const parentLead = Math.max(0, Math.round(TRANSITION_MS * 0.6));
            await Promise.race([waitForTransitionForNode(parentNode.id), wait(parentLead)]);
            // rebuild children under the parent (positions will use updated parent coords)
            const updatedParent = nodeById(parentNode.id) || parentNode;
            try {
                if ((window as any).__DEBUG__) console.debug('[expandCompetencies.call] source=childClick, target=updatedParent', { nodeId: updatedParent?.id, comps: Array.isArray(updatedParent?.competencies) ? updatedParent!.competencies.length : 0, opts: parentPrevPos, visualConfigLayout: props.visualConfig?.capabilityChildrenOffset, configDefault: LAYOUT_CONFIG.competency?.defaultLayout });
            } catch (err: unknown) { void err; }
            try {
                const compCount = Array.isArray(updatedParent?.competencies) ? updatedParent!.competencies.length : 0;
                if (compCount >= 4) {
                    // for 4..8 -> 4x2, 9..maxDisplay -> 5x2 (handled inside expandCompetencies via matrixVariants)
                    expandCompetencies(updatedParent as NodeItem, parentPrevPos, { layout: 'matrix' });
                } else {
                    // keep default behavior for small counts to preserve positions
                    expandCompetencies(updatedParent as NodeItem, parentPrevPos);
                }
            } catch (err: unknown) {
                expandCompetencies(updatedParent as NodeItem, parentPrevPos);
            }
            // find the freshly created child node by id
            const freshChild = childNodeById(node.id);
            // set selected child for sidebar and keep focusedNode as parent so layout remains stable
            selectedChild.value = freshChild || node;
            focusedNode.value = parentNode;
            // load related skills for the selected competency (if any)
            try {
                const comp = selectedChild.value as any;
                const compId = comp?.compId ?? comp?.raw?.id ?? Math.abs(comp?.id || 0);
                if (compId) {
                    const skills = await fetchSkillsForCompetency(Number(compId));
                                try { (selectedChild.value as any).skills = Array.isArray(skills) ? skills : []; } catch (err: unknown) { void err; }
                }
                            } catch (err: unknown) { void err; }
            // hide all except selected competency and its parent
            try {
                const cid = (selectedChild.value as any)?.id ?? (node as any)?.id;
                if (cid != null) showOnlySelectedAndParent(cid, true);
                } catch (err: unknown) { void err; }
            // Ensure the selected child remains visible (do not let it disappear)
            try {
                const fid = (selectedChild.value as any)?.id ?? (focusedNode.value as any).id;
                if (fid != null) {
                    // make any existing rendered child visible and bring to front
                    childNodes.value = childNodes.value.map((ch: any) => {
                        if (ch.id === fid) return { ...ch, animOpacity: 1, animScale: ch.animScale ?? 1, visible: true } as any;
                        return ch;
                    });
                    const found = childNodeById(fid);
                    if (found) {
                        const others = childNodes.value.filter((ch: any) => ch.id !== fid);
                        childNodes.value = others.concat(childNodes.value.filter((ch: any) => ch.id === fid));
                    }
                }
            } catch (err: unknown) {
                void err;
            }
            // set updated reference for later use
            updated = (freshChild as NodeItem) || updatedParent || nodeById(node.id) || node;
            // if the child itself has inner skills/competencies, expand them now
            if (freshChild && ((freshChild as any).skills || (freshChild as any).competencies)) {
                // expand grandchildren under the child. Start them slightly before the child
                // finishes by racing the child's transitionend with a short lead timeout.
                const childId = (freshChild as NodeItem).id;
                const childLead = Math.max(0, Math.round(TRANSITION_MS * 0.5));
                await Promise.race([waitForTransitionForNode(childId), wait(childLead)]);
                try {
                    if ((window as any).__DEBUG__) console.debug('[expandCompetencies.call] source=childClick.grandchildren, target=freshChild', { nodeId: freshChild?.id, comps: Array.isArray(freshChild?.competencies) ? freshChild!.competencies.length : 0, opts: parentPrevPos, visualConfigLayout: props.visualConfig?.capabilityChildrenOffset, configDefault: LAYOUT_CONFIG.competency?.defaultLayout });
                } catch (err: unknown) { void err; }
                    try {
                        const compCount = Array.isArray(freshChild?.competencies) ? freshChild!.competencies.length : 0;
                        if (compCount >= 4) {
                            expandCompetencies(freshChild as NodeItem, parentPrevPos, { layout: 'matrix' });
                        } else {
                            expandCompetencies(freshChild as NodeItem, parentPrevPos);
                        }
                    } catch (err: unknown) {
                        expandCompetencies(freshChild as NodeItem, parentPrevPos);
                    }
            }
        } else {
            // fallback: treat as normal node click
            focusedNode.value = node;
            const nodePrevPos = { x: node.x ?? 0, y: node.y ?? 0 };
            centerOnNode(node, prev);
            // start expanding a bit earlier: race transitionend with a lead timeout
            const nodeLead = Math.max(0, Math.round(TRANSITION_MS * 0.6));
            await Promise.race([waitForTransitionForNode(node.id), wait(nodeLead)]);
            updated = nodeById(node.id) || node;
            try {
                if ((window as any).__DEBUG__) console.debug('[expandCompetencies.call] source=childClick.fallback, target=updated', { nodeId: updated?.id, comps: Array.isArray(updated?.competencies) ? updated!.competencies.length : 0, opts: nodePrevPos, visualConfigLayout: props.visualConfig?.capabilityChildrenOffset, configDefault: LAYOUT_CONFIG.competency?.defaultLayout });
            } catch (err: unknown) { void err; }
            expandCompetencies(updated as NodeItem, nodePrevPos);
        }
    } else {
        // normal capability node click
            focusedNode.value = node;
            // first reposition nodes so focused node is centered and others snap aside
            const nodePrevPos = { x: node.x ?? 0, y: node.y ?? 0 };
            centerOnNode(node, prev);
            // ensure selected node is horizontally centered and vertically at 25% of canvas
            try {
                const selected = nodeById(node.id) || node;
                const centerX = Math.round(width.value / 2);
                const targetY = Math.round(height.value * 0.25);
                // apply desired coordinates (will animate via CSS transitions)
                selected.x = centerX;
                selected.y = targetY;
                // hide non-selected level-1 nodes but keep scenario node visible
                const scenarioId = scenarioNode.value?.id ?? null;
                nodes.value = nodes.value.map((n: any) => {
                    if (n.id === selected.id || n.id === scenarioId) return { ...n, visible: true };
                    return { ...n, visible: false };
                });
                // advance to display:none shortly after to free layout space (maintain timing behavior)
                setTimeout(() => {
                    nodes.value = nodes.value.map((n: any) => (n.id === selected.id || n.id === scenarioId ? { ...n, visible: true } : { ...n, visible: false }));
                }, Math.max(40, TRANSITION_MS - 120));
                // start expanding a bit earlier: race transitionend with a lead timeout
                const centeredLead = Math.max(0, Math.round(TRANSITION_MS * 0.6));
                await Promise.race([waitForTransitionForNode(selected.id), wait(centeredLead)]);
                // then expand competencies using the updated focused node coordinates (limit to 10 in 2x5)
                updated = nodeById(selected.id) || selected;
                try {
                    if ((window as any).__DEBUG__) console.debug('[expandCompetencies.call] source=capabilityClick, target=updated', { nodeId: updated?.id, comps: Array.isArray(updated?.competencies) ? updated!.competencies.length : 0, opts: { limit: 10, rows: 2, cols: 5 }, visualConfigLayout: props.visualConfig?.capabilityChildrenOffset, configDefault: LAYOUT_CONFIG.competency?.defaultLayout });
                } catch (err: unknown) { void err; }
                expandCompetencies(updated as NodeItem, nodePrevPos, { limit: 10, rows: 2, cols: 5 });
            } catch (err: unknown) {
                // fallback: original flow
                const centeredLead = Math.max(0, Math.round(TRANSITION_MS * 0.6));
                await Promise.race([waitForTransitionForNode(node.id), wait(centeredLead)]);
                updated = nodeById(node.id) || node;
                try {
                    if ((window as any).__DEBUG__) console.debug('[expandCompetencies.call] source=capabilityClick.fallback, target=updated', { nodeId: updated?.id, comps: Array.isArray(updated?.competencies) ? updated!.competencies.length : 0, opts: nodePrevPos, visualConfigLayout: props.visualConfig?.capabilityChildrenOffset, configDefault: LAYOUT_CONFIG.competency?.defaultLayout });
                } catch (err: unknown) { void err; }
                expandCompetencies(updated as NodeItem, nodePrevPos);
            }
    }

    // If we are NOT in true fullscreen mode, fix panel to top-left corner
    const inTrueFullscreen = !!document.fullscreenElement;
    if (!inTrueFullscreen) {
        tooltipX.value = 24;
        tooltipY.value = 24;
    } else if (event) {
        // when in fullscreen, use event coords
        tooltipX.value = event.clientX + 12;
        tooltipY.value = event.clientY + 12;
    } else {
        // fallback: derive from node position
        tooltipX.value = (node.x ?? 0) + 12;
        tooltipY.value = (node.y ?? 0) + 12;
    }

    // debug coords removed
    // If followScenario is enabled, move scenarioNode to follow the clicked node (smoothly)
    try {
        if (followScenario.value && scenarioNode.value && (updated || node)) {
            // position scenario node slightly above the clicked node to act as origin
            const offsetY = props.visualConfig?.scenarioOffset ?? 80;
            const refNode = updated || node;
            scenarioNode.value.x = (refNode.x ?? 0);
            scenarioNode.value.y = Math.round((refNode.y ?? 0) - offsetY);
        }
    } catch (err: unknown) {
        void err;
        // ignore
    }
};

const closeTooltip = () => {
    focusedNode.value = null;
    childNodes.value = [];
    childEdges.value = [];
    // animate collapse of skills when tooltip closes
    try { collapseGrandChildren(); } catch (err: unknown) { void err; }
    selectedChild.value = null;
    // restore original node positions if we have them
    if (originalPositions.value && originalPositions.value.size > 0) {
        nodes.value = nodes.value.map((n) => {
            const p = originalPositions.value.get(n.id);
            if (p) return { ...n, x: p.x, y: p.y } as any;
            return n;
        });
        // restore scenario node
        if (scenarioNode.value) {
            const sp = originalPositions.value.get(scenarioNode.value.id);
            if (sp) {
                scenarioNode.value.x = sp.x;
                scenarioNode.value.y = sp.y;
            }
        }
        originalPositions.value.clear();
    }
    // reset pan to default (kept for compatibility)
    viewX.value = 0;
    viewY.value = 0;
};

// panel drag (make the glass panel movable)
const panelDragging = ref(false);
const panelDragOffset = ref({ x: 0, y: 0 });

// sidebar collapsed state: false -> visible (anchored), true -> collapsed (narrow)
const nodeSidebarCollapsed = ref(false);

function toggleNodeSidebarCollapse() {
    nodeSidebarCollapsed.value = !nodeSidebarCollapsed.value;
}

// sidebar theme: 'light' | 'dark'
const sidebarTheme = ref<'light' | 'dark'>('light');

const dialogThemeClass = computed(() => (sidebarTheme.value === 'dark' ? 'dialog-dark' : 'dialog-light'));

function toggleSidebarTheme() {
    sidebarTheme.value = sidebarTheme.value === 'light' ? 'dark' : 'light';
}

// form ref for capability/capacity modal
const capForm = ref(null as any);

// best-effort computed: true when form is present and invalid
const capFormInvalid = computed(() => {
    try {
        const f = capForm.value as any;
        if (!f) return false;
        if (typeof f.valid === 'boolean') return !f.valid;
        if (typeof f.isValid === 'boolean') return !f.isValid;
        return false;
    } catch (err: unknown) {
        return false;
    }
});

// visible when not collapsed; when collapsed the sidebar shows a narrow tab
const nodeSidebarVisible = computed(() => {
    return !nodeSidebarCollapsed.value;
});

// helper to avoid referencing `document` from the template context (template type-checker complains)
// (we no longer keep a local CSS-only fullscreen state)

const panelStyle = computed<CSSProperties>(() => {
    // compute width depending on collapsed state and fullscreen
    const inTrueFullscreen = !!document.fullscreenElement;
    const widthPx = nodeSidebarCollapsed.value ? 56 : 360;

    // If document is in true Fullscreen API, keep the panel fixed
    if (inTrueFullscreen) {
        return {
            position: 'fixed',
            right: '0px',
            top: '0px',
            height: '100vh',
            width: `${widthPx}px`,
            zIndex: 100000,
            overflow: 'auto',
        } as CSSProperties;
    }

    // Normal mode: provide width so layout can shift; CSS handles positioning
    return {
        width: `${widthPx}px`,
    } as CSSProperties;
});

function startPanelDrag(e: PointerEvent) {
    panelDragging.value = true;
    panelDragOffset.value.x = e.clientX - (tooltipX.value || 0);
    panelDragOffset.value.y = e.clientY - (tooltipY.value || 0);
    window.addEventListener('pointermove', onPanelPointerMove);
    window.addEventListener('pointerup', onPanelPointerUp);
}

function onPanelPointerMove(e: PointerEvent) {
    if (!panelDragging.value) return;
    const proposedX = Math.round(e.clientX - panelDragOffset.value.x);
    const proposedY = Math.round(e.clientY - panelDragOffset.value.y);
    // attempt to clamp within mapRoot bounds (if available)
    const mapEl = mapRoot.value as HTMLElement | null;
    const panelEl = document.querySelector('.glass-panel-strong') as HTMLElement | null;
    if (panelEl) {
        const panelRect = panelEl.getBoundingClientRect();
        if (document.fullscreenElement) {
            // clamp against viewport when in fullscreen
            const minX = 8;
            const maxX = Math.round(window.innerWidth - panelRect.width - 8);
            const minY = 8;
            const maxY = Math.round(window.innerHeight - panelRect.height - 8);
            tooltipX.value = Math.min(Math.max(proposedX, minX), Math.max(minX, maxX));
            tooltipY.value = Math.min(Math.max(proposedY, minY), Math.max(minY, maxY));
            return;
        }
        if (mapEl) {
            const mapRect = mapEl.getBoundingClientRect();
            const minX = Math.round(mapRect.left + 8); // small padding
            const maxX = Math.round(mapRect.right - panelRect.width - 8);
            const minY = Math.round(mapRect.top + 8);
            const maxY = Math.round(mapRect.bottom - panelRect.height - 8);
            tooltipX.value = Math.min(Math.max(proposedX, minX), Math.max(minX, maxX));
            tooltipY.value = Math.min(Math.max(proposedY, minY), Math.max(minY, maxY));
            return;
        }
    }
    tooltipX.value = proposedX;
    tooltipY.value = proposedY;
}

function onPanelPointerUp() {
    panelDragging.value = false;
    window.removeEventListener('pointermove', onPanelPointerMove);
    window.removeEventListener('pointerup', onPanelPointerUp);
}

// Handle clicks on an individual skill node (optional: open a small detail or select)
function handleSkillClick(skill: any, event?: MouseEvent) {
    try {
        // Find parent competency by edge and set it as selected
        const parentEdge = grandChildEdges.value.find((e) => e.target === skill.id);
        const parentComp = parentEdge ? childNodeById(parentEdge.source) : null;
        if (parentComp) {
            selectedChild.value = parentComp as any;
            // optionally focus the parent capability as well
            const parentOfCompEdge = childEdges.value.find((e) => e.target === parentComp.id);
            if (parentOfCompEdge) focusedNode.value = nodeById(parentOfCompEdge.source);
        }
        // Open a skill-detail modal showing attributes for the clicked skill
        try {
            selectedSkillDetail.value = (skill && (skill.raw ?? skill)) || skill;
            // populate editable refs from selectedSkillDetail
            const s = selectedSkillDetail.value || {};
            skillEditName.value = s.name ?? '';
            skillEditDescription.value = s.description ?? '';
            skillEditCategory.value = s.category ?? skillEditCategory.value;
            skillEditComplexityLevel.value = s.complexity_level ?? skillEditComplexityLevel.value;
            skillEditScopeType.value = s.scope_type ?? skillEditScopeType.value;
            skillEditDomainTag.value = s.domain_tag ?? '';
            skillEditIsCritical.value = !!s.is_critical;

            // Try to populate pivot values (capability_competencies) from selectedChild/focusedNode
            try {
                const comp = selectedChild.value as any;
                const parentCap = focusedNode.value as any;
                if (comp && parentCap && props.scenario && props.scenario.id) {
                    // attempt to find pivot in comp.raw.pivot or in parentCap.raw.scenario_capabilities
                    let pivot: any = comp.raw?.pivot ?? comp.raw?.capability_pivot ?? null;
                    if (!pivot && Array.isArray(parentCap?.raw?.scenario_capabilities)) {
                        pivot = parentCap.raw.scenario_capabilities.find((p: any) => Number(p.competency_id ?? p.id ?? 0) === Number(comp.compId ?? comp.raw?.id ?? Math.abs(comp.id || 0)));
                    }
                    if (pivot) {
                        skillPivotRequiredLevel.value = pivot.required_level ?? pivot.required ?? skillPivotRequiredLevel.value;
                        skillPivotPriority.value = pivot.priority ?? skillPivotPriority.value;
                        skillPivotWeight.value = pivot.strategic_weight ?? pivot.weight ?? skillPivotWeight.value;
                        skillPivotRationale.value = pivot.rationale ?? '';
                        skillPivotIsRequired.value = !!(pivot.is_required ?? pivot.is_critical ?? false);
                    } else {
                        // fallback to empty defaults
                        skillPivotRequiredLevel.value = undefined;
                        skillPivotPriority.value = undefined;
                        skillPivotWeight.value = undefined;
                        skillPivotRationale.value = '';
                        skillPivotIsRequired.value = false;
                    }
                }
            } catch (errP: unknown) { void errP; }

            skillDetailDialogVisible.value = true;
        } catch (errInner: unknown) { void errInner; }
            } catch (err: unknown) { void err; }
}

// Save skill edits + optional pivot edits
async function saveSkillDetail() {
    // Debug: trace invocation and key state to help diagnose "botón no hace nada" en runtime
    try {
        console.debug('[saveSkillDetail] invoked', {
            selectedSkillDetail: selectedSkillDetail?.value ?? null,
            skillEditName: skillEditName?.value ?? null,
            selectedChild: selectedChild?.value ?? null,
            focusedNode: focusedNode?.value ?? null,
        });
    } catch (errDbg: unknown) { void errDbg; }

    if (!selectedSkillDetail.value) return showError('No hay skill seleccionada');
    const skillId = selectedSkillDetail.value.id ?? null;
    if (!skillId) return showError('Skill no tiene id');
    savingSkillDetail.value = true;
    try {
        const skillPayload: any = {
            name: skillEditName.value?.trim() ?? undefined,
            description: skillEditDescription.value?.trim() ?? undefined,
            category: skillEditCategory.value,
            complexity_level: skillEditComplexityLevel.value,
            scope_type: skillEditScopeType.value,
            domain_tag: skillEditDomainTag.value?.trim() ?? undefined,
            is_critical: !!skillEditIsCritical.value,
        };
        // remove undefined keys
        Object.keys(skillPayload).forEach((k) => skillPayload[k] === undefined && delete skillPayload[k]);
        // update skill entity
        try {
            // Ensure CSRF cookie (Sanctum) is present before mutating requests
            try { await ensureCsrf(); } catch (e) { /* proceed, server may not require it */ }
            await api.patch(`/api/skills/${skillId}`, skillPayload);
            showSuccess('Skill actualizada');
        } catch (err: unknown) {
            try { console.error('saveSkillDetail - skill patch error', err, (err as any)?.response?.data); } catch (e) { console.error('saveSkillDetail - skill patch error', err); }
            showError('Error guardando skill');
            throw err;
        }

        // attempt to save pivot (capability_competencies) if we can identify context
        try {
            // First try: if the skill object itself carries a competency_skills pivot (skill attached to a competency),
            // attempt to patch that pivot directly. This covers edits made from within a competency context.
            try {
                // Support both internal refs (.value) and unwrapped objects (tests may set plain values)
                const unwrappedSkill: any = (typeof selectedSkillDetail.value !== 'undefined')
                    ? (selectedSkillDetail.value ?? selectedSkillDetail)
                    : null;
                const skillPivotObj = unwrappedSkill?.pivot ?? unwrappedSkill?.raw?.pivot ?? null;
                const compSkillPivotId = skillPivotObj?.id ?? skillPivotObj?.pivot_id ?? null;
                const comp: any = (typeof selectedChild.value !== 'undefined') ? (selectedChild.value ?? selectedChild) : null;
                // Build payload using available pivot fields (weight is the primary field on competency_skills)
                if (compSkillPivotId) {
                    const csPayload: any = {};
                    if (skillPivotWeight.value !== undefined && skillPivotWeight.value !== null) csPayload.weight = skillPivotWeight.value;
                    // remove undefined
                    Object.keys(csPayload).forEach((k) => csPayload[k] === undefined && delete csPayload[k]);
                    if (Object.keys(csPayload).length > 0) {
                        try {
                            await api.patch(`/api/competency-skills/${compSkillPivotId}`, csPayload);
                            showSuccess('Atributos de relación skill-competencia actualizados');
                        } catch (errCs: unknown) {
                            console.error('saveSkillDetail - competency_skills patch failed', errCs);
                            // don't throw here; continue to other pivot attempts
                        }
                    }
                }
            } catch (errCsAll: unknown) { void errCsAll; }

                // Ensure we unwrap refs to actual objects (avoid returning the Ref itself)
            const comp2: any = (selectedChild.value && typeof selectedChild.value === 'object') ? (selectedChild.value ?? null) : null;
            const parentCap: any = (focusedNode.value && typeof focusedNode.value === 'object') ? (focusedNode.value ?? null) : null;
            const scenarioId = props.scenario?.id ?? null;
            const compId = comp2?.compId ?? comp2?.raw?.id ?? Math.abs(comp2?.id || 0);
            const capId = parentCap?.id ?? parentCap?.raw?.id ?? null;
            if (scenarioId && capId && compId) {
                const pivotPayload: any = {
                    required_level: skillPivotRequiredLevel.value,
                    priority: skillPivotPriority.value,
                    strategic_weight: skillPivotWeight.value,
                    weight: skillPivotWeight.value,
                    rationale: skillPivotRationale.value?.trim() ?? undefined,
                    is_required: !!skillPivotIsRequired.value,
                };
                Object.keys(pivotPayload).forEach((k) => pivotPayload[k] === undefined && delete pivotPayload[k]);
                if (Object.keys(pivotPayload).length > 0) {
                    // Primary endpoint: scenario-scoped capability->competency update
                    try {
                        await api.patch(`/api/strategic-planning/scenarios/${scenarioId}/capabilities/${capId}/competencies/${compId}`, pivotPayload);
                        showSuccess('Atributos de competencia actualizados');
                    } catch (errPrimary: unknown) {
                        // fallback: try pivot-specific endpoint if pivot had id
                        const pivotId = comp2?.raw?.pivot?.id ?? comp2?.raw?.capability_pivot?.id ?? null;
                        if (pivotId) {
                            try {
                                await api.patch(`/api/capability-competencies/${pivotId}`, pivotPayload);
                                showSuccess('Atributos de competencia actualizados (pivot)');
                            } catch (errPivot: unknown) {
                                console.error('saveSkillDetail - pivot patch fallback failed', errPivot);
                                showError('Error guardando atributos de competencia');
                            }
                        } else {
                            console.error('saveSkillDetail - pivot patch primary failed', errPrimary);
                            showError('Error guardando atributos de competencia');
                        }
                    }
                }
            }
        } catch (errPivotAll: unknown) { void errPivotAll; }

        // Save references to current context before reload
        const currentCompId = selectedChild.value?.compId ?? selectedChild.value?.id ?? selectedChild.value?.raw?.id ?? null;
        const currentCapId = focusedNode.value?.id ?? focusedNode.value?.raw?.id ?? null;
        const currentLayout = (selectedChild.value as any)?.skillsLayout ?? 'auto';

        // Refresh skill entity from API (authoritative source)
        let freshSkill: any = null;
        try {
            const skillResp: any = await api.get(`/api/skills/${skillId}`);
            freshSkill = skillResp?.data ?? skillResp;
        } catch (errRef: unknown) { 
            console.error('Failed to refresh skill after save', errRef);
        }

        // Reload the tree from API to get fresh data
        await loadTreeFromApi(props.scenario?.id);

        // After reload, restore the capability (focusedNode) and competency (selectedChild)
        if (currentCapId) {
            const restoredCap = nodeById(currentCapId);
            if (restoredCap) {
                focusedNode.value = restoredCap;
                
                // Re-expand competencies under this capability
                expandCompetencies(restoredCap, { x: restoredCap.x ?? 0, y: restoredCap.y ?? 0 });
                
                // Restore selectedChild from the newly expanded childNodes
                if (currentCompId) {
                    const restoredComp = childNodes.value.find((cn: any) => 
                        cn.id === currentCompId || cn.compId === currentCompId
                    );
                    if (restoredComp) {
                        selectedChild.value = restoredComp;

                        // Update the skill in the competency's skills array
                        if (freshSkill && Array.isArray((selectedChild.value as any).skills)) {
                            const skillIndex = (selectedChild.value as any).skills.findIndex((s: any) => 
                                (s.id ?? s.raw?.id) === skillId
                            );
                            if (skillIndex !== -1) {
                                const existingPivot = (selectedChild.value as any).skills[skillIndex].pivot 
                                    ?? (selectedChild.value as any).skills[skillIndex].raw?.pivot 
                                    ?? null;
                                (selectedChild.value as any).skills[skillIndex] = {
                                    ...freshSkill,
                                    pivot: existingPivot,
                                    raw: { ...freshSkill, pivot: existingPivot }
                                };
                            }
                        }

                        // Re-expand skills to show updated data with proper layout
                        expandSkills(selectedChild.value, undefined, { layout: currentLayout });
                    }
                }
            }
        }

        // Update selectedSkillDetail with fresh data
        if (freshSkill) {
            selectedSkillDetail.value = freshSkill;
        }

        skillDetailDialogVisible.value = false;
    } catch (errAll: unknown) {
        // already reported
    } finally {
        try { console.debug('[saveSkillDetail] finished, savingSkillDetail:', savingSkillDetail.value); } catch (e) { void e; }
        savingSkillDetail.value = false;
    }
}

// Clear edit refs when dialog closes
watch(skillDetailDialogVisible, (v) => {
    if (!v) {
        selectedSkillDetail.value = null;
        skillEditName.value = '';
        skillEditDescription.value = '';
        skillEditCategory.value = 'technical';
        skillEditComplexityLevel.value = 'tactical';
        skillEditScopeType.value = 'domain';
        skillEditDomainTag.value = '';
        skillEditIsCritical.value = false;
        skillPivotRequiredLevel.value = undefined;
        skillPivotPriority.value = undefined;
        skillPivotWeight.value = undefined;
        skillPivotRationale.value = '';
        skillPivotIsRequired.value = false;
    }
});

// Expose saveSkillDetail for debugging from browser console
onMounted(() => {
    try { (window as any).__saveSkillDetail = saveSkillDetail; } catch (e) { void e; }
});
onBeforeUnmount(() => {
    try { delete (window as any).__saveSkillDetail; } catch (e) { void e; }
});

// Fullscreen toggle removed: UX disabled. We rely only on the browser Fullscreen API when used externally.

function expandCompetencies(node: NodeItem, initialParentPos?: { x: number; y: number }, opts: { limit?: number; rows?: number; cols?: number; layout?: 'auto' | 'radial' | 'matrix' | 'sides' } = {}) {
    childNodes.value = [];
    childEdges.value = [];
    const comps = (node as any).competencies ?? [];
    if (!Array.isArray(comps) || comps.length === 0) return;
    const maxDisplay = (LAYOUT_CONFIG.competency && typeof LAYOUT_CONFIG.competency.maxDisplay === 'number') ? LAYOUT_CONFIG.competency.maxDisplay : 10;
    const limit = Math.min(opts.limit ?? maxDisplay, maxDisplay);
    const toShow = comps.slice(0, limit);
    if (comps.length > maxDisplay) {
        try { showError && showError(`Solo se muestran hasta ${maxDisplay} competencias`); } catch (err: unknown) { void err; }
    }

    console.debug('[expandCompetencies] count:', toShow.length, 'selectedChild:', selectedChild.value ? 'YES' : 'NO');

    // Use matrix layout (default 2 rows x 5 cols) centered under parent's x
    const cx = node.x ?? Math.round(width.value / 2);
    // place matrix top starting approximately below parent (use parent's y + offset)
    const parentY = node.y ?? Math.round(height.value / 2);
    // Defaults for competency layout (level-1 friendly defaults)
    const DEFAULT_COMPETENCY_LAYOUT = { rows: 1, cols: 4, hSpacing: 100, vSpacing: 80 };
    // allow smaller offset for fake/temporary nodes (negative ids)
    const defaultParentOffset = (node.id != null && node.id < 0) ? 80 : (LAYOUT_CONFIG.competency?.parentOffset ?? 150);
    // priority: visualConfig override > top-level prop > competencyLayout prop > default per-node fallback
    const verticalOffset = (typeof props.visualConfig?.capabilityChildrenOffset === 'number')
        ? props.visualConfig!.capabilityChildrenOffset!
        : (typeof props.capabilityChildrenOffset === 'number' ? props.capabilityChildrenOffset : (props.competencyLayout?.parentOffset ?? defaultParentOffset));
    const CHILD_DROP = props.visualConfig?.childDrop ?? props.competencyLayout?.skillDrop ?? 18;
    const topY = Math.round(parentY + verticalOffset + CHILD_DROP);

    let rows = opts.rows ?? props.competencyLayout?.rows ?? DEFAULT_COMPETENCY_LAYOUT.rows;
    let cols = opts.cols ?? props.competencyLayout?.cols ?? DEFAULT_COMPETENCY_LAYOUT.cols;
    let hSpacing = props.competencyLayout?.hSpacing ?? DEFAULT_COMPETENCY_LAYOUT.hSpacing;
    let vSpacing = props.competencyLayout?.vSpacing ?? DEFAULT_COMPETENCY_LAYOUT.vSpacing;

    // Detect if there's a selected child (focusedNode is one of the competencies)
    const selectedChildCompId = selectedChild.value?.compId ?? null;
    const hasSelectedChild = selectedChildCompId !== null && toShow.some((c: any) => c.id === selectedChildCompId);

    // If count falls into configured matrixVariants, use composable to choose rows/cols
    const matrixVariants = (LAYOUT_CONFIG.competency && Array.isArray(LAYOUT_CONFIG.competency.matrixVariants)) ? LAYOUT_CONFIG.competency.matrixVariants : [];
    try {
        const variantChoice = chooseMatrixVariant(toShow.length, matrixVariants, maxDisplay);
        rows = variantChoice.rows;
        cols = variantChoice.cols;
    } catch (err: unknown) { void err; }

    // Decide layout: explicit option overrides visualConfig/layout config, 'auto' uses centralized heuristic
    const configDefaultLayout = (LAYOUT_CONFIG.competency && LAYOUT_CONFIG.competency.defaultLayout) ? LAYOUT_CONFIG.competency.defaultLayout : 'auto';
    // Use provided option or fallback to the centralized default; avoid referencing a non-existent prop
    const layout = decideCompetencyLayout(opts.layout, hasSelectedChild, toShow.length, configDefaultLayout);
    console.debug('[expandCompetencies] hasSelectedChild:', hasSelectedChild, 'layout:', layout);

    let positions: any[] = [];
    if (layout === 'radial') {
        console.debug('[expandCompetencies] Using RADIAL layout');
        // Radial layout: selected in center, others distributed around (avoiding top where parent is)
        const selectedIdx = toShow.findIndex((c: any) => c.id === selectedChildCompId);
        const radius = LAYOUT_CONFIG.competency.radial.radius;
        const selectedOffsetY = LAYOUT_CONFIG.competency.radial.selectedOffsetY;
        const otherCount = toShow.length - 1; // number of non-selected nodes
        
        // Distribute nodes in lower semicircle + sides (from startAngle to endAngle)
        const startAngle = LAYOUT_CONFIG.competency.radial.startAngle;
        const endAngle = LAYOUT_CONFIG.competency.radial.endAngle;
        const angleRange = endAngle - startAngle;
        const angleStep = angleRange / otherCount;

        positions = toShow.map((c: any, i: number) => {
            if (i === selectedIdx) {
                // Selected node stays in center, offset down to leave room for skills
                return { x: cx, y: topY + selectedOffsetY };
            }
            // Others positioned radially, avoiding top
            const otherIdx = i > selectedIdx ? i - 1 : i;
            const angle = startAngle + otherIdx * angleStep;
            const x = Math.round(cx + radius * Math.cos(angle));
            const y = Math.round(topY + radius * Math.sin(angle));
            return { x, y };
        });
    } else if (layout === 'sides') {
        console.debug('[expandCompetencies] Using SIDES layout');
        try {
            const selectedIdx = toShow.findIndex((c: any) => c.id === selectedChildCompId);
            const colOffset = Math.max(220, Math.round(hSpacing * 1.6));
            const sidesOpts = {
                hSpacing: colOffset,
                vSpacing,
                parentOffset: verticalOffset + CHILD_DROP,
                selectedOffsetMultiplier: (LAYOUT_CONFIG.competency && LAYOUT_CONFIG.competency.sides && typeof LAYOUT_CONFIG.competency.sides.selectedOffsetMultiplier === 'number') ? LAYOUT_CONFIG.competency.sides.selectedOffsetMultiplier : 0.75,
            };
            positions = computeSidesPositions(toShow.length, cx, parentY, sidesOpts, selectedIdx >= 0 ? selectedIdx : null);
        } catch (err: unknown) { console.debug('sides layout compute failed', err); positions = []; }
    } else {
        // Matrix layout for normal/default case or <5 nodes
        console.debug('[expandCompetencies] Using MATRIX layout (rows:', rows, 'cols:', cols, 'hSpacing:', hSpacing, 'vSpacing:', vSpacing, ')');
        if (toShow.length > 5) {
            // Expand spacing to avoid overlaps when no selection
            hSpacing = Math.round(hSpacing * 1.3);
            vSpacing = Math.round(vSpacing * 1.4);
            console.debug('[expandCompetencies] Expanded spacing for >5 nodes without selection');
        }
        positions = computeCompetencyMatrixPositions(toShow.length, cx, topY, rows, cols, hSpacing, vSpacing);
    }

        const builtChildren: Array<any> = [];
    toShow.forEach((c: any, i: number) => {
        const pos = positions[i] || { x: cx, y: topY };
        const id = -(node.id * 1000 + i + 1);
        const delay = Math.max(0, Math.floor(i / cols) * (LAYOUT_CONFIG.animations.competencyStaggerRow ?? 30) + (i % cols) * (LAYOUT_CONFIG.animations.competencyStaggerCol ?? 12) + Math.round((Math.random() - 0.5) * (LAYOUT_CONFIG.animations.competencyStaggerRandom ?? 30)));
        const existingPos = childNodes.value.find((ch: any) => ch.compId === (c.id ?? null));
        const child = {
            id,
            compId: c.id ?? null,
            name: c.name ?? c,
            displayName: wrapLabel(c.name ?? c, 14),
            x: (existingPos ? existingPos.x : (initialParentPos?.x ?? (node.x ?? cx))),
            y: (existingPos ? existingPos.y : (initialParentPos?.y ?? (node.y ?? parentY))),
            animScale: 0.84,
            animOpacity: 0,
            animDelay: delay,
            animFilter: 'blur(6px) drop-shadow(0 10px 18px rgba(2,6,23,0.36))',
            animTargetX: pos.x,
            animTargetY: clampY(pos.y),
            is_critical: false,
            description: c.description ?? null,
            readiness: c.readiness ?? null,
            skills: Array.isArray(c.skills) ? c.skills : [],
            raw: c,
        } as any;
        builtChildren.push(child);
        childEdges.value.push({ source: node.id, target: id });
    });

    childNodes.value = builtChildren.slice();
    nextTick(() => {
                    childNodes.value = childNodes.value.map((ch: any) => ({
            ...ch,
            x: ch.animTargetX ?? ch.x,
            y: ch.animTargetY ?? ch.y,
            animScale: 1.06,
            animOpacity: 1,
            animFilter: 'none',
        }));

        setTimeout(() => {
            childNodes.value = childNodes.value.map((ch: any) => ({ ...ch, animScale: 1 }));
            nextTick(() => {
                childNodes.value.forEach((ch: any) => { delete ch.animTargetX; delete ch.animTargetY; delete ch.animDelay; delete ch.animFilter; });
            });
        }, LAYOUT_CONFIG.animations.competencyEntryFinalize ?? 160);
    });
}

function grandChildNodeById(id: number) {
    return grandChildNodes.value.find((n) => n.id === id) || null;
}

function expandSkills(node: any, initialPos?: { x: number; y: number }, opts: { layout?: 'auto' | 'radial' | 'matrix' | 'sides' } = {}) {
    grandChildNodes.value = [];
    grandChildEdges.value = [];
    const skills = Array.isArray(node.skills) ? node.skills : (node.raw?.skills ?? []);
    if (!Array.isArray(skills) || skills.length === 0) return;
    const limit = LAYOUT_CONFIG.skill.maxDisplay;
    const toShow = skills.slice(0, limit);
    // Allow caller to provide the parent's final map coords via initialPos (from getNodeMapCenter)
    // Otherwise prefer the rendered coordinates (renderedNodeById) so radial layout matches actual render positions
    const renderedParent = renderedNodeById(node.id) ?? undefined;
    const cx = (initialPos && typeof initialPos.x === 'number')
        ? initialPos.x
        : (renderedParent && typeof renderedParent.x === 'number')
            ? renderedParent.x
            : (node.x ?? Math.round(width.value / 2));
    const parentY = (initialPos && typeof initialPos.y === 'number')
        ? initialPos.y
        : (renderedParent && typeof renderedParent.y === 'number')
            ? renderedParent.y
            : (node.y ?? Math.round(height.value / 2));
    const SKILL_PARENT_OFFSET_BASE = LAYOUT_CONFIG.skill.radial.offsetY;
    const SKILL_PARENT_OFFSET = Math.round(SKILL_PARENT_OFFSET_BASE * (1 + (LAYOUT_CONFIG.skill.radial.offsetFactor ?? 0)));
    const SKILL_DROP_EXTRA = props.visualConfig?.skillDrop ?? props.competencyLayout?.skillDrop ?? 18;
    const topY = Math.round(parentY + SKILL_PARENT_OFFSET + SKILL_DROP_EXTRA);
    
    // Decide layout for skills (support 'auto'|'radial'|'matrix'|'sides')
    const layoutOpt = opts?.layout ?? (props.visualConfig?.skillLayout as any) ?? undefined;
    const configDefaultLayout = (LAYOUT_CONFIG.skill && (LAYOUT_CONFIG.skill as any).defaultLayout) ? (LAYOUT_CONFIG.skill as any).defaultLayout : 'auto';
    const layout = decideCompetencyLayout(layoutOpt as any, false, toShow.length, configDefaultLayout as any);
    console.debug && console.debug('[expandSkills] layout:', layout, 'count:', toShow.length);

    // Compute positions according to chosen layout
    let positions: any[] = [];
    if (layout === 'radial') {
        // radial distribution
        const radius = LAYOUT_CONFIG.skill.radial.radius;
        const startAngle = LAYOUT_CONFIG.skill.radial.startAngle;
        const endAngle = LAYOUT_CONFIG.skill.radial.endAngle;
        const angleRange = endAngle - startAngle;
        const angleStep = angleRange / Math.max(1, toShow.length - 1);
        positions = toShow.map((sk: any, i: number) => {
            const angle = startAngle + i * angleStep;
            const x = Math.round(cx + radius * Math.cos(angle));
            const y = Math.round(topY + radius * Math.sin(angle));
            return { x, y };
        });
    } else if (layout === 'sides') {
        // sides layout (left/right)
        const sidesOpts = (LAYOUT_CONFIG.skill as any).sides ?? { hSpacing: 120, vSpacing: 70, parentOffset: 80, selectedOffsetMultiplier: 0.75 };
        try {
            positions = computeSidesPositions(toShow.length, cx, parentY, sidesOpts, null);
        } catch (err: unknown) { void err; positions = []; }
    } else {
        // matrix / linear layout (use matrixVariants to choose rows/cols similar to competencies)
        const matrixVariants = (LAYOUT_CONFIG.skill && (LAYOUT_CONFIG.skill as any).matrixVariants) ? (LAYOUT_CONFIG.skill as any).matrixVariants : (LAYOUT_CONFIG.competency && (LAYOUT_CONFIG.competency as any).matrixVariants) ? (LAYOUT_CONFIG.competency as any).matrixVariants : [];
        try {
            const variant = chooseMatrixVariant(toShow.length, matrixVariants, LAYOUT_CONFIG.skill.maxDisplay);
            const rows = variant.rows;
            const cols = variant.cols;
            positions = computeMatrixPositions(toShow.length, cx, topY, { rows, cols, hSpacing: LAYOUT_CONFIG.skill.linear.hSpacing, vSpacing: LAYOUT_CONFIG.skill.linear.vSpacing });
        } catch (err: unknown) {
            const rows = 1;
            const cols = Math.min(4, toShow.length);
            positions = computeMatrixPositions(toShow.length, cx, topY, { rows, cols, hSpacing: LAYOUT_CONFIG.skill.linear.hSpacing, vSpacing: LAYOUT_CONFIG.skill.linear.vSpacing });
        }
    }

    const built: any[] = [];
    toShow.forEach((sk: any, i: number) => {
        const pos = positions[i] || { x: cx, y: topY };
        const id = -(Math.abs(node.id) * 100000 + i + 1);
        const delay = Math.max(0, Math.floor(i / 4) * (LAYOUT_CONFIG.animations.skillStaggerRow ?? 20) + (i % 4) * (LAYOUT_CONFIG.animations.skillStaggerCol ?? 8));
        const item = {
            id,
            name: sk.name ?? sk,
            x: initialPos?.x ?? (node.x ?? cx),
            y: initialPos?.y ?? (node.y ?? parentY),
            animScale: 0.8,
            animOpacity: 0,
            animDelay: delay,
            animTargetX: pos.x,
            animTargetY: clampY(pos.y),
            raw: sk,
        } as any;
        built.push(item);
        grandChildEdges.value.push({ source: node.id, target: id });
    });
    grandChildNodes.value = built.slice();
    nextTick(() => {
        grandChildNodes.value = grandChildNodes.value.map((g: any) => ({ ...g, x: g.animTargetX ?? g.x, y: g.animTargetY ?? g.y, animScale: 1, animOpacity: 1, animFilter: 'none' }));
        setTimeout(() => {
            grandChildNodes.value = grandChildNodes.value.map((g: any) => ({ ...g, animScale: 1 }));
            nextTick(() => { grandChildNodes.value.forEach((g: any) => { delete g.animTargetX; delete g.animTargetY; delete g.animDelay; delete g.animFilter; }); });
        }, LAYOUT_CONFIG.animations.skillEntryFinalize ?? 140);
    });
}

// Animación de colapso para nodos nietos (skills).
function collapseGrandChildren(animated = false, duration?: number) {
    try {
        duration = typeof duration === 'number' ? duration : (LAYOUT_CONFIG.animations.collapseDuration ?? 10);
        if (!animated) {
            grandChildNodes.value = [];
            grandChildEdges.value = [];
            return;
        }
        if (!Array.isArray(grandChildNodes.value) || grandChildNodes.value.length === 0) {
            grandChildEdges.value = [];
            return;
        }
        // Trigger visual departure: shrink + fade + subtle blur
        grandChildNodes.value = grandChildNodes.value.map((g: any) => ({ ...g, animScale: 0.8, animOpacity: 0, animFilter: 'blur(6px)' }));
        // fade edges (animate stroke-opacity) and clear after animation
        try {
            grandChildEdges.value = grandChildEdges.value.map((ed: any) => ({ ...ed, animOpacity: 0 }));
        } catch (err: unknown) { void err; }
        setTimeout(() => {
            try { grandChildEdges.value = []; } catch (err: unknown) { void err; }
        }, duration + 10);
        // remove nodes after animation finishes
        setTimeout(() => {
            try { grandChildNodes.value = []; } catch (err: unknown) { void err; }
        }, duration + 10);
        try { /* edges will be removed after opacity animation above */ } catch (err: unknown) { void err; }
    } catch (err: unknown) { void err; }
}

// clamp child node Y positions when setting them to avoid placing nodes outside viewport
function clampY(y: number) {
    const minY = (LAYOUT_CONFIG.clamp && typeof LAYOUT_CONFIG.clamp.minY === 'number') ? LAYOUT_CONFIG.clamp.minY : 40;
    const bottomPadding = (LAYOUT_CONFIG.clamp && typeof LAYOUT_CONFIG.clamp.bottomPadding === 'number') ? LAYOUT_CONFIG.clamp.bottomPadding : 40;
    const minViewportHeight = (LAYOUT_CONFIG.clamp && typeof LAYOUT_CONFIG.clamp.minViewportHeight === 'number') ? LAYOUT_CONFIG.clamp.minViewportHeight : 120;
    const maxY = Math.max(minViewportHeight, height.value - bottomPadding);
    return Math.min(Math.max(y, minY), maxY);
}

function startDrag(node: any, event: PointerEvent) {
    dragging.value = node;
    dragOffset.value.x = event.clientX - node.x;
    dragOffset.value.y = event.clientY - node.y;
    window.addEventListener('pointermove', onPointerMove);
    window.addEventListener('pointerup', onPointerUp);
}

// --- Competency API helpers ---
async function fetchAvailableCompetencies() {
    try {
        const res: any = await api.get('/api/competencies');
        const all = res?.data ?? res;
        // determine the node whose competencies we should consider as "attached";
        // prefer the focused node, fall back to the sidebar/display node when used from the detail panel
        const node = focusedNode.value ?? displayNode.value ?? null;
        const attached = (node as any)?.competencies?.map((c: any) => c.id) || [];
        availableExistingCompetencies.value = Array.isArray(all) ? all.filter((c: any) => !attached.includes(c.id)) : [];
    } catch (err: unknown) {
        availableExistingCompetencies.value = [];
    }
}

// Fetch skills for a competency. Tries multiple endpoint patterns and falls back gracefully.
async function fetchSkillsForCompetency(compId: number) {
    if (!compId) return [];
    loadingSkills.value = true;
    try {
        // 1) If we have a scenario context, prefer the capability-tree endpoint which includes
        //    competencies and their nested `skills` (produced by ScenarioController@getCapabilityTree)
        try {
            if (props.scenario && props.scenario.id) {
                const tree: any = await api.get(`/api/strategic-planning/scenarios/${props.scenario.id}/capability-tree`);
                const items = Array.isArray(tree) ? tree : (tree?.data ?? tree ?? []);
                for (const cap of items) {
                    if (!cap || !Array.isArray(cap.competencies)) continue;
                    for (const comp of cap.competencies) {
                        if (Number(comp.id) === Number(compId)) {
                            return Array.isArray(comp.skills) ? comp.skills : (comp?.skills || []);
                        }
                    }
                }
            }
        } catch (err: unknown) { void err; }

        // 2) Try dedicated competency endpoints (we added these routes server-side):
        try {
            const r: any = await api.get(`/api/competencies/${compId}/skills`);
            const s = r?.data ?? r;
            if (Array.isArray(s)) return s;
        } catch (err: unknown) { void err; }

        try {
            const r2: any = await api.get(`/api/competencies/${compId}`);
            const obj = r2?.data ?? r2;
            if (obj) {
                if (Array.isArray(obj.skills)) return obj.skills;
                if (Array.isArray(obj.data?.skills)) return obj.data.skills;
            }
        } catch (err: unknown) { void err; }

        // 3) Fallback: try generic skills endpoint and filter locally (best-effort)
        try {
            const res: any = await api.get('/api/skills');
            const all = Array.isArray(res) ? res : (res?.data ?? []);
            if (!Array.isArray(all)) return [];
            const filtered = all.filter((s: any) => {
                if (!s) return false;
                // If skills include nested competencies or pivot info, try to detect relation
                if (Array.isArray(s.competencies)) {
                    return s.competencies.some((c: any) => Number(c.id) === Number(compId));
                }
                if (s.pivot && (s.pivot.competency_id || s.pivot.competencyId)) {
                    return Number(s.pivot.competency_id || s.pivot.competencyId) === Number(compId);
                }
                return false;
            });
            return filtered;
        } catch (err: unknown) { void err; }

    } catch (err: unknown) { void err; }
    finally {
        loadingSkills.value = false;
    }

    return [];
}

async function createAndAttachComp() {
    // Resolve capability context robustly (avoid using a selected competency as parent)
    let parentCap: any = focusedNode.value ?? null;
    if (!parentCap && selectedChild.value) {
        const childId = (selectedChild.value as any)?.id ?? null;
        const parentEdge = childId != null ? childEdges.value.find((e) => e.target === childId) : null;
        parentCap = parentEdge ? nodeById(parentEdge.source) : null;
    }
    if (!parentCap && displayNode.value) {
        parentCap = displayNode.value as any;
    }

    const resolvedCapId = parentCap?.id ?? parentCap?.raw?.id ?? null;

    if (!resolvedCapId || Number(resolvedCapId) <= 0) {
        showError('Seleccione una capacidad para asociar');
        return;
    }
    if (!props.scenario?.id) {
        showError('Escenario no especificado');
        return;
    }
    const capId = resolvedCapId;
    const scenarioId = props.scenario.id;
    creatingComp.value = true;
    try {
        console.debug('[createAndAttachComp] start', { scenarioId, capId, name: newCompName.value });
        await ensureCsrf();
        // Single endpoint call that creates both competency and pivot record
        // The backend endpoint accepts either competency_id (existing) or competency object (create new)
        const payload = {
            competency: {
                name: newCompName.value,
                description: newCompDescription.value,
            },
            required_level: 3,
            weight: 1,
            rationale: '',
            is_required: false,
        };
        const res: any = await api.post(`/api/strategic-planning/scenarios/${scenarioId}/capabilities/${capId}/competencies`, payload);
        const result = res?.data ?? res;
        console.debug('[createAndAttachComp] success', result);

        // Refresh tree first to ensure we can resolve the newly-created competency ID
        await loadTreeFromApi(scenarioId);

        // Try to find the created competency under the parent node by matching the name
        const parent = nodeById(capId);
        let createdCompId: number | null = null;
        if (parent && Array.isArray((parent as any).competencies)) {
            const found = (parent as any).competencies.find((c: any) => {
                const n = c?.name || c?.compName || c?.raw?.name || '';
                return String(n).trim() === String(newCompName.value).trim();
            });
            if (found) {
                createdCompId = Number(found.id ?? found.compId ?? found.raw?.id ?? Math.abs(found.id || 0)) || null;
            }
        }

        // If we couldn't detect the new competency id from the refreshed tree, try common spots on the API result
        if (!createdCompId) {
            createdCompId = Number(result?.id ?? result?.data?.id ?? result?.competency?.id ?? result?.competency_id) || null;
        }

        // If the user provided comma-separated skills in the creation dialog, create and attach them
        try {
            if (newCompSkills.value && String(newCompSkills.value).trim()) {
                const skillNames = String(newCompSkills.value).split(',').map(s => s.trim()).filter(Boolean);
                for (const sName of skillNames) {
                    try {
                        if (!createdCompId) {
                            console.warn('[createAndAttachComp] no compId found; skipping skill creation for', sName);
                            continue;
                        }
                        const payload: any = { name: sName, category: 'technical' };
                        await createAndAttachSkillForComp(createdCompId, payload);
                    } catch (err: unknown) {
                        console.error('[createAndAttachComp] failed creating skill', sName, err);
                    }
                }
            }
        } catch (err: unknown) { void err; }

        createCompDialogVisible.value = false;
        // reset form fields after success
        resetCompetencyForm();
        if (parent) {
            expandCompetencies(parent as NodeItem, { x: parent.x ?? 0, y: parent.y ?? 0 });
        }
        showSuccess('Competencia creada y asociada');
    } catch (err: unknown) {
        console.error('[createAndAttachComp] error', err);
        showError('Error creando competencia');
    } finally {
        creatingComp.value = false;
    }
}

// Wrapper used by template to log clicks and call the real handler (helps debug clicks not firing)
function onClickCreateAndAttachComp() {
    try {
        console.debug('[onClickCreateAndAttachComp] click', { name: newCompName.value, cap: displayNode.value?.id });
    } catch (err) { void err; }

    // quick client-side validation to give immediate feedback
    if (!displayNode.value || !((displayNode.value as any).id)) {
        showError('Seleccione una capacidad para asociar');
        return;
    }
    if (!newCompName.value || !String(newCompName.value).trim()) {
        showError('El nombre de la competencia es obligatorio');
        return;
    }

    // ensure CSRF cookie present before mutating requests
    creatingComp.value = true;
    (async () => {
        try {
            await ensureCsrf();
            await createAndAttachComp();
        } catch (err) {
            console.error('[onClickCreateAndAttachComp] handler error', err);
            showError('Error al crear la competencia');
        } finally {
            creatingComp.value = false;
        }
    })();
}

async function attachExistingComp() {
    if (!displayNode.value || !((displayNode.value as any).id)) return showError('Seleccione una capacidad');
    if (!addExistingSelection.value) return showError('Seleccione una competencia existente');
    const capId = (displayNode.value as any).id;
        await ensureCsrf();
        try {
            await api.post(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities/${capId}/competencies`, { competency_id: addExistingSelection.value });
            addExistingCompDialogVisible.value = false;
            await loadTreeFromApi(props.scenario?.id);
            const parent = nodeById(capId);
            if (parent) expandCompetencies(parent as NodeItem, { x: parent.x ?? 0, y: parent.y ?? 0 });
            showSuccess('Competencia asociada correctamente');
        } catch (err: unknown) {
            // fallback: try capability-scoped endpoint
            try {
                await api.post(`/api/capabilities/${capId}/competencies`, { competency_id: addExistingSelection.value });
                addExistingCompDialogVisible.value = false;
                await loadTreeFromApi(props.scenario?.id);
                const parent = nodeById(capId);
                if (parent) expandCompetencies(parent as NodeItem, { x: parent.x ?? 0, y: parent.y ?? 0 });
                showSuccess('Competencia asociada correctamente');
            } catch (err2: unknown) {
                showError('Error asociando competencia');
            }
        }
}

// Save edits for selectedChild (competency and pivot)
async function saveSelectedChild() {
    const child = selectedChild.value;
    if (!child) return showError('No hay competencia seleccionada');
    await ensureCsrf();
    try {
        // Find parent capability first (needed for pivot updates)
        const parentEdge = childEdges.value.find((e) => e.target === child.id);
        const parentId = parentEdge ? parentEdge.source : null;
        const compId = child.compId ?? child.raw?.id ?? Math.abs(child.id);

        // 1) Update competency entity (name, description, skills - readiness is a calculated field, don't save)
        // Extract skill IDs from child.skills array (which contains skill objects with id property)
        const skillIds = Array.isArray(child.skills) 
            ? child.skills.map((s: any) => s.id ?? s.raw?.id ?? s).filter((id: any) => typeof id === 'number')
            : [];
        const compPayload: any = {
            name: editChildName.value,
            description: editChildDescription.value,
            skills: skillIds,
        };
        console.debug('[saveSelectedChild] compPayload (name, description, skills only; readiness is calculated)', compPayload, 'compId', compId, 'skillIds extracted from child.skills:', skillIds);
        console.debug('[saveSelectedChild] about to PATCH compId check:', !!compId);
        if (compId) {
            console.debug('[saveSelectedChild] INSIDE if (compId), about to call api.patch');
            try {
                const patchUrl = `/api/competencies/${compId}`;
                console.debug('[saveSelectedChild] calling PATCH:', patchUrl, 'with payload:', compPayload);
                const patchRes = await api.patch(patchUrl, compPayload);
                console.debug('[saveSelectedChild] PATCH /api/competencies/' + compId + ' success, response:', patchRes);
            } catch (errComp: unknown) {
                console.error('[saveSelectedChild] ERROR in PATCH /api/competencies/' + compId, (errComp as any)?.response?.data ?? errComp);
                showError('Error actualizando competencia: ' + ((errComp as any)?.response?.data?.message || (errComp as any)?.message || 'Unknown error'));
                return;
            }
        } else {
            console.warn('[saveSelectedChild] compId is falsy, skipping PATCH. child.compId=', child.compId, 'child.raw?.id=', child.raw?.id, 'child.id=', child.id);
        }

        // 2) Update pivot (capability_competencies) if we can find parent
        if (parentId && compId) {
            const pivotPayload = {
                weight: typeof editChildPivotStrategicWeight.value !== 'undefined' ? Number(editChildPivotStrategicWeight.value) : undefined,
                priority: typeof editChildPivotPriority.value !== 'undefined' ? Number(editChildPivotPriority.value) : undefined,
                required_level: typeof editChildPivotRequiredLevel.value !== 'undefined' ? Number(editChildPivotRequiredLevel.value) : undefined,
                // UI uses `is_critical` checkbox, but backend pivot expects `is_required`.
                // Send both for compatibility: primary is `is_required` so server updates expected column.
                is_required: !!editChildPivotIsCritical.value,
                is_critical: !!editChildPivotIsCritical.value,
                rationale: editChildPivotRationale.value,
            };
            console.debug('[saveSelectedChild] pivotPayload', pivotPayload, 'parentId', parentId, 'compId', compId);
            try {
                // preferred: update pivot within scenario context so we update scenario-specific attributes
                const childRes: any = await api.patch(`/api/strategic-planning/scenarios/${props.scenario?.id}/capabilities/${parentId}/competencies/${compId}`, pivotPayload);
                console.debug('[saveSelectedChild] PATCH child pivot response', childRes);
            } catch (errPivot: unknown) {
                // fallback to capability-scoped endpoint if available
                try {
                    const childRes2: any = await api.patch(`/api/capabilities/${parentId}/competencies/${compId}`, pivotPayload);
                    console.debug('[saveSelectedChild] PATCH child pivot fallback response', childRes2);
                } catch (err2: unknown) {
                    console.error('[saveSelectedChild] error updating pivot', (err2 as any)?.response?.data ?? err2);
                }
            }
        }

        // Get authoritative competency entity (to ensure name, description, readiness are fresh)
        let freshComp: any = null;
        try {
            if (compId) {
                const compResp: any = await api.get(`/api/competencies/${compId}`);
                freshComp = compResp?.data ?? compResp;
            }
        } catch (err: unknown) { void err; }

        // Refresh tree from API (may reconstruct competencies differently than the PATCH response)
        await loadTreeFromApi(props.scenario?.id);

        // After reload, merge authoritative competency fields into childNodes if we have them
        try {
            if (freshComp && typeof freshComp.id !== 'undefined' && parentId) {
                const freshCompId = freshComp.id;
                childNodes.value = childNodes.value.map((cn: any) => (cn.id === freshCompId || cn.compId === freshCompId ? { ...cn, name: freshComp.name ?? cn.name, description: freshComp.description ?? cn.description, readiness: freshComp.readiness ?? cn.readiness, raw: { ...(cn.raw ?? {}), ...(freshComp ?? {}) } } : cn));
                if (selectedChild.value && (selectedChild.value.compId === freshCompId || selectedChild.value.id === freshCompId || selectedChild.value.raw?.id === freshCompId)) {
                    selectedChild.value = { ...(selectedChild.value as any), name: freshComp.name ?? (selectedChild.value as any).name, description: freshComp.description ?? (selectedChild.value as any).description, readiness: freshComp.readiness ?? (selectedChild.value as any).readiness, raw: { ...((selectedChild.value as any).raw ?? {}), ...(freshComp ?? {}) } } as any;
                    // Re-initialize edit fields from refreshed data
                    try {
                        editChildName.value = freshComp.name ?? editChildName.value;
                        editChildDescription.value = freshComp.description ?? editChildDescription.value;
                        editChildReadiness.value = freshComp.readiness ?? editChildReadiness.value;
                    } catch (err: unknown) { void err; }
                }
            }
        } catch (err: unknown) { void err; }

        // Re-open parent expansion to show updated children
        if (parentId) {
            const parent = nodeById(parentId);
            if (parent) expandCompetencies(parent as NodeItem, { x: parent.x ?? 0, y: parent.y ?? 0 });
        }
        showSuccess('Competencia actualizada');
    } catch (err: unknown) {
        void err;
        showError('Error guardando competencia');
    }
}

function onPointerMove(e: PointerEvent) {
    if (!dragging.value) return;
    dragging.value.x = Math.round(e.clientX - dragOffset.value.x);
    dragging.value.y = Math.round(e.clientY - dragOffset.value.y);
    // mark positions as changed so they can be saved on pointer up
    positionsDirty.value = true;
}

async function onPointerUp() {
    if (dragging.value) {
        dragging.value = null;
    }
    window.removeEventListener('pointermove', onPointerMove);
    window.removeEventListener('pointerup', onPointerUp);

    // If positions changed during drag, save automatically
    if (positionsDirty.value) {
        try {
            await savePositions();
        } catch (err: unknown) {
            void err;
        }
        positionsDirty.value = false;
    }
}

const resetPositions = () => {
    // clear positions so layout recomputes
    nodes.value = nodes.value.map((n) => ({
        ...n,
        x: undefined as any,
        y: undefined as any,
    }));
    if (props.scenario && Array.isArray((props.scenario as any).capabilities)) {
        buildNodesFromItems((props.scenario as any).capabilities);
    }
};

const savePositions = async () => {
    if (!props.scenario || !props.scenario.id)
        return showError('Escenario no seleccionado');
    try {
        const payload = {
            positions: nodes.value.map((n) => ({ id: n.id, x: n.x, y: n.y })),
        };
        await api.post(
            `/api/strategic-planning/scenarios/${props.scenario.id}/capability-tree/save-positions`,
            payload,
        );
        showSuccess('Posiciones guardadas');
    } catch (err: unknown) {
        void err;
        showError('Error al guardar posiciones');
    }
};

const loadTreeFromApi = async (scenarioId?: number) => {
    if (!scenarioId) {
        nodes.value = [];
        loaded.value = true;
        return;
    }
    try {
        // fetch capability-tree for scenario
        const tree = await api.get(
            `/api/strategic-planning/scenarios/${scenarioId}/capability-tree`,
        );
        const items = (tree as any) || [];
        // keep raw payload for debugging / inspection in sidebar
        capabilityTreeRaw.value = items;
        try { console.debug('[loadTreeFromApi] fetched tree for scenario', scenarioId, 'items_count=', Array.isArray(items) ? items.length : 'na', items); } catch (err: unknown) { void err; }
        // capability-tree response received
        buildNodesFromItems(items);
        // ensure edges are rebuilt from the fetched items
        buildEdgesFromItems(items);
    } catch (err: unknown) {
        void err;
        // error loading capability-tree
        nodes.value = [];
    } finally {
        loaded.value = true;
        // ensure nodes are ordered after API load (persist layout)
        try { await reorderNodes(); } catch (err: unknown) { void err; }
    }
};

onMounted(async () => {
    // expose helper for quick debugging in browser console
    try { (window as any).__nodeLevel = nodeLevel; } catch (err: unknown) { void err; }
    // prefer passed-in scenario.capabilities to avoid extra network roundtrip
    // onMounted: handle incoming props.scenario
    if (
        props.scenario &&
        Array.isArray((props.scenario as any).capabilities) &&
        (props.scenario as any).capabilities.length > 0
    ) {
        const caps = (props.scenario as any).capabilities;
        // store incoming capabilities as raw payload for inspection
        capabilityTreeRaw.value = caps;
        // if the provided capabilities do not include scenario-scoped pivot attributes
        // (some list: strategic_weight, priority, required_level, is_critical, importance)
        // then prefer to fetch the canonical capability-tree from the API so we get the
        // scenario-specific attributes that the UI expects to display in modals.
        const first = caps[0];
        const hasPivot = !!first && (
            first.strategic_weight !== undefined ||
            first.priority !== undefined ||
            first.required_level !== undefined ||
            first.is_critical !== undefined ||
            (first.raw && (first.raw.strategic_weight !== undefined || first.raw.priority !== undefined)) ||
            (first.pivot && (first.pivot.strategic_weight !== undefined || first.pivot.priority !== undefined)) ||
            (first.scenario_capabilities && Object.keys(first.scenario_capabilities).length > 0)
        );
        if (!hasPivot) {
            // fetch canonical tree which includes pivot/entity attributes
            await loadTreeFromApi(props.scenario.id);
            // ensure positions and scenario node are initialized
            setScenarioInitial();
            try { await reorderNodes(); } catch (err: unknown) { void err; }
            return;
        }

        buildNodesFromItems(caps);
        buildEdgesFromItems(caps);
        // restore persisted UI state after nodes built
        try {
            const collapsed = localStorage.getItem(LS_KEYS.collapsed);
            if (collapsed !== null) nodeSidebarCollapsed.value = collapsed === 'true';
            const lastView = localStorage.getItem(LS_KEYS.lastView);
            const lastId = localStorage.getItem(LS_KEYS.lastFocusedId);
            if (lastId) savedFocusedNodeId.value = parseInt(lastId, 10);
            if (lastView === 'scenario' && !focusedNode.value) showSidebar.value = true;
            if (savedFocusedNodeId.value) {
                const restored = nodeById(savedFocusedNodeId.value);
                if (restored) focusedNode.value = restored;
            }
        } catch (err: unknown) {
            void err;
            // ignore storage errors
        }
        loaded.value = true;
        // ensure scenario node initialized with correct name
        setScenarioInitial();
        // reset positions to default layout on reload (user requested automatic reset)
        try {
            resetPositions();
        } catch (err: unknown) {
            void err;
        }
        // apply reorder and persist positions on initial load
        try { await reorderNodes(); } catch (err: unknown) { void err; }
        
        return;
    }
    // otherwise fetch capability tree from API
    void loadTreeFromApi(props.scenario?.id);

    // initialize responsive sizing and observe container
    const el = mapRoot.value as HTMLElement | null;
    const applySize = (w?: number, h?: number) => {
        const computedWidth = w ?? el?.clientWidth ?? 900;
        // compute available height inside the container: prefer container height if available,
        // otherwise use viewport remaining space below the container's top.
        let containerHeight = el?.clientHeight ?? 0;
        if (!containerHeight || containerHeight === 0) {
            const top = el?.getBoundingClientRect().top ?? 0;
            containerHeight = Math.max(320, Math.round(window.innerHeight - top - 24));
        }
        const controlsEl = el?.querySelector('.map-controls') as HTMLElement | null;
        const controlsH = controlsEl?.offsetHeight ?? 0;
        const computedHeight = h ?? Math.max(300, containerHeight - controlsH - 12);

        width.value = computedWidth;
        height.value = computedHeight;

        // if we have previously loaded items, rebuild positions to adapt
        if (Array.isArray(lastItems) && lastItems.length > 0) {
            buildNodesFromItems(lastItems);
            buildEdgesFromItems(lastItems);
        }
    };
    applySize();
    let ro: ResizeObserver | null = null;
    if (el && (window as any).ResizeObserver) {
        ro = new ResizeObserver((entries: any) => {
            for (const entry of entries) {
                const w = Math.round(entry.contentRect.width);
                const h = Math.round(entry.contentRect.height);
                applySize(w, h);
            }
        });
        ro.observe(el);
    }
    // fullscreen change listener removed (UI fullscreen button disabled)
    const onWindowResize = () => applySize();
    window.addEventListener('resize', onWindowResize);
    // attach scroll listener for edit form slider
    editFormScrollHandler = (ev: Event) => syncSliderFromScroll();
    // attempt to attach after a tick in case element not yet rendered
    nextTick(() => {
        if (editFormScrollEl.value) editFormScrollEl.value.addEventListener('scroll', editFormScrollHandler as EventListener);
    });
    onBeforeUnmount(() => {
        // cleanup edit form scroll listener
        if (editFormScrollEl.value && editFormScrollHandler) editFormScrollEl.value.removeEventListener('scroll', editFormScrollHandler as EventListener);
        editFormScrollHandler = null;
        if (ro) ro.disconnect();
        window.removeEventListener('resize', onWindowResize);
    });
});

// persist UI choices: collapsed, lastView, lastFocusedNodeId
watch(
    [nodeSidebarCollapsed, showSidebar, focusedNode],
    () => {
        try {
            localStorage.setItem(LS_KEYS.collapsed, nodeSidebarCollapsed.value ? 'true' : 'false');
            const lastView = focusedNode.value ? 'node' : showSidebar.value ? 'scenario' : 'none';
            localStorage.setItem(LS_KEYS.lastView, lastView);
            localStorage.setItem(LS_KEYS.lastFocusedId, focusedNode.value ? String((focusedNode.value as any).id) : '');
        } catch (err: unknown) {
            void err;
            // ignore storage errors
        }
    },
    { immediate: true },
);

// react to scenario prop updates (e.g., loaded after mount)
watch(
    () => props.scenario,
    (nv) => {
        if (!nv) return;
        if (
            Array.isArray((nv as any).capabilities) &&
            (nv as any).capabilities.length > 0
        ) {
            const caps = (nv as any).capabilities;
            buildNodesFromItems(caps);
            buildEdgesFromItems(caps);
            loaded.value = true;
            try { void reorderNodes(); } catch (err: unknown) { void err; }
        } else {
            void loadTreeFromApi((nv as any).id);
        }
    },
    { immediate: false, deep: true },
);

// keep scenario name in sync when scenario prop changes
watch(
    () => props.scenario && (props.scenario.name ?? null),
    (nv) => {
        if (scenarioNode.value) scenarioNode.value.name = nv ?? 'Escenario';
    },
);

// ensure scenarioEdges recompute whenever main nodes change (positions or list)
watch(
    () => nodes.value.map((n) => n.id),
    () => {
        if (scenarioNode.value && Array.isArray(nodes.value)) {
            scenarioEdges.value = nodes.value.map((n: any) => ({ source: scenarioNode.value!.id, target: n.id, isScenarioEdge: true } as Edge));
        }
    },
    { immediate: true },
);

// debug watch removed

// ensure edges exist even if no capabilities loaded yet (avoids template warnings)
if (!edges.value) edges.value = [];
</script>

<template>
    <div>

        <div class="prototype-map-root" ref="mapRoot" :class="{ 'no-animations': noAnimations }">
        <div
            class="map-controls"
            style="
                margin-bottom: 2px;
                display: flex;
                gap: 2px;
                align-items: center;
            "
        >
            <!-- top control removed; 'Crear capacidad' integrated next to home control -->
            <!-- título principal mostrado arriba -->
            <!-- Position controls removed: positions are saved/reset by default -->
            <!-- 'Volver a la vista inicial' integrado en la esfera del escenario y en el borde derecho del diagrama -->
                        <!-- extra soft halo/gloss to ensure bubble effect is visible on all nodes -->
                        <circle
                            class="node-gloss"
                            r="36"
                            fill="none"
                            stroke="#ffffff"
                            stroke-opacity="0.04"
                            stroke-width="6"
                            filter="url(#softGlow)"
                        />
            <!-- fullscreen button removed (disabled for now) -->
            <v-btn
                small
                :variant="followScenario ? 'tonal' : 'text'"
                :color="followScenario ? 'primary' : undefined"
                @click="followScenario = !followScenario"
                :title="followScenario ? 'Seguir origen: activado' : 'Seguir origen: desactivado'"
            >
                Seguir origen
            </v-btn>
        </div>
        <div v-if="!loaded">Cargando mapa...</div>
        <div v-else>
            <svg
                :width="width"
                :height="height"
                :viewBox="`0 0 ${width} ${height}`"
                class="map-canvas"
                style="touch-action: none"
            >
                <defs>
                    <linearGradient id="bgGrad" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#040914" stop-opacity="1" />
                        <stop offset="25%" stop-color="#071029" stop-opacity="1" />
                        <stop offset="70%" stop-color="#071a2a" stop-opacity="1" />
                        <stop offset="100%" stop-color="#051018" stop-opacity="1" />
                    </linearGradient>

                    <radialGradient id="nodeGrad" cx="30%" cy="25%" r="70%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.75" />
                        <stop offset="12%" stop-color="#e8f6ff" stop-opacity="0.55" />
                        <stop offset="45%" stop-color="#6fc3ff" stop-opacity="0.95" />
                        <stop offset="100%" stop-color="#0b66b2" stop-opacity="1" />
                    </radialGradient>
                    <!-- iridescent overlay to simulate soap-bubble sheen -->
                    <radialGradient id="iridescentGrad" cx="70%" cy="30%" r="90%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.0" />
                        <stop offset="18%" stop-color="#ffd6f7" stop-opacity="0.06" />
                        <stop offset="32%" stop-color="#d6f7ff" stop-opacity="0.07" />
                        <stop offset="48%" stop-color="#fff2d6" stop-opacity="0.06" />
                        <stop offset="68%" stop-color="#d6fff3" stop-opacity="0.05" />
                        <stop offset="100%" stop-color="#ffffff" stop-opacity="0.0" />
                    </radialGradient>
                    <!-- bubble outer gradient: darker near rim, lighter inward to simulate inner glow -->
                    <radialGradient id="bubbleOuterGrad" cx="50%" cy="50%" r="80%">
                        <stop offset="0%" stop-color="#0b66b2" stop-opacity="0.06" />
                        <stop offset="60%" stop-color="#6fc3ff" stop-opacity="0.18" />
                        <stop offset="85%" stop-color="#6fc3ff" stop-opacity="0.06" />
                        <stop offset="100%" stop-color="#0b66b2" stop-opacity="0.02" />
                    </radialGradient>

                    <!-- core gradient: small bright core to suggest nucleus -->
                    <radialGradient id="bubbleCoreGrad" cx="35%" cy="30%" r="60%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.9" />
                        <stop offset="35%" stop-color="#dffaff" stop-opacity="0.7" />
                        <stop offset="100%" stop-color="#6fc3ff" stop-opacity="0.0" />
                    </radialGradient>

                    <!-- inner glow filter: blur + composite to push glow inward -->
                    <filter id="innerGlow" x="-30%" y="-30%" width="160%" height="160%">
                        <feGaussianBlur in="SourceAlpha" stdDeviation="6" result="blurInner" />
                        <feComposite in="blurInner" in2="SourceGraphic" operator="arithmetic" k1="0" k2="1" k3="-1" k4="0" result="innerComp" />
                        <feMerge>
                            <feMergeNode in="innerComp" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <!-- glass fill for glassmorphism appearance on main nodes -->
                    <radialGradient id="glassGrad" cx="35%" cy="28%" r="72%">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.30" />
                        <stop offset="30%" stop-color="#dff6ff" stop-opacity="0.12" />
                        <stop offset="70%" stop-color="#9fd8ff" stop-opacity="0.08" />
                        <stop offset="100%" stop-color="#0b66b2" stop-opacity="0.18" />
                    </radialGradient>

                    <filter id="glassBlur" x="-20%" y="-20%" width="140%" height="140%">
                        <feGaussianBlur stdDeviation="4" result="gblur" />
                        <feMerge>
                            <feMergeNode in="gblur" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <filter
                        id="softGlow"
                        x="-50%"
                        y="-50%"
                        width="200%"
                        height="200%"
                    >
                        <feGaussianBlur stdDeviation="6" result="blur" />
                        <feMerge>
                            <feMergeNode in="blur" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <!-- gradient for child edges -->
                    <linearGradient id="childGrad" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#7dd3fc" stop-opacity="1" />
                        <stop offset="100%" stop-color="#60a5fa" stop-opacity="1" />
                    </linearGradient>

                    <!-- gradient for scenario->child edges (distinct visual) -->
                    <linearGradient id="scenarioEdgeGrad" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#9be7ff" stop-opacity="0.98" />
                        <stop offset="50%" stop-color="#6fb8ff" stop-opacity="0.9" />
                        <stop offset="100%" stop-color="#3fa6ff" stop-opacity="0.82" />
                    </linearGradient>

                    <!-- gradient for compass needle (blue) -->
                    <linearGradient id="compassNeedleGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#08306b" stop-opacity="1" />
                        <stop offset="60%" stop-color="#1e66d6" stop-opacity="1" />
                        <stop offset="100%" stop-color="#9fe6ff" stop-opacity="1" />
                    </linearGradient>

                    <!-- subtle gradient + glow for main edges -->
                    <linearGradient id="edgeGrad" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#09c8d2" stop-opacity="0.85" />
                        <stop offset="60%" stop-color="#66b8ff" stop-opacity="0.6" />
                        <stop offset="100%" stop-color="#9bd0ff" stop-opacity="0.45" />
                    </linearGradient>

                    <filter id="edgeGlow" x="-50%" y="-50%" width="200%" height="200%">
                        <feGaussianBlur stdDeviation="3" result="blurEdge" />
                        <feMerge>
                            <feMergeNode in="blurEdge" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>

                    <!-- dot marker for child edges -->
                    <marker id="childArrow" markerUnits="strokeWidth" markerWidth="8" markerHeight="8" refX="4" refY="4" orient="auto">
                        <circle cx="4" cy="4" r="2.5" fill="url(#childGrad)" />
                    </marker>

                    <!-- arrow marker for scenario edges -->
                    <!-- scenario arrow removed: prefer clean lines without arrowheads -->

                    <filter
                        id="innerShadow"
                        x="-20%"
                        y="-20%"
                        width="140%"
                        height="140%"
                    >
                        <feOffset dx="0" dy="2" result="off" />
                        <feGaussianBlur
                            in="off"
                            stdDeviation="2"
                            result="blur2"
                        />
                        <feComposite
                            in="SourceGraphic"
                            in2="blur2"
                            operator="over"
                        />
                    </filter>

                    <!-- soft specular blur for highlights (used on small highlight shapes) -->
                    <filter id="specular" x="-50%" y="-50%" width="200%" height="200%">
                        <feGaussianBlur stdDeviation="3" result="spec" />
                        <feMerge>
                            <feMergeNode in="spec" />
                            <feMergeNode in="SourceGraphic" />
                        </feMerge>
                    </filter>
                </defs>

                <!-- subtle background rect for contrast (rounded + border/glow) -->
                <rect
                    x="0"
                    y="0"
                    :width="width"
                    :height="height"
                    rx="12"
                    ry="12"
                    fill="url(#bgGrad)"
                />
                <!-- container border/glow to emulate glass frame -->
                <rect
                    x="1"
                    y="1"
                    :width="width - 4"
                    :height="height - 2"
                    rx="12"
                    ry="12"
                    fill="none"
                    stroke="rgba(255,255,255,0.04)"
                    stroke-width="1"
                    filter="url(#softGlow)"
                />               

                <!-- edges and nodes group -->
                <g class="viewport-group" :style="viewportStyle">
                    <!-- edges -->
                    <g class="edges">
                        <line
                            v-for="(e, idx) in edges"
                            :key="`edge-${idx}`"
                            :x1="renderedNodeById(e.source)?.x ?? undefined"
                            :y1="renderedNodeById(e.source)?.y ?? undefined"
                            :x2="renderedNodeById(e.target)?.x ?? undefined"
                            :y2="renderedNodeById(e.target)?.y ?? undefined"
                            class="edge-line"
                            :stroke="`url(#edgeGrad)`"
                            stroke-width="2"
                            stroke-linecap="round"
                            filter="url(#edgeGlow)"
                            stroke-opacity="0.9"
                        />
                    </g>

                    <!-- Create capability control: placed under the home control -->
                    <g
                        class="scenario-create-control"
                        :transform="`translate(${Math.max(48, width - 56)}, 72)`"
                        @click.stop="createCapabilityClicked"
                        style="cursor: pointer"
                        aria-label="Crear capacidad"
                    >
                        <circle r="12" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.05)"/>
                        <title>Crear capacidad</title>
                        <text x="0" y="4" text-anchor="middle" font-size="16" fill="#dbeafe" style="font-weight:700">+</text>
                    </g>
                    <!-- scenario -> capability edges (distinct group so we can style/animate) -->
                    <g class="scenario-edges">
                        <template v-for="(e, idx) in scenarioEdges" :key="`scenario-edge-${idx}`">
                            <path
                                v-if="scenarioEdgePath(e)"
                                :d="scenarioEdgePath(e)"
                                class="edge-line scenario-edge"
                                stroke="url(#scenarioEdgeGrad)"
                                stroke-width="2.6"
                                stroke-linecap="round"
                                fill="none"
                                filter="url(#edgeGlow)"
                                stroke-opacity="0.95"
                            />
                            <!-- fallback to straight line if path empty -->
                            <line
                                v-else
                                :x1="renderedNodeById(e.source)?.x ?? undefined"
                                :y1="renderedNodeById(e.source)?.y ?? undefined"
                                :x2="renderedNodeById(e.target)?.x ?? undefined"
                                :y2="renderedNodeById(e.target)?.y ?? undefined"
                                class="edge-line scenario-edge"
                                stroke="url(#scenarioEdgeGrad)"
                                stroke-width="2.6"
                                stroke-linecap="round"
                                filter="url(#edgeGlow)"
                                stroke-opacity="0.95"
                            />
                        </template>
                    </g>

                    <!-- child edges: conexiones entre la capacidad seleccionada y sus competencias -->
                    <g class="child-edges">
                        <!-- curva (modo 2) -->
                        <template v-if="childEdgeMode === 2">
                            <path
                                v-for="(e, idx) in childEdges"
                                :key="`child-edge-path-${idx}`"
                                :d="edgeRenderFor(e).d"
                                class="edge-line child-edge"
                                stroke="url(#childGrad)"
                                stroke-width="4"
                                stroke-linecap="round"
                                fill="none"
                                filter="url(#edgeGlow)"
                                stroke-opacity="0.98"
                                marker-end="url(#childArrow)"
                            />
                        </template>

                        <!-- líneas simples / modos no-curva -->
                        <template v-else>
                            <line
                                v-for="(e, idx) in childEdges"
                                :key="`child-edge-line-${idx}`"
                                :x1="edgeRenderFor(e).x1 ?? undefined"
                                :y1="edgeRenderFor(e).y1 ?? undefined"
                                :x2="edgeRenderFor(e).x2 ?? undefined"
                                :y2="edgeRenderFor(e).y2 ?? undefined"
                                class="edge-line child-edge"
                                stroke="url(#childGrad)"
                                stroke-width="2"
                                stroke-linecap="round"
                                filter="url(#edgeGlow)"
                                stroke-opacity="0.98"
                                marker-end="url(#childArrow)"
                            />
                        </template>
                    </g>

                    <!-- grandchild edges: conexiones entre la competencia seleccionada y sus skills (mismo estilo que child-edges) -->
                    <g class="grandchild-edges">
                        <!-- curva (modo 2) -->
                        <template v-if="childEdgeMode === 2">
                            <path
                                v-for="(e, idx) in grandChildEdges"
                                :key="`grandchild-edge-path-${idx}`"
                                :d="edgeRenderFor(e).d"
                                class="edge-line child-edge"
                                stroke="url(#childGrad)"
                                stroke-width="4"
                                stroke-linecap="round"
                                fill="none"
                                filter="url(#edgeGlow)"
                                :stroke-opacity="edgeAnimOpacity(e)"
                                :style="{ transition: 'stroke-opacity 180ms ease' }"
                                marker-end="url(#childArrow)"
                            />
                        </template>
                        <!-- líneas simples / modos no-curva -->
                        <line
                            v-else
                            v-for="(e, idx) in grandChildEdges"
                            :key="`grandchild-edge-line-${idx}`"
                            :x1="edgeRenderFor(e).x1 ?? undefined"
                            :y1="edgeRenderFor(e).y1 ?? undefined"
                            :x2="edgeRenderFor(e).x2 ?? undefined"
                            :y2="edgeRenderFor(e).y2 ?? undefined"
                            class="edge-line child-edge"
                            stroke="url(#childGrad)"
                            stroke-width="2"
                            stroke-linecap="round"
                            filter="url(#edgeGlow)"
                            :stroke-opacity="edgeAnimOpacity(e)"
                            :style="{ transition: 'stroke-opacity 180ms ease' }"
                            marker-end="url(#childArrow)"
                        />
                    </g>

                    <!-- nodes -->
                    <!-- scenario/origin node (optional) -->
                    <g
                        v-if="scenarioNode"
                        :style="{ transform: `translate(${scenarioNode.x}px, ${scenarioNode.y}px)` }"
                        class="node-group scenario-node"
                        :data-node-id="scenarioNode.id"
                        @click.stop="handleScenarioClick"
                        :title="'Ver información del escenario'"
                        style="cursor: pointer"
                    >
                        <title>{{ scenarioNode.name }}</title>
                        <!-- Smaller parent node (scenario) with icon support -->
                        <circle
                            class="node-circle"
                            r="22"
                            fill="url(#glassGrad)"
                            filter="url(#softGlow)"
                            stroke="rgba(255,255,255,0.06)"
                            stroke-width="1.2"
                        />
                        <!-- Compass rose icon centered on the scenario node (larger, stylized) -->
                        <g class="scenario-icon" transform="translate(0,0) scale(1.6)">
                            <!-- subtle backing circle for contrast -->
                            <circle r="9" fill="rgba(0,0,0,0.28)" />
                            <!-- elegant 8-point rose -->
                            <g class="rose" transform="translate(0,0)">
                                <g class="rose-x" transform="translate(0,2)">
                                    <path class="rose-x-arm" d="M0,-14 L5,0 L0,10 L-5,0 Z" transform="rotate(45) scale(1.2)" fill="rgba(255,255,255,0.06)" />
                                    <path class="rose-x-arm" d="M0,-14 L5,0 L0,10 L-5,0 Z" transform="rotate(-45) scale(1.2)" fill="rgba(255,255,255,0.06)" />
                                </g>
                                <!-- outline / secondary points -->
                                <path class="rose-outline" d="M0,-14 L4,-4 L14,0 L4,4 L0,14 L-4,4 L-14,0 L-4,-4 Z" />
                                <!-- primary north needle -->
                                <path class="rose-primary" d="M0,-14 L5,0 L0,10 L-5,0 Z" />
                                <!-- primary south needle (mirrored) - aumentada 10% -->
                                <path class="rose-secondary" d="M0,14 L5,0 L0,-10 L-5,0 Z" transform="scale(1.1)" fill="url(#compassNeedleGrad)" />
                                <!-- subtle X behind center using the needle shapes (rotated) -->
                                <!-- center hub -->
                                <circle class="rose-center" r="2" />
                            </g>
                        </g>
                    </g>

                    <g
                        v-for="node in nodes"
                        :key="node.id"
                        :style="{ transform: `translate(${renderNodeX(node)}px, ${node.y}px)` }"
                        class="node-group"
                        :data-node-id="node.id"
                        :class="{
                            critical: !!node.is_critical,
                            focused: focusedNode && focusedNode.id === node.id,
                            dragging: dragging && dragging.id === node.id,
                            small: focusedNode && focusedNode.id !== node.id,
                        }"
                        @pointerdown.prevent="startDrag(node, $event)"
                        @click.stop="(e) => handleNodeClick(node, e)"
                        @contextmenu.prevent.stop="(e) => openNodeContextMenu(node, e)"
                    >
                        <title>{{ node.name }}</title>
                        <circle
                            class="node-circle"
                            r="34"
                            fill="url(#bubbleOuterGrad)"
                            filter="url(#innerGlow)"
                            stroke="rgba(255,255,255,0.12)"
                            stroke-opacity="1"
                            stroke-width="1.2"
                        />
                        <!-- iridescent sheen overlay: semitransparent, uses blend to simulate soap colors -->
                        <circle
                            class="node-iridescence"
                            r="34"
                            fill="url(#iridescentGrad)"
                            opacity="0.22"
                            style="mix-blend-mode: screen"
                        />
                        <!-- bubble-style highlight: small blurred specular on top-left -->
                        <!-- <ellipse
                            class="node-reflection"
                            cx="-12"
                            cy="-14"
                            rx="14"
                            ry="9"
                            fill="#ffffff"
                            fill-opacity="0.18"
                            transform="rotate(-22)"
                            filter="url(#specular)"
                        /> -->
                        <!-- inner core that suggests nucleus -->
                        <circle
                            class="node-core"
                            r="10"
                            fill="url(#bubbleCoreGrad)"
                            filter="url(#specular)"
                        />
                        <!-- subtle glossy rim to enhance bubble feel -->
                        <circle
                            class="node-rim"
                            r="34"
                            fill="none"
                            stroke="#ffffff"
                            stroke-opacity="0.08"
                            stroke-width="1.4"
                        />
                        <circle
                            v-if="node.is_critical"
                            class="node-inner"
                            r="12"
                            fill="#ff5050"
                            fill-opacity="0.95"
                        />
                        <text :x="0" :y="38" text-anchor="middle" class="node-label">
                            <tspan v-for="(line, idx) in ((node as any).displayName ?? node.name).split('\n')" :key="idx" :x="0" :dy="idx === 0 ? 0 : 12">{{ line }}</tspan>
                        </text>
                    </g>

                    <!-- child nodes (competencies) -->
                    <g class="child-nodes">
                        <g
                            v-for="c in childNodes"
                            :key="c.id"
                            :style="{ transform: `translate(${c.x}px, ${c.y}px) scale(${c.animScale ?? 1})`, opacity: (c.animOpacity ?? 1), transitionDelay: (c.animDelay ? c.animDelay + 'ms' : undefined), filter: c.animFilter ? c.animFilter : undefined }"
                                class="node-group child-node"
                            :data-node-id="c.id"
                            @click.stop="(e) => handleNodeClick(c, e)"
                            @contextmenu.prevent.stop="(e) => openNodeContextMenu(c, e)"
                        >
                            <title>{{ c.name }}</title>
                            <circle
                                class="node-circle"
                                :r="20"
                                fill="#2b2b2b"
                                stroke="#ffffff"
                                stroke-opacity="0.06"
                                stroke-width="1"
                            />
                            <!-- child node: iridescent sheen + small reflection to match bubble style -->
                            <circle
                                class="node-iridescence child-iridescence"
                                :r="20"
                                fill="url(#iridescentGrad)"
                                opacity="0.18"
                                style="mix-blend-mode: screen"
                            />
                            <ellipse
                                class="node-reflection child-reflection"
                                cx="-6"
                                cy="-6"
                                rx="7"
                                ry="4.5"
                                fill="#ffffff"
                                fill-opacity="0.14"
                                transform="rotate(-22)"
                                filter="url(#specular)"
                            />
                            <circle
                                class="node-rim child-rim"
                                :r="20"
                                fill="none"
                                stroke="#ffffff"
                                stroke-opacity="0.06"
                                stroke-width="1"
                            />
                            <circle
                                class="node-gloss child-gloss"
                                :r="22"
                                fill="none"
                                stroke="#ffffff"
                                stroke-opacity="0.04"
                                stroke-width="4"
                                filter="url(#softGlow)"
                            />
                            <text :x="0" :y="22" text-anchor="middle" class="node-label" style="font-size:10px">
                                <tspan v-for="(line, idx) in String((c as any).displayName ?? c.name).split('\n')" :key="idx" :x="0" :dy="idx === 0 ? 0 : 10">{{ line }}</tspan>
                            </text>
                        </g>
                    </g>

                    <!-- skill nodes (grandchildren) -->
                    <g class="skill-nodes">
                        <g
                            v-for="(s) in grandChildNodes"
                            :key="s.id"
                            :style="{ transform: `translate(${s.x}px, ${s.y}px) scale(${s.animScale ?? 1})`, opacity: (s.animOpacity ?? 1) }"
                            class="node-group skill-node"
                            :data-node-id="s.id"
                            @click.stop="(e) => handleSkillClick(s, e)"
                        >
                            <title>{{ s.name }}</title>
                            <!-- skill bubble base -->
                            <circle class="node-circle" :r="14" fill="#0F8CA8" stroke="#ffffff" stroke-opacity="0.06" stroke-width="1" />
                            <!-- iridescent sheen -->
                            <circle class="node-iridescence" :r="14" fill="url(#iridescentGrad)" opacity="0.14" style="mix-blend-mode: screen" />
                            <!-- small glossy rim to enhance bubble feel -->
                            <circle class="node-rim skill-rim" :r="14" fill="none" stroke="#ffffff" stroke-opacity="0.06" stroke-width="1" />
                            <!-- subtle outer gloss + soft glow -->
                            <circle class="node-gloss skill-gloss" :r="16" fill="none" stroke="#ffffff" stroke-opacity="0.04" stroke-width="3" filter="url(#softGlow)" />
                            <!-- tiny specular reflection -->
                            <ellipse class="node-reflection skill-reflection" cx="-5" cy="-5" rx="5" ry="3" fill="#ffffff" fill-opacity="0.12" transform="rotate(-22)" filter="url(#specular)" />
                            <text :x="0" :y="4" text-anchor="middle" class="node-label" style="font-size:10px">{{ s.name }}</text>
                        </g>
                    </g>
                    <!-- overlay: dibujar conectores tipo 'hub' para que cada hijo tenga su conector visible -->
                    
                    <!-- Controles integrados en el SVG: reordenar / restaurar vista -->
                    <g
                        class="diagram-control reorder-control"
                        :transform="`translate(${Math.max(48, width - 56)}, 108)`"
                        @click.stop="reorderNodes"
                        style="cursor: pointer"
                    >
                        <circle r="12" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.06)"/>
                        <title>Reordenar nodos</title>
                        <text x="0" y="4" text-anchor="middle" font-size="11" fill="#dbeafe" style="font-weight:700">R</text>
                    </g>
                    
                    <g
                        class="diagram-control mode-control"
                        :transform="`translate(${Math.max(48, width - 56)}, 180)`"
                        @click.stop="nextChildEdgeMode"
                        style="cursor: pointer"
                        :title="`Edge mode: ${childEdgeModeLabels[childEdgeMode]}`"
                    >
                        <circle r="12" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.06)"/>
                        <title>Cambiar modo conector</title>
                        <text x="0" y="4" text-anchor="middle" font-size="10" fill="#dbeafe" style="font-weight:700">{{ childEdgeModeLabels[childEdgeMode] }}</text>
                    </g>

                    <g
                        class="diagram-control restore-control"
                        :transform="`translate(${Math.max(48, width - 56)}, 144)`"
                        @click.stop="restoreView"
                        style="cursor: pointer"
                    >
                        <circle r="12" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.06)"/>
                        <title>Restaurar vista</title>
                        <text x="0" y="4" text-anchor="middle" font-size="11" fill="#dbeafe" style="font-weight:700">↺</text>
                    </g>
                    </g>

                <!-- Integrated title overlay (renders on top of diagram) -->
                <foreignObject x="12" y="8" :width="Math.min(420, width - 48)" height="40">
                    <div xmlns="http://www.w3.org/1999/xhtml" class="svg-title-fo">
                        <div class="svg-title-text">
                            <template v-for="(part, idx) in breadcrumbParts" :key="`breadcrumb-${idx}`">
                                <div class="svg-title-line">{{ part }}</div>
                            </template>
                        </div>
                    </div>
                </foreignObject>

            </svg>

            <!-- Context menu overlay (right-click) replaced with Vuetify v-menu -->
            <v-menu
                v-model="contextMenuVisible"
                absolute
                offset-y
                transition="scale-transition"
                :close-on-content-click="false"
                :open-on-click="false"
                :style="{ left: contextMenuLeft + 'px', top: contextMenuTop + 'px', zIndex: 200000 }"
            >
                <template #default>
                    <div ref="contextMenuEl" class="node-context-menu-v" style="min-width:250px;">
                        <v-list density="compact">
                            <v-list-item @click="contextViewEdit" class="node-context-item">
                                <v-list-item-icon>
                                    <v-icon icon="mdi-eye-outline" />
                                    Ver Detalles
                                </v-list-item-icon>
                            </v-list-item>
                            <!-- <v-list-item @click="contextMenuIsChild ? contextCreateSkill() : contextCreateChild()" class="node-context-item">
                                <v-list-item-icon>
                                    <v-icon icon="mdi-plus" />  
                                </v-list-item-icon>
                                <v-list-item-title>{{ contextMenuIsChild ? 'Crear skill' : 'Crear competencia' }}</v-list-item-title>
                            </v-list-item>
                            <v-list-item v-if="contextMenuIsChild" @click="contextAttachExistingSkill" class="node-context-item">
                                <v-list-item-icon>
                                    <v-icon icon="mdi-link-variant" />
                                </v-list-item-icon>
                                <v-list-item-title>Agregar skill existente</v-list-item-title>
                            </v-list-item>
                            <v-list-item @click="contextDeleteNode" class="node-context-item">
                                <v-list-item-icon>
                                    <v-icon icon="mdi-delete-outline" />
                                </v-list-item-icon>
                                <v-list-item-title class="text-error">Eliminar nodo</v-list-item-title>
                            </v-list-item> -->
                        </v-list>
                    </div>
                </template>
            </v-menu>

            <!-- Reemplazo: mostrar detalles en modal en lugar de panel lateral -->
            <v-dialog v-model="showSidebar" max-width="980" persistent scrollable transition="scale-transition"
                      content-class="capability-dialog" role="dialog" aria-modal="true"
                      :aria-label="displayNode ? `Detalles: ${displayNode.name}` : 'Detalle de elemento'">
                <v-card :class="dialogThemeClass">
                    <v-card-title class="d-flex justify-space-between align-center">
                        <strong>{{ displayNode ? displayNode.name : (showSidebar ? 'Escenario' : 'Detalle') }}</strong>
                        <div class="d-flex align-center" style="gap:8px">
                           <!--  <v-btn icon small variant="text" @click="toggleSidebarTheme" :title="sidebarTheme === 'dark' ? 'Tema claro' : 'Tema oscuro'">
                                <v-icon :icon="sidebarTheme === 'dark' ? 'mdi-weather-sunny' : 'mdi-weather-night'" />
                            </v-btn> -->
                            <v-btn icon small variant="text" @click="showSidebar = false">
                                <v-icon icon="mdi-close" />
                            </v-btn>
                        </div>
                    </v-card-title>
                    <v-card-text style="max-height:70vh; overflow:auto;">
                        <template v-if="displayNode">
                            <div class="text-xs text-white/60 mb-2">
                                <div><strong>ID:</strong> {{ (displayNode as any).id ?? '—' }}</div>
                                <div><strong>Competencias:</strong> {{ ((displayNode as any).competencies || []).length }}</div>
                            </div>

                            <div class="text-small text-medium-emphasis mb-2">
                                <template v-if="(displayNode as any).skills || (displayNode as any).compId">
                                    <div style="position:relative;">
                                        <div style="max-height:360px; overflow:auto; padding-right:12px;">
                                            <v-form ref="capForm" @submit.prevent>
                                                <div style="display:flex; gap:8px; grid-column: 1 / -1; margin-top:8px">
                                                    <v-btn small color="primary" @click="showCreateSkillDialog">Crear Skill</v-btn>
                                                    <v-btn small color="primary" @click="openSelectSkillDialog">Agregar existente</v-btn>
                                                    <v-btn color="error" @click="deleteFocusedNode" :loading="savingNode">Eliminar nodo</v-btn>
                                                    <v-btn color="primary" @click="saveSelectedChild" :loading="savingNode" :disabled="savingNode || capFormInvalid">Guardar</v-btn>
                                                </div>
                                                <div style="display:grid; gap:12px; grid-template-columns: 1fr 1fr; align-items:start;">
                                                    <v-text-field v-model="editChildName" label="Nombre" required />
                                                    <div>
                                                        <v-text-field 
                                                            v-model="editChildReadiness" 
                                                            label="Readiness (%)" 
                                                            type="number" 
                                                            readonly 
                                                            hint="Calculado automáticamente por el sistema basado en evaluaciones y niveles"
                                                            persistent-hint
                                                        />
                                                    </div>
                                                    <v-textarea v-model="editChildDescription" label="Descripción" rows="3" style="grid-column: 1 / -1;" />

                                                    <div style="grid-column: 1 / -1; font-weight:700; margin-top:6px">Atributos de la relación con la capacidad</div>
                                                    <div>
                                                        <v-slider v-model="editChildPivotStrategicWeight" label="Strategic weight (1-10)" :min="1" :max="10" :step="1" color="primary" :ticks="tickLabelStrategic" show-ticks="always"/>
                                                    </div>
                                                    <div>
                                                        <v-slider v-model="editChildPivotPriority" label="Priority (1-5)" :min="1" :max="5" :step="1" color="orange" :ticks="tickLabelPriority" show-ticks="always"/>
                                                    </div>

                                                    <div>
                                                        <v-slider v-model="editChildPivotRequiredLevel" label="Required level (1-5)" :min="1" :max="5" :step="1" color="teal" :ticks="tickLabelRequiredLevel" show-ticks="always"/>
                                                    </div>
                                                    <v-checkbox v-model="editChildPivotIsCritical" label="Is critical" />

                                                    <v-textarea v-model="editChildPivotRationale" label="Rationale" rows="2" style="grid-column: 1 / -1;" />

                                                </div>
                                            </v-form>
                                        </div>
                                    </div>
                                </template>

                                <template v-else>
                                    <div style="position:relative;">
                                        <div style="display:flex; gap:8px; margin-bottom:8px">
                                            <v-btn small color="primary" @click="showCreateCompDialog">Crear competencia</v-btn>
                                            <v-btn small color="primary" @click="openAddExistingCompDialog">Agregar existente</v-btn>
                                            <v-btn color="error" @click="deleteFocusedNode" :loading="savingNode">Eliminar nodo</v-btn>
                                            <v-btn color="primary" @click="saveFocusedNode" :loading="savingNode" :disabled="savingNode || capFormInvalid">Guardar</v-btn>
                                            <v-btn text color="primary" @click="resetFocusedEdits">Cancelar</v-btn>

                                        </div>
                                        <div style="max-height:360px; overflow:auto; padding-right:12px;">
                                            <v-form ref="capForm" @submit.prevent>
                                                <div style="display:grid; gap:12px; grid-template-columns: 1fr 1fr; align-items:start;">
                                                    <v-text-field v-model="editCapName" label="Nombre" required />
                                                    <div>
                                                        <v-slider v-model="editCapImportance" label="Importancia" :min="1" :max="3" :step="1" color="primary" :ticks="tickLabelImportance" show-ticks="always"/>
                                                    </div>

                                                    <v-select v-model="editCapType" :items="['technical','behavioral','strategic']" label="Tipo" />
                                                    <v-select v-model="editCapCategory" :items="['technical','leadership','business','operational']" label="Categoría" />

                                                    <v-textarea v-model="editCapDescription" label="Descripción" rows="3" style="grid-column: 1 / -1;" />

                                                    <div style="grid-column: 1 / -1; font-weight:700; margin-top:6px">Atributos para el escenario (scenario_capabilities)</div>
                                                    <v-select v-model="editPivotStrategicRole" :items="['target','watch','sunset']" label="Strategic role" />

                                                    <div>
                                                        <v-slider v-model="editPivotStrategicWeight" label="Strategic weight" :min="1" :max="10" :step="1" color="primary" :ticks="tickLabelStrategic" show-ticks="always"/>
                                                    </div>
                                                    <div>
                                                        <v-slider v-model="editPivotPriority" label="Priority (1-5)" :min="1" :max="5" :step="1" color="orange" :ticks="tickLabelPriority" show-ticks="always"/>
                                                    </div>

                                                    <div>
                                                        <v-slider v-model="editPivotRequiredLevel" label="Required level (1-5)" :min="1" :max="5" :step="1" color="teal" :ticks="tickLabelRequiredLevel" show-ticks="always"/>
                                                    </div>
                                                    <v-switch v-model="editPivotIsCritical" label="Is critical" />

                                                    <v-textarea v-model="editPivotRationale" label="Rationale" rows="2" style="grid-column: 1 / -1;" />
                                                </div>
                                            </v-form>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template v-else>
                            <div>
                                <div style="font-weight:700">Escenario</div>
                                <div><strong>Nombre:</strong> {{ props.scenario?.name ?? '—' }}</div>
                                <div><strong>ID:</strong> {{ props.scenario?.id ?? '—' }}</div>
                                <div><strong>Descripción:</strong> {{ props.scenario?.description ?? '—' }}</div>
                                <div style="margin-top:6px"><strong>Estado:</strong> {{ props.scenario?.status ?? '—' }} • <strong>Año fiscal:</strong> {{ props.scenario?.fiscal_year ?? '—' }}</div>
                                <div style="margin-top:8px; display:flex; gap:8px; align-items:center">
                                    <v-btn small color="secondary" @click="showScenarioRaw = !showScenarioRaw">{{ showScenarioRaw ? 'Ocultar JSON' : 'Ver JSON crudo' }}</v-btn>
                                    <v-btn small text @click="() => { void loadTreeFromApi(props.scenario?.id); }">Refrescar árbol</v-btn>
                                </div>
                                <div v-if="showScenarioRaw" style="margin-top:12px; max-height:420px; overflow:auto; background:rgba(0,0,0,0.04); padding:8px; border-radius:6px">
                                    <pre style="white-space:pre-wrap; word-break:break-word">{{ capabilityTreeRaw ? JSON.stringify(capabilityTreeRaw, null, 2) : 'No hay datos cargados. Pulsa "Refrescar árbol".' }}</pre>
                                </div>
                            </div>
                        </template>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="showSidebar = false">Cerrar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Create competency dialog -->
            <v-dialog v-model="createCompDialogVisible" max-width="640" transition="scale-transition">
                <v-card :class="dialogThemeClass">
                    <v-card-title>Crear competencia</v-card-title>
                    <v-card-text>
                                        <v-form @submit.prevent>
                            <v-text-field v-model="newCompName" label="Nombre" required />
                            <v-textarea v-model="newCompDescription" label="Descripción" rows="3" />
                            <v-text-field v-model="newCompReadiness" label="Readiness" type="number" />
                            <v-textarea v-model="newCompSkills" label="Skills (coma-separadas)" rows="2" />
                                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="createCompDialogVisible = false">Cancelar</v-btn>
                                        <v-btn color="primary" :loading="creatingComp" @click="onClickCreateAndAttachComp">Crear y asociar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Create skill dialog -->
            <v-dialog v-model="createSkillDialogVisible" max-width="640" transition="scale-transition">
                <v-card :class="dialogThemeClass">
                    <v-card-title>Crear nueva skill</v-card-title>
                    <v-card-text>
                        <v-form>
                            <v-text-field v-model="newSkillName" label="Nombre" required />
                            <v-text-field v-model="newSkillCategory" label="Categoría" />
                            <v-textarea v-model="newSkillDescription" label="Descripción" rows="3" />
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="createSkillDialogVisible = false">Cancelar</v-btn>
                        <v-btn color="primary" :loading="savingSkill" @click="createAndAttachSkill">Crear y asociar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Select existing skill dialog -->
            <v-dialog v-model="selectSkillDialogVisible" max-width="720" transition="scale-transition">
                <v-card :class="dialogThemeClass">
                    <v-card-title>Seleccionar skill existente</v-card-title>
                    <v-card-text>
                        <v-select
                            :items="availableSkills"
                            item-title="name"
                            item-value="id"
                            v-model="selectedSkillId"
                            label="Skill"
                        />
                        <div v-if="availableSkills.length === 0" style="margin-top:8px">No se encontraron skills.</div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="selectSkillDialogVisible = false">Cancelar</v-btn>
                        <v-btn color="primary" :loading="attachingSkill" @click="attachExistingSkill">Asociar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Skill detail dialog (editable) -->
            <v-dialog v-model="skillDetailDialogVisible" max-width="720" transition="scale-transition">
                <v-card :class="dialogThemeClass">
                    <v-card-title>Detalle de la skill</v-card-title>
                    <v-card-text>
                        <div v-if="selectedSkillDetail">
                            <div class="grid" style="display:grid; gap:12px; grid-template-columns: 1fr 1fr;">
                                <v-text-field v-model="skillEditName" label="Nombre" required />
                                <v-select v-model="skillEditCategory" :items="['technical','behavioral','domain']" label="Categoría" />
                                <v-select v-model="skillEditComplexityLevel" :items="['basic','tactical','strategic']" label="Nivel de complejidad" />
                                <v-text-field v-model="skillEditDomainTag" label="Tag de dominio" />
                                <v-select v-model="skillEditScopeType" :items="['domain','global','local']" label="Scope" />
                                <v-textarea v-model="skillEditDescription" label="Descripción" rows="3" style="grid-column: 1 / -1" />
                            </div>

                            <!-- Pivot (capability_competencies) editable section when available -->
                            <div v-if="selectedChild && focusedNode" style="margin-top:14px">
                                <div style="font-weight:700; margin-bottom:6px">Atributos de la relación con la competencia</div>
                                <div>
                                    <v-slider v-model="skillPivotWeight" label="Peso estratégico (1-10)" :min="1" :max="10" :step="1" color="primary" :ticks="tickLabelStrategic" show-ticks="always"/>
                                </div>
                                <div class="grid" style="display:grid; gap:12px; grid-template-columns: 1fr 1fr;">
                                    <div>
                                        <v-slider v-model="skillPivotPriority" label="Prioridad (1-5)" :min="1" :max="5" :step="1" color="orange" :ticks="tickLabelPriority" show-ticks="always"/>
                                    </div>

                                    <div>
                                        <v-slider v-model="skillPivotRequiredLevel" label="Nivel requerido (1-5)" :min="1" :max="5" :step="1" color="teal" :ticks="tickLabelRequiredLevel" show-ticks="always"/>
                                    </div>
                                    <div>
                                    <v-switch v-model="skillPivotIsRequired" color="success" label="Es crítica" />
                                    <v-textarea v-model="skillPivotRationale" label="Justificación" rows="2" style="grid-column: 1 / -1;" />
                                </div>
                                </div>
                            </div>
                        </div>
                        <div v-else>No hay información de la skill.</div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="skillDetailDialogVisible = false">Cancelar</v-btn>
                        <v-btn color="primary" :loading="savingSkillDetail" @click="saveSkillDetail">Guardar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Add existing competency dialog -->
            <v-dialog v-model="addExistingCompDialogVisible" max-width="640" transition="scale-transition">
                <v-card :class="dialogThemeClass">
                    <v-card-title>Agregar competencia existente</v-card-title>
                    <v-card-text>
                        <v-select :items="availableExistingCompetencies" item-title="name" item-value="id" v-model="addExistingSelection" label="Competencia" />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="addExistingCompDialogVisible = false">Cancelar</v-btn>
                        <v-btn color="primary" @click="attachExistingComp">Agregar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!-- Create capability modal: form exposes fields from `capabilities` and `scenario_capabilities` -->
            <v-dialog v-model="createModalVisible" max-width="720" transition="scale-transition">
                <v-card :class="dialogThemeClass">
                    <v-card-title>Crear capacidad</v-card-title>
                    <v-card-text>
                        <div class="grid" style="display:grid; gap:12px; grid-template-columns: 1fr 1fr;">
                            <v-text-field v-model="newCapName" label="NombreXX" required />
                            <v-select v-model="newCapType" :items="['technical', 'behavioral', 'strategic']" label="Tipo" />
                            <v-select v-model="newCapCategory" :items="['technical', 'leadership', 'business', 'operational']" label="Categoría" />
                            <div>
                                <v-slider v-model="newCapImportance" label="Importancia (1-3):" :min="1" :max="3" :step="1" color="green" :ticks="tickLabelImportance" show-ticks="always"/>
                            </div>
                            <v-textarea v-model="newCapDescription" label="Descripción" rows="3" style="grid-column: 1 / -1" />
                        </div>

                        <div class="mt-3" style="margin-top:12px">
                            <div style="font-weight:700; margin-bottom:6px">Atributos para el escenario (scenario_capabilities)</div>
                            <div class="grid" style="display:grid; gap:12px; grid-template-columns: 1fr 1fr;">
                                <v-select v-model="pivotStrategicRole" :items="['target','watch','sunset']" label="Strategic role" />
                                <div>
                                    <v-slider v-model="pivotStrategicWeight" label="Strategic weight (1-10)" :min="1" :max="10" :step="1" color="primary"/>
                                    <div class="text-xs" style="margin-top:6px">Valor: {{ pivotStrategicWeight ?? '-' }}</div>
                                </div>
                                <div>
                                    <v-slider v-model="pivotPriority" label="Priority (1-5)" :min="1" :max="5" :step="1" color="orange"/>
                                    <div class="text-xs" style="margin-top:6px">Valor: {{ pivotPriority ?? '-' }}</div>
                                </div>
                                <div>
                                    <v-slider v-model="pivotRequiredLevel" label="Required level (1-5)" :min="1" :max="5" :step="1" color="teal"/>
                                    <div class="text-xs" style="margin-top:6px">Valor: {{ pivotRequiredLevel ?? '-' }}</div>
                                </div>
                                <v-checkbox v-model="pivotIsCritical" label="Is critical" />
                                <v-textarea v-model="pivotRationale" label="Rationale" rows="2" style="grid-column: 1 / -1" />
                            </div>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn text @click="createModalVisible = false">Cancelar</v-btn>
                        <v-btn color="primary" :loading="creating" @click="saveNewCapability">Guardar</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            
            <div class="cap-list" v-if="nodes.length === 0">
                No hay capacidades para mostrar.
            </div>
                <!-- debug controls removed -->
        </div>
    </div>
</div>
</template>

<style scoped>
.prototype-map-root {
    padding: 16px;
    position: relative;
    /* use viewport-aware height so the component adapts to screen size */
    height: calc(100vh - 120px);
    max-height: calc(100vh - 72px);
    display: flex;
    flex-direction: column;
    color: #ffffff;
}

/* Styles for capability dialog content */
.capability-dialog {
    padding: 8px !important;
    box-sizing: border-box;
    max-width: 980px;
}

.prototype-map-root::before {
    content: '';
    position: absolute;
    inset: 8px;
    border-radius: 14px;
    pointer-events: none;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.04), inset 0 -24px 48px rgba(11,22,48,0.12);
    z-index: 10;
}

.map-canvas {
    border-radius: 12px;
    overflow: visible;
}

/* sidebar styles - anchored to the right */
.scenario-sidebar {
    position: absolute;
    right: 16px;
    top: 16px;
    bottom: 16px;
    width: 340px;
    max-width: calc(100% - 48px);
    background: rgba(18, 24, 32, 0.95);
    color: #fff;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 12px 40px rgba(2,6,23,0.6);
    z-index: 60;
    overflow: auto;
}
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 240ms ease;
}
.slide-fade-enter-from {
    transform: translateX(12px);
    opacity: 0;
}
.slide-fade-enter-to {
    transform: translateX(0);
    opacity: 1;
}
.slide-fade-leave-from {
    transform: translateX(0);
    opacity: 1;
}
.slide-fade-leave-to {
    transform: translateX(12px);
    opacity: 0;
}

.edges line {
    transition:
        x1 0.08s linear,
        y1 0.08s linear,
        x2 0.08s linear,
        y2 0.08s linear;
}

/* visual improvements */
.edge-line {
    stroke: rgba(255, 255, 255, 0.12);
    stroke-width: 1.5;
    transition:
        stroke 0.12s ease,
        stroke-width 0.12s ease,
        opacity 0.12s ease;
}

/* make SVG canvas expand to available space */
.map-canvas {
    display: block;
    width: 100%;
    flex: 1 1 auto;
    height: auto;
    min-height: 260px;
}

/* smooth pan/zoom transitions for viewport group */
.viewport-group {
    transition: transform 420ms cubic-bezier(.22,.9,.3,1);
}

.node-group {
    cursor: grab;
    transition: transform 420ms cubic-bezier(.22,.9,.3,1), opacity 160ms ease, filter 180ms ease;
    transform-box: fill-box;
    transform-origin: center;
}
.node-group:active {
    cursor: grabbing;
}

/* hover scale removed to disable hover animation */

.node-circle {
    transition:
        r 0.12s ease,
        filter 0.12s ease,
        transform 0.12s ease;
}

/* SVG text labels should be white for better contrast */
.node-label {
    fill: #ffffff;
    fill-opacity: 1;
    font-weight: 600;
    font-size: 12px;
    dominant-baseline: hanging;
}

.node-group.small .node-label {
    font-size: 10px;
}

/* scale down non-selected nodes smoothly via CSS transform */
.node-circle {
    transition:
        r 0.12s ease,
        filter 0.12s ease,
        transform 420ms cubic-bezier(.22,.9,.3,1);
    transform-box: fill-box;
    transform-origin: center;
}

/* hover + focus */
.node-group:hover .node-circle {
    transform: scale(1.06);
}
.node-group:active .node-circle {
    transform: scale(1.02);
}

/* pulse for critical nodes */
.node-group.critical {
    animation: pulse 2400ms infinite;
}
@keyframes pulse {
    0% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(54, 52, 52, 0)); }
    50% { transform: scale(1.03); }
    100% { transform: scale(1); }
}

/* scale down visuals (circle + label) for non-selected nodes without moving their group */
.node-group.small .node-circle {
    transform: scale(0.5);
}
.node-group.small .node-label {
    transform: scale(0.85);
    transform-box: fill-box;
    transform-origin: center;
}

/* scenario node styling (smooth follow transition) */
.scenario-node {
    pointer-events: auto;
    transition: transform 360ms cubic-bezier(.22,.9,.3,1);
}

/* compass/rose styling */
.scenario-node .scenario-icon {
    pointer-events: none;
    transform-box: fill-box;
    transform-origin: center;
}
.scenario-node .scenario-icon .rose-outline {
    fill: rgba(211, 196, 196, 0.08);
    stroke: rgba(201, 210, 213, 0.432);
    stroke-width: 0.4;
}
.scenario-node .scenario-icon .rose-primary {
    fill: #5f82b9; /* north needle: changed from pink to blue */
}
.scenario-node .scenario-icon .rose-secondary {
    /* fill is provided by SVG gradient (compassNeedleGrad) */
}
.scenario-node .scenario-icon .rose-center {
    fill: #03555b;
}

/* make the north/south compass needles more subtle */
.scenario-node .scenario-icon .rose-primary,
.scenario-node .scenario-icon .rose-secondary {
    fill-opacity: 0.25;
}

/* animated scenario-edge: moving dash + soft pulse */
.scenario-edge {
    stroke-linecap: round;
}

/* scenario-edge: static styling (no animation) */
.edge-line.scenario-edge {
    stroke-linecap: round;
}

.child-node .node-circle {
    fill: #3e5069;
}

.child-iridescence {
    pointer-events: none;
    transition: opacity 200ms ease, transform 200ms ease;
    mix-blend-mode: screen;
}
.child-reflection {
    pointer-events: none;
}
.child-rim {
    pointer-events: none;
}
.child-edge {
    stroke-dasharray: none;
    opacity: 0.95;
}

/* Skill node specific styles (scaled bubble) */
.skill-node .node-circle {
    transition: transform 220ms ease, filter 180ms ease;
    fill: #16324a;
}
.skill-node .node-iridescence {
    pointer-events: none;
    opacity: 0.14;
    mix-blend-mode: screen;
}
.skill-node .skill-reflection {
    pointer-events: none;
    transition: opacity 160ms ease, transform 160ms ease;
}
.skill-node .skill-rim {
    pointer-events: none;
    transition: stroke-opacity 160ms ease;
}
.skill-node .skill-gloss {
    pointer-events: none;
}
.skill-node .node-label {
    font-size: 10px;
    font-weight: 600;
}

/* make child edges more visible with subtle glow */
.edge-line.child-edge {
    mix-blend-mode: screen;
}

.edge-line {
    transition: stroke-width 180ms ease, stroke-opacity 180ms ease, filter 220ms ease;
}

.node-inner {
    pointer-events: none;
}

.node-reflection {
    pointer-events: none;
    transition: opacity 180ms ease, transform 180ms ease;
}
.node-rim {
    pointer-events: none;
    transition: stroke-opacity 180ms ease, transform 180ms ease;
}

.node-iridescence {
    pointer-events: none;
    transition: opacity 220ms ease, transform 220ms ease;
    mix-blend-mode: screen;
}

.node-gloss,
.child-gloss {
    pointer-events: none;
}

/* glass panel styles */
.glass-panel-strong {
    color: #ffffff;
    background: rgba(255,255,255,0.04);
    padding: 20px;
    box-sizing: border-box;
    min-width: 220px;
}
.glass-panel-strong .panel-header {
    cursor: move;
    user-select: none;
    padding-bottom: 8px;
    margin-bottom: 8px;
}
.glass-panel-strong .mb-2 {
    margin-bottom: 8px;
}

/* sidebar variant: when a node detail sidebar is open, push content to the left */
.with-node-sidebar {
    transition: margin 180ms ease;
    margin-right: 360px; /* width of sidebar */
}

.node-details-sidebar {
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    width: 360px;
    box-sizing: border-box;
    z-index: 60;
    /* allow the collapse toggle to overflow the panel edge so it remains visible */
    overflow: visible;
    padding: 20px;
    backdrop-filter: blur(6px);
}

.node-details-sidebar.theme-dark {
    box-shadow: 0 8px 30px rgba(2,6,23,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
    border-left: 1px solid rgba(255,255,255,0.04);
}
.node-details-sidebar.theme-light {
    box-shadow: 0 8px 24px rgba(2,6,23,0.28), inset 0 1px 0 rgba(255,255,255,0.02);
}

/* Light theme (default) */
.node-details-sidebar.theme-light {
    border-left: 1px solid rgba(0,0,0,0.06);
    background: rgba(255,255,255,0.96);
    color: #0b0b0b;
}
.node-details-sidebar.theme-light .text-white\/60,
.node-details-sidebar.theme-light .text-white\/50,
.node-details-sidebar.theme-light .text-small,
.node-details-sidebar.theme-light .text-xs {
    color: rgba(0,0,0,0.72) !important;
}
.node-details-sidebar.theme-light pre,
.node-details-sidebar.theme-light summary {
    color: #0b0b0b !important;
}

/* Dark theme (glass-style) */
.node-details-sidebar.theme-dark {
    border-left: 1px solid rgba(255,255,255,0.04);
    background: rgba(11,16,41,0.95);
    color: #ffffff;
}
.node-details-sidebar.theme-dark .text-white\/60,
.node-details-sidebar.theme-dark .text-white\/50,
.node-details-sidebar.theme-dark .text-small,
.node-details-sidebar.theme-dark .text-xs {
    color: rgba(255,255,255,0.72) !important;
}
.node-details-sidebar.theme-dark pre,
.node-details-sidebar.theme-dark summary {
    color: #ffffff !important;
}




/* collapsed sidebar: narrow tab and reduced margin */
.with-node-sidebar-collapsed {
    transition: margin 180ms ease;
    margin-right: 56px; /* narrow tab width */
}
.node-details-sidebar.collapsed {
    width: 56px !important;
    padding: 8px !important;
    overflow: visible;
}
.node-details-sidebar.collapsed .panel-header > strong,
.node-details-sidebar.collapsed .text-xs,
.node-details-sidebar.collapsed .text-small,
.node-details-sidebar.collapsed details,
.node-details-sidebar.collapsed .sidebar-body {
    display: none !important;
}
.sidebar-collapse-toggle {
    position: absolute;
    left: -3em;
    top: 14%;
    transform: translateY(-50%);
    z-index: 100001;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-collapse-toggle .v-btn {
    width: 40px;
    height: 40px;
    min-width: 40px;
    border-radius: 999px;
    background: rgba(0,0,0,0.06);
    box-shadow: 0 6px 18px rgba(2,6,23,0.18);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: inherit;
    border: 1px solid rgba(0,0,0,0.06);
}

/* When `.no-animations` is set on the root, suppress transitions for diagnostic clicks */
.no-animations .node-group,
.no-animations .child-node,
.no-animations .viewport-group,
.no-animations .edges line,

/* Context menu (Vuetify) custom styling for node context */
.node-context-menu-v {
    background: var(--v-theme-surface, rgba(11,16,41,0.98));
    border-radius: 10px;
    padding: 6px 8px;
    box-shadow: 0 10px 30px rgba(2,6,23,0.6);
    color: var(--v-theme-on-surface, #e6eef8);
    min-width: 260px;
    max-width: 340px;
}
.node-context-menu-v .v-list {
    padding: 4px 0;
}
.node-context-item {
    border-radius: 8px;
    padding: 6px 8px;
    transition: background-color 140ms ease, transform 120ms ease;
}
.node-context-item {
    display: flex;
    align-items: center;
}
.node-context-item:hover {
    background: rgba(255,255,255,0.03);
}
.node-context-item .v-list-item-icon {
    color: rgba(255,255,255,0.9);
    min-width: 34px;
    display: flex;
    align-items: center;
    margin-right: 10px;
}
.node-context-item .v-list-item-title {
    color: var(--v-theme-on-surface, #e6eef8);
    font-weight: 600;
    font-size: 13px;
}
.node-context-menu-v .text-error { color: #ff8a80 !important; font-weight:700 }
.no-animations .edge-line,
.no-animations .node-circle,
.no-animations .child-iridescence,
.no-animations .node-reflection {
    transition: none !important;
    animation: none !important;
}

/* Dialog theme variants */
.dialog-dark {
    background: linear-gradient(180deg, rgba(6,10,25,0.95), rgba(11,16,41,0.98));
    color: var(--v-theme-on-surface, #e6eef8);
    border-radius: 12px;
    box-shadow: 0 18px 50px rgba(2,6,23,0.7);
}
.dialog-light {
    background: linear-gradient(180deg, #ffffff, #f6fbff);
    color: #0b2233;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(8,12,30,0.08);
}

.dialog-dark .v-card-title,
.dialog-light .v-card-title {
    padding: 14px 18px;
    font-size: 16px;
    font-weight: 700;
}

.dialog-dark .v-card-text,
.dialog-light .v-card-text {
    padding: 12px 18px 18px 18px;
}

/* Integrated SVG title styles */
.svg-title-fo {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    pointer-events: none; /* avoid intercepting map interactions */
}
.svg-title-text {
    pointer-events: auto; /* allow hover tooltip if needed */
    background: rgba(255,255,255,0.04);
    color: #ffffff;
    padding: 8px 14px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    box-shadow: 0 8px 30px rgba(2,6,23,0.45);
    backdrop-filter: blur(6px);
}
.svg-title-line {
    line-height: 1.05;
}
.svg-title-text .svg-title-line:first-child {
    font-size: 14px;
    font-weight: 800;
}
.svg-title-text .svg-title-line:nth-child(2) {
    font-size: 12px;
    font-weight: 600;
    opacity: 0.92;
}
.svg-title-text .svg-title-line:nth-child(3) {
    font-size: 11px;
    font-weight: 600;
    opacity: 0.9;
}

</style>
