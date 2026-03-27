# 📊 MONITORING GUIDE - Messaging MVP Staging Deployment

**Purpose:** Real-time monitoring during UAT phase (Mar 27-28, 2026)  
**Duration:** Continuous 24-hour monitoring post-deployment  
**Alert Owner:** DevOps/Operations team  
**Escalation:** To Backend/Frontend leads if thresholds exceeded

---

## 🎯 Monitoring Objectives

| Objective               | Why                             | Success Metric               |
| ----------------------- | ------------------------------- | ---------------------------- |
| **Availability**        | Catch outages immediately       | Uptime > 99.5%               |
| **Performance**         | Detect slow queries/bottlenecks | Response p95 < 500ms         |
| **Errors**              | Identify bugs/issues early      | Error rate < 0.1%            |
| **Data Integrity**      | Verify zero message loss        | All created messages persist |
| **Cache Effectiveness** | Validate N+1 optimization       | Hit ratio > 80%              |
| **Resource Usage**      | Prevent OOM/CPU exhaustion      | CPU < 70%, Memory < 80%      |

---

## 🔴 CRITICAL ALERTS (Immediate Action Required)

### Alert 1: Application Down (HTTP 5xx)

**Trigger:** Application returns HTTP 500 or 503  
**Check Frequency:** Every 1 minute  
**Threshold:** Any occurrence  
**Response Time:** Investigate within 5 minutes

**Monitor Command:**

```bash
# Check every 30 seconds
watch -n 30 \
  'curl -s -o /dev/null -w "%{http_code}" https://staging.stratos.app/api/health; echo ""'

# ✅ EXPECTED: 200 consistently
# ❌ ALERT IF: 500, 503, or timeout
```

**When Alert Triggers:**

```bash
# 1. Check application logs immediately
tail -100 /var/www/stratos-staging/storage/logs/laravel.log | grep -i "error\|exception\|fatal"

# 2. Check database connection
php artisan tinker <<< "DB::Connection 1"

# 3. Check Redis connection
redis-cli -h staging-redis.internal ping

# 4. Check service status
systemctl status php-fpm nginx supervisor | grep -E "active|failed"

# 5. Restart if needed (see Troubleshooting Guide)
sudo systemctl restart php-fpm nginx

# 6. Notify via Telegram: devops-alerts group
```

---

### Alert 2: Database Connection Failure

**Trigger:** Cannot connect to PostgreSQL  
**Check Frequency:** Every 2 minutes  
**Threshold:** Any failed connection  
**Response Time:** Investigate within 5 minutes

**Monitor Command:**

```bash
# Create monitoring script
cat > /usr/local/bin/monitor-db.sh << 'EOF'
#!/bin/bash
php artisan tinker <<< "
try {
    DB::selectOne('SELECT 1');
    echo 'OK';
} catch (\Exception \$e) {
    echo 'FAILED: ' . \$e->getMessage();
    exit(1);
}
"
EOF

chmod +x /usr/local/bin/monitor-db.sh

# Run every 2 minutes via cron
# Add to crontab: */2 * * * * /usr/local/bin/monitor-db.sh
```

**When Alert Triggers:**

```bash
# 1. Verify database host is reachable
ping -c 3 staging-db.internal

# 2. Test PostgreSQL connection directly
psql -h staging-db.internal -U postgres -d stratos_staging -c "SELECT 1"

# 3. Check database server status (contact DB team)
# From database server: sudo systemctl status postgresql

# 4. Retry connection
php artisan migrate:status  # Forces connection test

# 5. If failed: Notify DB team immediately
```

---

### Alert 3: Redis Connection Failure

**Trigger:** Cannot connect to Redis cache  
**Check Frequency:** Every 2 minutes  
**Threshold:** Any failed connection  
**Response Time:** Immediate (affects multiple services)

**Monitor Command:**

