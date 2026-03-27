# ✅ DEPLOYMENT CHECKLIST - Messaging MVP to Staging

**Date:** Mar 27, 2026  
**Version:** messaging-mvp-staging-v0.4.0  
**Status:** Ready for Execution  
**Estimated Duration:** 40 minutes  
**Risk Level:** LOW (759 tests passing)

---

## 👤 Prerequisites & Access

- [ ] SSH access to staging server
- [ ] Database backup tools (pg_dump)
- [ ] Git access with push permissions
- [ ] Admin credentials for staging environment
- [ ] Telegram group created for deployment notifications

---

## 🟢 PHASE 1: PRE-DEPLOYMENT VERIFICATION (5 mins)

### 1.1 Local Code Verification

```bash
# Navigate to project
cd /home/omar/Stratos

# Verify clean working directory
git status
# ✅ EXPECTED: working tree clean

# Verify latest commit
git log --oneline -1
# ✅ EXPECTED: fc4982d8 or later with "docs: Messaging MVP staging deployment plan"

# Verify all tests pass
php artisan test --compact
# ✅ EXPECTED: 759+ tests passed (136 N+1 + 623 Messaging)

# Show test breakdown
php artisan test --compact | grep -E "passed|FAILED"
```

**Status Tracker:**

- [ ] Working tree clean
- [ ] Latest commit verified (fc4982d8+)
- [ ] 759 tests passing
- [ ] No FAILED tests

---

### 1.2 Code Quality Verification

```bash
# Run static analysis
./vendor/bin/phpstan analyse app --level=9
# ✅ EXPECTED: 0 errors

# Check code formatting
./vendor/bin/pint --test
# ✅ EXPECTED: 0 files need formatting

# Verify security vulnerabilities
composer audit
# ✅ EXPECTED: 0 vulnerabilities marked CRITICAL or HIGH
```

**Status Tracker:**

- [ ] PHPStan: 0 errors
- [ ] Pint: 0 formatting issues
- [ ] Security: No critical vulnerabilities

---

### 1.3 Dependency Verification

```bash
# Check PHP version
php --version
# ✅ EXPECTED: PHP 8.4.xx or compatible

# Check PostgreSQL
psql --version
# ✅ EXPECTED: PostgreSQL 14+

# Check Redis
redis-cli --version
# ✅ EXPECTED: redis-cli 6.0+

# Verify Composer dependencies
composer check-platform-reqs
# ✅ EXPECTED: All checks pass
```

**Status Tracker:**

- [ ] PHP version compatible
- [ ] PostgreSQL available
- [ ] Redis available
- [ ] All platform requirements met

---

### 1.4 Configuration Verification

```bash
# Show current environment
echo $APP_ENV
# ℹ️ EXPECTED: Could be 'local' or 'staging' depending on current context

# Verify .env.example contains all required keys
grep -E "DB_HOST|REDIS_HOST|CACHE_STORE|QUEUE_CONNECTION" .env.example
# ✅ EXPECTED: All keys present

# Validate current Laravel config
php artisan config:list | head -20
# ✅ EXPECTED: Configuration loads without errors
```

**Status Tracker:**

- [ ] Environment configuration valid
- [ ] .env.example has all required keys
- [ ] Laravel config loads

---

## 🔵 PHASE 2: TAG & VERSION CREATION (3 mins)

### 2.1 Create Deployment Tag

```bash
# Create annotated tag with description
git tag -a messaging-mvp-staging-v0.4.0 \
  -m "Staging deployment v0.4.0 - Messaging MVP + N+1 optimizations (5 phases)"

# ✅ VERIFY tag was created
git tag -l | grep messaging-mvp-staging-v0.4.0

# ✅ SHOW tag details
git show messaging-mvp-staging-v0.4.0 | head -10
```

**Status Tracker:**

- [ ] Tag created: messaging-mvp-staging-v0.4.0
- [ ] Tag verified locally
- [ ] Tag message set correctly

---

### 2.2 Push Tag to Remote

```bash
# Push tag to origin
git push origin messaging-mvp-staging-v0.4.0

# ✅ VERIFY tag on remote
git ls-remote origin | grep messaging-mvp-staging-v0.4.0

# ✅ Check GitHub releases page
# Expected: Tag visible at https://github.com/oahumada/Stratos/releases
```

