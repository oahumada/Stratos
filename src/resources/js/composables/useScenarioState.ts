/**
 * useScenarioState.ts
 *
 * Centraliza toda la gestión de estado reactivo para el diagrama de escenarios.
 * Incluye nodes (capabilities, competencies, skills), UI state (dialogs, sidebar),
 * y formularios editables.
 */

import { ref, reactive, computed, Ref } from 'vue';
import type { NodeItem, Edge } from '@/types/brain';

export interface ScenarioStateOptions {
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
}

export function useScenarioState(options?: ScenarioStateOptions) {
  // ==================== NODES & EDGES ====================
  const nodes = ref<Array<NodeItem>>([]);
  const edges = ref<Array<Edge>>([]);
  const childNodes = ref<Array<any>>([]);
  const childEdges = ref<Array<Edge>>([]);
  const grandChildNodes = ref<Array<any>>([]);
  const grandChildEdges = ref<Array<Edge>>([]);
  const scenarioEdges = ref<Array<Edge>>([]);
  const scenarioNode = ref<any>(null);

  // ==================== FOCUS & SELECTION ====================
  const focusedNode = ref<NodeItem | null>(null);
  const selectedChild = ref<any>(null);
  const selectedSkillDetail = ref<any>(null);
  const contextMenuTarget = ref<any>(null);

  // ==================== UI STATE ====================
  const loaded = ref(false);
  const showSidebar = ref(false);
  const nodeSidebarCollapsed = ref(false);
  const sidebarTheme = ref<'light' | 'dark'>('light');
  const contextMenuVisible = ref(false);
  const contextMenuLeft = ref(0);
  const contextMenuTop = ref(0);
  const contextMenuIsChild = ref(false);
  const tooltipX = ref(0);
  const tooltipY = ref(0);
  const childEdgeMode = ref(2); // 0=offset, 1=gap-large, 2=curve, 3=spread
  const noAnimations = ref(false);
  const positionsDirty = ref(false);
  const suppressWatcherLayout = ref(false);

  // ==================== DIALOG STATES ====================
  const createModalVisible = ref(false);
  const createCompDialogVisible = ref(false);
  const addExistingCompDialogVisible = ref(false);
  const createSkillDialogVisible = ref(false);
  const selectSkillDialogVisible = ref(false);
  const skillDetailDialogVisible = ref(false);
  const followScenario = ref<boolean>(false);

  // ==================== LOADING STATES ====================
  const creating = ref(false);
  const creatingComp = ref(false);
  const savingNode = ref(false);
  const savingSkill = ref(false);
  const attachingSkill = ref(false);
  const savingSkillDetail = ref(false);
  const loadingSkills = ref(false);

  // ==================== FORM: CREATE CAPABILITY ====================
  const newCapName = ref('');
  const newCapDescription = ref('');
  const newCapType = ref('');
  const newCapCategory = ref('');
  const newCapImportance = ref<number>(3);
  const pivotStrategicRole = ref('target');
  const pivotStrategicWeight = ref<number | undefined>(10);
  const pivotPriority = ref<number | undefined>(1);
  const pivotRationale = ref('');
  const pivotRequiredLevel = ref<number | undefined>(3);
  const pivotIsCritical = ref(false);

  // ==================== FORM: EDIT CAPABILITY ====================
  const editCapName = ref('');
  const editCapDescription = ref('');
  const editCapImportance = ref<number | undefined>(undefined);
  const editCapLevel = ref<number | null>(null);
  const editCapType = ref('');
  const editCapCategory = ref('');
  const editPivotStrategicRole = ref('target');
  const editPivotStrategicWeight = ref<number | undefined>(10);
  const editPivotPriority = ref<number | undefined>(1);
  const editPivotRationale = ref('');
  const editPivotRequiredLevel = ref<number | undefined>(3);
  const editPivotIsCritical = ref(false);

  // ==================== FORM: CREATE COMPETENCY ====================
  const newCompName = ref('');
  const newCompDescription = ref('');
  const newCompReadiness = ref<number | undefined>(undefined);
  const newCompSkills = ref('');
  const addExistingSelection = ref<number | null>(null);

  // ==================== FORM: EDIT COMPETENCY ====================
  const editChildName = ref('');
  const editChildDescription = ref('');
  const editChildReadiness = ref<number | null>(null);
  const editChildSkills = ref('');
  const editChildPivotStrategicWeight = ref<number | undefined>(10);
  const editChildPivotPriority = ref<number | undefined>(1);
  const editChildPivotRequiredLevel = ref<number | undefined>(3);
  const editChildPivotIsCritical = ref(false);
  const editChildPivotRationale = ref('');

  // ==================== FORM: CREATE SKILL ====================
  const newSkillName = ref('');
  const newSkillCategory = ref('');
  const newSkillDescription = ref('');
  const skillCreationError = ref<string | null>(null);
  const skillCreationSuccess = ref<string | null>(null);
  const selectedSkillForDeletion = ref<any>(null);
  const selectedSkillId = ref<number | null>(null);

  // ==================== FORM: EDIT SKILL ====================
  const skillEditName = ref('');
  const skillEditDescription = ref('');
  const skillEditCategory = ref('technical');
  const skillEditComplexityLevel = ref('tactical');
  const skillEditScopeType = ref('domain');
  const skillEditDomainTag = ref('');
  const skillEditIsCritical = ref(false);

  // ==================== FORM: SKILL PIVOT (capability_competencies) ====================
  const skillPivotRequiredLevel = ref<number | undefined>(undefined);
  const skillPivotPriority = ref<number | undefined>(undefined);
  const skillPivotWeight = ref<number | undefined>(undefined);
  const skillPivotRationale = ref('');
  const skillPivotIsRequired = ref(false);

  // ==================== DATA FOR DEBUG ====================
  const capabilityTreeRaw = ref<any | null>(null);
  const showScenarioRaw = ref(false);

  // ==================== DRAG & PAN ====================
  const dragging = ref<any>(null);
  const dragOffset = ref({ x: 0, y: 0 });
  const panelDragging = ref(false);
  const panelDragOffset = ref({ x: 0, y: 0 });
  const viewX = ref(0);
  const viewY = ref(0);
  const viewScale = ref(1);

  // ==================== DIMENSIONS ====================
  const width = ref(900);
  const height = ref(600);

  // ==================== FORM SCROLL ====================
  const editFormScrollEl = ref<HTMLElement | null>(null);
  const editFormScrollPercent = ref<number>(0);

  // ==================== STORAGE & UI ====================
  const savedFocusedNodeId = ref<number | null>(null);
  const availableCapabilities = ref<any[]>([]);
  const availableExistingCompetencies = ref<any[]>([]);
  const availableSkills = ref<any[]>([]);

  // ==================== COMPUTED ====================
  const displayNode = computed(() => selectedChild.value ?? focusedNode.value);

  const dialogThemeClass = computed(() => (sidebarTheme.value === 'dark' ? 'dialog-dark' : 'dialog-light'));

  const breadcrumbTitle = computed(() => {
    const parts: string[] = [];
    try {
      const sName = options?.scenario?.name || '—';
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
      return `Escenario: ${options?.scenario?.name || '—'}`;
    }
    return parts.join('  ›  ');
  });

  const breadcrumbParts = computed(() => {
    const parts: string[] = [];
    try {
      const sName = options?.scenario?.name || '—';
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
      return [`Escenario: ${options?.scenario?.name || '—'}`];
    }
    return parts;
  });

  const nodeSidebarVisible = computed(() => !nodeSidebarCollapsed.value);

  const viewportStyle = computed(() => ({
    transform: `translate(${viewX.value}px, ${viewY.value}px) scale(${viewScale.value})`,
    transformOrigin: '0 0',
  }));

  // ==================== RESET FUNCTIONS ====================
  function resetCreateCapForm() {
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
  }

  function resetCompetencyForm() {
    newCompName.value = '';
    newCompDescription.value = '';
    newCompReadiness.value = undefined;
    newCompSkills.value = '';
  }

  function resetSkillForm() {
    newSkillName.value = '';
    newSkillCategory.value = '';
    newSkillDescription.value = '';
    skillCreationError.value = null;
    skillCreationSuccess.value = null;
    selectedSkillForDeletion.value = null;
    selectedSkillId.value = null;
  }

  function toggleSidebar() {
    showSidebar.value = !showSidebar.value;
  }

  function toggleNodeSidebarCollapse() {
    nodeSidebarCollapsed.value = !nodeSidebarCollapsed.value;
  }

  function toggleSidebarTheme() {
    sidebarTheme.value = sidebarTheme.value === 'light' ? 'dark' : 'light';
  }

  function nextChildEdgeMode() {
    childEdgeMode.value = (childEdgeMode.value + 1) % 4; // 0..3
  }

  function resetViewport() {
    viewX.value = 0;
    viewY.value = 0;
    viewScale.value = 1;
  }

  return {
    // NODES & EDGES
    nodes,
    edges,
    childNodes,
    childEdges,
    grandChildNodes,
    grandChildEdges,
    scenarioEdges,
    scenarioNode,

    // FOCUS & SELECTION
    focusedNode,
    selectedChild,
    selectedSkillDetail,
    contextMenuTarget,

    // UI STATE
    loaded,
    showSidebar,
    nodeSidebarCollapsed,
    sidebarTheme,
    contextMenuVisible,
    contextMenuLeft,
    contextMenuTop,
    contextMenuIsChild,
    tooltipX,
    tooltipY,
    childEdgeMode,
    noAnimations,
    positionsDirty,
    suppressWatcherLayout,

    // DIALOG STATES
    createModalVisible,
    createCompDialogVisible,
    addExistingCompDialogVisible,
    createSkillDialogVisible,
    selectSkillDialogVisible,
    skillDetailDialogVisible,
    followScenario,

    // LOADING STATES
    creating,
    creatingComp,
    savingNode,
    savingSkill,
    attachingSkill,
    savingSkillDetail,
    loadingSkills,

    // FORM: CREATE CAPABILITY
    newCapName,
    newCapDescription,
    newCapType,
    newCapCategory,
    newCapImportance,
    pivotStrategicRole,
    pivotStrategicWeight,
    pivotPriority,
    pivotRationale,
    pivotRequiredLevel,
    pivotIsCritical,

    // FORM: EDIT CAPABILITY
    editCapName,
    editCapDescription,
    editCapImportance,
    editCapLevel,
    editCapType,
    editCapCategory,
    editPivotStrategicRole,
    editPivotStrategicWeight,
    editPivotPriority,
    editPivotRationale,
    editPivotRequiredLevel,
    editPivotIsCritical,

    // FORM: CREATE COMPETENCY
    newCompName,
    newCompDescription,
    newCompReadiness,
    newCompSkills,
    addExistingSelection,

    // FORM: EDIT COMPETENCY
    editChildName,
    editChildDescription,
    editChildReadiness,
    editChildSkills,
    editChildPivotStrategicWeight,
    editChildPivotPriority,
    editChildPivotRequiredLevel,
    editChildPivotIsCritical,
    editChildPivotRationale,

    // FORM: CREATE SKILL
    newSkillName,
    newSkillCategory,
    newSkillDescription,
    skillCreationError,
    skillCreationSuccess,
    selectedSkillForDeletion,
    selectedSkillId,

    // FORM: EDIT SKILL
    skillEditName,
    skillEditDescription,
    skillEditCategory,
    skillEditComplexityLevel,
    skillEditScopeType,
    skillEditDomainTag,
    skillEditIsCritical,

    // FORM: SKILL PIVOT
    skillPivotRequiredLevel,
    skillPivotPriority,
    skillPivotWeight,
    skillPivotRationale,
    skillPivotIsRequired,

    // DATA FOR DEBUG
    capabilityTreeRaw,
    showScenarioRaw,

    // DRAG & PAN
    dragging,
    dragOffset,
    panelDragging,
    panelDragOffset,
    viewX,
    viewY,
    viewScale,

    // DIMENSIONS
    width,
    height,

    // FORM SCROLL
    editFormScrollEl,
    editFormScrollPercent,

    // STORAGE & UI
    savedFocusedNodeId,
    availableCapabilities,
    availableExistingCompetencies,
    availableSkills,

    // COMPUTED
    displayNode,
    dialogThemeClass,
    breadcrumbTitle,
    breadcrumbParts,
    nodeSidebarVisible,
    viewportStyle,

    // HELPERS
    resetCreateCapForm,
    resetCompetencyForm,
    resetSkillForm,
    toggleSidebar,
    toggleNodeSidebarCollapse,
    toggleSidebarTheme,
    nextChildEdgeMode,
    resetViewport,
  };
}
