# 🚀 K6 Staging Execution Checklist (4-5 April 2026)

**Goal:** Execute comprehensive load testing suite on staging environment to validate performance and stability before production rollout.

---

## 📋 Pre-Execution Checks (4 Abr 08:00)

- [ ] Staging environment is UP and responsive
- [ ] Database fully populated with test data
- [ ] Redis cache is running (staging instance)
- [ ] Monitoring/metrics collection active
- [ ] Team notified of testing window
- [ ] Runbook ready (escalation, rollback)
- [ ] k6 installed locally:
  ```bash
  which k6
  k6 version
  ```

---

## ✅ Test Execution Schedule (4 Abr)

### 09:00 — Task 1.1: Smoke Test (5 min)

**Purpose:** Verify staging is healthy before intensive testing

```bash
cd /home/omar/Stratos.worktrees/copilot-worktree-2026-04-03T21-40-39

# Run smoke test (minimal load)
k6 run tests/k6/scenarios/load.js \
  --vus 5 \
  --duration 2m \
  -e K6_BASE_URL=https://staging.stratos.app \
  --tag test:smoke-test
```

**Expected Results:**
- [ ] Status: PASSED ✅
- [ ] Error Rate: 0%
- [ ] p95 Latency: <1s
- [ ] All endpoints 200 OK

**Decision:**
- [ ] ✅ GO to next test
- [ ] ❌ STOP — Fix issues, restart

---

### 10:00 — Task 1.2: Baseline Load Test (30 min)

**Purpose:** Capture performance baseline (reference point for all comparisons)

```bash
# Run baseline with full scenario profile (as-is in load.js)
k6 run tests/k6/scenarios/load.js \
  -e K6_BASE_URL=https://staging.stratos.app \
  --out json=tests/k6/results/baseline-2026-04-04.json \
  --tag test:baseline
```

**Expected Results:**
- [ ] Max VUs: 35 concurrent
- [ ] p95 Latency: <200ms (read), <5s (preview)
- [ ] Error Rate: <1%
- [ ] Throughput: 50+ RPS
- [ ] Cache Hit Rate: >50%

**Metrics to Note:**
- [ ] p50 latency: ________ ms
- [ ] p95 latency: ________ ms
- [ ] p99 latency: ________ ms
- [ ] Error count: ________
- [ ] Total requests: ________

**Saved Results:**
- [ ] `tests/k6/results/baseline-2026-04-04.json` ✓

---

### 11:30 — Task 1.3: Stress Test (8 min)

**Purpose:** Find the breaking point (max stable load)

**Before:** Ensure system recovered from previous test (5 min wait, verify no lingering errors)

```bash
# CREATE NEW SCRIPT: tests/k6/scenarios/stress.js
# Then run:
k6 run tests/k6/scenarios/stress.js \
  -e K6_BASE_URL=https://staging.stratos.app \
  --out json=tests/k6/results/stress-2026-04-04.json \
  --tag test:stress
```

**Stress Profile:**
```
0-1 min:   0 → 50 VUs (ramp up)
1-3 min:   50 → 100 VUs (ramp up)
3-5 min:   100 → 200 VUs (ramp up)
5-7 min:   200 → 300 VUs (ramp up — find breaking point)
7-8 min:   300 → 0 (ramp down)
```

**Key Metrics to Track:**
- [ ] At what VU count does error rate spike?
  - Breaking point VUs: ________
- [ ] At what VU count does p95 latency exceed 3s?
  - Latency degradation point: ________
- [ ] Any cascading failures? (503s, timeouts)
  - [ ] YES — Document, continue
  - [ ] NO — Good sign, continue
- [ ] Resource bottleneck observed:
  - [ ] CPU
  - [ ] Memory
  - [ ] Database connections
  - [ ] Redis connections
  - [ ] Other: ________________

**Saved Results:**
- [ ] `tests/k6/results/stress-2026-04-04.json` ✓

**Decision:**
- [ ] ✅ Results acceptable (breaking point identified)
- [ ] ⚠️ Results concerning (escalate, investigate)
- [ ] ❌ Critical failure (stop testing, rollback analysis)

---

### 13:00 — Task 1.4: Spike Test (4 min)

**Purpose:** Verify system handles sudden traffic spikes (10x increase)

**Before:** 5 min recovery wait (verify system normalized)

