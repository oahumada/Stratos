<?php

namespace Tests\Feature;

use App\Models\Competency;
use App\Models\Organizations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransformCompetencyTest extends TestCase
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

    public function test_transform_creates_competency_version()
    {
        $comp = Competency::create(['organization_id' => $this->organization->id, 'name' => 'Comp To Transform']);

        $payload = ['name' => 'Comp Transformed v1', 'description' => 'Transformed version', 'effective_from' => now()->toDateString()];

        $resp = $this->actingAs($this->user)->postJson("/api/competencies/{$comp->id}/transform", $payload);
        $resp->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('competency_versions', [
            'competency_id' => $comp->id,
            'name' => 'Comp Transformed v1',
            'evolution_state' => 'transformed',
        ]);
    }
}
