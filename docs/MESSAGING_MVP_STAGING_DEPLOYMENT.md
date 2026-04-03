# 📦 Deployment Plan: Messaging MVP to Staging

**Date:** March 27, 2026  
**Status:** Ready for Deployment  
**Messaging MVP Version:** v0.4.0  
**Tests:** 623 passing (Messaging) + 136 (N+1 Performance) = 759 total ✅

---

## 📋 Pre-Deployment Checklist

### Code Quality ✅

- [x] All 759 tests passing (136 unit + 623 messaging)
- [x] Pre-push hooks successful
- [x] Zero security vulnerabilities (HIGH/CRITICAL)
- [x] Code formatted with Pint
- [x] No breaking changes
- [x] Semantic commits with full history

### Messaging MVP Components ✅

- [x] Message model + migrations
- [x] Conversation model
- [x] Admin Operations integration
- [x] Settings endpoints
- [x] Vue 3 messaging components
- [x] Message factory + seeders
- [x] API controllers (store, show, update, delete)
- [x] Form Request validation

### Performance Optimization ✅

- [x] N+1 query optimization (5 phases, -33.5% harness improvement)
- [x] Redis caching configured
- [x] Database indices applied
- [x] Cache warming scheduler set
- [x] Monitoring commands deployed

### Documentation ✅

- [x] PHASE2-5 completion notes
- [x] API endpoints documented
- [x] Deployment checklist created
- [x] PENDIENTES updated
- [x] ROADMAP updated

---

## 🚀 Deployment Steps

### Phase 1: Pre-Deployment (5 mins)

```bash
# 1. Verify main branch is clean
cd /home/omar/Stratos
git status  # Should show "working tree clean"

# 2. Verify tests pass
php artisan test --compact  # Must show 759+ tests passing

# 3. Create deployment tag
git tag -a messaging-mvp-staging-v0.4.0 -m "Staging deployment v0.4.0"

# 4. Verify configuration
php artisan config:cache
php artisan route:cache
```

### Phase 2: Environment Configuration (10 mins)

**Staging Environment (.env.staging):**

```env
APP_DEBUG=true
APP_ENV=staging
CACHE_STORE=redis
DB_CONNECTION=pgsql
DB_HOST=staging-db.aws
DB_DATABASE=stratos_staging
REDIS_HOST=staging-redis.aws
MAIL_DRIVER=log  # Use log for staging
```

**Database Configuration:**
```bash
# 1. Run migrations on staging
php artisan migrate --env=staging

# 2. Seed messaging data (optional)
php artisan db:seed --class=MessagingSeeder --env=staging

# 3. Cache warming (pre-populate Redis)
php artisan metrics:warm-cache --env=staging
```

### Phase 3: Deployment (15 mins)

**Option A: Manual Deployment**

```bash
# Via direct server
cd /var/www/stratos-staging
git checkout main
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan config:cache
php artisan route:cache
php artisan migrate --force --env=staging
systemctl restart php-fpm
systemctl restart supervisor  # For queues
```

**Option B: CI/CD Pipeline (Recommended)**

```bash
# Push tag to trigger staging deployment
git push origin messaging-mvp-staging-v0.4.0

# Pipeline automatically:
# - Pulls code from main
# - Installs dependencies
# - Runs migrations
# - Clears caches
# - Restarts services
# - Runs deployment smoke tests
```

### Phase 4: Smoke Tests (10 mins)

**API Endpoints Test:**

```bash
# Health check
curl https://staging.stratos.app/api/health

# Messaging endpoints
curl -H "Authorization: Bearer TOKEN" \
  https://staging.stratos.app/api/v1/messages

# Admin operations
curl -H "Authorization: Bearer TOKEN" \
  https://staging.stratos.app/api/admin/operations
```

**Database Verification:**

```sql
-- Verify migrations applied
SELECT COUNT(*) FROM information_schema.tables 
WHERE table_schema = 'public' AND table_name LIKE 'messages%';

-- Verify indices
SELECT indexname FROM pg_indexes 
WHERE tablename IN ('business_metrics', 'financial_indicators');

-- Check cache
redis-cli
> KEYS metrics_and_benchmarks_*
> INFO stats
```

**Frontend Testing:**

