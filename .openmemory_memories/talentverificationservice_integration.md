# Memory: TalentVerificationService Integration Points & Future Hooks

**Created:** 24-03-2026  
**Project:** Stratos Bloque 5 Sprint 3.1 Tarea 2  
**Status:** Integration strategy for Tarea 5 implementation  
**Memory Types:** integration_strategy, architecture_planning, api_design

---

## Current Integration Map (Tarea 2 Complete)

```
TalentVerificationService (IMPLEMENTED)
├── Depends On:
│   ├── RAGASEvaluator (DI injection)
│   │   └── Calls: evaluate(text, context): RAGASResponse
│   │   └── Fallback: logs warning, continues if unavailable
│   ├── AuditTrailService (DI injection)
│   │   └── Calls: log(action, metadata): void
│   │   └── Used in: auditVerification() after each verify()
│   ├── config/verification_rules.php (configuration)
│   │   └── Provides: agent-specific rules, constraints, enums
│   └── VerificationViolation & VerificationResult (Tarea 1 DTOs)
│       └── Immutable value objects for violation/result representation
│
├── Used By: (PENDING - Tarea 5)
│   └── AiOrchestratorService
│       └── Integrates after agentThink(): runs verify()
│       └── Feeds recommendation into response chain
│
└── Exposes:
    ├── Public verify(agentId, output, context): VerificationResult
    ├── Result contains:
    │   ├── score: float (1.0 / 0.75 / 0.5 / 0.2)
    │   ├── recommendation: string ['accept', 'review', 'reject']
    │   ├── violations: Collection
    │   ├── contradictions: Collection
    │   ├── hallucinations: Collection
    │   ├── totalChecks: int (always 5)
    │   └── errorCount: int (0-5)
    └── Audit trail captured via AuditTrailService
```

---

## Dependency Injection Setup (Current)

### Service Registration

```php
// bootstrap/providers.php (Laravel 12)
use App\Services\TalentVerificationService;
use App\Services\RAGASEvaluator;
use App\Services\AuditTrailService;

return [
    // Singleton (one instance per request cycle)
    TalentVerificationService::class => fn($app) => new TalentVerificationService(
        ragas: $app->make(RAGASEvaluator::class),
        audit: $app->make(AuditTrailService::class),
    ),
];
```

### Usage Pattern (Current)

```php
// In controller or another service
public function __construct(
    private TalentVerificationService $verifier
) {}

public function handleAgentOutput(string $agentId, array $output): Response
{
    $result = $this->verifier->verify(
        agentId: $agentId,
        output: $output,
        context: ['organization_id' => auth()->user()->organization_id]
    );

    // Use $result
    if ($result->recommendation === 'reject') {
        return response()->json(['error' => 'Output rejected'], 422);
    }

    return response()->json($result->toArray());
}
```

---

## Integration Point 1: AiOrchestratorService Hook (Tarea 5)

### Current AiOrchestratorService Flow (Pre-Integration)

```php
public function orchestrate(array $input): AgentResponse
{
    $agentThinkResult = $this->agentThink($input);
    
    // Process output
    return new AgentResponse(
        success: true,
        data: $agentThinkResult['output'],
        metadata: [...]
    );
}
```

### Proposed Integration (Tarea 5)

```php
public function orchestrate(array $input): AgentResponse
{
    $agentThinkResult = $this->agentThink($input);
    
    // NEW: Verify agent output
    $verification = $this->verifier->verify(
        agentId: $this->currentAgent->name,
        output: $agentThinkResult['output'],
        context: [
            'organization_id' => auth()->user()->organization_id,
            'prompt' => $input['prompt'],
            'source_data' => $input['source_data'] ?? null,
            'provider' => config('llm.provider'),
        ]
    );
    
    // Decision based on verification result
    if ($verification->recommendation === 'reject') {
        // Log rejection in audit trail
        return new AgentResponse(
            success: false,
            error: $verification->getHumanReadableErrors(),
            metadata: ['verification_result' => $verification->toArray()],
        );
    }
    
    // If 'review', flag for human review but still return data
    $flagReview = $verification->recommendation === 'review';
    
    return new AgentResponse(
        success: true,
        data: $agentThinkResult['output'],
        metadata: [
            'verification' => [
                'score' => $verification->score,
                'recommendation' => $verification->recommendation,
                'flag_for_review' => $flagReview,
            ],
            ...
        ]
    );
}
```

