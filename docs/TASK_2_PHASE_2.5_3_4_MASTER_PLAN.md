# 🚀 Phase 2.5, 3 & 4 - Master Implementation Plan

**Status**: Ready to Execute | **Date**: Mar 27, 2026  
**Target**: ~5,000+ LOC across 3 phases | **Timeline**: 4-5 weeks  

---

## 📊 Overview & Scope

### Phase 2.5: Workflow Enhancements (3-4 days)
**Goal**: Deepen Phase 2 with notifications, multi-signature, and dashboard  
**Deliverables**: 600-700 LOC  
**Estimated LOC**:
- Notification Service (200 LOC)
- Approval Dashboard Component (250 LOC)  
- Multi-signature approval flow (150 LOC)
- Tests (100 LOC)

### Phase 3: Planning Tools & Visualization (7-8 days)
**Goal**: Scenario templates, what-if analysis, executive dashboards  
**Deliverables**: 2,300+ LOC  
**Estimated LOC**:
- Template Service & API (300 LOC)
- What-if Analysis Engine (400 LOC)
- Executive Dashboard (600 LOC)
- Org Chart Overlay (300 LOC)
- Vue Components (500 LOC)
- Tests (200 LOC)

### Phase 4: Advanced Features (7-8 days)
**Goal**: Comparison, export/import, succession templates, bulk ops  
**Deliverables**: 2,000+ LOC  
**Estimated LOC**:
- Scenario Comparison Service (350 LOC)
- Export/Import Engine (300 LOC)
- Succession Templates (200 LOC)
- Bulk Operations Service (250 LOC)
- Vue Components (600 LOC)
- Tests (250 LOC)
- Landing page "Command Center" (50 LOC)

### Totals
- **Total LOC**: ~5,000+
- **Tests**: 50+ new tests
- **API Endpoints**: 15-20 new endpoints
- **Vue Components**: 8-10 new components
- **Database Tables**: 2-3 new tables
- **Commits**: 8-10 feature commits

---

## 🎯 Phase 2.5: Workflow Enhancements (Mar 27-30)

### Features

#### 1. Notification System Integration
- **Channels**: Email + Slack + In-App
- **Events**: Approval request, Approval granted, Approval rejected, Scenario activated
- **Service**: `ScenarioNotificationService` (200 LOC)
  - `notifyApprovers(approvalRequest, approvers)`
  - `notifyRejection(approvalRequest, rejectionReason)`
  - `notifyActivation(scenario, executionPlan)`
  - `sendSlackNotification(channel, message)` - needs webhook
  - `sendEmailNotification(recipient, subject, template)`

#### 2. Multi-Approver Status Dashboard
- **Component**: `ApprovalDashboard.vue` (250 LOC)
  - Real-time approval status matrix
  - Approval chain visualization
  - Pending actions indicator
  - Charts (approval rate, avg response time)
  - Filter by status/date range
  - Export approval metrics

#### 3. Enhanced Approval Card
- **Updates to**: `ApprovalRequestCard.vue` (expand 280→350 LOC)
  - Add button to "resend approval request"
  - Add change history (if notes updated)
  - Digital signature verification badge
  - Approval duration timer (shows when pending)

#### 4. Email Templates
- **New**: `resources/views/emails/approvals/`
  - `new_approval_request.blade.php`
  - `approval_granted.blade.php`
  - `approval_rejected.blade.php`
  - `scenario_activated.blade.php`

### Database Changes
```sql
-- Already have scenarios.decision_status, execution_status
-- Add to scenarios table:
ALTER TABLE scenarios ADD COLUMN notifications_sent_at TIMESTAMP NULL;
ALTER TABLE scenarios ADD COLUMN last_notification_resent_at TIMESTAMP NULL;

-- New table: approval_notifications
CREATE TABLE approval_notifications (
    id BIGINT PRIMARY KEY,
    approval_request_id BIGINT,
    channel ENUM('email', 'slack', 'in_app'),
    recipient VARCHAR(255),
    sent_at TIMESTAMP,
    delivered_at TIMESTAMP NULL,
    opened_at TIMESTAMP NULL,
    bounced_at TIMESTAMP NULL,
    organization_id UUID
);
```

### API Endpoints (Phase 2.5)
```
GET    /api/scenarios/{id}/approval-dashboard
       Response: { approval_chain, metrics, timeline }

POST   /api/approval-requests/{id}/resend-notification
       Body: { channels: ['email', 'slack'] }
       Response: { success, sent_to: [...] }

POST   /api/approval-requests/{id}/email-preview
       Response: { html, subject, preview_text }

GET    /api/approvals-summary
       Response: { pending_count, avg_response_time, approval_rate }
```

