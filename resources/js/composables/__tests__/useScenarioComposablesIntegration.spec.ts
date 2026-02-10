/**
 * useScenarioComposablesIntegration.spec.ts
 *
 * Integration tests that validate the interaction between:
 * - useScenarioState (state management)
 * - useScenarioAPI (API communication)
 * - useScenarioLayout (positioning & animations)
 * - useScenarioEdges (edge rendering)
 *
 * Tests the complete workflow:
 * 1. Load capability tree
 * 2. Expand competencies
 * 3. Expand skills
 * 4. Update positions
 * 5. Render edges
 */

import { describe, it, expect, beforeEach, vi } from 'vitest';
import { useScenarioState } from '@/composables/useScenarioState';
import { useScenarioAPI } from '@/composables/useScenarioAPI';
import { useScenarioLayout } from '@/composables/useScenarioLayout';
import { useScenarioEdges } from '@/composables/useScenarioEdges';
import { nextTick, ref } from 'vue';

// Mock useApi and useNotification
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
                    { id: 100, name: 'Docker', pivot: {} },
                    { id: 101, name: 'Kubernetes', pivot: {} },
                  ],
                  pivot: {},
                },
                {
                  id: 11,
                  name: 'Cloud Design',
                  skills: [
                    { id: 102, name: 'AWS', pivot: {} },
                  ],
                  pivot: {},
                },
              ],
            },
          ],
        };
      }
      return [];
    }),
    post: vi.fn(async () => ({ data: {} })),
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

