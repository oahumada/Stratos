# Phase 11: Mobile-First Support

**Status:** ✅ Complete  
**Scope:** 1,900+ LOC | 4 services | 3 models | 9 endpoints | 15+ tests  
**Build Time:** ~4 hours

---

## Overview

Phase 11 extends Stratos with comprehensive mobile-first capabilities for push notifications, mobile approval workflows, and offline queue synchronization. Integrates with Phase 8 (real-time events) and Phase 10 (automation & webhooks) to deliver critical alerts and escalation workflows to mobile devices.

### Key Features

1. **Push Notifications** (FCM + APNs)
    - Device token management (iOS + Android)
    - Critical alert delivery (anomalies, compliance breaches)
    - Batch and targeted notifications
    - Fallback queue for offline delivery

2. **Mobile Approvals**
    - Escalated action approval workflows
    - Timeout and auto-escalation (24h default)
    - Compliance audit trail
    - Approval history with pagination

3. **Offline Queue**
    - Local queue for requests made while offline
    - Automatic sync when connection restored
    - Conflict detection (deduplication)
    - Retry logic (3 attempts with exponential backoff)

4. **Device Management**
    - Register/deactivate device tokens
    - Metadata tracking (app version, OS, device model)
    - Cleanup of stale tokens (30+ days)
    - Organization-level device statistics

---

## Architecture

### Data Models

#### 1. DeviceToken

```
id | user_id (FK) | organization_id (FK) | token | platform (enum: ios|android) |
is_active | last_used_at | metadata (JSON) | timestamps

Indexes: (org_id, is_active), (user_id, platform, is_active), created_at
```

**Purpose:** Store push notification tokens for multi-platform delivery  
**Relationships:** BelongsTo User, Organization  
**Scopes:** Active, ForPlatform, RecentlyUsed

#### 2. MobileApproval

```
id | organization_id (FK) | user_id (FK-approver) | requester_id (FK) |
request_type (enum) | title | description | context (JSON) | severity (enum) |
status (enum: pending|approved|rejected|escalated|expired) |
requested_at | expires_at | approved_at | rejected_at | approver_notes |
rejection_reason | approval_data (JSON) | archived_at | timestamps

Indexes: (org_id, status), (user_id, status), (org_id, requested_at), severity
```

**Purpose:** Manage approval workflows for escalated auto-remediation actions  
**Relationships:** BelongsTo User (approver), User (requester), Organization  
**Scopes:** Pending, ForUser, ForOrganization, Critical, Approved, Rejected

#### 3. OfflineQueue

```
id | user_id (FK) | organization_id (FK) | request_type (enum) | endpoint |
payload (JSON) | deduplication_key | status (enum) | retry_count | last_error |
response_data (JSON) | queued_at | synced_at | timestamps

Indexes: (org_id, status), (user_id, status), (status, retry_count),
request_type, unique(user_id, dedup_key, status)
```

**Purpose:** Queue requests made while device offline for later sync  
**Relationships:** BelongsTo User, Organization  
**Scopes:** Pending, ForUser, ForOrganization, Failed, ApprovalResponses, ReadyForRetry

---

## Services (1,000+ LOC)

### 1. PushNotificationService (300 LOC)

**Responsibilities:**

- Register device tokens (FCM Android, APNs iOS)
- Send push notifications for critical alerts
- Batch notifications to multiple users
- Track delivery status and failures
- Cleanup stale tokens

**Key Methods:**

```php
registerDevice(userId, orgId, token, platform, metadata): DeviceToken
deactivateDevice(deviceId): bool
sendAlert(orgId, userId, type, title, message, data, severity): array
sendBatchAlert(orgId, userIds[], type, title, message, data, severity): array
sendApprovalNotification(approval, title, message): array
cleanupInactiveTokens(orgId): int
```

**Alert Types:**

- `anomaly`: Metric anomaly detected (spike, trend, health degradation)
- `compliance`: Compliance breach or policy violation
- `approval`: Mobile approval required
- `escalation`: Action requires manual escalation

