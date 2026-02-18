<?php

use App\Models\AssessmentSession;
use App\Models\Organizations;
use App\Models\People;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->person = People::factory()->create(['organization_id' => $this->org->id]);
});

it('can start an assessment session', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/strategic-planning/assessments/sessions', [
            'people_id' => $this->person->id,
            'type' => 'psychometric'
        ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('assessment_sessions', [
        'people_id' => $this->person->id,
        'status' => 'started'
    ]);
});

it('can send a message and get ai response', function () {
    $session = AssessmentSession::create([
        'organization_id' => $this->user->organization_id,
        'people_id' => $this->person->id,
        'type' => 'psychometric',
        'status' => 'started'
    ]);

    $mockResponse = [
        'role' => 'assistant',
        'content' => 'Hello, I am your psychometric interviewer.'
    ];

    Http::fake([
        '*/interview/chat' => Http::response($mockResponse, 200)
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/assessments/sessions/{$session->id}/messages", [
            'content' => 'Hello AI'
        ]);

    $response->assertStatus(200);
    $response->assertJson($mockResponse);

    $this->assertDatabaseHas('assessment_messages', [
        'assessment_session_id' => $session->id,
        'role' => 'user',
        'content' => 'Hello AI'
    ]);
});

it('can analyze a session', function () {
    $session = AssessmentSession::create([
        'organization_id' => $this->user->organization_id,
        'people_id' => $this->person->id,
        'type' => 'psychometric',
        'status' => 'in_progress'
    ]);

    // Add some messages
    $session->messages()->create(['role' => 'user', 'content' => 'Message 1']);
    $session->messages()->create(['role' => 'assistant', 'content' => 'Response 1']);
    $session->messages()->create(['role' => 'user', 'content' => 'Message 2']);

    $mockAnalysis = [
        'traits' => [
            ['name' => 'Resilience', 'score' => 0.8, 'rationale' => 'Good'],
            ['name' => 'Adaptability', 'score' => 0.9, 'rationale' => 'Vey good']
        ],
        'overall_potential' => 0.85,
        'summary_report' => 'Candidate report'
    ];

    Http::fake([
        '*/interview/analyze' => Http::response($mockAnalysis, 200)
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/assessments/sessions/{$session->id}/analyze");

    $response->assertStatus(200);
    $this->assertDatabaseHas('assessment_sessions', [
        'id' => $session->id,
        'status' => 'analyzed'
    ]);

    $this->assertDatabaseHas('psychometric_profiles', [
        'people_id' => $this->person->id,
        'trait_name' => 'Resilience',
        'score' => 0.8
    ]);
});

it('can analyze a session with external feedback (360)', function () {
    $session = AssessmentSession::create([
        'organization_id' => $this->user->organization_id,
        'people_id' => $this->person->id,
        'type' => 'psychometric',
        'status' => 'in_progress'
    ]);

    // Add some messages
    $session->messages()->create(['role' => 'user', 'content' => 'Message 1']);
    $session->messages()->create(['role' => 'assistant', 'content' => 'Response 1']);
    $session->messages()->create(['role' => 'user', 'content' => 'Message 2']);

    // Create external feedback
    $evaluator = People::factory()->create(['organization_id' => $this->org->id]);
    $request = \App\Models\AssessmentRequest::create([
        'organization_id' => $this->org->id,
        'subject_id' => $this->person->id,
        'evaluator_id' => $evaluator->id,
        'relationship' => 'peer',
        'status' => 'completed'
    ]);
    $request->feedback()->create([
        'question' => 'How is it?',
        'answer' => 'Very professional'
    ]);

    $mockAnalysis = [
        'traits' => [
            ['name' => 'Leadership', 'score' => 0.9, 'rationale' => 'Team values him']
        ],
        'overall_potential' => 0.9,
        'summary_report' => '360 report',
        'blind_spots' => ['Team sees leadership subject does not']
    ];

    Http::fake([
        '*/interview/analyze-360' => Http::response($mockAnalysis, 200)
    ]);

    $response = $this->actingAs($this->user)
        ->postJson("/api/strategic-planning/assessments/sessions/{$session->id}/analyze");

    $response->assertStatus(200);
    $response->assertJsonPath('session.metadata.blind_spots.0', 'Team sees leadership subject does not');
});
