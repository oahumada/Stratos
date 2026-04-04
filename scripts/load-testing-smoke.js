/**
 * K6 Smoke Test — Production Health Check
 *
 * Minimal test to verify the application is alive after a deployment.
 * Runs with very few VUs for a short time, strict thresholds.
 *
 * Run after every deploy:
 *   k6 run --env BASE_URL=https://app.stratos.com \
 *           --env AUTH_TOKEN=<prod-readonly-token> \
 *           scripts/load-testing-smoke.js
 */
import { check, group, sleep } from 'k6';
import http from 'k6/http';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const API_URL = `${BASE_URL}/api`;
const token = __ENV.AUTH_TOKEN || 'test-token';

export const options = {
    vus: 3,
    duration: '1m',
    thresholds: {
        // Smoke test: very strict — must be healthy immediately after deploy
        http_req_duration: ['p(95)<500', 'p(99)<800'],
        http_req_failed: ['rate<0.01'],
        checks: ['rate>0.99'],
    },
};

function authHeaders() {
    return {
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
        },
    };
}

export default function () {
    // 1. Health endpoint (no auth required)
    group('Health check', function () {
        const res = http.get(`${BASE_URL}/up`);
        check(res, {
            'health: 200': (r) => r.status === 200,
            'health: fast': (r) => r.timings.duration < 200,
        });
    });

    // 2. Auth works
    group('Auth verification', function () {
        const res = http.get(`${API_URL}/user`, authHeaders());
        check(res, {
            'auth: 200': (r) => r.status === 200,
            'auth: has id': (r) => {
                try { return JSON.parse(r.body).id !== undefined; } catch { return false; }
            },
        });
    });

    // 3. Core read endpoint
    group('Core API read', function () {
        const res = http.get(`${API_URL}/employees?per_page=1`, authHeaders());
        check(res, {
            'employees: 200': (r) => r.status === 200,
            'employees: <500ms': (r) => r.timings.duration < 500,
        });
    });

    // 4. Notification channels (cache layer)
    group('Cache layer (notifications)', function () {
        const res = http.get(`${API_URL}/notifications/channels`, authHeaders());
        check(res, {
            'notifications: no 5xx': (r) => r.status < 500,
        });
    });

    // 5. WFP plans (critical module)
    group('WFP module', function () {
        const res = http.get(`${API_URL}/workforce-planning/plans`, authHeaders());
        check(res, {
            'wfp: no 5xx': (r) => r.status < 500,
            'wfp: <600ms': (r) => r.timings.duration < 600,
        });
    });

    sleep(2);
}

export function handleSummary(data) {
    const failed = data.metrics.http_req_failed?.values?.rate ?? 0;
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const checks = data.metrics.checks?.values?.rate ?? 0;

    const pass = failed < 0.01 && p95 < 500 && checks > 0.99;

    return {
        stdout: `
╔══════════════════════════════════════╗
║        SMOKE TEST RESULT             ║
╠══════════════════════════════════════╣
║ ${pass ? '✅ PASS — deploy healthy' : '🚨 FAIL — investigate before traffic'}  
║ p95 latency : ${String(p95.toFixed(0) + 'ms').padEnd(22)}║
║ Error rate  : ${String((failed * 100).toFixed(2) + '%').padEnd(22)}║
║ Check pass  : ${String((checks * 100).toFixed(1) + '%').padEnd(22)}║
╚══════════════════════════════════════╝
`,
    };
}
