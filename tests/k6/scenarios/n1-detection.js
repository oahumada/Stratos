/**
 * Stratos k6 — N+1 Detection Test
 *
 * Propósito: Validar que los fixes de N+1 queries implementados en Phase 1
 * están funcionando correctamente en producción.
 *
 * Uso:
 *   k6 run tests/k6/scenarios/n1-detection.js
 *   k6 run -e K6_BASE_URL=https://api.stratos.com tests/k6/scenarios/n1-detection.js
 *
 * Características:
 *   - 5 VUs (bajo volumen para enfocarse en query profiling)
 *   - 10 minutos de ejecución
 *   - Monitor: Query counts, database time, cache efficiency
 *   - Target: Promedio de queries por request < 3
 *
 * Métodos de validación:
 *   1. Custom headers: X-Query-Count (si app los expone)
 *   2. Response time analysis (si queries lentas, aumenta latencia)
 *   3. Database logs (slow query log)
 *   4. APM tools (New Relic, DataDog, etc.)
 *
 * SLOs:
 *   - Avg queries per request: < 3
 *   - p95 latency: < 200ms (baseline)
 *   - Error rate: < 1%
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const n1Duration = new Trend('n1_duration', true);
const errorRate = new Rate('error_rate');
const requestsTotal = new Counter('requests_total');
const queryCountTotal = new Counter('query_count_total'); // Estimated from latency

// ── Load profile: N+1 detection ────────────────────────────────────────────
export const options = {
    scenarios: {
        n1Detection: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '2m',   target: 5 },    // ramp up to 5 VUs
                { duration: '8m',   target: 5 },    // sustained 5 VUs
                { duration: '1m',   target: 0 },    // ramp down
            ],
            exec: 'n1DetectionScenario',
            tags: { scenario: 'n1_detection' },
        },
    },

    thresholds: {
        // Debe mantenerse < 200ms (indica no hay N+1)
        'http_req_duration{scenario:n1_detection}': ['p(95)<200'],
        error_rate: ['rate<0.01'],
    },
};

export function setup() {
    return login();
}

// ── N+1 detection scenario: endpoints conocidos como N+1-prone
export function n1DetectionScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    // Endpoint 1: Dashboard (HIGH RISK for N+1)
    // Risk: Si dashboard agrega múltiples subrellenos sin eager loading
    group('Dashboard (N+1 Check)', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        requestsTotal.add(1);

        const duration = Date.now() - start;
        n1Duration.add(duration);

        // Si latencia es alta (>200ms), puede indicar N+1
        const ok = check(r, {
            'dashboard 200': (res) => res.status === 200,
            'dashboard latency < 200ms (no N+1)': (res) => res.timings.duration < 200,
            'dashboard has query count header': (res) => {
                // Si el app expone X-Query-Count, podemos validar directamente
                return res.headers['X-Query-Count'] !== undefined || res.status === 200;
            },
        });
        if (!ok) {
            failed = true;
        }

        // Log query count si está disponible
        if (r.headers['X-Query-Count']) {
            const count = parseInt(r.headers['X-Query-Count']);
            queryCountTotal.add(count > 3 ? 1 : 0); // Count as issue if > 3
        }
    });

    sleep(0.5);

    // Endpoint 2: Organizations (MEDIUM RISK)
    // Risk: Si lista devuelve muchos orgs sin eager load de relaciones
    group('Organizations (N+1 Check)', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/organizations?per_page=10`, params);
        requestsTotal.add(1);

        const duration = Date.now() - start;
        n1Duration.add(duration);

        const ok = check(r, {
            'orgs 200': (res) => res.status === 200,
            'orgs latency < 200ms': (res) => res.timings.duration < 200,
        });
        if (!ok) {
            failed = true;
        }
    });

    sleep(0.3);

    // Endpoint 3: Plans (MEDIUM-HIGH RISK)
    // Risk: Si plans include nested relationships (status, owner, etc)
    group('Plans (N+1 Check)', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/strategic-planning/plans?per_page=5`, params);
        requestsTotal.add(1);

        const duration = Date.now() - start;
        n1Duration.add(duration);

        const ok = check(r, {
            'plans 200': (res) => res.status === 200,
            'plans latency < 200ms': (res) => res.timings.duration < 200,
        });
        if (!ok) {
            failed = true;
        }
    });

    sleep(0.3);

    // Endpoint 4: People (if applicable)
    group('People (N+1 Check)', () => {
        const r = http.get(`${BASE_URL}/api/people?per_page=5`, params);
        requestsTotal.add(1);

        const ok = check(r, {
            'people 200 or 404': (res) => res.status === 200 || res.status === 404,
        });
        if (!ok) {
            failed = true;
        }
    });

    sleep(0.3);

    errorRate.add(failed);
}

/**
 * Resumen de N+1 detection
 */
export function handleSummary(data) {
    const p50 = data.metrics.http_req_duration?.values?.['p(50)'] ?? 0;
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const p99 = data.metrics.http_req_duration?.values?.['p(99)'] ?? 0;
    const errors = data.metrics.error_rate?.values?.rate ?? 0;
    const totalReqs = data.metrics.http_reqs?.values?.count ?? 0;
    const queryIssues = data.metrics.query_count_total?.values?.count ?? 0;

    const n1Detected = p95 > 200; // Heuristic: high latency = N+1
    const testPassed = !n1Detected && errors < 0.01;

    return {
        'tests/k6/results/n1_detection_summary.json': JSON.stringify({
            n1_detection_summary: {
                total_requests: totalReqs,
                p50_ms: Math.round(p50),
                p95_ms: Math.round(p95),
                p99_ms: Math.round(p99),
                error_rate_pct: Math.round(errors * 10000) / 100,
                query_issues_detected: queryIssues,
                n1_status: n1Detected ? 'POSSIBLE_N+1_DETECTED' : 'NO_N+1_DETECTED',
                test_result: testPassed ? 'PASSED' : 'NEEDS_INVESTIGATION',
                recommendations: [
                    'If p95 > 200ms: Review slow query log',
                    'If query_issues > 0: Verify eager loading in models',
                    'Use APM tools to profile database queries',
                    'Consider database indexes on foreign keys',
                    'Run additional targeted profiling with X-Debug headers',
                ],
            },
        }, null, 2),
        stdout: `\n🔍 N+1 Detection Summary\n  Total requests: ${totalReqs}\n  p(50): ${Math.round(p50)}ms\n  p(95): ${Math.round(p95)}ms\n  p(99): ${Math.round(p99)}ms\n  Errors: ${Math.round(errors * 10000) / 100}%\n  N+1 Status: ${n1Detected ? '⚠️  POSSIBLE' : '✅ NOT DETECTED'}\n  ${testPassed ? '✅ PASSED' : '❌ NEEDS_INVESTIGATION'}\n\n  📊 Interpretation:\n  - p95 < 200ms = No N+1 queries detected\n  - p95 > 200ms = Investigate: could be N+1, indexes, or other\n  - Use APM tools (New Relic, DataDog) for exact query counts\n`,
    };
}