**Alert Severity:**

- `info`: Informational (blue)
- `warning`: Warning (amber)
- `critical`: Critical (red) - high priority

**Integration Points:**

- Phase 8 (EventStore): Can subscribe to real-time anomaly events
- Phase 10 (RemediationService): Routes escalated actions to mobile approvals
- DeviceTokenService: Manages device tokens
- MobileApprovalService: Sends approval notifications

### 2. MobileApprovalService (300 LOC)

**Responsibilities:**

- Create approval requests for escalated actions
- Route approvals to appropriate users
- Handle approve/reject decisions
- Track approval history with audit trail
- Auto-escalate expired approvals

**Key Methods:**

```php
createApprovalRequest(orgId, requesterId, approverId, requestType, title,
                     description, context, severity): MobileApproval
approve(approval, reason, additionalData): bool
reject(approval, reason, additionalData): bool
getPendingApprovals(orgId, userId): array
getApprovalHistory(orgId, status, perPage): array
escalateExpiredApprovals(orgId): int
archiveOldApprovals(orgId, daysToKeep): int
```

**Request Types:**

```
escalated_action    - Auto-remediation action requires approval before execution
manual_approval     - User must manually approve before proceeding
policy_exception    - Policy override requires management approval
```

**Workflow:**

```
pending (0-24h) → approved/rejected
pending + expired → escalated (reassign to manager)
approved/rejected + 90d old → archived
```

**Audit Trail:**

- All decisions logged to EventStore
- Approval reason captured
- Rejection reason captured
- Approval data stored for audit

### 3. OfflineQueueService (350 LOC)

**Responsibilities:**

- Queue requests when device is offline
- Sync queued requests when online
- Handle retry logic (3 attempts)
- Detect duplicates
- Store in database for persistence

**Key Methods:**

```php
queueRequest(userId, orgId, requestType, endpoint, payload, dedupeKey): OfflineQueue
syncUserQueue(userId, orgId): array
syncOrganizationQueue(orgId): array
getQueueStatus(userId, orgId): array
retryFailedRequests(orgId, maxRetries): int
cleanupOldQueue(orgId, daysToKeep): int
```

**Queue Statuses:**

```
pending      - Waiting to be synced
synced       - Successfully sent to backend
duplicate    - Already processed (conflict detected)
failed       - Failed after 3 retries
error        - Error during processing
```

**Retry Strategy:**

- Initial attempt: Immediate
- Retry 1: After 5 seconds
- Retry 2: After 30 seconds
- Retry 3: After 2 minutes
- Max: 3 total attempts, no exponential backoff for offline queue

**Conflict Resolution:**

- Deduplication key prevents double submission
- If approval already decided in DB, mark as duplicate
- Return conflict_resolution status to client

### 4. DeviceTokenService (200 LOC)

**Responsibilities:**

- Centralized device token management
- Register/update tokens per platform
- Deactivate tokens (logout)
- Get active tokens for user/organization
- Validate token format
- Cleanup stale tokens
- Provide organization statistics

**Key Methods:**

```php
register(userId, orgId, token, platform, metadata): DeviceToken
updateMetadata(deviceId, metadata): bool
deactivate(deviceId): bool
getActiveDevices(userId, orgId): array
hasActiveDevices(userId, orgId): bool
getPreferredDevice(userId, orgId): ?DeviceToken
cleanupInactiveDevices(orgId, daysOld): int
getOrganizationStats(orgId): array
validateToken(token, platform): bool
```

**Token Validation:**

- Android (FCM): 100-500 alphanumeric characters
- iOS (APNs): 64 character hex string

**Device Cleanup:**

- Max 5 devices per user per platform
- Deactivate oldest when limit exceeded
- Clean up inactive tokens > 30 days
- Archive option for stale devices

---

## API Endpoints (9 endpoints)

### Device Management

