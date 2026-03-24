# Tarea 5: Verification Integration - OpenAPI Documentation

## Overview

Tarea 5 integra el TalentVerificationService como quality gate en AiOrchestratorService. This document specifies the verification system's interface and behavior.

> **Status:** Production Ready (Phase 4 - Deployment & Documentation)  
> **Implementation:** Complete (650 LOC code + 95 tests)  
> **Last Updated:** 2026-03-24

---

## Core Concepts

### 4-Phase Rollout Strategy

The verification system supports 4 progressive phases for safe deployment:

| Phase | Mode       | Behavior                      | User Impact                  | Environment       |
| ----- | ---------- | ----------------------------- | ---------------------------- | ----------------- |
| 1     | `silent`   | Log violations, accept output | None (logs only)             | Dev/Staging       |
| 2     | `flagging` | Flag violations in response   | Flag visible in API response | Staging→Prod      |
| 3     | `reject`   | Reject invalid outputs        | Error responses              | Prod              |
| 4     | `tuning`   | Reject + re-prompt (×2)       | Error + automatic retry      | Prod optimization |

### Configuration

**Environment Variables:**

```bash
# Enable/disable verification globally
VERIFICATION_ENABLED=true

# Current deployment phase
VERIFICATION_PHASE=silent  # silent | flagging | reject | tuning
```

**Configuration File:** `config/verification.php`

```php
return [
    'enabled' => env('VERIFICATION_ENABLED', true),
    'phase' => env('VERIFICATION_PHASE', 'silent'),

    'phases' => [
        'silent' => [
            'log_violations' => true,
            'flag_response' => false,
            'reject_output' => false,
        ],
        'flagging' => [
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => false,
        ],
        'reject' => [
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => true,
        ],
        'tuning' => [
            'log_violations' => true,
            'flag_response' => true,
            'reject_output' => true,
            'max_retries' => 2,
        ],
    ],

    'thresholds' => [
        'confidence_high' => 0.85,
        'confidence_medium' => 0.65,
        'confidence_low' => 0.40,
    ],
];
```

---

## Data Models

### VerificationResult DTO

Response object from `VerificationIntegrationService::verifyAgentOutput()`

```json
{
    "valid": false,
    "confidence_score": 0.35,
    "recommendation": "reject",
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
    "violations_count": 2,
    "agent_name": "Estratega de Talento",
    "phase": "reject",
    "metadata": {}
}
```

**Fields:**

| Field              | Type              | Description                                          |
| ------------------ | ----------------- | ---------------------------------------------------- |
| `valid`            | `boolean`         | Whether output passed all validations                |
| `confidence_score` | `float` (0.0-1.0) | Confidence level in verification result              |
| `recommendation`   | `string`          | Recommended action: `accept` \| `review` \| `reject` |
| `violations`       | `array`           | Array of validation violations found                 |
| `violations_count` | `integer`         | Number of violations detected                        |
| `agent_name`       | `string`          | Name of agent that generated output                  |
| `phase`            | `string`          | Current verification phase                           |
| `metadata`         | `object`          | Additional metadata                                  |

### VerificationAction DTO

Decision object representing action to take

```json
{
    "type": "reject",
    "should_retry": true,
    "phase": "tuning",
    "error_message": "Agent output failed verification (2 violations); confidence score 0.35 is below minimum 0.5; required field 'strategy' is missing or empty",
    "response_metadata": {
        "retry_instructions": "Please include all required fields and ensure confidence score is at least 0.5",
        "max_retries": 2,
        "retry_count": 0
    }
}
```

**Fields:**

| Field               | Type      | Description                                        |
| ------------------- | --------- | -------------------------------------------------- |
| `type`              | `string`  | Action type: `accept` \| `flag_review` \| `reject` |
| `should_retry`      | `boolean` | Whether retrying is permitted (tuning phase only)  |
| `phase`             | `string`  | Current verification phase                         |
| `error_message`     | `string`  | Human-readable error message (if applicable)       |
| `response_metadata` | `object`  | Metadata for retry mechanism                       |

