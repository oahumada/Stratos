# 🚀 Messaging MVP - Staging Deployment Execution Plan

**Execution Date:** Mar 27, 2026 (Tomorrow)  
**Estimated Duration:** 45 minutes  
**Risk Level:** LOW (623 tests passing, 0 failures)  
**Team:** Deployment Team (2 people recommended)  
**Owner:** DevOps Lead  

---

## 📋 Pre-Execution Checklist (Execute Mar 26, Tonight)

**By DevOps Lead before 23:59 Mar 26:**

- [ ] Have SSH credentials ready for staging server
- [ ] Verify VPN access to staging infrastructure
- [ ] Have database backup tool (pg_dump) available
- [ ] Prepare Slack channel for deployment notifications (#deployments or #ops)
- [ ] Communicate timeline to Product/Engineering stakeholders
- [ ] Print or bookmark this document + DEPLOYMENT_CHECKLIST.md
- [ ] Have rollback procedure (ROLLBACK_GUIDE.md) nearby
- [ ] Ensure team member #2 is on standby

---

## ⏱️ Execution Timeline (Mar 27, 08:00-09:00 UTC)

### 08:00-08:10 → **Phase 1: Pre-Deployment Verification** (10 min)

**Location:** Local machine (Omar's Stratos repo)

**Steps:**

```bash
cd /home/omar/Stratos

# 1. Verify tests pass
php artisan test --compact 2>&1 | tail -5
# ✅ EXPECTED: "Tests: 759 passed"

# 2. Check code quality
./vendor/bin/phpstan analyse app --level=9 2>&1 | grep -E "✓|ERROR" | head -1
# ✅ EXPECTED: No output (0 errors)

# 3. Verify formatting
./vendor/bin/pint --test 2>&1 | tail -3
# ✅ EXPECTED: "All checked files are valid"

# 4. Check latest commit
git log --oneline -1
# ✅ EXPECTED: Contains messaging or admin changes

# 5. Check security
composer audit 2>&1 | grep "critical\|high"
# ✅ EXPECTED: No output (0 critical/high vulns)
```

**Decision Point:** All checks pass? → **GO** | Any fail? → **ABORT, investigate, reschedule**

**Status:** ✅ PASS / ❌ FAIL  
**Notes:** _________________

---

### 08:10-08:15 → **Phase 2: Git Tag & Preparation** (5 min)

**Location:** Local machine

**Steps:**

```bash
# 1. Create deployment tag
git tag -a messaging-mvp-staging-v0.4.0 \
  -m "Messaging MVP + Admin Operations + Staging pre-deployment"

# 2. Verify tag
git show messaging-mvp-staging-v0.4.0 | head -5

# 3. Push to remote
git push origin messaging-mvp-staging-v0.4.0

# 4. Verify in GitHub (check releases page)
sleep 2
echo "✓ Tag visible in GitHub releases"
```

**Status:** ✅ Complete / ❌ Failed  
**Commit:** ________________  
**Tag:** messaging-mvp-staging-v0.4.0

---

### 08:15-08:35 → **Phase 3: Staging Environment Setup** (20 min)

**Location:** Staging server (SSH)

**Steps:**

```bash
# 1. SSH to staging
ssh -i /path/to/staging-key.pem ubuntu@staging.stratos.app
cd /var/www/stratos-staging

# 2. Verify current state
pwd
ls -la .env.staging | head -1
# ✅ EXPECTED: .env.staging exists

# 3. Create backup BEFORE deploying
mkdir -p /var/backups/stratos
pg_dump -h staging-db.internal \
  -U postgres \
  -d stratos_staging \
  > /var/backups/stratos/stratos_staging_$(date +%Y%m%d_%H%M%S).sql

# 4. Verify backup
ls -lh /var/backups/stratos/*.sql | tail -1
# ✅ EXPECTED: File size > 1MB

# 5. Fetch and checkout tag
git fetch origin
git checkout messaging-mvp-staging-v0.4.0

# 6. Install dependencies (no dev)
composer install --no-dev --optimize-autoloader --no-interaction
npm install --legacy-peer-deps

# 7. Build frontend
npm run build

# 8. Generate keys & cache
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Status:** ✅ Complete / ❌ Failed  
**Backup Created:** ________________  
**Build Output:** ________________

---

### 08:35-08:45 → **Phase 4: Database & Cache** (10 min)

**Location:** Staging server (SSH)

**Steps:**

```bash
# 1. Review pending migrations
php artisan migrate:status

# 2. Run migrations
php artisan migrate --force

# 3. Verify migrations
php artisan migrate:status | grep -E "✓|×"
# ✅ EXPECTED: All show ✓

# 4. Optional: Seed test data
# php artisan db:seed --class=MessagingSeeder --force
# (Uncomment if you want test data)

# 5. Clear and warm cache
php artisan cache:clear
php artisan config:cache

# 6. Restart services
sudo systemctl restart php-fpm
sleep 2
sudo systemctl restart nginx
sleep 2
sudo systemctl restart supervisor
sleep 2
```

**Status:** ✅ Complete / ❌ Failed  
**Migrations:** ✓ Applied / ✗ Blocked  
**Services Restarted:** ✓ YES / ✗ NO

---

### 08:45-09:00 → **Phase 5: Verification & Smoke Tests** (15 min)

**Location:** Local machine (curl commands) OR Staging server

**Steps:**

```bash
# 1. Test HTTP endpoint
curl -I https://staging.stratos.app/
# ✅ EXPECTED: HTTP 200 OK

# 2. Test API health
curl -s https://staging.stratos.app/api/health | jq .
# ✅ EXPECTED: {"status":"ok"}

# 3. Test DB connection (on staging server)
php artisan tinker
>>> Message::count()
# ✅ EXPECTED: Shows count (probably 0 if no seed)
>>> Conversation::count()
# ✅ EXPECTED: Shows count
>>> exit()

# 4. Test Redis
redis-cli -h staging-redis.internal ping
# ✅ EXPECTED: PONG

# 5. Check logs for errors
tail -20 storage/logs/laravel*.log | grep -i error
# ✅ EXPECTED: No [ERROR] entries

# 6. Check Queue status (for async messaging)
# If supervisor running:
ps aux | grep -i supervisor | grep -v grep
# ✅ EXPECTED: Supervisor process present
```

**Status:** ✅ All Green / ⚠️ Minor Issues / ❌ Critical Failure

**Test Results:**
- [ ] HTTP 200 OK
- [ ] API health responds
- [ ] DB connection works
- [ ] Redis responds
- [ ] No error logs
- [ ] Queue workers running

---

## 🎯 Success Criteria (9:00 UTC)

- ✅ HTTP endpoint accessible (200 OK)
- ✅ API health check passes
- ✅ Database queries work
- ✅ No [ERROR] entries in logs
- ✅ Queue workers active
- ✅ Redis cache working

**If ALL ✅:** → **DEPLOYMENT SUCCESSFUL** 🎉  
**If ANY ❌:** → **ROLLBACK & INVESTIGATE** (see section below)

---

## 🔄 Post-Deployment (Mar 27-28)

### 24-Hour Monitoring (Mar 27 09:00 → Mar 28 09:00)

**Team:** On-call engineer (rotating hourly shifts)

**Metrics to Monitor (using MONITORING_GUIDE.md):**

- [ ] Error rate < 1%
- [ ] P95 latency < 500ms
- [ ] Queue processing time < 5 min
- [ ] Database connections stable
- [ ] Redis hit ratio > 80%
- [ ] No memory leaks (check RSS memory)

**Actions if issues:**

- **Error spike:** Check logs for pattern → escalate if P0
- **Latency spike:** Review query times → investigate N+1
- **Queue backlog:** Check job failures → retry or escalate
- **OOM (Out of Memory):** Restart PHP-FPM → escalate to infrastructure

**Escalation Matrix:**

| Severity | Response Time | Escalate To |
|----------|---------------|-------------|
| P0 (Critical) | <15 min | DevOps Lead + CTO |
| P1 (High) | <1 hour | Tech Lead + DevOps |
| P2 (Medium) | <4 hours | Tech Lead |
| P3 (Low) | Next business day | Backlog |

---

## ⛔ EMERGENCY ROLLBACK (If needed)

**If critical issue detected:**

```bash
# 1. SSH to staging
ssh -i /path/to/staging-key.pem ubuntu@staging.stratos.app
cd /var/www/stratos-staging

# 2. Checkout previous stable tag
git checkout messaging-mvp-staging-v0.3.0   # (or known-good commit)

# 3. Run migrations in reverse
php artisan migrate:rollback --force

# 4. Restore backup (only if data corruption suspected)
# pg_restore -h staging-db.internal -U postgres -d stratos_staging \
#   /var/backups/stratos/stratos_staging_BACKUP_DATE.sql

# 5. Restart services
sudo systemctl restart php-fpm
sudo systemctl restart nginx
sudo systemctl restart supervisor

# 6. Verify rolled back state
curl -I https://staging.stratos.app/
# Should return to previous stable state (or error if critical)

# 7. Notify stakeholders
# Send message to #incidents Slack channel with:
# - Deployment rolled back
# - Reason for rollback
# - Time estimate for next attempt
```

**See:** [ROLLBACK_GUIDE.md](./ROLLBACK_GUIDE.md) for 4-level recovery procedures

---

## 📞 Communication Template

**Pre-Deployment (Mar 26, 17:00):**
```
🚀 Messaging MVP Staging Deployment Tomorrow

Timeline: Mar 27, 08:00-09:00 UTC
Status: Ready (623 tests green, 0 failures)
Impact: Staging environment brief downtime (~1 min)
Team: DevOps + Tech Lead on-call

Updates: #deployments Slack channel
```

**During Deployment (Mar 27, 08:00):**
```
🔄 DEPLOYMENT IN PROGRESS
Phase 1: Verification ✓
Phase 2: Git tag & prep ✓
Phase 3: Staging setup [05:15/20:00 remaining]
...
```

**Post-Deployment (Mar 27, 09:00):**
```
✅ DEPLOYMENT SUCCESSFUL

Messaging MVP now live in staging
- 26 API endpoints active
- Real-time messaging working
- Admin operations dashboard accessible
- 24-hour monitoring active

Next: UAT & stakeholder validation
Status: Ready for production (if UAT passes)
```

---

## 📊 Deployment Checklist Summary

| Phase | Duration | Status | Owner | Notes |
|-------|----------|--------|-------|-------|
| 1. Pre-verification | 10 min | ⏳ Pending | DevOps | Local checks |
| 2. Git tag & prep | 5 min | ⏳ Pending | DevOps | Create v0.4.0 tag |
| 3. Env setup | 20 min | ⏳ Pending | DevOps + Staging | Deploy code, build |
| 4. DB & cache | 10 min | ⏳ Pending | DevOps | Migrate, warm cache |
| 5. Verification | 15 min | ⏳ Pending | Tech Lead | Smoke tests |
| **Total** | **~60 min** | ⏳ Pending | All | Buffer: +15 min |

---

## 🎯 Success Outcome (Mar 28, 10:00 UTC)

If all tests pass:

✅ Messaging MVP deployed to staging  
✅ 24-hour UAT monitoring complete  
✅ No critical issues found  
✅ Ready for production approval  
✅ Next: Production deployment (Mar 31 or later per roadmap)

---

## 📎 Related Documents

- [DEPLOYMENT_CHECKLIST.md](./DEPLOYMENT_CHECKLIST.md) - Detailed step-by-step
- [ROLLBACK_GUIDE.md](./ROLLBACK_GUIDE.md) - Emergency procedures
- [MONITORING_GUIDE.md](./MONITORING_GUIDE.md) - Real-time monitoring + alerts
- [TROUBLESHOOTING_GUIDE.md](./TROUBLESHOOTING_GUIDE.md) - Common issues
- [OPERATIONS_SUMMARY.md](./OPERATIONS_SUMMARY.md) - Contact + escalation matrix

---

**Document Version:** v1.0  
**Created:** Mar 26, 2026  
**Last Updated:** Mar 26, 2026  
**Status:** Ready for execution  
**Next Review:** After deployment completion