### Integration Strategy

```
Agent Output → TalentVerificationService.verify()
    ↓
    ├─ Score Calculation
    ├─ Recommendation Generation
    ├─ Audit Trail Logging
    └─ Violations Collection
    ↓
    ├─ recommendation = 'reject' → Return Error Response
    ├─ recommendation = 'review' → Flag & Return with Metadata
    └─ recommendation = 'accept' → Return Success Response
```

**Benefits of this integration:**
1. **Quality Gate:** No LLM outputs pass verification without review
2. **Audit Trail:** Every agent output has verification record
3. **Graceful Degradation:** 'review' flag signals uncertain outputs
4. **User Feedback:** Users see why output might be problematic
5. **Data Safety:** Hallucinated data caught before storage

---

## Integration Point 2: API Response Shape (OpenAPI - Tarea 5)

### Current Response

```json
{
  "success": true,
  "data": { "strategy": "Buy", "confidence_score": 0.85 },
  "metadata": { "agent_id": "Estratega de Talento" }
}
```

### Proposed Response with Verification

```json
{
  "success": true,
  "data": { "strategy": "Buy", "confidence_score": 0.85 },
  "metadata": {
    "agent_id": "Estratega de Talento",
    "verification": {
      "score": 0.75,
      "recommendation": "review",
      "flag_for_review": true,
      "total_checks": 5,
      "error_count": 1,
      "violation_rules": ["field_inconsistency_approved_without_date"]
    }
  }
}
```

### Rejection Response

```json
{
  "success": false,
  "error": "Agent output rejected during verification",
  "details": {
    "verification_score": 0.2,
    "recommendation": "reject",
    "violations": [
      {
        "rule": "cross_tenant_data_detected",
        "message": "Field 'organization_ids' indicates multi-tenant data",
        "severity": "critical"
      },
      {
        "rule": "threshold_exceeded",
        "message": "Confidence score 0.3 below minimum 0.5"
      }
    ]
  }
}
```

---

## Integration Point 3: Configuration Workflow (Tarea 5)

### Pre-Deployment Configuration

```php
// config/verification_rules.php
return [
    'agents' => [
        'Estratega de Talento' => [
            'use_ragas_evaluator' => env('RAGAS_ENABLED', true),
            'hallucination_threshold' => env('RAGAS_HALLUC_THRESHOLD', 0.3),
            'require_review_if_halluc_gt' => env('RAGAS_REVIEW_THRESHOLD', 0.2),
            // ... more settings
        ],
    ],
];

// .env file
RAGAS_ENABLED=false          # Disable RAGAS in dev
RAGAS_HALLUC_THRESHOLD=0.3   # Hallucination rejection threshold
RAGAS_REVIEW_THRESHOLD=0.2   # Hallucination review flag threshold
```

### Runtime Configuration Management (Future)

```php
// Proposed: Admin UI modification of rules (post-Tarea 5)
// In VerificationRuleController@update()

POST /api/admin/verification-rules
{
    "agent_id": "Estratega de Talento",
    "field": "confidence_score",
    "min_value": 0.6,  // Change from 0.5 to 0.6
}

// This would update config in memory + log to audit trail
// Option to sync back to .env or persist to DB
```

---

## Integration Point 4: Monitoring & Observability (Tarea 5+)

### Metrics to Track

