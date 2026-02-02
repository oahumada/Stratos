import { describe, it, expect, vi } from 'vitest';

// Mock the useApi composable used by useCompetencySkills with a shared mock object
const sharedApiMock = { post: vi.fn(), get: vi.fn() };
vi.mock('@/composables/useApi', () => ({
  useApi: () => sharedApiMock,
}));

import { useCompetencySkills } from '@/composables/useCompetencySkills';
import { useApi } from '@/composables/useApi';

describe('useCompetencySkills composable', () => {
  it('createAndAttachSkill posts skill payload and returns created skill', async () => {
    const mockApi = useApi();
    const created = { id: 101, name: 'Created Skill' };
    (mockApi.post as unknown as vi.Mock).mockResolvedValueOnce({ data: created });

    const { createAndAttachSkill } = useCompetencySkills();
    const res = await createAndAttachSkill(1, { name: 'Created Skill' });

    expect(mockApi.post).toHaveBeenCalledWith('/api/competencies/1/skills', { skill: { name: 'Created Skill' } });
    expect(res).toEqual(created);
  });

  it('attachExistingSkill posts skill_id and returns attached skill', async () => {
    const mockApi = useApi();
    const attached = { id: 11, name: 'Existing' };
    (mockApi.post as unknown as vi.Mock).mockResolvedValueOnce({ data: attached });

    const { attachExistingSkill } = useCompetencySkills();
    const res = await attachExistingSkill(5, 11);

    expect(mockApi.post).toHaveBeenCalledWith('/api/competencies/5/skills', { skill_id: 11 });
    expect(res).toEqual(attached);
  });

  it('listSkillsForCompetency calls GET and returns list', async () => {
    const mockApi = useApi();
    const list = [{ id: 1, name: 'S1' }];
    (mockApi.get as unknown as vi.Mock).mockResolvedValueOnce({ data: list });

    const { listSkillsForCompetency } = useCompetencySkills();
    const res = await listSkillsForCompetency(7);

    expect(mockApi.get).toHaveBeenCalledWith('/api/competencies/7/skills');
    expect(res).toEqual(list);
  });
});
