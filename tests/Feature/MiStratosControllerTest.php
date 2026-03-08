<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiStratosControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $org;
    protected $user;
    protected $person;

    protected function setUp(): void
    {
        parent::setUp();
        $this->org = Organization::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->org->id]);
        
        $role = Roles::factory()->create(['organization_id' => $this->org->id]);
        $this->person = People::factory()->create([
            'organization_id' => $this->org->id,
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_dashboard_returns_consolidated_talent_data()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/mi-stratos/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'person',
                'kpis',
                'gap_analysis',
                'competencies',
                'learning_paths',
                'conversations',
                'quests'
            ]
        ]);

        $this->assertEquals($this->person->id, $response->json('data.person.id'));
    }
}
