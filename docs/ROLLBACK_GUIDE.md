# 🔄 ROLLBACK GUIDE - Emergency Recovery Procedures

**Purpose:** Fast, safe recovery if deployment has critical failures  
**When to Use:** Only if CRITICAL issues found during/after deployment  
**Execution Time:** 5-10 minutes  
**Authority:** Tech Lead approval required  

---

## ⚡ CRITICAL ISSUES REQUIRING ROLLBACK

**If seeing any of these → Execute rollback immediately:**

- Application throwing frequent 500 errors (> 10 per minute)
- Database corrupted or inaccessible
- Message data loss confirmed
- Widespread test failures
- All users unable to send messages
- Security vulnerability discovered in deployment

**Action → Notify Tech Lead (#devops-alerts + phone call) → Execute appropriate Level**

---

## 🟡 LEVEL 1: SOFT ROLLBACK (Service Restart) - 2 mins

**Use When:** Temporary issues but code is fine (memory spike, cache corruption, session issues)

```bash
# Step 1: Clear and restart services
php artisan cache:clear
php artisan config:cache

# Step 2: Restart services in order
sudo systemctl restart php-fpm
sleep 2
sudo systemctl restart nginx
sleep 2
sudo systemctl restart supervisor

# Step 3: Verify recovery
curl -s https://staging.stratos.app/api/health | jq .
# ✅ EXPECTED: {"status":"ok"}

# Step 4: Test application
php artisan test --compact --filter=smoke
```

**Result:** Services restarted, no code changes. If issues persist → Escalate to Level 2

---

## 🟠 LEVEL 2: CODE ROLLBACK (Git Revert) - 3 mins

**Use When:** Code deployment caused failure (new bugs, broken migrations, logic errors)

```bash
cd /var/www/stratos-staging

# Step 1: Identify previous good commit
git log --oneline | head -5
# The commit BEFORE current deployment should be the good one

# Step 2: Backup current state FIRST
sudo mkdir -p /var/backups/rollback
sudo cp -r . /var/backups/rollback/staging_$(date +%Y%m%d_%H%M%S)
echo "✅ Current state backed up"

# Step 3: Revert code
GOOD_COMMIT="2efbbc50"  # Adjust to your good commit
git reset --hard $GOOD_COMMIT

# Step 4: Undo any failed migrations
php artisan migrate:rollback --step=1
# OR if multiple migrations failed:
# php artisan migrate:refresh --force  (CAUTION: Rolls back ALL)

# Step 5: Reinstall and restart
composer install --no-dev --optimize-autoloader
npm install --legacy-peer-deps && npm run build
php artisan cache:clear && php artisan config:cache

# Step 6: Restart services
sudo systemctl restart php-fpm nginx supervisor

# Step 7: Verify
php artisan test --compact | tail -5
# ✅ EXPECTED: Tests passing again
```

**Result:** Code reverted to previous working version. Old deployment ignored.

---

## 🔴 LEVEL 3: DATABASE ROLLBACK (Point-in-Time Recovery) - 5 mins

**Use When:** Database migration corrupted/destroyed data (missing tables, wrong schema)

```bash
# Step 1: Verify backup exists
BACKUP_FILE="/var/backups/stratos/stratos_staging_20260327_restart.sql"
ls -lh $BACKUP_FILE  # Must exist and have size > 100MB

# Step 2: Create backup of current (failed) state for analysis
pg_dump -h staging-db.internal -U postgres -d stratos_staging \
  > /var/backups/stratos/FAILED_$(date +%Y%m%d_%H%M%S).sql

# Step 3: Terminate connections to database
psql -h staging-db.internal -U postgres << EOF
SELECT pg_terminate_backend(pg_stat_activity.pid)
FROM pg_stat_activity
WHERE datname = 'stratos_staging' AND pid <> pg_backend_pid();
EOF

# Step 4: Drop and recreate database
psql -h staging-db.internal -U postgres << EOF
DROP DATABASE IF EXISTS stratos_staging;
CREATE DATABASE stratos_staging;
EOF

# Step 5: Restore from backup
psql -h staging-db.internal -U postgres -d stratos_staging < $BACKUP_FILE

# Step 6: Verify structure
php artisan migrate:status
# ✅ EXPECTED: All migrations show [✓]

# Step 7: Clear cache and restart
php artisan cache:clear
php artisan config:cache
php artisan metrics:warm-cache
sudo systemctl restart php-fpm nginx supervisor

# Step 8: Verify
curl -s https://staging.stratos.app/api/health | jq .status
```

**Result:** Database restored to state before deployment. Any changes made AFTER backup are lost.

---

## 💥 LEVEL 4: COMPLETE ENVIRONMENT RESET (Nuclear Option) - 15 mins

**Use When:** Multiple cascading failures, uncertain root cause, all other recoveries failed

**⚠️ REQUIRES EXPLICIT TECH LEAD APPROVAL - Call tech lead before executing**

```bash
# Step 1: Document everything for post-mortem
tar -czf /var/backups/rollback/staging-$(date +%Y%m%d_%H%M%S).tar.gz \
  /var/www/stratos-staging/storage/logs/
echo "✅ Logs saved for analysis"

# Step 2: Full database reset
pg_dump -h staging-db.internal -U postgres -d stratos_staging \
  > /var/backups/stratos/staging_FULL_FAILED_$(date +%Y%m%d_%H%M%S).sql
gzip /var/backups/stratos/staging_FULL_FAILED_*.sql

# Kill all connections
psql -h staging-db.internal -U postgres << EOF
SELECT pg_terminate_backend(pg_stat_activity.pid)
FROM pg_stat_activity WHERE datname = 'stratos_staging' AND pid <> pg_backend_pid();
DROP DATABASE IF EXISTS stratos_staging;
CREATE DATABASE stratos_staging;
EOF

# Step 3: Full code reset
cd /var/www/stratos-staging
git fetch origin
git reset --hard origin/main
git checkout main

# Step 4: Fresh install
rm -rf node_modules vendor
composer install --no-dev --optimize-autoloader
npm install --legacy-peer-deps && npm run build

# Step 5: Database from scratch
php artisan migrate --env=staging --force
php artisan db:seed --class=MessagingSeeder --env=staging
php artisan metrics:warm-cache

# Step 6: Restart everything
sudo systemctl restart php-fpm nginx supervisor redis-server

# Step 7: Full smoke test
php artisan test --compact
# ✅ EXPECTED: 759+ tests passing

# Step 8: Final verification
curl -s https://staging.stratos.app/api/health | jq .
```

**Result:** Complete environment reset. All systems fresh. Old deployment completely gone.

---

## 📋 QUICK DECISION MATRIX

| Situation | Symptom | Level | Time | Risk |
|-----------|---------|-------|------|------|
| Temporary spike | CPU/Memory high, transient errors | 1 | 2 min | Very Low |
| Code bug | New errors in logs, migrations fail | 2 | 3 min | Low |
| Schema broken | Tables missing, queries fail | 3 | 5 min | Medium |
| Unknown chaos | Multiple failures, can't debug | 4 | 15 min | High |

---

## 🔍 HOW TO IDENTIFY ROOT CAUSE

### Step 1: Check Application Logs

```bash
tail -100 /var/www/stratos-staging/storage/logs/laravel.log | grep -i "error\|exception\|fatal"
```

### Step 2: Check Database

```bash
php artisan tinker <<< "DB::selectOne('SELECT 1')" && echo "DB OK" || echo "DB FAILED"
php artisan migrate:status | grep -E "Migration|Batch"
```

### Step 3: Check Recent Commit

```bash
git log --oneline -3
git diff HEAD~1..HEAD --stat | head -20
```

### Step 4: Identify Failing Test

```bash
php artisan test --verbose 2>&1 | grep -i "failed\|error" | head -5
```

**Based on findings, choose appropriate rollback level**

---

## 🛑 PREVENT ACCIDENTAL ROLLBACK

**Before executing ANY rollback:**

1. [ ] **Verify:** Read the command 3 times
2. [ ] **Backup:** Backup current state (already included in procedures)
3. [ ] **Approval:** Get tech lead confirmation (Level 2+)
4. [ ] **Document:** Note timestamp and reason in Slack
5. [ ] **Communicate:** Post to #devops-alerts before starting

---

## ✅ POST-ROLLBACK VERIFICATION

**After any rollback, verify:**

```bash
# 1. Services running
systemctl status php-fpm nginx supervisor | grep active

# 2. Database reachable
php artisan tinker <<< "DB::selectOne('SELECT 1')" && echo "✅ DB OK"

# 3. Tests passing
php artisan test --compact --filter=messaging

# 4. No errors in logs
tail -20 /var/www/stratos-staging/storage/logs/laravel.log | grep -c ERROR

# 5. Application responds
curl -s https://staging.stratos.app/api/health | jq .

# 6. Cache working
php artisan metrics:cache-stats | head -3
```

**All ✅? → Rollback complete. Post to #devops-alerts with status.**

---

## 📞 ESCALATION

| Issue | Action | Contact |
|-------|--------|---------|
| Level 1 failed | Try Level 2 | Backend lead |
| Level 2 failed | Try Level 3 | DevOps lead |
| Level 3 failed | Execute Level 4 | Tech lead (approval required) |
| Level 4 didn't work | System in unknown state | Call full team + AWS support |

---

## 🧠 DECISION: EXTEND vs ROLLBACK vs PUSH AHEAD

After identifying issue:

**ROLLBACK IF:**
- Bug in new code causing failures
- Critical data loss
- Security vulnerability
- Unable to fix in < 30 minutes

**EXTEND STAGING IF:**
- Minor issue found but doesn't block messaging
- Can be fixed and re-tested
- Time permits (before production window)

**PUSH AHEAD IF:**
- Issue is not in new code (pre-existing)
- Monitored and not critical
- Fix ready for production release

---

## 🔐 POST-INCIDENT CHECKLIST

After rollback, before attempting deployment again:

- [ ] Root cause identified and documented
- [ ] Fix implemented and tested locally
- [ ] Code reviewed by senior dev
- [ ] New tests added to prevent regression
- [ ] All 759 tests passing
- [ ] Tech lead approval on fix
- [ ] Deployment approval process repeated
- [ ] Team briefed on what changed

---

**Last Updated:** Mar 26, 2026  
**Maintained By:** DevOps Team  
**Emergency Contact:** Tech Lead (24/7)