---

## Service API

### VerificationIntegrationService

Core bridge service between orchestration and verification

#### `verifyAgentOutput(string $agentName, array $output, array $context): VerificationResult`

Verify agent output against business rules

**Parameters:**

| Parameter    | Type     | Description                                                          |
| ------------ | -------- | -------------------------------------------------------------------- |
| `$agentName` | `string` | Name of agent (e.g., "Estratega de Talento")                         |
| `$output`    | `array`  | Output from agent to verify                                          |
| `$context`   | `array`  | Metadata context: `organization_id`, `task_prompt`, `provider`, etc. |

**Returns:** `VerificationResult` containing validation results

**Throws:**

- `UnauthorizedTenantException` - If org_id mismatch detected
- `\Exception` - If validator not found (graceful fallback to neutral result)

**Example:**

```php
$result = $verificationService->verifyAgentOutput(
    agentName: 'Estratega de Talento',
    output: [
        'response' => 'Generated strategy',
        'confidence_score' => 0.35,
        // ... other output fields
    ],
    context: [
        'organization_id' => 1,
        'task_prompt' => 'Generate talent strategy...',
        'provider' => 'openai',
    ]
);

if (!$result->valid) {
    Log::warning('Verification failed', $result->violations);
}
```

#### `getCurrentPhase(): string`

Get current verification phase

**Returns:** `string` - One of: `silent`, `flagging`, `reject`, `tuning`

**Example:**

```php
$phase = $verificationService->getCurrentPhase();

if ($phase === 'tuning') {
    // Enable advanced re-prompt logic
    $action->shouldRetry = true;
}
```

#### `decideAction(VerificationResult $result): VerificationAction`

Decide what action to take based on verification result and phase

**Parameters:**

| Parameter | Type                 | Description                                    |
| --------- | -------------------- | ---------------------------------------------- |
| `$result` | `VerificationResult` | Verification result from `verifyAgentOutput()` |

**Returns:** `VerificationAction` specifying what to do next

**Phase Behavior:**

| Phase      | Invalid Output      | Valid Output |
| ---------- | ------------------- | ------------ |
| `silent`   | Accept (logged)     | Accept       |
| `flagging` | Flag (metadata)     | Accept       |
| `reject`   | Reject (error)      | Accept       |
| `tuning`   | Reject (with retry) | Accept       |

**Example:**

```php
$action = $verificationService->decideAction($result);

if ($action->shouldReject()) {
    throw new VerificationFailedException(
        violations: $result->violations,
        message: $action->errorMessage
    );
}

// Attach metadata to output
$output['_verification'] = [
    'valid' => $result->valid,
    'flagged' => $action->shouldFlag(),
    'confidence_score' => $result->confidenceScore,
];
```

---

## AiOrchestratorService Integration

### agentThink() Method Behavior

The `AiOrchestratorService::agentThink()` method now includes verification:

```php
public function agentThink(string $agentName, string $taskPrompt, ?string $systemPromptOverride = null): array
{
    // ... existing code ...

    try {
        $output = $provider->generate($taskPrompt, $options);

        // NEW: Verify output if enabled
        if (config('verification.enabled')) {
            $verificationResult = $this->verificationIntegration->verifyAgentOutput(
                agentName: $agentName,
                output: $output,
                context: [
                    'organization_id' => $agent->organization_id,
                    'task_prompt' => $taskPrompt,
                    'provider' => $agent->provider,
                ]
            );

            $action = $this->verificationIntegration->decideAction($verificationResult);

            // Reject if appropriate for current phase
            if ($action->shouldReject()) {
                throw new VerificationFailedException(
                    violations: $verificationResult->violations,
                    message: $action->errorMessage
                );
            }

            // Attach verification metadata
            $output['_verification'] = [
                'valid' => $verificationResult->valid,
                'recommendation' => $verificationResult->recommendation,
                'violations_count' => count($verificationResult->violations),
                'confidence_score' => $verificationResult->confidenceScore,
                'phase' => $verificationResult->phase,
                'flagged_for_review' => $action->shouldFlag(),
            ];
        }

        return $output;
    } catch (VerificationFailedException $e) {
        // Log verification failure
        Log::warning('Agent output verification failed', [
            'agent_name' => $agentName,
            'violations_count' => count($e->violations),
        ]);

        throw $e;
    }
}
```

