# ⚡ TALENT PASS CV 2.0 - QUICK REFERENCE

**Status:** Ready to execute after Messaging MVP production (Apr 19-21)  
**Duration:** 3 weeks implementation + 1 week staging/UAT  
**Team:** 1 Backend + 1 Frontend + 1 QA

---

## 🚀 QUICK FACTS

| Aspect                | Details                                       |
| --------------------- | --------------------------------------------- |
| **Scope**             | Digital CV/skills platform (read-only + edit) |
| **Dev Time**          | 3 weeks (30 hours total)                      |
| **Tests**             | 150+ (80+40+30)                               |
| **Cost**              | $0 (internal)                                 |
| **Start Date**        | Mar 31, 2026 (immediately after Messaging)    |
| **Staging Deploy**    | Apr 19, 2026                                  |
| **Production Deploy** | Apr 21, 2026 (if UAT green)                   |
| **Risk**              | LOW (proven pattern)                          |

---

## 📅 WEEK-BY-WEEK BREAKDOWN

### Week 1: Database & APIs (Mar 31 - Apr 4)

```bash
Day 1:  Database migrations (4 tables: talent_pass, skills, credentials, experiences)
        - 1 Laravel model per table + factories

Day 2-3: Services (CRUD, PDF export, search)
        - TalentPassService.php (400 lines)
        - CVExportService.php (300 lines)
        - TalentSearchService.php (200 lines)

Day 4-5: API controllers + tests
        - TalentPassController.php (REST endpoints)
        - 80+ tests (unit + feature)

Commit: feature/talent-pass-backend → main
Tests:  80 passing ✅
```

**Deploy to Staging:**

```bash
git checkout feature/talent-pass-backend
php artisan migrate --env=staging
php artisan test --filter=TalentPass
```

---

### Week 2: Vue3 Components (Apr 7 - Apr 11)

```bash
Day 6-7:  Viewer & Graph
          - TalentPassViewer.vue (read-only)
          - SkillsGraph.vue (visual display)
          - Tailwind CSS styling

Day 8-9:  Editor Form
          - TalentPassEditor.vue (edit form)
          - Inertia <Form> integration
          - Draft auto-save

Day 10:   Public page + responsive
          - PublicView.vue (shareable link)
          - Mobile-first responsive
          - Cross-browser test

Commit: feature/talent-pass-frontend → main
Tests:  40+ passing + E2E ✅
```

---

### Week 3: Integration (Apr 14 - Apr 18)

```bash
Day 11-12: Full integration
           - Workforce planning integration
           - Global talent search
           - Admin operations

Day 13-15: Testing sprint
           - E2E tests (Pest 4 browser)
           - Load testing (k6)
           - Security audit

Commit: feature/talent-pass-integration → main
Tests:  150+ total passing ✅
```

---

## 🧪 TEST STRUCTURE

```
tests/
├── Unit/
│   ├── TalentPassServiceTest.php (20 tests)
│   ├── CVExportServiceTest.php (15 tests)
│   └── TalentSearchServiceTest.php (15 tests)
│
├── Feature/
│   ├── TalentPassControllerTest.php (30 tests)
│   └── PublicTalentPassTest.php (10 tests)
│
└── Browser/
    ├── TalentPassViewerTest.php (10 tests)
    ├── TalentPassEditorTest.php (10 tests)
    └── PublicSharingTest.php (5 tests)

Total: 115 tests + k6 load tests + security audit
```

---

## 📦 FILE STRUCTURE

```
app/
├── Models/
│   ├── TalentPass.php
│   ├── TalentPassSkill.php
│   ├── TalentPassCredential.php
│   └── TalentPassExperience.php
├── Services/
│   ├── TalentPassService.php
│   ├── CVExportService.php
│   └── TalentSearchService.php
├── Http/Controllers/
│   ├── TalentPassController.php
│   └── PublicTalentPassController.php
└── Policies/
    └── TalentPassPolicy.php

resources/js/Pages/TalentPass/
├── Index.vue (list)
├── Show.vue (detail view)
├── Edit.vue (edit form)
└── PublicView.vue (public link)

resources/js/Components/
├── TalentPassViewer.vue
├── TalentPassEditor.vue
├── SkillsGraph.vue
├── CredentialsSection.vue
└── ExperienceTimeline.vue

database/migrations/
└── 2026_03_31_*.php (4 migrations)

database/factories/
├── TalentPassFactory.php
├── TalentPassSkillFactory.php
├── TalentPassCredentialFactory.php
└── TalentPassExperienceFactory.php
```