**Status Tracker:**

- [ ] Tag pushed to origin
- [ ] Tag visible in GitHub releases
- [ ] Remote in sync with local

---

## 🟡 PHASE 3: STAGING ENVIRONMENT SETUP (10 mins)

### 3.1 SSH Access & Directory Setup

```bash
# Connect to staging server
ssh user@staging-server

# Or use full connection string
ssh -i /path/to/key.pem ubuntu@staging.stratos.app

# Navigate/create staging directory
mkdir -p /var/www/stratos-staging
cd /var/www/stratos-staging

# ✅ VERIFY permissions
ls -ld /var/www/stratos-staging
# ✅ EXPECTED: drwxr-xr-x (755 minimum)
```

**Status Tracker:**

- [ ] SSH access verified
- [ ] Staging directory exists
- [ ] Directory permissions correct

---

### 3.2 Create/Update .env.staging File

```bash
# Create .env.staging with Staging Configuration
cat > /var/www/stratos-staging/.env.staging << 'EOF'
# Application Settings
APP_NAME="Stratos"
APP_ENV=staging
APP_DEBUG=true
APP_URL=https://staging.stratos.app
APP_KEY=base64:YOUR_GENERATED_KEY_HERE

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=staging-db.internal
DB_PORT=5432
DB_DATABASE=stratos_staging
DB_USERNAME=postgres
DB_PASSWORD=YOUR_DB_PASSWORD_HERE

# Redis Caching
CACHE_STORE=redis
REDIS_HOST=staging-redis.internal
REDIS_PORT=6379
REDIS_PASSWORD=YOUR_REDIS_PASSWORD_HERE
REDIS_CACHE_DB=0

# Queue & Sessions
QUEUE_CONNECTION=redis
SESSION_DRIVER=cookie
SESSION_LIFETIME=2880

# Mail (use log for staging)
MAIL_DRIVER=log
MAIL_FROM_ADDRESS=noreply@staging.stratos.app

# Auth & Security
SANCTUM_STATEFUL_DOMAINS=staging.stratos.app
TRUSTED_PROXIES=*
SANCTUM_EXPIRATION=10080

# Feature Flags (if applicable)
FEATURE_MESSAGING=true
FEATURE_ADMIN_OPERATIONS=true
EOF

# Restrict permissions (no world-readable secrets)
chmod 600 /var/www/stratos-staging/.env.staging

# ✅ VERIFY file exists and is readable
ls -l /var/www/stratos-staging/.env.staging
# ✅ EXPECTED: -rw------- (600 permissions)
```

**Status Tracker:**

- [ ] .env.staging file created
- [ ] Permissions set to 600 (private)
- [ ] All required keys present
- [ ] Secrets configured (DB, Redis)

---

### 3.3 Database Backup (CRITICAL)

```bash
# Create backup directory
mkdir -p /var/backups/stratos

# Perform database backup BEFORE any changes
pg_dump -h staging-db.internal \
  -U postgres \
  -d stratos_staging \
  > /var/backups/stratos/stratos_staging_$(date +%Y%m%d_%H%M%S).sql

# ✅ VERIFY backup was created
ls -lh /var/backups/stratos/*.sql | tail -1
# ✅ EXPECTED: File size > 10MB (depending on data)

# Compress backup to save space (optional)
gzip /var/backups/stratos/stratos_staging_*.sql
```

**Status Tracker:**

- [ ] Backup directory created (/var/backups/stratos)
- [ ] Database backup created
- [ ] Backup file verified and readable
- [ ] Backup compressed (optional)

---

## 🔴 PHASE 4: CODE DEPLOYMENT & INSTALLATION (15 mins)

### 4.1 Clone/Update Repository

```bash
cd /var/www/stratos-staging

# Initialize repo if first deployment
git clone https://github.com/oahumada/Stratos.git .

# Or update existing repo
git fetch origin
git status

# Checkout main branch and pull latest
git checkout main
git pull origin main

# ✅ VERIFY we're on correct tag
git describe --tags
# ✅ EXPECTED: messaging-mvp-staging-v0.4.0
```

