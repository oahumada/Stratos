<?php

use App\Models\LmsCourse;
use App\Models\Organization;
use App\Models\User;
use App\Services\AiOrchestratorService;
use App\Services\Content\ContentAgentService;
use Laravel\Sanctum\Sanctum;

it('generates an outline via AI', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $this->mock(AiOrchestratorService::class, function ($mock) {
        $mock->shouldReceive('agentThink')
            ->once()
            ->with('Arquitecto de Aprendizaje', \Mockery::type('string'))
            ->andReturn([
                'response' => json_encode([
                    'course_outline' => 'Curso sobre liderazgo',
                    'learning_objectives' => ['Objetivo 1', 'Objetivo 2'],
                    'modules' => [
                        [
                            'title' => 'Módulo 1',
                            'description' => 'Introducción',
                            'lessons' => [
                                ['title' => 'Lección 1', 'duration_minutes' => 30, 'content_type' => 'article'],
                            ],
                        ],
                    ],
                    'assessment_plan' => 'Evaluación final',
                ]),
            ]);
    });

    $response = $this->postJson('/api/lms/course-designer/generate-outline', [
        'topic' => 'Liderazgo transformacional',
        'target_audience' => 'Gerentes de nivel medio',
        'duration_target' => 10,
        'level' => 'intermediate',
    ]);

    $response->assertOk();
    $response->assertJsonStructure(['response']);
});

it('generates lesson content', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $this->mock(ContentAgentService::class, function ($mock) {
        $mock->shouldReceive('generateDraft')
            ->once()
            ->andReturn([
                'title' => 'Lección sobre comunicación',
                'body' => '<h1>Comunicación efectiva</h1><p>Contenido educativo...</p>',
                'excerpt' => 'Comunicación efectiva',
                'slug' => 'comunicacion-efectiva-abc123',
            ]);
    });

    $response = $this->postJson('/api/lms/course-designer/generate-content', [
        'lesson_title' => 'Comunicación efectiva',
        'module_context' => 'Habilidades blandas',
        'course_topic' => 'Liderazgo',
        'content_type' => 'article',
    ]);

    $response->assertOk();
    $response->assertJsonStructure(['title', 'body', 'content_type', 'estimated_duration']);
});

it('persists a complete course with modules and lessons', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $payload = [
        'title' => 'Curso de Prueba',
        'description' => 'Descripción del curso',
        'category' => 'Liderazgo',
        'level' => 'beginner',
        'estimated_duration_minutes' => 120,
        'xp_points' => 500,
        'is_active' => false,
        'modules' => [
            [
                'title' => 'Módulo 1',
                'order' => 1,
                'lessons' => [
                    [
                        'title' => 'Lección 1.1',
                        'content_type' => 'article',
                        'content_body' => '<p>Contenido</p>',
                        'order' => 1,
                        'duration_minutes' => 30,
                    ],
                    [
                        'title' => 'Lección 1.2',
                        'content_type' => 'video',
                        'order' => 2,
                        'duration_minutes' => 45,
                    ],
                ],
            ],
            [
                'title' => 'Módulo 2',
                'order' => 2,
                'lessons' => [
                    [
                        'title' => 'Lección 2.1',
                        'content_type' => 'exercise',
                        'order' => 1,
                        'duration_minutes' => 45,
                    ],
                ],
            ],
        ],
    ];

    $response = $this->postJson('/api/lms/course-designer/persist', $payload);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);

    $courseId = $response->json('course.id');
    $this->assertDatabaseHas('lms_courses', [
        'id' => $courseId,
        'title' => 'Curso de Prueba',
        'organization_id' => $org->id,
    ]);
    $this->assertDatabaseCount('lms_modules', 2);
    $this->assertDatabaseCount('lms_lessons', 3);
});

it('persists course scoped to organization', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $response = $this->postJson('/api/lms/course-designer/persist', [
        'title' => 'Curso Org-Scoped',
        'level' => 'advanced',
    ]);

    $response->assertStatus(201);

    $course = LmsCourse::find($response->json('course.id'));
    expect($course->organization_id)->toBe($org->id);
});

it('previews a course with all relations', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => 'Preview Course',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);

    $module = $course->modules()->create(['title' => 'Mod 1', 'order' => 1]);
    $module->lessons()->create([
        'title' => 'Les 1',
        'content_type' => 'article',
        'order' => 1,
        'duration_minutes' => 20,
    ]);

    $response = $this->getJson("/api/lms/course-designer/{$course->id}/preview");

    $response->assertOk();
    $response->assertJsonPath('course.title', 'Preview Course');
    $response->assertJsonStructure(['course' => ['id', 'title', 'modules' => [['id', 'title', 'lessons']]]]);
});

it('reviews a course via AI', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $course = LmsCourse::create([
        'title' => 'Review Course',
        'organization_id' => $org->id,
        'is_active' => true,
    ]);
    $module = $course->modules()->create(['title' => 'Mod 1', 'order' => 1]);
    $module->lessons()->create([
        'title' => 'Les 1',
        'content_type' => 'article',
        'order' => 1,
        'duration_minutes' => 20,
    ]);

    $this->mock(AiOrchestratorService::class, function ($mock) {
        $mock->shouldReceive('agentThink')
            ->once()
            ->with('Arquitecto de Aprendizaje', \Mockery::type('string'))
            ->andReturn([
                'response' => json_encode([
                    'score' => 85,
                    'strengths' => ['Buena estructura'],
                    'improvements' => ['Agregar más ejercicios'],
                    'suggestions' => ['Incluir evaluación final'],
                ]),
            ]);
    });

    $response = $this->postJson("/api/lms/course-designer/{$course->id}/review");

    $response->assertOk();
    $response->assertJsonStructure(['response']);
});

it('requires authentication', function () {
    $this->postJson('/api/lms/course-designer/generate-outline', [
        'topic' => 'Test',
        'target_audience' => 'Test',
    ])->assertUnauthorized();

    $this->postJson('/api/lms/course-designer/generate-content', [
        'lesson_title' => 'Test',
    ])->assertUnauthorized();

    $this->postJson('/api/lms/course-designer/persist', [
        'title' => 'Test',
    ])->assertUnauthorized();

    $this->getJson('/api/lms/course-designer/1/preview')
        ->assertUnauthorized();

    $this->postJson('/api/lms/course-designer/1/review')
        ->assertUnauthorized();
});

it('prevents cross-tenant access on preview', function () {
    $org = Organization::factory()->create();
    $otherOrg = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    $otherCourse = LmsCourse::create([
        'title' => 'Other Org Course',
        'organization_id' => $otherOrg->id,
        'is_active' => true,
    ]);

    $this->getJson("/api/lms/course-designer/{$otherCourse->id}/preview")
        ->assertNotFound();
});
