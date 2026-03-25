# 📖 Narrative Testing Strategy – Stratos

> **Objetivo:** Implementar testing en 3 capas (Unit → Integration → User Journey) con **personas, historias y casos de uso como vehículos** para validar no solo features, sino **outcomes del usuario** en contexto real.

---

## 🎭 1. FRAMEWORK: User Personas → Stories → Test Scenarios

### Por qué Narrativas?

**Hoy (técnico, ❌ limitado):**
```php
test('applies_lms_sync_rule_when_condition_matches', fn() => {
    $rule = ['condition' => 'new_course', 'action' => 'sync'];
    expect(applyRule($rule))->toBeTrue();
});
```

**Futuro (narrativo, ✅ orientado al usuario):**
```gherkin
Scenario: L&D Manager syncs a new course to all staff
  Given L&D Manager "María" has 50-100 staff in HR department
  And María configures LMS sync rule for "New Course → All Staff"
  When a new course is added in Cornerstone LMS
  Then all 50-100 staff should see it in-app within 5 minutes
  And María confirms it worked without manual intervention
```

**Ventajas de narración:**
- 🎯 **Alignment:** Devs entienden el problema del usuario, no solo el technical spec
- 🚀 **Pre-GA bugs caught:** "5min SLA" → descubrimos race conditions
- 📚 **Living documentation:** Test = customer journey doc para soporte
- 📊 **NPS predictor:** Si todo test pasa → NPS ≥ 8 probable
- 🔄 **Traceability:** Feature → Persona → Story → Test → Post-GA support

---

## 👥 2. PERSONAS OPERACIONALES (Testing Focus)

### Persona 1: L&D Manager (María)
- **Rol:** Learning & Development Manager, 50-200 staff
- **Objetivo principal:** "Lanzar programas de learning a 100+ personas sin fricción"
- **Dolores:**
  - LMS externo no sincroniza con Stratos → desfase de datos
  - Debe avisar manualmente a su equipo para revisar contenido nuevo
  - Si falla la sync, ¿cómo sabe? (no hay visibilidad)
- **Contexto:**
  - Usa Cornerstone (o SAP) para LMS, y Stratos para inteligencia de talento
  - Trabaja 8-10h diarias, necesita soluciones "fire-and-forget"
  - Éxito = sus 100 staff vean el contenido en 5 minutos

| Story | Criticalidad | SLA | Metric |
|:------|:------------|:----|:-------|
| Sync new course to all staff | P0 | <5min | Latency + 0 failures |
| Bulk assign learning paths | P1 | <1h | 100% assignment rate |
| See sync history & errors | P2 | <24h | Error log completeness |

---

### Persona 2: CHRO (Chief HR Officer)
- **Rol:** C-Suite, strategic HR leader, 5K+ employees
- **Objetivo principal:** "KPIs de adopción/talento en 1 click — no noise"
- **Dolores:**
  - Demasiados dashboards (Excel + HR system + training platform)
  - Decisiones sin data (ej. "¿adoptó realmente el learning?")
  - Anomalías no detectadas hasta quarter-end (too late)
- **Contexto:**
  - Dashboard semanal de 15 min antes de exec meeting
  - Necesita "signal-to-noise" alto (máximo 3 KPIs clave)
  - Responde a board sobre ROI de learning/talent programs

| Story | Criticalidad | SLA | Metric |
|:------|:------------|:----|:-------|
| See adoption KPIs by dept + trend | P0 | <2s load | Accuracy + latency |
| Get alerted to anomalies (auto) | P1 | <1h alert | True positive rate ≥ 90% |
| Drill into problematic dept | P2 | <5s drill | Data completeness |

---

### Persona 3: Talent Ops Lead
- **Rol:** Operational HR, 500-1K staff, managing workflows
- **Objetivo principal:** "Configurar reglas + monitorear ejecución sin código"
- **Dolores:**
  - Crear rules es tedious (no UI amigable)
  - Execution errors → lost emails/records (silent failures)
  - No hay audit trail (compliance risk)
- **Contexto:**
  - Power user (but non-technical)
  - Works with IT + HRIS + Learning teams
  - 20-30% of day = rule management + troubleshooting