**Status Tracker:**

- [ ] Repository cloned or updated
- [ ] On main branch
- [ ] Latest commit pulled (fc4982d8+)
- [ ] Tag verified

---

### 4.2 Install PHP Dependencies

```bash
# Install Composer dependencies (no development packages)
composer install \
  --no-dev \
  --optimize-autoloader \
  --no-interaction

# ✅ VERIFY installation
composer show | head -5
# ✅ EXPECTED: List of installed packages

# Check Laravel installation
php artisan --version
# ✅ EXPECTED: Laravel Framework 12.x.x
```

**Status Tracker:**

- [ ] Composer install completed
- [ ] No development packages installed
- [ ] Autoloader optimized
- [ ] Laravel CLI working

---

### 4.3 Install JavaScript Dependencies & Build

```bash
# Install npm dependencies
npm install --legacy-peer-deps

# ✅ VERIFY npm installation
npm list | head -20
# ✅ EXPECTED: Dependency tree showing Vue, Tailwind, Inertia

# Build Vue components & Tailwind CSS
npm run build

# ✅ VERIFY build output
ls -lh dist/ || ls -lh public/dist/
# ✅ EXPECTED: Compiled CSS and JS files present

# Show build size
du -sh public/dist/ || du -sh dist/
```

**Status Tracker:**

- [ ] npm dependencies installed
- [ ] Build completed successfully
- [ ] dist/ directory has compiled files
- [ ] No build errors

---

### 4.4 Generate Application Keys & Cache

```bash
# Generate APP_KEY if not present
php artisan key:generate --force

# Cache configuration (improves performance)
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# ✅ VERIFY cache files created
ls -lh bootstrap/cache/
# ✅ EXPECTED: config.php, routes.php, views.php present
```

**Status Tracker:**

- [ ] APP_KEY generated
- [ ] config:cache executed
- [ ] route:cache executed
- [ ] view:cache executed
- [ ] All cache files present

---

## 🟢 PHASE 5: DATABASE MIGRATIONS & SEEDING (8 mins)

### 5.1 Run Database Migrations

```bash
# List pending migrations (do NOT apply yet)
php artisan migrate:status

# Run migrations with --force flag (required in staging/production)
php artisan migrate \
  --env=staging \
  --force

# ✅ VERIFY migrations completed
php artisan migrate:status
# ✅ EXPECTED: All migrations show [✓] (completed)

# Show migration log
php artisan migrate:status | grep -E "✓|×"
```

**Status Tracker:**

- [ ] Migration status reviewed
- [ ] Migrations executed with --force
- [ ] No migration errors
- [ ] All migrations marked complete (✓)

---

### 5.2 Database Seeding (Optional but Recommended)

```bash
# Seed messaging data for testing
php artisan db:seed \
  --class=MessagingSeeder \
  --env=staging

# Seed admin operations (optional)
php artisan db:seed \
  --class=AdminOperationsSeeder \
  --env=staging

# Verify data was seeded
php artisan tinker
>>> Message::count()
# ✅ EXPECTED: Number > 0 if seeded successfully
>>> exit()
```

**Status Tracker:**

- [ ] MessagingSeeder executed
- [ ] AdminOperationsSeeder executed (optional)
- [ ] Verify record counts via tinker

---

### 5.3 Initialize Cache & Pre-Warm

```bash
# Clear all caches
php artisan cache:clear
php artisan config:cache

# Pre-populate Redis cache with metrics
php artisan metrics:warm-cache \
  --env=staging

# Verify cache was warmed
php artisan metrics:cache-stats

# ✅ EXPECTED OUTPUT similar to:
# Cache Key Count: 45
# Hit Ratio: 0% (newly warmed)
# Memory Usage: ~2.3 MB
```

**Status Tracker:**

- [ ] Cache cleared
- [ ] config:cache rerun
- [ ] metrics:warm-cache executed
- [ ] Cache stats verified

---

## 🟡 PHASE 6: SERVICE STARTUP & VERIFICATION (8 mins)

### 6.1 Restart Application Services