```php
// In AuditTrailService logging:
[
    'verification_score' => 0.75,           // For distribution analysis
    'recommendation' => 'review',            // Histogram: accept/review/reject
    'error_count' => 1,                      // Count by agent
    'violation_rules' => ['...'],            // Most common violations
    'violations_per_agent' => {              // Agent performance
        'Estratega de Talento': 1,
        'Orquestador 360': 0,
        ...
    },
    'ragas_latency_ms' => 245,              // RAGAS performance
    'ragas_available' => true,               // Availability tracking
]
```

### Dashboard Queries (Future)

```sql
-- Verification acceptance rates by agent
SELECT agent_id, 
       SUM(CASE WHEN recommendation='accept' THEN 1 ELSE 0 END) / COUNT(*) as acceptance_rate
FROM audit_trails 
WHERE action = 'verification_completed'
GROUP BY agent_id

-- Most common violations
SELECT violation_rules ->> 0, COUNT(*) as frequency
FROM audit_trails
WHERE action = 'verification_completed'
GROUP BY violation_rules ->> 0
ORDER BY frequency DESC

-- RAGAS service reliability
SELECT 
    SUM(CASE WHEN ragas_available THEN 1 ELSE 0 END) / COUNT(*) as availability
FROM audit_trails
WHERE action = 'verification_completed' AND date_created > DATE_SUB(NOW(), INTERVAL 7 DAY)
```

---

## Integration Point 5: Error Handling Chain (Tarea 5)

### Error Propagation Strategy

```
TalentVerificationService.verify()
    ↓ (catches all exceptions)
    ├─ Exception logged to AuditTrailService
    ├─ Exception caught in outer try-catch
    ├─ Returns VerificationResult with error flag
    ↓
AiOrchestratorService receives safe VerificationResult
    ↓
    ├─ Never crashes (no exceptions)
    ├─ Always has recommendation
    ├─ Can check recommendation and decide behavior
    ↓
Caller (controller, API, etc.)
    ├─ Checks response.success
    ├─ Handles 'review' flag as needed
    └─ Shows errors to user if recommendation='reject'
```

### Exception Types & Handling

```php
// In TalentVerificationService

try {
    // 1. Configuration exceptions
    $config = config("verification_rules.agents.{$agentId}");
    if (!$config) {
        throw new InvalidArgumentException("Unknown agent: {$agentId}");
    }

    // 2. Validation exceptions
    if (empty($output)) {
        throw new InvalidArgumentException("Output cannot be empty");
    }

    // 3. Service exceptions (RAGAS, Audit)
    $hallucinations = $this->detectHallucinations($output, $context);
    // If RAGAS fails: caught, logged, degraded response

    // 4. Logical exceptions
    if (!is_array($output)) {
        throw new TypeError("Output must be array");
    }

} catch (Throwable $e) {
    // Centralized error handling
    Log::error("Verification failed: {$e->getMessage()}", [
        'agent_id' => $agentId,
        'exception' => $e,
        'exception_type' => get_class($e),
    ]);

    $this->audit->log(
        action: 'verification_failed',
        error: $e->getMessage(),
        exception_type: get_class($e),
        organization_id: $context['organization_id'] ?? null,
    );

    return VerificationResult::rejected()
        ->withError($e->getMessage());
}
```

---

## Integration Point 6: Multi-Tenancy Enforcement (Tara 5)

### Tenant Context Propagation

```php
// In AiOrchestratorService.orchestrate()

$verificationContext = [
    'organization_id' => auth()->user()->organization_id,  // REQUIRED
    'user_id' => auth()->user()->id,                       // Optional
    'tenant_name' => auth()->user()->organization->name,   // For logs
    'prompt' => $input['prompt'],
    'source_data' => $input['source_data'] ?? null,
];

// Verify() validates organization_id is present
// Verify() checks for cross-tenant data patterns
// Audit trail logs organization_id
```

### Multi-Tenant Query Patterns

