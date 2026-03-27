# Task 2 Phase 2 - Planning Workflows & Approval System

**Status**: Iniciación  
**Date Range**: Mar 27 - Apr 20, 2026 (14 días)  
**Target LOC**: 2,000-2,400  
**Branch**: feature/scenario-planning-phase2

---

## Phase 2 Overview

Phase 2 implements the workflow engine and approval system for scenarios. This allows scenarios to flow through a formal approval process before activation, with notifications to stakeholders and full audit trail.

### Key Deliverables

| Component | Scope | Est. LOC |
|-----------|-------|---------|
| Backend Service | ScenarioWorkflowService | 400 |
| API Controller | ScenarioApprovalController | 250 |
| Database | Scenario workflow columns | 50 |
| Vue Components | 4-5 workflow UI | 800 |
| Tests | Workflow + approval logic | 350 |
| Migration | Workflow state tracking | 100 |
| **Total** | | **1,950** |

---

## Existing Patterns (Found in Codebase)

### 1. ApprovalRequest Model
**Location**: `app/Models/ApprovalRequest.php`  
**Capabilities**:
- UUID token (unique, single-use)
- Polymorphic approvable (Roles, Competencies, etc.)
- States: pending, approved, rejected
- Digital signature support
- Magic links for approval URLs
- Expiration handling

**For Phase 2**: We can extend this model for scenarios or create ScenarioApprovalRequest

### 2. RoleDesignerService Pattern
**Location**: `app/Services/Talent/RoleDesignerService.php`  
**Methods We'll Mirror**:
- `requestApproval($resourceId, $approverId)` → Creates ApprovalRequest
- `finalizeApproval($token, $data)` → Processes approval with signature
- `transitionStatus()` → Handles state transitions

### 3. Workflow States (Existing)
**From Workforce Planning**:
```
draft → pending_approval → approved → active
                        ↓
                      rejected
                        ↓
                      draft
```

**For Execution**:
```
planned → in_progress → paused/completed
```

### 4. Magic Link Pattern
**Existing Implementation**:
- URL: `/approve/role/{token}`
- Process: Generate token → Send link → User accesses without login → Approve/Reject

**For Phase 2**: Create `/approve/scenario/{token}`

---

## Phase 2 Architecture

### Backend Flow

```
┌─────────────────────────────────────────────────────┐
│ User: "Submit Scenario for Approval"                │
└────────────────────┬────────────────────────────────┘
                     │
                     ↓
    ┌─────────────────────────────────────┐
    │ ScenarioApprovalController           │
    │ POST /api/scenarios/{id}/submit      │
    └────────────┬────────────────────────┘
                 │
                 ↓
    ┌──────────────────────────────────────┐
    │ ScenarioWorkflowService               │
    │ - submitForApproval()                 │
    │ - buildApprovalMatrix()               │
    │ - notifyStakeholders()                │
    └────────────┬─────────────────────────┘
                 │
                 ↓
    ┌──────────────────────────────────────┐
    │ ApprovalRequest (polymorphic)        │
    │ - approvable_type: Scenario          │
    │ - approvable_id: scenario.id         │
    │ - approver_id: assigned user         │
    │ - token: UUID                        │
    │ - status: pending/approved/rejected  │
    └──────────────────────────────────────┘
```

### Approval Matrix Logic

```
Scenario -> ApprovalRequest 
         -> Assigned to: CHRO / Strategy Lead / Finance Lead
         -> Multi-signature possible (Phase 2.5)
         -> Each approval advances status
         -> All must approve before activation
```

### State Machine

```
DRAFT
  | POST /submit-approval
  v
PENDING_APPROVAL (frozen - no edits)
  | POST /approve (all approvers)
  v
APPROVED
  | POST /activate
  v
ACTIVE (execute)
  | If execution starts
  v
IN_PROGRESS
  | Complete/Pause/Archive
  v
COMPLETED / ARCHIVED
```

---

## Implementation Plan

### Week 1 (Mar 27 - Apr 2)

#### Day 1-2: Models & Migrations
- [ ] Create/extend ScenarioApprovalRequest model
- [ ] Migration: `scenarios` table add workflow columns
  - `decision_status` (draft, pending_approval, approved, active, rejected, archived)
  - `execution_status` (planned, in_progress, paused, completed)
  - `submitted_by` (who submitted for approval)
  - `submitted_at`
  - `approved_by` (who approved)
  - `approved_at`
  - `rejection_reason`

#### Day 3-4: Service Layer
- [ ] Create `ScenarioWorkflowService.php`
  - `submitForApproval(scenarioId, submittedById)`
  - `approve(approvalRequestId, approverId, notes)`
  - `reject(approvalRequestId, approverId, reason)`
  - `activate(scenarioId)`
  - `buildApprovalMatrix(scenarioId)` → determines who needs to approve
  - `canBeEdited(scenarioId)` → false if pending/approved
  - Helper: `notifyApprovers($scenario, $approverIds)`

