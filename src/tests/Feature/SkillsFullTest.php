<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use App\Models\Skills;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillsFullTest extends TestCase
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

    public function test_create_skill_with_all_fields()
    {
        $payload = [
            'data' => [
                'name' => 'End-to-end testing',
                'category' => 'engineering',
                'complexity_level' => 'strategic',
                'description' => 'Tests full stack flows',
                'is_critical' => true,
                'scope_type' => 'domain',
                'domain_tag' => 'testing',
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/skills', $payload);

        // Accept multiple successful status codes depending on implementation
        $this->assertTrue(in_array($response->getStatusCode(), [200, 201, 204]), 'Unexpected status for skill create: ' . $response->getStatusCode());

        // Verify record exists with provided fields
        $this->assertDatabaseHas('skills', [
            'name' => 'End-to-end testing',
            'organization_id' => $this->organization->id,
            'category' => 'engineering',
            'complexity_level' => 'strategic',
            'description' => 'Tests full stack flows',
            'is_critical' => 1,
            'scope_type' => 'domain',
            'domain_tag' => 'testing',
        ]);
    }

    public function test_update_skill_updates_all_fields()
    {
        $skill = Skills::create([
            'organization_id' => $this->organization->id,
            'name' => 'Old Skill',
            'category' => 'legacy',
        ]);

        $payload = [
            'name' => 'Updated Skill',
            'category' => 'modern',
            'complexity_level' => 'tactical',
            'description' => 'Now updated',
            'is_critical' => false,
            'scope_type' => 'transversal',
            'domain_tag' => 'qa',
        ];

        // The FormSchema repository expects payload inside `data` and the `id` included
        $updatePayload = ['data' => array_merge(['id' => $skill->id], $payload)];
        $response = $this->actingAs($this->user)
            ->patchJson("/api/skills/{$skill->id}", $updatePayload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'Updated Skill',
            'category' => 'modern',
            'complexity_level' => 'tactical',
            'description' => 'Now updated',
            'is_critical' => 0,
            'scope_type' => 'transversal',
            'domain_tag' => 'qa',
        ]);
    }
}
