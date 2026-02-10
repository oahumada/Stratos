import { describe, it, expect, beforeEach } from 'vitest';
import { useScenarioState } from '../useScenarioState';

describe('useScenarioState', () => {
  let state: ReturnType<typeof useScenarioState>;

  beforeEach(() => {
    state = useScenarioState();
  });

  describe('initialization', () => {
    it('initializes with empty nodes and edges', () => {
      expect(state.nodes.value).toEqual([]);
      expect(state.edges.value).toEqual([]);
      expect(state.childNodes.value).toEqual([]);
      expect(state.grandChildNodes.value).toEqual([]);
    });

    it('initializes UI state correctly', () => {
      expect(state.loaded.value).toBe(false);
      expect(state.showSidebar.value).toBe(false);
      expect(state.nodeSidebarCollapsed.value).toBe(false);
      expect(state.sidebarTheme.value).toBe('light');
      expect(state.contextMenuVisible.value).toBe(false);
    });

    it('initializes with null focus and selection', () => {
      expect(state.focusedNode.value).toBeNull();
      expect(state.selectedChild.value).toBeNull();
      expect(state.selectedSkillDetail.value).toBeNull();
    });
  });

  describe('node management', () => {
    it('can update nodes array', () => {
      const mockNode = { id: 1, type: 'capability', name: 'Test' };
      state.nodes.value = [mockNode];
      expect(state.nodes.value).toHaveLength(1);
      expect(state.nodes.value[0].id).toBe(1);
    });

    it('can set focused node', () => {
      const mockNode = { id: 1, type: 'capability', name: 'Test' };
      state.focusedNode.value = mockNode;
      expect(state.focusedNode.value).toEqual(mockNode);
    });

    it('can update child nodes and edges', () => {
      const mockChildNode = { id: 2, type: 'competency', name: 'Child' };
      const mockEdge = { source: 1, target: 2 };
      
      state.childNodes.value = [mockChildNode];
      state.childEdges.value = [mockEdge];
      
      expect(state.childNodes.value).toHaveLength(1);
      expect(state.childEdges.value).toHaveLength(1);
    });

    it('can update grandchild nodes and edges', () => {
      const mockGrandChild = { id: 3, type: 'skill', name: 'Grandchild' };
      const mockEdge = { source: 2, target: 3 };
      
      state.grandChildNodes.value = [mockGrandChild];
      state.grandChildEdges.value = [mockEdge];
      
      expect(state.grandChildNodes.value).toHaveLength(1);
      expect(state.grandChildEdges.value).toHaveLength(1);
    });
  });

  describe('UI state management', () => {
    it('can toggle sidebar visibility', () => {
      expect(state.showSidebar.value).toBe(false);
      state.showSidebar.value = true;
      expect(state.showSidebar.value).toBe(true);
    });

    it('can toggle sidebar collapse', () => {
      expect(state.nodeSidebarCollapsed.value).toBe(false);
      state.nodeSidebarCollapsed.value = true;
      expect(state.nodeSidebarCollapsed.value).toBe(true);
    });

    it('can change sidebar theme', () => {
      state.sidebarTheme.value = 'dark';
      expect(state.sidebarTheme.value).toBe('dark');
      state.sidebarTheme.value = 'light';
      expect(state.sidebarTheme.value).toBe('light');
    });

    it('can manage context menu state', () => {
      state.contextMenuVisible.value = true;
      state.contextMenuLeft.value = 100;
      state.contextMenuTop.value = 200;
      
      expect(state.contextMenuVisible.value).toBe(true);
      expect(state.contextMenuLeft.value).toBe(100);
      expect(state.contextMenuTop.value).toBe(200);
    });
  });

  describe('form field management', () => {
    it('has resetCreateCapForm method', () => {
      expect(typeof state.resetCreateCapForm).toBe('function');
    });

    it('has resetCompetencyForm method', () => {
      expect(typeof state.resetCompetencyForm).toBe('function');
    });

    it('has resetSkillForm method', () => {
      expect(typeof state.resetSkillForm).toBe('function');
    });
  });

  describe('computed properties', () => {
    it('has breadcrumbParts computed property', () => {
      expect(state.breadcrumbParts).toBeDefined();
      expect(Array.isArray(state.breadcrumbParts.value)).toBe(true);
    });

    it('has displayNode computed property', () => {
      expect(state.displayNode).toBeDefined();
    });

    it('has nodeSidebarVisible computed property', () => {
      expect(state.nodeSidebarVisible).toBeDefined();
      expect(typeof state.nodeSidebarVisible.value).toBe('boolean');
    });

    it('has viewportStyle computed property', () => {
      expect(state.viewportStyle).toBeDefined();
    });

    it('has dialogThemeClass computed property', () => {
      expect(state.dialogThemeClass).toBeDefined();
    });
  });

  describe('with initialization options', () => {
    it('initializes with scenario data', () => {
      const options = {
        scenario: {
          id: 1,
          name: 'Test Scenario',
          description: 'Test description',
          status: 'draft',
        },
      };
      
      const stateWithOptions = useScenarioState(options);
      expect(stateWithOptions.scenarioNode.value).toBeNull(); // Set separately
    });
  });
});
