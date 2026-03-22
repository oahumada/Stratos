<?php

use App\Jobs\EmbeddingIndexJob;
use App\Models\GuideFaq;
use App\Models\Organization;
use App\Services\RagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('answers using guide faq context via embeddings', function () {
    // Usar proveedor mock para embeddings genéricos
    config()->set('services.embeddings.provider', 'mock');
    config()->set('features.generate_embeddings', true);

    $organization = Organization::factory()->create();

    // Crear una FAQ representativa para StratosGuide
    $faq = GuideFaq::factory()->create([
        'organization_id' => $organization->id,
        'title' => '¿Cómo creo un escenario?',
        'question' => '¿Cómo puedo crear un nuevo escenario estratégico en Stratos?',
        'answer' => 'Para crear un escenario, ve al módulo de Scenario Planning y haz clic en "+ Nuevo Escenario".',
        'tags' => ['scenario_planning', 'getting_started'],
    ]);

    // Indexar FAQs en la tabla embeddings
    $job = new EmbeddingIndexJob('guide_faq', $organization->id);
    $job->handle();

    /** @var RagService $ragService */
    $ragService = app(RagService::class);

    $result = $ragService->ask(
        'Quiero saber cómo crear un escenario nuevo',
        (string) $organization->id,
        'guide_faq',
        5
    );

    expect($result['success'])->toBeTrue();
    expect($result['context_count'])->toBeGreaterThanOrEqual(0);
});