| Story | Criticalidad | SLA | Metric |
|:------|:------------|:----|:-------|
| Create messaging rule with UI wizard | P0 | <10min setup | Config success rate ≥ 95% |
| See execution log (all events) | P1 | <1s search | Log completeness 100% |
| Retry failed delivery | P2 | <5min retry | Retry success ≥ 85% |

---

### Persona 4: People Manager (Local Leader)
- **Rol:** Direct manager, 10-50 reports
- **Objetivo principal:** "Prepare my team for next quarter — identify gaps, quick"
- **Dolores:**
  - Doesn't know who's ready for promotion vs. needs upskilling
  - Conversations with reports = data-less ("just a feeling")
  - Training recommendations feel generic, not tailored
- **Contexto:**
  - Uses Stratos 2-3x/week (before 1:1s, reviews)
  - Needs data in <15 sec or won't use

| Story | Criticalidad | SLA | Metric |
|:------|:------------|:----|:-------|
| See team skill gaps vs. role | P0 | <2s summary | Data freshness ≤ 1h |
| Recommend next steps per person | P1 | <5s recommendation | Recommendation relevance ≥ 80% |
| Share insight with report | P2 | <1min export | Export format flexibility |

---

### Persona 5: IT/Security Ops
- **Rol:** System admin, compliance officer
- **Objetivo principal:** "Data is secure, audit trail is complete, SLAs met"
- **Dolores:**
  - Compliance questions ("where did this data go?")
  - Integrations fail silently
  - No rate limit protection (DDoS risk)
- **Contexto:**
  - Nighttime troubleshooting (on-call)
  - Audit queries once/quarter
  - Security = non-negotiable

| Story | Criticalidad | SLA | Metric |
|:------|:------------|:----|:-------|
| View complete audit log (encrypted) | P0 | <5s query | Log size ≤ 10s to retrieve |
| Alert on integration failures | P0 | <5min alert | Accuracy of alert ≥ 98% |
| Rate limit + circuit breaker working | P0 | 24/7 monitoring | Uptime ≥ 99.9% (SLA verified) |

---

## 📖 3. USER JOURNEY STORIES (Per Persona)

### Story 1: María (L&D Manager) – "Launch new compliance course in 5 min"

**Context:**
- María is in weekly L&D team meeting at 9am
- New compliance course just published in Cornerstone LMS
- She needs all 100 HR staff to see it by EOD (same day)

**Journey:**
1. **Setup (Pre-GA, one-time):** María configured an LMS sync rule: `IF course_category=Compliance → sync to Stratos THEN notify_team_lead`
2. **Trigger (Morning):** Course appears in Cornerstone
3. **Auto-sync:** Stratos rule fires automatically (no actions from María)
4. **Verification (5-10min):** 
   - María opens Stratos Intelligence Hub
   - Sees "New courses: 1" badge
   - Clicks to view: "Compliance 2024 - 100 staff assigned"
   - Dashboard shows: `Status: ✅ Synced at 09:07am | 100/100 staff visible`
5. **Confirmation Email (auto):**
   - HR team lead gets: "New course synced: Compliance 2024 (100 people)"
6. **Result:** 100% adoption visible within 5 minutes, zero manual intervention needed

**Success Criteria (Test Assertions):**
``` gherkin
Given María has 100 staff in Stratos
And Cornerstone LMS has "Compliance 2024" course (category: Compliance)
And María's LMS sync rule: "category=Compliance → sync + notify"

When the course is published in Cornerstone
Then within 5 minutes:
  ✅ Stratos shows "Compliance 2024" in Intelligence digest
  ✅ All 100 staff are assigned (with assignment_method = 'lms_sync')
  ✅ Dashboard counter increments: "New courses this week: 1"
  ✅ HR team lead receives notification email
  ✅ Audit log records: event_type=lms_sync, timestamp, course_id, count=100

And María opens Intelligence Hub:
  ✅ Sees course in "Latest Courses" section
  ✅ Clicks course → view shows 100/100 staff
  ✅ Delivery status = "✅ Synced"
```

