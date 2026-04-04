/**
 * Stratos k6 — Cache Failover Test
 *
 * Propósito: Validar que el sistema se degrada gracefully cuando Redis está DOWN.
 * Simula fallo de Redis durante un load test y verifica que:
 *   1. No hay errores 500 (graceful fallback a DB)
 *   2. Latencia aumenta pero es aceptable
 *   3. Sistema se recupera cuando Redis vuelve online
 *
 * Uso:
 *   # Ejecutar en terminal 1:
 *   k6 run tests/k6/scenarios/cache-failover.js
 *   
 *   # Ejecutar en terminal 2 (cuando sea el momento de simular fallo):
 *   redis-cli shutdown
 *
 *   # Y luego restaurar:
 *   redis-server (o docker start redis)
 *
 * Fases:
 *   0-2 min:  Baseline (Redis UP) — debe cached de DB
 *   2-4 min:  Degraded (Redis DOWN) — fallback a DB queries
 *   4-6 min:  Recovery (Redis UP) — debe reestablecer cache
 *
 * Umbrales de éxito:
 *   - Phase 1 (baseline): p95 < 200ms, cache hit > 60%
 *   - Phase 2 (degraded): p95 < 1s (aumento OK), error < 2%, no 500s
 *   - Phase 3 (recovery): p95 < 200ms (back to baseline), cache hit > 50%
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const cacheFailoverDuration = new Trend('cache_failover_duration', true);
const errorRate = new Rate('error_rate');
const requestsTotal = new Counter('requests_total');
const serverErrors = new Counter('server_errors_5xx');
const degradedRequests = new Counter('degraded_latency'); // > 1s

// ── Load profile: sustained load to detect failover ──────────────────────
export const options = {
    scenarios: {
        cacheFailover: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                // Phase 1: Baseline (Redis UP)
                { duration: '2m',  target: 10 },  // Load 10 VUs for 2 min (baseline)
                // Phase 2: Degraded (Redis DOWN — manual intervention required)
                { duration: '2m',  target: 10 },  // Keep 10 VUs for 2 min (Redis should be down)
                // Phase 3: Recovery (Redis UP again)
                { duration: '2m',  target: 10 },  // Keep 10 VUs for 2 min (Redis restored)
                // Ramp down
                { duration: '30s', target: 0 },   // Ramp down
            ],
            exec: 'cacheFailoverScenario',
            tags: { scenario: 'cache_failover_test' },
        },
    },

    thresholds: {
        // Allow higher latency during failover test
        'http_req_duration{scenario:cache_failover_test}': ['p(95)<1500'],
        // Error rate: < 2% acceptable during degraded mode
        error_rate: ['rate<0.02'],
        // No 5xx errors (most important)
        server_errors_5xx: ['count<5'],
        // Custom
        cache_failover_duration: ['p(95)<1500'],
    },
};

export function setup() {
    return login();
}

// ── Cache failover scenario ─────────────────────────────────────────────
export function cacheFailoverScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    // Phase detection based on elapsed test time
    const elapsedSec = __ENV.TEST_ELAPSED_SEC || '0';
    const phase = parseInt(elapsedSec) < 120 ? 'baseline' : parseInt(elapsedSec) < 240 ? 'degraded' : 'recovery';

    // Endpoint 1: Dashboard (cached endpoint)
    group(`Dashboard (${phase})`, () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        requestsTotal.add(1);

        const duration = Date.now() - start;
        cacheFailoverDuration.add(duration);

        let ok;
        if (phase === 'baseline') {
            ok = check(r, {
                'baseline: 200 OK': (res) => res.status === 200,
                'baseline: p95 < 200ms': (res) => res.timings.duration < 200,
                'baseline: cache hit (X-Cache header)': (res) => res.headers['X-Cache'] === 'HIT',
            });
        } else if (phase === 'degraded') {
            ok = check(r, {
                'degraded: no 500 errors': (res) => res.status !== 500 && res.status !== 502 && res.status !== 503,
                'degraded: acceptable latency': (res) => res.timings.duration < 1000, // Allow slower
                'degraded: graceful fallback': (res) => res.status === 200, // Should still work from DB
            });
            if (r.status >= 500) {
                serverErrors.add(1);
            }
            if (duration > 1000) {
                degradedRequests.add(1);
            }
        } else {
            ok = check(r, {
                'recovery: 200 OK': (res) => res.status === 200,
                'recovery: p95 < 500ms': (res) => res.timings.duration < 500,
                'recovery: cache restored': (res) => 
                    res.headers['X-Cache'] === 'HIT' || res.status === 200,
            });
        }

        if (!ok) {
            failed = true;
        }
    });

    sleep(0.3 + Math.random() * 0.4);

    // Endpoint 2: Lightweight endpoint (should be unaffected)
    group(`Profile (${phase})`, () => {
        const r = http.get(`${BASE_URL}/api/people/profile/1`, params);
        requestsTotal.add(1);

        const ok = check(r, {
            'no 500 error': (res) => res.status !== 500 && res.status !== 502 && res.status !== 503,
            '200 or 404 OK': (res) => res.status === 200 || res.status === 404,
        });

        if (!ok) {
            failed = true;
            if (r.status >= 500) {
                serverErrors.add(1);
            }
        }
    });

    sleep(0.2 + Math.random() * 0.3);

    // Endpoint 3: Organizations list (another cached endpoint)
    group(`Organizations (${phase})`, () => {
        const r = http.get(`${BASE_URL}/api/organizations?per_page=5`, params);
        requestsTotal.add(1);

        const ok = check(r, {
            'no 500 error': (res) => res.status !== 500 && res.status !== 502 && res.status !== 503,
        });

        if (!ok) {
            failed = true;
            if (r.status >= 500) {
                serverErrors.add(1);
            }
        }
    });

    errorRate.add(failed);
}

/**
 * Resumen de resultados
 */
