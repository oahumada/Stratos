<?php

use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsLearningPath;
use App\Models\LmsLearningPathEnrollment;
use App\Models\LmsLearningPathItem;
use App\Models\Organization;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('can create a learning path with items', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course1 = LmsCourse::create([
        'title' => 'Course 1',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);
    $course2 = LmsCourse::create([
        'title' => 'Course 2',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/lms/learning-paths', [
        'title' => 'Ruta de Liderazgo',
        'description' => 'Una ruta para líderes',
        'level' => 'intermediate',
        'estimated_duration_minutes' => 300,
        'is_mandatory' => true,
        'items' => [
            ['lms_course_id' => $course1->id, 'order' => 1, 'is_required' => true],
            ['lms_course_id' => $course2->id, 'order' => 2, 'is_required' => true],
        ],
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('lms_learning_paths', [
        'title' => 'Ruta de Liderazgo',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);
    $this->assertDatabaseCount('lms_learning_path_items', 2);
});

it('can list learning paths for organization', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    LmsLearningPath::create([
        'title' => 'Path 1',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);
    LmsLearningPath::create([
        'title' => 'Path 2',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);

    $response = $this->getJson('/api/lms/learning-paths');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});

it('can enroll in a learning path and creates course enrollments', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course1 = LmsCourse::create(['title' => 'C1', 'organization_id' => $org->id, 'is_active' => true]);
    $course2 = LmsCourse::create(['title' => 'C2', 'organization_id' => $org->id, 'is_active' => true]);

    $path = LmsLearningPath::create([
        'title' => 'Enrollment Path',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);
    $path->items()->create(['lms_course_id' => $course1->id, 'order' => 1]);
    $path->items()->create(['lms_course_id' => $course2->id, 'order' => 2]);

    $response = $this->postJson("/api/lms/learning-paths/{$path->id}/enroll");

    $response->assertOk();
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('lms_learning_path_enrollments', [
        'lms_learning_path_id' => $path->id,
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    // Should also create LmsEnrollment for each course
    $this->assertDatabaseHas('lms_enrollments', [
        'lms_course_id' => $course1->id,
        'user_id' => $user->id,
    ]);
    $this->assertDatabaseHas('lms_enrollments', [
        'lms_course_id' => $course2->id,
        'user_id' => $user->id,
    ]);
});

it('prerequisite engine: locked item returns locked, unlocked returns unlocked', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course1 = LmsCourse::create(['title' => 'Prereq Course', 'organization_id' => $org->id, 'is_active' => true]);
    $course2 = LmsCourse::create(['title' => 'Locked Course', 'organization_id' => $org->id, 'is_active' => true]);

    $path = LmsLearningPath::create([
        'title' => 'Prereq Path',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);

    $item1 = $path->items()->create(['lms_course_id' => $course1->id, 'order' => 1]);
    $item2 = $path->items()->create([
        'lms_course_id' => $course2->id,
        'order' => 2,
        'unlock_after_item_id' => $item1->id,
    ]);

    // Item 1 has no prereq, should be unlocked
    expect($item1->isUnlocked($user->id))->toBeTrue();

    // Item 2 depends on item 1, user hasn't completed course1 → locked
    expect($item2->isUnlocked($user->id))->toBeFalse();

    // Complete course 1
    LmsEnrollment::create([
        'lms_course_id' => $course1->id,
        'user_id' => $user->id,
        'status' => 'completed',
        'progress_percentage' => 100,
        'started_at' => now(),
        'completed_at' => now(),
    ]);

    // Now item 2 should be unlocked
    expect($item2->fresh()->isUnlocked($user->id))->toBeTrue();
});

it('progress recalculation works correctly', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course1 = LmsCourse::create(['title' => 'C1', 'organization_id' => $org->id, 'is_active' => true]);
    $course2 = LmsCourse::create(['title' => 'C2', 'organization_id' => $org->id, 'is_active' => true]);

    $path = LmsLearningPath::create([
        'title' => 'Progress Path',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);
    $path->items()->create(['lms_course_id' => $course1->id, 'order' => 1, 'is_required' => true]);
    $path->items()->create(['lms_course_id' => $course2->id, 'order' => 2, 'is_required' => true]);

    // Enroll
    $this->postJson("/api/lms/learning-paths/{$path->id}/enroll");

    // Complete course 1
    LmsEnrollment::where('lms_course_id', $course1->id)
        ->where('user_id', $user->id)
        ->update(['status' => 'completed', 'completed_at' => now()]);

    // Recalculate
    $response = $this->postJson("/api/lms/learning-paths/{$path->id}/recalculate");

    $response->assertOk();
    expect((float) $response->json('data.progress_percentage'))->toBe(50.0);
    expect($response->json('data.status'))->toBe('active');
});

it('completing all required courses marks path as completed', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course1 = LmsCourse::create(['title' => 'C1', 'organization_id' => $org->id, 'is_active' => true]);
    $course2 = LmsCourse::create(['title' => 'C2', 'organization_id' => $org->id, 'is_active' => true]);

    $path = LmsLearningPath::create([
        'title' => 'Complete Path',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);
    $path->items()->create(['lms_course_id' => $course1->id, 'order' => 1, 'is_required' => true]);
    $path->items()->create(['lms_course_id' => $course2->id, 'order' => 2, 'is_required' => false]);

    $this->postJson("/api/lms/learning-paths/{$path->id}/enroll");

    // Complete only the required course
    LmsEnrollment::where('lms_course_id', $course1->id)
        ->where('user_id', $user->id)
        ->update(['status' => 'completed', 'completed_at' => now()]);

    $response = $this->postJson("/api/lms/learning-paths/{$path->id}/recalculate");

    $response->assertOk();
    expect((float) $response->json('data.progress_percentage'))->toBe(100.0);
    expect($response->json('data.status'))->toBe('completed');
    expect($response->json('data.completed_at'))->not->toBeNull();
});

it('cannot enroll twice and returns existing enrollment', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create(['title' => 'C1', 'organization_id' => $org->id, 'is_active' => true]);

    $path = LmsLearningPath::create([
        'title' => 'Double Enroll Path',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);
    $path->items()->create(['lms_course_id' => $course->id, 'order' => 1]);

    // First enrollment
    $response1 = $this->postJson("/api/lms/learning-paths/{$path->id}/enroll");
    $response1->assertOk();

    // Second enrollment — should return existing
    $response2 = $this->postJson("/api/lms/learning-paths/{$path->id}/enroll");
    $response2->assertOk();

    $this->assertDatabaseCount('lms_learning_path_enrollments', 1);
    expect($response1->json('data.id'))->toBe($response2->json('data.id'));
});

it('cannot delete path with active enrollments', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $path = LmsLearningPath::create([
        'title' => 'Delete Path',
        'organization_id' => $org->id,
        'created_by' => $user->id,
    ]);

    LmsLearningPathEnrollment::create([
        'lms_learning_path_id' => $path->id,
        'user_id' => $user->id,
        'organization_id' => $org->id,
        'status' => 'active',
        'started_at' => now(),
    ]);

    $response = $this->deleteJson("/api/lms/learning-paths/{$path->id}");

    $response->assertStatus(422);
    $this->assertDatabaseHas('lms_learning_paths', ['id' => $path->id]);
});

it('cannot access path from another organization', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org1->id]);
    Sanctum::actingAs($user, ['*']);

    $path = LmsLearningPath::create([
        'title' => 'Other Org Path',
        'organization_id' => $org2->id,
        'created_by' => $user->id,
    ]);

    $this->getJson("/api/lms/learning-paths/{$path->id}")
        ->assertNotFound();
});

it('unauthenticated returns 401', function () {
    $this->getJson('/api/lms/learning-paths')
        ->assertUnauthorized();

    $this->postJson('/api/lms/learning-paths', ['title' => 'Test'])
        ->assertUnauthorized();

    $this->postJson('/api/lms/learning-paths/1/enroll')
        ->assertUnauthorized();

    $this->getJson('/api/lms/learning-paths/1/progress')
        ->assertUnauthorized();
});
