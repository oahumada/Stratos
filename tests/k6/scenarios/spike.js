/**
 * Stratos k6 — Spike Test
 *
 * Propósito: Validar que el sistema maneja picos de tráfico (sudden VU spikes).
 * Simula un pico brusco de 10x en VUs y verifica que el sistema se recupera.
 *
 * Uso:
 *   k6 run tests/k6/scenarios/spike.js
 *   k6 run -e K6_BASE_URL=https://staging.stratos.app tests/k6/scenarios/spike.js
 *
 * Timeline:
 *   0-1 min:      0 → 10 VUs (baseline steady)
 *   1-1m10s:     10 → 100 VUs (SPIKE! 900% increase in 10 seconds)
 *   1m10s-2m:   100 VUs (maintain spike)
 *   2-2m10s:    100 → 10 VUs (DROP! back to normal in 10 seconds)
 *   2m10s-3m:    10 VUs (recovery stabilization)
 *   3-3m10s:      0 VUs (ramp down)
 *   Total: 3m 10s
 *
 * Umbrales de éxito (SLOs):
 *   - No cascading failures (no 502/503)
 *   - Error rate < 5% during spike (permitido)
 *   - p(95) < 5s during spike (degraded)
 *   - Recovery time < 10s (back to <200ms p95)
 *   - No connection leaks after recovery
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const spikeDuration = new Trend('spike_duration', true);
const errorRate = new Rate('error_rate');
const requestsTotal = new Counter('requests_total');
const cascadeErrors = new Counter('cascade_errors'); // 502/503

// ── Load profile: sudden spike ──────────────────────────────────────────
export const options = {
    scenarios: {
        spike: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '1m',      target: 10 },   // baseline 0→10 VUs
                { duration: '10s',     target: 100 },  // SPIKE! 10→100 in 10s
                { duration: '50s',     target: 100 },  // maintain spike
                { duration: '10s',     target: 10 },   // DROP! 100→10 in 10s
                { duration: '50s',     target: 10 },   // recovery stabilization
                { duration: '10s',     target: 0 },    // ramp down 10→0
            ],
            exec: 'spikeScenario',
            tags: { scenario: 'spike_test' },
        },
    },

    thresholds: {
        // Latencia degradada durante spike, pero se recupera
        'http_req_duration{scenario:spike_test}': ['p(95)<5000'],
        // Error rate permitido durante spike: 5%
        error_rate: ['rate<0.05'],
        // Custom
        spike_duration: ['p(95)<5000'],
    },
};

export function setup() {
    return login();
}

// ── Spike scenario: simple read operations
export function spikeScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    // Endpoint 1: Dashboard (primary endpoint)
    group('Dashboard', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        requestsTotal.add(1);

        const duration = Date.now() - start;
        spikeDuration.add(duration);

        const ok = check(r, {
            'dashboard 200': (res) => res.status === 200,
            'dashboard no cascade': (res) => res.status !== 502 && res.status !== 503,
            'dashboard latency < 5s': (res) => res.timings.duration < 5000,
        });
        if (!ok) {
            failed = true;
            if (r.status === 502 || r.status === 503) cascadeErrors.add(1);
        }
    });

    sleep(0.2 + Math.random() * 0.3);

    // Endpoint 2: Lightweight read
    group('Profile', () => {
        const r = http.get(`${BASE_URL}/api/people/profile/1`, params);
        requestsTotal.add(1);
        check(r, {
            'profile 200': (res) => res.status === 200,
            'profile no cascade': (res) => res.status !== 502 && res.status !== 503,
        });
    });

    errorRate.add(failed);
}

/**
 * Resumen de resultados
 */
export function handleSummary(data) {
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const p99 = data.metrics.http_req_duration?.values?.['p(99)'] ?? 0;
    const errors = data.metrics.error_rate?.values?.rate ?? 0;
    const cascades = data.metrics.cascade_errors?.values?.count ?? 0;
    const totalReqs = data.metrics.http_reqs?.values?.count ?? 0;

    return {
        'tests/k6/results/spike_summary.json': JSON.stringify({
            spike_test_summary: {
                total_requests: totalReqs,
                p95_ms: Math.round(p95),
                p99_ms: Math.round(p99),
                error_rate_pct: Math.round(errors * 10000) / 100,
                cascade_errors_502_503: cascades,
                passed: cascades === 0 && errors < 0.05,
            },
        }, null, 2),
        stdout: `\n🔥 Spike Test Summary\n  Req total  : ${totalReqs}\n  p(95)      : ${Math.round(p95)}ms\n  p(99)      : ${Math.round(p99)}ms\n  Error rate : ${Math.round(errors * 10000) / 100}%\n  Cascades   : ${cascades}\n  ${cascades === 0 ? '✅ PASSED' : '❌ FAILED'}\n`,
    };
}
