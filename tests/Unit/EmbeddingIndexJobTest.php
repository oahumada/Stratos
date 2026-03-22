<?php

use App\Jobs\EmbeddingIndexJob;
use App\Models\Embedding;
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
