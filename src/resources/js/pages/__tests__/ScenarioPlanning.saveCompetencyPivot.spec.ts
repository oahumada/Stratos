// @vitest-environment jsdom
import { vi, describe, it, expect, beforeEach } from 'vitest';

const patchMock = vi.fn();
const getMock = vi.fn();
vi.mock('@/composables/useApi', () => ({ useApi: () => ({ patch: patchMock, get: getMock, api: { patch: patchMock, get: getMock } }) }));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({ showSuccess: () => {}, showError: () => {} }),
}));

import { shallowMount } from '@vue/test-utils';
import ScenarioPlanning from '../ScenarioPlanning/Index.vue';

describe('ScenarioPlanning save competency and pivot', () => {
  let wrapper: any;

  beforeEach(() => {
    patchMock.mockReset();
    getMock.mockReset();
    // patch for competency entity
    patchMock.mockResolvedValue({ data: { success: true } });
    // ensure reload/get calls resolve
    getMock.mockResolvedValue({ data: [] });

    wrapper = shallowMount(ScenarioPlanning, {
      props: { scenario: { id: 7, name: 'S-pivot', capabilities: [] } },
      shallow: true,
    });
  });

  it('patches competency entity and pivot payload when saving selectedChild', async () => {
    // simulate a selectedChild representing a competency node
    wrapper.vm.selectedChild = { id: -501, compId: 321, name: 'Comp X' };
    // simulate childEdges so parent capability id can be found
    wrapper.vm.childEdges = [{ source: 77, target: -501 }];

    // Wait for watchers to populate edit fields, then set competency form fields
    await wrapper.vm.$nextTick();
    // set competency form fields (child-specific refs) via .value to avoid being overwritten by watcher
    if (wrapper.vm.editChildName && typeof wrapper.vm.editChildName === 'object') wrapper.vm.editChildName.value = 'Comp X'; else wrapper.vm.editChildName = 'Comp X';
    if (wrapper.vm.editChildDescription && typeof wrapper.vm.editChildDescription === 'object') wrapper.vm.editChildDescription.value = 'updated desc'; else wrapper.vm.editChildDescription = 'updated desc';
    if (wrapper.vm.editChildSkills && typeof wrapper.vm.editChildSkills === 'object') wrapper.vm.editChildSkills.value = 's1,s2'; else wrapper.vm.editChildSkills = 's1,s2';

    // set pivot edit refs expected by saveSelectedChild (child-specific) after watcher settled
    if (wrapper.vm.editChildPivotStrategicWeight && typeof wrapper.vm.editChildPivotStrategicWeight === 'object') wrapper.vm.editChildPivotStrategicWeight.value = 12; else wrapper.vm.editChildPivotStrategicWeight = 12;
    if (wrapper.vm.editChildPivotPriority && typeof wrapper.vm.editChildPivotPriority === 'object') wrapper.vm.editChildPivotPriority.value = 2; else wrapper.vm.editChildPivotPriority = 2;
    if (wrapper.vm.editChildPivotRequiredLevel && typeof wrapper.vm.editChildPivotRequiredLevel === 'object') wrapper.vm.editChildPivotRequiredLevel.value = 3; else wrapper.vm.editChildPivotRequiredLevel = 3;
    if (wrapper.vm.editChildPivotIsCritical && typeof wrapper.vm.editChildPivotIsCritical === 'object') wrapper.vm.editChildPivotIsCritical.value = true; else wrapper.vm.editChildPivotIsCritical = true;
    if (wrapper.vm.editChildPivotRationale && typeof wrapper.vm.editChildPivotRationale === 'object') wrapper.vm.editChildPivotRationale.value = 'Roadmap need'; else wrapper.vm.editChildPivotRationale = 'Roadmap need';

    if (typeof wrapper.vm.saveSelectedChild === 'function') {
      // prevent full tree reload / heavy layout work inside the component
      wrapper.vm.loadTreeFromApi = vi.fn();
      await wrapper.vm.saveSelectedChild();

      // Expect a patch to competency entity (/api/competencies/:id)
      const compCall = patchMock.mock.calls.find((c: any) => String(c[0]).includes(`/api/competencies/321`));
      expect(compCall).toBeTruthy();

      // Expect a patch to the scenario pivot endpoint for parent capability
      const pivotCall = patchMock.mock.calls.find((c: any) => String(c[0]).includes(`/strategic-planning/scenarios/7/capabilities/77/competencies/321`));
      expect(pivotCall).toBeTruthy();

      const pivotPayload = pivotCall[1];
      // Accept either 'strategic_weight' or legacy 'weight' depending on schema
      const weightValue = pivotPayload.strategic_weight ?? pivotPayload.weight;
      expect(weightValue).toBe(12);
      expect(pivotPayload).toMatchObject({
        priority: 2,
        required_level: 3,
        is_critical: true,
        rationale: 'Roadmap need',
      });
    } else {
      expect(false).toBeTruthy();
    }
  });
});
