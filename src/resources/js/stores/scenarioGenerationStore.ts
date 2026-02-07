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
      return res.data
    }
  }
})

