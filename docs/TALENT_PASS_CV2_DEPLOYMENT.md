# 📋 TALENT PASS (CV 2.0) - DEPLOYMENT GUIDE

**Purpose:** Complete deployment documentation for Talent Pass CV 2.0 implementation  
**Version:** v1.0 (First Release)  
**Timeline:** Mar 31 - Apr 14, 2026 (2 weeks)  
**Status:** Planning phase (awaiting Messaging production deployment completion)  

---

## 📋 PROJECT OVERVIEW

### What is Talent Pass CV 2.0?

**Talent Pass** = Digital confidence and career credentials platform that transforms how talent presents themselves to employers and orgs.

**Key Features:**
- 📄 Dynamic CV/Resume with interactive skills visualization
- 🎓 Credential wallet (certifications, courses, achievements)
- 📊 Skills graph showing competency levels and endorsements
- 🔗 Portability: Export to PDF, JSON, LinkedIn, public link
- 👥 Social proof: Endorsements from peers and managers
- 📈 Career progression timeline
- 🎯 Skills gap analysis (vs job requirements)

### Phase 1 Scope (MVP)

**In Scope (v1.0):**
- ✅ Talent Pass viewer (read-only display)
- ✅ Edit profile: skills, competencies, track record
- ✅ CV/PDF export
- ✅ Public shareable link
- ✅ Integration with workforce planning (see talent skills)
- ✅ Search by skills (global talent search)

**Out of Scope (v2.0+):**
- ❌ Endorsement system (phase 2)
- ❌ Social features (phase 2)
- ❌ Blockchain verification (postponed - cost-benefit)
- ❌ Third-party integrations (phase 3)

---

## 🏗️ ARCHITECTURE

### Database Schema

```sql
-- Core tables
CREATE TABLE talent_passes (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    organization_id BIGINT NOT NULL,
    title VARCHAR(255),
    bio TEXT,
    headline VARCHAR(500),
    public_link VARCHAR(255) UNIQUE,
    is_public BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    --
    UNIQUE(organization_id, user_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (organization_id) REFERENCES organizations(id)
);

CREATE TABLE talent_pass_skills (
    id BIGINT PRIMARY KEY,
    talent_pass_id BIGINT NOT NULL,
    skill_name VARCHAR(255),
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert'),
    endorsement_count INT DEFAULT 0,
    created_at TIMESTAMP,
    --
    FOREIGN KEY (talent_pass_id) REFERENCES talent_passes(id) ON DELETE CASCADE
);

CREATE TABLE talent_pass_credentials (
    id BIGINT PRIMARY KEY,
    talent_pass_id BIGINT NOT NULL,
    title VARCHAR(255),
    issuer VARCHAR(255),
    issue_date DATE,
    expiry_date DATE,
    credential_url VARCHAR(500),
    created_at TIMESTAMP,
    --
    FOREIGN KEY (talent_pass_id) REFERENCES talent_passes(id) ON DELETE CASCADE
);

CREATE TABLE talent_pass_experiences (
    id BIGINT PRIMARY KEY,
    talent_pass_id BIGINT NOT NULL,
    title VARCHAR(255),
    company VARCHAR(255),
    duration_start DATE,
    duration_end DATE,
    description TEXT,
    created_at TIMESTAMP,
    --
    FOREIGN KEY (talent_pass_id) REFERENCES talent_passes(id) ON DELETE CASCADE
);
```

### Laravel Models

```
app/Models/
├── TalentPass.php (main model)
├── TalentPassSkill.php (has many)
├── TalentPassCredential.php (has many)
└── TalentPassExperience.php (has many)

app/Services/
├── TalentPassService.php (business logic)
├── CVExportService.php (PDF generation)
└── TalentSearchService.php (full-text search)

app/Http/Controllers/
├── TalentPassController.php (CRUD)
└── PublicTalentPassController.php (public view)

app/Policies/
└── TalentPassPolicy.php (authorization)
```

### Frontend Components

