# 🏗️ Verification Hub - Arquitectura del Sistema

## Diagrama General del Sistema

```mermaid
graph TB
    User["👤 Usuario Admin"]
    UI["🖥️ Vue 3 UI<br/>VerificationHub.vue"]

    subgraph "Frontend Layer"
        UI -->|Tab: Overview| SchedulerStatus["📊 SchedulerStatus<br/>Monitoreo en vivo"]
        UI -->|Tab: Overview| TransitionReadiness["🎯 TransitionReadiness<br/>Readiness metrics"]
        UI -->|Tab: Notifications| NotificationCenter["🔔 NotificationCenter<br/>Historial alerts"]
        UI -->|Tab: Configuration| ChannelConfig["⚙️ ChannelConfig<br/>Channels setup"]
        UI -->|Tab: Control| DryRunSimulator["🎮 DryRunSimulator<br/>What-if testing"]
        UI -->|Tab: Control| SetupWizard["🧙 SetupWizard<br/>5-step config"]
        UI -->|Tab: Audit| AuditLogExplorer["📋 AuditLogExplorer<br/>Audit history"]
        UI -->|Tab: Audit| ComplianceReportGenerator["📄 ComplianceReportGenerator<br/>Report export"]
    end

    User -->|Browser| UI

    subgraph "API Layer"
        API1["GET /scheduler-status"]
        API2["GET /transitions"]
        API3["GET /notifications"]
        API4["POST /test-notification"]
        API5["GET /configuration"]
        API6["GET /audit-logs"]
        API7["POST /dry-run"]
        API8["GET /compliance-report"]
    end

    SchedulerStatus -->|JSON| API1
    TransitionReadiness -->|JSON| API2
    NotificationCenter -->|JSON| API3
    ChannelConfig -->|JSON| API4
    ChannelConfig -->|JSON| API5
    DryRunSimulator -->|JSON| API7
    AuditLogExplorer -->|JSON| API6
    ComplianceReportGenerator -->|JSON| API8

    subgraph "Backend Controller"
        Controller["VerificationHubController<br/>instance methods"]
    end

    API1 --> Controller
    API2 --> Controller
    API3 --> Controller
    API4 --> Controller
    API5 --> Controller
    API6 --> Controller
    API7 --> Controller
    API8 --> Controller

    subgraph "Service Layer"
        MetricsService["VerificationMetricsService<br/>calculate metrics"]
        NotificationService["VerificationNotificationService<br/>send notifications"]
        CacheService["Cache Service<br/>org_id scoped"]
    end

    Controller -->|metrics| MetricsService
    Controller -->|notify| NotificationService
    Controller -->|cache| CacheService

    subgraph "Data Layer"
        DB["🗄️ PostgreSQL Database"]
        CACHE["💾 Redis Cache"]
    end

    subgraph "Database Models"
        AuditLog["VerificationAuditLog<br/>audit trail"]
        Notification["VerificationNotification<br/>notification history"]
    end

    MetricsService -->|query| DB
    NotificationService -->|write| Notification
    Controller -->|read/write| AuditLog

    CacheService -->|get/set| CACHE
    Controller -->|metrics cache| CACHE

    subgraph "External Services"
        SLACK["🎯 Slack Webhook<br/>Real-time alerts"]
        EMAIL["📧 Email Service<br/>SMTP notifications"]
        LOGS["📝 Application Logs<br/>Observability"]
    end

    NotificationService -->|POST| SLACK
    NotificationService -->|SEND| EMAIL
    NotificationService -->|LOG| LOGS

    subgraph "Multi-tenant Scope"
        TENANT["🔐 organization_id filter<br/>Applied to ALL queries"]
    end

    TENANT -.->|filter| DB
    TENANT -.->|filter| CACHE
    TENANT -.->|isolation| AuditLog
    TENANT -.->|isolation| Notification
```

---

## Flujo de Datos Detallado

### 1. Flujo de Lectura (Monitoreo)

