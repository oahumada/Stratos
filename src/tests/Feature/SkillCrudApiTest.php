<?php

namespace Tests\Feature;

use App\Models\Organizations;
use App\Models\User;
use App\Models\Competency;
use App\Models\Skill;
use App\Models\Capability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SkillCrudApiTest extends TestCase
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

    public function test_create_skill_via_formschema_store_saves_all_fields()
    {
        $payload = [
            'organization_id' => $this->organization->id,
            'name' => 'Skill Full Create',
            'category' => 'technical',
            'complexity_level' => 'strategic',
            'description' => 'Detailed desc',
            'is_critical' => true,
            'scope_type' => 'specific',
            'domain_tag' => 'platform',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/skills', $payload);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('skills', [
            'name' => 'Skill Full Create',
            'organization_id' => $this->organization->id,
            'category' => 'technical',
            'complexity_level' => 'strategic',
            'is_critical' => 1,
            'scope_type' => 'specific',
            'domain_tag' => 'platform',
        ]);
    }

    public function test_update_skill_via_formschema_updates_fields()
    {
        $skill = Skill::create([
            'organization_id' => $this->organization->id,
            'name' => 'Skill To Update',
            'category' => 'technical',
        ]);

        $payload = [
            'data' => [
                'id' => $skill->id,
                'name' => 'Skill Updated Name',
                'category' => 'business',
                'complexity_level' => 'strategic',
                'description' => 'Updated description',
                'is_critical' => true,
                'scope_type' => 'transversal',
                'domain_tag' => 'infra',
            ],
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/skills/{$skill->id}", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'Skill Updated Name',
            'category' => 'business',
            'complexity_level' => 'strategic',
            'is_critical' => 1,
            'scope_type' => 'transversal',
            'domain_tag' => 'infra',
        ]);
    }

    public function test_competency_sync_updates_pivot_weight()
    {
        $capability = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap Sync']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $capability->id, 'name' => 'Comp Sync']);

        $skill = Skill::create(['organization_id' => $this->organization->id, 'name' => 'S for Pivot', 'category' => 'technical']);

        // attach initial pivot
        DB::table('competency_skills')->insert([
            'competency_id' => $comp->id,
            'skill_id' => $skill->id,
            'weight' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // now sync with new weight via competencies PATCH (skills => [id => ['weight' => X]])
        $syncPayload = [
            'skills' => [
                $skill->id => ['weight' => 42],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->patchJson("/api/competencies/{$comp->id}", $syncPayload);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('competency_skills', [
            'competency_id' => $comp->id,
            'skill_id' => $skill->id,
            'weight' => 42,
        ]);
    }

    public function test_delete_skill_removes_skill_and_detaches_pivot()
    {
        $capability = Capability::create(['organization_id' => $this->organization->id, 'name' => 'Cap Del']);
        $comp = Competency::create(['organization_id' => $this->organization->id, 'capability_id' => $capability->id, 'name' => 'Comp Del']);

        $skill = Skill::create(['organization_id' => $this->organization->id, 'name' => 'Skill To Delete', 'category' => 'technical']);

        DB::table('competency_skills')->insert([
            'competency_id' => $comp->id,
            'skill_id' => $skill->id,
            'weight' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // detach pivot first
        DB::table('competency_skills')->where('competency_id', $comp->id)->where('skill_id', $skill->id)->delete();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/skills/{$skill->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
        $this->assertDatabaseMissing('competency_skills', ['skill_id' => $skill->id]);
    }
}