```
resources/js/Pages/
├── TalentPass/
│   ├── Index.vue (list all talent passes)
│   ├── Show.vue (view single talent pass - detailed)
│   ├── Edit.vue (edit talent pass)
│   └── PublicView.vue (public link viewer)

resources/js/Components/
├── TalentPassViewer.vue (display read-only CV)
├── TalentPassEditor.vue (form to edit)
├── SkillsGraph.vue (visualization)
├── CredentialsSection.vue (credentials display)
└── ExperienceTimeline.vue (career timeline)
```

---

## 📅 IMPLEMENTATION TIMELINE

### Week 1: Database & Backend (Mar 31 - Apr 4)

**Day 1 (Mar 31):**
- [ ] Create migrations (tables above)
- [ ] Generate models with factories
- [ ] Run migrations on staging
- **Time:** 2 hours
- **PR:** feature/talent-pass-database

**Day 2-3 (Apr 1-2):**
- [ ] Implement TalentPassService (CRUD business logic)
- [ ] Implement CVExportService (PDF generation via Dompdf/TCPDF)
- [ ] Implement TalentSearchService (full-text search)
- [ ] Add authorization policies
- **Time:** 4 hours
- **PR:** feature/talent-pass-services

**Day 4-5 (Apr 3-4):**
- [ ] Build API controllers (REST endpoints)
- [ ] Add Form Request validation
- [ ] Write 80+ tests (unit + feature)
- [ ] Update Wayfinder for typed API access
- **Time:** 4 hours
- **PR:** feature/talent-pass-api

**Week 1 Deliverables:**
- ✅ Database schema with 4 core tables
- ✅ 5 Laravel models
- ✅ 3 service classes (CRUD, export, search)
- ✅ 2 API controllers
- ✅ 80+ backend tests (target > 90%)
- ✅ Full API documentation

---

### Week 2: Frontend Components (Apr 7-11)

**Day 6-7 (Apr 7-8):**
- [ ] Build TalentPassViewer.vue (read-only display)
- [ ] Build SkillsGraph.vue (visual skills display)
- [ ] Add Tailwind CSS styling (glass design system)
- [ ] Create responsive layout
- **Time:** 4 hours
- **PR:** feature/talent-pass-viewer-ui

**Day 8-9 (Apr 9-10):**
- [ ] Build TalentPassEditor.vue (edit form)
- [ ] Integrate with Inertia <Form>
- [ ] Add form validation feedback
- [ ] Implement draft auto-save
- **Time:** 3 hours
- **PR:** feature/talent-pass-editor-ui

**Day 10 (Apr 11):**
- [ ] Public view page (shareable links)
- [ ] QA testing (all flows)
- [ ] Performance optimization
- [ ] Browser testing (Chrome, Firefox, Safari)
- **Time:** 3 hours
- **PR:** feature/talent-pass-public-view

**Week 2 Deliverables:**
- ✅ 4 Vue 3 components (viewer, editor, graph, public)
- ✅ Full UI responsive design
- ✅ 40+ frontend tests
- ✅ Cross-browser compatibility
- ✅ Accessibility compliance

---

### Week 3: Integration & Testing (Apr 14-18)

**Day 11-12 (Apr 14-15):**
- [ ] Integrate with workforce planning
- [ ] Add to talent search (global search)
- [ ] Integration tests (full user flow)
- [ ] Admin operations (view/manage talent passes)
- **Time:** 3 hours

**Day 13-15 (Apr 16-18):**
- [ ] E2E browser tests (Pest 4)
- [ ] Load testing with k6
- [ ] Security review (OWASP top 10)
- [ ] Performance profiling
- **Time:** 4 hours

**Week 3 Deliverables:**
- ✅ Full integration with existing features
- ✅ 150+ total tests (backend + frontend)
- ✅ E2E tests on real browser
- ✅ Performance benchmarks documented
- ✅ Security audit completed

---

## 🚀 DEPLOYMENT PHASES

### Phase 1: Staging Deployment (Apr 19, 09:00 UTC)

**Duration:** 40 minutes  
**Steps:**