### Tests (Phase 2.5)
- ✅ Notification service sends email correctly
- ✅ Notification service sends Slack correctly
- ✅ Dashboard calculates metrics correctly
- ✅ Resend notification works
- ✅ Multi-channel notifications
- ✅ Error handling (email fails, continues with Slack)

---

## 🎨 Phase 3: Planning Tools & Visualization (Apr 1-8)

### Features

#### 1. Scenario Templates
- **Purpose**: Save/reuse scenario configurations
- **Service**: `ScenarioTemplateService` (300 LOC)
  - `createTemplate(scenario, name, description)`
  - `getTemplates(organizationId)` → list with filtering
  - `useTemplate(templateId, newScenarioName)` → create scenario from template
  - `updateTemplate(templateId, changes)`
  - `deleteTemplate(templateId)`

#### 2. What-If Analysis Engine
- **Purpose**: Calculate impact of changes
- **Service**: `WhatIfAnalysisService` (400 LOC)
  - `analyzeHeadcountImpact(changes)` → predict hiring/reduction
  - `analyzeFinancialImpact(changes)` → costs & savings
  - `analyzeRiskImpact(changes)` → risk scores
  - `compareScenariosWithBaseline(scenarioId)` → deltas
  - `predictOutcomes(scenario)` → success probability

#### 3. Executive Summary Dashboard
- **Component**: `ExecutiveSummary.vue` (300 LOC)
  - 6-8 KPI cards (cost, risk, timeline, ROI, headcount change)
  - Executive decision indicators (red/amber/green)
  - Comparison to company baseline
  - "Ready to activate?" recommendation badge
  - Risk heatmap (2x2: likelihood vs impact)
  - Export to PDF button

#### 4. Org Chart Overlay
- **Component**: `OrgChartOverlay.vue` (200 LOC)
  - Show current org structure
  - Overlay scenario changes (planned hires/departures)
  - Highlight impact zones
  - Show successor candidates from succession planning
  - Click to drill into role details

#### 5. Scenario Comparison Component
- **Component**: `ScenarioComparison.vue` (200 LOC)
  - Side-by-side scenario comparison
  - Headcount delta by role
  - Cost delta breakdown
  - Risk comparison (traffic lights)
  - Timeline comparison
  - Recommendation (which scenario is "best"?)

### Database Changes
```sql
-- New tables
CREATE TABLE scenario_templates (
    id BIGINT PRIMARY KEY,
    organization_id UUID,
    name VARCHAR(255),
    description TEXT,
    template_data JSON,
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE what_if_analyses (
    id BIGINT PRIMARY KEY,
    organization_id UUID,
    scenario_id BIGINT,
    analysis_type ENUM('headcount', 'financial', 'risk', 'combined'),
    input_changes JSON,
    calculated_impacts JSON,
    success_probability DECIMAL(3,2),
    created_at TIMESTAMP
);

CREATE TABLE executive_summaries (
    id BIGINT PRIMARY KEY,
    organization_id UUID,
    scenario_id BIGINT,
    kpis JSON,
    decision_recommendation ENUM('proceed', 'revise', 'reject'),
    recommendation_reason TEXT,
    generated_at TIMESTAMP
);
```

### API Endpoints (Phase 3)
```
GET    /api/scenario-templates
       Response: { templates: [...], count }

POST   /api/scenario-templates
       Body: { name, description, scenario_id }
       Response: { template_id, ... }

POST   /api/scenarios/from-template/{templateId}
       Body: { scenario_name, scenario_copy? }
       Response: { scenario_id, ... }

POST   /api/scenarios/{id}/what-if-analyze
       Body: { changes: [{role, delta_count}, ...] }
       Response: { impacts: { headcount, financial, risk } }

GET    /api/scenarios/{id}/executive-summary
       Response: { kpis, decision_recommendation, risks }

GET    /api/scenarios/{id}/org-chart?include_scenario_overlay=true
       Response: { current_org, scenario_changes, successors }

POST   /api/scenarios/{id}/export-summary
       Body: { format: 'pdf'|'pptx' }
       Response: { download_url }

GET    /api/scenarios/compare?ids=1,2,3
       Response: { comparison_matrix, deltas, recommendation }
```

### Vue Components (Phase 3)
- ✅ `ExecutiveSummary.vue` (300 LOC)
- ✅ `OrgChartOverlay.vue` (200 LOC)
- ✅ `ScenarioComparison.vue` (200 LOC)
- ✅ `WhatIfAnalyzer.vue` (200 LOC - interactive calculator)
- ✅ `TemplateLibrary.vue` (150 LOC - browse & use templates)

