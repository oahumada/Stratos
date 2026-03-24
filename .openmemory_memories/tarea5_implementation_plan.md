# Tarea 5 Implementation Plan: TalentVerificationService Integration into AiOrchestratorService

**Date:** 24-03-2026  
**Phase:** Phase 1 - Planning & Design  
**Status:** 🚀 IN PROGRESS  

---

## Executive Summary

Tarea 5 integrates the comprehensive TalentVerificationService (+ 9 validators) into AiOrchestratorService to create a **quality gate** for agent outputs. This ensures AI-generated talent decisions meet business rules before returning to users.

### Key Metrics

| Aspect | Details |
|--------|---------|
| **Scope** | Modify AiOrchestratorService to verify outputs post-generation |
| **Validators** | 9 per-agent validators (from Tarea 3) |
| **Test Suite** | 18 existing + new integration tests |
| **Rollout Strategy** | 4-phase (silent → flagging → reject → tuning) |
| **Estimated Time** | 4-5 hours |

---

## Implementation Strategy (4 Parts)

### Part 1: Create VerificationIntegrationService Wrapper

**Purpose:** Encapsulate verification logic separate from AiOrchestratorService  
**Location:** `app/Services/VerificationIntegrationService.php`  
**Responsibility:** Bridge between orchestration and verification

```php
class VerificationIntegrationService
{
    public function __construct(
        protected TalentVerificationService $verifier,
        protected AuditService $audit,
    ) {}

    /**
     * Verify agent output and return verification result with metadata
     */
    public function verifyAgentOutput(
        string $agentName,
        array $output,
        array $context = []
    ): VerificationResult {}

    /**
     * Get current verification phase (silent|flagging|reject|tuning)
     */
    public function getCurrentPhase(): string {}

    /**
     * Decide action based on verification result and current phase
     */
    public function decideAction(
        VerificationResult $result,
        string $phase
    ): VerificationAction {}
}
```

**Output Structure:**
```php
class VerificationResult
{
    public bool $valid;
    public float $confidenceScore;
    public string $recommendation; // accept|review|reject
    public array $violations;
    public string $phase;
    public array $metadata;
}

class VerificationAction
{
    public string $type; // accept|flag_review|reject
    public array $responseMetadata;
    public ?string $errorMessage;
}
```

### Part 2: Implement 4-Phase Rollout Strategy

**Configuration Location:** `config/verification.php`

```php
// config/verification.php
return [
    'enabled' => env('VERIFICATION_ENABLED', true),
    'phase' => env('VERIFICATION_PHASE', 'silent'), // silent|flagging|reject|tuning

    'phases' => [
        'silent' => [
            'log_violations' => true,
            'flag_response' => false,
            'reject_output' => false,
            'description' => 'Log violations, accept output (invisible to users)',
        ],
        'flagging' => [
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => false,
            'description' => 'Include violations in response metadata (visible flag)',
        ],
        'reject' => [
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => true,
            'description' => 'Reject invalid outputs, re-prompt or error',
        ],
        'tuning' => [
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => true,
            'prompt_refinement' => true,
            'description' => 'Reject + refine prompts based on patterns (max 2 retries)',
        ],
    ],

    // Thresholds for recommendations
    'thresholds' => [
        'confidence_high' => 0.85,
        'confidence_medium' => 0.65,
        'confidence_low' => 0.40,
    ],
];
```

**Phase Behavior:**

| Phase | Log | Flag Response | Reject | Re-prompt |
|-------|-----|---------------|--------|-----------|
| Silent | ✅ | ❌ | ❌ | ❌ |
| Flagging | ✅ | ✅ | ❌ | ❌ |
| Reject | ✅ | ✅ | ✅ | ❌ |
| Tuning | ✅ | ✅ | ✅ | ✅ (×2) |

### Part 3: Update AiOrchestratorService.agentThink()

**Integration Points:**

