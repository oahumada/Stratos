<?php

use App\Models\LmsCourse;
use App\Models\LmsCourseRating;
use App\Models\LmsEnrollment;
use App\Models\Organization;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = User::factory()->admin()->create(['current_organization_id' => $this->org->id]);
});

function createCatalogCourse(object $testCase, array $overrides = []): LmsCourse
{
    return LmsCourse::create(array_merge([
        'title' => 'Test Course',
        'description' => 'A test course',
        'category' => 'Liderazgo',
        'level' => 'beginner',
        'estimated_duration_minutes' => 60,
        'is_active' => true,
        'organization_id' => $testCase->org->id,
        'enrollment_type' => 'open',
        'featured' => false,
        'tags' => ['leadership', 'management'],
    ], $overrides));
}

it('can search courses with filters', function () {
    Sanctum::actingAs($this->user, ['*']);

    createCatalogCourse($this, ['title' => 'Liderazgo Avanzado', 'category' => 'Liderazgo', 'level' => 'advanced']);
    createCatalogCourse($this, ['title' => 'Comunicación Básica', 'category' => 'Comunicación', 'level' => 'beginner']);

    $response = $this->getJson('/api/lms/catalog?category=Liderazgo');
    $response->assertOk();
    $data = $response->json('data');
    expect(count($data))->toBe(1);
    expect($data[0]['title'])->toBe('Liderazgo Avanzado');

    $response = $this->getJson('/api/lms/catalog?level=beginner');
    $response->assertOk();
    expect(count($response->json('data')))->toBe(1);
});

it('can sort courses', function () {
    Sanctum::actingAs($this->user, ['*']);

    $older = createCatalogCourse($this, ['title' => 'Curso A']);
    LmsCourse::where('id', $older->id)->update(['created_at' => now()->subDays(5)]);
    $newer = createCatalogCourse($this, ['title' => 'Curso B']);
    LmsCourse::where('id', $newer->id)->update(['created_at' => now()]);

    // Newest first (default)
    $response = $this->getJson('/api/lms/catalog?sort=newest');
    $response->assertOk();
    $data = $response->json('data');
    expect($data[0]['title'])->toBe('Curso B');

    // Popularity
    LmsEnrollment::create([
        'lms_course_id' => $older->id,
        'user_id' => $this->user->id,
        'status' => 'active',
        'progress_percentage' => 0,
        'started_at' => now(),
    ]);
    $response = $this->getJson('/api/lms/catalog?sort=popularity');
    $response->assertOk();
    expect($response->json('data.0.title'))->toBe('Curso A');
});

it('tags filter works with JSON column', function () {
    Sanctum::actingAs($this->user, ['*']);

    createCatalogCourse($this, ['title' => 'Con Tag', 'tags' => ['scrum', 'agile']]);
    createCatalogCourse($this, ['title' => 'Sin Tag', 'tags' => ['finance']]);

    $response = $this->getJson('/api/lms/catalog?tags[]=scrum');
    $response->assertOk();
    $data = $response->json('data');
    expect(count($data))->toBe(1);
    expect($data[0]['title'])->toBe('Con Tag');
});

it('can rate a course', function () {
    Sanctum::actingAs($this->user, ['*']);
    $course = createCatalogCourse($this);

    $response = $this->postJson("/api/lms/catalog/{$course->id}/rate", [
        'rating' => 4,
        'review' => 'Muy buen curso',
    ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);
    $this->assertDatabaseHas('lms_course_ratings', [
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'rating' => 4,
        'review' => 'Muy buen curso',
    ]);
});

it('cannot rate same course twice - updates instead', function () {
    Sanctum::actingAs($this->user, ['*']);
    $course = createCatalogCourse($this);

    $this->postJson("/api/lms/catalog/{$course->id}/rate", ['rating' => 3]);
    $this->postJson("/api/lms/catalog/{$course->id}/rate", ['rating' => 5, 'review' => 'Actualizado']);

    expect(LmsCourseRating::where('lms_course_id', $course->id)->where('user_id', $this->user->id)->count())->toBe(1);
    $this->assertDatabaseHas('lms_course_ratings', [
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'rating' => 5,
        'review' => 'Actualizado',
    ]);
});

it('rating must be 1-5', function () {
    Sanctum::actingAs($this->user, ['*']);
    $course = createCatalogCourse($this);

    $this->postJson("/api/lms/catalog/{$course->id}/rate", ['rating' => 0])
        ->assertUnprocessable();
    $this->postJson("/api/lms/catalog/{$course->id}/rate", ['rating' => 6])
        ->assertUnprocessable();
});

it('can self-enroll in open course', function () {
    Sanctum::actingAs($this->user, ['*']);
    $course = createCatalogCourse($this, ['enrollment_type' => 'open']);

    $response = $this->postJson("/api/lms/catalog/{$course->id}/enroll");

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    $this->assertDatabaseHas('lms_enrollments', [
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'active',
    ]);
});

it('cannot self-enroll in invite-only course', function () {
    Sanctum::actingAs($this->user, ['*']);
    $course = createCatalogCourse($this, ['enrollment_type' => 'invite']);

    $response = $this->postJson("/api/lms/catalog/{$course->id}/enroll");

    $response->assertStatus(422);
    $response->assertJsonFragment(['message' => 'Este curso no permite inscripción abierta.']);
});

it('cannot self-enroll twice', function () {
    Sanctum::actingAs($this->user, ['*']);
    $course = createCatalogCourse($this, ['enrollment_type' => 'open']);

    $this->postJson("/api/lms/catalog/{$course->id}/enroll")->assertStatus(201);
    $this->postJson("/api/lms/catalog/{$course->id}/enroll")->assertStatus(422);
});

it('course detail includes avg rating and enrollment count', function () {
    Sanctum::actingAs($this->user, ['*']);
    $course = createCatalogCourse($this);

    LmsCourseRating::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'rating' => 4,
    ]);

    LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'active',
        'progress_percentage' => 0,
        'started_at' => now(),
    ]);

    $response = $this->getJson("/api/lms/catalog/{$course->id}");
    $response->assertOk();
    $response->assertJsonPath('course.enrollments_count', 1);
    expect((float) $response->json('course.ratings_avg_rating'))->toBe(4.0);
    $response->assertJsonPath('modules_count', 0);
    $response->assertJsonPath('lessons_count', 0);
});

it('multi-tenant: cannot see courses from other org', function () {
    Sanctum::actingAs($this->user, ['*']);

    $otherOrg = Organization::factory()->create();
    LmsCourse::create([
        'title' => 'Other Org Course',
        'is_active' => true,
        'organization_id' => $otherOrg->id,
    ]);
    createCatalogCourse($this, ['title' => 'My Org Course']);

    $response = $this->getJson('/api/lms/catalog');
    $response->assertOk();
    $data = $response->json('data');
    expect(count($data))->toBe(1);
    expect($data[0]['title'])->toBe('My Org Course');
});

it('unauthenticated returns 401', function () {
    $this->getJson('/api/lms/catalog')->assertUnauthorized();
    $this->getJson('/api/lms/catalog/1')->assertUnauthorized();
    $this->postJson('/api/lms/catalog/1/rate', ['rating' => 5])->assertUnauthorized();
    $this->postJson('/api/lms/catalog/1/enroll')->assertUnauthorized();
    $this->getJson('/api/lms/catalog/recommendations')->assertUnauthorized();
    $this->getJson('/api/lms/catalog/categories')->assertUnauthorized();
    $this->getJson('/api/lms/catalog/tags')->assertUnauthorized();
});