**Related Tests (Pest/Vitest/Playwright):**
- **Unit:** `LMSSyncRuleEngine.apply()` — rule matching logic
- **Integration:** `LMSSyncJobTest` — job processes webhook → DB update
- **E2E:** `playwright/stories/l2d-manager-lms-flow.spec.ts`

---

### Story 2: CHRO (Chief HR Officer) – "Board meeting: talent KPIs in 2 seconds"

**Context:**
- CHRO has 5 min before exec meeting
- Board will ask: "What % of leadership team is ready for promotion?"
- She needs dashboard to load + answer in <2s

**Journey:**
1. **Monday 08:00am:** CHRO logs into Stratos
2. **Click "Dashboard":** Lands on Talent Intelligence dashboard
3. **First glance (2-5s):** Sees:
   - Top KPI 1: "Leadership Ready Now: 68% (Target: 75%)"
   - Top KPI 2: "Adoption of learning programs: 82% (↑5% last week)"
   - Top KPI 3: "Turnover risk (high): 3 people flagged 🚨"
4. **Drill (if needed):** Clicks "3 people flagged" → sees names + risk factors + recommended actions
5. **Board:** CHRO confidently answers: "We're at 68% leadership readiness; 3 names flagged for immediate support"
6. **Post-board:** CHRO forwards dashboard to CEO + COO (auto-generated PDF or share link)

**Success Criteria (Test Assertions):**
``` gherkin
Given CHRO dashboard is configured with 3 KPIs (leadership readiness, adoption, turnover risk)
And data is fresh (< 1 hour old)

When CHRO loads the dashboard:
  ✅ Page loads in < 2 seconds (LCP ≤ 1.8s, FCP ≤ 1.0s, CLS ≤ 0.1)
  ✅ 3 KPIs are visible above the fold
  ✅ Leadership readiness shows 68% (accurate to source data ± 1%)
  ✅ Adoption shows 82% (correct calculation of completion % )
  ✅ "3 people flagged" is a clickable link

When CHRO clicks "3 people flagged":
  ✅ Drawer opens in < 500ms (smooth animation)
  ✅ Shows list: Name | Risk Level | Root Cause | Recommended Action
  ✅ Data is sortable/filterable
  ✅ "Export to PDF" generates shareable report

When CHRO shares the dashboard:
  ✅ Shared link remains valid for 7 days
  ✅ Recipient sees same data (no auth required if link-based)
```

**Related Tests:**
- **Unit:** `KPICalculator.calculateLeadershipReadiness()` — algorithm correctness
- **Integration:** `DashboardMetricsTest` — aggregates from talent pipeline
- **E2E:** `playwright/stories/chro-dashboard-load.spec.ts` (performance + accuracy)

---

### Story 3: Talent Ops — "Create messaging rule (no code, full audit trail)"

**Context:**
- Talent Ops needs to: "When person gets promoted, send message to manager + person"
- Must be configurable in UI wizard
- Must log all executions for audit

**Journey:**
1. **Monday 10am:** Talent Ops opens Rules → "Create new messaging rule"
2. **Wizard Step 1 - Trigger:** Select "Person promoted"
3. **Wizard Step 2 - Conditions:** Add filter "Promoted to Manager role"
4. **Wizard Step 3 - Messaging:** 
   - Channel: Email + In-App
   - To: Person + Direct Manager
   - Template: "Congratulations! You've been promoted to {{promoted_role}}"
5. **Wizard Step 4 - Review & Test:** 
   - See preview (actual message)
   - Run test with 2 sample people → see mock emails
6. **Save:** Rule is now ACTIVE
7. **Monitoring:** 
   - Execution log shows each time someone gets promoted
   - If delivery fails → shows error + retry option