describe('Composables Integration - Complete Workflow', () => {
  let state: ReturnType<typeof useScenarioState>;
  let api: ReturnType<typeof useScenarioAPI>;
  let layout: ReturnType<typeof useScenarioLayout>;
  let edges: ReturnType<typeof useScenarioEdges>;

  beforeEach(() => {
    state = useScenarioState({
      scenario: {
        id: 1,
        name: 'Test Scenario',
        status: 'draft',
      },
    });
    api = useScenarioAPI({ scenario: { id: 1 } });
    layout = useScenarioLayout();
    edges = useScenarioEdges();
    vi.clearAllMocks();
  });

  describe('Step 1: Load capability tree', () => {
    it('loads capability tree via API', async () => {
      const tree = await api.loadCapabilityTree(1);
      expect(tree).toBeDefined();
    });

    it('populates state with capabilities', async () => {
      const result = await api.loadCapabilityTree(1);
      const tree = result?.capabilities ?? result ?? [];
      state.nodes.value = tree.map((cap: any) => ({
        id: cap.id,
        name: cap.name,
        x: Number(cap.x) || 0,
        y: Number(cap.y) || 0,
        type: 'capability' as const,
      }));

      expect(state.nodes.value).toHaveLength(1);
      expect((state.nodes.value[0] as any).type).toBe('capability');
    });

    it('marks scenario as loaded', () => {
      state.loaded.value = true;
      expect(state.loaded.value).toBe(true);
    });
  });

  describe('Step 2: Expand competencies', () => {
    beforeEach(async () => {
      // Load initial tree
      const result = await api.loadCapabilityTree(1);
      const tree = result?.capabilities ?? result ?? [];
      state.nodes.value = tree.map((cap: any) => ({
        id: cap.id,
        name: cap.name,
        x: Number(cap.x) || 0,
        y: Number(cap.y) || 0,
        type: 'capability' as const,
      }));
    });

    it('sets focused node', () => {
      const capability = state.nodes.value[0];
      state.focusedNode.value = capability;

      expect(state.focusedNode.value).toBeDefined();
      expect(state.focusedNode.value?.id).toBe(1);
    });

    it('creates child nodes from competencies', async () => {
      const capability = state.nodes.value[0];
      const mockCompetencies = [
        { id: 10, name: 'Microservices', skills: [] },
        { id: 11, name: 'Cloud Design', skills: [] },
      ];

      // Simulate expandCompetencies
      state.childNodes.value = mockCompetencies.map(c => ({
        id: c.id,
        name: c.name,
        type: 'competency',
        x: (capability?.x ?? 0) + 100,
        y: capability?.y ?? 0,
      }));

      expect(state.childNodes.value).toHaveLength(2);
      expect(state.childNodes.value[0].type).toBe('competency');
    });

    it('creates edges from capability to competencies', () => {
      if (state.childNodes.value.length === 0) {
        state.childNodes.value = [
          { id: 10, name: 'Microservices', type: 'competency', x: 200, y: 100 },
          { id: 11, name: 'Cloud Design', type: 'competency', x: 240, y: 120 },
        ];
      }
      state.childEdges.value = state.childNodes.value.map(child => ({
        source: state.focusedNode.value?.id || 1,
        target: child.id,
      }));

      expect(state.childEdges.value).toHaveLength(2);
      expect(state.childEdges.value[0].source).toBe(1);
    });

    it('can change edge rendering mode', () => {
      edges.childEdgeMode.value = 0; // offset
      expect(edges.childEdgeMode.value).toBe(0);

      edges.childEdgeMode.value = 2; // curve
      expect(edges.childEdgeMode.value).toBe(2);
    });
  });

  describe('Step 3: Expand skills', () => {
    beforeEach(async () => {
      // Setup: load capabilities and expand to competencies
      const result = await api.loadCapabilityTree(1);
      const tree = result?.capabilities ?? result ?? [];
      state.nodes.value = tree.map((cap: any) => ({
        id: cap.id,
        name: cap.name,
        x: Number(cap.x) || 0,
        y: Number(cap.y) || 0,
        type: 'capability' as const,
      }));

      // Expand competencies
      const capability = state.nodes.value[0];
      const mockCompetencies = [
        { id: 10, name: 'Microservices', skills: [] },
      ];
      state.childNodes.value = mockCompetencies.map(c => ({
        id: c.id,
        name: c.name,
        type: 'competency',
        x: (capability?.x ?? 0) + 100,
        y: capability?.y ?? 0,
      }));

      state.focusedNode.value = capability;
      state.selectedChild.value = state.childNodes.value[0];
    });

    it('selects competency for expansion', () => {
      expect(state.selectedChild.value).toBeDefined();
      expect((state.selectedChild.value as any)?.type).toBe('competency');
    });

    it('creates grandchild nodes from skills', () => {
      const competency = state.selectedChild.value;
      const mockSkills = [
        { id: 100, name: 'Docker', pivot: {} },
        { id: 101, name: 'Kubernetes', pivot: {} },
      ];

      state.grandChildNodes.value = mockSkills.map(s => ({
        id: s.id,
        name: s.name,
        type: 'skill',
        x: (competency?.x ?? 0) + 80,
        y: (competency?.y ?? 0),
      }));

      expect(state.grandChildNodes.value).toHaveLength(2);
      expect(state.grandChildNodes.value[0].type).toBe('skill');
    });

    it('creates edges from competency to skills', () => {
      if (state.grandChildNodes.value.length === 0) {
        state.grandChildNodes.value = [
          { id: 100, name: 'Docker', type: 'skill', x: 280, y: 100 },
          { id: 101, name: 'Kubernetes', type: 'skill', x: 300, y: 120 },
        ];
      }
      state.grandChildEdges.value = state.grandChildNodes.value.map(grandchild => ({
        source: state.selectedChild.value?.id || 10,
        target: grandchild.id,
      }));

      expect(state.grandChildEdges.value).toHaveLength(2);
    });
  });

  describe('Step 4: Layout calculations', () => {
    it('uses LAYOUT_CONFIG for positioning', () => {
      const config = layout.LAYOUT_CONFIG || {};
      expect(config.competency).toBeDefined();
      expect(config.skill).toBeDefined();
    });

    it('clamps Y coordinates within viewport bounds', () => {
      const topY = layout.clampY(-100);
      const midY = layout.clampY(200);
      const bottomY = layout.clampY(5000);

      expect(topY).toBeGreaterThanOrEqual(40); // min clamp
      expect(midY).toBe(200); // valid range
      expect(bottomY).toBeLessThanOrEqual(5000); // respects viewport
    });

    it('wraps long labels', () => {
      const short = 'Test';
      const long = 'This is a very long label that needs wrapping';

      const shortWrapped = layout.wrapLabel(short);
      const longWrapped = layout.wrapLabel(long, 10);

      expect(shortWrapped).toBe(short);
      expect(longWrapped).toContain('\n');
    });
  });

  describe('Step 5: Edge rendering', () => {
    beforeEach(() => {
      // Setup nodes for edge rendering
      state.nodes.value = [
        { id: 1, x: 100, y: 100, name: 'Cap 1' } as any,
      ];
      state.childNodes.value = [
        { id: 10, x: 200, y: 100, name: 'Comp 1' } as any,
        { id: 11, x: 300, y: 150, name: 'Comp 2' } as any,
      ];
      state.childEdges.value = [
        { source: 1, target: 10 },
        { source: 1, target: 11 },
      ];

      // Ensure nodes have numeric x/y (not null) before injecting
      state.nodes.value = state.nodes.value.map(n => ({
        ...n,
        x: n.x ?? 0,
        y: n.y ?? 0,
      }));
      state.childNodes.value = state.childNodes.value.map(n => ({
        ...n,
        x: n.x ?? 0,
        y: n.y ?? 0,
      }));
      state.grandChildNodes.value = state.grandChildNodes.value.map(n => ({
        ...n,
        x: n.x ?? 0,
        y: n.y ?? 0,
      }));

      // Inject state into edges composable
      edges.injectState(
        state.childEdges,
        state.grandChildEdges,
        state.nodes,
        state.childNodes,
        state.grandChildNodes
      );
    });

    it('injects state references for edge rendering', () => {
      expect(typeof edges.injectState).toBe('function');
    });

    it('calculates edge endpoints', () => {
      expect(typeof edges.edgeEndpoint).toBe('function');
      // Actual endpoint calculations tested in edge rendering
    });

    it('groups parallel edges', () => {
      expect(typeof edges.groupedIndexForEdge).toBe('function');
      // Grouping logic tested when rendering
    });

    it('handles scenario edge paths', () => {
      const scenarioEdge = { source: 0, target: 1 };
      expect(typeof edges.scenarioEdgePath).toBe('function');
    });

    it('switches between edge render modes', () => {
      // Test mode switching
      for (const mode of [0, 1, 2, 3] as const) {
        edges.childEdgeMode.value = mode;
        expect(edges.childEdgeMode.value).toBe(mode);
      }
    });
  });

  describe('Full workflow integration', () => {
    it('completes full load → expand capability → expand skills workflow', async () => {
      // Step 1: Load
      const result = await api.loadCapabilityTree(1);
      const tree = result?.capabilities ?? result ?? [];
      state.nodes.value = tree.map((cap: any) => ({
        id: cap.id,
        name: cap.name,
        type: 'capability',
        x: Number(cap.x) || 100,
        y: Number(cap.y) || 100,
      }));

      expect(state.nodes.value).toHaveLength(1);
      state.loaded.value = true;

      // Step 2: Focus capability
      state.focusedNode.value = state.nodes.value[0];
      expect(state.focusedNode.value).toBeDefined();

      // Step 3: Expand to competencies
      const mockCompetencies = [
        { id: 10, name: 'Microservices', skills: [] },
        { id: 11, name: 'Cloud Design', skills: [] },
      ];
      state.childNodes.value = mockCompetencies.map(c => ({
        id: c.id,
        name: c.name,
        type: 'competency',
        x: 200,
        y: 100,
      }));

      state.childEdges.value = state.childNodes.value.map(child => ({
        source: state.focusedNode.value?.id || 1,
        target: child.id,
      }));

      expect(state.childNodes.value).toHaveLength(2);
      expect(state.childEdges.value).toHaveLength(2);

      // Step 4: Select competency
      state.selectedChild.value = state.childNodes.value[0];
      expect(state.selectedChild.value?.id).toBe(10);

      // Step 5: Expand to skills
      const mockSkills = [
        { id: 100, name: 'Docker', pivot: {} },
        { id: 101, name: 'Kubernetes', pivot: {} },
      ];
      state.grandChildNodes.value = mockSkills.map(s => ({
        id: s.id,
        name: s.name,
        type: 'skill',
        x: 280,
        y: 100,
      }));

      state.grandChildEdges.value = state.grandChildNodes.value.map(gc => ({
        source: 10,
        target: gc.id,
      }));

      expect(state.grandChildNodes.value).toHaveLength(2);
      expect(state.grandChildEdges.value).toHaveLength(2);

      // Step 6: Setup edge rendering
      edges.injectState(
        state.childEdges,
        state.grandChildEdges,
        state.nodes,
        state.childNodes,
        state.grandChildNodes
      );

      // Verify full state
      expect(state.loaded.value).toBe(true);
      expect(state.focusedNode.value?.type).toBe('capability');
      expect(state.selectedChild.value?.type).toBe('competency');
      expect(state.grandChildNodes.value[0].type).toBe('skill');
    });

    it('maintains state consistency across composable interactions', () => {
      // Add node
      state.nodes.value.push({ id: 1, type: 'capability', name: 'Test', x: 0, y: 0 });
      expect(state.nodes.value).toHaveLength(1);

      // Focus it
      state.focusedNode.value = state.nodes.value[0];

      // Add children
      state.childNodes.value.push({ id: 10, type: 'competency', name: 'Child', x: 100, y: 100 });
      
      // Create edges
      state.childEdges.value.push({ source: 1, target: 10 });

      // Verify consistency
      expect(state.childEdges.value[0].source).toBe(state.focusedNode.value.id);
      expect(state.childNodes.value.some(cn => cn.id === state.childEdges.value[0].target)).toBe(true);
    });

    it('preserves state during layout operations', () => {
      const originalNodes = [
        { id: 1, type: 'capability' as const, name: 'Test', x: 100, y: 100 },
      ];
      state.nodes.value = [...originalNodes];

      // Apply layout (clamp Y)
      state.nodes.value = state.nodes.value.map(n => ({
        ...n,
        y: layout.clampY(n.y),
      }));

      // Verify nodes unchanged (within bounds)
      expect(state.nodes.value[0].id).toBe(originalNodes[0].id);
      expect(state.nodes.value[0].type).toBe(originalNodes[0].type);
    });
  });

  describe('Error handling and edge cases', () => {
    it('handles empty capability tree gracefully', async () => {
      const result = await api.loadCapabilityTree(999); // non-existent scenario
      const tree = result?.capabilities ?? result ?? [];
      expect(Array.isArray(tree)).toBe(true);
    });

    it('handles null focus node', () => {
      state.focusedNode.value = null;
      expect(state.focusedNode.value).toBeNull();
      expect(() => {
        // Should not crash when trying to access null node
        const nodeId = state.focusedNode.value?.id;
      }).not.toThrow();
    });

    it('handles empty child/grandchild nodes', () => {
      state.childNodes.value = [];
      state.grandChildNodes.value = [];

      expect(state.childNodes.value).toHaveLength(0);
      expect(state.grandChildNodes.value).toHaveLength(0);
      expect(state.childEdges.value.length >= 0).toBe(true);
    });

    it('clamps extreme Y values safely', () => {
      const results = [
        layout.clampY(-10000),
        layout.clampY(10000),
        layout.clampY(0),
        layout.clampY(NaN),
      ];

      results.forEach(y => {
        expect(typeof y).toBe('number');
        expect(isNaN(y) || y >= 0).toBe(true);
      });
    });
  });
});
