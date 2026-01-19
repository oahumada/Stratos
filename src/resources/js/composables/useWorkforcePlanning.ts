import { useApi } from '@/composables/useApi'
import type { WorkforcePlan, ScopeUnit, ScopeRole, TransformationProject, TalentRisk, PlanDocument, PlanStatistics } from '@/types/workforcePlanning'

export function useWorkforcePlanning() {
  const api = useApi()
  const base = '/api/workforce-planning/workforce-plans'

  const listPlans = (params?: any) => api.get(base, params)
  const createPlan = (payload: Partial<WorkforcePlan>) => api.post(base, payload)
  const getPlan = (id: number | string) => api.get(`${base}/${id}`)
  const updatePlan = (id: number | string, payload: Partial<WorkforcePlan>) => api.patch(`${base}/${id}`, payload)
  const deletePlan = (id: number | string) => api.delete(`${base}/${id}`)

  const addScopeUnit = (id: number | string, payload: Partial<ScopeUnit>) => api.post(`${base}/${id}/scope-units`, payload)
  const addScopeRole = (id: number | string, payload: Partial<ScopeRole>) => api.post(`${base}/${id}/scope-roles`, payload)
  const addTransformationProject = (id: number | string, payload: Partial<TransformationProject>) => api.post(`${base}/${id}/transformation-projects`, payload)
  const addTalentRisk = (id: number | string, payload: Partial<TalentRisk>) => api.post(`${base}/${id}/talent-risks`, payload)

  const analyzeRisks = (id: number | string) => api.post(`${base}/${id}/analyze-risks`, {})
  const generateScopeDocument = (id: number | string, meta?: Partial<PlanDocument>) => api.post(`${base}/${id}/scope-document`, meta || {})
  const getStatistics = (id: number | string) => api.get(`${base}/${id}/statistics`) as Promise<PlanStatistics>

  const approve = (id: number | string) => api.post(`${base}/${id}/approve`, {})
  const activate = (id: number | string) => api.post(`${base}/${id}/activate`, {})
  const archive = (id: number | string) => api.post(`${base}/${id}/archive`, {})

  return {
    listPlans,
    createPlan,
    getPlan,
    updatePlan,
    deletePlan,
    addScopeUnit,
    addScopeRole,
    addTransformationProject,
    addTalentRisk,
    analyzeRisks,
    generateScopeDocument,
    getStatistics,
    approve,
    activate,
    archive,
  }
}
