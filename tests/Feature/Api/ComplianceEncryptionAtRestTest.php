<?php

use App\Models\LLMEvaluation;
use App\Models\Organization;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('encrypts strategic role fields at rest and decrypts on read', function () {
    $organization = Organization::factory()->create();

    $description = 'Descripción estratégica confidencial';
    $purpose = 'Propósito sensible del rol';
    $expectedResults = 'Resultados esperados privados';

    $role = Roles::query()->create([
        'organization_id' => $organization->id,
        'name' => 'Role Encryption Test',
        'description' => $description,
        'purpose' => $purpose,
        'expected_results' => $expectedResults,
    ]);

    $raw = DB::table('roles')->where('id', $role->id)->first(['description', 'purpose', 'expected_results']);

    expect($raw)->not->toBeNull()
        ->and($raw->description)->not->toBe($description)
        ->and($raw->purpose)->not->toBe($purpose)
        ->and($raw->expected_results)->not->toBe($expectedResults)
        ->and(Crypt::decryptString($raw->description))->toBe($description)
        ->and(Crypt::decryptString($raw->purpose))->toBe($purpose)
        ->and(Crypt::decryptString($raw->expected_results))->toBe($expectedResults);

    $freshRole = Roles::query()->findOrFail($role->id);

    expect($freshRole->description)->toBe($description)
        ->and($freshRole->purpose)->toBe($purpose)
        ->and($freshRole->expected_results)->toBe($expectedResults);
});

it('supports legacy plaintext role data without breaking reads', function () {
    $organization = Organization::factory()->create();

    $legacyDescription = 'Legacy plain description';

    $roleId = DB::table('roles')->insertGetId([
        'organization_id' => $organization->id,
        'name' => 'Legacy Role Plaintext',
        'description' => $legacyDescription,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $role = Roles::query()->findOrFail($roleId);

    expect($role->description)->toBe($legacyDescription);
});

it('encrypts llm evaluation private content at rest and decrypts on read', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $organization->id]);

    $inputContent = 'Prompt confidencial para evaluación';
    $outputContent = 'Salida privada del modelo';
    $contextContent = 'Contexto sensible del caso';

    $evaluation = LLMEvaluation::factory()->create([
        'organization_id' => $organization->id,
        'created_by' => $user->id,
        'input_content' => $inputContent,
        'output_content' => $outputContent,
        'context_content' => $contextContent,
    ]);

    $raw = DB::table('llm_evaluations')
        ->where('id', $evaluation->id)
        ->first(['input_content', 'output_content', 'context_content']);

    expect($raw)->not->toBeNull()
        ->and($raw->input_content)->not->toBe($inputContent)
        ->and($raw->output_content)->not->toBe($outputContent)
        ->and($raw->context_content)->not->toBe($contextContent)
        ->and(Crypt::decryptString($raw->input_content))->toBe($inputContent)
        ->and(Crypt::decryptString($raw->output_content))->toBe($outputContent)
        ->and(Crypt::decryptString($raw->context_content))->toBe($contextContent);

    $freshEvaluation = LLMEvaluation::query()->findOrFail($evaluation->id);

    expect($freshEvaluation->input_content)->toBe($inputContent)
        ->and($freshEvaluation->output_content)->toBe($outputContent)
        ->and($freshEvaluation->context_content)->toBe($contextContent);
});

it('supports legacy plaintext llm evaluation data without breaking reads', function () {
    $organization = Organization::factory()->create();

    $input = 'Legacy input plain';
    $output = 'Legacy output plain';
    $context = 'Legacy context plain';

    $evaluationId = DB::table('llm_evaluations')->insertGetId([
        'uuid' => (string) Str::uuid(),
        'organization_id' => $organization->id,
        'evaluable_type' => 'ScenarioGeneration',
        'evaluable_id' => 1,
        'llm_provider' => 'mock',
        'input_content' => $input,
        'output_content' => $output,
        'context_content' => $context,
        'status' => 'completed',
        'is_latest' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $evaluation = LLMEvaluation::query()->findOrFail($evaluationId);

    expect($evaluation->input_content)->toBe($input)
        ->and($evaluation->output_content)->toBe($output)
        ->and($evaluation->context_content)->toBe($context);
});