```php
// In audit queries (Tarea 5+)
// Only return results for user's organization

$org_id = auth()->user()->organization_id;

$verifications = AuditTrail::query()
    ->where('organization_id', $org_id)  // Tenant filter
    ->where('action', 'verification_completed')
    ->where('date_created', '>', now()->subDays(7))
    ->get();
```

---

## Integration Point 7: Testing Strategy (Tarea 4-5)

### Test Pyramid with Verification

```
                  ▲
              End-to-End Tests (Tarea 5)
        /       orchestrate() with verification      \     5-10 tests
       /         (mock HTTP, real service logic)      \
      /________________________________________________\

         Integration Tests (Tarea 4)
    /    AiOrchestratorService + Verification    \    10-15 tests
   /      (real service, mocked RAGASEvaluator)    \
  /____________________________________________________\

       Unit Tests (Tarea 2 - COMPLETE)
   /   TalentVerificationService components    \    18 tests ✅
  /     (validators, calculations, etc.)        \
 /____________________________________________________\
```

### End-to-End Test Example (Tarea 5)

```php
it('rejects agent output with cross-tenant data in orchestration flow', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    
    $user = User::factory()->create(['organization_id' => $org1->id]);
    
    $this->actingAs($user);
    
    $orchestrator = app(AiOrchestratorService::class);
    
    $output = [
        'evaluations' => [
            ['org_id' => $org1->id, 'data' => '...'],
            ['org_id' => $org2->id, 'data' => '...'],  // Cross-tenant!
        ],
    ];
    
    $response = $orchestrator->orchestrate([
        'agent_id' => 'Orquestador 360',
        'output' => $output,
    ]);
    
    expect($response->success)->toBeFalse();
    expect($response->metadata['verification']['recommendation'])->toBe('reject');
});
```

---

## Known Integration Limitations & Mitigations

| Limitation | Impact | Mitigation (Tarea 5) |
|------------|--------|----------------------|
| RAGAS service unavailable | Hallucinations not detected | Graceful degradation, use low threshold |
| Network latency to RAGAS | Verification slow (245ms typical) | Async verification queue option (Tarea 6+) |
| False positive hallucinations | Valid outputs flagged for review | Audit monitoring, adjust threshold over time |
| Config not hot-reloadable | Need restart to change rules | Run in debug mode, cache invalidation (Tarea 6+) |
| No per-user verification rules | Same rules for all users in org | Add user_role to context, filter rules by role (Tarea 6+) |
| Violation messages hardcoded | Not localized for tenants | Add i18n keys, translate at API layer (Tarea 6+) |

---

## Recommended Integration Rollout (Tarea 5)

### Phase 1: Silent Integration (Week 1)
```php
// AiOrchestratorService
$verification = $this->verifier->verify(...);
// Log to audit trail but always return 'accept'
// Gather baseline metrics on false positives
```

### Phase 2: Review Flagging (Week 2)
```php
// AiOrchestratorService
$verification = $this->verifier->verify(...);
if ($verification->recommendation === 'review') {
    // Flag in metadata but still return success
    // Users see flag, can manually review
}
```

### Phase 3: Hard Reject (Week 3)
```php
// AiOrchestratorService
$verification = $this->verifier->verify(...);
if ($verification->recommendation === 'reject') {
    // Return error response
    // Block output from being stored/returned
}
```

### Phase 4: Monitoring & Tuning (Week 4+)
```php
// Analyze metrics
// Adjust thresholds based on false positive rate
// Add agent-specific rules refinements
// Consider async verification for performance
```

---

## Future Extensions (Tarea 6+)

### Potential Add-Ons
1. **Async Verification Queue** - Long-running verifications in background
2. **Rule Learning** - ML model learns agent-specific rule patterns
3. **A/B Testing** - Test different verification strategies
4. **Custom Rules per Tenant** - Organization-specific verification rules
5. **Verification History** - Track changes to rules over time
6. **Batch Verification API** - Verify 100s of outputs at once
7. **Webhook Notifications** - Notify on verification failures
8. **Verification Reports** - Dashboard showing trends and issues

---