```php
// app/Services/AiOrchestratorService.php

public function agentThink(string $agentName, string $taskPrompt, ?string $systemPromptOverride = null): array
{
    // ... existing code ...

    try {
        $output = $provider->generate($taskPrompt, $options);
        $latency = intval((microtime(true) - $startMicrotime) * 1000);

        // NEW: VERIFY OUTPUT
        $verificationResult = $this->verifyOutput(
            agentName: $agentName,
            output: $output,
            organizationId: $agent->organization_id,
            taskPrompt: $taskPrompt
        );

        // NEW: DECIDE ACTION
        $action = $this->verificationIntegration->decideAction(
            $verificationResult,
            $this->verificationIntegration->getCurrentPhase()
        );

        // NEW: HANDLE ACTION
        if ($action->type === 'reject') {
            // Re-throw or return error response
            throw new VerificationFailedException(
                message: $action->errorMessage,
                violations: $verificationResult->violations
            );
        }

        // Attach verification metadata to output
        $output['_verification'] = [
            'valid' => $verificationResult->valid,
            'recommendations' => $verificationResult->recommendation,
            'violations' => count($verificationResult->violations),
            'confidence' => $verificationResult->confidenceScore,
            'flagged' => $action->type === 'flag_review',
        ];

        // ... rest of existing code ...
        return $output;
    } catch (\Throwable $e) {
        // ... existing error handling ...
    }
}

/**
 * Verify agent output using TalentVerificationService
 */
private function verifyOutput(
    string $agentName,
    array $output,
    ?int $organizationId,
    string $taskPrompt
): VerificationResult {
    return $this->verificationIntegration->verifyAgentOutput(
        agentName: $agentName,
        output: $output,
        context: [
            'organization_id' => $organizationId,
            'task_prompt' => substr($taskPrompt, 0, 200),
            'provider' => config('stratos.llm.provider'),
            'timestamp' => now(),
        ]
    );
}
```

### Part 4: Create Integration Tests

**Location:** `tests/Feature/Services/AiOrchestratorVerificationTest.php`

**Test Coverage:**

```
✓ 4 tests for silent phase (verify output logged but accepted)
✓ 4 tests for flagging phase (verify violations flagged in response)
✓ 4 tests for reject phase (verify invalid output rejected with error)
✓ 3 tests for tuning phase (verify re-prompt mechanism with edge cases)
✓ 3 tests for multi-tenant scope (verify organization_id isolation)
✓ 2 tests for audit trail (verify verification events logged)
─────────
  Total: 20 integration tests
```

---

## Detailed Implementation Steps

### Step 1: Create VerificationIntegrationService

**Pseudo-code flow:**

```
Input: agentName, output, context
  ↓
Step 1: Determine agent validator class
  - Map agentName → ValidatorClass
  - Example: "Estratega de Talento" → StrategyAgentValidator
  ↓
Step 2: Instantiate validator & call validate()
  - $validator = ValidatorFactory::create($agentName)
  - $result = $validator->validate($output)
  ↓
Step 3: Calculate confidence score
  - No violations → 1.0
  - 1-2 violations → 0.65-0.85
  - 3+ violations → <0.40
  ↓
Step 4: Generate recommendation
  - valid=true → "accept"
  - valid=false + violations<3 → "review"
  - valid=false + violations>=3 → "reject"
  ↓
Step 5: Create VerificationResult, audit-log
  - Include violations array
  - Include confidence score
  - Include phase & timestamp
  ↓
Output: VerificationResult
```

### Step 2: Register Service & Configuration

**Service Provider:** Update `config/app.php` or `bootstrap/providers.php`

```php
// bootstrap/providers.php
use App\Services\VerificationIntegrationService;
use App\Services\TalentVerificationService;

return [
    // ...existing providers...
];

// Additional bootstrap registration:
Container::getInstance()->singleton(VerificationIntegrationService::class, function ($app) {
    return new VerificationIntegrationService(
        verifier: $app->make(TalentVerificationService::class),
        audit: $app->make(AuditService::class),
    );
});
```

### Step 3: Update AiOrchestratorService Constructor

```php
public function __construct(
    protected TalentVerificationService $verifier,  // NEW
    protected VerificationIntegrationService $verificationIntegration,  // NEW
    protected AuditTrailService $audit,  // NEW (for logging violations)
) {}
```

### Step 4: Modify agentThink() Method

- Add verification call after `$provider->generate()`
- Handle VerificationFailedException
- Attach `_verification` metadata to output
- Log violations in audit trail

### Step 5: Create Integration Tests

- Test each phase (silent, flagging, reject, tuning)
- Test multi-tenant scoping
- Test audit trail logging
- Test response metadata structure
- Test re-prompt logic (tuning phase)

---

## Data Model Additions

### Audit Trail Entry for Verification

```php
// New event type in existing audit system:
[
    'event_name' => 'agent_output_verified',
    'aggregate_type' => 'Agent',
    'aggregate_id' => $agent->id,
    'data' => [
        'agent_name' => 'Estratega de Talento',
        'verification_valid' => false,
        'violations_count' => 2,
        'violations' => [
            ['rule' => 'confidence_score_below_minimum', 'field' => 'confidence_score'],
            ['rule' => 'required_field_missing', 'field' => 'strategy'],
        ],
        'phase' => 'reject',
        'timestamp' => '2026-03-24T12:30:00Z',
    ],
    'organization_id' => $org->id,
]
```

---

## Multi-Tenant Scoping

**All verification operations must include organization_id:**