#### Day 5: API Controller
- [ ] Create `ScenarioApprovalController.php`
  - `submitForApproval()` - POST /api/scenarios/{id}/submit-approval
  - `approve()` - POST /api/approval-requests/{id}/approve
  - `reject()` - POST /api/approval-requests/{id}/reject
  - `getApprovalMatrix()` - GET /api/scenarios/{id}/approval-matrix
  - `activate()` - POST /api/scenarios/{id}/activate
  - Authorization: Policy guards for each action

### Week 2 (Apr 3-9)

#### Day 6-7: Vue Components
- [ ] **ApprovalMatrix.vue** (280 LOC)
  - Shows who needs to approve
  - Current status of each approver
  - Timeline of approvals
  
- [ ] **WorkflowTimeline.vue** (300 LOC)
  - Timeline: Draft → Pending → Approved → Active → In Progress → Completed
  - Shows current stage
  - Milestone dates
  
- [ ] **ApprovalRequestCard.vue** (250 LOC)
  - Card showing approval request details
  - Current approver info
  - Approve/Reject buttons (for approvers)
  - Status badge
  
- [ ] **ExecutionPlan.vue** (280 LOC)
  - Generated after approval
  - Phases, milestones, tasks
  - Start/pause/complete controls

#### Day 8: Integration into ScenarioPlanning/Analytics.vue
- [ ] Add 2 new tabs:
  - "Approval" (shows current approval status)
  - "Execution" (shows execution plan)
- [ ] Integration with Phase 1 components
- [ ] Dark mode support

#### Day 9: Tests
- [ ] Create `ScenarioWorkflowServiceTest.php` (10 tests)
- [ ] Create `ScenarioApprovalControllerTest.php` (15 tests)

### Week 3 (Apr 10-16)

#### Day 10: Build & Polish
- [ ] Build verification
- [ ] Fix any errors
- [ ] Test workflow end-to-end

#### Day 11: Documentation
- [ ] Phase 2 execution summary
- [ ] Workflow architecture guide
- [ ] API endpoint documentation

#### Day 12-13: Phase 2.5 - Multi-signature (Optional)
- [ ] Allow multiple approvers in sequence
- [ ] Approval chain logic
- [ ] Parallel vs sequential approvals

#### Day 14: Buffer & Review
- [ ] Final testing
- [ ] Performance review
- [ ] Security audit

---

## Technical Decisions

### 1. Approval Request Model Strategy
**Option A**: Extend existing `ApprovalRequest` (polymorphic)  
**Option B**: Create new `ScenarioApprovalRequest` (specific to scenarios)

**Decision**: Use **Option A** (extend existing) - leverages existing infrastructure, polymorphism supports multiple resource types.

### 2. Notification Strategy
**Methods**:
- In-app notifications (Bell icon)
- Email notifications (if email configured)
- Slack webhooks (optional)

**Phase 2**: In-app + email

### 3. Signature Strategy
**Existing Pattern**: HMAC-SHA256 digital signature on approval  
**Phase 2**: Reuse this pattern for scenarios

### 4. Workflow State Storage
**Location**: `scenarios.decision_status`, `scenarios.execution_status`  
**Alternative**: Event store (too complex for Phase 2)

### 5. Approval Matrix
**Logic**: 
- Submitter role determines approvers
- Can be overridden by strategy configuration
- For Phase 2: Hardcoded based on scenario impact (financial threshold)

---

## API Endpoints (Phase 2)

```
POST   /api/scenarios/{id}/submit-approval
       Request: { submitted_by: user_id, notes?: string }
       Response: { success, approval_request_id, approvers: [...] }

GET    /api/scenarios/{id}/approval-matrix
       Response: { approvers: [...], required_approvals: int, current: int }

POST   /api/approval-requests/{id}/approve
       Request: { approver_id: int, notes: string, signature?: string }
       Response: { success, status: 'approved', next_approver?: {...} }

POST   /api/approval-requests/{id}/reject
       Request: { approver_id: int, reason: string }
       Response: { success, status: 'rejected', scenario_reverted_to: 'draft' }

POST   /api/scenarios/{id}/activate
       Request: {}
       Response: { success, execution_plan_id: int }

GET    /api/scenarios/{id}/execution-plan
       Response: { phases: [...], timeline: [], milestones: [...] }
```

---

## Database Schema Changes

### Scenarios Table - Add Columns

