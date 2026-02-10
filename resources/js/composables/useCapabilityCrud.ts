import { useNodeCrud } from './useNodeCrud';

/**
 * Composable especializado para operaciones CRUD de Capabilities
 * Extiende useNodeCrud con lógica específica de capabilities
 */
export function useCapabilityCrud() {
  const nodeCrud = useNodeCrud({
    entityName: 'capacidad',
    entityNamePlural: 'capabilities',
    parentRoute: '/api/strategic-planning/scenarios',
  });

  /**
   * Crear una capability y asociarla a un scenario
   */
  async function createCapabilityForScenario(scenarioId: number | string, payload: {
    name: string;
    description?: string;
    importance?: number;
    type?: string;
    category?: string;
    // Pivot attributes
    strategic_role?: string;
    strategic_weight?: number;
    priority?: number;
    rationale?: string;
    required_level?: number;
    is_critical?: boolean;
  }) {
    return nodeCrud.createAndAttach(scenarioId, payload);
  }

  /**
   * Actualizar una capability
   */
  async function updateCapability(capabilityId: number | string, payload: {
    name?: string;
    description?: string;
    importance?: number;
    position_x?: number;
    position_y?: number;
    type?: string;
    category?: string;
  }) {
    return nodeCrud.updateEntity(capabilityId, payload);
  }

  /**
   * Actualizar pivot scenario_capabilities
   */
  async function updateCapabilityPivot(
    scenarioId: number | string,
    capabilityId: number | string,
    pivotData: {
      strategic_role?: string;
      strategic_weight?: number;
      priority?: number;
      rationale?: string;
      required_level?: number;
      is_critical?: boolean;
    }
  ) {
    return nodeCrud.updatePivot(scenarioId, capabilityId, pivotData);
  }

  /**
   * Eliminar una capability
   */
  async function deleteCapability(capabilityId: number | string) {
    return nodeCrud.deleteEntity(capabilityId);
  }

  /**
   * Obtener una capability por ID
   */
  async function fetchCapability(capabilityId: number | string) {
    return nodeCrud.fetchEntity(capabilityId);
  }

  return {
    // Estados del CRUD genérico
    ...nodeCrud,

    // Operaciones específicas de capabilities
    createCapabilityForScenario,
    updateCapability,
    updateCapabilityPivot,
    deleteCapability,
    fetchCapability,
  };
}

export default useCapabilityCrud;