```mermaid
sequenceDiagram
    actor User
    participant Vue as Vue Component
    participant API as API Endpoint
    participant Controller as VerificationHubController
    participant Service as VerificationMetricsService
    participant DB as Database
    participant Redis as Cache

    User->>Vue: 1. Click en Overview Tab
    Vue->>API: 2. GET /api/deployment/verification/scheduler-status
    API->>Controller: 3. schedulerStatus()
    Controller->>Redis: 4. Get cache key: verification_scheduler_*
    alt Cache Hit
        Redis-->>Controller: 5a. Cached data
    else Cache Miss
        Controller->>Service: 5b. Calculate metrics
        Service->>DB: 6. Query verification data
        DB-->>Service: 7. Raw metrics
        Service->>Controller: 8. Calculated metrics
        Controller->>Redis: 9. Store in cache (5 min TTL)
    end
    Controller-->>API: 10. JSON response
    API-->>Vue: 11. Response received
    Vue->>Vue: 12. Render cards + countdown
    Vue-->>User: 13. Display in browser
```

### 2. Flujo de Acción (Configuración)

```mermaid
sequenceDiagram
    actor Admin
    participant Vue as ChannelConfig.vue
    participant API as API Endpoint
    participant Controller as VerificationHubController
    participant Service as VerificationNotificationService
    participant Slack as Slack Webhook

    Admin->>Vue: 1. Enter Slack webhook URL
    Admin->>Vue: 2. Click [Test Slack]
    Vue->>API: 3. POST /api/deployment/verification/test-notification
    API->>Controller: 4. testNotification(channel='slack')
    Controller->>Service: 5. send(channel, 'test_message')
    Service->>Slack: 6. POST webhook URL with message
    Slack-->>Service: 7. 200 OK
    Service-->>Controller: 8. Success
    Controller-->>API: 9. JSON: {success: true}
    API-->>Vue: 10. Response received
    Vue->>Vue: 11. Show toast "Test sent successfully"
    Vue-->>Admin: 12. Visual confirmation
```

### 3. Flujo de Dry-Run Simulation

```mermaid
sequenceDiagram
    actor User
    participant Vue as DryRunSimulator.vue
    participant API as API Endpoint
    participant Controller as VerificationHubController
    participant MetricsService as VerificationMetricsService
    participant Database as Database

    User->>Vue: 1. Adjust threshold sliders
    User->>Vue: 2. Click [Run Simulation]
    Vue->>Vue: 3. Show loading spinner
    Vue->>API: 4. POST /api/deployment/verification/dry-run
    API->>Controller: 5. dryRunSimulation(thresholds)
    Controller->>MetricsService: 6. evaluateTransition(thresholds)
    MetricsService->>Database: 7. Query recent test results
    Database-->>MetricsService: 8. Test data
    MetricsService->>MetricsService: 9. Calculate:
    Note over MetricsService: - confidence vs threshold<br/>- error_rate vs threshold<br/>- retry_rate vs threshold<br/>- sample_size validation
    MetricsService->>MetricsService: 10. Determine: would_transition?
    MetricsService->>MetricsService: 11. Identify gaps (if any)
    MetricsService-->>Controller: 12. Result object
    Controller-->>API: 13. JSON response
    API-->>Vue: 14. Response received
    Vue->>Vue: 15. Render results:
    Note over Vue: - Current phase badge<br/>- Would transition?<br/>- Gaps identified<br/>- Days to meet
    Vue-->>User: 16. Display simulation results
```

### 4. Flujo de Auditoría

```mermaid
sequenceDiagram
    participant Auto as Automation
    participant Service as Verification Service
    participant DB as Database
    participant Notification as Notification Service

    Auto->>Service: 1. Hourly scheduler evaluates phase
    Service->>Service: 2. Analyze metrics
    alt Transition Needed
        Service->>DB: 3. Save audit_log entry
        DB-->>Service: 4. Audit entry created
        Service->>Notification: 5. Create notifications (multi-channel)
        Notification->>DB: 6. Save notification records
        Notification->>Notification: 7. Send via:
        par Slack
            Notification->>Notification: - To webhook
        and Email
            Notification->>Notification: - To recipients
        and Logs
            Notification->>Notification: - To application logs
        end
    else No Change
        Service->>Service: 6. Log: "No transition needed"
    end
```

