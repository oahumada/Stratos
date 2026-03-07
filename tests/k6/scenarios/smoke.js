/**
 * Stratos k6 — Smoke Test
 *
 * Propósito: sanity check rápido (1 VU, 1 iteración) para verificar que todos
 * los endpoints críticos responden correctamente antes de correr load tests.
 *
 * Uso:
 *   k6 run tests/k6/scenarios/smoke.js
 *   k6 run -e K6_BASE_URL=https://staging.stratos.app tests/k6/scenarios/smoke.js
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { authParams, BASE_URL, login } from '../utils/auth.js';

export const options = {
    vus: 1,
    iterations: 1,
    thresholds: {
        // Todos los checks deben pasar
        checks: ['rate==1.0'],
        // Tiempos de respuesta aceptables para sanity check
        http_req_duration: ['p(95)<3000'],
    },
};

export function setup() {
    return login();
}

export default function (auth) {
    const params = authParams(auth);

    // ── 1. Dashboard metrics ───────────────────────────────────────────────
    group('Dashboard', () => {
        const res = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        check(res, {
            'dashboard metrics 200': (r) => r.status === 200,
            'dashboard has data': (r) => {
                try {
                    const body = JSON.parse(r.body);
                    return body !== null;
                } catch {
                    return false;
                }
            },
        });
    });

    sleep(0.3);

    // ── 2. RAGAS metrics ───────────────────────────────────────────────────
    group('RAGAS Metrics', () => {
        const res = http.get(`${BASE_URL}/api/qa/llm-evaluations/metrics/summary`, params);
        check(res, {
            'ragas metrics 200': (r) => r.status === 200,
            'ragas has success flag': (r) => {
                try {
                    return JSON.parse(r.body).success === true;
                } catch {
                    return false;
                }
            },
        });
    });

    sleep(0.3);

    // ── 3. Scenario generation preview ────────────────────────────────────
    group('Scenario Generation — Preview', () => {
        const payload = JSON.stringify({
            company_name: 'Empresa k6 Test S.A.',
            industry: 'technology',
            company_size: 'medium',
            current_headcount: 100,
            planning_horizon: '12_months',
            strategic_objectives: ['Optimizar procesos con IA'],
            challenges: ['Escasez de talento'],
        });

        const res = http.post(
            `${BASE_URL}/api/strategic-planning/scenarios/generate/preview`,
            payload,
            params,
        );

        check(res, {
            'preview 200 or 201': (r) => r.status === 200 || r.status === 201,
            'preview returns prompt': (r) => {
                try {
                    const body = JSON.parse(r.body);
                    return (
                        body.prompt !== undefined ||
                        body.data !== undefined ||
                        body.preview !== undefined
                    );
                } catch {
                    return false;
                }
            },
        });
    });

    sleep(0.3);

    // ── 4. RAGAS evaluations list ──────────────────────────────────────────
    group('RAGAS Evaluations List', () => {
        const res = http.get(`${BASE_URL}/api/qa/llm-evaluations?per_page=5`, params);
        check(res, {
            'evaluations list 200': (r) => r.status === 200,
        });
    });
}
