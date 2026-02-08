import axios from 'axios';
import { defineStore } from 'pinia';

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
        next() {
            this.step = Math.min(5, this.step + 1);
        },
        setField(key: string, value: any) {
            // allow Step components to update store.data fields
            // if nested fields are needed, components should handle structure
            if (Object.prototype.hasOwnProperty.call(this.data, key)) {
                (this.data as any)[key] = value;
            } else {
                // create field if missing
                (this.data as any)[key] = value;
            }
        },
        prev() {
            this.step = Math.max(1, this.step - 1);
        },
        reset() {
            this.step = 1;
            this.generationId = null;
            this.generationStatus = null;
            this.generationResult = null;
            // keep data
        },
        async generate() {
            this.generating = true;
            try {
                const res = await axios.post(
                    '/api/strategic-planning/scenarios/generate',
                    this.data,
                );
                this.generationId = res.data.data.id;
                this.generationStatus = res.data.data.status;
                this.importAutoAccepted = false;
                return res.data;
            } catch (e) {
                throw e;
            } finally {
                this.generating = false;
            }
        },
        async preview() {
            try {
                const res = await axios.post(
                    '/api/strategic-planning/scenarios/generate/preview',
                    this.data,
                );
                this.previewPrompt = res.data.data.prompt;
                return this.previewPrompt;
            } catch (e) {
                throw e;
            }
        },
        async fetchStatus() {
            if (!this.generationId) return;
            const res = await axios.get(
                `/api/strategic-planning/scenarios/generate/${this.generationId}`,
            );
            this.generationStatus = res.data.data.status;
            this.generationResult = res.data.data.llm_response;
            // Auto-accept/import flow: if generation completed and operator chose importAfterAccept,
            // trigger accept once (idempotent guard via importAutoAccepted)
            if (
                this.generationStatus === 'complete' &&
                this.importAfterAccept &&
                !this.importAutoAccepted
            ) {
                try {
                    await this.accept(this.generationId);
                    this.importAutoAccepted = true;
                } catch (e) {
                    // log to console; don't throw to avoid breaking UI polling
                    // caller (UI) can surface error if needed
                    // eslint-disable-next-line no-console
                    console.error('Auto-accept failed', e);
                }
            }
            return res.data;
        },
        async accept(generationId?: number) {
            const id = generationId || this.generationId;
            if (!id) throw new Error('No generation id provided');
            try {
                const res = await axios.post(
                    `/api/strategic-planning/scenarios/generate/${id}/accept`,
                    {
                        import: !!this.importAfterAccept,
                    },
                );
                return res.data;
            } catch (e) {
                throw e;
            }
        },
        validate() {
            // returns { valid: boolean, errors: string[] }
            const d: any = this.data;
            const errors: string[] = [];
            const critical = [
                'company_name',
                'industry',
                'company_size',
                'current_challenges',
                'current_capabilities',
                'strategic_goal',
                'key_initiatives',
                'time_horizon',
            ];
            critical.forEach((k) => {
                if (!d[k]) {
                    // human-friendly labels
                    const labelMap: Record<string, string> = {
                        company_name: 'Nombre de la organización',
                        industry: 'Industria',
                        company_size: 'Tamaño (personas)',
                        current_challenges: 'Desafíos',
                        current_capabilities: 'Capacidades existentes',
                        strategic_goal: 'Objetivo principal',
                        key_initiatives: 'Iniciativas clave',
                        time_horizon: 'Plazo',
                    };
                    errors.push(labelMap[k] || k);
                }
            });
            return { valid: errors.length === 0, errors };
        },
    },
});