```php
// In VerificationIntegrationService
public function verifyAgentOutput(
    string $agentName,
    array $output,
    array $context = []
): VerificationResult {
    $organizationId = $context['organization_id'] ?? auth()->user()->organization_id;
    
    // Verify org_id matches authenticated user
    if ($organizationId !== auth()->user()->organization_id) {
        throw new UnauthorizedTenantException("Cross-tenant access detected");
    }

    // ... verification logic scoped to org ...
}
```

---

## Error Handling

### New Exception Classes

```php
// app/Exceptions/VerificationFailedException.php
class VerificationFailedException extends Exception
{
    public function __construct(
        public $violations,
        $message = "Agent output failed verification",
    ) {
        parent::__construct($message);
    }
}

// app/Exceptions/UnauthorizedTenantException.php
class UnauthorizedTenantException extends Exception {}

// app/Exceptions/AgentValidatorNotFoundException.php
class AgentValidatorNotFoundException extends Exception {}
```

### Error Response Format

```json
{
    "success": false,
    "error": "Agent output failed verification (2 violations)",
    "verification": {
        "valid": false,
        "violations": [
            {
                "rule": "confidence_score_below_minimum",
                "field": "confidence_score",
                "message": "Confidence score 0.35 is below minimum 0.5",
                "received": 0.35
            },
            {
                "rule": "required_field_missing",
                "field": "strategy",
                "message": "Required field 'strategy' is missing or empty",
                "received": null
            }
        ],
        "recommendation": "reject",
        "phase": "reject"
    }
}
```

---

## Testing Strategy

### Unit Tests (5-10 tests)

- VerificationIntegrationService instantiation
- Phase determination logic
- Recommendation generation
- Confidence score calculation

### Feature Tests (20 tests)

- AiOrchestratorService + TalentVerificationService integration
- Each phase behavior (silent, flagging, reject, tuning)
- Multi-tenant scoping
- Audit trail recording
- Error handling

### Integration Tests (5-10 tests)

- End-to-end flow: Agent task → Output → Verification → Response
- API response structure validation
- Cross-agent verification (test with 2-3 different agents)

---

## Rollout Plan (4-Phase Strategy)

### Phase 1: Silent Mode (Dev/Staging)
- **Deploy date:** 24-03-2026
- **Visibility:** Developers only via logs
- **Impact:** None (violations logged but output accepted)
- **Goal:** Collect baseline violation data

### Phase 2: Flagging Mode (Staging → Prod)
- **Deploy date:** 25-03-2026 (next day)
- **Visibility:** Flags in response metadata
- **Impact:** Users see `_verification.flagged: true` when violations exist
- **Goal:** User education + QA feedback

### Phase 3: Reject Mode (Prod)
- **Deploy date:** 26-03-2026 (after 24h flagging feedback)
- **Visibility:** Error responses for invalid outputs
- **Impact:** Invalid outputs rejected, users see error message
- **Goal:** Hard quality gate

### Phase 4: Tuning Mode (Prod + Optimization)
- **Deploy date:** 27-03-2026 (optional, based on needs)
- **Visibility:** Same as Phase 3 + re-prompt attempts
- **Impact:** Agents retry up to 2 times with refined prompts
- **Goal:** Maximize successful output generation

---

## Success Criteria (Tarea 5 Completion)

- ✅ VerificationIntegrationService created (100 LOC)
- ✅ AiOrchestratorService updated with verification hook (50-70 LOC changes)
- ✅ config/verification.php created with 4-phase configuration (50 LOC)
- ✅ Integration tests created (200-250 LOC, 20 tests)
- ✅ All tests passing (363 existing + 20 new = 383 total)
- ✅ Audit trail logging for verification events
- ✅ Response metadata includes `_verification` structure
- ✅ Multi-tenant scoping verified
- ✅ Documentation updated (openmemory.md + code comments)
- ✅ Commit with message "feat: Tarea 5 - TalentVerificationService integration"

---

## Timeline Estimate

| Step | Time | Status |
|------|------|--------|
| 1. Create VerificationIntegrationService | 45 min | ⏳ |
| 2. Create config/verification.php | 15 min | ⏳ |
| 3. Update AiOrchestratorService | 30 min | ⏳ |
| 4. Create integration tests | 60 min | ⏳ |
| 5. Debug & fix failures | 30 min | ⏳ |
| 6. Documentation & commit | 20 min | ⏳ |
| **Total** | **3.3 hours** | **Realistic** |

---

## Next Action

✅ **Phase 1 (Planning) Complete**  
⏳ **Ready to proceed to Phase 2 (Implementation)**

Recommend starting with **Step 1: Create VerificationIntegrationService** 👇

