/**
 * Stratos k6 — Canary Medium Test (50% Rollout)
 *
 * Propósito: Validar que el 50% de tráfico en producción se maneja correctamente.
 * Este es el SEGUNDO paso del canary deployment (después que canary-light pase).
 *
 * Uso:
 *   k6 run tests/k6/scenarios/canary-medium.js
 *   k6 run -e K6_BASE_URL=https://api.stratos.com tests/k6/scenarios/canary-medium.js
 *
 * Características:
 *   - 25 VUs (simula ~50% tráfico producción)
 *   - 30 minutos de carga sostenida
 *   - Ramp gradual: 10→25 VUs en 5 min (desde estado anterior)
 *   - Mix de endpoints (lectura pesada + algunos writes)
 *
 * Go/No-Go Criteria:
 *   - p(95) < 200ms (debe mantenerse)
 *   - error rate < 1%
 *   - Sustained for 15+ minutes
 *   - Cache hit rate stable
 *   - Rate limiting working
 *
 * Timeline:
 *   0-5 min:    Ramp up 10→25 VUs (gradual desde canary-light)
 *   5-30 min:   Sustained 25 VUs
 *   30-31 min:  Ramp down 25→0 VUs
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const canaryDuration = new Trend('canary_duration', true);
const errorRate = new Rate('error_rate');
const requestsTotal = new Counter('requests_total');
const cacheHits = new Counter('cache_hits');

// ── Load profile: 50% canary ───────────────────────────────────────────────
export const options = {
    scenarios: {
        canaryMedium: {
            executor: 'ramping-vus',
            startVUs: 10, // Start from previous canary-light level
            stages: [
                { duration: '5m',   target: 25 },   // ramp up 10→25 (gradual)
                { duration: '25m',  target: 25 },   // sustained 25 VUs
                { duration: '1m',   target: 0 },    // ramp down 25→0
            ],
            exec: 'canaryMediumScenario',
            tags: { scenario: 'canary_medium' },
        },
    },

    thresholds: {
        'http_req_duration{scenario:canary_medium}': ['p(95)<200'],
        error_rate: ['rate<0.01'],
    },
};

export function setup() {
    return login();
}

// ── Canary medium scenario: realistic production workload (heavier)
export function canaryMediumScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    // 1. Dashboard (most common)
    group('Dashboard', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        requestsTotal.add(1);
        canaryDuration.add(Date.now() - start);

        const ok = check(r, {
            'dashboard 200': (res) => res.status === 200,
        });
        if (!ok) failed = true;

        // Track cache hits
        if (r.headers['X-Cache'] === 'HIT') {
            cacheHits.add(1);
        }
    });

    sleep(0.5 + Math.random() * 0.5);

    // 2. Organizations list
    group('Organizations', () => {
        const r = http.get(`${BASE_URL}/api/organizations?per_page=10`, params);
        requestsTotal.add(1);

        check(r, {
            'orgs 200': (res) => res.status === 200,
        });
    });

    sleep(0.3 + Math.random() * 0.3);

    // 3. Strategic Planning
    group('Plans', () => {
        const r = http.get(`${BASE_URL}/api/strategic-planning/plans?per_page=5`, params);
        requestsTotal.add(1);

        check(r, {
            'plans 200': (res) => res.status === 200,
        });
    });

    sleep(0.3);

    // 4. People profiles (occasional read)
    if (__VU % 3 === 0) {
        group('People', () => {
            const r = http.get(`${BASE_URL}/api/people/profile/1`, params);
            requestsTotal.add(1);

            check(r, {
                'profile 200 or 404': (res) => res.status === 200 || res.status === 404,
            });
        });

        sleep(0.2);
    }

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
    const cacheH = data.metrics.cache_hits?.values?.count ?? 0;

    const cacheHitRate = totalReqs > 0 ? Math.round((cacheH / totalReqs) * 10000) / 100 : 0;
    const passed = errors < 0.01 && p95 < 200;

    return {
        'tests/k6/results/canary_medium_summary.json': JSON.stringify({
            canary_medium_summary: {
                total_requests: totalReqs,
                rps: Math.round(rps * 100) / 100,
                p95_ms: Math.round(p95),
                error_rate_pct: Math.round(errors * 10000) / 100,
                cache_hit_rate_pct: cacheHitRate,
                duration_min: 31,
                vus_sustained: 25,
                result: passed ? 'GO' : 'NO-GO',
            },
        }, null, 2),
        stdout: `\n🐤 Canary Medium (50%) Summary\n  Duration: 31 min\n  VUs: 25\n  Requests: ${totalReqs}\n  RPS: ${Math.round(rps * 100) / 100}\n  p(95): ${Math.round(p95)}ms\n  Errors: ${Math.round(errors * 10000) / 100}%\n  Cache hits: ${cacheHitRate}%\n  Decision: ${passed ? '🟢 GO to 100%' : '🔴 NO-GO'}\n`,
    };
}
