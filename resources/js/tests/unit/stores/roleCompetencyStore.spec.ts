import { describe, it, expect, beforeEach, vi } from 'vitest';
import { setActivePinia, createPinia } from 'pinia';
import { useRoleCompetencyStore } from '@/stores/roleCompetencyStore';

// Mock fetch
globalThis.fetch = vi.fn();

describe('RoleCompetencyStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia());
    vi.clearAllMocks();
  });

  describe('loadScenarioData', () => {
    it('loads scenario data successfully', async () => {
      const mockData = {
        scenario: {
          id: 1,
          name: 'Test Scenario',
          horizon_months: 12,
        },
        roles: [
          {
            id: 1,
            scenario_id: 1,
            role_id: 1,
            role_name: 'Product Manager',
            fte: 1,
            role_change: 'maintain',
            evolution_type: 'maintain',
          },
        ],
        competencies: [
          {
            id: 1,
            name: 'Strategic Planning',
          },
        ],
        mappings: [
          {
            id: 1,
            scenario_id: 1,
            role_id: 1,
            competency_id: 1,
            competency_name: 'Strategic Planning',
            required_level: 4,
            is_core: true,
            change_type: 'maintenance',
            rationale: 'Core competency',
          },
        ],
      };

      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => mockData,
      });

      const store = useRoleCompetencyStore();
      await store.loadScenarioData(1);

      expect(store.scenarioId).toBe(1);
      expect(store.scenarioName).toBe('Test Scenario');
      expect(store.horizonMonths).toBe(12);
      expect(store.roles).toHaveLength(1);
      expect(store.competencies).toHaveLength(1);
      expect(store.mappings).toHaveLength(1);
      expect(store.loading).toBe(false);
      expect(store.error).toBeNull();
    });

    it('sets error on fetch failure', async () => {
      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: false,
      });

      const store = useRoleCompetencyStore();
      await store.loadScenarioData(1);

      expect(store.error).toBeTruthy();
      expect(store.loading).toBe(false);
    });

    it('sets loading state during fetch', () => {
      (globalThis.fetch as any).mockImplementationOnce(
        () =>
          new Promise((resolve) =>
            setTimeout(
              () =>
                resolve({
                  ok: true,
                  json: async () => ({
                    scenario: { id: 1, name: 'Test', horizon_months: 12 },
                    roles: [],
                    competencies: [],
                    mappings: [],
                  }),
                }),
              100
            )
          )
      );

      const store = useRoleCompetencyStore();
      store.loadScenarioData(1);

      expect(store.loading).toBe(true);
    });
  });

  describe('saveMapping', () => {
    beforeEach(() => {
      const store = useRoleCompetencyStore();
      store.scenarioId = 1;
    });

    it('creates new mapping successfully', async () => {
      const mockResponse = {
        success: true,
        mapping: {
          id: 1,
          scenario_id: 1,
          role_id: 1,
          competency_id: 1,
          competency_name: 'Strategic Planning',
          required_level: 4,
          is_core: true,
          change_type: 'maintenance',
          rationale: 'Core skill',
        },
        message: 'Mapeo guardado',
      };

      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const store = useRoleCompetencyStore();
      const newMapping = {
        id: undefined,
        scenario_id: 1,
        role_id: 1,
        competency_id: 1,
        competency_name: 'Strategic Planning',
        required_level: 4,
        is_core: true,
        change_type: 'maintenance',
        rationale: 'Core skill',
      };

      await store.saveMapping(newMapping as any);

      expect(store.mappings).toHaveLength(1);
      expect(store.success).toBeTruthy();
      expect(store.error).toBeNull();
    });

    it('updates existing mapping', async () => {
      const store = useRoleCompetencyStore();
      store.mappings = [
        {
          id: 1,
          scenario_id: 1,
          role_id: 1,
          competency_id: 1,
          competency_name: 'Strategic Planning',
          required_level: 3,
          is_core: false,
          change_type: 'maintenance',
          rationale: 'Old',
        },
      ];

      const mockResponse = {
        success: true,
        mapping: {
          id: 1,
          scenario_id: 1,
          role_id: 1,
          competency_id: 1,
          competency_name: 'Strategic Planning',
          required_level: 4,
          is_core: true,
          change_type: 'transformation',
          rationale: 'Updated',
        },
      };

      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const updatedMapping = {
        id: 1,
        scenario_id: 1,
        role_id: 1,
        competency_id: 1,
        competency_name: 'Strategic Planning',
        required_level: 4,
        is_core: true,
        change_type: 'transformation' as const,
        rationale: 'Updated',
      };

      await store.saveMapping(updatedMapping);

      expect(store.mappings).toHaveLength(1);
      expect(store.mappings[0].required_level).toBe(4);
      expect(store.mappings[0].change_type).toBe('transformation');
    });

    it('handles save errors', async () => {
      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: false,
      });

      const store = useRoleCompetencyStore();
      const mapping = {
        scenario_id: 1,
        role_id: 1,
        competency_id: 1,
        competency_name: 'Test',
        required_level: 3,
        is_core: false,
        change_type: 'maintenance',
        rationale: 'Test',
      } as any;

      await store.saveMapping(mapping);

      expect(store.error).toBeTruthy();
    });
  });

  describe('removeMapping', () => {
    beforeEach(() => {
      const store = useRoleCompetencyStore();
      store.scenarioId = 1;
      store.mappings = [
        {
          id: 1,
          scenario_id: 1,
          role_id: 1,
          competency_id: 1,
          competency_name: 'Strategic Planning',
          required_level: 4,
          is_core: true,
          change_type: 'maintenance',
          rationale: 'Core',
        },
        {
          id: 2,
          scenario_id: 1,
          role_id: 2,
          competency_id: 2,
          competency_name: 'Digital Skills',
          required_level: 3,
          is_core: false,
          change_type: 'transformation',
          rationale: 'New',
        },
      ];
    });

    it('removes mapping successfully', async () => {
      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          success: true,
          message: 'Mapping removed',
        }),
      });

      const store = useRoleCompetencyStore();
      await store.removeMapping(1, 1, 1);

      expect(store.mappings).toHaveLength(1);
      expect(store.mappings[0].id).toBe(2);
      expect(store.success).toBeTruthy();
    });

    it('handles remove errors', async () => {
      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: false,
      });

      const store = useRoleCompetencyStore();
      await store.removeMapping(1, 1, 1);

      expect(store.error).toBeTruthy();
      expect(store.mappings).toHaveLength(2); // No cambió
    });
  });

  describe('addNewRole', () => {
    beforeEach(() => {
      const store = useRoleCompetencyStore();
      store.scenarioId = 1;
    });

    it('adds new role successfully', async () => {
      const mockResponse = {
        success: true,
        role: {
          id: 1,
          scenario_id: 1,
          role_id: 1,
          role_name: 'AI Architect',
          fte: 2,
          role_change: 'create',
          evolution_type: 'new_role',
        },
        message: 'Role added',
      };

      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const store = useRoleCompetencyStore();
      const newRole = {
        role_name: 'AI Architect',
        fte: 2,
        role_change: 'create',
        evolution_type: 'new_role',
        impact_level: 'critical',
        rationale: 'New strategic initiative',
      };

      await store.addNewRole(newRole);

      expect(store.roles).toHaveLength(1);
      expect(store.roles[0].role_name).toBe('AI Architect');
      expect(store.success).toBeTruthy();
    });

    it('handles add role errors', async () => {
      (globalThis.fetch as any).mockResolvedValueOnce({
        ok: false,
      });

      const store = useRoleCompetencyStore();
      const newRole = {
        role_name: 'Test',
        fte: 1,
        role_change: 'create',
        evolution_type: 'new_role',
      };

      await store.addNewRole(newRole);

      expect(store.error).toBeTruthy();
    });
  });

  describe('computed properties', () => {
    it('computes matrixRows correctly', () => {
      const store = useRoleCompetencyStore();
      store.roles = [
        {
          id: 1,
          scenario_id: 1,
          role_id: 1,
          role_name: 'PM',
          fte: 1,
          role_change: 'maintain',
          evolution_type: 'maintain',
          impact_level: 'medium',
          rationale: '',
        },
      ];
      store.scenarioId = 1;
      store.mappings = [
        {
          id: 1,
          scenario_id: 1,
          role_id: 1,
          competency_id: 1,
          competency_name: 'Strategic',
          required_level: 4,
          is_core: true,
          change_type: 'maintenance',
          rationale: '',
        },
      ];

      const rows = store.matrixRows;

      expect(rows).toHaveLength(1);
      expect(rows[0].roleName).toBe('PM');
      expect(rows[0].fte).toBe(1);
      expect(rows[0].mappings.size).toBe(1);
    });

    it('computes competencyColumns correctly', () => {
      const store = useRoleCompetencyStore();
      store.competencies = [
        { id: 1, name: 'Strategic Planning', category: 'Leadership' },
        { id: 2, name: 'Technical Skills', category: 'Technical' },
      ];

      const columns = store.competencyColumns;

      expect(columns).toHaveLength(2);
      expect(columns[0].name).toBe('Strategic Planning');
      expect(columns[1].name).toBe('Technical Skills');
    });
  });

  describe('getMapping', () => {
    it('returns mapping if exists', () => {
      const store = useRoleCompetencyStore();
      store.scenarioId = 1;
      store.mappings = [
        {
          id: 1,
          scenario_id: 1,
          role_id: 1,
          competency_id: 1,
          competency_name: 'Strategic',
          required_level: 4,
          is_core: true,
          change_type: 'maintenance',
          rationale: '',
        },
      ];

      const mapping = store.getMapping(1, 1);

      expect(mapping).toBeDefined();
      expect(mapping?.id).toBe(1);
    });

    it('returns undefined if mapping not found', () => {
      const store = useRoleCompetencyStore();
      store.scenarioId = 1;
      store.mappings = [];

      const mapping = store.getMapping(1, 1);

      expect(mapping).toBeUndefined();
    });
  });

  describe('clearMessages', () => {
    it('clears error and success messages', () => {
      const store = useRoleCompetencyStore();
      store.error = 'Some error';
      store.success = 'Some success';

      store.clearMessages();

      expect(store.error).toBeNull();
      expect(store.success).toBeNull();
    });
  });
});
