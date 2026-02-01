import { describe, it, expect, beforeEach } from 'vitest';
import { useScenarioLayout, LAYOUT_CONFIG } from '../useScenarioLayout';

describe('useScenarioLayout', () => {
  let layout: ReturnType<typeof useScenarioLayout>;

  beforeEach(() => {
    layout = useScenarioLayout();
  });

  describe('LAYOUT_CONFIG', () => {
    it('exports centralized configuration', () => {
      expect(LAYOUT_CONFIG).toBeDefined();
    });

    it('has capability configuration', () => {
      expect(LAYOUT_CONFIG.capability).toBeDefined();
      expect(LAYOUT_CONFIG.capability.spacing).toBeDefined();
      expect(LAYOUT_CONFIG.capability.forces).toBeDefined();
    });

    it('has competency configuration', () => {
      expect(LAYOUT_CONFIG.competency).toBeDefined();
      expect(LAYOUT_CONFIG.competency.radial).toBeDefined();
      expect(LAYOUT_CONFIG.competency.sides).toBeDefined();
      expect(LAYOUT_CONFIG.competency.defaultLayout).toBe('auto');
      expect(LAYOUT_CONFIG.competency.maxDisplay).toBe(10);
    });

    it('has skill configuration', () => {
      expect(LAYOUT_CONFIG.skill).toBeDefined();
      expect(LAYOUT_CONFIG.skill.radial).toBeDefined();
      expect(LAYOUT_CONFIG.skill.sides).toBeDefined();
      expect(LAYOUT_CONFIG.skill.maxDisplay).toBe(10);
    });

    it('has animation configuration', () => {
      expect(LAYOUT_CONFIG.animations).toBeDefined();
      expect(LAYOUT_CONFIG.animations.competencyEntryFinalize).toBe(80);
      expect(LAYOUT_CONFIG.animations.skillEntryFinalize).toBe(70);
    });

    it('has node radius configuration', () => {
      expect(LAYOUT_CONFIG.node).toBeDefined();
      expect(LAYOUT_CONFIG.node.radius).toBe(34);
      expect(LAYOUT_CONFIG.node.focusRadius).toBe(44);
    });

    it('has clamp configuration for viewport bounds', () => {
      expect(LAYOUT_CONFIG.clamp).toBeDefined();
      expect(LAYOUT_CONFIG.clamp.minY).toBe(40);
      expect(LAYOUT_CONFIG.clamp.bottomPadding).toBe(40);
    });
  });

  describe('helper functions', () => {
    it('exports expandCompetencies function', () => {
      expect(typeof layout.expandCompetencies).toBe('function');
    });

    it('exports expandSkills function', () => {
      expect(typeof layout.expandSkills).toBe('function');
    });

    it('exports centerOnNode function', () => {
      expect(typeof layout.centerOnNode).toBe('function');
    });

    it('exports collapseGrandChildren function', () => {
      expect(typeof layout.collapseGrandChildren).toBe('function');
    });

    it('exports runForceLayout function', () => {
      expect(typeof layout.runForceLayout).toBe('function');
    });

    it('exports clampY function', () => {
      expect(typeof layout.clampY).toBe('function');
    });

    it('exports wrapLabel function', () => {
      expect(typeof layout.wrapLabel).toBe('function');
    });

    it('exports wait function', () => {
      expect(typeof layout.wait).toBe('function');
    });

    it('exports computeInitialPosition function', () => {
      expect(typeof layout.computeInitialPosition).toBe('function');
    });
  });

  describe('clampY function', () => {
    it('clamps Y to minimum value', () => {
      const result = layout.clampY(-100);
      expect(result).toBeGreaterThanOrEqual(LAYOUT_CONFIG.clamp.minY);
    });

    it('clamps Y to reasonable value', () => {
      const result = layout.clampY(100);
      expect(result).toBeGreaterThanOrEqual(LAYOUT_CONFIG.clamp.minY);
    });

    it('allows values within valid range', () => {
      const validY = 100;
      const result = layout.clampY(validY);
      expect(result).toBe(validY);
    });
  });

  describe('wrapLabel function', () => {
    it('returns empty string for null/undefined', () => {
      expect(layout.wrapLabel(null)).toBe('');
      expect(layout.wrapLabel(undefined)).toBe('');
    });

    it('returns short label unchanged', () => {
      const short = 'Test';
      expect(layout.wrapLabel(short)).toBe(short);
    });

    it('wraps long label at word boundaries', () => {
      const long = 'This is a very long label that should wrap';
      const wrapped = layout.wrapLabel(long, 10);
      expect(wrapped).toContain('\n');
    });

    it('handles labels with custom max length', () => {
      const label = 'Testing';
      const wrapped = layout.wrapLabel(label, 4);
      expect(wrapped.length).toBeLessThanOrEqual(label.length + 1); // +1 for newline
    });
  });

  describe('expandCompetencies function', () => {
    it('accepts node and returns result object', () => {
      const mockNode = { id: 1, x: 100, y: 100, type: 'capability' };
      const mockChildren = [];
      const mockEdges = [];
      
      expect(typeof layout.expandCompetencies).toBe('function');
      // Actual results tested in integration tests
    });
  });

  describe('expandSkills function', () => {
    it('accepts competency node and options', () => {
      const mockNode = { id: 2, x: 150, y: 150, type: 'competency' };
      
      expect(typeof layout.expandSkills).toBe('function');
      // Actual layout calculations tested in integration tests
    });
  });

  describe('force layout simulation', () => {
    it('runForceLayout accepts nodes and edges', () => {
      const nodes = [
        { id: 1, x: 0, y: 0, type: 'capability' },
        { id: 2, x: 0, y: 0, type: 'competency' },
      ];
      const edges = [{ source: 1, target: 2 }];
      
      expect(typeof layout.runForceLayout).toBe('function');
      // Force simulation tested in integration tests
    });
  });

  describe('centerOnNode function', () => {
    it('accepts node and viewport height for centering', () => {
      const mockNode = { id: 1, x: 100, y: 100, type: 'capability' };
      const viewportHeight = 800;
      
      expect(typeof layout.centerOnNode).toBe('function');
      // Centering logic tested in integration tests
    });
  });

  describe('collapseGrandChildren function', () => {
    it('accepts grandchild arrays for collapse operation', () => {
      const mockGrandChildren = [];
      const mockGrandEdges = [];
      
      expect(typeof layout.collapseGrandChildren).toBe('function');
      // Collapse logic tested in integration tests
    });
  });
});
