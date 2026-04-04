<?php

use App\Models\Departments;
use App\Models\Organization;
use App\Models\People;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

const ORG_CHART_BASE = '/api/org-chart/people';

beforeEach(function () {
    $this->org  = Organization::factory()->create();
    $this->user = createUserForOrganizationWithRole($this->org, 'qa_org_chart');
    Sanctum::actingAs($this->user, ['*']);

    // Create a 3-level hierarchy: CEO → Manager → Employee
    $this->ceo = People::factory()->create([
        'organization_id' => $this->org->id,
        'first_name' => 'Ana',
        'last_name' => 'CEO',
        'supervised_by' => null,
    ]);
    $this->manager = People::factory()->create([
        'organization_id' => $this->org->id,
        'first_name' => 'Luis',
        'last_name' => 'Manager',
        'supervised_by' => $this->ceo->id,
    ]);
    $this->employee = People::factory()->create([
        'organization_id' => $this->org->id,
        'first_name' => 'Maria',
        'last_name' => 'Employee',
        'supervised_by' => $this->manager->id,
    ]);

    // Create dept hierarchy
    $this->parentDept = Departments::create([
        'organization_id' => $this->org->id,
        'name' => 'Engineering',
        'parent_id' => null,
    ]);
    $this->childDept = Departments::create([
        'organization_id' => $this->org->id,
        'name' => 'Backend',
        'parent_id' => $this->parentDept->id,
    ]);
});

// ─── Tree ─────────────────────────────────────────────────────────────────────

it('returns people tree with root nodes', function () {
    $res = $this->getJson(ORG_CHART_BASE);

    $res->assertOk()
        ->assertJsonStructure(['view', 'nodes', 'meta']);

    expect($res->json('view'))->toBe('people');
    $nodeNames = collect($res->json('nodes'))->pluck('name')->all();
    expect($nodeNames)->toContain('Ana CEO');
});

it('tree root node has children array', function () {
    $res = $this->getJson(ORG_CHART_BASE);
    $root = collect($res->json('nodes'))->firstWhere('name', 'Ana CEO');

    expect($root)->not->toBeNull();
    expect($root['children'])->toBeArray();
    expect($root['direct_reports_count'])->toBe(1);
});

it('tree is nested 3 levels deep', function () {
    $res = $this->getJson(ORG_CHART_BASE);
    $root = collect($res->json('nodes'))->firstWhere('name', 'Ana CEO');
    $manager = collect($root['children'])->firstWhere('name', 'Luis Manager');
    expect($manager)->not->toBeNull();

    $employee = collect($manager['children'])->firstWhere('name', 'Maria Employee');
    expect($employee)->not->toBeNull();
    expect($employee['depth'])->toBe(2);
});

it('tree does not include people from other orgs', function () {
    $otherOrg = Organization::factory()->create();
    People::factory()->create([
        'organization_id' => $otherOrg->id,
        'first_name' => 'Hacker',
        'last_name' => 'Other',
        'supervised_by' => null,
    ]);

    $res = $this->getJson(ORG_CHART_BASE);
    $allNames = collect($res->json('nodes'))->pluck('name')->all();
    expect($allNames)->not->toContain('Hacker Other');
});

it('departments view returns department tree', function () {
    $res = $this->getJson(ORG_CHART_BASE . '?view=departments');
    $res->assertOk();

    expect($res->json('view'))->toBe('departments');
    $rootNames = collect($res->json('nodes'))->pluck('name')->all();
    expect($rootNames)->toContain('Engineering');
});

it('departments tree nests child departments', function () {
    $res = $this->getJson(ORG_CHART_BASE . '?view=departments');
    $eng = collect($res->json('nodes'))->firstWhere('name', 'Engineering');
    expect($eng)->not->toBeNull();
    $childNames = collect($eng['children'])->pluck('name')->all();
    expect($childNames)->toContain('Backend');
});

// ─── Subtree ─────────────────────────────────────────────────────────────────

it('subtree returns subtree rooted at person', function () {
    $res = $this->getJson(ORG_CHART_BASE . "/{$this->manager->id}/subtree");
    $res->assertOk()
        ->assertJsonStructure(['root']);

    expect($res->json('root.name'))->toBe('Luis Manager');
    $childNames = collect($res->json('root.children'))->pluck('name')->all();
    expect($childNames)->toContain('Maria Employee');
});

it('subtree 404s for person in another org', function () {
    $otherOrg = Organization::factory()->create();
    $other = People::factory()->create(['organization_id' => $otherOrg->id]);

    $this->getJson(ORG_CHART_BASE . "/{$other->id}/subtree")->assertNotFound();
});

// ─── Stats ────────────────────────────────────────────────────────────────────

it('stats returns total employees and manager counts', function () {
    $res = $this->getJson(ORG_CHART_BASE . '/stats');
    $res->assertOk()
        ->assertJsonStructure(['total_employees', 'total_managers', 'avg_span_of_control', 'max_depth']);

    expect($res->json('total_employees'))->toBe(3);
    // CEO and Manager are supervisors
    expect($res->json('total_managers'))->toBe(2);
});

it('stats max_depth is correct', function () {
    $res = $this->getJson(ORG_CHART_BASE . '/stats');
    expect($res->json('max_depth'))->toBe(2); // CEO(0) → Manager(1) → Employee(2)
});