---

## 🔐 API ENDPOINTS

### Public Routes (No Auth)

```
GET    /api/talent-pass/{public_link}          → View public Talent Pass
```

### Authenticated Routes

```
GET    /api/talent-pass                        → List user's Talent Passes
GET    /api/talent-pass/{id}                   → View single Talent Pass
POST   /api/talent-pass                        → Create new Talent Pass
PUT    /api/talent-pass/{id}                   → Update Talent Pass
DELETE /api/talent-pass/{id}                   → Delete Talent Pass

POST   /api/talent-pass/{id}/skills            → Add skill
PUT    /api/talent-pass/{id}/skills/{skill_id} → Update skill
DELETE /api/talent-pass/{id}/skills/{skill_id} → Delete skill

POST   /api/talent-pass/{id}/credentials       → Add credential
PUT    /api/talent-pass/{id}/credentials/{c_id} → Update credential
DELETE /api/talent-pass/{id}/credentials/{c_id} → Delete credential

POST   /api/talent-pass/{id}/export-pdf        → Generate PDF export
GET    /api/talent-pass/search?skills=Laravel  → Search by skills
```

### Admin Routes

```
GET    /api/admin/talent-passes                → See all Talent Passes
GET    /api/admin/talent-passes/{id}           → View any Talent Pass
POST   /api/admin/talent-passes/{id}/feature   → Feature user's TP
```

---

## 📊 DATABASE SCHEMA QUICK VIEW

```sql
talent_passes
├── id, user_id, organization_id
├── title, bio, headline
├── public_link, is_public
├── created_at, updated_at
└── UNIQUE(organization_id, user_id)

talent_pass_skills
├── id, talent_pass_id
├── skill_name
├── proficiency_level (beginner→expert)
├── endorsement_count
└── created_at

talent_pass_credentials
├── id, talent_pass_id
├── title, issuer
├── issue_date, expiry_date
├── credential_url
└── created_at

talent_pass_experiences
├── id, talent_pass_id
├── title, company
├── duration_start, duration_end
├── description
└── created_at
```

---

## ⚠️ CRITICAL PREREQUISITES

**MUST Complete Before Starting:**

1. ✅ Messaging MVP staging deployed (Mar 27)
2. ✅ Messaging MVP production approved (Mar 31)
3. ✅ Operations team trained on deployment
4. ✅ Staging environment verified for new feature
5. ✅ Database backup strategy confirmed

**If any prerequisite not met → DELAY start to next week**

---

## 🎯 GO/NO-GO CRITERIA (Staging UAT, Apr 19-20)

**GO if:**

- ✅ 150+ tests passing
- ✅ Error rate < 0.1%
- ✅ PDF export < 500ms consistently
- ✅ Search < 200ms with 1000 records
- ✅ Zero data loss
- ✅ Public links work correctly
- ✅ No HIGH/CRITICAL security issues

**NO-GO if:**

- ❌ < 80% test passing
- ❌ Error rate > 0.5%
- ❌ PDF export > 2s frequently
- ❌ Data loss during testing
- ❌ Critical security vulnerability found

**EXTEND UAT if:**

- Minor issue found but fixable
- Needs 1-2 more days to resolve
- Will not block production release

---

## 📞 WHO DOES WHAT

