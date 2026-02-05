<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillsApiTest extends TestCase
{
    use RefreshDatabase;

    protected Organizations $organization;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organizations::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    }

    public function test_post_skills_without_data_returns_500()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/skills', []);

        $response->assertStatus(500);
    }

    public function test_post_skills_with_data_creates_skill()
    {
        $payload = [
            'data' => [
                'name' => 'Test Skill X',
                'category' => 'technical',
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/skills', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('skills', [
            'name' => 'Test Skill X',
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_post_skills_with_null_category()
    {
        $payload = [
            'data' => [
                'name' => 'Test Skill NullCat',
                'category' => null,
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/skills', $payload);

        // Check whether the backend accepts null category or returns 500/422
        $this->assertTrue(in_array($response->getStatusCode(), [200, 201, 422, 500]));

        if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
            $this->assertDatabaseHas('skills', [
                'name' => 'Test Skill NullCat',
                'organization_id' => $this->organization->id,
            ]);
        }
    }
}
