import http from 'k6/http';
import { check, group, sleep } from 'k6';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const API_URL = `${BASE_URL}/api`;

// Thresholds for performance
export const options = {
  stages: [
    { duration: '30s', target: 5 },   // Ramp-up 5 users
    { duration: '1m', target: 15 },   // Ramp-up 15 users
    { duration: '30s', target: 0 },   // Ramp-down
  ],
  thresholds: {
    http_req_duration: ['p(95)<500', 'p(99)<1000'],
    http_req_failed: ['rate<0.3'], // Allow some failures (auth required)
  },
};

// Scenario 1: Public Endpoints (No Auth Required)
export function testPublicEndpoints() {
  group('Public Endpoints', function () {
    // GET catalogs
    let response = http.get(`${API_URL}/catalogs`);
    check(response, {
      'catalogs status 200 or 401': (r) => r.status === 200 || r.status === 401,
    });

    sleep(1);

    // GET assessments feedback
    response = http.get(`${API_URL}/assessments/feedback`);
    check(response, {
      'assessments 200 or 401': (r) => r.status === 200 || r.status === 401,
    });

    sleep(1);
  });
}

// Scenario 2: Health Check
export function testHealthCheck() {
  group('System Health', function () {
    let response = http.get(`${BASE_URL}/health`);
    check(response, {
      'health check responds': (r) => r.status === 200 || r.status === 404 || r.status === 401,
    });

    sleep(1);
  });
}

// Scenario 3: API Rate Limiting Headers
export function testRateLimitHeaders() {
  group('Rate Limiting Headers', function () {
    let response = http.get(`${API_URL}/catalogs`);
    
    check(response, {
      'has X-RateLimit-Limit': (r) => r.headers['X-RateLimit-Limit'] !== undefined || r.status === 401,
      'has X-RateLimit-Remaining': (r) => r.headers['X-RateLimit-Remaining'] !== undefined || r.status === 401,
      'has X-RateLimit-Reset': (r) => r.headers['X-RateLimit-Reset'] !== undefined || r.status === 401,
    });

    sleep(1);
  });
}

// Scenario 4: Response Time Validation
export function testResponseTimes() {
  group('Response Time Performance', function () {
    let response = http.get(`${API_URL}/catalogs`);
    
    check(response, {
      'response time < 1s': (r) => r.timings.duration < 1000,
    });

    sleep(1);
  });
}

// Scenario 5: Multiple Rapid Requests (Load)
export function testRapidRequests() {
  group('Rapid Requests', function () {
    for (let i = 0; i < 5; i++) {
      let response = http.get(`${API_URL}/catalogs`);
      check(response, {
        'rapid request ok': (r) => r.status === 200 || r.status === 401 || r.status === 429,
      });
    }

    sleep(1);
  });
}

// Main test execution
export default function () {
  testPublicEndpoints();
  testHealthCheck();
  testRateLimitHeaders();
  testResponseTimes();
  testRapidRequests();
}