```bash
# Simple Redis ping check
redis-cli -h staging-redis.internal ping

# ✅ EXPECTED: PONG
# ❌ ALERT IF: (error) or timeout

# Automated check script
cat > /usr/local/bin/monitor-redis.sh << 'EOF'
#!/bin/bash
RESPONSE=$(redis-cli -h staging-redis.internal ping 2>&1)
if [[ $RESPONSE != "PONG" ]]; then
    echo "ALERT: Redis down - $RESPONSE" | mail -s "Redis Alert" ops@stratos.app
    exit 1
fi
EOF

chmod +x /usr/local/bin/monitor-redis.sh
```

**When Alert Triggers:**

```bash
# 1. Check Redis status on cache server
redis-cli -h staging-redis.internal INFO stats

# 2. Check memory usage (might be full)
redis-cli -h staging-redis.internal INFO memory | grep used_memory_human

# 3. Restart Redis if needed (on cache server)
sudo systemctl restart redis-server

# 4. Verify Laravel cache works
php artisan cache:put test value 60
php artisan cache:get test
# ✅ EXPECTED: Returns 'value'

# 5. Check for connection limit issues
redis-cli -h staging-redis.internal INFO clients | grep connected_clients
```

---

### Alert 4: Error Rate > 1%

**Trigger:** Application error rate exceeds 1%  
**Check Frequency:** Every 5 minutes  
**Threshold:** Errors > 1% of total requests  
**Response Time:** Investigate within 10 minutes

**Monitor Command:**

```bash
# Calculate error rate from logs (last hour)
cd /var/www/stratos-staging

# Count total requests
TOTAL=$(grep -c "GET\|POST\|PUT\|DELETE" storage/logs/laravel.log)

# Count error requests (HTTP 500, exceptions)
ERRORS=$(grep -c "ERROR\|Exception\|FATAL" storage/logs/laravel.log)

# Calculate percentage
if [ $TOTAL -gt 0 ]; then
    ERROR_RATE=$((ERRORS * 100 / TOTAL))
    echo "Error Rate: $ERROR_RATE%"
    if [ $ERROR_RATE -gt 1 ]; then
        echo "⚠️ ALERT TRIGGERED: Error rate $ERROR_RATE%"
    fi
fi

# Alternative: Use structured logging if available
jq '.level' storage/logs/laravel.json 2>/dev/null | sort | uniq -c | grep -i error
```

**When Alert Triggers:**

```bash
# 1. Identify specific errors
tail -50 storage/logs/laravel.log | grep -E "ERROR|Exception|FATAL" | head -10

# 2. Identify affected endpoints
grep -E "GET|POST" storage/logs/laravel.log | grep -i error | awk '{print $1, $2}' | sort | uniq -c

# 3. Check for specific pattern (e.g., all message creation fails)
grep -i "messages" storage/logs/laravel.log | grep -i error

# 4. Restart PHP-FPM if errors persist
sudo systemctl restart php-fpm

# 5. If errors continue: Trigger rollback (see Rollback Guide)
```

---

## 🟡 WARNING ALERTS (Investigate Within 30 mins)

### Alert 5: Response Time p95 > 500ms

**Trigger:** 95th percentile response time exceeds 500ms  
**Check Frequency:** Every 10 minutes  
**Threshold:** p95 > 500ms for more than 2 consecutive checks  
**Action:** Investigate and optimize

**Monitor Command:**

```bash
# Calculate response time from Nginx logs
# (Assumes Nginx configured with response time logging)

tail -1000 /var/log/nginx/staging-stratos.access.log | \
  awk '{print $NF}' | \
  sort -n | \
  awk '
  {a[NR]=$1}
  END {
    p50=a[int(NR*0.50)];
    p95=a[int(NR*0.95)];
    p99=a[int(NR*0.99)];
    print "p50:", p50*1000 "ms";
    print "p95:", p95*1000 "ms";
    print "p99:", p99*1000 "ms";
  }'

# ✅ EXPECTED: p95 < 500ms
# ⚠️ ALERT IF: p95 > 500ms
```

**When Alert Triggers:**

