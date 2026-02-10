// @vitest-environment jsdom
import { vi, describe, it, expect, beforeEach } from 'vitest';

// Ensure CSRF check in ensureCsrf() is skipped by providing cookie
beforeEach(() => {
  Object.defineProperty(document, 'cookie', {
    writable: true,
    value: 'XSRF-TOKEN=1;',
  });
});

const patchMock = vi.fn();
const postMock = vi.fn();
const getMock = vi.fn();

vi.mock('@/composables/useApi', () => ({ useApi: () => ({ patch: patchMock, post: postMock, get: getMock, api: { patch: patchMock, post: postMock, get: getMock } }) }));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({ showSuccess: () => {}, showError: () => {} }),
}));

import { shallowMount } from '@vue/test-utils';
let ScenarioPlanning: any;

describe('ScenarioPlanning pivot save', () => {
  beforeEach(async () => {
    patchMock.mockReset();
    postMock.mockReset();
    getMock.mockReset();
    // default patch behaviour: resolve with an updated relation object
    patchMock.mockImplementation(async (url: string, payload?: any) => {
      return { data: { updated: payload } };
    });
    // dynamic import after mocks
    ScenarioPlanning = (await import('@/pages/ScenarioPlanning/Index.vue')).default;
  });

  it('sends pivot PATCH and merges updates into local node', async () => {
    const wrapper = shallowMount(ScenarioPlanning, {
      props: { scenario: { id: 42, name: 'S' } },
      shallow: true,
    });

    // set focusedNode to a capability with id=10
    wrapper.vm.focusedNode = { id: 10, raw: { scenario_capabilities: [{ strategic_weight: 5 }] } };

    // wait a tick in case watchers reset edit fields, then set edit pivot fields
    await wrapper.vm.$nextTick();
    wrapper.vm.editPivotStrategicWeight = 8; if (typeof wrapper.vm.editPivotStrategicWeight === 'object' && 'value' in wrapper.vm.editPivotStrategicWeight) wrapper.vm.editPivotStrategicWeight.value = 8;
    wrapper.vm.editPivotPriority = 2; if (typeof wrapper.vm.editPivotPriority === 'object' && 'value' in wrapper.vm.editPivotPriority) wrapper.vm.editPivotPriority.value = 2;
    wrapper.vm.editPivotRationale = 'test rationale'; if (typeof wrapper.vm.editPivotRationale === 'object' && 'value' in wrapper.vm.editPivotRationale) wrapper.vm.editPivotRationale.value = 'test rationale';
    wrapper.vm.editPivotRequiredLevel = 4; if (typeof wrapper.vm.editPivotRequiredLevel === 'object' && 'value' in wrapper.vm.editPivotRequiredLevel) wrapper.vm.editPivotRequiredLevel.value = 4;
    wrapper.vm.editPivotIsCritical = true; if (typeof wrapper.vm.editPivotIsCritical === 'object' && 'value' in wrapper.vm.editPivotIsCritical) wrapper.vm.editPivotIsCritical.value = true;

    // call saveFocusedNode
    if (typeof wrapper.vm.saveFocusedNode === 'function') {
      await wrapper.vm.saveFocusedNode();

      // verify PATCH for capability entity was called
      const capPatchCall = patchMock.mock.calls.find((c: any) => String(c[0]).includes('/api/capabilities/10'));
      expect(capPatchCall).toBeTruthy();
      const capPayload = capPatchCall[1];
      // capability payload should at least include name/description keys (may be undefined)
      expect(capPayload).toHaveProperty('name');

      // verify PATCH was called against scenario pivot endpoint
      const pivotCall = patchMock.mock.calls.find((c: any) => String(c[0]).includes('/strategic-planning/scenarios/42/capabilities/10'));
      expect(pivotCall).toBeTruthy();

      // inspect the payload sent for pivot
      const sentPayload = pivotCall[1];
      expect(sentPayload).toMatchObject({
        strategic_weight: 8,
        priority: 2,
        rationale: 'test rationale',
        required_level: 4,
        is_critical: true,
      });

      // Ensure local focusedNode.raw got merged with pivot update
      expect(wrapper.vm.focusedNode.raw).toBeTruthy();
      const pivots = wrapper.vm.focusedNode.raw.scenario_capabilities || wrapper.vm.focusedNode.raw.pivot || wrapper.vm.focusedNode.raw._pivot;
      expect(pivots).toBeTruthy();
      const first = Array.isArray(pivots) ? pivots[0] : pivots;
      expect(first.strategic_weight).toBe(8);
      expect(first.priority).toBe(2);
      expect(first.rationale).toBe('test rationale');
    } else {
      // if function missing, fail the test
      expect(false).toBeTruthy();
    }
  });
});
