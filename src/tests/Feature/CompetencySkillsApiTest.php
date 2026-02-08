<?php

namespace Tests\Feature;

use App\Models\Capability;
use App\Models\Competency;
use App\Models\Organizations;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompetencySkillsApiTest extends TestCase
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

    public function test_get_competency_returns_skills()
    {
        $capability = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap Test A']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $capability->id, 'name' => 'Comp A']);

        $s1 = Skill::create(['organization_id' => $this->organization->id, 'name' => 'Skill One', 'category' => 'technical']);
        $s2 = Skill::create(['organization_id' => $this->organization->id, 'name' => 'Skill Two', 'category' => 'technical']);

        DB::table('competency_skills')->insert([
            ['competency_id' => $comp->id, 'skill_id' => $s1->id, 'weight' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['competency_id' => $comp->id, 'skill_id' => $s2->id, 'weight' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/competencies/{$comp->id}");

        $response->assertStatus(200)->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'data' => ['id', 'name', 'skills']]);

        $json = $response->json();
        $this->assertCount(2, $json['data']['skills']);
    }

    public function test_post_competency_attach_existing_skill_creates_pivot()
    {
        $capability = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap Test B']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $capability->id, 'name' => 'Comp B']);
        $skill = Skill::create(['organization_id' => $this->organization->id, 'name' => 'Attach Skill', 'category' => 'technical']);

        $response = $this->actingAs($this->user)
            ->postJson("/api/competencies/{$comp->id}/skills", ['skill_id' => $skill->id]);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('competency_skills', [
            'competency_id' => $comp->id,
            'skill_id' => $skill->id,
        ]);
    }

    public function test_post_competency_create_and_attach_skill()
    {
        $capability = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap Test C']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $capability->id, 'name' => 'Comp C']);

        $payload = [
            'skill' => ['name' => 'New Skill X', 'category' => 'technical'],
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/competencies/{$comp->id}/skills", $payload);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('skills', [
            'name' => 'New Skill X',
            'organization_id' => $this->organization->id,
        ]);

        $skill = Skill::where('name', 'New Skill X')->first();
        $this->assertNotNull($skill);

        $this->assertDatabaseHas('competency_skills', [
            'competency_id' => $comp->id,
            'skill_id' => $skill->id,
        ]);
    }
}
