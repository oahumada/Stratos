# 🔧 TROUBLESHOOTING GUIDE - Messaging MVP Staging Deployment

**Purpose:** Quick reference for common issues during and after deployment  
**Status:** For use during deployment (Mar 27) and UAT (Mar 27-28)  
**Update:** Add issues as they occur  

---

## 🔍 DIAGNOSTIC COMMANDS

**Always start with these before investigating specific issues:**

```bash
# System Status Check
echo "=== SYSTEM STATUS ==="
systemctl status php-fpm nginx supervisor
echo ""

# Application Logs
echo "=== RECENT ERRORS ==="
tail -20 /var/www/stratos-staging/storage/logs/laravel.log | grep -i "error\|exception\|fatal"
echo ""

# Database Check
echo "=== DATABASE ==="
php artisan tinker <<< "DB::selectOne('SELECT 1')" && echo "✅ DB OK" || echo "❌ DB FAILED"
echo ""

# Redis Check
echo "=== REDIS ==="
redis-cli -h staging-redis.internal ping && echo "✅ Redis OK" || echo "❌ Redis FAILED"
echo ""

# Cache Check
echo "=== CACHE ==="
php artisan metrics:cache-stats 2>&1 | head -5
```

---

## ❌ ISSUE 1: Tests Failing During Pre-Deployment

**Symptoms:**
- `php artisan test --compact` shows < 759 tests passing
- Some tests show FAILED or ERROR status
- Output includes red text with assertion failures

**Root Causes:**
1. Database not cleaned between tests
2. Environment variables not set correctly
3. Redis not available during test run
4. Stale test data from previous runs

**Solution:**

```bash
# Step 1: Rebuild test database
php artisan migrate:refresh --env=testing
# ✅ EXPECTED: All migrations refreshed

# Step 2: Clear test cache
php artisan cache:clear
php artisan config:cache

# Step 3: Run tests with verbose output
php artisan test --verbose 2>&1 | tail -20
# Look for specific test that's failing

# Step 4: Run single failing test
php artisan test --filter=FailingTestName --verbose
# Shows detailed error message

# If still failing:
# Step 5: Check environment variables
php artisan config:list | grep APP_KEY
php artisan config:list | grep DB_

# Step 6: Verify Redis is running
redis-cli ping
# ✅ EXPECTED: PONG

# Step 7: Try full test reset
php artisan test --recreate-databases --compact
```

**Prevention:**
- Always run full test suite locally before pushing
- Commit `phpunit.xml` with correct test environment config
- Document any test-specific environment variables in `.env.testing.example`

---

## ❌ ISSUE 2: Deployment Tag Already Exists

**Symptoms:**
```
fatal: tag 'messaging-mvp-staging-v0.4.0' already exists
```

**Root Causes:**
- Tag was created in previous deployment attempt
- Tag exists locally but wasn't deleted before new attempt
- Tag exists on remote but local wasn't synced

**Solution:**

```bash
# Option A: Delete local tag and recreate
git tag -d messaging-mvp-staging-v0.4.0
git tag -a messaging-mvp-staging-v0.4.0 \
  -m "Staging deployment v0.4.0 (retry)"

# Option B: Delete remote tag (CAUTION - affects shared repo)
git push origin --delete messaging-mvp-staging-v0.4.0
git push origin messaging-mvp-staging-v0.4.0

# Option C: Use new version suffix if needed
git tag -a messaging-mvp-staging-v0.4.0-retry \
  -m "Staging deployment v0.4.0 (retry)"
git push origin messaging-mvp-staging-v0.4.0-retry

# Verify tag status
git tag -l | grep messaging-mvp
git ls-remote origin | grep messaging-mvp
```

---

## ❌ ISSUE 3: SSH Connection Fails to Staging Server

**Symptoms:**
```
ssh: connect to host staging.stratos.app port 22: Connection timed out
ssh: connect to host staging-db.internal port 22: Permission denied
```

**Root Causes:**
- SSH key permissions are incorrect (644 instead of 400)
- SSH key not added to ssh-agent
- Network connectivity issue
- Host not reachable
- Auth key not in ~/.ssh/authorized_keys

**Solution:**

```bash
# Step 1: Verify SSH key permissions (must be 400 or 600)
chmod 400 ~/.ssh/staging-key.pem
# NOT 644 or 755 (too permissive)

# Step 2: Test SSH with verbose output
ssh -v -i ~/.ssh/staging-key.pem ubuntu@staging.stratos.app
# Shows detailed connection attempt and errors

# Step 3: Add key to ssh-agent
ssh-add ~/.ssh/staging-key.pem
# Then try connection again

# Step 4: Check network connectivity to host
ping -c 3 staging.stratos.app
# ✅ EXPECTED: packets received

# Step 5: Check if SSH port is open
telnet staging.stratos.app 22
# ✅ EXPECTED: SSH-2.0 banner response

# Step 6: Try alternative SSH options
ssh -i ~/.ssh/staging-key.pem \
  -o StrictHostKeyChecking=accept-new \
  -o ConnectTimeout=10 \
  ubuntu@staging.stratos.app

# Step 7: Add public key to server (if you have sudo access)
cat ~/.ssh/staging-key.pub | ssh ubuntu@staging.stratos.app \
  'mkdir -p ~/.ssh && cat >> ~/.ssh/authorized_keys'
```

