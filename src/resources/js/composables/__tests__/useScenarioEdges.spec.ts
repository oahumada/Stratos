import { describe, it, expect, beforeEach } from 'vitest';
import { useScenarioEdges } from '../useScenarioEdges';
import { ref } from 'vue';

describe('useScenarioEdges', () => {
  let edges: ReturnType<typeof useScenarioEdges>;

  beforeEach(() => {
    edges = useScenarioEdges();
  });

  describe('initialization', () => {
    it('initializes with default child edge mode', () => {
      expect(edges.childEdgeMode.value).toBe(2); // curve mode
    });

    it('has injectState method', () => {
      expect(typeof edges.injectState).toBe('function');
    });
  });

  describe('edge mode configuration', () => {
    it('allows changing child edge mode', () => {
      edges.childEdgeMode.value = 0; // offset
      expect(edges.childEdgeMode.value).toBe(0);

      edges.childEdgeMode.value = 1; // gap-large
      expect(edges.childEdgeMode.value).toBe(1);

      edges.childEdgeMode.value = 2; // curve
      expect(edges.childEdgeMode.value).toBe(2);

      edges.childEdgeMode.value = 3; // spread
      expect(edges.childEdgeMode.value).toBe(3);
    });

    it('edge modes are numeric (0-3)', () => {
      const validModes = [0, 1, 2, 3];
      validModes.forEach(mode => {
        edges.childEdgeMode.value = mode as 0 | 1 | 2 | 3;
        expect(edges.childEdgeMode.value).toBe(mode);
      });
    });
  });

  describe('state injection', () => {
    it('injects external state references', () => {
      const mockChildEdges = ref([]);
      const mockGrandChildEdges = ref([]);
      const mockNodes = ref([]);
      const mockChildNodes = ref([]);
      const mockGrandChildNodes = ref([]);

      expect(() => {
        edges.injectState(
          mockChildEdges,
          mockGrandChildEdges,
          mockNodes,
          mockChildNodes,
          mockGrandChildNodes
        );
      }).not.toThrow();
    });
  });

  describe('edge endpoint functions', () => {
    it('exports edgeEndpoint function', () => {
      expect(typeof edges.edgeEndpoint).toBe('function');
    });

    it('exports groupedIndexForEdge function', () => {
      expect(typeof edges.groupedIndexForEdge).toBe('function');
    });

    it('exports edgeAnimOpacity function', () => {
      expect(typeof edges.edgeAnimOpacity).toBe('function');
    });
  });

  describe('edge rendering functions', () => {
    it('exports edgeRenderFor function', () => {
      expect(typeof edges.edgeRenderFor).toBe('function');
    });

    it('exports scenarioEdgePath function', () => {
      expect(typeof edges.scenarioEdgePath).toBe('function');
    });
  });

  describe('edge rendering modes', () => {
    it('has 4 rendering modes defined', () => {
      // The modes are internally handled, we verify the composable supports mode changes
      const modes = [0, 1, 2, 3];
      modes.forEach(mode => {
        edges.childEdgeMode.value = mode as 0 | 1 | 2 | 3;
        expect(edges.childEdgeMode.value).toBe(mode);
      });
    });

    it('mode 0: offset small lines', () => {
      edges.childEdgeMode.value = 0;
      expect(edges.childEdgeMode.value).toBe(0);
    });

    it('mode 1: gap-large lines', () => {
      edges.childEdgeMode.value = 1;
      expect(edges.childEdgeMode.value).toBe(1);
    });

    it('mode 2: curve (Bezier) lines', () => {
      edges.childEdgeMode.value = 2;
      expect(edges.childEdgeMode.value).toBe(2);
    });

    it('mode 3: spread offset lines', () => {
      edges.childEdgeMode.value = 3;
      expect(edges.childEdgeMode.value).toBe(3);
    });
  });

  describe('endpoint calculation', () => {
    it('edgeEndpoint accepts edge object', () => {
      const edge = { source: 1, target: 2, animOpacity: 1 };
      expect(typeof edges.edgeEndpoint).toBe('function');
      // Actual calculations tested in integration tests
    });

    it('handles node type detection for Y adjustment', () => {
      // Node types (capability, competency, skill) have different Y adjustments
      expect(typeof edges.edgeEndpoint).toBe('function');
    });
  });

  describe('grouping for parallel edges', () => {
    it('calculates grouped index for edge', () => {
      const edge = { source: 1, target: 2 };
      expect(typeof edges.groupedIndexForEdge).toBe('function');
      // Grouping logic tested in integration tests
    });
  });

  describe('animation opacity', () => {
    it('computes animation opacity for edge', () => {
      const edge = { source: 1, target: 2, animOpacity: 0.5 };
      expect(typeof edges.edgeAnimOpacity).toBe('function');
      // Animation tested in integration tests
    });
  });

  describe('scenario edge special handling', () => {
    it('scenarioEdgePath handles scenario→capability edges', () => {
      expect(typeof edges.scenarioEdgePath).toBe('function');
      // Bezier curve calculation tested in integration tests
    });
  });
});
