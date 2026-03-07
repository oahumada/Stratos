/**
 * Stratos k6 — Load Test
 *
 * Propósito: medir el comportamiento de la API bajo carga realista de producción.
 * Simula múltiples usuarios concurrentes navegando el dashboard, leyendo
 * métricas y lanzando previews de generación de escenarios.
 *
 * Escenarios:
 *   - readHeavy:    20 VUs leyendo dashboard/métricas (carga sostenida 5 min)
 *   - previewLoad:  5 VUs generando previews de escenarios
 *   - ragasPolling: 10 VUs consultando métricas RAGAS continuamente
 *
 * Uso:
 *   k6 run tests/k6/scenarios/load.js
 *   k6 run -e K6_BASE_URL=https://staging.stratos.app tests/k6/scenarios/load.js
 *
 * Umbrales de éxito (SLOs):
 *   - p(95) < 2s  para endpoints de lectura
 *   - p(95) < 5s  para endpoints de generación
 *   - error rate  < 1%
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const previewDuration = new Trend('scenario_preview_duration', true);
const ragasQueryDuration = new Trend('ragas_query_duration', true);
const errorRate = new Rate('error_rate');
const requestsTotal = new Counter('requests_total');

// ── Load profile ───────────────────────────────────────────────────────────
export const options = {
    scenarios: {
        // 1. Dashboard + métricas — carga sostenida, 20 VUs
        readHeavy: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '30s', target: 10 },  // ramp up
                { duration: '3m',  target: 20 },  // steady state
                { duration: '30s', target: 0 },   // ramp down
            ],
            exec: 'readHeavyScenario',
            tags: { scenario: 'read_heavy' },
        },

        // 2. Scenario preview — 5 VUs (más lento, simula uso real del wizard)
        previewLoad: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '1m',  target: 3 },
                { duration: '2m',  target: 5 },
                { duration: '30s', target: 0 },
            ],
            exec: 'previewScenario',
            tags: { scenario: 'preview_load' },
            startTime: '30s', // empieza después del ramp-up de readHeavy
        },

        // 3. RAGAS polling — 10 VUs leyendo métricas
        ragasPolling: {
            executor: 'constant-vus',
            vus: 10,
            duration: '4m',
            exec: 'ragasPollingScenario',
            tags: { scenario: 'ragas_polling' },
            startTime: '30s',
        },
    },

    thresholds: {
        // Lectura: p95 < 2s
        'http_req_duration{scenario:read_heavy}':    ['p(95)<2000'],
        // Preview: p95 < 5s (genera prompt, costoso)
        'http_req_duration{scenario:preview_load}':  ['p(95)<5000'],
        // RAGAS: p95 < 1.5s (solo lectura DB)
        'http_req_duration{scenario:ragas_polling}': ['p(95)<1500'],
        // Tasa de error global < 1%
        error_rate:   ['rate<0.01'],
        // Custom trend thresholds
        scenario_preview_duration: ['p(95)<5000'],
        ragas_query_duration:      ['p(95)<1500'],
    },
};

export function setup() {
    return login();
}

// ── Ejecutores ────────────────────────────────────────────────────────────

export function readHeavyScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    group('Dashboard Metrics', () => {
        const r = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        requestsTotal.add(1);
        const ok = check(r, {
            'dashboard 200': (res) => res.status === 200,
            'dashboard p95 < 2s': (res) => res.timings.duration < 2000,
        });
        if (!ok) { failed = true; }
    });

    sleep(0.5 + Math.random() * 0.5);

    group('People Profile', () => {
        // Simula apertura de un perfil (organización real tendría IDs reales)
        const r = http.get(`${BASE_URL}/api/people/profile/1`, params);
        requestsTotal.add(1);
        check(r, {
            'profile 200 or 404': (res) => res.status === 200 || res.status === 404,
        });
    });

    sleep(0.3 + Math.random() * 0.3);

    errorRate.add(failed);
}

export function previewScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    group('Scenario Preview', () => {
        const start = Date.now();
        const payload = JSON.stringify({
            company_name: `Empresa Carga ${__VU} S.A.`,
            industry: ['technology', 'finance', 'healthcare', 'retail'][__VU % 4],
            company_size: 'medium',
            current_headcount: 50 + (__VU * 10),
            planning_horizon: '12_months',
            strategic_objectives: ['Modernización digital', 'Reducción de costos'],
            challenges: ['Brecha de habilidades', 'Alta rotación'],
        });

        const r = http.post(
            `${BASE_URL}/api/strategic-planning/scenarios/generate/preview`,
            payload,
            params,
        );
        requestsTotal.add(1);

        const duration = Date.now() - start;
        previewDuration.add(duration);

        const ok = check(r, {
            'preview 200': (res) => res.status === 200 || res.status === 201,
            'preview has content': (res) => res.body && res.body.length > 50,
        });
        if (!ok) { failed = true; }
    });

    sleep(2 + Math.random() * 2); // usuarios reales piensan antes de generar
    errorRate.add(failed);
}

export function ragasPollingScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    group('RAGAS Summary', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/qa/llm-evaluations/metrics/summary`, params);
        requestsTotal.add(1);

        ragasQueryDuration.add(Date.now() - start);

        const ok = check(r, {
            'ragas 200': (res) => res.status === 200,
            'ragas has success': (res) => {
                try { return JSON.parse(res.body).success === true; } catch { return false; }
            },
        });
        if (!ok) { failed = true; }
    });

    sleep(0.5 + Math.random() * 0.5);

    group('RAGAS List (paginated)', () => {
        const r = http.get(`${BASE_URL}/api/qa/llm-evaluations?per_page=10`, params);
        requestsTotal.add(1);
        check(r, { 'ragas list 200': (res) => res.status === 200 });
    });

    sleep(0.5);
    errorRate.add(failed);
}