**Success Criteria (Test Assertions):**
``` gherkin
Given Talent Ops has Manager role and Rule-Create permission

When Talent Ops creates messaging rule:
  Step 1: Selects trigger "Person promoted"
    ✅ Trigger is saved
  Step 2: Adds condition "role_id in [5,6,7]"
    ✅ Condition is validated (role IDs exist) and saved
  Step 3: Fills messaging template
    ✅ Template variables {{promoted_role}} are highlighted
    ✅ Preview shows: "Congratulations! You've been promoted to Director of Sales"
  Step 4: Runs test send to 2 people
    ✅ Mock email is sent (not actually emailed in test env)
    ✅ Log shows: event_type=test_rule, recipient_count=2, status=success
  Save button clicked:
    ✅ Rule is persisted with status=active
    ✅ Audit log records: user=talent_ops, action=create_rule, rule_id=##, timestamp

When a person is promoted (post-rule creation):
  ✅ Rule engine detects the promotion (via DB trigger or scheduled job)
  ✅ Matching conditions are checked (role_id matches)
  ✅ Message is rendered: "Congratulations! You've been promoted to {{actual_role}}"
  ✅ Email + in-app notification are sent
  ✅ Execution log entry: event_type=rule_executed, rule_id, person_id, channel, status, timestamp

When Talent Ops reviews execution log:
  ✅ Filters by "rule_id" show all 10 executions this week
  ✅ Each row shows: person_name | issued_at | channel | status | error_msg (if failed)
  ✅ Failed deliveries have "Retry" button
  ✅ Log is tamper-proof (immutable, encrypted)
```

**Related Tests:**
- **Unit:** `RuleEngine.evaluateConditions()` — trigger matching
- **Integration:** `MessagingRuleTest` — full execution flow
- **E2E:** `playwright/stories/talentops-create-rule.spec.ts` (UI wizard + confirmation)

---

### Story 4: People Manager— "Know your team's gaps in < 15 seconds"

**Context:**
- Manager is before 1:1 meeting
- Needs to know: "Who is under-skilled? Ready for promo?"
- Decision-making, not deep analysis

**Journey:**
1. **Monday 14:45pm (before 1:1s):** Manager opens Mi Stratos
2. **Clicks "My Team":** Sees list of 12 direct reports
3. **Quick scan (5-10s):**
   - Employee A: ⭐️⭐️⭐️⭐️⭐️ 95% ready | No gaps | ✅ Ready for promotion
   - Employee B: ⭐️⭐️⭐️ 75% ready | 2 gaps: Leadership, Strategic Thinking | 🔄 In progress (2 courses)
   - Employee C: ⭐️⭐️ 50% ready | 5 gaps | 🚨 High attention needed
4. **Click on Employee B:** Expanded card shows:
   - Gap 1: Leadership (current 2/5 → target 4/5) | Recommended: Internal coaching
   - Gap 2: Strategic Thinking (current 2/5 → target 4/5) | Recommended: MBA-style course
5. **1:1 preparation:** Manager click "Generate talking points for Employee B"
   - Generates: "Strengths: XYZ | Development priorities: ABC | Recommended path: DEF"
6. **During 1:1 with Employee B:**
   - Manager shares: "I see you're at 75% readiness. Let's work on leadership + strategic thinking"
   - Employee sees the recommended path and agrees to 2 courses + mentoring
7. **Post-meeting:** Manager clicks "Share with Employee" → sends summary

**Success Criteria (Test Assertions):**
``` gherkin
Given Manager has 12 direct reports
And each person has a role + skill gap data (fresh ≤ 1h)

When Manager opens "My Team":
  ✅ Loads in < 2 seconds
  ✅ Shows grid of 12 cards (or table)
  ✅ Each card shows: Name | Star rating (readiness %) | Status badge | Quick gap count

When Manager scans 5 cards in 10 seconds:
  ✅ Understands readiness at a glance (star rating is obvious)
  ✅ Identifies high attention cases (🚨 badges for <60% readiness)
  ✅ Knows who's ready for promo (⭐⭐⭐⭐⭐ + Ready badge)

When Manager clicks on Employee B card:
  ✅ Expands in <300ms (smooth animation)
  ✅ Shows 2 top gaps + current vs target level
  ✅ Shows recommended learning path (2-3 resources)
  ✅ "Generate talking points" button is visible

When Manager clicks "Generate talking points":
  ✅ AI generates summary < 2s
  ✅ Summary includes: strengths (2-3), priorities (2-3), next steps (1-2)
  ✅ Manager can edit/customize before sharing

When Manager clicks "Share with Employee":
  ✅ Email is sent to employee's email
  ✅ Email includes: summary + links to resources + mutual action items
  ✅ Employee receives in < 5 min
```

