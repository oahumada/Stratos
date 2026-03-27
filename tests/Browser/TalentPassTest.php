<?php

use App\Models\Organization;
use App\Models\User;
use Pest\Laravel\RefreshDatabase;
use Tests\Browser\Pages\TalentPass as TalentPassPage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create([
        'organization_id' => $this->organization->id,
    ]);
});

test('user can create a talent pass', function () {
    $page = visit('/talent-pass/create')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertSee('Create Talent Pass');

    $page->fill('input[name="title"]', 'Senior Software Engineer')
        ->fill('textarea[name="summary"]', 'Experienced full-stack developer with expertise in Laravel, Vue, and cloud infrastructure.')
        ->select('input[name="visibility"]', 'public')
        ->click('button:contains("Create Talent Pass")')
        ->assertNoJavascriptErrors();

    // Verify redirected to show page
    assertDatabaseHas('talent_passes', [
        'title' => 'Senior Software Engineer',
        'user_id' => $this->user->id,
    ]);
});

test('user can view talent pass list with filters', function () {
    // Create some test talent passes
    $talentPass1 = $this->user->talentPasses()->create([
        'title' => 'Product Manager',
        'summary' => 'PM with 5 years experience',
        'visibility' => 'public',
        'status' => 'published',
    ]);

    $talentPass2 = $this->user->talentPasses()->create([
        'title' => 'Data Scientist',
        'summary' => 'Data scientist specializing in ML',
        'visibility' => 'private',
        'status' => 'draft',
    ]);

    $page = visit('/talent-pass')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertSee('Senior Software Engineer');

    // Test search functionality
    $page->fill('input[name="search"]', 'Product Manager')
        ->assertSee($talentPass1->title)
        ->assertDontSee($talentPass2->title);

    // Test status filter
    $page->click('button:contains("Draft")')
        ->assertSee($talentPass2->title)
        ->assertDontSee($talentPass1->title);
});

test('user can edit talent pass', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Original Title',
        'summary' => 'Original summary',
        'visibility' => 'private',
    ]);

    $page = visit('/talent-pass/' . $talentPass->id . '/edit')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertSee('Original Title');

    $page->clear('input[name="title"]')
        ->fill('input[name="title"]', 'Updated Title')
        ->fill('textarea[name="summary"]', 'Updated summary with new information')
        ->click('button:contains("Update")')
        ->assertNoJavascriptErrors();

    assertDatabaseHas('talent_passes', [
        'id' => $talentPass->id,
        'title' => 'Updated Title',
    ]);
});

test('user can view talent pass details', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Full Stack Developer',
        'summary' => 'Full stack with cloud expertise',
    ]);

    // Add some skills
    $talentPass->skills()->create([
        'name' => 'Laravel',
        'level' => 5,
    ]);

    $talentPass->skills()->create([
        'name' => 'Vue.js',
        'level' => 4,
    ]);

    $page = visit('/talent-pass/' . $talentPass->id)
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertSee('Full Stack Developer')
        ->assertSee('Laravel')
        ->assertSee('Vue.js');

    // Verify completeness indicator is visible
    $page->assertSee('Profile Completeness');

    // Verify action buttons
    $page->assertSee('Edit')
        ->assertSee('Export')
        ->assertSee('Share');
});

test('user can add skills to talent pass', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Software Engineer',
    ]);

    $page = visit('/talent-pass/' . $talentPass->id)
        ->actingAs($this->user)
        ->assertNoJavascriptErrors();

    // Click the add skills button
    $page->click('button:contains("Add")')
        ->fill('input[placeholder*="skill"]', 'Kubernetes')
        ->select('select[name="level"]', '5')
        ->click('button:contains("Add Skill")')
        ->assertNoJavascriptErrors()
        ->assertSee('Kubernetes');

    assertDatabaseHas('talent_pass_skills', [
        'talent_pass_id' => $talentPass->id,
        'name' => 'Kubernetes',
        'level' => 5,
    ]);
});

test('public talent pass view works without authentication', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Public Profile',
        'summary' => 'This profile is publicly shared',
        'visibility' => 'public',
    ]);

    $page = visit('/public/talent-pass/' . $talentPass->ulid)
        ->assertNoJavascriptErrors()
        ->assertSee('Public Profile')
        ->assertSee('This profile is publicly shared');

    // Verify no edit button
    $page->assertDontSee('Edit')
        ->assertDontSee('Delete');

    // Verify share button
    $page->assertSee('Share');
});

test('public talent pass copy link works', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Shareable Profile',
        'visibility' => 'link',
    ]);

    $page = visit('/public/talent-pass/' . $talentPass->ulid)
        ->assertNoJavascriptErrors();

    // Click copy link button - this will simulate clipboard action
    $page->click('button:contains("Copy")')
        ->assertSee('Copied!');
});

test('responsive layout works on mobile viewport', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Mobile Test',
    ]);

    $page = visit('/talent-pass')
        ->actingAs($this->user)
        ->windowSize(375, 667) // iPhone viewport
        ->assertNoJavascriptErrors()
        ->assertSee('Mobile Test');

    // Verify mobile layout (single column)
    $page->assertCookieValue('X-Viewport-Width', '<= 768');
});

test('dark mode is default and consistent', function () {
    $page = visit('/talent-pass')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors();

    // Verify dark mode classes are applied
    // Check for dark theme elements
    $page->assertScript('
        const darkElements = document.querySelectorAll("[class*=\"dark\"]");
        return darkElements.length > 0;
    ', true);
});

test('no console errors on talent pass pages', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Console Test',
    ]);

    // Test index page
    visit('/talent-pass')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();

    // Test create page
    visit('/talent-pass/create')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();

    // Test show page
    visit('/talent-pass/' . $talentPass->id)
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

test('delete talent pass workflow', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'To Delete',
    ]);

    $page = visit('/talent-pass/' . $talentPass->id)
        ->actingAs($this->user)
        ->assertNoJavascriptErrors();

    // Click delete button
    $page->click('button:contains("Delete")')
        ->waitFor('button:contains("Confirm Delete")', 2)
        ->click('button:contains("Confirm Delete")')
        ->assertNoJavascriptErrors();

    assertDatabaseMissing('talent_passes', [
        'id' => $talentPass->id,
    ]);
});

test('export menu appears on detail page', function () {
    $talentPass = $this->user->talentPasses()->create([
        'title' => 'Export Test',
    ]);

    $page = visit('/talent-pass/' . $talentPass->id)
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertSee('Export');

    // Click export menu
    $page->click('button:contains("Export")')
        ->assertSee('Export as PDF')
        ->assertSee('Export as JSON');
});