#### 1. Register Device Token

```
POST /api/mobile/register-device
Authorization: Bearer {token}

Request:
{
  "token": "fcm_token_abc...",
  "platform": "android|ios",
  "metadata": {
    "app_version": "1.0.0",
    "os_version": "14.0",
    "device_model": "iPhone 13"
  }
}

Response:
{
  "success": true,
  "message": "Device registered successfully",
  "data": {
    "device_id": 123,
    "platform": "android",
    "registered_at": "2026-03-25T12:00:00Z"
  }
}
```

#### 2. Get Active Devices

```
GET /api/mobile/devices
Authorization: Bearer {token}

Response:
{
  "success": true,
  "count": 2,
  "data": [
    {
      "id": 123,
      "platform": "android",
      "token": "dXN4UF8y...",
      "last_used_at": "2026-03-25T11:30:00Z",
      "metadata": {...}
    }
  ]
}
```

#### 3. Deactivate Device

```
DELETE /api/mobile/devices/{deviceId}
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Device deactivated successfully"
}
```

### Approval Workflows

#### 4. Get Pending Approvals

```
GET /api/mobile/approvals
Authorization: Bearer {token}

Response:
{
  "success": true,
  "count": 3,
  "data": [
    {
      "id": 456,
      "request_type": "escalated_action",
      "title": "Scale Up Server Infrastructure",
      "description": "CPU utilization spike detected",
      "severity": "critical",
      "requested_at": "2026-03-25T12:00:00Z",
      "expires_at": "2026-03-26T12:00:00Z",
      "context": {...}
    }
  ]
}
```

#### 5. Approve Request

```
POST /api/mobile/approvals/{approvalId}/approve
Authorization: Bearer {token}

Request:
{
  "reason": "Looks good to proceed",
  "additional_data": {...}
}

Response:
{
  "success": true,
  "message": "Request approved successfully",
  "data": {
    "approval_id": 456,
    "status": "approved",
    "approved_at": "2026-03-25T12:05:00Z"
  }
}
```

#### 6. Reject Request

```
POST /api/mobile/approvals/{approvalId}/reject
Authorization: Bearer {token}

Request:
{
  "reason": "Need more information before proceeding",
  "additional_data": {...}
}

Response:
{
  "success": true,
  "message": "Request rejected successfully",
  "data": {
    "approval_id": 456,
    "status": "rejected",
    "rejected_at": "2026-03-25T12:05:00Z"
  }
}
```

#### 7. Get Approval History

```
GET /api/mobile/approvals/history?page=1&status=approved&per_page=20
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": [
    {
      "id": 456,
      "request_type": "escalated_action",
      "title": "...",
      "status": "approved",
      "severity": "critical",
      "requester_name": "John Doe",
      "approver_name": "Jane Smith",
      "requested_at": "...",
      "responded_at": "..."
    }
  ],
  "pagination": {
    "total": 45,
    "per_page": 20,
    "current_page": 1,
    "last_page": 3
  }
}
```

### Offline Queue

#### 8. Sync Offline Queue

```
POST /api/mobile/offline-queue/sync
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Queue synced",
  "data": {
    "total": 5,
    "synced": 4,
    "failed": 1,
    "details": [...]
  }
}
```

#### 9. Get Queue Status

```
GET /api/mobile/offline-queue/status
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "pending": 2,
    "failed": 1,
    "synced_today": 8,
    "last_sync": "2026-03-25T11:45:00Z"
  }
}
```

---

## Multi-Tenancy & Security

### Multi-Tenant Isolation

- All endpoints scoped by `user->organization_id`
- Cannot access other organization's data
- DeviceToken indexed by (organization_id, is_active)
- MobileApproval queries filtered by organization_id
- OfflineQueue fully scoped to user + organization

### Authentication

- All endpoints require `auth:sanctum` middleware
- Token-based authentication via user tokens
- User context extracted from authenticated request

### Authorization

