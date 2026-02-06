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
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useScenarioGenerationStore = defineStore('scenarioGeneration', () => {
  const loading = ref(false);
  const error = ref<string | null>(null);
  const data = ref<any>({});
  const generationId = ref<number | null>(null);
  const result = ref<any | null>(null);

  const setField = (key: string, value: any) => {
    data.value[key] = value;
  };

  const generate = async () => {
    loading.value = true;
    error.value = null;
    try {
      const res = await fetch('/api/strategic-planning/scenarios/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data.value),
      });
      if (!res.ok) throw new Error('Error requesting generation');
      const json = await res.json();
      generationId.value = json.data?.id ?? json.data?.generation_id ?? null;
      return json;
    } catch (e: any) {
      error.value = e.message || 'Unknown error';
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const fetchResult = async (id?: number) => {
    const gid = id ?? generationId.value;
    if (!gid) throw new Error('No generation id');
    loading.value = true;
    try {
      const res = await fetch(`/api/strategic-planning/scenarios/generate/${gid}`);
      if (!res.ok) throw new Error('Error fetching generation');
      const json = await res.json();
      result.value = json.data ?? null;
      return json;
    } finally {
      loading.value = false;
    }
  };

  return { loading, error, data, generationId, result, setField, generate, fetchResult };
});
