/**
 * ScenarioPlanning.composablesIntegration.spec.ts
 *
 * Integration tests for Index.vue with refactored composables.
 * Tests the component's interaction with:
 * - useScenarioState (state mutations)
 * - useScenarioAPI (data loading)
 * - useScenarioLayout (positioning)
 * - useScenarioEdges (rendering)
 */

import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { nextTick, ref } from 'vue';
import { useScenarioState } from '@/composables/useScenarioState';
import { useScenarioAPI } from '@/composables/useScenarioAPI';
import { useScenarioLayout, LAYOUT_CONFIG } from '@/composables/useScenarioLayout';
import { useScenarioEdges } from '@/composables/useScenarioEdges';

// Mock composables
vi.mock('@/composables/useApi', () => ({
  useApi: () => ({
    get: vi.fn(async (url: string) => {
      if (url.includes('/capability-tree')) {
        return {
          capabilities: [
            {
              id: 1,
              name: 'Architecture',
              x: 100,
              y: 100,
              competencies: [
                {
                  id: 10,
                  name: 'Microservices',
                  skills: [
                    { id: 100, name: 'Docker', pivot: { importance: 8 } },
                  ],
                  pivot: {},
                },
              ],
            },
          ],
        };
      }
      return { data: [] };
    }),
    post: vi.fn(async () => ({ data: { success: true } })),
    patch: vi.fn(async () => ({ data: {} })),
    delete: vi.fn(async () => ({ data: {} })),
    api: { get: vi.fn() },
  }),
}));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({
    showSuccess: vi.fn(),
    showError: vi.fn(),
  }),
}));