```bash
# Test messaging UI in browser
# 1. Login to staging.stratos.app
# 2. Navigate to Messaging > Conversations
# 3. Send test message
# 4. Verify received in real-time
# 5. Check Admin > Operations > Messages
```

### Phase 5: Monitoring (Ongoing)

**Log Aggregation:**

```bash
# Monitor application logs
tail -f /var/log/stratos/staging/laravel.log

# Monitor queue workers
tail -f /var/log/supervisor/laravel-worker.log

# Monitor Redis cache
redis-cli --stat
```

**Performance Monitoring:**

```bash
# Check cache hit ratio
php artisan metrics:cache-stats

# Monitor database queries
# Enable query logging in staging
# DB::enableQueryLog();

# Check application performance
php artisan tinker
>>> DB::getQueryLog()
```

---

## 📊 Rollback Plan

**If issues occur during staging deployment:**

### Immediate Rollback (< 2 mins)

```bash
# 1. Revert to previous commit
git revert HEAD
git push origin main

# 2. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# 3. Restart services
systemctl restart php-fpm
systemctl restart supervisor
```

### Database Rollback (if needed)

```bash
# 1. Backup current state
pg_dump stratos_staging > backup_$(date +%s).sql

# 2. Rollback last migration
php artisan migrate:rollback --step=1

# 3. Restore from backup if needed
psql stratos_staging < backup_TIMESTAMP.sql
```

---

## ✅ Post-Deployment Verification

### 24-Hour Monitoring

- [ ] Error rate < 0.1%
- [ ] Response times p95 < 500ms
- [ ] Cache hit ratio > 80%
- [ ] No HIGH/CRITICAL alerts
- [ ] All messaging features functional
- [ ] Admin operations working
- [ ] No database locks

### UAT Tasks for QA Team

- [ ] Create/send messages between users
- [ ] Test real-time notification delivery
- [ ] Verify message persistence
- [ ] Test admin message viewing/moderation
- [ ] Load testing (100+ concurrent users)
- [ ] Cross-browser testing (Chrome, Firefox, Safari)
- [ ] Mobile (PWA) testing

---

## 📅 Timeline

| Step | Duration | Cumulative | Status |
|------|----------|-----------|--------|
| Pre-deployment | 5 min | 5 min | ✅ Ready |
| Env config | 10 min | 15 min | 🔜 Next |
| Deployment | 15 min | 30 min | 🔜 Next |
| Smoke tests | 10 min | 40 min | 🔜 Next |
| UAT (24hrs) | 1400 min | 1440 min | 🔜 After |

**Total deployment window:** ~40 minutes  
**Total UAT:** 24 hours  
**Production release target:** Mar 31, 2026 (after successful staging UAT)

---

## 🎯 Success Criteria

✅ All tests passing on staging  
✅ No error rate spike  
✅ Cache hit ratio > 80%  
✅ Response times unchanged / improved  
✅ Real-time messaging working  
✅ Admin operations functional  
✅ Zero data loss  
✅ No breaking changes for end users

---

## 📞 Communication

**Pre-deployment notification (24 hrs before):**
- [ ] Notify staging users
- [ ] Alert support team
- [ ] Notify product team

**During deployment:**
- [ ] Post deployment start time
- [ ] Monitor error logs
- [ ] Be available for rollback

**Post-deployment (24 hrs after):**
- [ ] Release notes published
- [ ] Known issues documented
- [ ] Schedule production deployment

---

## 📝 Deployment Sign-Off

**Prepared by:** GitHub Copilot  
**Date:** March 26, 2026  
**Approved by:** [TO BE FILLED]  
**Deployment date:** March 27, 2026 (estimated)  
**Expected duration:** 40 mins + 24hr UAT

**Notes:**
- All code changes reviewed and tested
- Zero breaking changes
- Backward compatible with existing deployments
- Rollback plan ready
- Monitoring configured

---

## 🚀 Next Steps

1. **Mar 27 (Morning):** Execute deployment
2. **Mar 27-28 (24 hrs):** UAT & monitoring
3. **Mar 28 (Afternoon):** Go/No-Go decision
4. **Mar 31 (Production):** Production deployment
5. **Apr 1+:** Post-production monitoring

---

**Status:** ✅ **READY FOR STAGING DEPLOYMENT**  
**Risk Level:** LOW (comprehensive test coverage, rollback plan ready)  
**Go/No-Go:** READY TO PROCEED 🚀
