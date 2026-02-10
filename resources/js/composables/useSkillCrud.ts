import { useNodeCrud } from './useNodeCrud';

/**
 * Composable especializado para operaciones CRUD de Skills
 * Extiende useNodeCrud con lógica específica de skills
 */
export function useSkillCrud() {
  const nodeCrud = useNodeCrud({
    entityName: 'skill',
    entityNamePlural: 'skills',
  });

  /**
   * Actualizar una skill
   */
  async function updateSkill(skillId: number | string, payload: {
    name?: string;
    description?: string;
    category?: string;
    complexity_level?: string;
    scope_type?: string;
    domain_tag?: string;
    is_critical?: boolean;
  }) {
    // Limpiar campos undefined
    const cleanPayload = Object.fromEntries(
      Object.entries(payload).filter(([_, v]) => v !== undefined)
    );
    
    return nodeCrud.updateEntity(skillId, cleanPayload);
  }

  /**
   * Actualizar pivot competency_skills
   */
  async function updateSkillPivot(
    competencyId: number | string,
    skillId: number | string,
    pivotData: {
      weight?: number;
    }
  ) {
    // El endpoint para skill pivot puede variar según contexto
    // Intentar múltiples endpoints como fallback
    const endpoints = [
      `/api/competencies/${competencyId}/skills/${skillId}`,
      `/api/skills/${skillId}/pivot`,
    ];

    for (const endpoint of endpoints) {
      try {
        return await nodeCrud.updatePivot(competencyId, skillId, pivotData, endpoint);
      } catch (error) {
        console.debug(`[useSkillCrud] Pivot update failed for ${endpoint}, trying next...`);
        // Si es el último endpoint, lanzar el error
        if (endpoint === endpoints[endpoints.length - 1]) {
          throw error;
        }
      }
    }
  }

  /**
   * Eliminar una skill
   */
  async function deleteSkill(skillId: number | string) {
    return nodeCrud.deleteEntity(skillId);
  }

  /**
   * Obtener una skill por ID
   */
  async function fetchSkill(skillId: number | string) {
    return nodeCrud.fetchEntity(skillId);
  }

  return {
    // Estados del CRUD genérico
    ...nodeCrud,

    // Operaciones específicas de skills
    updateSkill,
    updateSkillPivot,
    deleteSkill,
    fetchSkill,
  };
}

export default useSkillCrud;
