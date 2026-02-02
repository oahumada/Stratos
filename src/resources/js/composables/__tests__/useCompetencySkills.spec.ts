import { describe, it, expect, vi } from 'vitest';

vi.mock('@/composables/useApi', () => {
  return {
    useApi: () => ({
      post: vi.fn(async (url: string, data?: any) => {
        const m = url.match(/\/api\/competencies\/(\d+)\/skills/);
        if (m) {
          const id = 12345;
          const skill = (data && data.skill) ? { id, ...data.skill } : { id };
          return { data: skill };
        }
        return { data: {} };
      }),
      get: vi.fn(async () => ({ data: [] })),
    }),
  };
});

import { useCompetencySkills } from '@/composables/useCompetencySkills';

describe('useCompetencySkills', () => {
  it('createAndAttachSkill calls API and returns created skill', async () => {
    const { createAndAttachSkill } = useCompetencySkills();
    const payload = { name: 'X Skill', category: 'domain' };
    const created = await createAndAttachSkill(77, payload);
    expect(created).toBeTruthy();
    expect(created.id).toBe(12345);
    expect(created.name).toBe('X Skill');
  });

  it('attachExistingSkill calls API and returns result', async () => {
    const { attachExistingSkill } = useCompetencySkills();
    const res = await attachExistingSkill(88, 999);
    expect(res).toBeTruthy();
    // mocked implementation returns { data: {} } for non-create case
  });
});
