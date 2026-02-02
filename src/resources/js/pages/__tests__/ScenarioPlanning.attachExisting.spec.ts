// @vitest-environment jsdom
import { vi, describe, it, expect, beforeEach } from 'vitest';

const postMock = vi.fn();
vi.mock('@/composables/useApi', () => ({ useApi: () => ({ post: postMock, get: async () => ({ data: [] }), api: { post: postMock, get: async () => ({ data: [] }) } }) }));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({ showSuccess: () => {}, showError: () => {} }),
}));

import { shallowMount } from '@vue/test-utils';
import ScenarioPlanning from '../ScenarioPlanning/Index.vue';

describe('ScenarioPlanning attach existing competency', () => {
  let wrapper: any;
  beforeEach(() => {
    postMock.mockReset();
    postMock.mockResolvedValue({ data: { success: true } });
    wrapper = shallowMount(ScenarioPlanning, { props: { scenario: { id: 11, name: 'S' } }, shallow: true });
  });

  it('posts competency_id to scenario-capability endpoint when attaching existing', async () => {
    // set focusedNode (displayNode is computed) to a capability id
    wrapper.vm.focusedNode = { id: 77 };
    wrapper.vm.addExistingSelection = 555;
    if (typeof wrapper.vm.attachExistingComp === 'function') {
      await wrapper.vm.attachExistingComp();
      expect(postMock).toHaveBeenCalled();
      const call = postMock.mock.calls.find((c: any) => String(c[0]).includes('/strategic-planning/scenarios/11/capabilities/77/competencies'));
      expect(call).toBeTruthy();
      const payload = call[1];
      expect(payload).toMatchObject({ competency_id: 555 });
    } else {
      expect(false).toBeTruthy();
    }
  });
});