```bash
# 1. Identify slow endpoints
tail -500 /var/log/nginx/staging-stratos.access.log | \
  awk '{print $7, $NF}' | \
  sort -k2 -n | \
  tail -20
# Shows slowest endpoints

# 2. Check for slow database queries
php artisan tinker
>>> DB::listen(function($query) {
      if ($query->time > 100) {  // Queries > 100ms
        echo $query->sql . '\n';
      }
    });

# 3. Check cache hit ratio (should be > 80%)
php artisan metrics:cache-stats | grep "Hit Ratio"

# 4. If cache hit ratio low, warm cache
php artisan metrics:warm-cache

# 5. Monitor N+1 optimization metrics
# Check if query count increased unexpectedly
php artisan tinker
>>> Spatie\QueryLogger\QueryLogger::getStore()->count()
```

---

### Alert 6: Cache Hit Ratio < 70%

**Trigger:** Redis cache serving < 70% of cache attempts  
**Check Frequency:** Every 15 minutes  
**Threshold:** Hit ratio < 70% for 2+ consecutive checks  
**Action:** Investigate cache invalidation or warming

**Monitor Command:**

```bash
# Get cache statistics
php artisan metrics:cache-stats

# ✅ EXPECTED OUTPUT similar to:
# Cache Keys: 45
# Keys Expired: 2
# Hit Ratio: 85.3%
# Memory: 2.3 MB / 128 MB
# TTL: 600 seconds

# ⚠️ ALERT IF: Hit Ratio < 70%

# Detailed Redis stats
redis-cli -h staging-redis.internal INFO stats | grep -E "total_commands_processed|keyspace|used_memory"
```

**When Alert Triggers:**

```bash
# 1. Check for cache invalidation cascade
grep -i "cache\|invalidat\|expired" /var/www/stratos-staging/storage/logs/laravel.log | tail -20

# 2. Verify observers are running (model events fire correctly)
php artisan tinker
>>> DB::listen(function($query) { echo $query->sql . '\n'; });
>>> $msg = Message::create(['conversation_id'=>1, 'content'=>'Test']);
# Should see cache invalidation queries

# 3. Manually warm cache
php artisan metrics:warm-cache --verbose

# 4. Check cache TTL (should be 600 seconds)
redis-cli -h staging-redis.internal TTL metrics_cache_key
# ✅ EXPECTED: TTL > 0 (showing seconds remaining)

# 5. Increase cache warming frequency if needed
# Modify bootstrap/app.php:
# $schedule->command('metrics:warm-cache')->everyThirtyMinutes();  // Instead of twice daily
```

---

### Alert 7: Memory Usage > 80%

**Trigger:** Server memory > 80% utilized  
**Check Frequency:** Every 5 minutes  
**Threshold:** Memory > 80% for 3+ consecutive checks  
**Action:** Investigate memory leak or optimize

**Monitor Command:**

```bash
# Check system memory
free -h
# Shows: Total, Used, Free

# Calculate memory percentage
free | awk '/^Mem:/ {printf("Memory Usage: %.1f%%\n", $3*100/$2)}'

# ✅ EXPECTED: < 80%
# ⚠️ ALERT IF: > 80%

# Check memory by process
ps aux --sort=-%mem | head -10
# Shows top memory-using processes
```

**When Alert Triggers:**

```bash
# 1. Check Laravel memory usage
php artisan tinker
>>> memory_get_peak_usage(true) / (1024 * 1024)  # Output in MB

# 2. Check if specific process is memory hog
ps aux --sort=-%mem | grep php
ps aux --sort=-%mem | grep redis
ps aux --sort=-%mem | grep nginx

# 3. Check for memory leaks in recent code changes
grep -r "while.*true\|for.*;;do" app/
# Look for infinite loops or memory-consuming patterns

# 4. Restart memory-heavy services
sudo systemctl restart php-fpm  # Recycles PHP processes

# 5. Check Redis memory
redis-cli -h staging-redis.internal INFO memory | grep used_memory_human
# If > 1GB, may need to:
#   - Reduce cache TTL
#   - Flush expired keys: redis-cli FLUSHDB

# 6. Increase system swap if available (temporary measure)
# sudo swapon -s  # Check current swap
```

---

### Alert 8: CPU Usage > 70%

