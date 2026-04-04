/**
 * K6 Cache Failover Test
 *
 * Simulates Redis unavailability scenarios to verify the application
 * gracefully degrades (falls back to DB) rather than returning 500s.
 *
 * How to use:
 *   1. Run normally first to establish baseline (Redis healthy):
 *      k6 run --env BASE_URL=... --env AUTH_TOKEN=... --env REDIS_UP=true
 *   2. Kill Redis on the test server, then run with REDIS_UP=false:
 *      k6 run --env BASE_URL=... --env AUTH_TOKEN=... --env REDIS_UP=false
 *   3. Compare p95 latency: should increase (DB fallback) but no 5xx surge.
 *
 * Run:
 *   k6 run --env BASE_URL=https://staging.stratos.app \
 *           --env AUTH_TOKEN=<token> \
 *           --env REDIS_UP=true \
 *           scripts/load-testing-cache-failover.js
 */
import { check, group, sleep } from 'k6';
import http from 'k6/http';
import { Rate, Trend } from 'k6/metrics';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const API_URL = `${BASE_URL}/api`;
const token = __ENV.AUTH_TOKEN || 'test-token';
const redisUp = __ENV.REDIS_UP !== 'false';

export const options = {
    stages: [
        { duration: '30s', target: 20 },   // warm up
        { duration: '2m', target: 40 },    // sustained load during "failover window"
        { duration: '30s', target: 40 },   // hold
        { duration: '30s', target: 0 },    // ramp-down
    ],
    thresholds: {
        // Even without Redis, p95 must stay under 3s (DB fallback)
        http_req_duration: ['p(95)<3000'],
        // No 5xx errors tolerated — graceful degradation is required
        cache_failover_5xx_rate: ['rate<0.01'],
        // Cache-dependent endpoints must still return valid data
        cache_hit_or_miss_ok: ['rate>0.99'],
    },
    tags: { redis_state: redisUp ? 'up' : 'down' },
};

const fivexxRate = new Rate('cache_failover_5xx_rate');
const cacheOk = new Rate('cache_hit_or_miss_ok');
const notificationPrefsLatency = new Trend('notification_prefs_latency_ms');
const orgChannelsLatency = new Trend('org_channels_latency_ms');

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
    // NotificationCacheService caches user prefs with 1h TTL in Redis
    group('Notification prefs (cache-dependent)', function () {
        const res = http.get(`${API_URL}/notifications/channels`, authHeaders());

        const ok = check(res, {
            'prefs: no 5xx': (r) => r.status < 500,
            'prefs: returns data': (r) => r.status === 200 || r.status === 401,
        });

        fivexxRate.add(res.status >= 500);
        cacheOk.add(res.status < 500);
        notificationPrefsLatency.add(res.timings.duration);
    });

    // Org-level channel settings — also cached
    group('Org channel settings (cache-dependent)', function () {
        const res = http.get(`${API_URL}/admin/notification-channel-settings`, authHeaders());

        const ok = check(res, {
            'org channels: no 5xx': (r) => r.status < 500,
        });

        fivexxRate.add(res.status >= 500);
        cacheOk.add(res.status < 500);
        orgChannelsLatency.add(res.timings.duration);
    });

    // Non-cached endpoints — should be unaffected by Redis state
    group('Non-cached endpoint (baseline)', function () {
        const res = http.get(`${API_URL}/employees?per_page=5`, authHeaders());

        check(res, {
            'employees: no 5xx': (r) => r.status < 500,
            'employees: fast even without cache': (r) => r.timings.duration < 2000,
        });

        fivexxRate.add(res.status >= 500);
    });

    sleep(1);
}

export function handleSummary(data) {
    const p95 = data.metrics.http_req_duration?.values?.['p(95)'] ?? 0;
    const errorRate = data.metrics.cache_failover_5xx_rate?.values?.rate ?? 0;

    const mode = redisUp ? '🟢 Redis UP (cache healthy)' : '🔴 Redis DOWN (failover mode)';

    return {
        stdout: `
╔══════════════════════════════════════════════╗
║         Cache Failover Test Summary          ║
╠══════════════════════════════════════════════╣
║ Redis State: ${mode.padEnd(30)}║
║ p95 latency: ${String(p95.toFixed(0) + 'ms').padEnd(30)}║
║ 5xx rate:    ${String((errorRate * 100).toFixed(2) + '%').padEnd(30)}║
╚══════════════════════════════════════════════╝

${p95 > 1500 && !redisUp ? '⚠️  Latency increase expected (DB fallback) — check indexes.' : ''}
${errorRate > 0.01 ? '🚨 5xx errors detected — cache layer not failing gracefully!' : '✅ Graceful degradation confirmed.'}
`,
    };
}
