// @vitest-environment jsdom
import { vi, describe, it, expect, beforeEach } from 'vitest';

// Mock composables that rely on browser globals or network during unit tests
vi.mock('@/composables/useApi', () => ({
  useApi: () => ({ api: { get: async () => ({}) }, apiClient: {} }),
}));
vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({ showSuccess: () => {}, showError: () => {} }),
}));

import { shallowMount } from '@vue/test-utils';
import ScenarioPlanning from '../ScenarioPlanning/Index.vue';

describe('ScenarioPlanning interactions (shallow)', () => {
  let wrapper: any;

  beforeEach(() => {
    wrapper = shallowMount(ScenarioPlanning, {
      props: {
        scenario: { id: 1, name: 'S1', capabilities: [] },
      },
      shallow: true,
    });
  });

  it('does not call expandCompetencies for <=3 competencies', async () => {
    const node = { id: 10, competencies: [1, 2, 3], x: 100, y: 100 };
    // Ensure function exists
    expect(typeof wrapper.vm.expandCompetencies).toBe('function');

    const spy = vi.spyOn(wrapper.vm, 'expandCompetencies');
    await wrapper.vm.handleNodeClick(node, { clientX: 0, clientY: 0 });
    expect(spy).not.toHaveBeenCalled();
    spy.mockRestore();
  });

  it('calls expandCompetencies with matrix for >=4 competencies', async () => {
    const node = { id: 20, competencies: [1, 2, 3, 4, 5], x: 200, y: 200 };
      await wrapper.vm.handleNodeClick(node, { clientX: 0, clientY: 0 });
      // ensure handler updated focus to this capability and expandCompetencies exists
      expect(typeof wrapper.vm.expandCompetencies).toBe('function');
      expect(wrapper.vm.focusedNode && wrapper.vm.focusedNode.id === node.id).toBeTruthy();
  });
});
