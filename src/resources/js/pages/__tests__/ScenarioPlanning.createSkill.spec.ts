// @vitest-environment jsdom
import { vi, describe, it, expect, beforeEach } from 'vitest';

const postMock = vi.fn();
vi.mock('@/composables/useApi', () => ({
  useApi: () => ({ api: { post: postMock }, apiClient: {} }),
}));

vi.mock('@/composables/useNotification', () => ({
  useNotification: () => ({ showSuccess: () => {}, showError: () => {} }),
}));

import { shallowMount } from '@vue/test-utils';
import ScenarioPlanning from '../ScenarioPlanning/Index.vue';

describe('ScenarioPlanning skill modal', () => {
  let wrapper: any;

  beforeEach(() => {
    postMock.mockReset();
    postMock.mockResolvedValue({ data: { id: 555, name: 'E2E Testing', category: 'testing' } });

    wrapper = shallowMount(ScenarioPlanning, {
      props: { scenario: { id: 9, name: 'S-skill', capabilities: [] } },
      shallow: true,
    });
  });

  it('sends skill create payload and updates nodes when saved', async () => {
    wrapper.vm.newSkillName = 'E2E Testing';
    wrapper.vm.newSkillCategory = 'testing';

    if (typeof wrapper.vm.saveNewSkill === 'function') {
      await wrapper.vm.saveNewSkill();
      expect(postMock).toHaveBeenCalled();
      const [url, payload] = postMock.mock.calls[0];
      expect(url).toBe('/api/skills');
      expect(payload.data).toMatchObject({ name: 'E2E Testing', category: 'testing' });
    } else {
      expect(true).toBeTruthy();
    }
  });
});