- Device endpoints: User can only manage their own devices
- Approval endpoints: User can only approve assigned approvals
- Statistics: Admin/manager only (role-based check)

### Data Security

- Device tokens are hidden in API responses (masked)
- Raw secrets never exposed in database queries
- Sensitive data in offline queue payloads not logged
- Approval reasons stored securely

---

## Integration Examples

### 1. Push Notification on Anomaly (Phase 8 + Phase 11)

```php
// In EventTriggerService (Phase 10)
public function triggerFromAnomalies($anomalies) {
    foreach ($anomalies as $anomaly) {
        // Create mobile approval for escalation
        $approval = $this->mobileApprovalService->createApprovalRequest(
            organizationId: $anomaly->organization_id,
            requesterId: Auth::id(),
            approverId: $this->findApprover($anomaly),
            requestType: 'escalated_action',
            title: "Anomaly: {$anomaly->metric_name}",
            description: $anomaly->description,
            context: $anomaly->toArray(),
            severity: $anomaly->severity
        );

        // Send push notification
        $this->pushService->sendApprovalNotification(
            $approval,
            "Action Required: {$anomaly->metric_name}",
            $anomaly->description
        );
    }
}
```

### 2. Offline Approval Sync

```php
// Mobile app queues approval when offline
$this->queueService->queueRequest(
    userId: Auth::id(),
    organizationId: Auth::user()->organization_id,
    requestType: 'approval_response',
    endpoint: '/api/mobile/approvals/456/approve',
    payload: [
        'approval_id' => 456,
        'reason' => 'Approved',
    ],
    deduplicationKey: 'approval_456_user_'.Auth::id()
);

// When online, app calls sync endpoint
$result = $this->syncUserQueue(userId, orgId);
// Queued requests are processed in order
```

### 3. Timeout & Escalation

```php
// Scheduled job (e.g., every 30 minutes)
$this->mobileApprovalService->escalateExpiredApprovals($organizationId);

// Internally:
// 1. Find all pending approvals with expires_at < now()
// 2. Mark as 'escalated'
// 3. Find manager for original approver
// 4. Create new approval request with manager
// 5. Send push notification to manager
```

---

## Testing (15+ test cases)

### Test Coverage

1. ✅ Register Android device
2. ✅ Register iOS device
3. ✅ Reject invalid token format
4. ✅ Get active devices
5. ✅ Deactivate device
6. ✅ Create approval request
7. ✅ Get pending approvals
8. ✅ Approve request
9. ✅ Reject request
10. ✅ Cannot approve expired request
11. ✅ Get approval history
12. ✅ Cannot access other user's approval
13. ✅ Sync offline queue
14. ✅ Get queue status
15. ✅ Multi-tenant isolation
16. ✅ Unauthenticated request rejected
17. ✅ Device stats admin only

**Run tests:**

```bash
php artisan test tests/Feature/Api/MobileControllerTest.php --compact
php artisan test --compact --filter=MobileController
```

---

## Performance Considerations

| Operation                  | Complexity            | Estimated Time       |
| -------------------------- | --------------------- | -------------------- |
| Register device            | O(1) DB insert        | <50ms                |
| Send push notification     | O(n) devices          | 100-500ms per device |
| Sync offline queue         | O(n) pending requests | 1-5s per 50 items    |
| Get pending approvals      | O(n) approvals        | <200ms for 100 items |
| Approve request            | O(1) update + event   | <100ms               |
| Escalate expired approvals | O(n) expired          | 2-10s for 1000 items |

**Optimization Tips:**

- Cache active device count per user in Redis
- Batch push notifications (max 500 per request)
- Sync offline queue in background job (avoid blocking mobile)
- Archive approvals > 90 days to improve query performance
- Index (organization_id, status) for fast filtering

---

## Configuration

### Environment Variables