```bash
# CREATE NEW SCRIPT: tests/k6/scenarios/spike.js
# Then run:
k6 run tests/k6/scenarios/spike.js \
  -e K6_BASE_URL=https://staging.stratos.app \
  --out json=tests/k6/results/spike-2026-04-04.json \
  --tag test:spike
```

**Spike Profile:**
```
0-1 min:    0 → 10 VUs (baseline)
1-1m10s:    10 → 100 VUs (SPIKE! 900% increase in 10 seconds)
1m10s-2m:   100 VUs (maintain spike)
2-2m10s:    100 → 10 VUs (DROP! back to normal in 10 seconds)
2m10s-3m:   10 VUs (recover)
3-4m:       0 (end)
```

**Success Criteria:**
- [ ] No cascading failures (no 502/503 during spike)
- [ ] Error rate during spike: <5% (allowed)
- [ ] p95 latency during spike: <5s (degraded but acceptable)
- [ ] Recovery time (back to <200ms p95): <10 seconds
- [ ] No connection leaks after recovery

**Key Observations:**
- [ ] Error rate at peak: ________%
- [ ] Max latency during spike: ________ ms
- [ ] Time to recovery: ________ seconds
- [ ] Any error patterns?
  - [ ] 502 Bad Gateway
  - [ ] 503 Service Unavailable
  - [ ] 504 Gateway Timeout
  - [ ] 429 Rate Limited
  - [ ] Other: ________________

**Saved Results:**
- [ ] `tests/k6/results/spike-2026-04-04.json` ✓

**Decision:**
- [ ] ✅ Spike handled well
- [ ] ⚠️ Issues observed but recoverable
- [ ] ❌ System unstable during spike

---

### 14:30 — Task 1.5: Rate Limit Validation (5 min)

**Purpose:** Verify rate limiting is working correctly (3-tier limits)

**Before:** 5 min recovery wait

```bash
# CREATE NEW SCRIPT: tests/k6/scenarios/rate-limit.js
# Should test:
#   1. Auth user limit (300 req/min)
#   2. Guest limit (60 req/min)
#   3. Response headers (X-RateLimit-*)
#   4. Cache bypass doesn't bypass rate limit
# Then run:
k6 run tests/k6/scenarios/rate-limit.js \
  -e K6_BASE_URL=https://staging.stratos.app \
  --out json=tests/k6/results/rate-limit-2026-04-04.json \
  --tag test:rate-limit
```

**Test 1: Auth User Rate Limit (300 req/min)**
- [ ] Send 300 requests in 60s: Should all succeed
- [ ] Send request 301: Should get 429 Too Many Requests
- [ ] Response headers present:
  - [ ] X-RateLimit-Limit: 300
  - [ ] X-RateLimit-Remaining: ≤0
  - [ ] X-RateLimit-Reset: (future timestamp)

**Test 2: Guest Rate Limit (60 req/min)**
- [ ] Send 60 requests in 60s: Should all succeed
- [ ] Send request 61: Should get 429 Too Many Requests
- [ ] Limit is stricter than auth user ✓

**Test 3: Cache Bypass**
- [ ] Add Cache-Control: no-cache header
- [ ] Should NOT bypass rate limit
- [ ] Still enforced after 60 requests

**Test 4: Rate Limit Reset**
- [ ] After 60 seconds, window resets
- [ ] Can send another 300 requests (auth) / 60 (guest)

**Results Summary:**
- [ ] Auth limit: ✅ PASS / ❌ FAIL
- [ ] Guest limit: ✅ PASS / ❌ FAIL
- [ ] Headers: ✅ PASS / ❌ FAIL
- [ ] Cache bypass: ✅ PASS / ❌ FAIL
- [ ] Rate reset: ✅ PASS / ❌ FAIL

**Saved Results:**
- [ ] `tests/k6/results/rate-limit-2026-04-04.json` ✓

---

### 16:00 — Task 1.6: Cache Failover Test (6 min)

**Purpose:** Verify system gracefully degrades when Redis is DOWN

**Steps:**

1. **Minute 0-2: Baseline (Redis UP)**
   ```bash
   k6 run tests/k6/scenarios/cache-failover-baseline.js \
     -e K6_BASE_URL=https://staging.stratos.app \
     --vus 10 --duration 2m
   ```
   - [ ] Cache hit rate: >60%
   - [ ] Error rate: 0%
   - [ ] p95 latency: <200ms

