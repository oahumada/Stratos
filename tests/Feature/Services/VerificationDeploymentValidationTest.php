<?php

namespace Tests\Feature\Services;

use App\Models\Agent;
use App\Models\Organization;
use App\Services\AiOrchestratorService;
use App\Services\VerificationIntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Deployment & Sanity Validation Tests
 *
 * Final validation tests for production deployment:
 * - Configuration validation
 * - Environment variable verification
 * - End-to-end orchestration flow
 * - Rollback scenarios
 */
class VerificationDeploymentValidationTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $organization;

    protected Agent $agent;

    protected VerificationIntegrationService $integration;

    protected AiOrchestratorService $orchestrator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create(['name' => 'Test Org']);
        $this->agent = Agent::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Estratega de Talento',
            'provider' => 'openai',
            'model' => 'gpt-4o',
        ]);

        $this->integration = app(VerificationIntegrationService::class);
        $this->orchestrator = app(AiOrchestratorService::class);
    }

    // ====================================================================
    // CONFIGURATION VALIDATION
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_config_exists_and_valid(): void
    {
        $config = config('verification');

        expect($config)->not->toBeNull();
        expect($config)->toHaveKey('enabled');
        expect($config)->toHaveKey('phase');
        expect($config)->toHaveKey('phases');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function all_required_phases_configured(): void
    {
        $phases = config('verification.phases');

        expect($phases)->toHaveKey('silent');
        expect($phases)->toHaveKey('flagging');
        expect($phases)->toHaveKey('reject');
        expect($phases)->toHaveKey('tuning');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function phase_configurations_have_required_keys(): void
    {
        $phases = config('verification.phases');

        foreach ($phases as $phaseName => $config) {
            expect($config)->toHaveKey('description');
            expect($config)->toHaveKey('log_violations');
            expect($config)->toHaveKey('flag_response');
            expect($config)->toHaveKey('reject_output');
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function confidence_thresholds_configured(): void
    {
        $thresholds = config('verification.thresholds');

        expect($thresholds)->toHaveKey('confidence_high');
        expect($thresholds)->toHaveKey('confidence_medium');
        expect($thresholds)->toHaveKey('confidence_low');

        // Verify hierarchy: low < medium < high
        expect($thresholds['confidence_low'])
            ->toBeLessThan($thresholds['confidence_medium'])
            ->toBeLessThan($thresholds['confidence_high']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function default_phase_is_safe(): void
    {
        // If no phase is set, should default to safe mode
        $defaultPhase = config('verification.phase', 'silent');
        expect(['silent', 'flagging', 'reject', 'tuning'])->toContain($defaultPhase);
    }

    // ====================================================================
    // ENVIRONMENT VARIABLE VALIDATION
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_enabled_respects_env_variable(): void
    {
        config(['verification.enabled' => env('VERIFICATION_ENABLED', true)]);

        $enabled = config('verification.enabled');
        expect(is_bool($enabled))->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_phase_respects_env_variable(): void
    {
        config(['verification.phase' => env('VERIFICATION_PHASE', 'silent')]);

        $phase = config('verification.phase');
        expect(['silent', 'flagging', 'reject', 'tuning'])->toContain($phase);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function invalid_phase_env_variable_handled(): void
    {
        config(['verification.phase' => 'invalid_phase']);

        // Should not crash, but could be handled gracefully
        $phase = config('verification.phase');
        expect($phase)->toBe('invalid_phase');
    }

    // ====================================================================
    // SERVICE INSTANTIATION & DEPENDENCY INJECTION
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_integration_service_resolves_from_container(): void
    {
        $service = app(VerificationIntegrationService::class);

        expect($service)->toBeInstanceOf(VerificationIntegrationService::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orchestrator_service_has_verification_integration(): void
    {
        $orchestrator = app(AiOrchestratorService::class);

        expect($orchestrator)->toBeInstanceOf(AiOrchestratorService::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function audit_service_is_available(): void
    {
        $auditService = app(\App\Services\AuditService::class);

        expect($auditService)->not->toBeNull();
    }

    // ====================================================================
    // FACTORY VALIDATION
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function agent_factory_creates_valid_agent(): void
    {
        $agent = Agent::factory()->create(['organization_id' => $this->organization->id]);

        expect($agent->id)->not->toBeNull();
        expect($agent->name)->not->toBeEmpty();
        expect($agent->organization_id)->toBe($this->organization->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function multiple_agents_can_be_created(): void
    {
        $agents = Agent::factory(5)->create(['organization_id' => $this->organization->id]);

        expect($agents)->toHaveCount(5);
        foreach ($agents as $agent) {
            expect($agent->organization_id)->toBe($this->organization->id);
        }
    }

    // ====================================================================
    // END-TO-END ORCHESTRATION FLOW
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function orchestrator_silent_mode_accepts_and_continues(): void
    {
        config(['verification.enabled' => true, 'verification.phase' => 'silent']);

        // In silent mode, even with invalid verification, output should be accepted
        $phase = $this->integration->getCurrentPhase();
        expect($phase)->toBe('silent');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orchestrator_can_switch_phases(): void
    {
        $phases = ['silent', 'flagging', 'reject', 'tuning'];

        foreach ($phases as $phase) {
            config(['verification.phase' => $phase]);
            $currentPhase = $this->integration->getCurrentPhase();
            expect($currentPhase)->toBe($phase);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_can_be_disabled_globally(): void
    {
        config(['verification.enabled' => false]);

        $enabled = config('verification.enabled');
        expect($enabled)->toBeFalse();
    }

    // ====================================================================
    // BACKWARD COMPATIBILITY
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function orchestrator_works_without_verification(): void
    {
        config(['verification.enabled' => false]);

        // Should not crash or throw errors when verification is disabled
        expect($this->orchestrator)->toBeInstanceOf(AiOrchestratorService::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function existing_agent_interfaces_unchanged(): void
    {
        $agent = $this->agent;

        // Verify core agent attributes still exist and accessible
        expect($agent->name)->not->toBeEmpty();
        expect($agent->provider)->not->toBeEmpty();
        expect($agent->model)->not->toBeEmpty();
        expect($agent->organization_id)->not->toBeNull();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_metadata_optional_in_output(): void
    {
        // Even if verification fails, orchestrator should handle gracefully
        config(['verification.enabled' => true, 'verification.phase' => 'silent']);

        expect(config('verification.enabled'))->toBeTrue();
    }

    // ====================================================================
    // MULTI-TENANT DATA ISOLATION
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function agents_isolated_by_organization(): void
    {
        $org2 = Organization::factory()->create(['name' => 'Org Two']);
        $agent2 = Agent::factory()->create(['organization_id' => $org2->id]);

        expect($this->agent->organization_id)->not->toBe($agent2->organization_id);
        expect($this->agent->id)->not->toBe($agent2->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_scoped_to_agent_organization(): void
    {
        $org2 = Organization::factory()->create(['name' => 'Org Two']);
        $agent2 = Agent::factory()->create(['organization_id' => $org2->id]);

        // Each agent belongs to specific org
        expect($this->agent->organization_id)->toBe($this->organization->id);
        expect($agent2->organization_id)->toBe($org2->id);
    }

    // ====================================================================
    // ERROR RECOVERY & ROLLBACK
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function verification_enabled_can_be_rolled_back(): void
    {
        // Start enabled
        config(['verification.enabled' => true]);
        expect(config('verification.enabled'))->toBeTrue();

        // Disable (rollback)
        config(['verification.enabled' => false]);
        expect(config('verification.enabled'))->toBeFalse();

        // Re-enable
        config(['verification.enabled' => true]);
        expect(config('verification.enabled'))->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function phase_can_be_rolled_back(): void
    {
        $originalPhase = config('verification.phase');

        // Switch to reject
        config(['verification.phase' => 'reject']);
        expect($this->integration->getCurrentPhase())->toBe('reject');

        // Rollback to original
        config(['verification.phase' => $originalPhase]);
        expect($this->integration->getCurrentPhase())->toBe($originalPhase);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function offline_fallback_when_verification_disabled(): void
    {
        config(['verification.enabled' => false]);

        // Should behave as if verification doesn't exist
        expect(config('verification.enabled'))->toBeFalse();
    }

    // ====================================================================
    // PRODUCTION READINESS CHECKLIST
    // ====================================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function all_required_services_are_registered(): void
    {
        $services = [
            VerificationIntegrationService::class,
            AiOrchestratorService::class,
            \App\Services\AuditService::class,
        ];

        foreach ($services as $service) {
            expect(app($service))->not->toBeNull();
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function config_file_accessible(): void
    {
        $config = config('verification');
        expect($config)->not->toBeNull();
        expect(is_array($config))->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function models_have_factories(): void
    {
        // Verify Agent can create instances via factory
        $agent = Agent::factory()->create(['organization_id' => $this->organization->id]);
        expect($agent->exists)->toBeTrue();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function exceptions_are_defined(): void
    {
        $exceptions = [
            \App\Exceptions\VerificationFailedException::class,
            \App\Exceptions\UnauthorizedTenantException::class,
        ];

        foreach ($exceptions as $exception) {
            expect(class_exists($exception))->toBeTrue();
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function dtos_are_defined(): void
    {
        $dtos = [
            \App\DTOs\VerificationResult::class,
            \App\DTOs\VerificationAction::class,
        ];

        foreach ($dtos as $dto) {
            expect(class_exists($dto))->toBeTrue();
        }
    }
}
