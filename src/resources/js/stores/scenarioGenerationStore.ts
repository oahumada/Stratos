import axios from 'axios';
import { defineStore } from 'pinia';

// Helper to normalize various LLM response shapes into a consistent object.
function normalizeLlMResponse(raw: any): any {
    if (!raw) return raw;

    // If raw is a string, try to parse as JSON, otherwise return as { content: raw }
    if (typeof raw === 'string') {
        try {
            const parsed = JSON.parse(raw);
            return normalizeLlMResponse(parsed);
        } catch (e) {
            return { content: raw };
        }
    }

    // If object with `content` string (common when backend stores assembled text), try parse it
    if (raw && typeof raw === 'object' && typeof raw.content === 'string') {
        try {
            const parsed = JSON.parse(raw.content);
            return normalizeLlMResponse(parsed);
        } catch (e) {
            // keep original object but expose content
            return { ...raw, content: raw.content };
        }
    }

    // If the LLM returned a wrapped key like 'escenario', try to map it
    if (raw.escenario) {
        const esc = raw.escenario;
        const out: any = {};
        out.scenario_metadata = {
            name: esc.nombre || esc.id || null,
            generated_at: esc.fecha_actualizacion || esc.generated_at || null,
            confidence_score: esc.confidence_score || esc.confidence || null,
            raw_escenario: esc,
        };
        out.capabilities = Array.isArray(esc.capabilities) ? esc.capabilities : [];
        out.competencies = Array.isArray(esc.competencies) ? esc.competencies : [];
        out.skills = Array.isArray(esc.skills) ? esc.skills : [];
        out.suggested_roles = Array.isArray(esc.suggested_roles) ? esc.suggested_roles : [];
        out.impact_analysis = Array.isArray(esc.impact_analysis) ? esc.impact_analysis : [];
        out.confidence_score = out.scenario_metadata.confidence_score;
        return out;
    }

    // If already in expected shape, return as-is
    if (raw.scenario_metadata || raw.capabilities || raw.content) {
        if (!raw.confidence_score) {
            raw.confidence_score = raw.scenario_metadata?.confidence_score ?? null;
        }
        return raw;
    }

    // Fallback: return raw wrapped as content
    return { content: raw };
}

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
        prefillDemo() {
            // Prefill store.data with a demo scenario for "Adopción de IA generativa para la empresa"
            this.data.company_name = 'Acme Labs S.A.';
            this.data.industry = 'Tecnología / Software';
            this.data.sub_industry = 'Plataformas de datos e IA';
            this.data.company_size = 450;
            this.data.geographic_scope = 'LatAm';
            this.data.organizational_cycle = 'Anual';
            this.data.current_challenges =
                'Baja adopción de modelos de IA, falta de datos etiquetados y equipos con experiencia limitada en ML.';
            this.data.current_capabilities =
                'Equipos de data engineering básicos, pipelines de datos internos y experiencia en integración de APIs.';
            this.data.current_gaps =
                'Carencia de modelos generativos productivos, no hay prácticas MLOps maduras.';
            this.data.current_roles_count = 120;
            this.data.has_formal_competency_model = false;
            this.data.strategic_goal =
                'Adoptar IA generativa para mejorar productividad y automatizar tareas de atención al cliente y documentación técnica.';
            this.data.target_markets =
                'Mercado latinoamericano; clientes enterprise y pymes tecnológicas';
            this.data.expected_growth =
                'Aumento del 25% en eficiencia operativa en 12 meses';
            this.data.transformation_type = ['automation', 'innovation'];
            this.data.key_initiatives =
                'Pilotos de chatbots generativos, generación automática de documentación, integración con soporte y herramientas internas.';
            this.data.budget_level = 'Medio';
            this.data.talent_availability =
                'Limitada internamente; se requiere contratación y formación.';
            this.data.training_capacity =
                'Moderada; equipo de L&D con programas trimestrales.';
            this.data.technology_maturity =
                'Madurez media: infra de datos existente pero falta orquestación y modelos.';
            this.data.critical_constraints =
                'Regulación de datos y privacidad, riesgo de calidad de respuestas generadas.';
            this.data.time_horizon = '12 meses';
            this.data.urgency_level = 'Alta';
            this.data.milestones =
                '1) Piloto interno (3 meses); 2) Integración con soporte (6 meses); 3) Escalado productivo (12 meses)';
            // keep organization_id untouched so tenant is implicit
        },
        async generate() {
            this.generating = true;
            try {
                const res = await axios.post(
                    '/api/strategic-planning/scenarios/generate',
                    this.preparePayload(),
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
                    this.preparePayload(),
                );
                this.previewPrompt = res.data.data.prompt;
                return this.previewPrompt;
            } catch (e) {
                throw e;
            }
        },

        preparePayload() {
            // sanitize and coerce fields to expected types before sending to backend
            const payload: any = { ...(this.data as any) };

            // ensure geographic_scope and organizational_cycle are strings
            if (
                payload.geographic_scope !== undefined &&
                payload.geographic_scope !== null
            ) {
                payload.geographic_scope = String(payload.geographic_scope);
            }
            if (
                payload.organizational_cycle !== undefined &&
                payload.organizational_cycle !== null
            ) {
                payload.organizational_cycle = String(
                    payload.organizational_cycle,
                );
            }

            // coerce organization_id to integer if present; otherwise remove to avoid type errors
            if (
                payload.organization_id !== undefined &&
                payload.organization_id !== null &&
                payload.organization_id !== ''
            ) {
                const n = Number(payload.organization_id);
                if (!Number.isNaN(n)) payload.organization_id = Math.trunc(n);
                else delete payload.organization_id;
            } else {
                delete payload.organization_id;
            }

            // coerce instruction_id to integer if present; otherwise remove to avoid validation errors
            if (
                payload.instruction_id !== undefined &&
                payload.instruction_id !== null &&
                payload.instruction_id !== ''
            ) {
                const iid = Number(payload.instruction_id);
                if (!Number.isNaN(iid))
                    payload.instruction_id = Math.trunc(iid);
                else delete payload.instruction_id;
            } else {
                delete payload.instruction_id;
            }

            // remove empty instruction string to avoid sending blank values
            if (payload.instruction !== undefined) {
                if (
                    payload.instruction === null ||
                    payload.instruction === ''
                ) {
                    delete payload.instruction;
                }
            }

            return payload;
        },
        async fetchStatus() {
            if (!this.generationId) return;
            const res = await axios.get(
                `/api/strategic-planning/scenarios/generate/${this.generationId}`,
            );
            this.generationStatus = res.data.data.status;
            // Normalize LLM response into a predictable shape for the UI.
            const raw = res.data.data.llm_response;
            this.generationResult = normalizeLlMResponse(raw);
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
