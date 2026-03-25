<?php

use App\Services\EmbeddingService;
use App\Services\LLMClient;
use App\Services\RagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

it('postFilter redacts PII from answer', function () {
    $rag = new RagService(app(EmbeddingService::class), app(LLMClient::class));

    $answer = 'User email: john@example.com';
    $filtered = $rag->postFilter($answer);

    expect($filtered)->not->toContain('john@example.com');
});