**Prevention:**
- Document SSH setup procedure in README
- Use SSH config file (~/.ssh/config) for easier management
- Test SSH connection before (or early in) deployment

---

## ❌ ISSUE 4: .env.staging File Not Found or Permissions Wrong

**Symptoms:**
```
env file [.env.staging] not found
PHP Warning: fopen(.../.env.staging): Permission denied
```

**Root Causes:**
- File not created with correct path
- Permissions too restrictive (not readable by PHP-FPM user)
- File created in wrong directory
- Typo in file location

**Solution:**

```bash
# Step 1: Verify file exists
ls -lh /var/www/stratos-staging/.env.staging
# ✅ EXPECTED: File exists and shows size

# Step 2: Check file permissions
stat /var/www/stratos-staging/.env.staging | grep Access
# Permissions should be similar to: (0600/-rw-------)

# Step 3: Verify PHP-FPM can read it
sudo -u www-data cat /var/www/stratos-staging/.env.staging | head -3
# ✅ EXPECTED: Shows first few lines (not permission denied)

# Step 4: If permissions are wrong, fix them
chmod 600 /var/www/stratos-staging/.env.staging
chown www-data:www-data /var/www/stratos-staging/.env.staging

# Step 5: Verify Laravel can load config
cd /var/www/stratos-staging
php artisan config:list | grep APP_NAME
# ✅ EXPECTED: Shows "Stratos" or your app name

# Step 6: Check that .env.staging is being loaded
php artisan config:list | grep -E "^APP_ENV|^DB_HOST"
# EXPECTED values should match what's in .env.staging
```

**Prevention:**
- Use template .env.staging.example in git
- Document exact permissions required
- Add check to deployment checklist: `ls -l .env.staging`

---

## ❌ ISSUE 5: Composer Install Hangs or Fails

**Symptoms:**
```
Resolving dependencies...
[hangs for > 2 minutes]
Failed to open /packages.json: HTTP/1.1 400 Bad Request
```

**Root Causes:**
- Network connectivity issue to Packagist
- Outdated Composer cache
- Incorrect PHP version
- Disk space full
- Composer authentication token expired

**Solution:**

```bash
# Step 1: Clear Composer cache
composer clear-cache
# ✅ EXPECTED: Cache cleared successfully

# Step 2: Update Composer itself
composer self-update
# ✅ EXPECTED: Composer updated to latest version

# Step 3: Test network connectivity
curl https://packagist.org -I
# ✅ EXPECTED: HTTP/1.1 200 OK

# Step 4: Check disk space
df -h /var/www/stratos-staging
# ✅ EXPECTED: Enough space (at least 500MB free)

# Step 5: Try install with verbose output
composer install -vvv 2>&1 | tail -50
# Shows detailed information about what's failing

# Step 6: Install specific problematic package
composer require laravel/framework:^12.0 --no-update
composer update laravel/framework -vvv

# Step 7: If all else fails, regenerate lock file
composer update --no-scripts
# Then restart PHP-FPM

# Step 8: Check PHP version compatibility
composer diagnose | grep -i "php version"
# PHP version must match composer.json requirements
```

**Prevention:**
- Test composer install locally before deployment
- Document all PHP version requirements
- Add "composer clear-cache" to deployment checklist

---

## ❌ ISSUE 6: Database Migrations Fail

**Symptoms:**
```
SQLSTATE[42P09]: Exception: 7 ERROR: relation "messages" already exists
SQLSTATE[HY000]: General error: 1 Could not freshly create schema
```

**Root Causes:**
- Table already exists from previous migration
- Migration has syntax error (invalid SQL)
- Database user doesn't have CREATE TABLE permission
- Connection string is wrong

**Solution:**

