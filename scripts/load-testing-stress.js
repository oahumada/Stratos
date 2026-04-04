import { check, group, sleep } from 'k6';
import http from 'k6/http';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const API_URL = `${BASE_URL}/api`;

// Stress Testing Configuration: Escalada gradual hasta breaking point
export const options = {
    stages: [
        { duration: '2m', target: 10 }, // Ramp-up 10 VUs
        { duration: '2m', target: 50 }, // Ramp-up 50 VUs
        { duration: '2m', target: 100 }, // Ramp-up 100 VUs
        { duration: '2m', target: 200 }, // Ramp-up 200 VUs (buscar problema)
        { duration: '2m', target: 500 }, // Ramp-up 500 VUs (hasta breaking point)
        { duration: '1m', target: 0 }, // Ramp-down
    ],
    thresholds: {
        http_req_duration: ['p(95)<1000', 'p(99)<2000'], // Relax for stress
        http_req_failed: ['rate<0.5'], // Allow failures under stress
    },
};

// Spike Testing Configuration: Picos súbitos de tráfico
export const optionsSpike = {
    stages: [
        { duration: '2m', target: 10 }, // Normal operation
        { duration: '30s', target: 500 }, // Spike súbito
        { duration: '2m', target: 500 }, // Mantener spike
        { duration: '1m', target: 0 }, // Recovery
    ],
    thresholds: {
        http_req_duration: ['p(95)<2000'], // Expect degradation
        http_req_failed: ['rate<0.3'],
    },
};

// Scenarios under stress
export function stressTest() {
    group('Stress Test - API Endpoints', () => {
        // Heavy endpoint: Catalogs with filters
        let response = http.get(
            `${API_URL}/catalogs?page=1&limit=100&filter=active`,
        );
        check(response, {
            'catalog endpoint responding': (r) =>
                r.status === 200 || r.status === 401,
            'acceptable latency': (r) => r.timings.duration < 3000,
        });

        // Another heavy endpoint: Scenarios
        response = http.get(
            `${API_URL}/strategic-planning/scenarios?include=roles,users`,
        );
        check(response, {
            'scenarios endpoint responding': (r) =>
                r.status === 200 || r.status === 401,
            'no timeout': (r) => r.status !== 504,
        });

        // Multi-read pattern
        response = http.get(`${API_URL}/notification-preferences`);
        check(response, {
            'notification prefs responding': (r) =>
                r.status === 200 || r.status === 401,
        });

        sleep(1);
    });
}

// Spike test scenario
export function spikeTest() {
    group('Spike Test - Sudden Traffic Increase', () => {
        let response = http.get(`${API_URL}/catalogs`);

        check(response, {
            'spike handling': (r) =>
                r.status === 200 || r.status === 401 || r.status === 429,
            'no server error': (r) => r.status < 500,
            'acceptable latency under spike': (r) => r.timings.duration < 5000,
        });

        sleep(0.5); // Less sleep under spike
    });
}

// Rate limit verification under stress
export function rateLimitStressTest() {
    group('Rate Limit Boundary Under Stress', () => {
        for (let i = 0; i < 20; i++) {
            let response = http.get(`${API_URL}/catalogs`);

            check(response, {
                'rate limit header present': (r) =>
                    r.headers['X-RateLimit-Limit'] !== undefined ||
                    r.status === 401,
                'no premature 429': (r) => (i < 300 ? r.status !== 429 : true), // Auth limit is 300/min
            });
        }

        sleep(1);
    });
}

// Error recovery test
export function errorRecoveryTest() {
    group('Error Recovery Under Load', () => {
        let response = http.get(`${API_URL}/catalogs`);

        if (response.status === 500) {
            // Retry logic
            sleep(2);
            response = http.get(`${API_URL}/catalogs`);
        }

        check(response, {
            'eventual success': (r) => r.status === 200 || r.status === 401,
            'recovery time': (r) => r.timings.duration < 5000,
        });

        sleep(1);
    });
}

// Resource exhaustion detection
export function resourceExhaustionTest() {
    group('Resource Exhaustion Detection', () => {
        let responses = [];

        // Rapid-fire requests to find limits
        for (let i = 0; i < 10; i++) {
            let response = http.get(`${API_URL}/catalogs`);
            responses.push(response.status);
        }

        let successCount = responses.filter(
            (s) => s === 200 || s === 401,
        ).length;
        let failureCount = responses.filter((s) => s >= 500).length;

        check({
            'most requests successful': () => successCount >= 7,
            'not all requests failing': () => failureCount < 5,
        });

        sleep(2);
    });
}

// Main export - select test mode via env variable
export default function () {
    const testMode = __ENV.TEST_MODE || 'stress';

    switch (testMode) {
        case 'stress':
            stressTest();
            break;
        case 'spike':
            spikeTest();
            break;
        case 'ratelimit':
            rateLimitStressTest();
            break;
        case 'recovery':
            errorRecoveryTest();
            break;
        case 'resource':
            resourceExhaustionTest();
            break;
        default:
            stressTest();
    }
}
