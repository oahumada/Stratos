/**
 * Stratos k6 — Stress / Spike Test
 *
 * Propósito: llevar el sistema más allá de la carga esperada para encontrar
 * el punto de quiebre y validar tolerancia a fallos.
 *
 * Fases:
 *   Ramp-up    →  0 → 30 VUs en 1 min
 *   Steady     →  30 VUs durante 3 min (carga alta sostenida)
 *   Spike      →  30 → 60 VUs en 30s (pico brusco)
 *   Recovery   →  60 → 20 VUs en 30s (vuelta gradual)
 *   Sustain    →  20 VUs durante 2 min (verifica recuperación)
 *   Ramp-down  →  20 → 0 en 30s
 *
 * Uso:
 *   k6 run tests/k6/scenarios/stress.js
 *
 * Nota: Este test NO llama al endpoint real de `generate` (LLM).
 *       Target principal: endpoints de lectura + preview (genera prompt sin LLM).
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate, Trend } from 'k6/metrics';
import { authParams, BASE_URL, login } from '../utils/auth.js';

// ── Custom metrics ─────────────────────────────────────────────────────────
const errorRate = new Rate('error_rate');
const p99Duration = new Trend('p99_custom', true);

// ── Stress profile ─────────────────────────────────────────────────────────
export const options = {
    stages: [
        { duration: '1m',  target: 30 },  // ramp-up
        { duration: '3m',  target: 30 },  // steady high load
        { duration: '30s', target: 60 },  // spike
        { duration: '30s', target: 20 },  // recovery
        { duration: '2m',  target: 20 },  // verify recovery
        { duration: '30s', target: 0 },   // ramp-down
    ],

    thresholds: {
        // Durante stress permitimos un poco más de latencia
        http_req_duration: ['p(95)<4000', 'p(99)<8000'],
        // Errores: máximo 5% bajo stress
        error_rate:        ['rate<0.05'],
        // Requests fallidos del propio k6
        http_req_failed:   ['rate<0.05'],
    },
};

export function setup() {
    return login();
}

export default function (auth) {
    const params = authParams(auth);
    let ok = true;

    // ── Grupo 1: endpoints de lectura (más agresivos) ─────────────────────
    group('Read endpoints', () => {
        const start = Date.now();

        const responses = http.batch([
            ['GET', `${BASE_URL}/api/dashboard/metrics`, null, params],
            ['GET', `${BASE_URL}/api/qa/llm-evaluations/metrics/summary`, null, params],
            ['GET', `${BASE_URL}/api/qa/llm-evaluations?per_page=5`, null, params],
        ]);

        p99Duration.add(Date.now() - start);

        const allOk = responses.every((r) =>
            check(r, {
                'status 200': (res) => res.status === 200,
                'no server error': (res) => res.status < 500,
            }),
        );
        if (!allOk) { ok = false; }
    });

    sleep(0.5 + Math.random());

    // ── Grupo 2: preview (50% de los VUs) — más costoso ──────────────────
    // Solo la mitad de los VUs hace preview para simular uso mixto
    if (__VU % 2 === 0) {
        group('Scenario Preview', () => {
            const payload = JSON.stringify({
                company_name: `Stress Test Co ${__VU}`,
                industry: 'technology',
                company_size: 'large',
                current_headcount: 500,
                planning_horizon: '24_months',
                strategic_objectives: ['Escalabilidad', 'Automatización'],
                challenges: ['Talento disponible', 'Presupuesto'],
            });

            const r = http.post(
                `${BASE_URL}/api/strategic-planning/scenarios/generate/preview`,
                payload,
                params,
            );

            const previewOk = check(r, {
                'preview no 5xx': (res) => res.status < 500,
                'preview responds': (res) => res.body !== null,
            });
            if (!previewOk) { ok = false; }
        });

        sleep(1 + Math.random() * 2);
    }

    errorRate.add(!ok);
}

/**
 * Resumen de resultados al finalizar el test.
 * k6 llama a handleSummary() si se define.
 */
export function handleSummary(data) {
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const p99 = data.metrics.http_req_duration?.values?.['p(99)'] ?? 0;
    const errors = data.metrics.error_rate?.values?.rate ?? 0;
    const totalReqs = data.metrics.http_reqs?.values?.count ?? 0;
    const rps = data.metrics.http_reqs?.values?.rate ?? 0;

    const summary = {
        stress_test_summary: {
            total_requests: totalReqs,
            requests_per_second: Math.round(rps * 100) / 100,
            p95_ms: Math.round(p95),
            p99_ms: Math.round(p99),
            error_rate_pct: Math.round(errors * 10000) / 100,
            passed: errors < 0.05 && p95 < 4000,
        },
    };

    return {
        'tests/k6/results/stress_summary.json': JSON.stringify(summary, null, 2),
        stdout: `\n📊 Stress Test Summary\n  Req total : ${totalReqs}\n  RPS       : ${summary.stress_test_summary.requests_per_second}\n  p(95)     : ${summary.stress_test_summary.p95_ms}ms\n  p(99)     : ${summary.stress_test_summary.p99_ms}ms\n  Error rate: ${summary.stress_test_summary.error_rate_pct}%\n  ${summary.stress_test_summary.passed ? '✅ PASSED' : '❌ FAILED'}\n`,
    };
}
