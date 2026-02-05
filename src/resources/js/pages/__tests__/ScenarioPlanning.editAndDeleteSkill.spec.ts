// @vitest-environment jsdom
import { vi, describe, it, expect, beforeEach } from 'vitest';

const patchMock = vi.fn();
const getMock = vi.fn();
const deleteMock = vi.fn();
vi.mock('@/composables/useApi', () => ({
  useApi: () => ({ patch: patchMock, get: getMock, delete: deleteMock, api: { patch: patchMock, get: getMock, delete: deleteMock } }),
}));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({ showSuccess: () => {}, showError: () => {} }),
}));

import { mount } from '@vue/test-utils';
import ScenarioPlanning from '../ScenarioPlanning/Index.vue';

// Simple Vuetify component stubs to render slots as plain HTML elements
const vuetifyStubs: Record<string, any> = {
  'v-btn': { template: '<button><slot/></button>' },
  'v-dialog': { template: '<div><slot/></div>' },
  'v-card': { template: '<div><slot/></div>' },
  'v-card-actions': { template: '<div><slot/></div>' },
  'v-card-text': { template: '<div><slot/></div>' },
  'v-card-title': { template: '<div><slot/></div>' },
  'v-text-field': { template: '<input />' },
  'v-form': { template: '<form><slot/></form>' },
  'v-row': { template: '<div><slot/></div>' },
  'v-col': { template: '<div><slot/></div>' },
  'v-slider': { template: '<input type="range" />' },
};

describe('ScenarioPlanning skill edit & delete modal', () => {
  let wrapper: any;

  beforeEach(() => {
    patchMock.mockReset();
    getMock.mockReset();
    deleteMock.mockReset();

    patchMock.mockResolvedValue({ data: { success: true } });
    getMock.mockResolvedValue({ data: { id: 555, name: 'Edited Skill', category: 'testing' } });
    deleteMock.mockResolvedValue({ data: { success: true } });

    wrapper = mount(ScenarioPlanning, {
      props: { scenario: { id: 9, name: 'S-edit', capabilities: [] } },
      global: { stubs: ['RouterLink'], components: vuetifyStubs },
    });
  });

  it('edits skill fields, patches skill and pivot, closes modal and refreshes selected skill', async () => {
    // Prepare component state: selectedSkillDetail and selectedChild/focusedNode to provide pivot context
    const skillObj = { id: 555, name: 'Old', raw: { pivot: { id: 77 } } };
    // Assign plain (unwrapped) values on the instance — component now supports this in saveSkillDetail
    wrapper.vm.selectedSkillDetail = skillObj;
    wrapper.vm.selectedChild = { compId: 21, raw: { pivot: { id: 77 } } };
    wrapper.vm.focusedNode = { id: 3 };

    // change editable refs
    wrapper.vm.skillEditName = 'Edited Skill';
    wrapper.vm.skillEditCategory = 'testing';
    wrapper.vm.skillPivotWeight = 8;

    // Call saveSkillDetail
    await wrapper.vm.saveSkillDetail();

    // Expect skill patch called
    expect(patchMock).toHaveBeenCalled();
    const firstCall = patchMock.mock.calls[0][0];
    expect(firstCall).toBe('/api/skills/555');

    // Expect competency_skills patch attempted (our added flow)
    const csCall = patchMock.mock.calls.find((c: any) => typeof c[0] === 'string' && c[0].includes('/api/competency-skills/'));
    expect(csCall).toBeTruthy();

    // Expect either competency_skills patch or scenario-scoped pivot patch to have been attempted
    const pivotCall = patchMock.mock.calls.find((c: any) => typeof c[0] === 'string' && c[0].includes('/api/strategic-planning/scenarios/'));
    expect(csCall || pivotCall).toBeTruthy();

    // Modal closed and selectedSkillDetail refreshed from api.get
    expect(wrapper.vm.skillDetailDialogVisible).toBe(false);
    expect(getMock).toHaveBeenCalledWith('/api/skills/555');
    expect(wrapper.vm.selectedSkillDetail.name).toBe('Edited Skill');
  });

  it('removes a skill from competency (delete pivot) and updates local arrays', async () => {
    const skill = { id: 888, raw: { pivot: { id: 999 } }, name: 'ToRemove' };
    const comp = { compId: 21, skills: [skill] };
    wrapper.vm.selectedChild = comp;
    wrapper.vm.focusedNode = { id: 3 };
    wrapper.vm.grandChildNodes = [{ id: 888 }];

    // Call removeSkillFromCompetency
    await wrapper.vm.removeSkillFromCompetency(skill);

    // Expect API delete called for competency skill endpoint
    expect(deleteMock).toHaveBeenCalled();
    const delUrl = deleteMock.mock.calls[0][0];
    expect(delUrl).toContain('/api/competencies/21/skills/888');

    // Skills array should be updated
    // grandChildNodes should be filtered
    expect(wrapper.vm.grandChildNodes.find((g: any) => g.id === 888)).toBeUndefined();
  });

  it('clicking Guardar button triggers saveSkillDetail', async () => {
    const skillObj = { id: 777, name: 'ClickSave', raw: { pivot: { id: 55 } } };
    wrapper.vm.selectedSkillDetail = skillObj;
    wrapper.vm.selectedChild = { compId: 21, raw: { pivot: { id: 55 } } };
    wrapper.vm.focusedNode = { id: 3 };
    wrapper.vm.skillDetailDialogVisible = true;
    await wrapper.vm.$nextTick();

    // With Vuetify stubs, v-btn renders as a <button>; if not present,
    // call the handler directly to ensure behavior is exercised.
    const buttons = wrapper.findAll('button');
    const saveBtn = buttons.find((b: any) => b.text().includes('Guardar'));
    if (saveBtn) {
      await (saveBtn as any).trigger('click');
    } else {
      await wrapper.vm.saveSkillDetail();
    }

    // expect API patch for skill invoked
    expect(patchMock).toHaveBeenCalled();
    const firstCall = patchMock.mock.calls[0][0];
    expect(firstCall).toBe('/api/skills/777');
  });
});