### Output Metadata Structure

When verification is enabled, output includes `_verification` metadata:

```json
{
    "response": "Generated strategy...",
    "confidence_score": 0.95,
    "_verification": {
        "valid": true,
        "recommendation": "accept",
        "violations_count": 0,
        "confidence_score": 0.95,
        "phase": "reject",
        "flagged_for_review": false
    }
}
```

---

## Error Handling

### Exception Classes

#### VerificationFailedException

Thrown when output fails verification in reject/tuning phases

```php
throw new VerificationFailedException(
    violations: [
        ['rule' => 'confidence_score_below_minimum', 'field' => 'confidence_score'],
        ['rule' => 'required_field_missing', 'field' => 'strategy'],
    ],
    agentName: 'Estratega de Talento',
    message: 'Agent output failed verification (2 violations)'
);
```

**Properties:**

| Property     | Type     | Description                                 |
| ------------ | -------- | ------------------------------------------- |
| `violations` | `array`  | Array of violation details                  |
| `agentName`  | `string` | Name of agent that generated invalid output |
| `message`    | `string` | Human-readable error message                |

#### UnauthorizedTenantException

Thrown when cross-tenant access detected

```php
throw new UnauthorizedTenantException(
    "Cannot verify output for organization {$requestOrgId}: user belongs to {$userOrgId}"
);
```

### Error Response Format

When verification fails in reject phase:

```json
{
    "success": false,
    "error": "Agent output failed verification (2 violations)",
    "status_code": 422,
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
        "violations_count": 2,
        "recommendation": "reject",
        "phase": "reject"
    }
}
```

---

## Confidence Score Algorithm

Confidence scores are calculated based on violation count:

| Violations | Score Range | Interpretation              |
| ---------- | ----------- | --------------------------- |
| 0          | 1.0         | Perfect confidence - accept |
| 1-2        | 0.65-0.85   | Medium confidence - review  |
| 3+         | <0.40       | Low confidence - reject     |

**Algorithm:**

```
if (violations.length === 0) {
    score = 1.0;
} else if (violations.length === 1) {
    score = 0.85;
} else if (violations.length === 2) {
    score = 0.65;
} else if (violations.length >= 3) {
    score = 0.40 - (0.10 * (violations.length - 3));
    score = max(0.0, score);
}
```

---

## Deployment Rollout

### Phase 1: Silent Mode (Dev/Staging)

**Timeline:** Immediate  
**Configuration:** `VERIFICATION_PHASE=silent`

**Behavior:**

- Violations logged but output accepted
- No user impact
- Collect baseline violation data

**Validation:**

- ✅ Verify logging works correctly
- ✅ All agents continue generating output
- ✅ No API changes visible to users

### Phase 2: Flagging Mode (Staging→Prod)

**Timeline:** After 24h of Phase 1 baseline  
**Configuration:** `VERIFICATION_PHASE=flagging`

**Behavior:**

- Violations included in response metadata
- Response structure includes `_verification` object
- Users see violations but output still returned

**Validation:**

- ✅ Frontend displays `_verification.flagged_for_review`
- ✅ QA reviews flagged outputs for false positives
- ✅ Monitor flagging ratio

### Phase 3: Reject Mode (Prod)

**Timeline:** After 24h of Phase 2 flag data  
**Configuration:** `VERIFICATION_PHASE=reject`

**Behavior:**