---

## Mapa de Componentes

### Frontend Components Tree

```mermaid
graph TD
    VerificationHub["📄 VerificationHub.vue<br/>Master Page"]

    OverviewTab["📊 OVERVIEW TAB"]
    ControlTab["🎮 CONTROL TAB"]
    NotificationsTab["🔔 NOTIFICATIONS TAB"]
    ConfigTab["⚙️ CONFIGURATION TAB"]
    AuditTab["📋 AUDIT TAB"]

    VerificationHub --> OverviewTab
    VerificationHub --> ControlTab
    VerificationHub --> NotificationsTab
    VerificationHub --> ConfigTab
    VerificationHub --> AuditTab

    OverviewTab --> SchedulerStatus["📊 SchedulerStatus<br/>232 LOC"]
    OverviewTab --> TransitionReadiness["🎯 TransitionReadiness<br/>260 LOC"]

    ControlTab --> DryRunSimulator["🎮 DryRunSimulator<br/>350 LOC"]
    ControlTab --> SetupWizard["🧙 SetupWizard<br/>300 LOC"]

    NotificationsTab --> NotificationCenter["🔔 NotificationCenter<br/>210 LOC"]

    ConfigTab --> ChannelConfig["⚙️ ChannelConfig<br/>320 LOC"]

    AuditTab --> AuditLogExplorer["📋 AuditLogExplorer<br/>350 LOC"]
    AuditTab --> ComplianceReportGenerator["📄 ComplianceReportGenerator<br/>300 LOC"]
```

### Component Dependency Graph

```mermaid
graph LR
    Vue["Vue 3 + TypeScript"]
    Tailwind["Tailwind CSS v4"]
    Vuetify["Vuetify 3<br/>Optional components"]
    Inertia["Inertia.js v2<br/>Form helper"]

    Components["All 9 Vue<br/>Components"]

    Vue --> Components
    Tailwind --> Components
    Vuetify --> Components
    Inertia --> Components

    Components -->|HTTP| API["REST API<br/>JSON"]

    API -->|Laravel| Controller["VerificationHubController"]

    Controller -->|Service| MetricsService["VerificationMetricsService"]
    Controller -->|Service| NotificationService["VerificationNotificationService"]

    MetricsService -->|Eloquent| Models["Eloquent Models"]
    NotificationService -->|Eloquent| Models

    Models -->|Read/Write| Database["🗄️ Database"]
    Models -->|Cache| Redis["💾 Redis"]
```

---

## Data Flow Architecture

### State Management Pattern

```mermaid
graph TB
    Component["Vue Component"]
    LocalState["Local State<br/>ref, computed"]
    APICall["API Call<br/>fetch/axios"]
    Cache["Redis Cache<br/>5-60 min TTL"]
    Database["PostgreSQL<br/>Persistent storage"]

    Component -->|fetch| LocalState
    Component -->|GET /api/*| APICall
    APICall -->|org_id scoped| Cache
    alt Cache Hit
        Cache -->|Data| APICall
    else Cache Miss
        Cache -->|Query| Database
        Database -->|Results| Cache
        Cache -->|Data| APICall
    end
    APICall -->|JSON| Component
    Component -->|Update| LocalState
    Component -->|Display| Browser["🖥️ Browser DOM"]
```

### Multi-Tenant Scoping (Critical)