```bash
# Restart PHP-FPM (handles PHP execution)
sudo systemctl restart php-fpm
sleep 3

# Verify PHP-FPM is running
sudo systemctl status php-fpm
# ✅ EXPECTED: active (running)

# Restart Nginx (web server)
sudo systemctl restart nginx
sleep 3

# Verify Nginx is running
sudo systemctl status nginx
# ✅ EXPECTED: active (running)

# Restart Supervisor (queue workers for async jobs)
sudo systemctl restart supervisor
sleep 3

# Verify Supervisor is running
sudo systemctl status supervisor
# ✅ EXPECTED: active (running)
```

**Status Tracker:**

- [ ] PHP-FPM restarted and running
- [ ] Nginx restarted and running
- [ ] Supervisor restarted and running
- [ ] All services confirmed active

---

### 6.2 Verify Application Health

```bash
# Test HTTP endpoint directly
curl -I https://staging.stratos.app/
# ✅ EXPECTED: HTTP 200 OK

# Test API health endpoint
curl -s https://staging.stratos.app/api/health | jq .
# ✅ EXPECTED: {"status":"ok"}

# Test with authentication (if health endpoint requires it)
curl -H "Authorization: Bearer YOUR_TEST_TOKEN" \
  https://staging.stratos.app/api/health

# Check application logs for errors
tail -50 storage/logs/laravel*.log
# ✅ EXPECTED: No errors or [ERROR] entries (only info/debug)
```

**Status Tracker:**

- [ ] HTTP endpoint responds with 200
- [ ] /api/health returns {"status":"ok"}
- [ ] Application logs show no errors

---

### 6.3 Verify Database Connection

```bash
# Test database connection via Laravel
php artisan tinker
>>> DB::connection()->getPdo()
# ✅ EXPECTED: PDOConnection object (connection successful)

>>> Message::count()
# ✅ EXPECTED: Shows number of messages in DB

>>> User::count()
# ✅ EXPECTED: Shows number of users in DB

>>> exit()
```

**Status Tracker:**

- [ ] Database connection successful
- [ ] Can query Message model
- [ ] Can query User model
- [ ] Data counts as expected

---

### 6.4 Verify Redis Connection

```bash
# Test Redis connection
redis-cli -h staging-redis.internal ping
# ✅ EXPECTED: PONG

# Check Redis stats
redis-cli -h staging-redis.internal INFO stats

# Show cached metrics keys
redis-cli -h staging-redis.internal --pattern "*metrics*" COUNT
# ✅ EXPECTED: Multiple keys present if cache warming worked
```

**Status Tracker:**

- [ ] Redis responds to PING
- [ ] Redis stats accessible
- [ ] Cache warming keys present

---

## 🟢 PHASE 7: SMOKE TESTS & VALIDATION (Ongoing)

### 7.1 API Smoke Tests

```bash
# Auth Token (replace with actual test token)
TOKEN="your_test_bearer_token_here"

# Test 1: Get User Profile
curl -H "Authorization: Bearer $TOKEN" \
  https://staging.stratos.app/api/v1/user
# ✅ EXPECTED: 200 OK with user data

# Test 2: List Messages
curl -H "Authorization: Bearer $TOKEN" \
  https://staging.stratos.app/api/v1/messages
# ✅ EXPECTED: 200 OK with message array

# Test 3: Create Message
curl -X POST \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"conversation_id": 1, "content": "Test from staging deployment"}' \
  https://staging.stratos.app/api/v1/messages
# ✅ EXPECTED: 201 Created with new message

# Test 4: Admin Operations Endpoint
curl -H "Authorization: Bearer $ADMIN_TOKEN" \
  https://staging.stratos.app/api/admin/operations
# ✅ EXPECTED: 200 OK (or 403 if not admin)
```

**Status Tracker:**

- [ ] /api/v1/user endpoint works (200)
- [ ] /api/v1/messages endpoint works (200)
- [ ] Message creation works (201)
- [ ] Admin endpoints work (200 or 403 as expected)

---

### 7.2 Frontend UI Tests (Manual)

**Prerequisites:** Have login credentials for staging account

**Test Flow:**

