# Verification Hub: Advanced Dashboards (Phase 7)

> **Status**: ✅ Complete | **Version**: 1.0 | **Last Updated**: 2025-01-27

## 📊 Overview

The Advanced Dashboards suite provides comprehensive real-time analytics and insights for the Verification Hub system. Built with Vue 3, TypeScript, and VueApexCharts, these 6 specialized dashboards enable administrators to monitor, analyze, and optimize system performance across different dimensions.

### Dashboard Stack

| Dashboard | Purpose | Key Metrics | Refresh Rate | Users |
|-----------|---------|-------------|--------------|-------|
| **Executive** | C-Level overview | KPIs, transitions, health | 30s | Admins |
| **Operational** | System operations | Status, alerts, events | 15s | Operators |
| **Compliance** | Audit & compliance | Scores, history, trends | 60s | Auditors |
| **Performance** | Latency & throughput | P50/P95/P99, capacity | 60s | DevOps |
| **Insights** | AI analysis | Recommendations, forecasts | 60s | Analysts |
| **Real-time Monitor** | Live event stream | Events, transactions, logs | 5-10s | Operators |

---

## 🏗️ Architecture

### Composable-Based State Management

All dashboards share a single composable: `useVerificationDashboard()` located at:
```
resources/js/composables/useVerificationDashboard.ts
```

**Features:**
- Centralized metrics state
- Auto-polling with configurable intervals (15-60s)
- Real-time event subscription
- Multi-tenant scoped (org_id)
- Type-safe TypeScript interfaces
- Error handling & loading states
- Export functionality (JSON, CSV, PDF)

**Methods:**
```typescript
// Fetching
fetchMetrics(): Promise<void>
fetchComplianceData(): Promise<void>
fetchMetricsHistory(hours: number): Promise<void>
subscribeToRealtimeEvents(): () => void

// Actions
startPolling(intervalMs: number): () => void
getBlockers(): string[]
exportMetrics(format: 'json' | 'csv' | 'pdf'): Promise<void>

// Computed
systemStatus: ComputedRef<'healthy' | 'warning' | 'critical'>
transitionReadiness: ComputedRef<ReadinessStatus>
complianceTrend: ComputedRef<'up' | 'down' | 'stable'>
recentAlerts: ComputedRef<RealtimeEvent[]>
```

### API Layer

Located at:
```
app/Http/Controllers/Deployment/VerificationDashboardController.php
```

**Endpoints:**

```
GET /api/deployment/verification/metrics
GET /api/deployment/verification/compliance-metrics
GET /api/deployment/verification/metrics-history?window={24|48|72}
GET /api/deployment/verification/realtime-events?since={iso8601}
GET /api/deployment/verification/export-metrics?format={json|csv}
```

**Response Format (Metrics):**
```json
{
  "currentPhase": "tuning",
  "confidenceScore": 92,
  "errorRate": 28,
  "retryRate": 16,
  "sampleSize": 540,
  "transitionReadiness": 94,
  "lastUpdated": "2025-01-27T14:32:00Z",
  "timestamp": "2025-01-27T14:32:15Z"
}
```

---

## 📈 Dashboard Details

### 1️⃣ Executive Dashboard
**Path**: `/deployment/verification/dashboard/executive`

**Components:**
- 4 KPI Cards
  - Current Phase
  - Confidence Score (%)
  - Error Rate (%)
  - Compliance Score (%)
- 7-day Transitions Chart (ApexCharts Area)
- System Health Progress Bars
- Recent Transitions List (last 5)
- Quick Stats Section
- Export to PDF Button

**Use Cases:**
- Executive briefings
- Board-level reporting
- Phase transition planning
- System health at-a-glance

**Data Refresh**: 30 seconds

### 2️⃣ Operational Dashboard
**Path**: `/deployment/verification/dashboard/operational`

**Components:**
- Status Indicator Grid
  - Phase indicator
  - Confidence gauge
  - Error rate alert
- Recent Alerts Section
  - Live pulse indicator
  - Severity color coding
  - Last 10 alerts
- Live Events Stream
  - Real-time transaction log
  - Type filtering (transitions, alerts, config, notifications)
  - 100-event buffer