```bash
# Step 1: Check migration status
php artisan migrate:status
# Shows which migrations have run

# Step 2: If table exists, either reset or skip
# Option A: Reset database (DESTRUCTIVE - ONLY in staging!)
php artisan migrate:refresh --env=staging --force
# This rolls back all migrations and re-runs them

# Option B: Install from scratch (most nuclear)
php artisan migrate:reset --env=staging --force
# Then: php artisan migrate --env=staging --force

# Step 3: Verify database connection first
php artisan tinker
>>> DB::connection()->getPdo()
# ✅ EXPECTED: PDOConnection object

# Step 4: Check specific migration for errors
php artisan migrate:status | grep -i "pending\|failed"
# See which migration is causing issue

# Step 5: Run single problematic migration
php artisan migrate --path=database/migrations/2026_03_26_*.php --env=staging --force --verbose

# Step 6: Manually check table in database
psql -h staging-db.internal -U postgres -d stratos_staging -c "\dt"
# Shows all tables in database

# Step 7: If table needs to be dropped first
psql -h staging-db.internal -U postgres -d stratos_staging << EOF
DROP TABLE IF EXISTS messages CASCADE;
DROP TABLE IF EXISTS conversations CASCADE;
EOF
# Then retry migration
```

**Prevention:**
- Test migrations on local database replica first
- Keep migrations idempotent (can run multiple times safely)
- Use `if not exists` clauses in migrations
- Review migration SQL before pushing

---

## ❌ ISSUE 7: npm Build Fails

**Symptoms:**
```
npm ERR! code ERESOLVE
npm ERR! ERESOLVE unable to resolve dependency tree
npm ERR! npm ERR! While resolving: stratos@1.0.0
```

**Root Causes:**
- Node.js version incompatible
- Incompatible peer dependencies
- npm cache corrupted
- package-lock.json conflicts with package.json

**Solution:**

```bash
# Step 1: Check Node and npm versions
node --version  # Should be 18+
npm --version   # Should be 8+

# Step 2: Clear npm cache
npm cache clean --force

# Step 3: Delete node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Step 4: If still fails, use legacy peer deps flag
npm install --legacy-peer-deps

# Step 5: Build after successful install
npm run build
# ✅ EXPECTED: Compiled assets in dist/ or public/dist/

# Step 6: If build fails, check for TypeScript errors
npm run build -- --verbose 2>&1 | grep -i "error\|failed"

# Step 7: Clean build
rm -rf dist/ public/dist/ .nuxt/
npm run build

# Step 8: Nuclear option (only if desperate)
rm -rf node_modules package-lock.json
npm install --legacy-peer-deps
npm cache verify
npm run build
```

**Prevention:**
- Lock Node version in .nvmrc or .node-version
- Use `npm ci` instead of `npm install` in CI/CD
- Test npm build locally with same Node version

---

## ❌ ISSUE 8: Redis Connection Fails

**Symptoms:**
```
Redis [Connection refused] could not connect to Redis at 127.0.0.1:6379
REDIS ERRORR: ECONNREFUSED Connection refused
```

**Root Causes:**
- Redis not running
- Redis host/port misconfigured in .env
- Redis authentication required but password not set
- Firewall blocking Redis port

**Solution:**

```bash
# Step 1: Check if Redis is running
redis-cli -h staging-redis.internal ping
# ✅ EXPECTED: PONG

# Step 2: If not running, start it
sudo systemctl start redis-server
# Or: redis-server (if running in foreground)

# Step 3: Check Redis status
sudo systemctl status redis-server
# ✅ EXPECTED: active (running)

# Step 4: Verify connection from staging app server
redis-cli -h staging-redis.internal -a YOUR_PASSWORD ping
# ✅ EXPECTED: PONG (if password required)

# Step 5: Check Redis configuration in .env
grep -E "REDIS_" /var/www/stratos-staging/.env.staging

# Step 6: Test Laravel Redis connection
cd /var/www/stratos-staging
php artisan tinker
>>> Cache::put('test', 'value', 60)
>>> Cache::get('test')
// ✅ EXPECTED: Returns 'value'

# Step 7: Check Redis memory and info
redis-cli -h staging-redis.internal INFO stats

# Step 8: If authentication failing
redis-cli -h staging-redis.internal -a "$REDIS_PASSWORD" ping
# Replace $REDIS_PASSWORD with actual password

# Step 9: Network connectivity to Redis host
telnet staging-redis.internal 6379
# ✅ EXPECTED: Connected (then press Ctrl+])
```

**Prevention:**
- Document Redis host/port/password in deployment guide
- Test Redis connection in pre-deployment checklist
- Add Redis health check to monitoring

---

## ❌ ISSUE 9: Messages Not Appearing in UI

**Symptoms:**
- Messaging page loads but no messages in thread
- Send message completes but doesn't show up
- Empty conversation list despite seeded data
- API returns messages but UI shows nothing

**Root Causes:**
- Database seeding didn't run or failed
- Cache is stale and serving old/wrong data
- Frontend Vue component has JavaScript error
- API response data structure wrong
- Message scoping by organization_id filtering them out

**Solution:**