### Tests (Phase 3)
- ✅ Template creation & retrieval
- ✅ Template duplication creates accurate copy
- ✅ What-if analysis calculations correct
- ✅ Executive summary generates accurate KPIs
- ✅ Org chart overlay shows changes correctly
- ✅ Comparison highlights deltas accurately

---

## 🚀 Phase 4: Advanced Features (Apr 9-16)

### Features

#### 1. Scenario Comparison (Advanced)
- **Service**: `ScenarioComparisonService` (350 LOC)
  - `buildComparisonMatrix(scenarioIds)` → all metrics side-by-side
  - `calculateDeltasAgainstBaseline(scenario, baseline)`
  - `rankScenarios(scenarios, weights)` → scoring algorithm
  - `generateComparisonReport(scenarioIds)` → formatted data for export

#### 2. Export/Import Engine
- **Service**: `ScenarioExportService` (300 LOC)
  - `exportScenarioToJSON(scenarioId)` → full dump
  - `exportScenarioToPDF(scenarioId, options)` → formatted report
  - `exportScenarioToPPTX(scenarioId, options)` → executive slide deck
  - `importScenarioFromJSON(jsonData)` → restore
  - `validateImport(jsonData)` → check compatibility

#### 3. Succession Planning Templates
- **Service**: `SuccessionTemplateService` (200 LOC)
  - `createSuccessionTemplate(scenario)` → from scenario
  - `applySuccessionTemplate(templateId, organizationId)` → set up succession plans
  - `generateSuccessionRecommendations(scenario)` → suggest succesors

#### 4. Bulk Operations
- **Service**: `BulkScenarioOperations` (250 LOC)
  - `bulkApproveScenarios(scenarioIds)` → admin approve multiple
  - `bulkArchiveScenarios(scenarioIds, reason)`
  - `bulkExportScenarios(scenarioIds, format)` → zip file
  - `bulkDuplicateScenarios(scenarioId, count)` → create variants