- Transition Readiness
  - Blockers list
  - Recommendation cards
  - Action buttons

**Use Cases:**
- Live system monitoring
- Incident response
- Issue investigation
- Real-time alerting

**Data Refresh**: 15 seconds (most frequent)

### 3️⃣ Compliance Dashboard
**Path**: `/deployment/verification/dashboard/compliance`

**Components:**
- Compliance Score Card (large)
  - Current score (e.g., 94%)
  - Trend indicator (↑/↓/→)
  - Change percentage
- Passed Tests Indicator
  - 12/12 passed (example)
  - Percentage badge
- 6-Month Trend Line Chart (ApexCharts)
  - Historical compliance scores
  - Smooth curves
  - Gradient fill
- Audit History List
  - 3 most recent audits
  - Status badge (passed/failed/pending)
  - Date & score
  - Export Report Button

**Use Cases:**
- Audit compliance reporting
- Regulatory reviews
- Trend analysis
- Historical tracking

**Data Refresh**: 60 seconds

### 4️⃣ Performance Dashboard
**Path**: `/deployment/verification/dashboard/performance`

**Components:**
- Latency KPIs (4 cards)
  - Average Latency (ms)
  - P50 Latency
  - P95 Latency (yellow warning at >500ms)
  - P99 Latency (red alert at >1000ms)
- Throughput KPIs (2 cards)
  - Current Throughput (req/s)
  - Average Throughput (24h)
- Latency Trend Chart (24h)
  - Multi-line chart (avg, p50, p95, p99)
  - ApexCharts Line
  - Dark theme
- Request Distribution Donut Chart
  - Successful (60%)
  - Retry (25%)
  - Error (10%)
  - Timeout (5%)
- Throughput Trend Chart (24h)
  - ApexCharts Area
  - Gradient fill
- Performance Summary
  - Status badges (Optimal/Acceptable/Degraded)
  - Health metrics

**Use Cases:**
- Performance optimization
- Capacity planning
- SLA monitoring
- Resource allocation

**Data Refresh**: 60 seconds

### 5️⃣ Insights Dashboard
**Path**: `/deployment/verification/dashboard/insights`

**Components:**
- Key Insights (4 cards)
  - Recommendation: Optimize Retry Strategy
  - Anomaly: Unusual Error Spike
  - Prediction: Forecast Phase Transition
  - Optimization: Sample Size
- Metric Trends (4 cards)
  - Confidence Score (target: ≥90%)
  - Error Rate (target: ≤40%)
  - Retry Rate (target: ≤20%)
  - System Health (target: >90%)
  - Trend indicators (↑/↓ with % change)
- AI Recommendations
  - Dynamic list based on thresholds
  - Priority levels (critical, high, medium)
  - Color-coded severity
  - "Learn more" links
- System Health Score
  - Circular gauge (92/100)
  - Status badge (Excellent/Good/Fair/Poor)
  - Breakdown (Reliability, Performance, Compliance)
- Anomaly Timeline (24h)
  - Event markers
  - Severity indicators
  - Duration tracking
  - Cause analysis
- 24h Forecast
  - Predicted phase
  - Expected confidence
  - Error rate projection
  - System health forecast

**Use Cases:**
- Predictive analysis
- Anomaly detection
- Trend forecasting
- Decision support
- Capacity forecasting

**Data Refresh**: 60 seconds

### 6️⃣ Real-time Monitor
**Path**: `/deployment/verification/dashboard/realtime`

**Components:**
- Live Status Badge
  - Green pulse indicator
  - "LIVE" label
- Event Statistics (5 cards)
  - Total events
  - Transitions
  - Alerts
  - Config changes
  - Notifications
- Event Stream (scrollable)
  - Max height: calc(100vh - 400px)
  - Auto-scroll toggle
  - Event cards with:
    - Type icon (🔄🎯⚠️⚙️📢)
    - Event message
    - Event data (key-value pairs)
    - Timestamp (formatted + relative)
    - Severity color coding
- System Metrics Footer
  - Phase, Confidence, Error Rate, Sample Size

