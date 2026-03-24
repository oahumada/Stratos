# Memory: TalentVerificationService Architecture Decisions & Patterns

**Created:** 24-03-2026  
**Project:** Stratos Bloque 5 Sprint 3.1 Tarea 2  
**Status:** Documentation of architectural patterns used in TalentVerificationService  
**Memory Types:** architecture_decisions, design_patterns, technical_strategy

---

## Core Architecture Decision Matrix

| Decision              | Choice                                           | Rationale                                          | Tradeoff                                     |
| --------------------- | ------------------------------------------------ | -------------------------------------------------- | -------------------------------------------- |
| **Validator Order**   | Multi-tenant → Schema → Rules → Halluc → Contrad | Fail fast on security, then structural, then logic | Can't short-circuit early if security fails  |
| **Score Calculation** | Discrete thresholds (0/1/2-3/4+)                 | Simple, auditable, matches business logic          | Less granular than continuous (0.0-1.0)      |
| **RAGAS Integration** | Optional, graceful degradation                   | Service unavailability ≠ verification failure      | May allow hallucinated content if RAGAS down |
| **Immutability**      | VerificationResult immutable, violations locked  | Audit safety, no hidden mutations                  | Can't update result after creation           |
| **Error Handling**    | Outer try-catch wraps service                    | Never throw from verify()                          | Exceptions logged but not exposed to caller  |
| **Multi-Tenant**      | Validated first, not assumed                     | Zero-trust model                                   | Slight performance hit for org_id extraction |
| **Configuration**     | Externalized in PHP config                       | Easy to modify without code changes                | Need to restart app for config changes       |
| **Audit Integration** | Sync logging in verify()                         | Events recorded immediately                        | Adds minimal latency to verification         |

---

## Validator Pipeline Design

### Sequential vs. Parallel Execution

**Choice:** Sequential (current implementation)

```
Multi-tenant → Schema → Rules → Hallucinations → Contradictions
```

**Rationale:**

- Fail-fast: Stop early if critical violations found
- Deterministic: Same order every run
- Auditable: Clear progression of validation stages
- Incremental violations: Can inspect at each stage

**Alternative (Parallel):**

```
All validators run simultaneously, collect all violations
```

**Why not chosen:**

- Harder to debug (which violation came from where?)
- Less fail-fast (always runs all validators)
- Score calculation becomes more complex (no clear priority)
- Audit trail less clear about progression

**Decision:** Sequential maximizes auditability for compliance use case.

---

## Score Calculation Design

### Discrete Threshold Model (Chosen)

```
0 violations   → score = 1.0   → recommendation: accept
1 violation    → score = 0.75  → recommendation: review
2-3 violations → score = 0.5   → recommendation: review
4+ violations  → score = 0.2   → recommendation: reject
```

**Rationale:**

- Matches business logic (HR teams think in discrete categories)
- Easy to explain ("Each violation costs X points")
- Easy to audit ("Why was recommendation 'review'? 2 violations")
- Easy to modify thresholds (change numbers, not formula)
- Prevents gaming (e.g., 0.748 score vs 0.75)

**Alternative (Continuous):**

```php
score = max(0.2, 1.0 - (violations.count * 0.15))
```

**Why not chosen:**

- Harder to explain ("Why 0.741 and not 0.75?")
- Business rules mentality is categorical, not continuous
- Recommendations become arbitrary at crossover points
- Audit trail less clear

**Decision:** Discrete thresholds better match compliance requirements.

---

## RAGAS Integration Strategy

### Conditional Evaluation (Chosen)

```php
if (config('verification_rules.agents.{$agentId}.use_ragas_evaluator', true)) {
    return $this->detectHallucinations($output, $context);
}
return new VerificationResult(violations: []);
```

**Rationale:**

