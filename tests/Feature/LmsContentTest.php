<?php

use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsInteractiveContent;
use App\Models\LmsLesson;
use App\Models\LmsMicroContent;
use App\Models\LmsModule;
use App\Models\LmsVideoTracking;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function createAuthUserWithLesson(?Organization $org = null): array
{
    $org = $org ?? Organization::factory()->create();
    $user = User::factory()->admin()->create([
        'organization_id' => $org->id,
        'current_organization_id' => $org->id,
    ]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'organization_id' => $org->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $module = LmsModule::create([
        'lms_course_id' => $course->id,
        'title' => 'Module 1',
        'order' => 1,
    ]);

    $lesson = LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Lesson 1',
        'content_type' => 'video',
        'order' => 1,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'in_progress',
        'progress_percentage' => 0,
    ]);

    return [$org, $user, $course, $module, $lesson, $enrollment];
}

// ── Video Tracking Tests ──

it('can get or create video tracking', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    $response = $this->getJson("/api/lms/lessons/{$lesson->id}/video/tracking?provider=youtube&video_id=abc123&duration=600");

    $response->assertOk();
    $response->assertJsonStructure(['data' => ['tracking', 'progress_percentage', 'embed']]);
    expect($response->json('data.tracking.provider'))->toBe('youtube');
    expect($response->json('data.tracking.video_id'))->toBe('abc123');
    expect($response->json('data.embed.embed_url'))->toContain('youtube.com/embed/abc123');
    expect(LmsVideoTracking::count())->toBe(1);
});

it('can update video progress', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    LmsVideoTracking::create([
        'organization_id' => $org->id,
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $lesson->id,
        'user_id' => $user->id,
        'provider' => 'youtube',
        'video_id' => 'abc123',
        'duration_seconds' => 600,
        'watched_seconds' => 0,
        'last_position' => 0,
    ]);

    $response = $this->postJson("/api/lms/lessons/{$lesson->id}/video/progress", [
        'watched_seconds' => 300,
        'last_position' => 300,
    ]);

    $response->assertOk();
    expect($response->json('data.tracking.watched_seconds'))->toBe(300);
    expect((float) $response->json('data.progress_percentage'))->toBe(50.0);
});

it('marks video completed when threshold reached', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    LmsVideoTracking::create([
        'organization_id' => $org->id,
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $lesson->id,
        'user_id' => $user->id,
        'provider' => 'vimeo',
        'video_id' => 'vid456',
        'duration_seconds' => 100,
        'watched_seconds' => 0,
        'last_position' => 0,
        'completion_threshold' => 0.90,
    ]);

    $response = $this->postJson("/api/lms/lessons/{$lesson->id}/video/progress", [
        'watched_seconds' => 95,
        'last_position' => 95,
    ]);

    $response->assertOk();
    expect($response->json('data.tracking.completed'))->toBeTrue();
});

it('does not mark video completed below threshold', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    LmsVideoTracking::create([
        'organization_id' => $org->id,
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $lesson->id,
        'user_id' => $user->id,
        'provider' => 'youtube',
        'video_id' => 'abc',
        'duration_seconds' => 100,
        'watched_seconds' => 0,
        'last_position' => 0,
        'completion_threshold' => 0.90,
    ]);

    $response = $this->postJson("/api/lms/lessons/{$lesson->id}/video/progress", [
        'watched_seconds' => 80,
        'last_position' => 80,
    ]);

    $response->assertOk();
    expect($response->json('data.tracking.completed'))->toBeFalse();
});

it('can get video stats for instructor', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    LmsVideoTracking::create([
        'organization_id' => $org->id,
        'enrollment_id' => $enrollment->id,
        'lesson_id' => $lesson->id,
        'user_id' => $user->id,
        'provider' => 'youtube',
        'video_id' => 'abc',
        'duration_seconds' => 100,
        'watched_seconds' => 100,
        'last_position' => 100,
        'completed' => true,
    ]);

    $response = $this->getJson("/api/lms/lessons/{$lesson->id}/video/stats");

    $response->assertOk();
    expect($response->json('data.total_viewers'))->toBe(1);
    expect($response->json('data.completed_count'))->toBe(1);
    expect((float) $response->json('data.completion_rate'))->toBe(100.0);
});