- Invalid outputs rejected with error response
- Quality gate enforced
- Clients must handle 422 responses

**Validation:**

- ✅ API returns 422 for invalid outputs
- ✅ Front-end properly handles errors
- ✅ Error messages are actionable

### Phase 4: Tuning Mode (Prod Optimization)

**Timeline:** Optional, after Phase 3 stability  
**Configuration:** `VERIFICATION_PHASE=tuning`

**Behavior:**

- Rejected outputs trigger automatic re-prompting (max 2 retries)
- Refined prompts based on violation patterns
- Success rates optimized

**Validation:**

- ✅ Retry logic functions correctly
- ✅ Success rate improves with re-prompting
- ✅ Retry limit enforcement works

---

## Testing Coverage

### Unit Tests (10 tests)

- ✅ Confidence score calculation
- ✅ Phase decision logic
- ✅ Recommendation generation
- ✅ Error message formatting

### Feature Tests (65 tests)

- ✅ All 4 phases (20 tests)
- ✅ Tuning & error scenarios (16 tests)
- ✅ Deployment validation (29 tests)

### Integration Tests

- ✅ End-to-end orchestration flow
- ✅ Multi-tenant scoping
- ✅ Audit trail logging
- ✅ Backward compatibility

**Total Coverage:** 95 tests | 438 overall suite | 0 regressions

---

## Configuration Examples

### Development Environment

```bash
# config/verification.php in .env.local
VERIFICATION_ENABLED=true
VERIFICATION_PHASE=silent
```

### Staging Environment

```bash
VERIFICATION_ENABLED=true
VERIFICATION_PHASE=flagging
```

### Production Environment (Phase 3+)

```bash
VERIFICATION_ENABLED=true
VERIFICATION_PHASE=reject
```

---

## Migration Guide

### From Previous Version

If upgrading from Stratos without verification:

1. **No breaking changes** - Verification is optional via `VERIFICATION_ENABLED`
2. **Default: Safe** - Defaults to `silent` phase (no user impact)
3. **Gradual rollout** - Progress through phases at your pace
4. **Rollback:** Set `VERIFICATION_PHASE=silent` to disable enforcement

### Disabling Verification

```bash
VERIFICATION_ENABLED=false
```

In this mode:

- No validation performed
- No metadata attached
- Performance unchanged
- Backward compatible

---

## Support & Troubleshooting

### Common Issues

**Q: Verification configured but phase not changing?**  
A: Ensure environment variable is loaded: `php artisan config:cache`

**Q: Getting "Validator not found" errors?**  
A: Check agent name matches a configured validator in `ValidatorFactory`

**Q: Violations flagged in phase 1, OK?**  
A: Yes - silent phase logs all violations for baseline analysis

---

## Code Examples

### Enable Verification

```php
// config/verification.php
'enabled' => env('VERIFICATION_ENABLED', true),
'phase' => env('VERIFICATION_PHASE', 'silent'),
```

### Access Verification Result

```php
$orchestrator->agentThink('Estratega de Talento', $task);

// Output now includes:
[
    'response' => '...',
    '_verification' => [
        'valid' => true,
        'confidence_score' => 0.95,
        'flagged_for_review' => false,
    ]
]
```

### Check Current Phase

```php
$integrationService = app(VerificationIntegrationService::class);
$currentPhase = $integrationService->getCurrentPhase();

if ($currentPhase === 'tuning') {
    // Enable advanced features
}
```

---

## Version History

| Version | Date       | Status | Notes                     |
| ------- | ---------- | ------ | ------------------------- |
| 1.0.0   | 2026-03-24 | Stable | Initial release (Phase 4) |

---

**Document:** Tarea 5 Phase 4 - Final Deployment Documentation  
**Status:** Production Ready ✅  
**Tests:** 95/95 passing  
**Commits:** 70a7ef47, 0940940c, 6156bc13, 35b35870 (+ deployment docs)
