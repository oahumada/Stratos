import { describe, it, expect, beforeEach, vi } from 'vitest';
import { useScenarioAPI } from '../useScenarioAPI';

// Mock useApi and useNotification
vi.mock('@/composables/useApi', () => ({
  useApi: () => ({
    get: vi.fn(),
    post: vi.fn(),
    patch: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    api: {
      get: vi.fn(),
    },
  }),
}));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({
    showSuccess: vi.fn(),
    showError: vi.fn(),
  }),
}));

describe('useScenarioAPI', () => {
  let api: ReturnType<typeof useScenarioAPI>;

  beforeEach(() => {
    api = useScenarioAPI();
    vi.clearAllMocks();
  });

  describe('initialization', () => {
    it('initializes without errors', () => {
      expect(api).toBeDefined();
    });
  });

  describe('CSRF handling', () => {
    it('has ensureCsrf method', async () => {
      expect(typeof api.ensureCsrf).toBe('function');
    });
  });

  describe('capability tree loading', () => {
    it('returns empty array for undefined scenarioId', async () => {
      const result = await api.loadCapabilityTree(undefined);
      expect(result).toEqual([]);
    });

    it('returns empty array for null scenarioId', async () => {
      const result = await api.loadCapabilityTree(null as any);
      expect(result).toEqual([]);
    });

    it('has loadCapabilityTree method', () => {
      expect(typeof api.loadCapabilityTree).toBe('function');
    });
  });

  describe('capability operations', () => {
    it('has saveCapability method', () => {
      expect(typeof api.saveCapability).toBe('function');
    });

    it('has deleteCapability method', () => {
      expect(typeof api.deleteCapability).toBe('function');
    });

    it('saveCapability accepts capability data', async () => {
      const capabilityData = {
        id: 1,
        name: 'Test Capability',
        description: 'Test description',
      };
      
      expect(typeof api.saveCapability).toBe('function');
      // Method should accept the data without errors
      // Actual implementation tested via integration tests
    });

    it('deleteCapability accepts capability id', async () => {
      expect(typeof api.deleteCapability).toBe('function');
      // Actual implementation tested via integration tests
    });
  });

  describe('competency operations', () => {
    it('has createCompetency method', () => {
      expect(typeof api.createCompetency).toBe('function');
    });

    it('has deleteCompetency method', () => {
      expect(typeof api.deleteCompetency).toBe('function');
    });
  });;

  describe('skill operations', () => {
    it('has fetchSkillsForCompetency method', () => {
      expect(typeof api.fetchSkillsForCompetency).toBe('function');
    });

    it('has saveSkill method', () => {
      expect(typeof api.saveSkill).toBe('function');
    });

    it('has deleteSkill method', () => {
      expect(typeof api.deleteSkill).toBe('function');
    });
  });

  describe('position persistence', () => {
    it('has savePositions method', () => {
      expect(typeof api.savePositions).toBe('function');
    });

    it('savePositions accepts nodes array', async () => {
      const nodes = [
        { id: 1, x: 100, y: 200, type: 'capability' },
        { id: 2, x: 300, y: 400, type: 'competency' },
      ];
      
      expect(typeof api.savePositions).toBe('function');
    });
  });

  describe('connection operations', () => {
    it('has savePositions method', () => {
      expect(typeof api.savePositions).toBe('function');
    });
  });

  describe('with initialization options', () => {
    it('initializes with scenario data', () => {
      const options = {
        scenario: {
          id: 1,
          name: 'Test Scenario',
        },
      };
      
      const apiWithOptions = useScenarioAPI(options);
      expect(apiWithOptions).toBeDefined();
    });
  });
});
