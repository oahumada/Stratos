<?php

use App\Models\EmbeddingAuditLog;
use App\Models\IntelligenceMetric;
use App\Models\Organization;
use App\Models\User;
use App\Services\Intelligence\IncidentResponseService;
use App\Services\Intelligence\SlaMonitorService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

// ============ MULTI-TENANT ISOLATION ============

beforeEach(function () {
    $this->orgA = Organization::factory()->create();
    $this->orgB = Organization::factory()->create();

    $this->userA = User::factory()->create([
        'organization_id' => $this->orgA->id,
    ]);
    $this->userB = User::factory()->create([
        'organization_id' => $this->orgB->id,
    ]);
});

it('intelligence metrics are scoped by organization_id', function () {
    IntelligenceMetric::factory()->create([
        'organization_id' => $this->orgA->id,
        'metric_type' => 'rag',
    ]);
    IntelligenceMetric::factory()->create([
        'organization_id' => $this->orgB->id,
        'metric_type' => 'rag',
    ]);

    $metricsA = IntelligenceMetric::where('organization_id', $this->orgA->id)->get();
    $metricsB = IntelligenceMetric::where('organization_id', $this->orgB->id)->get();

    expect($metricsA)->toHaveCount(1)
        ->and($metricsB)->toHaveCount(1)
        ->and($metricsA->first()->organization_id)->toBe($this->orgA->id);
});

it('embedding audit logs are scoped by organization_id', function () {
    EmbeddingAuditLog::factory()->create([
        'organization_id' => $this->orgA->id,
        'action' => 'created',
    ]);
    EmbeddingAuditLog::factory()->create([
        'organization_id' => $this->orgB->id,
        'action' => 'flagged',
    ]);

    $logsA = EmbeddingAuditLog::where('organization_id', $this->orgA->id)->get();
    $logsB = EmbeddingAuditLog::where('organization_id', $this->orgB->id)->get();

    expect($logsA)->toHaveCount(1)
        ->and($logsB)->toHaveCount(1)
        ->and($logsA->first()->organization_id)->toBe($this->orgA->id);
});

// ============ GATE AUTHORIZATION ============

it('rag.ask gate allows user with organization', function () {
    $this->actingAs($this->userA);
    expect(Gate::allows('rag.ask'))->toBeTrue();
});

it('rag.ask gate denies user without organization', function () {
    $noOrgUser = User::factory()->create(['organization_id' => null]);
    $this->actingAs($noOrgUser);
    expect(Gate::denies('rag.ask'))->toBeTrue();
});

// ============ EMBEDDING AUDIT TRAIL ============

it('records embedding audit log entries', function () {
    $log = EmbeddingAuditLog::record(
        $this->orgA->id,
        1,
        'flagged',
        ['reason' => 'hallucination'],
        'reindex_job',
    );

    expect($log)->toBeInstanceOf(EmbeddingAuditLog::class)
        ->and($log->organization_id)->toBe($this->orgA->id)
        ->and($log->action)->toBe('flagged')
        ->and($log->triggered_by)->toBe('reindex_job')
        ->and($log->changes)->toBe(['reason' => 'hallucination']);
});

// ============ SLA MONITORING ============

it('sla check returns compliant when no data', function () {
    $service = new SlaMonitorService;
    $result = $service->checkSla($this->orgA->id);

    expect($result['compliant'])->toBeTrue()
        ->and($result['violations'])->toBeEmpty();
});

it('sla check detects latency violation', function () {
    // Create metrics with high latency (>2s = >2000ms)
    IntelligenceMetric::factory()->count(20)->create([
        'organization_id' => $this->orgA->id,
        'duration_ms' => 3000,
        'success' => true,
    ]);

    $service = new SlaMonitorService;
    $result = $service->checkSla($this->orgA->id);

    expect($result['compliant'])->toBeFalse()
        ->and(collect($result['violations'])->pluck('metric'))->toContain('latency_p95');
});

it('sla check detects low success rate', function () {
    IntelligenceMetric::factory()->count(10)->create([
        'organization_id' => $this->orgA->id,
        'success' => false,
        'duration_ms' => 100,
    ]);
    IntelligenceMetric::factory()->count(2)->create([
        'organization_id' => $this->orgA->id,
        'success' => true,
        'duration_ms' => 100,
    ]);

    $service = new SlaMonitorService;
    $result = $service->checkSla($this->orgA->id);

    expect($result['compliant'])->toBeFalse()
        ->and(collect($result['violations'])->pluck('metric'))->toContain('success_rate');
});

it('sla alert logs violation', function () {
    Log::shouldReceive('channel')->with('agents')->andReturnSelf();
    Log::shouldReceive('critical')->once();

    $service = new SlaMonitorService;
    $slaResult = [
        'compliant' => false,
        'metrics' => ['total_calls' => 10],
        'violations' => [['metric' => 'latency_p95', 'threshold' => 2.0, 'actual' => 3.5]],
    ];

    $service->alertOnViolation($slaResult, $this->orgA->id);
});

// ============ INCIDENT RESPONSE ============

it('reports hallucination incident correctly', function () {
    $service = new IncidentResponseService;
    $result = $service->reportIncident(
        IncidentResponseService::INCIDENT_HALLUCINATION,
        ['query' => 'test', 'response' => 'bad response'],
        $this->orgA->id,
    );

    expect($result['severity'])->toBe('high')
        ->and($result['type'])->toBe('hallucination_detected')
        ->and($result['actions_taken'])->toContain('flagged_response');

    // Verify stored in intelligence_metrics
    $metric = IntelligenceMetric::where('organization_id', $this->orgA->id)
        ->where('metric_type', 'incident')
        ->first();

    expect($metric)->not->toBeNull()
        ->and($metric->metadata['incident_type'])->toBe('hallucination_detected');
});

it('reports security incident with critical severity', function () {
    $service = new IncidentResponseService;
    $result = $service->reportIncident(
        IncidentResponseService::INCIDENT_SECURITY,
        ['reason' => 'unauthorized access attempt'],
        $this->orgA->id,
    );

    expect($result['severity'])->toBe('critical')
        ->and($result['actions_taken'])->toContain('blocked');
});

it('resolves incident by id', function () {
    $service = new IncidentResponseService;
    $result = $service->reportIncident(
        IncidentResponseService::INCIDENT_SLA_BREACH,
        ['metric' => 'latency'],
        $this->orgA->id,
    );

    $service->resolveIncident($result['incident_id']);

    $metric = IntelligenceMetric::where('metric_type', 'incident')
        ->whereJsonContains('metadata->incident_id', $result['incident_id'])
        ->first();

    expect($metric->metadata['resolved'])->toBeTrue()
        ->and($metric->metadata)->toHaveKey('resolved_at');
});

it('incident history is scoped by organization', function () {
    $service = new IncidentResponseService;

    $service->reportIncident(IncidentResponseService::INCIDENT_HALLUCINATION, ['q' => '1'], $this->orgA->id);
    $service->reportIncident(IncidentResponseService::INCIDENT_SLA_BREACH, ['q' => '2'], $this->orgB->id);

    $historyA = $service->getIncidentHistory($this->orgA->id);
    $historyB = $service->getIncidentHistory($this->orgB->id);

    expect($historyA)->toHaveCount(1)
        ->and($historyB)->toHaveCount(1)
        ->and($historyA[0]['type'])->toBe('hallucination_detected')
        ->and($historyB[0]['type'])->toBe('sla_breach');
});