```sql
ALTER TABLE scenarios ADD COLUMN (
    decision_status VARCHAR(50) DEFAULT 'draft', -- draft, pending_approval, approved, active, archived
    execution_status VARCHAR(50) DEFAULT 'planned', -- planned, in_progress, paused, completed
    submitted_by BIGINT UNSIGNED NULL,
    submitted_at TIMESTAMP NULL,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    rejected_by BIGINT UNSIGNED NULL,
    rejected_at TIMESTAMP NULL,
    rejection_reason TEXT NULL
);

CREATE INDEX idx_scenarios_decision_status ON scenarios(decision_status);
CREATE INDEX idx_scenarios_execution_status ON scenarios(execution_status);
```

### ApprovalRequests (Existing)

```sql
-- Already has:
-- approvable_type: 'App\\Models\\Scenario'
-- approvable_id: scenarios.id
-- approver_id: user.id
-- status: 'pending', 'approved', 'rejected'
-- token, signed_at, signature_data
```

---

## Testing Strategy

### Unit Tests (10 tests)
- `testSubmitForApproval()` - Creates ApprovalRequest correctly
- `testApprovalMatrix()` - Determines correct approvers
- `testCannotEditPendingScenario()` - Frozen during approval
- `testRejectTransitionsToDraft()` - Rejection restores editability
- `testApproveAdvancesStatus()` - Each approval moves forward
- `testAllApprovalsRequiredBeforeActivation()` - Multi-signature logic
- `testActivationCreatesExecutionPlan()` - Execution plan generation
- `testNotificationsSent()` - Approvers notified
- `testMagicLinkGeneration()` - Token & URL creation
- `testExpiration()` - Approval requests expire

### Feature Tests (15 tests)
- `testFullApprovalWorkflow()` - Happy path: submit → approve → activate
- `testRejectionWorkflow()` - Rejection → back to draft → edit → resubmit
- `testMultipleApprovers()` - Sequential approvals
- `testConcurrentApprovalAttempts()` - Only one can approve
- `testAuthorizationGates()` - Only authorized users can approve
- `testOrganizationScoping()` - Multi-tenant isolation
- `testExecutionPlanDetails()` - Plan structure validation
- `testTimelineCalculation()` - Dates calculated correctly
- `testCostBreakdownInApproval()` - Financial impact shown
- `testRiskAssessmentInApproval()` - Risk visible during approval
- `testSkillGapsInApproval()` - Talent impact visible
- `testEmailNotifications()` - Emails sent to approvers
- `testInAppNotifications()` - Dashboard updates
- `testAuditTrail()` - All actions logged
- `testRoleBasedAccess()` - Only authorized roles can submit

---

## Git Commits Plan

```
1. migration: Add scenario workflow columns
2. feat: Create ScenarioWorkflowService with state machine
3. feat: Create ScenarioApprovalController with 5 endpoints
4. feat: Create Phase 2 Vue components (ApprovalMatrix, Workflow, etc)
5. feat: Integrate approval workflow into Analytics page
6. test: Add 25+ comprehensive workflow tests
7. docs: Add Phase 2 architecture and workflow documentation
```

---

## Success Criteria

- ✅ Scenario can be submitted for approval (state: draft → pending_approval)
- ✅ Approval request creates with correct approvers identified
- ✅ Approvers can approve/reject via magic link or dashboard
- ✅ Approval changes state (pending_approval → approved)
- ✅ Rejected scenarios return to draft (pending_approval → draft)
- ✅ Approved scenarios can be activated (approved → active)
- ✅ Active scenarios can start execution (active → in_progress)
- ✅ Approvers are notified of requests
- ✅ Full audit trail maintained
- ✅ Build: 0 errors
- ✅ Tests: 25+ passing
- ✅ ~2,000-2,400 LOC delivered

---

## Risk Assessment

### Known Risks

1. **Migration Test Database** - Previous issue with talent_passes table  
   *Mitigation*: Run full migration suite in order

2. **Complex State Transitions** - Many possible states  
   *Mitigation*: Implement state machine guard clauses

3. **Concurrent Approvals** - Multiple approvers simultaneously  
   *Mitigation*: Lock mechanism or transaction-based updates

### Contingency

If time runs short:
- Keep Phase 2.5 (multi-signature) as optional
- Reduce to single-approver workflow
- Simplify execution plan generation

---

## Phase 2.5 (Optional - If Time Permits)

- Parallel vs Sequential approval flows
- Multi-level approval chains
- Delegation of approvals
- Approval templates
- Approval history/audit dashboard
- Advanced notification preferences

---

## Next Steps

1. Create migration for scenario workflow columns
2. Implement ScenarioWorkflowService
3. Create ScenarioApprovalController
4. Build Vue components
5. Integrate into Phase 1 Analytics page
6. Write comprehensive tests
7. Document architecture

**Start Date**: March 27-28   
**Expected Completion**: April 16-20

---

**Phase 2 Lead**: Prepared based on existing approval patterns in codebase (RoleDesigner, MobileApproval, ApprovalRequests)

👉 **Ready to begin implementation**
