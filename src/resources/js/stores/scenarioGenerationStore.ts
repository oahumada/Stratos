import { defineStore } from 'pinia'
import axios from 'axios'

export const useScenarioGenerationStore = defineStore('scenarioGeneration', {
  state: () => ({
    step: 1,
    data: {
      company_name: '',
      industry: '',
      sub_industry: '',
      company_size: null,
      geographic_scope: '',
      organizational_cycle: '',
      current_challenges: '',
      current_capabilities: '',
      current_gaps: '',
      current_roles_count: null,
      has_formal_competency_model: false,
      auto_generate: false,
      strategic_goal: '',
      target_markets: '',
      expected_growth: '',
      transformation_type: [],
      key_initiatives: '',
      budget_level: '',
      talent_availability: '',
      training_capacity: '',
      technology_maturity: '',
      critical_constraints: '',
      time_horizon: '',
      urgency_level: '',
      milestones: '',
      organization_id: null,
    },
    generating: false,
    generationId: null,
    generationStatus: null,
    generationResult: null,
    previewPrompt: null,
    importAfterAccept: false,
    importAutoAccepted: false,
  }),
  actions: {
    next() { this.step = Math.min(5, this.step + 1) },
    prev() { this.step = Math.max(1, this.step - 1) },
    reset() {
      this.step = 1
      this.generationId = null
      this.generationStatus = null
      this.generationResult = null
      // keep data
    },
    async generate() {
      this.generating = true
      try {
        const res = await axios.post('/api/strategic-planning/scenarios/generate', this.data)
        this.generationId = res.data.data.id
        this.generationStatus = res.data.data.status
        this.importAutoAccepted = false
        return res.data
      } catch (e) {
        throw e
      } finally {
        this.generating = false
      }
    },
    async preview() {
      try {
        const res = await axios.post('/api/strategic-planning/scenarios/generate/preview', this.data)
        this.previewPrompt = res.data.data.prompt
        return this.previewPrompt
      } catch (e) {
        throw e
      }
    },
    async fetchStatus() {
      if (!this.generationId) return
      const res = await axios.get(`/api/strategic-planning/scenarios/generate/${this.generationId}`)
      this.generationStatus = res.data.data.status
      this.generationResult = res.data.data.llm_response
      // Auto-accept/import flow: if generation completed and operator chose importAfterAccept,
      // trigger accept once (idempotent guard via importAutoAccepted)
      if (this.generationStatus === 'complete' && this.importAfterAccept && !this.importAutoAccepted) {
        try {
          await this.accept(this.generationId)
          this.importAutoAccepted = true
        } catch (e) {
          // log to console; don't throw to avoid breaking UI polling
          // caller (UI) can surface error if needed
          // eslint-disable-next-line no-console
          console.error('Auto-accept failed', e)
        }
      }
      return res.data
    }
    async accept(generationId?: number) {
      const id = generationId || this.generationId
      if (!id) throw new Error('No generation id provided')
      try {
        const res = await axios.post(`/api/strategic-planning/scenarios/generate/${id}/accept`, {
          import: !!this.importAfterAccept,
        })
        return res.data
      } catch (e) {
        throw e
      }
    }
  }
})