**Trigger:** Server CPU > 70% sustained  
**Check Frequency:** Every 5 minutes  
**Threshold:** CPU > 70% for 2+ consecutive checks  
**Action:** Investigate resource-intensive operations

**Monitor Command:**

```bash
# Check CPU usage
top -b -n 1 | head -15
# Shows CPU and load average

# Check which processes are CPU-intensive
ps aux --sort=-%cpu | head -10

# ✅ EXPECTED: < 70% sustained
# ⚠️ ALERT IF: > 70%

# Check load average
uptime
# Format: "... load average: 0.5, 0.4, 0.3" (1min, 5min, 15min)
# On 4-core system: load > 4 means overloaded
```

**When Alert Triggers:**

```bash
# 1. Identify CPU-heavy processes
ps aux --sort=-%cpu | head -5

# 2. If Laravel (php-fpm):
#    - Check for expensive queries
php artisan tinker
>>> DB::listen(function($q) {
  if ($q->time > 500) echo $q->sql . ' took ' . $q->time . "ms\n";
});

# 3. If Nginx: Check for too many concurrent connections
netstat -an | grep ESTABLISHED | wc -l

# 4. If Redis: Check for large operations
redis-cli -h staging-redis.internal --bigkeys

# 5. Restart services if CPU stays high
sudo systemctl restart php-fpm
sudo systemctl restart nginx

# 6. Scale horizontally if load is legitimate
# (Contact DevOps for load balancer adjustment)
```

---

### Alert 9: Disk Usage > 85%

**Trigger:** Disk space < 15% available  
**Check Frequency:** Every 30 minutes  
**Threshold:** > 85% used  
**Action:** Clean up logs and old databases

**Monitor Command:**

```bash
# Check disk usage
df -h /var/www/stratos-staging

# Detailed disk analysis
du -sh /var/www/stratos-staging/*

# ✅ EXPECTED: > 20% free space
# ⚠️ ALERT IF: < 15% free space
```

**When Alert Triggers:**

```bash
# 1. Find largest files/directories
du -sh /var/www/stratos-staging/* | sort -h | tail -10

# 2. Clean up old logs
rm -rf /var/www/stratos-staging/storage/logs/*.log
# Or compress: gzip /var/www/stratos-staging/storage/logs/*.log

# 3. Clean up database backups (if old)
rm -f /var/backups/stratos/*_old.sql

# 4. Clear application cache
php artisan cache:clear
php artisan config:clear

# 5. If still full: Check for large temp files
find /tmp -type f -larger 100M -exec ls -lh {} \;
```

---

## 🟢 INFORMATIONAL METRICS (Track but No Alert)

### Metric 1: Message Delivery Rate

**What to Track:** Percentage of messages successfully created and delivered  
**Expected:** 100% (every created message appears in thread)

**Monitor Command:**

```bash
# Historical tracking
# Log:
# - Total messages attempted (from logs or API)
# - Messages visible in UI (manual or API check)
# - Delivery rate = visible / attempted

# Example: Daily report
echo "=== Message Delivery Report ==="
echo "Date: $(date)"
ATTEMPTS=$(grep -c "POST.*messages" /var/log/nginx/staging-stratos.access.log)
echo "Create attempts: $ATTEMPTS"
# (Verify manually or via database count)
CREATED=$(php artisan tinker <<< "echo Message::where('created_at', '>=', now()->subDay())->count();")
echo "Actually created: $CREATED"
echo "Delivery rate: $(echo "scale=2; $CREATED * 100 / $ATTEMPTS" | bc)%"
```

---

### Metric 2: N+1 Query Count

**What to Track:** Number of database queries per request (should be stable after optimization)  
**Expected:** ~6-7 consolidated queries (down from 12 baseline)

**Monitor Command:**

```bash
# Verify query count is maintained
php artisan tinker
>>> Debugbar::enable();
>>> // Load page via browser
>>> // Check Laravel Debugbar in browser for query count

# Or via code:
>>> DB::listen(function($query) {
  // Track query count
  static $count = 0;
  $count++;
  echo "$count: {$query->sql}\n";
});
```

