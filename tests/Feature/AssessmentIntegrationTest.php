<?php

namespace Tests\Feature;

use App\Models\AssessmentSession;
use App\Models\Organization;
use App\Models\People;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AssessmentIntegrationTest extends TestCase
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
            'user_id' => $this->user->id,
        ]);

        $this->user->update(['person_id' => $this->person->id]);
    }

    public function test_analyzing_session_triggers_360_feedback_requests()
    {
        // 1. Arrange: Create a person with a manager
        $manager = People::factory()->create(['organization_id' => $this->org->id]);

        // Use a direct DB insert for relationship if relationship methods are read-only
        \DB::table('people_relationships')->insert([
            'person_id' => $this->person->id,
            'related_person_id' => $manager->id,
            'relationship_type' => 'manager',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $session = AssessmentSession::create([
            'organization_id' => $this->org->id,
            'people_id' => $this->person->id,
            'type' => 'psychometric',
            'status' => 'in_progress',
        ]);

        // Add dummy messages
        $session->messages()->create(['role' => 'user', 'content' => 'Hello']);
        $session->messages()->create(['role' => 'assistant', 'content' => 'Hi']);
        $session->messages()->create(['role' => 'user', 'content' => 'I am ready']);

        // Mock AI Analysis
        Http::fake([
            '*/interview/analyze' => Http::response([
                'traits' => [['name' => 'Resilience', 'score' => 0.8]],
                'overall_potential' => 0.8,
                'summary_report' => 'Good',
            ], 200),
        ]);

        // 2. Act
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/strategic-planning/assessments/sessions/{$session->id}/analyze");

        $response->assertStatus(200);

        // 3. Assert: Check if AssessmentRequests were created for the manager
        $this->assertDatabaseHas('assessment_requests', [
            'subject_id' => $this->person->id,
            'evaluator_id' => $manager->id,
            'relationship' => 'manager',
        ]);
    }
}
