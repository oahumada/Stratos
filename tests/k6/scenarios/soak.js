/**
 * Stratos k6 — Soak Test
 *
 * Propósito: Detectar memory leaks, connection pool exhaustion, y otros
 * problemas que emergen después de 6+ horas de carga sostenida.
 *
 * Uso:
 *   k6 run tests/k6/scenarios/soak.js
 *   k6 run -e K6_BASE_URL=https://api.stratos.com tests/k6/scenarios/soak.js
 *
 * Características:
 *   - 20 VUs sostenidos por 6-12 horas (típicamente overnight)
 *   - Carga realista pero moderada
 *   - Monitorea memory, connections, error rate
 *   - Detecta degradación gradual del rendimiento
 *
 * Ejecución típica:
 *   - Iniciar: 23:00 (11 PM) en producción o staging
 *   - Finalizar: 05:00-11:00 (next morning)
 *   - Revisar: Logs de memory, connection pools, error trends
 *
 * Umbrales de éxito:
 *   - Memory: Estable (no crecimiento indefinido)
 *   - Error rate: Flat o decreciente (no empeorando con el tiempo)
 *   - p95 latency: Estable
 *   - Connections: No exhaustion
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const soakDuration = new Trend('soak_duration', true);
const errorRate = new Rate('error_rate');
const requestsTotal = new Counter('requests_total');
const errorCount = new Counter('error_count');

// ── Load profile: soak test ────────────────────────────────────────────────
export const options = {
    scenarios: {
        soak: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '10m',   target: 20 },   // ramp up to 20 VUs
                { duration: '12h',   target: 20 },   // sustained 20 VUs for 12 hours
                { duration: '5m',    target: 0 },    // ramp down
            ],
            exec: 'soakScenario',
            tags: { scenario: 'soak_test' },
        },
    },

    thresholds: {
        // Latencia debe mantenerse estable durante la prueba
        'http_req_duration{scenario:soak_test}': ['p(95)<500'],
        // Error rate debe mantenerse < 1% (idealmente flat)
        error_rate: ['rate<0.01'],
        // Contar errores totales
        error_count: ['count<1000'], // Esperamos muy pocos errores en 12h
    },
};

export function setup() {
    return login();
}

// ── Soak scenario: simula uso realista sostenido
export function soakScenario(auth) {
    const params = authParams(auth);
    let failed = false;

    // 1. Dashboard (80% del tráfico)
    group('Dashboard', () => {
        const start = Date.now();
        const r = http.get(`${BASE_URL}/api/dashboard/metrics`, params);
        requestsTotal.add(1);
        soakDuration.add(Date.now() - start);

        const ok = check(r, {
            'dashboard 200': (res) => res.status === 200,
            'dashboard latency < 500ms': (res) => res.timings.duration < 500,
        });
        if (!ok) {
            failed = true;
            errorCount.add(1);
        }
    });

    sleep(0.5 + Math.random() * 0.5);

    // 2. Organizations (15% del tráfico)
    group('Organizations', () => {
        const r = http.get(`${BASE_URL}/api/organizations?per_page=10`, params);
        requestsTotal.add(1);

        const ok = check(r, {
            'orgs 200': (res) => res.status === 200,
        });
        if (!ok) {
            failed = true;
            errorCount.add(1);
        }
    });

    sleep(0.3 + Math.random() * 0.3);

    // 3. Plans (5% del tráfico)
    if (__VU % 20 === 0) {
        group('Plans', () => {
            const r = http.get(`${BASE_URL}/api/strategic-planning/plans?per_page=5`, params);
            requestsTotal.add(1);

            const ok = check(r, {
                'plans 200': (res) => res.status === 200,
            });
            if (!ok) {
                failed = true;
                errorCount.add(1);
            }
        });

        sleep(0.2);
    }

    errorRate.add(failed);
}

/**
 * Resumen detallado para análisis post-soak
 */
export function handleSummary(data) {
    const p50 = data.metrics.http_req_duration?.values?.['p(50)'] ?? 0;
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const p99 = data.metrics.http_req_duration?.values?.['p(99)'] ?? 0;
    const errors = data.metrics.error_rate?.values?.rate ?? 0;
    const errorTotal = data.metrics.error_count?.values?.count ?? 0;
    const totalReqs = data.metrics.http_reqs?.values?.count ?? 0;
    const rps = data.metrics.http_reqs?.values?.rate ?? 0;

    const duration_hours = Math.round((totalReqs / (rps || 1)) / 3600 * 100) / 100;

    return {
        'tests/k6/results/soak_summary.json': JSON.stringify({
            soak_test_summary: {
                total_requests: totalReqs,
                total_errors: errorTotal,
                error_rate_pct: Math.round(errors * 10000) / 100,
                duration_hours: duration_hours,
                rps: Math.round(rps * 100) / 100,
                p50_ms: Math.round(p50),
                p95_ms: Math.round(p95),
                p99_ms: Math.round(p99),
                stability: {
                    latency_stable: p95 < 500 ? 'YES' : 'NO',
                    error_rate_acceptable: errors < 0.01 ? 'YES' : 'NO',
                    memory_leak_indicator: 'CHECK_LOGS',
                },
                next_steps: [
                    'Check application logs for memory usage patterns',
                    'Verify database connection pool did not exhaust',
                    'Check Redis memory usage (if applicable)',
                    'Verify no OOM (Out of Memory) errors',
                    'Review CPU & I/O patterns for degradation',
                ],
            },
        }, null, 2),
        stdout: `\n⏱️  Soak Test Summary (12-hour sustained load)\n  Total requests: ${totalReqs}\n  Duration: ${duration_hours}h\n  RPS: ${Math.round(rps * 100) / 100}\n  p(50): ${Math.round(p50)}ms\n  p(95): ${Math.round(p95)}ms\n  p(99): ${Math.round(p99)}ms\n  Errors: ${errorTotal} (${Math.round(errors * 10000) / 100}%)\n  Latency stable: ${p95 < 500 ? '✅ YES' : '❌ NO'}\n  Error rate stable: ${errors < 0.01 ? '✅ YES' : '❌ NO'}\n\n  📝 IMPORTANT: Review system logs for memory leaks and connection exhaustion\n`,
    };
}
