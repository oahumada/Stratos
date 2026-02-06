<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class ChangeSetApiTest extends TestCase
{
    public function test_create_changeset_requires_authentication(): void
    {
        $response = $this->postJson('/api/strategic-planning/scenarios/1/change-sets', []);
        $response->assertStatus(401);
    }
}