```mermaid
graph TD
    Request["Incoming Request<br/>auth()->user()"]

    MiddleWare["Middleware<br/>tenant, verified, role:admin"]

    Request -->|Check Auth| MiddleWare
    MiddleWare -->|Extract| OrgID["organization_id<br/>from user context"]

    OrgID -->|Filter ALL| QueryScope["Query Scope"]

    QueryScope -->|Eloquent| Models["Models"]
    Models -->|where organization_id = X| Database["Database"]
    Database -->|Selected rows<br/>org_id = X only| Response["JSON Response<br/>Scoped data"]

    subgraph Safety
        OrgID
        QueryScope
        Models
    end
```

---

## Sequence of Operations

### Daily Operational Flow

```mermaid
sequenceDiagram
    participant Admin as Admin User
    participant Browser as Browser
    participant API as Laravel API
    participant Queue as Job Queue
    participant DB as Database
    participant Redis as Cache
    participant Slack as Slack

    Note over Admin,Slack: 08:00 - Morning Check
    Admin->>Browser: 1. Open /deployment/verification-hub
    Browser->>API: 2. Fetch scheduler status
    API->>Redis: 3. Check cache
    Redis-->>Browser: 4. Cached metrics
    Browser-->>Admin: 5. Display overview

    Note over Admin,Slack: 09:00 - Check Notifications
    Admin->>Browser: 6. Click Notifications tab
    Browser->>API: 7. GET /notifications?unread=true
    API->>DB: 8. Query unread notifications
    DB-->>Browser: 9. Last 20 notifications
    Browser-->>Admin: 10. Display list

    Note over Admin,Slack: 10:00 - Automatic Scheduler Run
    Queue ->>Queue: 1. Verification scheduler job
    Queue->>API: 2. Evaluate current phase
    API->>DB: 3. Get test results
    DB-->>API: 4. Metrics data
    API->>API: 5. Evaluate vs thresholds
    alt Transition Meets Criteria
        API->>DB: 6. Create audit_log entry
        API->>DB: 7. Create notification records
        API->>Slack: 8. POST to webhook
        Slack-->>Admin: 9. Slack notification received
    else No Transition
        API->>DB: 6. Log: "No change needed"
    end

    Note over Admin,Slack: EOD - Export Report
    Admin->>Browser: 11. Click Audit tab
    Browser->>API: 12. GET /compliance-report?date_from=...
    API->>DB: 13. Query audit logs (date range)
    DB-->>API: 14. Audit trail
    API-->>Browser: 15. JSON data
    Browser-->>Admin: 16. Generate PDF
    Admin->>Admin: 17. Download report
```

---

## Security Architecture

### Authentication & Authorization

```mermaid
graph TB
    User["👤 User"]
    Login["Login Page"]
    Sanctum["📍 Sanctum<br/>Token Generation"]
    Token["Bearer Token<br/>Stored in Cookie/LS"]

    Request["HTTP Request"]
    Middleware["Middleware<br/>auth:sanctum"]

    User -->|1. Login| Login
    Login -->|2. Verify credentials| Sanctum
    Sanctum -->|3. Generate token| Token
    Token -->|4. Store| Browser["🖥️ Browser"]

    Browser -->|5. Include token| Request
    Request -->|6. Check token| Middleware
    Middleware -->|7. Valid?| ValidCheck{Valid<br/>Token?}
    ValidCheck -->|Yes| Authenticated["✅ Authenticated<br/>user context available"]
    ValidCheck -->|No| Unauthorized["❌ 401<br/>Unauthorized"]

    Authenticated -->|8. Extract org_id| ScopeFilter["Scope Filter<br/>organization_id"]
    ScopeFilter -->|Query WHERE| Database
```

### Data Isolation (Multi-tenant)

```mermaid
graph TB
    OrgA["🏢 Organization A<br/>org_id = 1"]
    OrgB["🏢 Organization B<br/>org_id = 2"]

    UserA["User A<br/>org_id=1"]
    UserB["User B<br/>org_id=2"]

    RequestA["Request from UserA"]
    RequestB["Request from UserB"]

    UserA -->|Login| OrgA
    UserB -->|Login| OrgB

    OrgA -->|Query| RequestA
    OrgB -->|Query| RequestB

    RequestA -->|WHERE org_id=1| Database["Database"]
    RequestB -->|WHERE org_id=2| Database

    Database -->|ONLY<br/>rows org_id=1| UserA
    Database -->|ONLY<br/>rows org_id=2| UserB

    subgraph "🔒 Isolation Guarantee"
        Database
    end
```