#### 5. Scenario Planning Command Center
- **Page**: `resources/js/Pages/ScenarioPlanning/CommandCenter.vue` (150 LOC)
  - 8 dashboard cards (Create, My Scenarios, Pending Approvals, Compare, Templates, Executive Summary, Analytics, Archived)
  - Quick stats (total scenarios, pending approvals, this month's changes)
  - Recent activity feed (scenarios modified, approvals, templates used)
  - Admin panel (bulk operations, templates management)

### Database Changes
```sql
-- New tables
CREATE TABLE scenario_exports (
    id BIGINT PRIMARY KEY,
    organization_id UUID,
    scenario_id BIGINT,
    export_type ENUM('json', 'pdf', 'pptx'),
    file_path VARCHAR(500),
    exported_by BIGINT,
    exported_at TIMESTAMP,
    expires_at TIMESTAMP
);

CREATE TABLE scenario_bulk_operations (
    id BIGINT PRIMARY KEY,
    organization_id UUID,
    operation_type ENUM('execute', 'export', 'archive', 'duplicate'),
    scenario_ids JSON,
    status ENUM('pending', 'processing', 'completed', 'failed'),
    result_data JSON,
    scheduled_at TIMESTAMP,
    completed_at TIMESTAMP
);

CREATE TABLE succession_templates (
    id BIGINT PRIMARY KEY,
    organization_id UUID,
    name VARCHAR(255),
    description TEXT,
    template_config JSON,
    created_by BIGINT,
    created_at TIMESTAMP
);
```

### API Endpoints (Phase 4)
```
GET    /api/scenarios/compare/matrix?ids=1,2,3
       Response: { comparison_matrix, rankings, recommendation }

POST   /api/scenarios/{id}/export
       Body: { format: 'json'|'pdf'|'pptx', include_data: [...] }
       Response: { download_url, expires_at }

POST   /api/scenarios/{id}/import
       Body: FormData with JSON file
       Response: { success, new_scenario_id, validation_errors? }

POST   /api/scenarios/bulk-approve
       Body: { scenario_ids: [...] }
       Response: { updated_count, failed: [...] }

POST   /api/scenarios/bulk-archive
       Body: { scenario_ids: [...], reason: string }
       Response: { archived_count }

POST   /api/scenarios/bulk-export
       Body: { scenario_ids: [...], format: 'zip' }
       Response: { download_url }

GET    /api/scenarios/command-center
       Response: { stats, recent_activity, pending_actions }

POST   /api/succession-templates/{id}/apply
       Body: { organization_id }
       Response: { succession_plans_created: [...] }

POST   /api/scenarios/{id}/duplicate
       Body: { count: 3 }
       Response: { duplicates: [...] }
```

### Vue Components (Phase 4)
- ✅ `ScenarioComparisonMatrix.vue` (250 LOC)
- ✅ `ExportImportDialog.vue` (200 LOC)
- ✅ `BulkOperationsPanel.vue` (200 LOC)
- ✅ `CommandCenter.vue` (150 LOC)

### Tests (Phase 4)
- ✅ Comparison matrix accuracy
- ✅ Export formats (JSON/PDF/PPTX)
- ✅ Import validation works
- ✅ Bulk operations execute correctly
- ✅ Succession templates apply correctly
- ✅ Duplicate scenarios create accurate copies

---

## 📅 Implementation Timeline

### Week 1: Phase 2.5 (Mar 27-30)
- **Day 1 (Mar 27)**: 
  - [ ] Create `ScenarioNotificationService.php`
  - [ ] Create email templates
  - [ ] Add tests
- **Day 2 (Mar 28)**:
  - [ ] Build `ApprovalDashboard.vue` component
  - [ ] API endpoints for notifications
- **Day 3 (Mar 29)**:
  - [ ] Enhance `ApprovalRequestCard.vue`
  - [ ] Integration with landing page
- **Day 4 (Mar 30)**:
  - [ ] Build verification
  - [ ] Commit Phase 2.5

### Week 2-3: Phase 3 (Apr 1-8)
- **Days 1-2**: Template system
- **Days 3-4**: What-if analysis engine
- **Days 5-6**: Executive dashboard
- **Days 7-8**: Org chart overlay

### Week 4-5: Phase 4 (Apr 9-16)
- **Days 1-2**: Comparison engine
- **Days 3-4**: Export/Import
- **Days 5-6**: Succession templates & bulk ops
- **Days 7-8**: Command Center

---

## 🔧 Technical Decisions

### 1. Notification Infrastructure
- **Use existing**: `VerificationNotificationService` pattern (already in codebase)
- **Add to config**: `config/scenario-notifications.php`
- **Queue jobs**: For async email/Slack delivery
- **Multi-tenant**: All notifications scoped by organization_id

### 2. What-If Analysis Algorithm
- **Headcount impact**: Linear interpolation based on role requirements
- **Financial impact**: Role salary × headcount_delta + training costs
- **Risk impact**: Historical regression model based on past scenarios
- **Success probability**: Weighted scoring (team readiness, budget, timeline)

### 3. Export Formats
- **JSON**: Raw data + metadata (for import/reuse)
- **PDF**: Using `barryvdh/laravel-dompdf` (already in composer.json)
- **PPTX**: Using `phpoffice/phpword` (need to add)

### 4. Component Organization
```
resources/js/components/ScenarioPlanning/
├── Phase2.5/
│   ├── ApprovalDashboard.vue (enhanced)
│   ├── ApprovalRequestCard.vue (enhanced)
│   └── NotificationPreferences.vue
├── Phase3/
│   ├── ExecutiveSummary.vue
│   ├── OrgChartOverlay.vue
│   ├── ScenarioComparison.vue
│   ├── WhatIfAnalyzer.vue
│   └── TemplateLibrary.vue
└── Phase4/
    ├── BulkOperationsPanel.vue
    ├── ExportImportDialog.vue
    └── ComparisonMatrix.vue
```

---

## 📊 Progress Tracking

### Phase 2.5: ▓▓▓▓▓░░░░ (Ready to start)
### Phase 3: ░░░░░░░░░░ (0%)
### Phase 4: ░░░░░░░░░░ (0%)

### Git Commits Plan
1. Phase 2.5: Notification system + dashboard (feat/phase-2.5-notifications)
2. Phase 3: Templates + What-if + Executive dashboard (feat/phase-3-planning-tools)
3. Phase 3: Org chart + Comparison (feat/phase-3-visualization)
4. Phase 4: Export/Import + Bulk operations (feat/phase-4-advanced-1)
5. Phase 4: Succession + Command Center (feat/phase-4-advanced-2)

---

## 🎯 Success Criteria

- ✅ All 3 phases complete with 0 build errors
- ✅ 50+ new tests passing
- ✅ 15-20 new API endpoints functional
- ✅ 8-10 new Vue components with dark mode
- ✅ Integrated into landing page command center
- ✅ Multi-tenant security throughout
- ✅ Performance: API responses < 500ms
- ✅ Full TypeScript type safety
- ✅ Comprehensive documentation

---

## 🚀 Ready to Execute

All planning complete. Ready to implement Phase 2.5, 3, and 4 starting **NOW**.

**Next Step**: Begin Phase 2.5 implementation (Notification system)

