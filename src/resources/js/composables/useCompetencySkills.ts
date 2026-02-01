import { useApi } from '@/composables/useApi';

export function useCompetencySkills() {
  const { post, get } = useApi();

  async function createAndAttachSkill(compId: number | string, skillPayload: any) {
    if (!compId) throw new Error('compId required');
    // backend endpoint accepts { skill: { ... } } to create and attach in transaction
    try {
      const res: any = await post(`/api/competencies/${compId}/skills`, { skill: skillPayload });
      return res?.data ?? res;
    } catch (err: any) {
      // Re-lanzar el error para que se capture en el componente
      throw err;
    }
  }

  async function attachExistingSkill(compId: number | string, skillId: number) {
    if (!compId) throw new Error('compId required');
    if (!skillId) throw new Error('skillId required');
    try {
      const res: any = await post(`/api/competencies/${compId}/skills`, { skill_id: skillId });
      return res?.data ?? res;
    } catch (err: any) {
      // Re-lanzar el error para que se capture en el componente
      throw err;
    }
  }

  async function listSkillsForCompetency(compId: number | string) {
    if (!compId) return [];
    const res: any = await get(`/api/competencies/${compId}/skills`);
    return res?.data ?? res;
  }

  return {
    createAndAttachSkill,
    attachExistingSkill,
    listSkillsForCompetency,
  };
}

export default useCompetencySkills;
