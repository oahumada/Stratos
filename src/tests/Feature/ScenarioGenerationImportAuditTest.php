<?php

namespace Tests\Feature;

use App\Models\ScenarioGeneration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ScenarioGenerationImportAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_audit_records_skip_validation_and_success()
    {
        $org = \App\Models\Organizations::factory()->create();
        $user = User::factory()->create(['organization_id' => $org->id]);

        $this->actingAs($user);

        $llmResponse = [
            'scenario_metadata' => ['name' => 'Audit Scenario', 'description' => 'desc'],
            'capabilities' => [],
        ];

        $generation = ScenarioGeneration::create([
            'organization_id' => $org->id,
            'created_by' => $user->id,
            'prompt' => 'redacted prompt',
            'llm_response' => $llmResponse,
            'status' => 'complete',
            'redacted' => true,
        ]);

        // 1) Feature flag disabled -> should record skipped_feature_flag and return 403
        config(['features.import_generation' => false]);

        $res = $this->postJson("/api/strategic-planning/scenarios/generate/{$generation->id}/accept", ['import' => true]);
        $res->assertStatus(403);

        $generation->refresh();
        $this->assertArrayHasKey('import_audit', $generation->metadata ?? []);
        $last = last($generation->metadata['import_audit']);
        $this->assertEquals('skipped_feature_flag', $last['result']);

        // 2) Validation failure -> validator returns invalid -> record validation_failed and return 422
        config(['features.import_generation' => true, 'features.validate_llm_response' => true]);

        $validatorMock = Mockery::mock(\App\Services\LlmResponseValidator::class);
        $validatorMock->shouldReceive('validate')->andReturn(['valid' => false, 'errors' => ['missing_fields']]);
        $this->app->instance(\App\Services\LlmResponseValidator::class, $validatorMock);

        $res2 = $this->postJson("/api/strategic-planning/scenarios/generate/{$generation->id}/accept", ['import' => true]);
        $res2->assertStatus(422);

        $generation->refresh();
        $this->assertArrayHasKey('import_audit', $generation->metadata ?? []);
        $last2 = last($generation->metadata['import_audit']);
        $this->assertEquals('validation_failed', $last2['result']);

        // 3) Successful import -> validator valid + importer returns report -> record success
        $validatorOk = Mockery::mock(\App\Services\LlmResponseValidator::class);
        $validatorOk->shouldReceive('validate')->andReturn(['valid' => true, 'errors' => []]);
        $this->app->instance(\App\Services\LlmResponseValidator::class, $validatorOk);

        $importerMock = Mockery::mock(\App\Services\ScenarioGenerationImporter::class);
        $importerMock->shouldReceive('importGeneration')->once()->andReturn(['created' => ['capabilities' => 0]]);
        $this->app->instance(\App\Services\ScenarioGenerationImporter::class, $importerMock);

        $res3 = $this->postJson("/api/strategic-planning/scenarios/generate/{$generation->id}/accept", ['import' => true]);
        $res3->assertStatus(201);

        $generation->refresh();
        $this->assertArrayHasKey('import_audit', $generation->metadata ?? []);
        $last3 = last($generation->metadata['import_audit']);
        $this->assertEquals('success', $last3['result']);
        // Verify the importer report was recorded in the audit entry
        $this->assertArrayHasKey('report', $last3);
        $this->assertEquals(['created' => ['capabilities' => 0]], $last3['report']);
    }
}