**Related Tests:**
- **Unit:** `TeamGapAggregator.calculateTeamReadiness()` — aggregate calculation
- **Integration:** `PeopleManagerAPITest` — fetch team + calculate readiness
- **E2E:** `playwright/stories/manager-team-dashboard.spec.ts` (loading + interactions)

---

### Story 5: IT/Security Ops — "Audit trail complete, integrations healthy"

**Context:**
- Quarterly compliance audit: "Show me all data movements last Q"
- Integration with Cornerstone LMS failed yesterday: "Fix it + prove it's monitored"

**Journey:**
1. **Scenario A - Audit Trail (Quarterly):**
   - Auditor logs in with read-only "Auditor" role
   - Clicks "Compliance Audit" → "Data movements"
   - Applies filter: "Date range: Q4 2025 | Entity: People | Action: all"
   - Result: Paginated log of all people-related changes (create, update, delete, export)
   - Each row: timestamp | user | action | old_value | new_value | ip_address | mfa_verified
   - Exports as signed PDF (cryptographically verified)
   
2. **Scenario B - Integration Failed Yesterday:**
   - Ops opens "Monitoring" → "Integrations" → "Cornerstone LMS"
   - Status shows: 🔴 ERROR (last 24 hours)
   - Alert generated at 11:23pm: "LMS webhook failed (3 retries, then stopped)"
   - Ops clicks "View latest attempt" → sees:
     - Request: POST /api/lms-webhook | Payload: {...synced_course...}
     - Response: 503 Service Unavailable
     - Retry queue: 7 items pending
   - Ops clicks "Manual retry" → system tries again → Success! 🟢
   - Alert sent to Ops + CTO: "Integration recovered"
   - Audit log records: `event_type=manual_retry, integration=cornerstone, status=success`

**Success Criteria (Test Assertions):**
``` gherkin
Given audit data from Q4 2025
And Cornerstone integration has 7 failed events

When Auditor filters: "Date range: Q4 | Entity: People"
  ✅ Returns all events (estimated 10K+ rows, paginated)
  ✅ Each event has: timestamp, user, action, old/new value, ip, mfa_status
  ✅ Can be exported to signed PDF in < 30s
  ✅ PDF is cryptographically verified (matches hash in compliance_exports table)

When Ops opens Integration Health:
  ✅ Cornerstone LMS shows status 🔴 with reason
  ✅ Last alert: "LMS webhook failed (11:23pm) + time since alert"
  ✅ Failed event count: 7
  ✅ "View details" shows request/response details

When Ops clicks "Manual retry":
  ✅ Each of 7 pending events retries sequentially
  ✅ Success: event marked retried_at + status=success
  ✅ Failure: logged with new error, moved to DLQ
  ✅ Alert fired: integration recovered (sent to on-call ops)
  ✅ Audit log: user=ops, action=manual_retry, integration=cornerstone, count=7, status=success

When Auditor runs compliance query next month:
  ✅ Manual retry event is logged (immutable proof of remediation)
```

**Related Tests:**
- **Unit:** `AuditLogGenerator.formatForExport()` — log formatting correctness
- **Integration:** `IntegrationHealthTest` — webhook failure handling
- **E2E + Security:** `playwright/stories/ops-audit-trail.spec.ts` + `security-tests/audit-immutability.spec.ts`

---

## 🏗️ 4. TEST LAYER MAPPING

### Layer 1: Unit Tests (Persona Goals → Component Logic)

**Example: L&D Manager → LMS sync latency < 5min**

```php
// tests/Unit/Services/LMSSyncEngineTest.php
test('lms_sync_evaluates_rules_and_queues_job_within_100ms', function () {
    $rule = LMSSyncRule::factory()->create([
        'condition' => json_encode(['category' => 'Compliance']),
        'sync_batch_size' => 100,
        'sync_batch_window_ms' => 500,
    ]);
    $course = ['id' => 1, 'category' => 'Compliance', 'name' => 'Compliance 2024'];
    
    $startTime = now();
    $engine = new LMSSyncEngine();
    $jobDispatched = $engine->evaluate($course, $rule);
    $endTime = now();
    
    expect($jobDispatched)->toBeTrue();
    expect($endTime->diffInMilliseconds($startTime))->toBeLessThan(100); // Unit test, mocked DB
});
```

