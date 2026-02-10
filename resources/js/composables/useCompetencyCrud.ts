import { useNodeCrud } from './useNodeCrud';

/**
 * Composable especializado para operaciones CRUD de Competencies
 * Extiende useNodeCrud con lógica específica de competencies
 */
export function useCompetencyCrud() {
  const nodeCrud = useNodeCrud({
    entityName: 'competencia',
    entityNamePlural: 'competencies',
  });

  /**
   * Crear una competency y asociarla a una capability dentro de un scenario
   */
  async function createCompetencyForCapability(
    scenarioId: number | string,
    capabilityId: number | string,
    payload: {
      name: string;
      description?: string;
      skills?: number[]; // Array de skill IDs
      // Pivot attributes para capability_competencies
      weight?: number;
      priority?: number;
      required_level?: number;
      is_required?: boolean;
      rationale?: string;
    }
  ) {
    const endpoint = `/api/strategic-planning/scenarios/${scenarioId}/capabilities/${capabilityId}/competencies`;
    return nodeCrud.createAndAttach(capabilityId, payload, endpoint);
  }

  /**
   * Actualizar una competency
   */
  async function updateCompetency(competencyId: number | string, payload: {
    name?: string;
    description?: string;
    skills?: number[]; // Array de skill IDs
  }) {
    return nodeCrud.updateEntity(competencyId, payload);
  }

  /**
   * Actualizar pivot capability_competencies
   */
  async function updateCompetencyPivot(
    scenarioId: number | string,
    capabilityId: number | string,
    competencyId: number | string,
    pivotData: {
      weight?: number;
      priority?: number;
      required_level?: number;
      is_required?: boolean;
      is_critical?: boolean;
      rationale?: string;
    }
  ) {
    const pivotRoute = `/api/strategic-planning/scenarios/${scenarioId}/capabilities/${capabilityId}/competencies/${competencyId}`;
    return nodeCrud.updatePivot(capabilityId, competencyId, pivotData, pivotRoute);
  }

  /**
   * Eliminar una competency
   */
  async function deleteCompetency(competencyId: number | string) {
    return nodeCrud.deleteEntity(competencyId);
  }

  /**
   * Obtener una competency por ID
   */
  async function fetchCompetency(competencyId: number | string) {
    return nodeCrud.fetchEntity(competencyId);
  }

  return {
    // Estados del CRUD genérico
    ...nodeCrud,

    // Operaciones específicas de competencies
    createCompetencyForCapability,
    updateCompetency,
    updateCompetencyPivot,
    deleteCompetency,
    fetchCompetency,
  };
}

export default useCompetencyCrud;
