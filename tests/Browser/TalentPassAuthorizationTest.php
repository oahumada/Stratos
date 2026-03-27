<?php

use App\Models\Organization;
use App\Models\User;
use Pest\Laravel\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        'organization_id' => $this->organization->id,
    ]);

    $this->otherUser = User::factory()->create([
        'organization_id' => Organization::factory()->create()->id,
    ]);
});

test('unauthenticated user is redirected to login', function () {
    visit('/talent-pass')
        ->assertRedirect('/login');
});

test('user can access only their own talent passes', function () {
    $myTalentPass = $this->user->talentPasses()->create([
        'title' => 'My Profile',
    ]);

    $otherTalentPass = $this->otherUser->talentPasses()->create([
        'title' => 'Other Profile',
    ]);

    $page = visit('/talent-pass')
        ->actingAs($this->user)
        ->assertSee('My Profile')
        ->assertDontSee('Other Profile');
});

test('user cannot edit other users talent pass', function () {
    $otherTalentPass = $this->otherUser->talentPasses()->create([
        'title' => 'Other Profile',
    ]);

    visit('/talent-pass/' . $otherTalentPass->id . '/edit')
        ->actingAs($this->user)
        ->assertForbidden();
});

test('public talent pass is accessible without authentication', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Public Profile',
        'visibility' => 'public',
    ]);

    visit('/public/talent-pass/' . $talentPass->ulid)
        ->assertOk()
        ->assertSee('Public Profile');
});

test('private talent pass is not accessible without authentication', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Private Profile',
        'visibility' => 'private',
    ]);

    visit('/public/talent-pass/' . $talentPass->ulid)
        ->assertNotFound();
});

test('talent pass status transitions work correctly', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Status Test',
        'status' => 'draft',
    ]);

    $page = visit('/talent-pass/' . $talentPass->id)
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertSee('Draft');

    // Click publish button
    $page->click('button:contains("Publish")')
        ->assertSee('Published');

    assertDatabaseHas('talent_passes', [
        'id' => $talentPass->id,
        'status' => 'published',
    ]);
});

test('skills are validated on create', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Validation Test',
    ]);

    $page = visit('/talent-pass/' . $talentPass->id)
        ->actingAs($this->user)
        ->click('button:contains("Add")')
        ->fill('input[placeholder*="skill"]', '')
        ->click('button:contains("Add Skill")')
        ->assertSee('This field is required'); // or appropriate validation message
});

test('multi-tenant isolation is enforced', function () {
    $otherOrg = Organization::factory()->create();
    $otherOrgUser = User::factory()->create([
        'organization_id' => $otherOrg->id,
    ]);

    $myTalentPass = $this->user->talentPasses()->create([
        'title' => 'My Org Profile',
    ]);

    // Other org user should not see this profile
    visit('/talent-pass')
        ->actingAs($otherOrgUser)
        ->assertDontSee('My Org Profile');
});

test('performance: list page loads with many talent passes', function () {
    // Create 50 talent passes
    for ($i = 0; $i < 50; $i++) {
        $this->user->talentPasses()->create([
            'title' => 'Profile ' . $i,
        ]);
    }

    $start = microtime(true);

    $page = visit('/talent-pass')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors();

    $duration = microtime(true) - $start;

    // Should load in less than 2 seconds
    expect($duration)->toBeLessThan(2);
});