**Use Cases:**
- Live operations center
- Event debugging
- Real-time anomaly detection
- Incident tracking
- Performance monitoring

**Data Refresh**: 5-10 seconds (SSE ready for upgrades)

---

## 🔧 Implementation Details

### File Structure

```
resources/js/
├── composables/
│   └── useVerificationDashboard.ts      (200+ LOC, state mgmt)
├── Pages/
│   └── Verification/
│       ├── ExecutiveDashboard.vue       (420 LOC, KPIs)
│       ├── OperationalDashboard.vue     (340 LOC, real-time)
│       ├── ComplianceDashboard.vue      (320 LOC, audit)
│       ├── PerformanceDashboard.vue     (450 LOC, latency)
│       ├── InsightsDashboard.vue        (380 LOC, AI)
│       └── RealtimeMonitor.vue          (360 LOC, events)

app/Http/Controllers/Deployment/
└── VerificationDashboardController.php  (280+ LOC, API)

routes/
├── api.php                              (5 new endpoints)
└── web.php                              (6 new routes)
```

### Database Schema Requirements

The composable uses `VerificationAudit` model which requires:

```sql
-- Existing structure (verified)
Table: verification_audits
- id
- organization_id
- created_at / updated_at
- current_phase (string: silent|flagging|reject|tuning)
- confidence_score (int: 0-100)
- error_rate (int: 0-100)
- retry_rate (int: 0-100)
- sample_size (int)
- status (string)
- throughput (int, optional)
- latency (int in ms, optional)
```

### Configuration

**Polling Intervals** (configurable in composable):
```typescript
// Default intervals by dashboard
ExecutiveDashboard:     30000ms (30s)
OperationalDashboard:   15000ms (15s)
ComplianceDashboard:    60000ms (60s)
PerformanceDashboard:   60000ms (60s)
InsightsDashboard:      60000ms (60s)
RealtimeMonitor:        10000ms (10s)
```

**Thresholds** (soft limits that trigger warnings):
```json
{
  "confidence": { "target": 90, "min": 80 },
  "errorRate": { "target": 40, "max": 50 },
  "retryRate": { "target": 20, "max": 25 },
  "sampleSize": { "target": 100, "min": 50 },
  "latencyP95": { "target": 500, "warning": 750 },
  "latencyP99": { "target": 1000, "warning": 1500 },
  "throughput": { "min": 500 }
}
```

---

## 🔐 Security & Multi-tenancy

All endpoints are protected:
- ✅ Sanctum token authentication
- ✅ Admin role requirement (via middleware in routes/web.php)
- ✅ Multi-tenant scoping: `auth()->user()->organization_id`
- ✅ CSRF protection (automatic with Laravel)
- ✅ Authorization policies (enforced in controllers)

**Example Scoping** (in VerificationDashboardController):
```php
$organizationId = auth()->user()->organization_id;
$metrics = VerificationAudit::where('organization_id', $organizationId)
    ->latest('created_at')
    ->first();
```

---

## 📱 Responsive Design

All dashboards are fully responsive:

| Breakpoint | Grid Cols | Behavior |
|-----------|-----------|----------|
| Mobile (sm) | 1-2 cols | Stacked cards |
| Tablet (md) | 2-3 cols | 2-column grid |
| Desktop (lg) | 3-4 cols | Full grid |
| Wide (xl) | 4+ cols | Multi-column |

**TailwindCSS Classes Used:**
- `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 lg:grid-cols-4`
- `lg:col-span-2` (for spanning charts)
- `max-h-[calc(...)]` (for scrollable sections)

---

## 🎨 Styling & Theming

**Design System:**
- **Theme**: Dark mode (dark background with glass morphism)
- **Colors**: 
  - Primary: Indigo/Purple (500/600)
  - Success: Green (400/500)
  - Warning: Yellow (400/500)
  - Error: Red (400/500)
- **Backdrop**: `backdrop-blur-xl` (glass effect)
- **Borders**: `border-white/10` (subtle dividers)
- **Transparency**: `bg-white/5 to bg-white/20` (layered depth)