2. **Minute 2: KILL REDIS** (simulated failure)
   ```bash
   # In another terminal on staging server:
   # redis-cli shutdown  (or docker stop redis, etc.)
   ```
   - [ ] Redis stopped ✓

3. **Minute 2-4: Degraded Mode (Redis DOWN)**
   ```bash
   k6 run tests/k6/scenarios/cache-failover-degraded.js \
     -e K6_BASE_URL=https://staging.stratos.app \
     --vus 10 --duration 2m
   ```
   - [ ] No 500 errors (graceful fallback)
   - [ ] Error rate: <2% (acceptable)
   - [ ] p95 latency: <1s (some increase OK)
   - [ ] All 200 OK responses

4. **Minute 4: RESTORE REDIS** (recovery)
   ```bash
   # In other terminal:
   # redis-cli  (or docker start redis)
   ```
   - [ ] Redis restarted ✓

5. **Minute 4-6: Recovery (Redis UP)**
   ```bash
   k6 run tests/k6/scenarios/cache-failover-recovery.js \
     -e K6_BASE_URL=https://staging.stratos.app \
     --vus 10 --duration 2m
   ```
   - [ ] Cache hit rate recovering: >50%
   - [ ] p95 latency: <200ms (back to baseline)
   - [ ] Error rate: 0%

**Success Criteria:**
- [ ] Graceful degradation (no cascading failures)
- [ ] Auto-recovery when Redis restored
- [ ] No data loss or corruption

**Saved Results:**
- [ ] `tests/k6/results/cache-failover-2026-04-04.json` ✓

---

## 📊 Analysis & Report (5 Abr 09:00 — 2 hours)

### Compare Results

```bash
cd tests/k6/results

# Compare baseline vs stress vs spike
# Create comparison table:
```

| Metric | Baseline | Stress | Spike | Target | Status |
|--------|----------|--------|-------|--------|--------|
| p95 Latency | __ ms | __ ms | __ ms | 200ms | ? |
| Error Rate | __% | __% | __% | <1% | ? |
| Max VUs | 35 | 300 | 100 | TBD | ? |
| Cache Hit | __% | __% | __% | >60% | ? |
| Throughput | __ RPS | __ RPS | __ RPS | 100 RPS | ? |

### Generate Report

Create file: `tests/k6/results/STAGING_ANALYSIS_2026-04-05.md`

```markdown
# K6 Staging Load Test Analysis (4 April 2026)

## Summary
- ✅ All baseline SLOs met
- ⚠️ Stress breaking point identified at ___ VUs
- ✅ Spike recovery time acceptable
- ✅ Rate limiting working correctly
- ✅ Cache failover graceful

## Bottlenecks Identified
1. ...
2. ...

## Recommendations
1. ...
2. ...

## Production Readiness: ✅ APPROVED / ⚠️ CONDITIONAL / ❌ NOT READY
```

- [ ] Report created and reviewed
- [ ] All stakeholders signed off
- [ ] Issues documented
- [ ] Ready for production?

---

## ✅ Final Checklist (5 Abr 17:00)

- [ ] All 6 tests completed
- [ ] All results files saved
- [ ] Analysis report generated
- [ ] No critical issues remaining
- [ ] Team notified of results
- [ ] Production rollout approved
- [ ] Runbook reviewed
- [ ] On-call team confirmed

---

## 🎯 Go/No-Go Decision (5 Abr 17:30)

**Ready for Production Rollout (8 Abr)?**

- [ ] ✅ **YES** — Proceed to production canary deployment
- [ ] ⚠️ **CONDITIONAL** — Fix specific issues, re-run tests
- [ ] ❌ **NO** — Hold for additional investigation

**Decision Made By:** __________________  
**Date/Time:** __________________  
**Notes:** 

---

## 📞 Escalation Contacts

| Role | Name | Phone | Slack |
|------|------|-------|-------|
| DevOps Lead | | | |
| SRE On-Call | | | |
| Tech Lead | | | |
| Backend Lead | | | |

---

**Plan Created:** 4 April 2026  
**Execution Window:** 4-5 April 2026  
**Next:** Production Rollout (8 April 2026)
