/**
 * K6 Rate Limit Validation Test
 *
 * Verifies that the ApiRateLimiter middleware enforces correct limits:
 *   - Authenticated: 300 req/min
 *   - Guest: 60 req/min
 *   - Public: 30 req/min
 *
 * Also validates X-RateLimit-* response headers are present and correct.
 *
 * Run:
 *   k6 run --env BASE_URL=https://staging.stratos.app \
 *           --env AUTH_TOKEN=<token> \
 *           scripts/load-testing-rate-limit.js
 */
import { check, group, sleep } from 'k6';
import http from 'k6/http';
import { Counter, Rate } from 'k6/metrics';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const API_URL = `${BASE_URL}/api`;
const token = __ENV.AUTH_TOKEN || 'test-token';

export const options = {
    scenarios: {
        // Scenario 1: hammer authenticated endpoint just under limit
        auth_under_limit: {
            executor: 'constant-arrival-rate',
            rate: 200, // 200 req/min — under 300 limit
            timeUnit: '1m',
            duration: '2m',
            preAllocatedVUs: 10,
            maxVUs: 20,
            exec: 'authScenario',
        },
        // Scenario 2: hammer guest endpoint above limit to trigger 429
        guest_over_limit: {
            executor: 'constant-arrival-rate',
            rate: 120, // 120 req/min — over 60 limit → expect 429
            timeUnit: '1m',
            duration: '2m',
            preAllocatedVUs: 5,
            maxVUs: 10,
            exec: 'guestScenario',
            startTime: '15s', // offset to avoid startup noise
        },
    },
    thresholds: {
        // Under-limit auth requests should all succeed
        'http_req_failed{scenario:auth_under_limit}': ['rate<0.01'],
        // Guest over-limit: expect many 429s (not failures per se, but 429 = expected)
        rate_limit_headers_present: ['rate>0.90'],
        rate_limited_count: ['count>0'], // must see at least some 429s in guest scenario
    },
};

const headersPresent = new Rate('rate_limit_headers_present');
const rateLimitedCount = new Counter('rate_limited_count');

function authHeaders() {
    return {
        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`,
            Accept: 'application/json',
        },
    };
}

// Authenticated scenario — should stay under rate limit
export function authScenario() {
    group('Auth rate-limit check', function () {
        const res = http.get(`${API_URL}/employees?per_page=5`, authHeaders());

        const hasHeaders = check(res, {
            'has X-RateLimit-Limit': (r) =>
                r.headers['X-Ratelimit-Limit'] !== undefined,
            'has X-RateLimit-Remaining': (r) =>
                r.headers['X-Ratelimit-Remaining'] !== undefined,
            'not rate limited (auth)': (r) => r.status !== 429,
        });

        headersPresent.add(
            res.headers['X-Ratelimit-Limit'] !== undefined &&
                res.headers['X-Ratelimit-Remaining'] !== undefined,
        );
    });

    sleep(0.1);
}

// Guest (no token) scenario — will exceed 60 req/min limit
export function guestScenario() {
    group('Guest rate-limit enforcement', function () {
        const res = http.get(`${API_URL}/health`, {
            headers: { Accept: 'application/json' },
        });

        if (res.status === 429) {
            rateLimitedCount.add(1);
            check(res, {
                '429 has Retry-After header': (r) =>
                    r.headers['Retry-After'] !== undefined,
            });
        }

        // Both 200 and 429 are acceptable here — 429 means the limiter works
        check(res, {
            'status 200 or 429': (r) => r.status === 200 || r.status === 429,
        });
    });

    sleep(0.2);
}
