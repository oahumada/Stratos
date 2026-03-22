<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('runs the embeddings reindex artisan command successfully', function () {
    $this->artisan('stratos:embeddings:reindex')
        ->assertExitCode(0);

    $this->artisan('stratos:embeddings:reindex people --delta')
        ->assertExitCode(0);
});