```bash
# 1. Create deployment tag
git tag -a talent-pass-cv2-v1.0.0 -m "Talent Pass CV 2.0 - First release"

# 2. Pre-deployment checks
php artisan test --compact  # Must pass 150+ tests
composer audit  # Zero vulnerabilities

# 3. Database migrations
php artisan migrate --env=staging --force

# 4. Seed sample data
php artisan db:seed --class=TalentPassSeeder --env=staging

# 5. Restart services
sudo systemctl restart php-fpm nginx

# 6. Smoke tests
curl -H "Authorization: Bearer TOKEN" \
  https://staging.stratos.app/api/talent-passes
# Expected: 200 OK with talent pass list
```

**Success Criteria:**
- ✅ All tests passing (150+)
- ✅ API returns talent passes
- ✅ Viewer page loads
- ✅ Editor form works
- ✅ PDF export works
- ✅ Search returns results

**24-Hour Monitoring (UAT):**
- [ ] Error rate < 0.1%
- [ ] Response p95 < 300ms
- [ ] No CV export failures
- [ ] Search works with 1000+ records
- [ ] Public links accessible

---

### Phase 2: Production Deployment (Apr 21, 14:00 UTC)

**Duration:** 40 minutes (after staging UAT approval)  
**Steps:** Same as staging, but on production environment

**Post-Launch Monitoring (72 hours):**
- [ ] Track adoption: % users viewing their Talent Pass
- [ ] Monitor PDF generation (CPU, memory)
- [ ] Track search performance with full data
- [ ] Gather user feedback via in-app survey

---

## 📊 DEPLOYMENT CHECKLIST

### Pre-Deployment (Week before)

- [ ] Code review completed (2+ approvals)
- [ ] All 150+ tests passing
- [ ] Zero security vulnerabilities
- [ ] Performance profiling done (export < 500ms)
- [ ] Documentation complete (API docs, user guide)
- [ ] Migration tested on local replica DB
- [ ] Staging environment prepped and tested
- [ ] Team trained on new features
- [ ] Customer success briefed (launch day talking points)

### Staging Deployment Day

- [ ] All prerequisites verified
- [ ] Database backup created
- [ ] Create deployment tag
- [ ] Pull code to staging
- [ ] Run migrations
- [ ] Run smoke tests
- [ ] Send notification to team
- [ ] Begin 24-hour monitoring
- [ ] QA performs acceptance tests

### Production Deployment Day

- [ ] UAT results reviewed (go/no-go)
- [ ] Production database backup
- [ ] Create production tag
- [ ] Pull code to production
- [ ] Run migrations
- [ ] Run smoke tests
- [ ] Monitor error rate (first 1 hour)
- [ ] Internal team verifies features
- [ ] Send launch announcement to users
- [ ] Monitor 72 hours continuously

---

## 🧪 TEST COVERAGE TARGETS

| Category | Target | Tests |
|----------|--------|-------|
| **Unit Tests** | 100% | 40+ |
| **Feature Tests** | 95%+ | 50+ |
| **E2E Tests** | Key flows | 20+ |
| **Browser Tests** | 3 browsers | 15+ |
| **Load Tests** | Peak load | k6 script |
| **Security Tests** | OWASP top 10 | 10+ |
| **Total** | > 150 | **150+** |

---

## 📈 SUCCESS METRICS (First Week)

| Metric | Target | Actual |
|--------|--------|--------|
| Uptime | > 99.5% | — |
| Error Rate | < 0.1% | — |
| Response p95 | < 300ms | — |
| PDF Export Time | < 500ms | — |
| Search Results (p95) | < 200ms | — |
| User Adoption | > 20% | — |
| Zero Data Loss | 100% | — |

---

## 🔄 ROLLBACK PLAN

**If critical issues discovered in staging:**

```bash
# Level 1: Soft rollback (restart services)
sudo systemctl restart php-fpm nginx

# Level 2: Code rollback (revert git)
git reset --hard HEAD~1
php artisan cache:clear

# Level 3: Database rollback (restore from backup)
pg_restore -d stratos_staging /backups/talent_pass_backup.sql

# Level 4: Full staging reset
# Contact DevOps for environment reset
```

**If issues in production:**