```
1. Navigate to https://staging.stratos.app
   [ ] Page loads without errors
   [ ] Login form visible

2. Sign in with test account
   [ ] Credentials accepted
   [ ] Redirected to dashboard
   [ ] No JavaScript errors in browser console (F12 → Console tab)

3. Navigate to Messaging feature
   [ ] Messaging menu item visible
   [ ] Click navigates to messaging page
   [ ] Existing conversations load

4. Open existing conversation (or create new)
   [ ] Conversation thread visible
   [ ] Previous messages display correctly

5. Send test message
   [ ] Type message: "Deployment test from staging"
   [ ] Click Send button
   [ ] Message appears in thread immediately
   [ ] No loading errors

6. Refresh page (Ctrl+R or Cmd+R)
   [ ] Page reloads completely
   [ ] Conversation still visible
   [ ] Test message persists in thread

7. Navigate to Admin > Operations (if admin user)
   [ ] Operations page loads
   [ ] Message operations visible
   [ ] Recent test message listed

8. Check browser console for errors
   [ ] Press F12 → Console tab
   [ ] Look for red [ERROR] messages
   [ ] ✅ EXPECTED: Clean console or only INFO/DEBUG messages
```

**Status Tracker:**

- [ ] Login successful
- [ ] Dashboard loads
- [ ] Messaging page accessible
- [ ] Can send messages
- [ ] Messages persist after refresh
- [ ] Admin operations visible
- [ ] Browser console clean (no errors)

---

## 🔵 PHASE 8: DEPLOYMENT SIGN-OFF

### 8.1 Final Verification Checklist

```bash
# Run final comprehensive test
php artisan test --compact

# ✅ EXPECTED: 759+ tests passing in staging environment

# Show deployment summary
echo "=== DEPLOYMENT SUMMARY ==="
echo "Deployment Date: $(date)"
echo "Version: $(git describe --tags)"
echo "Branch: $(git branch --show-current)"
echo "Last Commit: $(git log -1 --oneline)"
echo "Test Status: $(php artisan test --compact 2>&1 | grep -E 'passed|failed')"
echo "Application URL: https://staging.stratos.app"
echo "API Health: $(curl -s https://staging.stratos.app/api/health | jq .status)"
```

**Status Tracker:**

- [ ] All tests passing (759+)
- [ ] Deployment summary confirmed
- [ ] URL accessible and healthy
- [ ] Ready for 24-hour UAT monitoring

---

## ⏰ NEXT STEPS

### Immediate Post-Deployment (Next 1 hour)

1. [ ] **Email Notification:** Notify stakeholders deployment is complete
2. [ ] **Monitoring Setup:** Confirm logs are being monitored
3. [ ] **Alert Configuration:** Verify alerts are active
4. [ ] **Team Notification:** Let QA/UAT team know environment is ready

### UAT Phase (Next 24 hours, Mar 27-28)

1. [ ] Monitor hourly for errors
2. [ ] Check cache hit ratios
3. [ ] Verify message delivery
4. [ ] Monitor response times
5. [ ] Confirm no data loss

### Go/No-Go Decision (Mar 28, 10:00 AM)

1. [ ] Review UAT results
2. [ ] Make production decision
3. [ ] If GO: Proceed to Production Deployment
4. [ ] If NO-GO: Document issues and rollback if needed

---

## 📊 Deployment Metrics

| Metric              | Expected | Actual | Status |
| ------------------- | -------- | ------ | ------ |
| Tests Passing       | 759+     | —      | [ ]    |
| API Health          | OK       | —      | [ ]    |
| Database Connection | OK       | —      | [ ]    |
| Redis Connection    | OK       | —      | [ ]    |
| Frontend Load Time  | < 3s     | —      | [ ]    |
| Message Send Time   | < 1s     | —      | [ ]    |
| Cache Hit Ratio     | > 70%    | —      | [ ]    |
| Error Rate          | < 0.1%   | —      | [ ]    |

---

**Deployment completed on:** **\*\*\*\***\_\_\_**\*\*\*\***  
**Deployed by:** **\*\*\*\***\_\_\_**\*\*\*\***  
**Approved by:** **\*\*\*\***\_\_\_**\*\*\*\***  
**Notes:** **\*\*\*\***\_\_\_**\*\*\*\***

---

_Last Updated: Mar 26, 2026_  
_Next Review: After staging UAT (Mar 28)_