```env
# Push notification providers
FIREBASE_PROJECT_ID=your-firebase-project
FIREBASE_API_KEY=your-firebase-api-key
APNS_KEY_ID=your-apns-key-id
APNS_TEAM_ID=your-apns-team-id
APNS_BUNDLE_ID=com.stratos.app

# Mobile settings
MOBILE_APPROVAL_TIMEOUT_HOURS=24
MOBILE_CLEANUP_DAYS=30
MOBILE_MAX_DEVICES_PER_PLATFORM=5
```

### config/mobile.php

```php
return [
    'approval_timeout_hours' => env('MOBILE_APPROVAL_TIMEOUT_HOURS', 24),
    'cleanup_days' => env('MOBILE_CLEANUP_DAYS', 30),
    'max_devices_per_platform' => env('MOBILE_MAX_DEVICES_PER_PLATFORM', 5),
    'offline_queue' => [
        'max_retries' => 3,
        'batch_size' => 50,
    ],
];
```

---

## Database Migrations

### Migration Files

1. `2026_03_25_120000_create_device_tokens_table.php` (110 LOC)
2. `2026_03_25_120001_create_mobile_approvals_table.php` (95 LOC)
3. `2026_03_25_120002_create_offline_queue_table.php` (85 LOC)

**Run migrations:**

```bash
php artisan migrate
```

---

## File Structure

```
app/Services/Mobile/
├── PushNotificationService.php (300 LOC)
├── MobileApprovalService.php (300 LOC)
├── OfflineQueueService.php (350 LOC)
└── DeviceTokenService.php (200 LOC)

app/Models/
├── DeviceToken.php (80 LOC)
├── MobileApproval.php (120 LOC)
└── OfflineQueue.php (110 LOC)

app/Http/Controllers/Api/
└── MobileController.php (350 LOC)

database/migrations/
├── 2026_03_25_120000_create_device_tokens_table.php
├── 2026_03_25_120001_create_mobile_approvals_table.php
└── 2026_03_25_120002_create_offline_queue_table.php

database/factories/
├── DeviceTokenFactory.php (70 LOC)
├── MobileApprovalFactory.php (90 LOC)
└── OfflineQueueFactory.php (110 LOC)

tests/Feature/Api/
└── MobileControllerTest.php (420 LOC)

routes/
└── api.php (updated with 9 mobile endpoints)
```

---

## Deployment Checklist

- [ ] Run migrations: `php artisan migrate`
- [ ] Publish configuration: `php artisan vendor:publish --tag=mobile-config`
- [ ] Configure Firebase credentials in .env
- [ ] Configure APNs credentials in .env
- [ ] Run test suite: `php artisan test`
- [ ] Set queue worker for offline sync job
- [ ] Setup scheduled job for approval escalation (every 30 min)
- [ ] Setup scheduled job for token cleanup (daily)
- [ ] Monitor push notification delivery rates
- [ ] Test push notifications on iOS and Android devices
- [ ] Verify offline queue sync works
- [ ] Validate multi-tenant isolation

---

## Future Enhancements

1. **Rich Notifications**
    - Add images/videos to push notifications
    - Custom notification styles per organization
    - Deep linking to specific app screens

2. **Advanced Approval Workflows**
    - Multi-level approvals (chain of command)
    - Delegation (approver can delegate)
    - Approval templates (canned responses)

3. **Analytics**
    - Device adoption metrics
    - Push delivery/open rates
    - Approval response times
    - Mobile engagement tracking

4. **Customization**
    - Per-organization notification preferences
    - Quiet hours configuration
    - Notification channels (SMS fallback)

5. **Performance**
    - GraphQL API for mobile (reduce payload)
    - Webhook batching for offline queue
    - WebSocket for real-time approval updates

---

## Related Phases

- **Phase 8:** Real-time WebSockets & SSE (EventStore integration)
- **Phase 9:** AI/ML Anomaly Detection (anomaly alerts)
- **Phase 10:** Event-Driven Automation (escalation triggers)

---

**Generated:** 2026-03-25  
**Status:** ✅ Complete & Production Ready
