<?php

use App\Models\LmsCommunity;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\Organizations;
use App\Models\People;
use App\Models\Roles;
use App\Models\Scenario;
use App\Models\Skill;
use App\Models\User;

beforeEach(function () {
    $this->org = Organizations::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->org->id]);
    $this->actingAs($this->user);
});

describe('Stratos Logos — Executive Summary', function () {
    test('returns cross-module executive summary', function () {
        People::factory()->count(5)->create(['organization_id' => $this->org->id, 'status' => 'active']);
        Roles::factory()->count(2)->create(['organization_id' => $this->org->id]);
        Skill::factory()->count(3)->create(['organization_id' => $this->org->id]);

        $response = $this->getJson('/api/logos/executive-summary');

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'data' => [
                    'core' => ['total_people', 'total_roles', 'total_skills', 'active_people'],
                    'praxis' => ['total_courses', 'completion_rate'],
                    'agora' => ['total_communities', 'active_communities'],
                    'horizon' => ['total_scenarios'],
                    'stratos_iq' => ['current_iq', 'average_proficiency_gap'],
                ],
            ]);

        expect($response->json('data.core.total_people'))->toBe(5);
        expect($response->json('data.core.total_roles'))->toBe(2);
        expect($response->json('data.core.total_skills'))->toBe(3);
    });

    test('isolates data by organization (multi-tenant)', function () {
        People::factory()->count(3)->create(['organization_id' => $this->org->id]);

        $otherOrg = Organizations::factory()->create();
        People::factory()->count(10)->create(['organization_id' => $otherOrg->id]);

        $response = $this->getJson('/api/logos/executive-summary');

        expect($response->json('data.core.total_people'))->toBe(3);
    });
});

describe('Stratos Logos — Module Metrics', function () {
    test('returns core module metrics', function () {
        People::factory()->count(4)->create(['organization_id' => $this->org->id]);

        $response = $this->getJson('/api/logos/module/core');

        $response->assertOk();
        expect($response->json('data.total_people'))->toBe(4);
        expect($response->json('module'))->toBe('core');
    });

    test('returns praxis module metrics with completion rate', function () {
        $course = LmsCourse::create([
            'organization_id' => $this->org->id,
            'title' => 'Test Course',
            'is_active' => true,
            'created_by' => $this->user->id,
        ]);
        foreach (range(1, 3) as $_) {
            LmsEnrollment::create([
                'lms_course_id' => $course->id,
                'user_id' => User::factory()->create(['organization_id' => $this->org->id])->id,
                'status' => 'completed',
                'enrolled_at' => now(),
            ]);
        }
        foreach (range(1, 2) as $_) {
            LmsEnrollment::create([
                'lms_course_id' => $course->id,
                'user_id' => User::factory()->create(['organization_id' => $this->org->id])->id,
                'status' => 'in_progress',
                'enrolled_at' => now(),
            ]);
        }

        $response = $this->getJson('/api/logos/module/praxis');

        $response->assertOk();
        expect($response->json('data.total_enrollments'))->toBe(5);
        expect($response->json('data.completed_enrollments'))->toBe(3);
        expect((float) $response->json('data.completion_rate'))->toBe(60.0);
    });

    test('returns agora module metrics', function () {
        LmsCommunity::factory()->count(2)->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
            'health_score' => 72.5,
        ]);

        $response = $this->getJson('/api/logos/module/agora');

        $response->assertOk();
        expect($response->json('data.active_communities'))->toBe(2);
        expect($response->json('data.avg_health_score'))->toBe(72.5);
    });

    test('returns horizon module metrics', function () {
        Scenario::factory()->count(3)->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/logos/module/horizon');

        $response->assertOk();
        expect($response->json('data.active_scenarios'))->toBe(3);
    });

    test('returns 404 for unknown module', function () {
        $response = $this->getJson('/api/logos/module/unknown');
        $response->assertNotFound();
    });
});

describe('Stratos Logos — Trends', function () {
    test('returns empty trends when no snapshots exist', function () {
        $response = $this->getJson('/api/logos/trends');

        $response->assertOk();
        expect($response->json('data'))->toBeArray()->toBeEmpty();
    });

    test('respects months parameter', function () {
        $response = $this->getJson('/api/logos/trends?months=3');
        $response->assertOk();
    });
});

describe('Stratos Logos — Authentication', function () {
    test('requires authentication', function () {
        auth()->logout();

        $this->getJson('/api/logos/executive-summary')->assertUnauthorized();
        $this->getJson('/api/logos/module/core')->assertUnauthorized();
        $this->getJson('/api/logos/trends')->assertUnauthorized();
    });
});
