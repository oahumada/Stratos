// @vitest-environment jsdom
import { vi, describe, it, expect, beforeEach } from 'vitest';

// Mock API composable to intercept POST
const postMock = vi.fn();
const getMock = vi.fn();
vi.mock('@/composables/useApi', () => ({
  useApi: () => ({ post: postMock, get: getMock, apiClient: {}, api: { post: postMock, get: getMock } }),
}));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({ showSuccess: () => {}, showError: () => {} }),
}));

import { shallowMount } from '@vue/test-utils';
import ScenarioPlanning from '../ScenarioPlanning/Index.vue';

describe('ScenarioPlanning create capability modal', () => {
  let wrapper: any;

  beforeEach(() => {
    postMock.mockReset();
    getMock.mockReset();
    postMock.mockResolvedValue({ data: { id: 123, name: 'Full Capability', description: 'Full description', importance: 2, is_critical: 1 } });

    // ensure loadTreeFromApi returns the created capability so canonical reload preserves it
    getMock.mockResolvedValue([{ id: 123, name: 'Full Capability', description: 'Full description', importance: 2, is_critical: 1 }]);

    wrapper = shallowMount(ScenarioPlanning, {
      props: {
        scenario: { id: 42, name: 'S1', capabilities: [] },
      },
      shallow: true,
    });
  });

  it('composes payload and calls API when saving new capability', async () => {
    // set form fields
    wrapper.vm.newCapName = 'Full Capability';
    wrapper.vm.newCapDescription = 'Full description';
    wrapper.vm.newCapImportance = 2;
    wrapper.vm.newCapType = 'technical';
    wrapper.vm.newCapCategory = 'technical';
    // pivot fields
    wrapper.vm.pivotStrategicRole = 'target';
    wrapper.vm.pivotStrategicWeight = 8;
    wrapper.vm.pivotPriority = 2;
    wrapper.vm.pivotRequiredLevel = 4;
    wrapper.vm.pivotRationale = 'Needed for roadmap';
    wrapper.vm.pivotIsCritical = true;

    await wrapper.vm.saveNewCapability();

    expect(postMock).toHaveBeenCalled();
    const call = postMock.mock.calls.find((c: any) => String(c[0]).includes(`/strategic-planning/scenarios/42/capabilities`));
    expect(call).toBeTruthy();
    const url = call[0];
    const payload = call[1];
    expect(String(url)).toContain(`/api/strategic-planning/scenarios/42/capabilities`);
    expect(payload).toMatchObject({
      name: 'Full Capability',
      description: 'Full description',
      importance: 2,
      type: 'technical',
      category: 'technical',
      strategic_role: 'target',
      strategic_weight: 8,
      priority: 2,
      required_level: 4,
      rationale: 'Needed for roadmap',
      is_critical: true,
    });

    // optimistic UI: node should be appended with returned id
    const nodes = wrapper.vm.nodes;
    const found = nodes.find((n: any) => Number(n.id) === 123);
    expect(found).toBeTruthy();
    expect(found.name).toBe('Full Capability');
  });
});