// ── Micro Content Tests ──

it('can create micro content for a lesson', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    $cards = [
        ['type' => 'text', 'title' => 'Intro', 'content' => 'Welcome', 'media_url' => null, 'quiz_data' => null],
        ['type' => 'tip', 'title' => 'Tip 1', 'content' => 'Remember this', 'media_url' => null, 'quiz_data' => null],
    ];

    $response = $this->postJson("/api/lms/lessons/{$lesson->id}/micro", [
        'cards' => $cards,
        'estimated_minutes' => 3,
    ]);

    $response->assertStatus(201);
    expect(LmsMicroContent::count())->toBe(1);
    expect($response->json('data.cards'))->toHaveCount(2);
    expect($response->json('data.estimated_minutes'))->toBe(3);
});

it('can get micro content for a lesson', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    LmsMicroContent::create([
        'organization_id' => $org->id,
        'lesson_id' => $lesson->id,
        'cards' => [['type' => 'text', 'title' => 'Card 1', 'content' => 'Hello']],
        'estimated_minutes' => 2,
    ]);

    $response = $this->getJson("/api/lms/lessons/{$lesson->id}/micro");

    $response->assertOk();
    expect($response->json('data.card_count'))->toBe(1);
});

it('updates existing micro content on repeat store', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    LmsMicroContent::create([
        'organization_id' => $org->id,
        'lesson_id' => $lesson->id,
        'cards' => [['type' => 'text', 'title' => 'Old', 'content' => 'Old']],
        'estimated_minutes' => 2,
    ]);

    $newCards = [
        ['type' => 'tip', 'title' => 'New', 'content' => 'New content', 'media_url' => null, 'quiz_data' => null],
    ];

    $response = $this->postJson("/api/lms/lessons/{$lesson->id}/micro", [
        'cards' => $newCards,
        'estimated_minutes' => 1,
    ]);

    $response->assertOk();
    expect(LmsMicroContent::count())->toBe(1);
    expect($response->json('data.cards.0.title'))->toBe('New');
});

it('can generate micro content from lesson', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    $lesson->update(['content_body' => 'This is a test lesson with enough content to generate multiple micro cards for learning purposes.']);

    $response = $this->postJson("/api/lms/lessons/{$lesson->id}/micro/generate");

    $response->assertOk();
    expect($response->json('data.cards'))->toBeArray();
    expect(count($response->json('data.cards')))->toBeGreaterThan(0);
});

// ── Interactive Content Tests ──

it('can create interactive content', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    $response = $this->postJson("/api/lms/lessons/{$lesson->id}/interactive", [
        'widget_type' => 'accordion',
        'config' => ['panels' => [['title' => 'Panel 1', 'content' => 'Content 1']]],
        'title' => 'Test Accordion',
    ]);

    $response->assertStatus(201);
    expect($response->json('data.widget_type'))->toBe('accordion');
    expect($response->json('data.title'))->toBe('Test Accordion');
    expect(LmsInteractiveContent::count())->toBe(1);
});

it('can list interactive content for lesson', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    LmsInteractiveContent::create([
        'organization_id' => $org->id,
        'lesson_id' => $lesson->id,
        'widget_type' => 'tabs',
        'config' => ['tabs' => [['label' => 'Tab 1', 'content' => 'Content']]],
        'title' => 'My Tabs',
    ]);

    $response = $this->getJson("/api/lms/lessons/{$lesson->id}/interactive");

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

it('can update interactive content', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    $content = LmsInteractiveContent::create([
        'organization_id' => $org->id,
        'lesson_id' => $lesson->id,
        'widget_type' => 'accordion',
        'config' => ['panels' => []],
        'title' => 'Original',
    ]);

    $response = $this->putJson("/api/lms/interactive/{$content->id}", [
        'config' => ['panels' => [['title' => 'Updated', 'content' => 'New']]],
        'title' => 'Updated Title',
    ]);

    $response->assertOk();
    expect($response->json('data.title'))->toBe('Updated Title');
});

