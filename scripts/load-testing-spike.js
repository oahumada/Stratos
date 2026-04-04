/**
 * K6 Spike Test — sudden 10x load surge
 *
 * Simulates a flash event (viral share, campaign launch) that sends
 * 10× normal traffic in seconds, then drops back to baseline.
 *
 * Run:
 *   k6 run --env BASE_URL=https://staging.stratos.app \
 *           --env AUTH_TOKEN=<token> \
 *           scripts/load-testing-spike.js
 */
import { check, group, sleep } from 'k6';
import http from 'k6/http';
import { Rate, Trend } from 'k6/metrics';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const API_URL = `${BASE_URL}/api`;
const token = __ENV.AUTH_TOKEN || 'test-token';

export const options = {
    stages: [
        { duration: '30s', target: 10 }, // baseline — normal traffic
        { duration: '10s', target: 100 }, // spike: 10x surge in 10 seconds
        { duration: '1m', target: 100 }, // sustain spike for 1 minute
        { duration: '10s', target: 10 }, // drop back to baseline
        { duration: '30s', target: 10 }, // verify recovery at baseline
        { duration: '10s', target: 0 }, // ramp-down
    ],
    thresholds: {
        // During spike, allow degradation but no total meltdown
        http_req_duration: ['p(95)<2000', 'p(99)<5000'],
        http_req_failed: ['rate<0.20'],
        spike_error_rate: ['rate<0.20'],
    },
};

const spikeErrors = new Rate('spike_error_rate');
const spikeLatency = new Trend('spike_latency_ms');

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
    group('Spike — Dashboard read', function () {
        const res = http.get(`${API_URL}/dashboard/metrics`, authHeaders());
        const ok = check(res, {
            'status 2xx': (r) => r.status >= 200 && r.status < 300,
        });
        spikeErrors.add(!ok);
        spikeLatency.add(res.timings.duration);
    });

    group('Spike — Employees list', function () {
        const res = http.get(`${API_URL}/employees?per_page=10`, authHeaders());
        check(res, {
            'employees status 2xx': (r) => r.status >= 200 && r.status < 300,
        });
    });

    group('Spike — Auth ping', function () {
        const res = http.get(`${API_URL}/user`, authHeaders());
        check(res, {
            'auth ok': (r) => r.status === 200,
            'response fast': (r) => r.timings.duration < 3000,
        });
    });

    sleep(0.5);
}
