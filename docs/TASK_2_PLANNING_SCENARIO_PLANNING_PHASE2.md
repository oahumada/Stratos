# Task 2: Scenario Planning Phase 2 - Comprehensive Planning

**Status:** 🚀 Ready to Start (Apr 1-25, 2026)  
**Project ID:** `oahumada/Stratos`  
**Priority:** HIGH  
**Complexity:** Medium-High (Similar to Task 1)

---

## Executive Summary

**Task 1 Completion**: Admin Dashboard Polish ✅ (6,504 LOC, 10 Vue components, 20 API endpoints, v0.3.0 released)

**Task 2 Objective**: Expand Scenario Planning system with advanced planning capabilities, analytics, and workforce transformation workflows.

**Estimated Scope**: 5,500-7,000 LOC backend/frontend combined, 50-70 tests, 8-10 Vue components, 3 phases

**Timeline**: Apr 1-25, 2026 (25 days, ~3.5 weeks)

---

## 1. Project Context

### 1.1 What is Scenario Planning?

Scenario Planning is the ability for HR and strategic teams to model different organizational transformation strategies:

- Create multiple "scenarios" (e.g., "Growth Strategy", "Cost Optimization", "Market Expansion")
- Define workforce changes: headcount adjustments, role realignments, skill requirements
- Simulate impact on people, budget, risk
- Track execution: from planning → approval → implementation

### 1.2 Current State (After Task 1)

✅ Foundation System:

- Scenario model exists with basic CRUD
- Alert system in place (SLA monitoring, escalation)
- Audit trail for change tracking
- Admin dashboard operational

❌ Not Yet Implemented:

- Advanced scenario analysis (ROI, cost impact, risk assessment)
- Workforce transformation workflows
- Scenario comparison (side-by-side analysis)
- Planning templates and wizards
- Change impact simulation
- Stakeholder approval workflows

### 1.3 Target Users

- **Strategic Planners**: Create, model, and refine scenarios
- **Finance Teams**: Budget impact analysis, ROI calculations
- **HR Leaders**: Workforce impact, skill gap analysis, change management
- **Executives**: Executive summaries, decision dashboards
- **Managers**: Team-level impact, direct reports analysis

---

## 2. Phase Breakdown

### Phase 1: Advanced Scenario Analytics (Apr 1-10)

**Duration**: 10 days | **Scope**: ~1,200-1,500 LOC | **Deliverables**: 3-4 Vue components + 2 services

**Objectives**:

- [ ] Build scenario comparison engine (scenario A vs B vs C side-by-side)
- [ ] Implement financial impact calculation service
- [ ] Create scenario timeline visualization component
- [ ] Build scenario metrics dashboard component
- [ ] Create scenario risk assessment component
- [ ] Write 15+ tests for analytics logic

**Technical Details**:

```
Backend:
- ScenarioAnalyticsService (extended)
  - compareScenarios(ids: []) → analysis object
  - calculateFinancialImpact(scenarioId) → impact breakdown
  - calculateRiskMetrics(scenarioId) → risk scores
  - calculateSkillGaps(scenarioId) → gap analysis
  - projectWorkforceEvolution(scenarioId) → timeline

- Models:
  - Extend Scenario model with helper methods
  - Create ScenarioComparison DTO

Frontend:
- ScenarioComparison.vue (450 LOC)
  - Side-by-side layout (3-4 scenarios)
  - Metric comparison grid
  - Visual diff highlighting
  - Export comparison as PDF

- ScenarioTimeline.vue (280 LOC)
  - Phase timeline (months)
  - Milestone markers
  - Key transformation events
  - SVG-based timeline

- ScenarioMetrics.vue (250 LOC)
  - 4 primary Cards (Cost, Headcount, Risk, ROI)
  - Gauge charts
  - Trend indicators

- RiskAssessment.vue (280 LOC)
  - Risk matrix (probability vs impact)
  - Risk itemization
  - Mitigation suggestions
```

**API Endpoints (Phase 1)**:

- POST /api/scenarios/compare (body: scenario_ids[])
- GET /api/scenarios/{id}/analytics
- GET /api/scenarios/{id}/financial-impact
- GET /api/scenarios/{id}/risk-assessment
- GET /api/scenarios/{id}/skill-gaps

**Tests**: 15+ (analytics logic, comparison engine, impact calculations)

**Git Commits**: 3-4 commits

---

### Phase 2: Planning Workflows & Approval System (Apr 11-18)

**Duration**: 8 days | **Scope**: ~2,000-2,400 LOC | **Deliverables**: 4-5 Vue components + 2-3 services

**Objectives**:

- [ ] Build scenario workflow engine (draft → review → approved → active)
- [ ] Create approval request / sign-off system
- [ ] Implement stakeholder notification system
- [ ] Build approval matrix (who approves what)
- [ ] Create scenario execution plan generator
- [ ] Write 20+ workflow tests

**Technical Details**:

```
Backend:
- ScenarioWorkflowService (350 LOC)
  - submitForApproval(scenarioId) → creates ApprovalRequest
  - approve(approvalId, notes) → advances workflow
  - reject(approvalId, reason) → sends back to draft
  - activate(scenarioId) → marks as active
  - archive(scenarioId, reason) → archives scenario
  - getApprovalChain(scenarioId) → stakeholder list

- ApprovalRequest model
  - Fields: scenario_id, requested_by, approvers, current_status, responses
  - Relationships: belongsTo Scenario, hasMany ApprovalResponse
  - Helpers: isApproved(), isPending(), isPendingReview()

- ApprovalResponse model
  - Fields: approval_request_id, approver_id, status (approved|rejected), notes
  - Relationships: belongsTo ApprovalRequest, User
  - Events: triggers notifications

- ScenarioExecutionPlanService (280 LOC)
  - generateExecutionPlan(scenarioId) → plan object
  - createTransformationTasks() → milestone tasks
  - assignOwners() → task ownership
  - trackProgress() → completion metrics

- Policies: ApprovalPolicy, ScenarioPolicy (advanced)
  - can approve (roles: finance_lead, cfo, hr_lead)
  - can activate (roles: executive_leadership)
  - can archive (roles: admin, executive_leadership)

Frontend:
- ScenarioApprovalFlow.vue (350 LOC)
  - Workflow status display
  - Current approver cards
  - Approval history timeline
  - Action buttons (approve/reject)

- ApprovalMatrix.vue (300 LOC)
  - Department-based matrix
  - Role-based requirements
  - Approval chains visualization
  - Edit matrix (admin only)

- ExecutionPlanView.vue (350 LOC)
  - Milestone list (Gantt-style)
  - Task assignments
  - Dependency visualization
  - Progress tracking

- NotificationCenter.vue (200 LOC)
  - Approval notifications
  - Task assignments
  - System alerts
  - Dismiss/archive actions

API Endpoints (Phase 2):
- POST /api/scenarios/{id}/submit-approval
- POST /api/approval-requests/{id}/approve
- POST /api/approval-requests/{id}/reject
- POST /api/scenarios/{id}/activate
- GET /api/approval-matrix
- GET /api/scenarios/{id}/execution-plan

Tests: 20+ (workflow logic, approval chains, task creation)

Git Commits: 3-4 commits
```

---

### Phase 3: Planning Tools & Visualization (Apr 19-25)

**Duration**: 7 days | **Scope**: ~2,300-2,600 LOC | **Deliverables**: 3-4 Vue components + 1-2 services

**Objectives**:

- [ ] Build scenario template system (reusable workflows)
- [ ] Create interactive scenario builder wizard
- [ ] Build 3D/advanced org chart with scenario overlay
- [ ] Create executive dashboard summarizing all scenarios
- [ ] Implement scenario what-if analysis tool
- [ ] Write 15-20 tests for visualization components

**Technical Details**:

```
Backend:
- ScenarioTemplateService (220 LOC)
  - listTemplates() → available scenario templates
  - createFromTemplate(templateId, customizations) → clone template
  - saveAsTemplate(scenarioId, templateName) → create template from scenario
  - updateTemplate(templateId, changes) → update template

- ScenarioTemplate model
  - Fields: name, description, category, structure (JSON)
  - Relationships: belongs_to organization, hasMany scenarios_created_from
  - Categories: growth, optimization, market_expansion, cost_reduction

- WhatIfAnalysisService (280 LOC)
  - simulateChange(scenarioId, change) → predicted impact
  - calculateSensitivity(scenarioId, parameter) → sensitivity analysis
  - compareDifference(scenarioIdA, scenarioIdB) → detailed diff
  - projectedChanges(scenarioId) → month-by-month changes

Frontend:
- ScenarioBuilder.vue (600 LOC - substantial)
  - Multi-step wizard
  - Step 1: Select template or start blank
  - Step 2: Define workforce changes (headcount, roles)
  - Step 3: Set timeline & phases
  - Step 4: Define success metrics
  - Step 5: Review & create
  - Form validation at each step

- OrgChartOverlay.vue (400 LOC)
  - Load org chart
  - Apply scenario transformations
  - Highlight changes (color-coded)
  - Show role transitions
  - Interactive tooltips with impact details

- ExecutiveDashboard.vue (350 LOC)
  - Grid of all active + pending scenarios
  - Summary metrics per scenario
  - Approval status indicators
  - Quick actions (view, compare, approve)
  - Filtering (status, department, finance impact)

- WhatIfAnalyzer.vue (300 LOC)
  - Parameter sliders (headcount, budget, timeline)
  - Real-time impact recalculation
  - Sensitivity charts
  - Scenario comparison overlay
  - Export analysis

API Endpoints (Phase 3):
- GET /api/scenario-templates
- POST /api/scenarios/from-template/{templateId}
- POST /api/scenarios/{id}/save-as-template
- POST /api/scenarios/{id}/what-if-analyze
- GET /api/scenarios/executive-summary
- GET /api/org-chart/{id}?scenario_id=X (with overlay)

Tests: 15-20 (template logic, what-if calculations, visualization edge cases)

Git Commits: 3-4 commits
```

---

## 3. Integration & Command Center

### 3.1 Landing Page Integration

Create a new "Scenario Planning Center" landing page (similar to Command Center):

**Location**: `/scenario-planning/command-center` (or extend admin dashboard)

**Layout**: 6-8 tarjetas representing:

1. **Create Scenario** (blue) → launch wizard
2. **My Scenarios** (indigo) → list personal scenarios
3. **Pending Approvals** (amber) → scenarios awaiting sign-off
4. **Compare Scenarios** (cyan) → select & compare
5. **Templates** (purple) → manage templates
6. **Executive Summary** (rose) → dashboard
7. **Analytics** (green) → detailed analytics
8. **Archived** (gray) → historical scenarios

**Menu Navigation**:

- Add "Scenario Planning" to sidebar (if not already present)
- Icon: PhFlowArrow or PhGitBranch (transformation/branching concept)
- Admin + HR Leader access

### 3.2 Navigation Updates

**AppSidebar.vue**:

- Add scenario planning nav item (if not present)
- Visibility rules: admin, hr_leader roles

**routes/web.php**:

- Add `/scenario-planning/command-center` route

---

## 4. Database Schema Additions

### New Tables (To Be Created)

```sql
-- Approval system
CREATE TABLE approval_requests (
  id BIGINT PK,
  organization_id BIGINT FK,
  scenario_id BIGINT FK,
  requested_by BIGINT FK (user),
  current_status ENUM('pending', 'in_review', 'approved', 'rejected'),
  rejection_reason TEXT,
  created_at, updated_at
);

CREATE TABLE approval_responses (
  id BIGINT PK,
  approval_request_id BIGINT FK,
  approver_id BIGINT FK (user),
  status ENUM('approved', 'rejected', 'pending'),
  notes TEXT,
  responded_at TIMESTAMP,
  created_at, updated_at
);

-- Planning templates
CREATE TABLE scenario_templates (
  id BIGINT PK,
  organization_id BIGINT FK,
  name VARCHAR,
  description TEXT,
  category VARCHAR (growth|optimization|market_expansion|cost_reduction),
  structure JSON,
  created_by BIGINT FK (user),
  created_at, updated_at
);

-- Execution tracking
CREATE TABLE transformation_tasks (
  id BIGINT PK,
  scenario_id BIGINT FK,
  title VARCHAR,
  description TEXT,
  assigned_to BIGINT FK (user),
  status ENUM('pending', 'in_progress', 'completed', 'blocked'),
  due_date DATE,
  completion_date DATE,
  created_at, updated_at
);
```