```bash
# Step 1: Verify data in database
php artisan tinker
>>> Message::count()
# ✅ EXPECTED: > 0 (at least some messages)

>>> Message::first()->toArray()
# Verify message structure is correct

# Step 2: Clear and rebuild cache
php artisan cache:clear
php artisan config:cache

# Step 3: Verify API returns messages
curl -H "Authorization: Bearer $TOKEN" \
  https://staging.stratos.app/api/v1/messages | jq .

# Step 4: Check browser console for errors
# Open https://staging.stratos.app in browser
# Press F12 → Console tab
# Look for red [ERROR] messages
# Common errors:
#   - "Uncaught ReferenceError: component not defined"
#   - "Permission denied" (auth issue)
#   - "Cannot read property of undefined"

# Step 5: Check API response structure
curl -H "Authorization: Bearer $TOKEN" \
  https://staging.stratos.app/api/v1/messages | jq '.data | .[0]'
# Check response has: id, content, conversation_id, user_id, created_at

# Step 6: Verify organization_id scoping
php artisan tinker
>>> $org_id = 1
>>> Message::where('organization_id', $org_id)->count()
# Check if messages filtered by org

# Step 7: Check Vue component rendering
# In browser F12 → Console, run:
// Vue.createApp gives access to app instance
// Check if messages prop contains data

# Step 8: Reseed messages table completely
php artisan db:seed --class=MessagingSeeder --force

# Step 9: Full cache warming
php artisan metrics:warm-cache --org-id=1
```

**Prevention:**
- Test full message flow: seed → API → UI
- Add browser console error check to smoke tests
- Log API responses for debugging
- Add frontend unit tests for message component

---

## ❌ ISSUE 10: Service Restart Fails or Hangs

**Symptoms:**
```
Job for php-fpm.service failed because the control process exited with error code
systemctl: error reading /var/run/php-fpm.pid: Permission denied
Process already running on port 9000
```

**Root Causes:**
- Service already running (double-restart attempt)
- PID file corrupted or locked
- Port already in use by another process
- Permissions issue on service files

**Solution:**

```bash
# Step 1: Check current service status
systemctl status php-fpm | head -20

# Step 2: If stuck, forcefully kill the service
sudo pkill -9 php-fpm
# Then restart
sudo systemctl start php-fpm

# Step 3: Check for stale PID file
sudo rm -f /var/run/php-fpm.pid
# Then restart
sudo systemctl start php-fpm

# Step 4: Check if port is already in use
sudo lsof -i :9000
# Shows what's running on port 9000
# Kill if needed: sudo kill -9 <PID>

# Step 5: Check service logs for errors
sudo journalctl -u php-fpm -n 50 --no-pager
# Shows last 50 lines of service logs

# Step 6: Verify service config syntax
php-fpm -t
# ✅ EXPECTED: configuration file test successful

# Step 7: Start PHP-FPM in debug mode
sudo php-fpm -D --nodaemonize
# Runs in foreground, shows errors immediately
# Use Ctrl+C to stop

# Step 8: Restart all related services in order
sudo systemctl restart nginx
sleep 2
sudo systemctl restart php-fpm
sleep 2
sudo systemctl restart supervisor

# Step 9: Verify all are running
systemctl status php-fpm nginx supervisor | grep -E "active|failed"
```

**Prevention:**
- Use `systemctl restart` instead of stop/start separately
- Check logs before restarting
- Test restart procedure on lower environment first

---

## ⚡ QUICK REFERENCE TABLE

| Issue | Symptom | Quick Fix |
|-------|---------|-----------|
| Tests fail | < 759 passing | `php artisan migrate:refresh --env=testing` |
| Tag exists | "tag already exists" | `git tag -d {tag} && git tag -a ...` |
| SSH fails | Connection timeout | `chmod 400 ~/.ssh/key.pem && ssh-add ...` |
| .env missing | "env file not found" | `ls -lh .env.staging && chmod 600` |
| Composer hangs | Resolving > 2 min | `composer clear-cache && composer install` |
| Migration fails | "relation already exists" | `php artisan migrate:refresh --force` |
| npm fails | ERESOLVE | `npm install --legacy-peer-deps` |
| Redis fails | ECONNREFUSED | `redis-cli ping && systemctl status redis-server` |
| No messages | UI empty | `php artisan tinker → Message::count()` |
| Service fails | Job failed | `sudo pkill -9 php-fpm && systemctl start php-fpm` |

---

## 📞 Who to Contact

| Issue Category | Contact | Channel |
|---|---|---|
| Deployment infra | DevOps team | Telegram devops |
| Database problems | DB team | Telegram database |
| Laravel/Backend | Backend lead | Telegram backend |
| Vue/Frontend issues | Frontend lead | Telegram frontend |
| Redis/Cache issues | Infrastructure | Telegram devops |
| Permission/SSH issues | System admin | Email or Telegram |

---

**Last Updated:** Mar 26, 2026  
**Add new issues as they occur**

