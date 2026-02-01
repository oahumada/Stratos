import { ref } from 'vue';
import { useApi } from '@/composables/useApi';
import { useNotification } from '@/composables/useNotification';

/**
 * Composable genérico para operaciones CRUD de nodos jerárquicos
 * (Capabilities, Competencies, Skills)
 * 
 * Aplica principio DRY consolidando:
 * - Creación de entidades
 * - Actualización de entidades
 * - Actualización de pivotes
 * - Manejo de errores consistente
 * - Estados de carga
 */

interface CrudConfig {
  entityName: string;        // Nombre singular para mensajes (e.g., "capacidad", "competencia", "skill")
  entityNamePlural: string;  // Nombre plural para endpoints (e.g., "capabilities", "competencies", "skills")
  parentRoute?: string;      // Ruta base del padre (e.g., "/api/strategic-planning/scenarios")
}

export function useNodeCrud(config: CrudConfig) {
  const { post, patch, del, get } = useApi();
  const { showSuccess, showError } = useNotification();

  const saving = ref(false);
  const creating = ref(false);
  const deleting = ref(false);
  const loading = ref(false);

  /**
   * Asegurar cookie CSRF de Sanctum
   */
  async function ensureCsrf() {
    try {
      await get('/sanctum/csrf-cookie');
    } catch (err) {
      // Servidor puede no requerirlo en desarrollo
      console.debug('[ensureCsrf] CSRF cookie request failed, proceeding anyway', err);
    }
  }

  /**
   * Crear y asociar una nueva entidad a su padre
   * @param parentId - ID del nodo padre
   * @param payload - Datos de la entidad (incluye campos de pivot si aplica)
   * @param customEndpoint - Endpoint personalizado (opcional)
   */
  async function createAndAttach(parentId: number | string, payload: any, customEndpoint?: string) {
    creating.value = true;
    await ensureCsrf();
    
    try {
      const endpoint = customEndpoint || `${config.parentRoute}/${parentId}/${config.entityNamePlural}`;
      console.debug(`[useNodeCrud.createAndAttach] POST ${endpoint}`, payload);
      
      const res: any = await post(endpoint, payload);
      const created = res?.data ?? res;
      
      showSuccess(`${capitalizeFirst(config.entityName)} creada exitosamente`);
      return created;
    } catch (error: any) {
      console.error(`[useNodeCrud.createAndAttach] Error creating ${config.entityName}:`, error?.response?.data ?? error);
      showError(`Error creando ${config.entityName}: ${error?.response?.data?.message || error?.message || 'Unknown error'}`);
      throw error;
    } finally {
      creating.value = false;
    }
  }

  /**
   * Actualizar una entidad existente
   * @param entityId - ID de la entidad
   * @param payload - Campos a actualizar
   * @param customEndpoint - Endpoint personalizado (opcional)
   */
  async function updateEntity(entityId: number | string, payload: any, customEndpoint?: string) {
    saving.value = true;
    await ensureCsrf();

    try {
      const endpoint = customEndpoint || `/api/${config.entityNamePlural}/${entityId}`;
      console.debug(`[useNodeCrud.updateEntity] PATCH ${endpoint}`, payload);
      
      const res: any = await patch(endpoint, payload);
      const updated = res?.data ?? res;
      
      showSuccess(`${capitalizeFirst(config.entityName)} actualizada`);
      return updated;
    } catch (error: any) {
      console.error(`[useNodeCrud.updateEntity] Error updating ${config.entityName}:`, error?.response?.data ?? error);
      showError(`Error actualizando ${config.entityName}: ${error?.response?.data?.message || error?.message || 'Unknown error'}`);
      throw error;
    } finally {
      saving.value = false;
    }
  }

  /**
   * Actualizar atributos de pivot (relación many-to-many)
   * @param parentId - ID del nodo padre
   * @param entityId - ID de la entidad
   * @param pivotPayload - Atributos del pivot a actualizar
   * @param pivotRoute - Ruta del pivot (e.g., "scenarios/{id}/capabilities/{id}")
   */
  async function updatePivot(
    parentId: number | string, 
    entityId: number | string, 
    pivotPayload: any,
    pivotRoute?: string
  ) {
    saving.value = true;
    await ensureCsrf();

    try {
      const endpoint = pivotRoute 
        || `${config.parentRoute}/${parentId}/${config.entityNamePlural}/${entityId}`;
      
      console.debug(`[useNodeCrud.updatePivot] PATCH ${endpoint}`, pivotPayload);
      
      const res: any = await patch(endpoint, pivotPayload);
      const updated = res?.data ?? res;
      
      console.debug(`[useNodeCrud.updatePivot] PATCH response`, updated);
      return updated;
    } catch (error: any) {
      console.error(`[useNodeCrud.updatePivot] Error updating pivot:`, error?.response?.data ?? error);
      // No mostrar toast para pivots (puede ser silencioso)
      throw error;
    } finally {
      saving.value = false;
    }
  }

  /**
   * Eliminar una entidad
   * @param entityId - ID de la entidad a eliminar
   * @param customEndpoint - Endpoint personalizado (opcional)
   */
  async function deleteEntity(entityId: number | string, customEndpoint?: string) {
    deleting.value = true;
    await ensureCsrf();

    try {
      const endpoint = customEndpoint || `/api/${config.entityNamePlural}/${entityId}`;
      console.debug(`[useNodeCrud.deleteEntity] DELETE ${endpoint}`);
      
      await del(endpoint);
      
      showSuccess(`${capitalizeFirst(config.entityName)} eliminada`);
      return true;
    } catch (error: any) {
      console.error(`[useNodeCrud.deleteEntity] Error deleting ${config.entityName}:`, error?.response?.data ?? error);
      showError(`Error eliminando ${config.entityName}: ${error?.response?.data?.message || error?.message || 'Unknown error'}`);
      throw error;
    } finally {
      deleting.value = false;
    }
  }

  /**
   * Obtener una entidad por ID
   * @param entityId - ID de la entidad
   * @param customEndpoint - Endpoint personalizado (opcional)
   */
  async function fetchEntity(entityId: number | string, customEndpoint?: string) {
    loading.value = true;

    try {
      const endpoint = customEndpoint || `/api/${config.entityNamePlural}/${entityId}`;
      console.debug(`[useNodeCrud.fetchEntity] GET ${endpoint}`);
      
      const res: any = await get(endpoint);
      const entity = res?.data ?? res;
      
      return entity;
    } catch (error: any) {
      console.error(`[useNodeCrud.fetchEntity] Error fetching ${config.entityName}:`, error?.response?.data ?? error);
      showError(`Error cargando ${config.entityName}`);
      throw error;
    } finally {
      loading.value = false;
    }
  }

  /**
   * Capitalizar primera letra (helper)
   */
  function capitalizeFirst(str: string): string {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  return {
    // Estados
    saving,
    creating,
    deleting,
    loading,

    // Operaciones CRUD
    createAndAttach,
    updateEntity,
    updatePivot,
    deleteEntity,
    fetchEntity,

    // Utilidades
    ensureCsrf,
  };
}

export default useNodeCrud;