### Extended Columns (Existing Tables)

**scenarios table** (extend):

- `approval_status` ENUM('draft', 'submitted', 'approved', 'active', 'archived')
- `activated_at` TIMESTAMP
- `archived_at` TIMESTAMP
- `archive_reason` TEXT
- `execution_plan` JSON

---

## 5. API Architecture

### Namespace Structure

```
/api/scenarios/
├── GET        / (list scenarios)
├── POST       / (create)
├── GET        /{id} (show)
├── PATCH      /{id} (update)
├── DELETE     /{id} (delete)
├── POST       /{id}/submit-approval
├── POST       /{id}/activate
├── POST       /{id}/archive
├── POST       /{id}/submit-approval
├── GET        /{id}/compare?scenarios=id1,id2
├── GET        /{id}/financial-impact
├── GET        /{id}/risk-assessment
├── GET        /{id}/execution-plan
├── GET        /{id}/what-if-analyze?params=...
└── POST       /{id}/save-as-template

/api/approval-requests/
├── GET        / (list requests)
├── POST       /{id}/approve
├── POST       /{id}/reject
└── GET        /{id} (show details)

/api/scenario-templates/
├── GET        / (list templates)
├── GET        /{id} (show)
├── POST       / (create)
├── GET        /{id}/preview
└── DELETE     /{id}
```

---

## 6. Forms & Form Requests

### Form Requests to Create

1. **CreateScenarioRequest** (update from Phase 1)
    - name, description, category, target_date
    - validation rules

2. **UpdateScenarioRequest** (extend)
    - Same as create + additional fields

3. **ApprovalMatrixRequest**
    - department_id, approvers[], required_signatures
    - validation

4. **TemplateRequest**
    - name, description, category, structure
    - validation

---

## 7. Implementation Sequence (Daily Breakdown)

### Week 1 (Apr 1-5) - Phase 1: Analytics

| Day         | Focus                   | Deliverables                                          |
| ----------- | ----------------------- | ----------------------------------------------------- |
| **Apr 1**   | Kickoff + Backend setup | ScenarioAnalyticsService, models, migrations          |
| **Apr 2-3** | Backend services        | Financial impact, risk, skill gap calculations        |
| **Apr 4-5** | Frontend components     | ScenarioComparison, ScenarioTimeline, ScenarioMetrics |

### Week 2 (Apr 8-12) - Phase 2: Workflows

| Day           | Focus               | Deliverables                                    |
| ------------- | ------------------- | ----------------------------------------------- |
| **Apr 8-9**   | Workflow engine     | ScenarioWorkflowService, ApprovalRequest models |
| **Apr 10-11** | Frontend workflows  | ApprovalFlow, ExecutionPlan, NotificationCenter |
| **Apr 12**    | Integration + tests | API endpoints, tests, bug fixes                 |

### Week 3 (Apr 15-19) - Phase 3: Tools

| Day           | Focus               | Deliverables                                        |
| ------------- | ------------------- | --------------------------------------------------- |
| **Apr 15-16** | Templates + Wizard  | ScenarioBuilder component, TemplateService          |
| **Apr 17-18** | Visualization       | OrgChartOverlay, ExecutiveDashboard, WhatIfAnalyzer |
| **Apr 19**    | Integration + tests | Landing page, side navigation, full test suite      |

### Week 4 (Apr 22-25) - Finalization

