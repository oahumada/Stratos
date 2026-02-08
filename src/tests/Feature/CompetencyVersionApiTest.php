<?php

namespace Tests\Feature;

use App\Models\Competency;
use App\Models\Organizations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetencyVersionApiTest extends TestCase
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

    public function test_crud_competency_versions_api()
    {
        $comp = Competency::create(['organization_id' => $this->organization->id, 'name' => 'Comp API']);

        // Create
        $payload = ['name' => 'v1', 'description' => 'desc'];
        $resp = $this->actingAs($this->user)->postJson("/api/competencies/{$comp->id}/versions", $payload);
        $resp->assertStatus(201)->assertJson(['success' => true]);
        $id = $resp->json('data.id');

        // Index
        $list = $this->actingAs($this->user)->getJson("/api/competencies/{$comp->id}/versions");
        $list->assertStatus(200)->assertJson(['success' => true]);

        // Show
        $show = $this->actingAs($this->user)->getJson("/api/competencies/{$comp->id}/versions/{$id}");
        $show->assertStatus(200)->assertJson(['success' => true]);

        // Delete
        $del = $this->actingAs($this->user)->deleteJson("/api/competencies/{$comp->id}/versions/{$id}");
        $del->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('competency_versions', ['id' => $id]);
    }
}
