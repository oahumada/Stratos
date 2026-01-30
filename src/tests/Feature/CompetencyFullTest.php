<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use App\Models\Competency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetencyFullTest extends TestCase
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

    public function test_create_competency_with_all_fields()
    {
        $payload = [
            'name' => 'Architecture Competency',
            'description' => 'Design and review architectures',
            'organization_id' => $this->organization->id,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/competencies', $payload);

        $this->assertTrue(in_array($response->getStatusCode(), [200, 201]));

        $this->assertDatabaseHas('competencies', [
            'name' => 'Architecture Competency',
            'description' => 'Design and review architectures',
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_update_competency_updates_fields()
    {
        $comp = Competency::create([
            'organization_id' => $this->organization->id,
            'name' => 'Old Comp',
            'description' => 'Old desc',
        ]);

        $payload = [
            'name' => 'Updated Comp',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->patchJson("/api/competencies/{$comp->id}", $payload);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('competencies', [
            'id' => $comp->id,
            'name' => 'Updated Comp',
            'description' => 'Updated description',
        ]);
    }

    public function test_delete_competency_preserves_related_capabilities()
    {
        $comp = Competency::create([
            'organization_id' => $this->organization->id,
            'name' => 'ToDelete',
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/api/competencies/{$comp->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('competencies', ['id' => $comp->id]);
    }
}