| Day        | Focus                   | Deliverables                                 |
| ---------- | ----------------------- | -------------------------------------------- |
| **Apr 22** | Code review + polish    | Refactor, optimization, cleanup              |
| **Apr 23** | Build verification      | npm run build, auto-fixes                    |
| **Apr 24** | Comprehensive testing   | php artisan test --compact                   |
| **Apr 25** | Documentation + release | Execution summary, v0.4.0 tag, merge to main |

---

## 8. Success Metrics

### Code Quality

- ✅ 0 build errors, 0 TypeScript errors
- ✅ All tests passing (50-70 tests)
- ✅ Code coverage >80% for business logic
- ✅ Pest v4 + Feature tests for workflows

### Performance

- ✅ Scenario comparison: <2 second response
- ✅ Financial impact calc: <1 second sub-query
- ✅ Bundle size increase <15% over Task 1

### User Experience

- ✅ Wizard completion: <5 minutes per scenario
- ✅ Approval workflow: <1 click to approve/reject
- ✅ Comparison view: side-by-side rendering complete

### Documentation

- ✅ Execution summary (300+ LOC markdown)
- ✅ openmemory.md updated with components
- ✅ API documentation accurate

---

## 9. Risk & Mitigation

| Risk                                 | Severity | Mitigation                                |
| ------------------------------------ | -------- | ----------------------------------------- |
| Scope creep (new features requested) | HIGH     | Frozen scope after Apr 1, defer to Task 3 |
| Performance degradation              | MEDIUM   | Optimize queries, add DB indexes early    |
| Approval workflow complexity         | MEDIUM   | Use existing alert patterns, simplify     |
| Testing time overruns                | MEDIUM   | Start tests early, target 70% on Apr 20   |

---

## 10. Resources & Dependencies

### Existing Infrastructure (From Task 1 - Ready to Use)

- ✅ Multi-tenant scoping patterns
- ✅ Policy authorization system
- ✅ Alert system (can reuse for notifications)
- ✅ Audit trail (automatic change tracking)
- ✅ API structure & conventions
- ✅ Vue 3 Composition API setup
- ✅ Pest v4 testing framework
- ✅ Tailwind CSS v4 styling

### External Dependencies (None new)

- Laravel 12 (existing)
- Vue 3 (existing)
- Phosphor Icons (existing)
- Vite (existing)

### Team Capacities

- 1 developer (you) - 40 hours/week
- Estimated requirement: 160-200 hours total
- Timeline: 25 days = 5 weeks @ 3-4 hours/day

---

## 11. Deliverables Checklist

### Code

- [ ] Phase 1: Analytics components (6 files, ~1,200 LOC)
- [ ] Phase 2: Workflow components (8 files, ~2,200 LOC)
- [ ] Phase 3: Planning tools (7 files, ~2,300 LOC)
- [ ] Landing page integration (1 file, ~300 LOC)
- [ ] Database migrations (4 new tables)
- [ ] API endpoints (15-20 routes)
- [ ] Comprehensive tests (50-70 tests)

### Documentation

- [ ] TASK_2_PHASE_1_EXECUTION_SUMMARY.md
- [ ] TASK_2_PHASE_2_EXECUTION_SUMMARY.md
- [ ] TASK_2_PHASE_3_EXECUTION_SUMMARY.md
- [ ] openmemory.md updated
- [ ] PENDIENTES_2026_03_26.md updated

### Release

- [ ] Build verification: 0 errors
- [ ] All tests passing
- [ ] 5-6 commits to feature branch
- [ ] Merge to main
- [ ] Tag v0.4.0

---

## 12. Next Steps

1. **Apr 1, 09:00 UTC**: Kickoff Task 2 Phase 1
2. **Create database migrations** for approval system
3. **Implement ScenarioAnalyticsService** with comparison logic
4. **Build Phase 1 Vue components**
5. **Write comprehensive tests**
6. **Daily commits** with semantic messages

---

**Start Date**: Apr 1, 2026  
**Target Release**: Apr 25, 2026  
🚀 **Ready to Begin!**