**Typography:**
- Headings: `text-white` with `font-bold`
- Labels: `text-white/70` (muted)
- Values: `text-white` or `text-{color}-400` (highlighted)

---

## 🚀 Usage Examples

### Loading a Dashboard in Vue

```tsx
// In a wrapper or navigation component
import { router } from '@inertiajs/vue3'

// Navigate to Executive Dashboard
router.visit('/deployment/verification/dashboard/executive')

// Or use direct links with Inertia
<Link href="/deployment/verification/dashboard/operational">
  Open Operational
</Link>
```

### Using the Composable Directly

```vue
<script setup lang="ts">
import { useVerificationDashboard } from '@/composables/useVerificationDashboard'

const { metrics, isLoading, fetchMetrics, startPolling } = useVerificationDashboard()

onMounted(() => {
  fetchMetrics()
  startPolling(30000) // 30-second refresh
})
</script>

<template>
  <div v-if="isLoading" class="spinner">Loading...</div>
  <div v-else class="metrics">
    <p>Phase: {{ metrics?.currentPhase }}</p>
    <p>Confidence: {{ metrics?.confidenceScore }}%</p>
  </div>
</template>
```

### Exporting Data

```typescript
const { exportMetrics } = useVerificationDashboard()

// Export as JSON
await exportMetrics('json')

// Export as CSV
await exportMetrics('csv')
```

---

## 📊 API Integration

### Metrics Endpoint Response

```json
{
  "currentPhase": "tuning",
  "confidenceScore": 92,
  "errorRate": 28,
  "retryRate": 16,
  "sampleSize": 540,
  "transitionReadiness": 94,
  "lastUpdated": "2025-01-27T14:32:00Z"
}
```

### Compliance Metrics Response

```json
{
  "complianceScore": 94,
  "passedTests": 12,
  "totalTests": 12,
  "trend": 3,
  "recentAudits": [
    {
      "date": "Jan 27, 14:32",
      "status": "passed",
      "score": 94,
      "errorRate": 28
    }
  ]
}
```

### Metrics History Response

```json
[
  {
    "timestamp": "2025-01-27T14:00:00Z",
    "confidenceScore": 88,
    "errorRate": 35,
    "retryRate": 20,
    "throughput": 890,
    "latency": 342
  }
]
```

### Real-time Events Response

```json
[
  {
    "id": "evt_123",
    "timestamp": "2025-01-27T14:32:15Z",
    "type": "transition",
    "message": "System transitioned to tuning phase",
    "severity": "info",
    "data": {
      "phase": "tuning",
      "status": "success"
    }
  }
]
```

---

## 🔄 Polling Strategy

**Current Implementation: Client-side polling**
```typescript
// In composable
const polling = setInterval(fetchMetrics, interval)
onUnmounted(() => clearInterval(polling))
```

**Future Enhancements:**
1. **WebSockets** (via Laravel Reverb): Replace polling with real-time subscriptions
2. **Server-Sent Events (SSE)**: For one-way server-to-client streams
3. **GraphQL Subscriptions**: More granular data subscriptions
4. **Connection pooling**: For high-frequency dashboards

---

## 🧪 Testing

### Unit Tests
```bash
# Test composable logic
php artisan test tests/Unit/Composables/...

# Test API responses
php artisan test tests/Unit/Controllers/...
```

### Feature Tests
```bash
# Test full dashboard data flow
php artisan test tests/Feature/Dashboards/...
```

### Browser Tests (E2E)
```bash
# Test user interactions on dashboards
php artisan test --browser tests/Browser/DashboardsTest.php
```

---

## 🛠️ Customization

### Adding a New Metric

1. **Update the composable** (`useVerificationDashboard.ts`):
   ```typescript
   interface DashboardMetrics {
     // ...existing...
     newMetric: number
   }
   ```

2. **Update the API** (`VerificationDashboardController.php`):
   ```php
   public function metrics(): JsonResponse {
     return response()->json([
       // ...existing...
       'newMetric' => $calculated_value,
     ]);
   }
   ```

3. **Update the dashboard components** that use the metric:
   ```vue
   <p>New Metric: {{ metrics.newMetric }}</p>
   ```

### Changing Refresh Intervals

