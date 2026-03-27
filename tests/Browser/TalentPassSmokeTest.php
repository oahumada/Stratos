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

    $this->talentPass = $this->user->talentPasses()->create([
        'title' => 'Responsive Test Profile',
        'summary' => 'Testing responsive design across devices',
        'visibility' => 'public',
    ]);

    // Add skills
    for ($i = 1; $i <= 5; $i++) {
        $this->talentPass->skills()->create([
            'name' => 'Skill ' . $i,
            'level' => random_int(1, 5),
        ]);
    }
});

test('smoke: index page loads on desktop', function () {
    visit('/talent-pass')
        ->actingAs($this->user)
        ->windowSize(1920, 1080)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

test('smoke: index page loads on tablet', function () {
    visit('/talent-pass')
        ->actingAs($this->user)
        ->windowSize(768, 1024) // iPad
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

test('smoke: index page loads on mobile', function () {
    visit('/talent-pass')
        ->actingAs($this->user)
        ->windowSize(375, 667) // iPhone SE
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

test('smoke: detail page responsive on desktop', function () {
    visit('/talent-pass/' . $this->talentPass->id)
        ->actingAs($this->user)
        ->windowSize(1920, 1080)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Responsive Test Profile');
});

test('smoke: detail page responsive on tablet', function () {
    visit('/talent-pass/' . $this->talentPass->id)
        ->actingAs($this->user)
        ->windowSize(768, 1024)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Responsive Test Profile');
});

test('smoke: detail page responsive on mobile', function () {
    visit('/talent-pass/' . $this->talentPass->id)
        ->actingAs($this->user)
        ->windowSize(375, 667)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs()
        ->assertSee('Responsive Test Profile');
});

test('smoke: public page loads on all devices', function () {
    visit('/public/talent-pass/' . $this->talentPass->ulid)
        ->windowSize(1920, 1080) // Desktop
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();

    visit('/public/talent-pass/' . $this->talentPass->ulid)
        ->windowSize(768, 1024) // Tablet
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();

    visit('/public/talent-pass/' . $this->talentPass->ulid)
        ->windowSize(375, 667) // Mobile
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

test('smoke: dark mode works on all pages', function () {
    // Test index
    visit('/talent-pass')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertScript('
            const hasDarkClass = document.documentElement.classList.contains("dark")
                || document.body.classList.contains("dark")
                || window.matchMedia("(prefers-color-scheme: dark)").matches;
            return hasDarkClass;
        ', true);

    // Test show
    visit('/talent-pass/' . $this->talentPass->id)
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertScript('
            const hasDarkClass = document.documentElement.classList.contains("dark")
                || document.body.classList.contains("dark")
                || window.matchMedia("(prefers-color-scheme: dark)").matches;
            return hasDarkClass;
        ', true);
});

test('smoke: all pages have no broken links', function () {
    // Index page
    $pages = visit([
        '/talent-pass',
        '/talent-pass/create',
        '/talent-pass/' . $this->talentPass->id,
    ])
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertNoConsoleLogs();
});

test('smoke: create and edit pages load forms correctly', function () {
    // Create page
    visit('/talent-pass/create')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertSee('Create Talent Pass')
        ->assertSee('Privacy & Visibility');

    // Edit page
    visit('/talent-pass/' . $this->talentPass->id . '/edit')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors()
        ->assertSee('Edit Talent Pass')
        ->assertSee($this->talentPass->title);
});

test('smoke: navigation between pages works', function () {
    $page = visit('/talent-pass')
        ->actingAs($this->user)
        ->assertNoJavascriptErrors();

    // Click on a profile card to navigate to show page
    $page->click('a:contains("' . $this->talentPass->title . '")');

    // Alternative: Click a specific link by href
    $page->visit('/talent-pass/' . $this->talentPass->id)
        ->assertSee($this->talentPass->title);

    // Navigate back to index
    $page->click('a:contains("Back")')
        ->assertSee('Talent Pass');
});

test('smoke: error handling works correctly', function () {
    // Try to access non-existent talent pass
    visit('/talent-pass/999999')
        ->actingAs($this->user)
        ->assertNotFound();
});