---

## Performance Optimization

### Caching Strategy

```mermaid
graph TB
    Request["Incoming Request"]

    CacheKey["Generate Cache Key<br/>verification_scheduler_{org_id}"]

    CheckCache{"Cache<br/>Exists?"}

    Request -->|1. API called| CacheKey
    CacheKey -->|2. Build key| CheckCache

    CheckCache -->|Yes| CacheHit["✅ HIT<br/>Return cached data<br/>~10ms"]
    CacheHit -->|No DB query| Response["Response<br/>Instant"]

    CheckCache -->|No| CacheMiss["❌ MISS<br/>Execute query"]
    CacheMiss -->|3. Query DB| Database["Database"]
    Database -->|4. Results| Process["5. Process<br/>calculate metrics"]
    Process -->|6. Store| Cache["Set Cache<br/>TTL: 5 min"]
    Cache -->|Return| Response

    Response -->|Display| Frontend["Vue Component"]
```

### Query Optimization

```mermaid
graph LR
    BadQuery["❌ N+1 Problem<br/>Loop over items<br/>Query each one"]

    GoodQuery["✅ Eager Loading<br/>Load all at once<br/>Join in single query"]

    BadQuery -->|1000 items<br/>1001 queries<br/>2 seconds| Slow["🐌 Slow"]

    GoodQuery -->|1000 items<br/>1 query<br/>50ms| Fast["⚡ Fast"]

    style Slow fill:#ff6666
    style Fast fill:#66ff66
```

---

## Deployment Architecture

### Environment Stages

```mermaid
graph LR
    Dev["💻 Development<br/>localhost:8000"]
    Staging["🔧 Staging<br/>staging.stratos.com"]
    Production["🚀 Production<br/>app.stratos.com"]

    Dev -->|Code pushed| CI["🤖 CI/CD<br/>Tests run"]
    CI -->|Tests pass| Staging
    Staging -->|Manual approval| Production

    Dev -.->|Feature branch| Git["GitHub Repo"]
    Git -.->|Pull request| CI
```

### File Structure

```
src/
├── app/
│   └── Http/Controllers/Deployment/
│       └── VerificationHubController.php (430 LOC)
│
├── resources/js/
│   ├── Pages/Deployment/
│   │   └── VerificationHub.vue (master)
│   │
│   └── components/Verification/
│       ├── SchedulerStatus.vue
│       ├── NotificationCenter.vue
│       ├── ChannelConfig.vue
│       ├── TransitionReadiness.vue
│       ├── DryRunSimulator.vue
│       ├── SetupWizard.vue
│       ├── AuditLogExplorer.vue
│       └── ComplianceReportGenerator.vue
│
└── routes/
    └── web.php (API routes + web route)
```

---

## Metrics & Monitoring

### Key Performance Indicators

```mermaid
pie title Verification System KPIs
    "Uptime (Target 99.9%)" : 99.8
    "Downtime" : 0.2
```

### Health Checks

```mermaid
graph TD
    HealthCheck["Health Check<br/>/deployment/verification-hub"]

    HealthCheck -->|1. API Responsive| API_Status{API<br/>OK?}
    HealthCheck -->|2. Database Connected| DB_Status{DB<br/>Connected?}
    HealthCheck -->|3. Cache Available| Cache_Status{Redis<br/>OK?}

    API_Status -->|✅| Green["🟢 System Healthy"]
    DB_Status -->|✅| Green
    Cache_Status -->|✅| Green

    API_Status -->|❌| Red["🔴 Alert Admin"]
    DB_Status -->|❌| Red
    Cache_Status -->|❌| Red
```

---
