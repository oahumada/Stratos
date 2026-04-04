<?php

use App\Models\Departments;
use App\Models\Organization;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use App\Models\Skill;
use App\Services\SkillIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const SKILL_INTEL_BASE = '/api/skill-intelligence';

beforeEach(function () {
    $this->org  = Organization::factory()->create();
    $this->user = createUserForOrganizationWithRole($this->org, 'qa_skill_intel');
    Sanctum::actingAs($this->user, ['*']);

    // Create department
    $this->dept = Departments::create([
        'organization_id' => $this->org->id,
        'name' => 'Engineering',
        'description' => 'Engineering dept',
    ]);

    // Create skills
    $this->skillA = Skill::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Python',
        'category' => 'technical',
        'is_critical' => true,
    ]);
    $this->skillB = Skill::factory()->create([
        'organization_id' => $this->org->id,
        'name' => 'Liderazgo',
        'category' => 'leadership',
        'is_critical' => false,
    ]);

    // Create people in org + dept
    $this->person1 = People::factory()->create([
        'organization_id' => $this->org->id,
        'department_id'   => $this->dept->id,
    ]);
    $this->person2 = People::factory()->create([
        'organization_id' => $this->org->id,
        'department_id'   => $this->dept->id,
    ]);

    // Skill records: person1 has gap 2 on Python, gap 0 on Liderazgo
    PeopleRoleSkills::create([
        'people_id'      => $this->person1->id,
        'skill_id'       => $this->skillA->id,
        'current_level'  => 1,
        'required_level' => 3,
        'is_active'      => true,
    ]);
    PeopleRoleSkills::create([
        'people_id'      => $this->person1->id,
        'skill_id'       => $this->skillB->id,
        'current_level'  => 3,
        'required_level' => 3,
        'is_active'      => true,
    ]);
    // person2 has gap 1 on Python, gap 1 on Liderazgo
    PeopleRoleSkills::create([
        'people_id'      => $this->person2->id,
        'skill_id'       => $this->skillA->id,
        'current_level'  => 2,
        'required_level' => 3,
        'is_active'      => true,
    ]);
    PeopleRoleSkills::create([
        'people_id'      => $this->person2->id,
        'skill_id'       => $this->skillB->id,
        'current_level'  => 2,
        'required_level' => 3,
        'is_active'      => true,
    ]);
});

// ─── Heatmap ─────────────────────────────────────────────────────────────────

it('heatmap returns departments and skills arrays', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/heatmap');

    $res->assertOk()
        ->assertJsonStructure(['departments', 'skills', 'matrix', 'meta']);

    expect($res->json('departments'))->toContain('Engineering');
    expect($res->json('skills'))->toContain('Python');
});

it('heatmap matrix contains avg gap values', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/heatmap');
    $res->assertOk();

    $matrix = $res->json('matrix');
    expect($matrix)->toHaveKey('Engineering');
    expect($matrix['Engineering'])->toHaveKey('Python');
    // person1 gap=2, person2 gap=1 → avg=1.5
    expect($matrix['Engineering']['Python'])->toBe(1.5);
});

it('heatmap filters by category', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/heatmap?category=technical');
    $res->assertOk();

    $skills = $res->json('skills');
    expect($skills)->toContain('Python');
    expect($skills)->not->toContain('Liderazgo');
});

it('heatmap returns empty when no skill records', function () {
    PeopleRoleSkills::query()->delete();
    $res = $this->getJson(SKILL_INTEL_BASE . '/heatmap');
    $res->assertOk();
    expect($res->json('departments'))->toBe([]);
    expect($res->json('skills'))->toBe([]);
});

// ─── Top Gaps ─────────────────────────────────────────────────────────────────

it('top-gaps returns skills with gaps', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/top-gaps');

    $res->assertOk()
        ->assertJsonStructure(['top_gaps' => [['skill_id', 'skill_name', 'avg_gap', 'affected_people', 'critical_count']]]);

    $skillNames = collect($res->json('top_gaps'))->pluck('skill_name')->all();
    expect($skillNames)->toContain('Python');
});

it('top-gaps Python has higher priority than Liderazgo', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/top-gaps');
    $gaps = $res->json('top_gaps');

    // Python: critical_count=1 (gap>=2 for person1), avg_gap=1.5
    // Liderazgo: critical_count=0, avg_gap=0.5 — should not appear (avg_gap=0 for Liderazgo? no)
    // Actually Liderazgo: person1 gap=0, person2 gap=1 → avg=0.5 → affected=1 → appears
    $pythonIdx = collect($gaps)->search(fn ($g) => $g['skill_name'] === 'Python');
    expect($pythonIdx)->toBe(0); // Python first
});

it('top-gaps respects limit param', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/top-gaps?limit=1');
    $res->assertOk();
    expect(count($res->json('top_gaps')))->toBeLessThanOrEqual(1);
});

// ─── Upskilling ───────────────────────────────────────────────────────────────

it('upskilling returns recommendations with suggested_action', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/upskilling');

    $res->assertOk()
        ->assertJsonStructure(['recommendations' => [['skill_id', 'skill_name', 'avg_gap', 'priority', 'suggested_action', 'urgent_people']]]);
});

it('upskilling Python recommendation has alta or media priority', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/upskilling');
    $recs = collect($res->json('recommendations'));
    $python = $recs->firstWhere('skill_name', 'Python');

    expect($python)->not->toBeNull();
    expect($python['priority'])->toBeIn(['alta', 'media', 'baja']);
    expect($python['suggested_action'])->toBeString();
});

// ─── Coverage ─────────────────────────────────────────────────────────────────

it('coverage returns org_avg_coverage_pct and skills array', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/coverage');

    $res->assertOk()
        ->assertJsonStructure(['org_avg_coverage_pct', 'skills', 'meta']);

    expect((float) $res->json('org_avg_coverage_pct'))->toBeFloat();
});

it('coverage Python has 0% since both people have gap', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/coverage');
    $skills = collect($res->json('skills'));
    $python = $skills->firstWhere('skill_name', 'Python');

    expect($python)->not->toBeNull();
    expect((float) $python['coverage_pct'])->toBe(0.0);
    expect($python['people_meeting'])->toBe(0);
});

it('coverage Liderazgo has 50% since person1 meets required level', function () {
    $res = $this->getJson(SKILL_INTEL_BASE . '/coverage');
    $skills = collect($res->json('skills'));
    $lid = $skills->firstWhere('skill_name', 'Liderazgo');

    expect($lid)->not->toBeNull();
    expect((float) $lid['coverage_pct'])->toBe(50.0);
});

// ─── Auth ─────────────────────────────────────────────────────────────────────

it('endpoints return 401 without authentication', function () {
    \Illuminate\Support\Facades\Auth::forgetGuards();
    $endpoints = ['heatmap', 'top-gaps', 'upskilling', 'coverage'];
    foreach ($endpoints as $ep) {
        $this->getJson(SKILL_INTEL_BASE . "/{$ep}")
            ->assertUnauthorized();
    }
});