- Per-agent opt-in (some agents may not need it)
- Graceful degradation (service outage doesn't fail verification)
- Testable: Can mock HTTP response easily
- Performance: No delay if RAGAS disabled
- Flexibility: Can add more agents without modifying code

**Alternative 1 (Always Required):**

```php
// Fail if RAGAS unavailable
$hallucinations = $this->detectHallucinations($output, $context);
if (!$hallucinations) throw new RAGASUnavailableException();
```

**Why not chosen:**

- Creates hard dependency on external service
- System goes down if RAGAS goes down (bad for SaaS)
- No fallback for degraded RAGAS performance

**Alternative 2 (Never Used):**

```php
// Skip hallucination check
$hallucinations = [];
```

**Why not chosen:**

- Loses LLM output quality assurance
- Can't detect hallucinations at all
- Reduces verification completeness

**Decision:** Conditional + graceful degradation provides best balance of safety and robustness.

---

## Error Handling Strategy

### Outer Try-Catch (Chosen)

```php
public function verify($agentId, $output, $context): VerificationResult
{
    try {
        // All validation steps
        $result = new VerificationResult(violations: [...]);
        return $result;
    } catch (Throwable $e) {
        // Log but don't throw
        $this->auditVerification($agentId, $output, $context,
            verification_failed: true, error: $e->getMessage());

        // Return safe default
        return VerificationResult::rejected()
            ->withError($e->getMessage());
    }
}
```

**Rationale:**

- Never throws from verify() (fail-safe pattern)
- Caller always gets VerificationResult
- Errors captured in audit trail
- Error information available in result object

**Alternative (Inner Try-Catch per Validator):**

```php
try {
    $this->validateSchema(...);
} catch (Exception $e) {
    // Log but continue
    $violations[] = new VerificationViolation(...);
}

try {
    $this->validateBusinessRules(...);
} catch (Exception $e) {
    // Log but continue
    $violations[] = new VerificationViolation(...);
}
```

**Why not chosen:**

- Verbose, repeated error handling
- Inconsistent error recovery across validators
- Harder to audit (unclear which validator errored)
- Lost error context (which step failed?)

**Decision:** Outer try-catch is cleaner, more auditable, and fail-safe.

---

## Multi-Tenant Enforcement Pattern

### Zero-Trust Model (Chosen)

```php
private function validateMultiTenant(
    array $output,
    array $context
): VerificationResult
{
    $violations = [];

    // Rule 1: organization_id must be present
    $orgId = $context['organization_id'] ?? null;
    if (!$orgId) {
        $violations[] = new VerificationViolation(
            rule: 'missing_organization_id'
        );
    }

    // Rule 2: No cross-tenant data detected
    $crossTenantPatterns = [
        'organization_ids',  // Array of IDs (not just current org)
        'all_users',         // Global data reference
        'shared_workspace',  // Shared vs org-specific
    ];

    foreach ($output as $key => $value) {
        if (in_array($key, $crossTenantPatterns, true)) {
            $violations[] = new VerificationViolation(
                rule: 'cross_tenant_data_detected',
                message: "Field '{$key}' indicates multi-tenant data"
            );
        }
    }

    return new VerificationResult(violations: $violations);
}
```

**Rationale:**

- Security first: Validate org_id before any other validation
- Defense in depth: Multiple checks for cross-tenant data
- Pattern-based: Key names that indicate shared data
- Regex fallback: Catch case variations

**Alternative (Trust-Provided):**

```php
// Assume framework/middleware ensures organization_id
// Skip this validation entirely
```

**Why not chosen:**

- Violates zero-trust principle
- Middleware could be bypassed
- Bug in framework = data leak
- No audit trail of multi-tenant check

**Decision:** Zero-trust model prevents accidental cross-tenant data exposure.

---

## Configuration Management Pattern

### Externalized Config (Chosen)

```php
// config/verification_rules.php
return [
    'agents' => [
        'Estratega de Talento' => [
            'use_ragas_evaluator' => true,
            'fields' => [
                'strategy' => ['type' => 'string', 'min' => 5, 'required' => true],
                'confidence_score' => ['type' => 'float', 'min' => 0.5, 'max' => 1.0],
            ],
            'constraints' => [
                'max_recommendations' => 10,
                'max_timeline_months' => 24,
            ],
        ],
    ],
];

// In service:
$config = config("verification_rules.agents.{$agentId}");
$minScore = $config['fields']['confidence_score']['min'];
```

**Rationale:**

- Zero code changes to add new agent
- HR team can modify via config without developer
- Restart app vs. deploy code (same cost operationally)
- Centralized business rules source
- Easy to audit ("What rules applied for this verification?")

**Alternative 1 (Database-Driven):**

```php
$config = VerificationRule::where('agent_id', $agentId)->get();
```

**Why not chosen:**

- Extra DB query per verification (performance)
- Database becomes source of truth (harder to version)
- Migration complexity (config schema changes)
- Risk of partial config loads

**Alternative 2 (Hardcoded in Service):**

```php
if ($agentId === 'Estratega de Talento') {
    $minScore = 0.5;
} elseif ($agentId === 'Orquestador 360') {
    $minScore = 0.6;
}
```

**Why not chosen:**

- Duplicate rules across codebase
- Hard to discover all agents
- Code deployment needed for config changes
- Error-prone (easy to miss agents)

**Decision:** Externalized config provides best balance of flexibility and simplicity.

---

## Immutability Pattern (VerificationResult)

### Immutable Result Object (Chosen)

```php
final class VerificationResult
{
    public function __construct(
        public readonly Collection $violations,
        public readonly ?string $recommendation = null,
        public readonly ?float $score = null,
        // ...
    ) {}

    // No setters - mutations return new instance
    public function withRecommendation(string $rec): self
    {
        return new self(
            violations: $this->violations,
            recommendation: $rec,
            score: $this->score,
        );
    }
}
```

**Rationale:**

- Audit safety: Result can't be modified after creation
- Thread-safe: No state mutations across requests
- Audit clarity: Exact result that was returned
- Error prevention: Can't accidentally modify in callback

**Alternative (Mutable):**

```php
class VerificationResult
{
    public function setRecommendation(string $rec): void
    {
        $this->recommendation = $rec;  // Modifies in place
    }
}
```

**Why not chosen:**

- Someone could mutate result after verification (hidden bugs)
- Audit trail unclear (which result was actually returned?)
- Multi-thread issues if result mutated during logging
- Hard to debug (mutations spread across codebase)

**Decision:** Immutability ensures audit integrity and prevents hidden mutations.

---

## Dependency Injection Pattern

### Constructor Injection (Chosen)

```php
public function __construct(
    private RAGASEvaluator $ragas,
    private AuditTrailService $audit,
) {}

public function verify($agentId, $output, $context): VerificationResult
{
    // Use injected dependencies
    $hallucinations = $this->ragas->evaluate(...);
    $this->audit->log(...);
}
```

**Rationale:**

- Testable: Can mock RAGAS and AuditTrailService
- Flexible: Can swap implementations without code changes
- Clear dependencies: Constructor shows what service needs
- Laravel convention: Service locator anti-pattern

**Alternative (Service Locator):**

```php
$ragas = app(RAGASEvaluator::class);
$hallucinations = $ragas->evaluate(...);
```

**Why not chosen:**

- Less testable (hard to mock Magic app() call)
- Hidden dependencies (not clear from constructor)
- Runtime discovery (type checker can't verify)
- Laravel anti-pattern (explicit is better than implicit)

**Decision:** Constructor injection is Django/Laravel best practice for testability.

---

## Contradiction Detection Strategy

### Logical Consistency Checks (Chosen)

```php
private function detectContradictions(array $output): VerificationResult
{
    $violations = [];

    // Contradiction 1: Approved without approval date
    if ($output['approved'] === true && empty($output['approved_date'])) {
        $violations[] = new VerificationViolation(
            rule: 'approved_without_date'
        );
    }

    // Contradiction 2: Buy strategy with training recommendation
    if ($output['strategy'] === 'Buy' &&
        isset($output['recommendations']) &&
        in_array('Training', $output['recommendations'])
    ) {
        $violations[] = new VerificationViolation(
            rule: 'strategy_recommendation_conflict'
        );
    }

    return new VerificationResult(violations: $violations);
}
```

**Rationale:**

- Business logic validation (not just data format)
- Catches LLM reasoning errors (hallucinations at logic level)
- Domain-specific (rules learned from domain experts)
- High accuracy (false positives rare)

**Alternative (No Contradiction Checks):**

```php
// Skip logical validation
return new VerificationResult(violations: []);
```

**Why not chosen:**

- LLM can produce structurally valid but logically invalid outputs
- Misses reasoning errors (hallucinations at logic level)
- Reduces verification completeness
- HR teams expect logical consistency checks

**Decision:** Include contradiction checks as core part of verification completeness.

---

## Violations Collection Pattern

### Immutable Collections (Chosen)

```php
// In VerificationResult
private Collection $violations;  // Laravel Collection

public function __construct(
    public readonly Collection $violations = collect([])
) {}

// Immutable operations:
$rules = $this->violations
    ->pluck('rule')      // Extract rule names
    ->values()           // Reset keys
    ->toArray();         // Convert for comparison
```

**Rationale:**

- Laravel best practice for list handling
- Immutable (supports immutable result object)
- Easy filtering/mapping/chaining
- Familiar to Laravel developers
- Audit-friendly (exact list of violations)

**Alternative (Array):**

```php
private array $violations = [];

// Need to track changes manually
$this->violations[] = $violation;  // Mutates
```

**Why not chosen:**

- Native arrays mutable (harder to protect)
- Less fluent API (can't chain operations)
- Harder to map/filter
- Less idiomatic Laravel

**Decision:** Laravel Collections provide immutability + fluent API.

---

## Audit Trail Integration

### Verification-Level Logging (Chosen)

```php
private function auditVerification(
    string $agentId,
    array $output,
    array $context,
    VerificationResult $result
): void
{
    $this->audit->log(
        action: 'verification_completed',
        agent_id: $agentId,
        organization_id: $context['organization_id'],
        total_checks: $result->getTotalChecks(),
        error_count: $result->getErrorCount(),
        score: $result->score,
        recommendation: $result->recommendation,
        violation_rules: $result->violations
            ->pluck('rule')
            ->unique()
            ->values()
            ->toArray(),
        timestamp: now(),
    );
}
```

**Rationale:**

- Single audit log per verification (atomic)
- Captures complete verification state
- Linked to organization (multi-tenant audit)
- Uses AuditTrailService (company standard)
- Immutable log (no changes after creation)

**Alternative (Per-Validator Logging):**

```php
$this->audit->log('multi_tenant_check_passed');
$this->audit->log('schema_check_passed');
// ... more logs
```

**Why not chosen:**

- Log spam (5+ logs per verification)
- Harder to correlate (which logs belong to same verification?)
- More processing (5 DB inserts vs 1)
- Lost atomic context

**Decision:** Single verification-level log provides clear audit trail.

---

## Future Architecture Decisions Pending

### Decision: Per-Agent Business Rule Validators (Tarea 3)

**Options being considered:**

1. **Common Base Class + 9 Subclasses**
    - BaseBusinessRuleValidator
    - EstrategiaValidator extends Base
    - OrquestacionValidator extends Base
    - ... 7 more

2. **Strategy Pattern (9 Strategy Classes)**
    - BusinessRuleValidationStrategy interface
    - EstrategiaStrategy implements interface
    - inject (Strategy) not (Validator)

3. **Configuration-Driven (No Classes)**
    - All rules in verification_rules.php
    - Generic validateBusinessRules() reads config

**Trade-offs under evaluation:**

- Option 1: Type-safe, clear inheritance, larger codebase
- Option 2: Flexible, interchangeable, adds abstraction
- Option 3: Simple, config-driven, less type safety

**Recommendation (pending):** Option 1 (inheritance) provides best clarity for domain-specific rules.

---
