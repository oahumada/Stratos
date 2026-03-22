<?php

use App\Jobs\EmbeddingIndexJob;
use App\Models\Embedding;
use App\Models\GuideFaq;
use App\Services\EmbeddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('indexes roles into embeddings table', function () {
    // Mock EmbeddingService to avoid external calls
    $mock = \Mockery::mock(EmbeddingService::class);
    $mock->shouldReceive('forRole')
        ->andReturn(array_fill(0, 8, 0.1));
    app()->instance(EmbeddingService::class, $mock);

    // Crear un rol mínimo usando factory existente si aplica
    $role = \App\Models\Roles::factory()->create();

    // Ejecutar job solo para roles
    $job = new EmbeddingIndexJob('role', $role->organization_id ?? null);
    $job->handle();

    $record = Embedding::where('resource_type', 'role')
        ->where('resource_id', $role->id)
        ->first();

    expect($record)->not->toBeNull();
    expect($record->organization_id)->toEqual($role->organization_id ?? null);
});

it('indexes guide faqs into embeddings table', function () {
    $mock = \Mockery::mock(EmbeddingService::class);
    $mock->shouldReceive('generate')
        ->andReturn(array_fill(0, 8, 0.2));
    app()->instance(EmbeddingService::class, $mock);

    $faq = GuideFaq::factory()->create();

    $job = new EmbeddingIndexJob('guide_faq', $faq->organization_id ?? null);
    $job->handle();

    $record = Embedding::where('resource_type', 'guide_faq')
        ->where('resource_id', $faq->id)
        ->first();

    expect($record)->not->toBeNull();
    expect($record->organization_id)->toEqual($faq->organization_id ?? null);
});