---

### Metric 3: Cache Warming Duration

**What to Track:** How long cache warming command takes to execute  
**Expected:** < 30 seconds

**Monitor Command:**

```bash
# Time cache warming command
time php artisan metrics:warm-cache

# ✅ EXPECTED: real 0m15s (30 seconds max)
# ⚠️ WARNING IF: > 45 seconds (may indicate slow DB or network)
```

---

## 📋 MONITORING CHECKLIST (Every 1 Hour During UAT)

```
⏰ Hourly checks (do every hour for 24 hours):

[ ] Application health (curl /api/health → 200)
[ ] Database connection (php artisan migrate:status)
[ ] Redis cache (redis-cli ping → PONG)
[ ] Error rate (grep ERROR logs → < 1%)
[ ] Response time p95 (nginx logs → < 500ms)
[ ] Cache hit ratio (metrics:cache-stats → > 70%)
[ ] Memory usage (free -h → < 80%)
[ ] CPU usage (top → < 70%)
[ ] Disk usage (df -h → < 85%)
[ ] Message delivery (send test message → appears in UI)
[ ] Admin operations (can view operations in admin panel)
[ ] Log size (du /storage/logs → clean up if > 500MB)

⏰ Daily checklist (do once per day):

[ ] Test full user journey (login → message → navigate)
[ ] Verify no data loss (all users/messages intact)
[ ] Check performance trend (compare p95 across hours)
[ ] Review error patterns (any recurring issues)
[ ] Test backup/recovery (can restore from backup)
[ ] Verify monitoring scripts running (cron jobs)
```

---

## 🚨 ESCALATION MATRIX

| Severity | Issue                   | Escalate To      | Wait Time |
| -------- | ----------------------- | ---------------- | --------- |
| CRITICAL | Down/500 errors/DB fail | Backend + DevOps | 5 mins    |
| CRITICAL | Redis down              | Infrastructure   | 5 mins    |
| HIGH     | Error rate > 1%         | Backend          | 15 mins   |
| HIGH     | p95 > 1s                | Backend + DevOps | 15 mins   |
| MEDIUM   | Hit ratio < 70%         | Backend          | 30 mins   |
| MEDIUM   | Memory/CPU > 80%        | DevOps           | 30 mins   |
| LOW      | Disk > 85%              | DevOps           | 2 hours   |
| LOW      | Slow endpoint (< 1s)    | Backend          | 4 hours   |

---

## 📊 DASHBOARD RECOMMENDATIONS

**If using monitoring tool (New Relic, DataDog, etc):**

1. **Real-time panels:**
    - HTTP status codes (200, 5xx count)
    - Response time (p50, p95, p99)
    - Error rate
    - Database query count
    - Cache hit ratio

2. **Alerts configured:**
    - HTTP 500 errors (immediate)
    - Response p95 > 500ms (5x10 minute checks)
    - Error rate > 1% (triggered)
    - Database errors (immediate)
    - Cache failures (immediate)

3. **Historical trending:**
    - Response time over 24 hours (should be stable)
    - Error rate over time (should stay ~0%)
    - Query count (should stay ~6-7)
    - Memory usage (should be stable)

---

## 🔄 Post-UAT Actions (Mar 28, 10:00 AM)

**After 24-hour monitoring completes, review:**

1. [ ] Did any CRITICAL alerts fire? If yes, document and fix before production
2. [ ] Error rate consistently < 0.1%? If no, investigate
3. [ ] p95 response time consistently < 500ms? If no, optimize
4. [ ] Cache hit ratio > 80%? If no, increase warming frequency
5. [ ] All tests still passing? Run `php artisan test --compact` again
6. [ ] Message delivery 100%? Verify sample messages persisted

**Decision:**

- [ ] GO for production (all metrics good)
- [ ] EXTEND UAT (fix issues found)
- [ ] ROLLBACK (critical failures, see Rollback Guide)

---

**Last Updated:** Mar 26, 2026  
**Next Review:** Mar 28, 2026 (Post-UAT)  
**Owner:** DevOps Team