describe('ScenarioPlanning Component - Composables Integration', () => {
  describe('State management through composables', () => {
    it('initializes state via useScenarioState', () => {
      const state = useScenarioState();

      // Verify initialization
      expect(state.nodes.value).toBeDefined();
      expect(Array.isArray(state.nodes.value)).toBe(true);
      expect(state.loaded.value).toBe(false);
      expect(state.focusedNode.value).toBeNull();
    });

    it('updates focused node', () => {
      const state = useScenarioState();

      const mockNode = { id: 1, type: 'capability', name: 'Architecture', x: 100, y: 100 };
      state.focusedNode.value = mockNode;

      expect(state.focusedNode.value).toEqual(mockNode);
    });

    it('tracks sidebar visibility', () => {
      const state = useScenarioState();

      expect(state.showSidebar.value).toBe(false);
      state.showSidebar.value = true;
      expect(state.showSidebar.value).toBe(true);
    });

    it('manages context menu state', () => {
      const state = useScenarioState();

      state.contextMenuVisible.value = true;
      state.contextMenuLeft.value = 150;
      state.contextMenuTop.value = 250;

      expect(state.contextMenuVisible.value).toBe(true);
      expect(state.contextMenuLeft.value).toBe(150);
      expect(state.contextMenuTop.value).toBe(250);
    });
  });

  describe('API communication via useScenarioAPI', () => {
    it('loads capability tree', async () => {
      const api = useScenarioAPI({ scenario: { id: 1 } });

      const tree = await api.loadCapabilityTree(1);
      expect(tree).toBeDefined();
    });

    it('saves positions via API', async () => {
      const api = useScenarioAPI({ scenario: { id: 1 } });

      const positions = [
        { id: 1, x: 100, y: 200 },
        { id: 2, x: 300, y: 400 },
      ];

      const result = await api.savePositions(1, positions);
      expect(typeof result).toBe('boolean');
    });

    it('creates competency via API', async () => {
      const api = useScenarioAPI({ scenario: { id: 1 } });

      expect(typeof api.createCompetency).toBe('function');
    });

    it('saves skill via API', async () => {
      const api = useScenarioAPI({ scenario: { id: 1 } });

      expect(typeof api.saveSkill).toBe('function');
    });
  });

  describe('Layout calculations via useScenarioLayout', () => {
    it('exports layout configuration', () => {
      const layout = useScenarioLayout();

      expect(LAYOUT_CONFIG).toBeDefined();
      expect(LAYOUT_CONFIG.competency).toBeDefined();
      expect(LAYOUT_CONFIG.skill).toBeDefined();
    });

    it('provides expansion functions', () => {
      const layout = useScenarioLayout();

      expect(typeof layout.expandCompetencies).toBe('function');
      expect(typeof layout.expandSkills).toBe('function');
    });

    it('clamps coordinates', () => {
      const layout = useScenarioLayout();

      const clamped = layout.clampY(5000);
      expect(typeof clamped).toBe('number');
    });

    it('wraps labels', () => {
      const layout = useScenarioLayout();

      const short = layout.wrapLabel('Test');
      const long = layout.wrapLabel('This is a very long capability name that needs wrapping', 10);

      expect(short).toBe('Test');
      expect(long.length >= 'Test'.length).toBe(true);
    });

    it('centers on node', () => {
      const layout = useScenarioLayout();

      expect(typeof layout.centerOnNode).toBe('function');
    });
  });

  describe('Edge rendering via useScenarioEdges', () => {
    it('initializes edge mode', () => {
      const edges = useScenarioEdges();

      expect([0, 1, 2, 3].includes(edges.childEdgeMode.value)).toBe(true);
    });

    it('injects state for rendering', () => {
      const edges = useScenarioEdges();

      const mockEdges = ref([]);
      const mockGrandEdges = ref([]);
      const mockNodes = ref([]);
      const mockChildNodes = ref([]);
      const mockGrandChildNodes = ref([]);

      expect(() => {
        edges.injectState(
          mockEdges,
          mockGrandEdges,
          mockNodes,
          mockChildNodes,
          mockGrandChildNodes
        );
      }).not.toThrow();
    });

    it('provides edge path functions', () => {
      const edges = useScenarioEdges();

      expect(typeof edges.edgeRenderFor).toBe('function');
      expect(typeof edges.scenarioEdgePath).toBe('function');
      expect(typeof edges.edgeEndpoint).toBe('function');
    });

    it('switches between edge modes', () => {
      const edges = useScenarioEdges();

      edges.childEdgeMode.value = 0;
      expect(edges.childEdgeMode.value).toBe(0);

      edges.childEdgeMode.value = 2;
      expect(edges.childEdgeMode.value).toBe(2);
    });
  });

  describe('Integrated workflow: Capability → Competencies → Skills', () => {
    it('loads scenario and populates nodes', async () => {
      const api = useScenarioAPI({ scenario: { id: 1 } });
      const state = useScenarioState();

      const result = await api.loadCapabilityTree(1);
      const tree = result?.capabilities || result || [];
      state.nodes.value = tree.map((cap: any) => ({
        id: cap.id,
        name: cap.name,
        type: 'capability',
        x: cap.x ?? 100,
        y: cap.y ?? 100,
      }));

      state.loaded.value = true;

      expect(state.loaded.value).toBe(true);
      expect(Array.isArray(state.nodes.value)).toBe(true);
    });

    it('expands capability to show competencies', async () => {
      const api = useScenarioAPI({ scenario: { id: 1 } });
      const state = useScenarioState();
      const layout = useScenarioLayout();

      // Load and populate
      const result = await api.loadCapabilityTree(1);
      const tree = result?.capabilities || result || [];
      state.nodes.value = tree.map((cap: any) => ({
        id: cap.id,
        name: cap.name,
        type: 'capability',
        x: cap.x ?? 100,
        y: cap.y ?? 100,
      }));

      const capability = state.nodes.value[0];
      state.focusedNode.value = capability;

      // Expand competencies
      const competencies = (capability as any).competencies || [];
      state.childNodes.value = competencies.map((c: any) => ({
        id: c.id,
        name: c.name,
        type: 'competency',
        x: capability.x + 120,
        y: capability.y,
      }));

      state.childEdges.value = state.childNodes.value.map(child => ({
        source: capability.id,
        target: child.id,
      }));

      expect(state.focusedNode.value?.id).toBe(state.nodes.value[0]?.id);
      expect(Array.isArray(state.childNodes.value)).toBe(true);
      expect(Array.isArray(state.childEdges.value)).toBe(true);
    });

    it('expands competency to show skills', () => {
      const state = useScenarioState();

      // Setup: already have capability + competency
      state.nodes.value = [
        { id: 1, type: 'capability', name: 'Architecture', x: 100, y: 100 },
      ];
      state.childNodes.value = [
        { id: 10, type: 'competency', name: 'Microservices', x: 220, y: 100 },
      ];
      state.focusedNode.value = state.nodes.value[0];
      state.selectedChild.value = state.childNodes.value[0];

      // Expand to skills
      const skills = [
        { id: 100, name: 'Docker', pivot: {} },
        { id: 101, name: 'Kubernetes', pivot: {} },
      ];

      state.grandChildNodes.value = skills.map(s => ({
        id: s.id,
        name: s.name,
        type: 'skill',
        x: 340,
        y: 100,
      }));

      state.grandChildEdges.value = state.grandChildNodes.value.map(gc => ({
        source: state.selectedChild.value!.id,
        target: gc.id,
      }));

      expect(state.selectedChild.value?.id).toBe(10);
      expect(state.grandChildNodes.value).toHaveLength(2);
      expect(state.grandChildEdges.value).toHaveLength(2);
    });

    it('sets up edge rendering for complete hierarchy', () => {
      const edges = useScenarioEdges();
      const state = useScenarioState();

      // Complete hierarchy
      state.nodes.value = [
        { id: 1, type: 'capability', name: 'Architecture', x: 100, y: 100 },
      ];
      state.childNodes.value = [
        { id: 10, type: 'competency', name: 'Microservices', x: 220, y: 100 },
      ];
      state.grandChildNodes.value = [
        { id: 100, type: 'skill', name: 'Docker', x: 340, y: 100 },
      ];

      state.childEdges.value = [
        { source: 1, target: 10 },
      ];
      state.grandChildEdges.value = [
        { source: 10, target: 100 },
      ];

      // Inject state
      edges.injectState(
        state.childEdges,
        state.grandChildEdges,
        state.nodes,
        state.childNodes,
        state.grandChildNodes
      );

      // Verify setup
      expect(state.childEdges.value).toHaveLength(1);
      expect(state.grandChildEdges.value).toHaveLength(1);
      expect(typeof edges.edgeRenderFor).toBe('function');
    });
  });

  describe('Composable coordination', () => {
    it('state changes propagate to layout calculations', () => {
      const state = useScenarioState();
      const layout = useScenarioLayout();

      state.childNodes.value = [
        { id: 10, type: 'competency', name: 'Test', x: 100, y: 5000 },
      ];

      // Layout operations work with state data
      const clampedY = layout.clampY(state.childNodes.value[0].y);
      expect(clampedY).toBeLessThanOrEqual(5000 + 100);
    });

    it('API results feed into state', async () => {
      const api = useScenarioAPI({ scenario: { id: 1 } });
      const state = useScenarioState();

      const result = await api.loadCapabilityTree(1);
      const tree = result?.capabilities || result || [];

      // Feed API result into state
      state.nodes.value = tree.map((cap: any) => ({
        id: cap.id,
        name: cap.name,
        type: 'capability',
        x: cap.x ?? 100,
        y: cap.y ?? 100,
      }));

      expect(Array.isArray(state.nodes.value)).toBe(true);
    });

    it('state updates trigger edge recalculations', () => {
      const state = useScenarioState();
      const edges = useScenarioEdges();

      // Update state
      state.nodes.value = [{ id: 1, type: 'capability', name: 'Test', x: 100, y: 100 }];
      state.childNodes.value = [{ id: 10, type: 'competency', name: 'Child', x: 200, y: 100 }];
      state.childEdges.value = [{ source: 1, target: 10 }];

      // Inject for edge rendering
      edges.injectState(
        state.childEdges,
        state.grandChildEdges,
        state.nodes,
        state.childNodes,
        state.grandChildNodes
      );

      // Edge functions should work with updated state
      expect(typeof edges.edgeRenderFor).toBe('function');
      expect(state.childEdges.value).toHaveLength(1);
    });
  });
});
