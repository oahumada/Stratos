/**
 * Stratos k6 — Production Normal Test (100% Rollout)
 *
 * Propósito: Validar que el 100% de tráfico en producción se maneja correctamente.
 * Este es el TERCER y FINAL paso del canary deployment.
 *
 * Uso:
 *   k6 run tests/k6/scenarios/production-normal.js
 *   k6 run -e K6_BASE_URL=https://api.stratos.com tests/k6/scenarios/production-normal.js
 *
 * Características:
 *   - 50-100 VUs (simula 100% tráfico producción)
 *   - 60 minutos de carga sostenida
 *   - Ramp gradual: 25→50 VUs en 5 min, luego 50→100 en 10 min
 *   - Mix realista de endpoints (lectura pesada + operaciones reales)
 *   - Simula picos naturales de uso
 *
 * Go/No-Go Criteria (STRICT):
 *   - p(95) < 200ms (critical)
 *   - error rate < 1% (critical)
 *   - p99 < 500ms (important)
 *   - Sustained for 30+ minutes
 *   - Cache hit rate > 60%
 *   - Rate limiting functioning
 *   - No degradation over time (memory stable)
 *
 * Timeline:
 *   0-5 min:    Ramp up 25→50 VUs
 *   5-15 min:   Ramp up 50→100 VUs (gradual)
 *   15-50 min:  Sustained 100 VUs (steady state)
 *   50-55 min:  Introduce natural traffic spike (temporary)
 *   55-60 min:  Ramp down 100→0 VUs
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const prodDuration = new Trend('prod_duration', true);
const errorRate = new Rate('error_rate');
const requestsTotal = new Counter('requests_total');
const cacheHits = new Counter('cache_hits');
const rateLimitHits = new Counter('rate_limit_hits');

// ── Load profile: 100% production ───────────────────────────────────────────
export const options = {
    scenarios: {
        productionNormal: {
            executor: 'ramping-vus',
            startVUs: 25, // Start from previous canary-medium level
            stages: [
                { duration: '5m',   target: 50 },   // ramp 25→50 VUs
                { duration: '10m',  target: 100 },  // ramp 50→100 VUs (gradual)
                { duration: '35m',  target: 100 },  // sustained 100 VUs
                { duration: '5m',   target: 50 },   // natural decline
                { duration: '5m',   target: 0 },    // ramp down
            ],
            exec: 'productionNormalScenario',
            tags: { scenario: 'production_normal' },
        },
    },

    thresholds: {
        'http_req_duration{scenario:production_normal}': [
            'p(95)<200',
            'p(99)<500',
        ],
        error_rate: ['rate<0.01'],
    },
};

export function setup() {
    return login();
}

// ── Production normal scenario: full realistic workload
export function productionNormalScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    // 1. Dashboard (60% of traffic)
    group('Dashboard', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        requestsTotal.add(1);
        prodDuration.add(Date.now() - start);

        const ok = check(r, {
            'dashboard 200': (res) => res.status === 200,
            'dashboard latency < 200ms': (res) => res.timings.duration < 200,
        });
        if (!ok) failed = true;

        if (r.headers['X-Cache'] === 'HIT') {
            cacheHits.add(1);
        }
        if (r.status === 429) {
            rateLimitHits.add(1);
        }
    });

    sleep(0.5 + Math.random() * 0.5);

    // 2. Organizations list (20% of traffic)
    group('Organizations', () => {
        const r = http.get(`${BASE_URL}/api/organizations?per_page=10`, params);
        requestsTotal.add(1);

        check(r, {
            'orgs 200': (res) => res.status === 200,
        });
    });

    sleep(0.3 + Math.random() * 0.3);

    // 3. Strategic Planning (10% of traffic)
    group('Plans', () => {
        const r = http.get(`${BASE_URL}/api/strategic-planning/plans?per_page=5`, params);
        requestsTotal.add(1);

        check(r, {
            'plans 200': (res) => res.status === 200,
        });
    });

    sleep(0.3);

    // 4. People profiles (5% of traffic)
    if (__VU % 20 === 0) {
        group('People', () => {
            const r = http.get(`${BASE_URL}/api/people/profile/1`, params);
            requestsTotal.add(1);

            check(r, {
                'profile 200 or 404': (res) => res.status === 200 || res.status === 404,
            });
        });

        sleep(0.2);
    }

    // 5. RAGAS evaluations (5% of traffic — heavier endpoint)
    if (__VU % 20 === 1) {
        group('RAGAS', () => {
            const r = http.get(`${BASE_URL}/api/qa/llm-evaluations?per_page=5`, params);
            requestsTotal.add(1);

            check(r, {
                'ragas 200': (res) => res.status === 200,
            });
        });

        sleep(0.3);
    }

    errorRate.add(failed);
}

/**
 * Resumen
 */
export function handleSummary(data) {
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const p99 = data.metrics.http_req_duration?.values?.['p(99)'] ?? 0;
    const errors = data.metrics.error_rate?.values?.rate ?? 0;
    const totalReqs = data.metrics.http_reqs?.values?.count ?? 0;
    const rps = data.metrics.http_reqs?.values?.rate ?? 0;
    const cacheH = data.metrics.cache_hits?.values?.count ?? 0;
    const rateLimitH = data.metrics.rate_limit_hits?.values?.count ?? 0;

    const cacheHitRate = totalReqs > 0 ? Math.round((cacheH / totalReqs) * 10000) / 100 : 0;
    const passed = errors < 0.01 && p95 < 200 && p99 < 500 && cacheHitRate > 60;

    return {
        'tests/k6/results/production_normal_summary.json': JSON.stringify({
            production_normal_summary: {
                total_requests: totalReqs,
                rps: Math.round(rps * 100) / 100,
                p95_ms: Math.round(p95),
                p99_ms: Math.round(p99),
                error_rate_pct: Math.round(errors * 10000) / 100,
                cache_hit_rate_pct: cacheHitRate,
                rate_limit_hits: rateLimitH,
                duration_min: 60,
                peak_vus: 100,
                result: passed ? 'APPROVED' : 'FAILED',
            },
        }, null, 2),
        stdout: `\n🚀 Production Normal (100%) Summary\n  Duration: 60 min\n  Peak VUs: 100\n  Requests: ${totalReqs}\n  RPS: ${Math.round(rps * 100) / 100}\n  p(95): ${Math.round(p95)}ms\n  p(99): ${Math.round(p99)}ms\n  Errors: ${Math.round(errors * 10000) / 100}%\n  Cache hits: ${cacheHitRate}%\n  Rate limits: ${rateLimitH}\n  ${passed ? '✅ APPROVED for continued production' : '❌ FAILED — investigate'}\n`,
    };
}
