// @vitest-environment jsdom
import { vi, describe, it, expect, beforeEach } from 'vitest';

const deleteMock = vi.fn();
vi.mock('@/composables/useApi', () => ({ useApi: () => ({ delete: deleteMock, get: async () => ({ data: [] }), api: { delete: deleteMock, get: async () => ({ data: [] }) } }) }));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({ showSuccess: () => {}, showError: () => {} }),
}));

import { shallowMount } from '@vue/test-utils';
import ScenarioPlanning from '../ScenarioPlanning/Index.vue';

describe('ScenarioPlanning delete node', () => {
  let wrapper: any;
  beforeEach(() => {
    deleteMock.mockReset();
    deleteMock.mockResolvedValue({ data: { success: true } });
    wrapper = shallowMount(ScenarioPlanning, { props: { scenario: { id: 99, name: 'S' } }, shallow: true });
    // stub confirm to always accept
    vi.stubGlobal('confirm', () => true);
  });

  it('deletes competency when selectedChild present', async () => {
    // Simulate selectedChild as a child node with negative id and compId
    wrapper.vm.selectedChild = { id: -123, compId: 321 };
    // Simulate childEdges to find parent capability id
    wrapper.vm.childEdges = [{ source: 77, target: -123 }];
    if (typeof wrapper.vm.deleteFocusedNode === 'function') {
      await wrapper.vm.deleteFocusedNode();
      expect(deleteMock).toHaveBeenCalled();
      const call = deleteMock.mock.calls.find((c: any) => String(c[0]).includes('/strategic-planning/scenarios/99/capabilities/77/competencies/321'));
      expect(call).toBeTruthy();
    } else {
      expect(false).toBeTruthy();
    }
  });
});