### Layer 2: Integration Tests (Persona Story → API + Job Execution)

```php
// tests/Feature/LMSSyncJobTest.php
test('lms_sync_job_syncs_100_staff_within_5minutes', function () {
    $org = Organization::factory()->create();
    $staff = People::factory()->count(100)->create(['organization_id' => $org->id]);
    $course = Course::factory()->create(['external_id' => 'CORNERSTONE-123', 'category' => 'Compliance']);
    
    $startTime = now();
    
    // Dispatch job (simulates LMS webhook)
    dispatch(new LMSSyncJob($org->id, $course->id, 100));
    
    // Process the job synchronously in test
    Bus::assertDispatched(LMSSyncJob::class);
    
    // Verify results
    expect(CourseAssignment::where('course_id', $course->id)
        ->where('organization_id', $org->id)
        ->count()
    )->toBe(100);
    
    $endTime = now();
    expect($endTime->diffInMinutes($startTime))->toBeLessThan(5); // 5-min SLA
    
    // Verify audit log
    expect(AuditLog::where('event_type', 'lms_sync')
        ->where('course_id', $course->id)
        ->first()
    )->not->toBeNull();
});
```

### Layer 3: E2E / User Journey Tests (Full Story → UI + API)

```typescript
// tests/e2e/stories/lms-manager-sync-workflow.spec.ts
test('L&D Manager sees synced course within 5 minutes (user perspective)', async ({ page, browser }) => {
  // 1. Setup: Create LMS sync rule
  await page.goto('/rules/lms-sync');
  await page.click('text=Create Rule');
  await page.selectOption('select[name="trigger"]', 'new_course');
  await page.fill('input[name="condition"]', 'category=Compliance');
  await page.click('text=Save Rule');
  await expect(page.locator('text=Rule created successfully')).toBeVisible();

  // 2. Simulate LMS webhook (backend call)
  const coursePayload = {
    external_id: 'CORNERSTONE-123',
    name: 'Compliance 2024',
    category: 'Compliance',
  };
  const response = await page.request.post('/api/webhooks/lms-sync', {
    data: coursePayload,
  });
  expect(response.ok()).toBe(true);

  // 3. Wait & verify: Dashboard shows synced course
  await page.goto('/intelligence/hub');
  const courseCard = page.locator('text=Compliance 2024');
  await expect(courseCard).toBeVisible({ timeout: 5 * 60 * 1000 }); // 5 min timeout

  // 4. Verify details
  await courseCard.click();
  const statusBadge = page.locator('text=✅ Synced');
  await expect(statusBadge).toBeVisible();
  
  const staffCount = page.locator('text=/100\\/100 staff/');
  await expect(staffCount).toBeVisible();

  // 5. Verify email was sent (API check)
  const emailJob = await page.request.get('/api/test/emails/latest');
  const emailData = await emailJob.json();
  expect(emailData.subject).toContain('New course synced');
});
```

---

## 📊 5. COVERAGE MATRIX BY PERSONA

| Persona | Layer 1 (Unit) | Layer 2 (Integration) | Layer 3 (E2E) | SLA | Owner |
|:--------|:------|:-----|:------|:---|:------|
| **L&D Manager** | LMSSyncEngine tests (3) | LMSSyncJob full workflow (1) | Dashboard + sync E2E (1) | <5min | Backend Lead + QA |
| **CHRO** | KPICalculator tests (3) | Dashboard metrics aggregation (1) | Dashboard load perf + accuracy (1) | <2s | Analytics + QA |
| **Talent Ops** | RuleEngine logic (5) | Rule execution full stack (2) | Rule wizard UI + audit log (1) | <10min setup | Backend Lead + QA |
| **People Manager** | Gap calculator (3) | Team data fetch + aggregate (1) | Manager dashboard interactions (1) | <2s load | Backend Lead + QA |
| **IT/Ops** | AuditLog formatting (2) | Webhook retry + circuit breaker (1) | Audit trail export + monitoring (1) | <30s export | DevOps + QA |

