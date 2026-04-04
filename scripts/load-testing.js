import http from 'k6/http';
import { check, group, sleep } from 'k6';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';
const API_URL = `${BASE_URL}/api`;

// Thresholds for performance
export const options = {
  stages: [
    { duration: '30s', target: 5 },   // Ramp-up 5 users
    { duration: '1m30s', target: 25 }, // Ramp-up 25 users
    { duration: '1m', target: 25 },    // Stay at 25 users
    { duration: '30s', target: 0 },    // Ramp-down
  ],
  thresholds: {
    http_req_duration: ['p(95)<500', 'p(99)<1000'],
    http_req_failed: ['rate<0.1'],
  },
};

// Auth token - normally would be obtained via login
const token = __ENV.AUTH_TOKEN || 'test-token';

function authHeaders() {
  return {
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`,
      Accept: 'application/json',
    },
  };
}

// Scenario 1: Auth Flow
export function testAuthFlow() {
  group('Auth Flow', function () {
    // Login endpoint
    let response = http.post(
      `${API_URL}/login`,
      JSON.stringify({
        email: 'test@example.com',
        password: 'password',
      }),
      {
        headers: {
          'Content-Type': 'application/json',
        },
      }
    );

    check(response, {
      'login status is 200': (r) => r.status === 200,
      'has token': (r) => r.body.includes('token'),
    });

    sleep(1);
  });
}

// Scenario 2: Catalog Browsing
export function testCatalogBrowsing() {
  group('Catalog Browsing', function () {
    // Get catalogs
    let response = http.get(`${API_URL}/catalogs`, authHeaders());

    check(response, {
      'catalogs status is 200': (r) => r.status === 200,
      'has data': (r) => r.body.includes('data'),
    });

    sleep(1);

    // Search catalogs
    response = http.get(`${API_URL}/catalogs/search?q=test`, authHeaders());

    check(response, {
      'search status is 200': (r) => r.status === 200,
    });

    sleep(1);

    // Pagination
    response = http.get(`${API_URL}/catalogs?page=1&limit=10`, authHeaders());

    check(response, {
      'pagination status is 200': (r) => r.status === 200,
    });

    sleep(1);
  });
}

// Scenario 3: Approval Workflow
export function testApprovalWorkflow() {
  group('Approval Workflow', function () {
    // Get approval requests
    let response = http.get(
      `${API_URL}/approval-requests?status=pending`,
      authHeaders()
    );

    check(response, {
      'approvals status is 200': (r) => r.status === 200,
    });

    sleep(1);

    // Create approval request
    response = http.post(
      `${API_URL}/approval-requests`,
      JSON.stringify({
        title: 'Test Request',
        description: 'Load test approval',
      }),
      authHeaders()
    );

    check(response, {
      'create approval status is 201': (r) => r.status === 201,
    });

    sleep(1);
  });
}

// Scenario 4: Messaging & Notifications
export function testMessagingNotifications() {
  group('Messaging & Notifications', function () {
    // Get notification preferences
    let response = http.get(
      `${API_URL}/notification-preferences`,
      authHeaders()
    );

    check(response, {
      'notification prefs status is 200': (r) => r.status === 200,
    });

    sleep(1);

    // Add notification channel
    response = http.post(
      `${API_URL}/notification-preferences`,
      JSON.stringify({
        channel_type: 'email',
        is_active: true,
        channel_config: {
          email: 'user@example.com',
        },
      }),
      authHeaders()
    );

    check(response, {
      'add channel status is 201': (r) => r.status === 201 || r.status === 200,
    });

    sleep(1);

    // Get org-level notification settings
    response = http.get(
      `${API_URL}/notification-channel-settings`,
      authHeaders()
    );

    check(response, {
      'org settings status is 200': (r) => r.status === 200,
    });

    sleep(1);
  });
}

// Scenario 5: Workforce Planning
export function testWorkforcePlanning() {
  group('Workforce Planning', function () {
    // Get scenarios
    let response = http.get(
      `${API_URL}/strategic-planning/scenarios`,
      authHeaders()
    );

    check(response, {
      'scenarios status is 200': (r) => r.status === 200,
    });

    sleep(1);

    // Get executive summary
    // Note: This would need a real scenario ID
    response = http.get(
      `${API_URL}/strategic-planning/scenarios/1/executive-summary`,
      authHeaders()
    );

    check(response, {
      'summary status is 200 or 404': (r) => r.status === 200 || r.status === 404,
    });

    sleep(1);

    // Get org chart
    response = http.get(
      `${API_URL}/strategic-planning/scenarios/1/org-chart`,
      authHeaders()
    );

    check(response, {
      'org chart status is 200 or 404': (r) => r.status === 200 || r.status === 404,
    });

    sleep(1);
  });
}

// Main test execution
export default function () {
  const scenario = __ENV.SCENARIO || 'all';

  if (scenario === 'all' || scenario === 'auth') {
    testAuthFlow();
  }

  if (scenario === 'all' || scenario === 'catalog') {
    testCatalogBrowsing();
  }

  if (scenario === 'all' || scenario === 'approval') {
    testApprovalWorkflow();
  }

  if (scenario === 'all' || scenario === 'messaging') {
    testMessagingNotifications();
  }

  if (scenario === 'all' || scenario === 'workforce') {
    testWorkforcePlanning();
  }

  sleep(1);
}