it('can delete interactive content', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    $content = LmsInteractiveContent::create([
        'organization_id' => $org->id,
        'lesson_id' => $lesson->id,
        'widget_type' => 'timeline',
        'config' => ['events' => []],
        'title' => 'Timeline',
    ]);

    $response = $this->deleteJson("/api/lms/interactive/{$content->id}");

    $response->assertOk();
    expect(LmsInteractiveContent::count())->toBe(0);
});

it('can get available widget types', function () {
    [$org, $user] = [Organization::factory()->create(), null];
    $user = User::factory()->admin()->create([
        'organization_id' => $org->id,
        'current_organization_id' => $org->id,
    ]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/lms/interactive/widget-types');

    $response->assertOk();
    $types = collect($response->json('data'))->pluck('type')->all();
    expect($types)->toContain('accordion', 'tabs', 'timeline', 'hotspot', 'drag_drop', 'fill_blanks');
});

it('rejects invalid widget type', function () {
    [$org, $user, $course, $module, $lesson, $enrollment] = createAuthUserWithLesson();

    $response = $this->postJson("/api/lms/lessons/{$lesson->id}/interactive", [
        'widget_type' => 'invalid_type',
        'config' => [],
        'title' => 'Bad Widget',
    ]);

    $response->assertStatus(422);
});

// ── Multi-tenant Isolation Tests ──

it('video tracking is tenant-isolated', function () {
    [$org1, $user1, $course1, $module1, $lesson1, $enrollment1] = createAuthUserWithLesson();

    LmsVideoTracking::create([
        'organization_id' => $org1->id,
        'enrollment_id' => $enrollment1->id,
        'lesson_id' => $lesson1->id,
        'user_id' => $user1->id,
        'provider' => 'youtube',
        'video_id' => 'abc',
        'duration_seconds' => 100,
        'watched_seconds' => 50,
        'last_position' => 50,
    ]);

    // Create second org user
    $org2 = Organization::factory()->create();
    $user2 = User::factory()->admin()->create([
        'organization_id' => $org2->id,
        'current_organization_id' => $org2->id,
    ]);
    Sanctum::actingAs($user2, ['*']);

    $response = $this->getJson("/api/lms/lessons/{$lesson1->id}/video/stats");
    $response->assertOk();
    expect($response->json('data.total_viewers'))->toBe(0);
});

it('micro content is tenant-isolated', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $user1 = User::factory()->admin()->create([
        'organization_id' => $org1->id,
        'current_organization_id' => $org1->id,
    ]);

    $course = LmsCourse::create(['organization_id' => $org1->id, 'title' => 'C1', 'is_active' => true]);
    $module = LmsModule::create(['lms_course_id' => $course->id, 'title' => 'M1', 'order' => 1]);
    $lesson = LmsLesson::create(['lms_module_id' => $module->id, 'title' => 'L1', 'content_type' => 'micro', 'order' => 1]);

    LmsMicroContent::create([
        'organization_id' => $org1->id,
        'lesson_id' => $lesson->id,
        'cards' => [['type' => 'text', 'title' => 'Org1 Card', 'content' => 'Private']],
        'estimated_minutes' => 2,
    ]);

    expect(LmsMicroContent::forOrganization($org1->id)->count())->toBe(1);
    expect(LmsMicroContent::forOrganization($org2->id)->count())->toBe(0);
});

it('interactive content is tenant-isolated', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();

    $course = LmsCourse::create(['organization_id' => $org1->id, 'title' => 'C1', 'is_active' => true]);
    $module = LmsModule::create(['lms_course_id' => $course->id, 'title' => 'M1', 'order' => 1]);
    $lesson = LmsLesson::create(['lms_module_id' => $module->id, 'title' => 'L1', 'content_type' => 'interactive', 'order' => 1]);

    LmsInteractiveContent::create([
        'organization_id' => $org1->id,
        'lesson_id' => $lesson->id,
        'widget_type' => 'accordion',
        'config' => ['panels' => []],
        'title' => 'Org1 Widget',
    ]);

    expect(LmsInteractiveContent::forOrganization($org1->id)->count())->toBe(1);
    expect(LmsInteractiveContent::forOrganization($org2->id)->count())->toBe(0);
});

it('unauthenticated users cannot access video tracking', function () {
    // Reset auth
    app('auth')->forgetGuards();

    $response = $this->getJson('/api/lms/lessons/1/video/tracking');
    $response->assertStatus(401);
});