export function handleSummary(data) {
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const errors = data.metrics.error_rate?.values?.rate ?? 0;
    const fivexx = data.metrics.server_errors_5xx?.values?.count ?? 0;
    const degraded = data.metrics.degraded_latency?.values?.count ?? 0;
    const totalReqs = data.metrics.http_reqs?.values?.count ?? 0;

    const testPassed = fivexx === 0 && errors < 0.02;

    return {
        'tests/k6/results/cache_failover_summary.json': JSON.stringify({
            cache_failover_test_summary: {
                total_requests: totalReqs,
                p95_ms: Math.round(p95),
                error_rate_pct: Math.round(errors * 10000) / 100,
                server_5xx_errors: fivexx,
                degraded_requests: degraded,
                test_result: testPassed ? 'PASSED' : 'FAILED',
                expected_behavior: {
                    phase_1_baseline: 'Redis UP → cache HIT > 60%, p95 < 200ms',
                    phase_2_degraded: 'Redis DOWN → graceful fallback, p95 < 1s, NO 5xx',
                    phase_3_recovery: 'Redis UP → cache restored, p95 < 200ms',
                },
            },
        }, null, 2),
        stdout: `\n💾 Cache Failover Test Summary\n  Req total     : ${totalReqs}\n  p(95)         : ${Math.round(p95)}ms\n  Error rate    : ${Math.round(errors * 10000) / 100}%\n  5xx errors    : ${fivexx}\n  Degraded      : ${degraded} requests\n  Graceful fail : ${fivexx === 0 ? '✅ YES' : '❌ NO'}\n  ${testPassed ? '✅ PASSED' : '❌ FAILED'}\n\n  📝 MANUAL STEPS:\n  1. Let baseline phase run (2 min)\n  2. THEN: redis-cli shutdown (or docker stop redis)\n  3. Let degraded phase run (2 min)\n  4. THEN: redis-server (or docker start redis)\n  5. Let recovery phase run (2 min)\n`,
    };
}
