<?php

namespace Tests\Feature\Services;

use App\Models\AssessmentCycle;
use App\Models\AssessmentRequest;
use App\Models\Organization;
use App\Models\People;
use App\Models\User;
use App\Services\Assessment\AssessmentCycleNotificationService;
use App\Services\Assessment\AssessmentCycleSchedulerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class AssessmentCycleSchedulerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $org;

    protected $admin;

    protected $service;

    protected $mockNotificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->org = Organization::factory()->create();
        $this->admin = User::factory()->create(['organization_id' => $this->org->id]);
        $this->service = app(AssessmentCycleSchedulerService::class);

        // Mock notification service
        $this->mockNotificationService = Mockery::mock(AssessmentCycleNotificationService::class);
        $this->app->instance(AssessmentCycleNotificationService::class, $this->mockNotificationService);
    }

    public function test_activates_a_scheduled_cycle_when_its_start_date_is_reached()
    {
        // 1. Arrange
        $cycle = AssessmentCycle::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'scheduled',
            'starts_at' => now()->subMinute(),
            'evaluators' => [
                'self' => true,
                'manager' => false,
                'peers' => 0,
                'reports' => false,
            ],
        ]);

        // Create some people
        People::factory()->count(3)->create(['organization_id' => $this->org->id]);

        // Expect notifications to be sent
        $this->mockNotificationService->shouldReceive('notifyEvaluator')->times(3);

        // 2. Act
        $this->service->processCycles();

        // 3. Assert
        $cycle->refresh();
        $this->assertEquals('active', $cycle->status);
        $this->assertEquals(3, AssessmentRequest::count());
    }

    public function test_correctly_generates_requests_based_on_evaluator_configuration()
    {
        // 1. Arrange
        $cycle = AssessmentCycle::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'scheduled',
            'starts_at' => now()->subMinute(),
            'evaluators' => [
                'self' => true,
                'manager' => true,
                'peers' => 1,
                'reports' => true,
            ],
        ]);

        $subject = People::factory()->create(['organization_id' => $this->org->id, 'first_name' => 'Subject']);
        $manager = People::factory()->create(['organization_id' => $this->org->id, 'first_name' => 'Manager']);
        $peer = People::factory()->create(['organization_id' => $this->org->id, 'first_name' => 'Peer']);
        $report = People::factory()->create(['organization_id' => $this->org->id, 'first_name' => 'Report']);

        // Setup relationships
        DB::table('people_relationships')->insert([
            ['person_id' => $subject->id, 'related_person_id' => $manager->id, 'relationship_type' => 'manager', 'created_at' => now(), 'updated_at' => now()],
            ['person_id' => $subject->id, 'related_person_id' => $peer->id, 'relationship_type' => 'peer', 'created_at' => now(), 'updated_at' => now()],
            ['person_id' => $subject->id, 'related_person_id' => $report->id, 'relationship_type' => 'subordinate', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Expect 7 notifications: 4 for subject (self, manager, peer, report) + 3 for others (self only)
        $this->mockNotificationService->shouldReceive('notifyEvaluator')->times(7);

        // 2. Act
        $this->service->activateCycle($cycle);

        // 3. Assert
        $this->assertEquals(4, AssessmentRequest::where('subject_id', $subject->id)->count());
        $this->assertDatabaseHas('assessment_requests', [
            'subject_id' => $subject->id,
            'evaluator_id' => $subject->id,
            'relationship' => 'self',
        ]);
        $this->assertDatabaseHas('assessment_requests', [
            'subject_id' => $subject->id,
            'evaluator_id' => $manager->id,
            'relationship' => 'supervisor',
        ]);
        $this->assertDatabaseHas('assessment_requests', [
            'subject_id' => $subject->id,
            'evaluator_id' => $peer->id,
            'relationship' => 'peer',
        ]);
        $this->assertDatabaseHas('assessment_requests', [
            'subject_id' => $subject->id,
            'evaluator_id' => $report->id,
            'relationship' => 'subordinate',
        ]);
    }
}