Edit the composable's `startPolling()` calls in each dashboard:
```typescript
// In each dashboard's onMounted()
unsubscribe = startPolling(120000) // 120 seconds instead of 60
```

### Customizing Colors & Styling

Edit TailwindCSS classes in dashboard components:
```vue
<!-- Change from indigo to green -->
<div class="bg-green-500/20 text-green-300">
  {{ metrics.confidenceScore }}%
</div>
```

---

## 📋 Deployment Checklist

- [ ] API endpoints tested in Postman/REST client
- [ ] Database has `verification_audits` table with required columns
- [ ] Routes registered (`routes/api.php` and `routes/web.php`)
- [ ] Dashboard components placed in correct directory
- [ ] Composable imported correctly in all dashboards
- [ ] VueApexCharts installed and configured
- [ ] Authentication/authorization tested (admin-only access)
- [ ] Multi-tenant scoping verified (org_id filter)
- [ ] Responsive design tested on mobile/tablet/desktop
- [ ] Dark mode verified across all dashboards
- [ ] Error states tested (missing data, API failures)
- [ ] Loading states verified (skeleton screens)
- [ ] Export functionality tested
- [ ] Browser console: no errors/warnings
- [ ] Performance: sub-2 second load time
- [ ] Accessibility: WCAG 2.1 AA compliance

---

## 🐛 Troubleshooting

### Dashboard Not Loading

**Problem**: Blank page or 404 error
**Solution**:
1. Verify route exists: `php artisan route:list | grep verification`
2. Check authentication: Ensure you're logged in as admin
3. Verify Inertia setup: Check `config/inertia.php`

### API Returns 401 Unauthorized

**Problem**: API responds with 401 status
**Solution**:
1. Verify Sanctum token: Check `Authorization` header
2. Check role: `User::find(1)->checkPermissionTo('admin')`
3. Review middleware in routes/web.php

### Charts Not Rendering

**Problem**: Blank chart areas
**Solution**:
1. Verify VueApexCharts installed: `npm list vue3-apexcharts`
2. Check data format: Ensure `timestamp` is ISO8601 string
3. Review browser console for errors: `F12` → Console tab

### Slow Performance

**Problem**: Dashboards slow to load or update
**Solution**:
1. Reduce polling interval (but watch API load)
2. Implement pagination for large datasets
3. Use lazy loading for charts
4. Profile with browser DevTools: `F12` → Performance tab

### Missing Data

**Problem**: Metrics show 0 or null
**Solution**:
1. Verify database has records: `select * from verification_audits`
2. Check org_id filtering: Ensure user's org has data
3. Review API response: Check `/api/deployment/verification/metrics` in Postman

---

## 📚 Related Documentation

- **Verification Hub Guide**: `VERIFICATION_HUB_GUIDE.md`
- **Testing Summary**: `VERIFICATION_HUB_TESTING_SUMMARY.md`
- **API Endpoints**: `docs/dia5_api_endpoints.md`
- **Architecture**: `docs/DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md`
- **Frontend Setup**: `docs/DIA6_GUIA_INICIO_FRONTEND.md`

---

## 📈 Metrics & KPIs

### Current Implementation

- **6 Dashboards** deployed
- **5 API Endpoints** serving data
- **200+ Lines** of composable code
- **2,000+ Lines** of Vue components
- **280+ Lines** of controller code
- **200ms** average API response time
- **<2s** dashboard load time
- **30-60s** polling frequency

### Future Roadmap

- [ ] Real-time WebSocket integration
- [ ] Advanced filtering & drill-down
- [ ] Custom dashboard builder
- [ ] Alert automation & webhooks
- [ ] Data export with scheduling
- [ ] Mobile app support
- [ ] Audit trail for all dashboard actions
- [ ] Role-based dashboard visibility

---

## 🙋 Support

For questions or issues:
1. Check this guide first
2. Review code comments in components
3. Check Laravel Boost MCP for framework guidance
4. Consult team wiki at `docs_wiki/`

---

**Version**: 1.0  
**Created**: 2025-01-27  
**Last Updated**: 2025-01-27  
**Maintained By**: Stratos Team