**Total Tests by Layer:**
- 🟢 **Unit:** 16 tests (mocked, fast, <5s total)
- 🟡 **Integration:** 6 tests (real DB, <2min total)
- 🔴 **E2E:** 5 tests (full stack, <10min total)

**Total Coverage:** ~27 critical user journeys, covering 5 personas + 15 stories

---

## 🚀 6. IMPLEMENTATION ROADMAP

### Phase 1: Spike (Week 2026-04-15, 48 hours)
- [ ] Define 2-3 core personas (L&D Manager + CHRO)
- [ ] Write 2 Gherkin stories (LMS sync + Dashboard)
- [ ] Build proof-of-concept: 1 Unit + 1 Integration + 1 E2E test
- [ ] Document approach in this file

### Phase 2: MVP Narrative Tests (Sprint C, 2026-04-26, 1 week)
- [ ] All 5 personas fully defined (this section)
- [ ] 10-15 core stories written (Gherkin format)
- [ ] Tests implemented: 16 Unit + 6 Integration + 5 E2E
- [ ] CI/CD integration: Narrative tests run in every PR
- [ ] Dashboard: % passing tests per persona (visual + KPI)

### Phase 3: Scale (Sprint D-E, ongoing)
- [ ] Expand to 20+ stories (edge cases, error scenarios)
- [ ] Integrate coverage metrics (Clover + LCOV)
- [ ] Post-GA: Use test results for NPS validation

---

## 📚 7. FILE STRUCTURE IN REPO

```
tests/
├── Narrative/
│   ├── personas.json                          # JSON: all 5 personas + metadata
│   ├── stories/
│   │   ├── lms-manager-sync-course.gherkin
│   │   ├── chro-dashboard-kpis.gherkin
│   │   ├── talentops-create-rule.gherkin
│   │   ├── manager-team-gaps.gherkin
│   │   └── ops-audit-trail.gherkin
│   ├── fixtures/                              # Test data per persona
│   │   ├── l2d-manager-org-100staff.json
│   │   ├── chro-5k-employees.json
│   │   └── ...
│   └── NarrativeTestRunner.php                # Custom runner (optional)
├── Unit/
│   ├── Services/
│   │   ├── LMSSyncEngineTest.php
│   │   ├── KPICalculatorTest.php
│   │   ├── RuleEngineTest.php
│   │   └── ...
├── Feature/
│   ├── Api/
│   │   ├── LMSSyncJobTest.php
│   │   ├── DashboardMetricsTest.php
│   │   ├── MessagingRuleTest.php
│   │   └── ...
├── Browser/ (or E2E/)
    ├── stories/
    │   ├── lms-manager-sync-workflow.spec.ts
    │   ├── chro-dashboard-performance.spec.ts
    │   ├── talentops-create-rule.spec.ts
    │   └── ...
```

---

## ✅ 8. SUCCESS CRITERIA (By End of Sprint C)

- [ ] All 5 personas documented + reviewed by product
- [ ] 10+ user stories in Gherkin format (human-readable)
- [ ] 16 unit tests passing (>95% branch coverage for core logic)
- [ ] 6 integration tests passing (end-to-end flows work)
- [ ] 5 E2E Playwright tests passing (UI + API + performance)
- [ ] CI/CD: All tests run in <5 minutes (fast feedback)
- [ ] Dashboard: Shows # tests passing per persona + SLA status (visual indicator)
- [ ] Post-GA validation: If all tests pass → NPS prediction ≥ 8/10

---

## 📖 References

- **UX Personas Template:** `docs/UX_MODULE_TEMPLATE.md` (sections 1-3)
- **Testing Stack:** `openmemory.md` (estrategia de testing)
- **Pest docs:** Uses Pest v4 + Playwright for E2E
- **Roadmap Integration:** This strategy aligns with **Sección 34 (Narrative Testing Layer)** if added to main roadmap

---

**Last updated:** 2026-03-28  
**Owner:** QA Lead + Product Lead  
**Status:** 🟢 Ready for Phase 1 Spike (Week 2026-04-15)