1. Immediate: Notify #product and #devops
2. Assess: Is it blocking users? High error rate?
3. Decide: Can fix in < 30 mins? Otherwise rollback
4. Execute: Use appropriate level above
5. Communicate: Post mortems and user notification

---

## 📞 OPERATIONS CONTACTS

| Role | On-Call (Mar 31 - Apr 21) |
|------|---------------------------|
| Backend Lead | [Name] |
| Frontend Lead | [Name] |
| DevOps Lead | [Name] |
| Product Manager | [Name] |
| QA Lead | [Name] |

---

## 🎯 CROSS-TEAM DEPENDENCIES

**Before Starting Implementation:**

1. **Design Team:** ✅ UI mockups & design system approved
2. **Product:** ✅ Requirement sign-off complete
3. **QA:** ✅ Acceptance criteria defined
4. **Infrastructure:** ✅ Staging/production environments prepped
5. **Marketing:** ✅ Launch messaging ready

**Messaging MVP Prerequisite:**

- ❌ Must complete: Messaging MVP staging deployment (Mar 27-28)
- ❌ Must complete: Go/no-go decision (Mar 28)
- ❌ Must complete: Production deployment decision (Mar 31)

**Only start Talent Pass after all Messaging MVP steps complete.**

---

## 💰 COST ESTIMATES

| Item | Estimate | Actual |
|------|----------|--------|
| Development (2 weeks, 1 dev) | $8,000 | — |
| QA/Testing (1 week) | $2,000 | — |
| Deployment/Ops (2 days) | $1,200 | — |
| **Total** | **$11,200** | **$0** (internal) |
| Infrastructure | $0 (existing) | $0 |
| Third-party services | $0 | $0 |

**Cost:** $0 (internal development, no external services)

---

## 📋 DEFINITION OF DONE (DoD)

Before marking complete:

- ✅ All acceptance criteria met
- ✅ Code review approved (2+ devs)
- ✅ 90%+ test coverage
- ✅ Zero HIGH/CRITICAL vulnerabilities
- ✅ Performance benchmarked (e.g., < 500ms export)
- ✅ Documentation complete (API, user guide, runbooks)
- ✅ Browser compatibility tested (3+ browsers)
- ✅ Accessibility audit passed (WCAG 2.1)
- ✅ Staging UAT successful
- ✅ Stakeholder sign-off obtained

---

## 📚 DOCUMENTATION TO DELIVER

**Technical:**
- [ ] API documentation (OpenAPI/Swagger)
- [ ] Database schema diagram
- [ ] Architecture decision records (ADRs)
- [ ] Model relationships diagram

**Operations:**
- [ ] Deployment checklist (step-by-step)
- [ ] Troubleshooting guide (common issues)
- [ ] Monitoring alerts & thresholds
- [ ] Rollback procedures

**User-Facing:**
- [ ] User guide (how to fill out Talent Pass)
- [ ] FAQ (common questions)
- [ ] Video tutorial (30 seconds)
- [ ] In-app help text

---

## ✅ SIGN-OFF & APPROVAL

| Role | Name | Date | Status |
|------|------|------|--------|
| Product Manager | — | — | [ ] |
| Engineering Lead | — | — | [ ] |
| QA Lead | — | — | [ ] |
| Operations | — | — | [ ] |

---

## 📌 NEXT STEPS

1. **Immediately After Messaging MVP Production:**
   - Review this deployment guide with team
   - Assign frontend/backend developers
   - Create feature branches (feature/talent-pass-*)

2. **Week of Mar 31:**
   - Begin database schema implementation
   - Start backend service development
   - Set up staging environment for CV 2.0

3. **Week of Apr 7:**
   - Begin frontend component development
   - Integration testing starts
   - E2E test coverage increases

4. **Week of Apr 14:**
   - Feature complete for staging
   - Full UAT begins
   - Monitoring configured

5. **Apr 19:**
   - Staging deployment
   - 24-hour UAT monitoring
   - Production decision (Apr 21)

---

**Last Updated:** Mar 26, 2026  
**Version:** 1.0 (Planning Phase)  
**Status:** Ready to execute after Messaging MVP completion  
**Dependencies:** Awaiting completion of Messaging MVP deployment (Mar 27-31)

