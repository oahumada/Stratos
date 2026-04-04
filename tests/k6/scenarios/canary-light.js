/**
 * Stratos k6 — Canary Light Test (10% Rollout)
 *
 * Propósito: Validar que el 10% de tráfico en producción se maneja correctamente.
 * Este es el PRIMER paso del canary deployment.
 *
 * Uso:
 *   k6 run tests/k6/scenarios/canary-light.js
 *   k6 run -e K6_BASE_URL=https://api.stratos.com tests/k6/scenarios/canary-light.js
 *
 * Características:
 *   - 10 VUs (simula ~10% tráfico producción)
 *   - 20 minutos de carga sostenida
 *   - Ramp gradual: 0→10 VUs en 2 min
 *   - Endpoints variados (lectura + escritura ligera)
 *
 * Go/No-Go Criteria:
 *   - p(95) < 200ms
 *   - error rate < 1%
 *   - Sustained for 10+ minutes
 *
 * Timeline:
 *   0-2 min:    Ramp up 0→10 VUs
 *   2-20 min:   Sustained 10 VUs
 *   20-21 min:  Ramp down 10→0 VUs
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const canaryDuration = new Trend('canary_duration', true);
const errorRate = new Rate('error_rate');
const requestsTotal = new Counter('requests_total');

// ── Load profile: 10% canary ───────────────────────────────────────────────
export const options = {
    scenarios: {
        canaryLight: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '2m',   target: 10 },   // ramp up 0→10
                { duration: '18m',  target: 10 },   // sustained 10 VUs
                { duration: '1m',   target: 0 },    // ramp down 10→0
            ],
            exec: 'canaryScenario',
            tags: { scenario: 'canary_light' },
        },
    },

    thresholds: {
        'http_req_duration{scenario:canary_light}': ['p(95)<200'],
        error_rate: ['rate<0.01'],
    },
};

export function setup() {
    return login();
}

// ── Canary scenario: realistic production workload (light)
export function canaryScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    // 1. Dashboard (most common operation)
    group('Dashboard', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        requestsTotal.add(1);
        canaryDuration.add(Date.now() - start);

        const ok = check(r, {
            'dashboard 200': (res) => res.status === 200,
        });
        if (!ok) failed = true;
    });

    sleep(0.5 + Math.random() * 0.5);

    // 2. Organizations list (read)
    group('Organizations', () => {
        const r = http.get(`${BASE_URL}/api/organizations?per_page=10`, params);
        requestsTotal.add(1);

        const ok = check(r, {
            'orgs 200': (res) => res.status === 200,
        });
        if (!ok) failed = true;
    });

    sleep(0.3 + Math.random() * 0.3);

    // 3. Strategic Planning (read)
    group('Plans', () => {
        const r = http.get(`${BASE_URL}/api/strategic-planning/plans?per_page=5`, params);
        requestsTotal.add(1);

        check(r, { 'plans 200': (res) => res.status === 200 });
    });

    sleep(0.3);

    errorRate.add(failed);
}

/**
 * Resumen
 */
export function handleSummary(data) {
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const errors = data.metrics.error_rate?.values?.rate ?? 0;
    const totalReqs = data.metrics.http_reqs?.values?.count ?? 0;
    const rps = data.metrics.http_reqs?.values?.rate ?? 0;

    const passed = errors < 0.01 && p95 < 200;

    return {
        'tests/k6/results/canary_light_summary.json': JSON.stringify({
            canary_light_summary: {
                total_requests: totalReqs,
                rps: Math.round(rps * 100) / 100,
                p95_ms: Math.round(p95),
                error_rate_pct: Math.round(errors * 10000) / 100,
                duration_min: 20,
                vus: 10,
                result: passed ? 'GO' : 'NO-GO',
            },
        }, null, 2),
        stdout: `\n🐤 Canary Light (10%) Summary\n  Duration: 20 min\n  VUs: 10\n  Requests: ${totalReqs}\n  RPS: ${Math.round(rps * 100) / 100}\n  p(95): ${Math.round(p95)}ms\n  Errors: ${Math.round(errors * 10000) / 100}%\n  Decision: ${passed ? '🟢 GO to 50%' : '🔴 NO-GO'}\n`,
    };
}