| Phase             | Owner         | Duration        | Status |
| ----------------- | ------------- | --------------- | ------ |
| Database/APIs     | Backend Lead  | Week 1 (10h)    | [ ]    |
| Vue Components    | Frontend Lead | Week 2 (10h)    | [ ]    |
| Integration/Tests | QA Lead       | Week 3 (10h)    | [ ]    |
| Staging Deploy    | DevOps        | Apr 19 (2h)     | [ ]    |
| UAT & Monitoring  | Operations    | Apr 19-20 (16h) | [ ]    |
| Production Deploy | DevOps        | Apr 21 (2h)     | [ ]    |

---

## 🚨 DEPLOYMENT CHECKPOINTS

| Checkpoint                    | Date   | Owner     | Status |
| ----------------------------- | ------ | --------- | ------ |
| Database schema approved      | Mar 28 | Backend   | [ ]    |
| APIs complete + tests passing | Apr 4  | Backend   | [ ]    |
| UI mockups approved           | Apr 1  | Design    | [ ]    |
| Components complete           | Apr 11 | Frontend  | [ ]    |
| Integration tests passing     | Apr 18 | QA        | [ ]    |
| Security audit clean          | Apr 18 | Security  | [ ]    |
| Staging ready                 | Apr 19 | DevOps    | [ ]    |
| UAT approval                  | Apr 20 | Product   | [ ]    |
| Production ready              | Apr 21 | Tech Lead | [ ]    |

---

## 🔄 ROLLBACK IF NEEDED

**Staging Issues (< 24 hours):**

```bash
# Soft rollback: restart services
sudo systemctl restart php-fpm nginx

# Code rollback: revert commit
git reset --hard HEAD~1
php artisan cache:clear
sudo systemctl restart php-fpm
```

**Production Issues:**

```bash
# Same as staging, but on production cluster
# If code rollback doesn't work → database rollback
# If database issues → full environment reset (contact DevOps)
```

---

## 💡 TIPS FOR SUCCESS

1. **Start immediately after Messaging production** (Mar 31)
2. **Use Messaging MVP as template** (proven pattern)
3. **Test continuously** (don't wait for end of week)
4. **Deploy to staging early** (Apr 19, not later)
5. **Brief team daily** (15-min standups)
6. **Monitor closely** (first 72 hours production are critical)
7. **Document as you go** (not at the end)
8. **Get user feedback** (early in UAT)

---

## 📋 DELIVERABLES CHECKLIST

**Backend (Week 1):**

- [ ] 4 database tables + migrations
- [ ] 4 Laravel models + factories
- [ ] 3 Service classes (400+300+200 lines)
- [ ] 2 Controllers (REST endpoints)
- [ ] 80 unit + feature tests
- [ ] API documentation (Swagger/OpenAPI)

**Frontend (Week 2):**

- [ ] 4 Vue 3 components (1200 lines total)
- [ ] Tailwind CSS styling
- [ ] Responsive mobile + desktop
- [ ] 40 frontend + E2E tests
- [ ] Browser compatibility (3+ browsers)

**Integration (Week 3):**

- [ ] Workforce planning integration
- [ ] Global talent search
- [ ] Admin operations panel
- [ ] 30 integration + load tests
- [ ] Security audit report
- [ ] Deployment runbooks (5 docs)

**Operations:**

- [ ] Deployment checklist (PR template)
- [ ] Troubleshooting guide (10 issues)
- [ ] Monitoring setup (alerts configured)
- [ ] Rollback procedures documented
- [ ] Operations summary (for stakeholders)

---

## 🎓 KNOWLEDGE TRANSFER

**Before Starting:**

- [ ] Team reads TALENT_PASS_CV2_DEPLOYMENT.md
- [ ] 1-hour kickoff meeting (architecture review)
- [ ] Local setup testing (DB + migrations work)
- [ ] Code examples reviewed (from Messaging MVP)

**During Implementation:**

- [ ] Daily 15-min standups
- [ ] Weekly code reviews
- [ ] Mid-week checkpoint (are we on track?)

**After Release:**

- [ ] Lessons learned session
- [ ] Update deployment guide based on learnings
- [ ] Document new patterns discovered

---

**Last Updated:** Mar 26, 2026  
**Next Review:** Mar 31, 2026 (start date)  
**Keep updated:** As implementation progresses
