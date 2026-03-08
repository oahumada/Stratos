<?php

namespace Tests\Feature;

use App\Models\AssessmentSession;
use App\Models\Competency;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected $org1;
    protected $org2;
    protected $admin1;
    protected $admin2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->org1 = Organization::factory()->create(['name' => 'Org 1']);
        $this->org2 = Organization::factory()->create(['name' => 'Org 2']);
        
        $this->admin1 = User::factory()->create(['organization_id' => $this->org1->id, 'role' => 'admin']);
        $this->admin2 = User::factory()->create(['organization_id' => $this->org2->id, 'role' => 'admin']);
    }

    public function test_cannot_access_assessment_sessions_from_another_organization()
    {
        $person1 = People::factory()->create(['organization_id' => $this->org1->id]);
        $session1 = AssessmentSession::create([
            'organization_id' => $this->org1->id,
            'people_id' => $person1->id,
            'type' => 'psychometric',
            'status' => 'started'
        ]);

        // Admin 2 tries to access Session 1
        $response = $this->actingAs($this->admin2, 'sanctum')
            ->getJson("/api/strategic-planning/assessments/sessions/{$session1->id}");

        // Should return 404 or 403 depending on implementation (usually 404 for tenant isolation)
        $response->assertStatus(404);
    }

    public function test_cannot_access_competencies_from_another_organization()
    {
        $comp1 = Competency::create([
            'organization_id' => $this->org1->id,
            'name' => 'Org 1 Competency',
            'status' => 'active'
        ]);

        // Admin 2 tries to access Comp 1
        $response = $this->actingAs($this->admin2, 'sanctum')
            ->getJson("/api/strategic-planning/competencies/{$comp1->id}");

        $response->assertStatus(404);
    }
}
