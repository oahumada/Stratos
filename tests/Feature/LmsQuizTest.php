<?php

use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsLesson;
use App\Models\LmsModule;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->org = Organization::factory()->create();
    $this->user = User::factory()->admin()->create([
        'organization_id' => $this->org->id,
        'current_organization_id' => $this->org->id,
    ]);
    Sanctum::actingAs($this->user, ['*']);
});

// Helper to create a quiz with questions
function createQuizWithQuestions(int $orgId, ?int $lessonId = null): LmsQuiz
{
    $quiz = LmsQuiz::create([
        'organization_id' => $orgId,
        'lms_lesson_id' => $lessonId,
        'title' => 'Test Quiz',
        'passing_score' => 70,
        'max_attempts' => 3,
        'is_active' => true,
    ]);

    LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'What is 2+2?',
        'question_type' => 'multiple_choice',
        'options' => [
            ['id' => 'a', 'text' => '3'],
            ['id' => 'b', 'text' => '4'],
            ['id' => 'c', 'text' => '5'],
        ],
        'correct_answer' => ['b'],
        'points' => 1,
        'order' => 0,
    ]);

    LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'The sky is blue.',
        'question_type' => 'true_false',
        'correct_answer' => [true],
        'points' => 1,
        'order' => 1,
    ]);

    LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'The capital of France is ___.',
        'question_type' => 'fill_blank',
        'correct_answer' => ['Paris', 'paris'],
        'points' => 1,
        'order' => 2,
    ]);

    return $quiz;
}

it('can create a quiz with questions', function () {
    $response = $this->postJson('/api/lms/quizzes', [
        'title' => 'New Quiz',
        'description' => 'A test quiz',
        'passing_score' => 80,
        'max_attempts' => 2,
        'questions' => [
            [
                'question_text' => 'What is 1+1?',
                'question_type' => 'multiple_choice',
                'options' => [['id' => 'a', 'text' => '1'], ['id' => 'b', 'text' => '2']],
                'correct_answer' => ['b'],
                'points' => 1,
            ],
        ],
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('title', 'New Quiz');
    $response->assertJsonPath('passing_score', 80);

    $this->assertDatabaseHas('lms_quizzes', [
        'title' => 'New Quiz',
        'organization_id' => $this->org->id,
    ]);
    $this->assertDatabaseHas('lms_quiz_questions', [
        'question_text' => 'What is 1+1?',
    ]);
});

it('can list quizzes for organization (multi-tenant isolation)', function () {
    $otherOrg = Organization::factory()->create();

    LmsQuiz::create(['title' => 'My Quiz', 'organization_id' => $this->org->id]);
    LmsQuiz::create(['title' => 'Other Quiz', 'organization_id' => $otherOrg->id]);

    $response = $this->getJson('/api/lms/quizzes');

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['title'])->toBe('My Quiz');
});

it('can start an attempt and respects max_attempts', function () {
    $quiz = createQuizWithQuestions($this->org->id);

    // Start first attempt
    $response = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
    $response->assertStatus(201);
    $attemptId = $response->json('id');

    // Complete the attempt
    LmsQuizAttempt::where('id', $attemptId)->update(['completed_at' => now()]);

    // Start and complete more attempts until max
    for ($i = 1; $i < $quiz->max_attempts; $i++) {
        $resp = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
        $resp->assertStatus(201);
        LmsQuizAttempt::where('id', $resp->json('id'))->update(['completed_at' => now()]);
    }

    // Should fail - max attempts reached
    $response = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
    $response->assertStatus(422);
});

it('can submit and auto-grade multiple_choice', function () {
    $quiz = LmsQuiz::create([
        'organization_id' => $this->org->id,
        'title' => 'MC Quiz',
        'passing_score' => 50,
    ]);

    $q = LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'Pick B',
        'question_type' => 'multiple_choice',
        'options' => [['id' => 'a', 'text' => 'A'], ['id' => 'b', 'text' => 'B']],
        'correct_answer' => ['b'],
        'points' => 1,
        'order' => 0,
    ]);

    // Start attempt
    $start = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
    $attemptId = $start->json('id');

    $response = $this->postJson("/api/lms/quizzes/{$quiz->id}/submit", [
        'attempt_id' => $attemptId,
        'answers' => [
            ['question_id' => $q->id, 'answer' => ['b']],
        ],
    ]);

    $response->assertOk();
    expect((float) $response->json('score'))->toBe(100.0);
    expect($response->json('passed'))->toBeTrue();
});

it('can submit and auto-grade true_false', function () {
    $quiz = LmsQuiz::create([
        'organization_id' => $this->org->id,
        'title' => 'TF Quiz',
        'passing_score' => 50,
    ]);

    $q = LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'True or false?',
        'question_type' => 'true_false',
        'correct_answer' => [true],
        'points' => 1,
        'order' => 0,
    ]);

    $start = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
    $attemptId = $start->json('id');

    $response = $this->postJson("/api/lms/quizzes/{$quiz->id}/submit", [
        'attempt_id' => $attemptId,
        'answers' => [
            ['question_id' => $q->id, 'answer' => [true]],
        ],
    ]);

    $response->assertOk();
    expect((float) $response->json('score'))->toBe(100.0);
    expect($response->json('passed'))->toBeTrue();
});

it('can submit and auto-grade fill_blank', function () {
    $quiz = LmsQuiz::create([
        'organization_id' => $this->org->id,
        'title' => 'FB Quiz',
        'passing_score' => 50,
    ]);

    $q = LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'Capital of France?',
        'question_type' => 'fill_blank',
        'correct_answer' => ['Paris', 'paris'],
        'points' => 1,
        'order' => 0,
    ]);

    $start = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
    $attemptId = $start->json('id');

    $response = $this->postJson("/api/lms/quizzes/{$quiz->id}/submit", [
        'attempt_id' => $attemptId,
        'answers' => [
            ['question_id' => $q->id, 'answer' => ['Paris']],
        ],
    ]);

    $response->assertOk();
    expect((float) $response->json('score'))->toBe(100.0);
});

it('passing score works correctly (pass/fail)', function () {
    $quiz = LmsQuiz::create([
        'organization_id' => $this->org->id,
        'title' => 'Pass/Fail Quiz',
        'passing_score' => 100,
    ]);

    $q1 = LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'Q1',
        'question_type' => 'multiple_choice',
        'options' => [['id' => 'a', 'text' => 'A'], ['id' => 'b', 'text' => 'B']],
        'correct_answer' => ['a'],
        'points' => 1,
        'order' => 0,
    ]);

    $q2 = LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'Q2',
        'question_type' => 'multiple_choice',
        'options' => [['id' => 'a', 'text' => 'A'], ['id' => 'b', 'text' => 'B']],
        'correct_answer' => ['b'],
        'points' => 1,
        'order' => 1,
    ]);

    $start = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
    $attemptId = $start->json('id');

    // Answer one right, one wrong => 50%, passing is 100%
    $response = $this->postJson("/api/lms/quizzes/{$quiz->id}/submit", [
        'attempt_id' => $attemptId,
        'answers' => [
            ['question_id' => $q1->id, 'answer' => ['a']],
            ['question_id' => $q2->id, 'answer' => ['a']], // wrong
        ],
    ]);

    $response->assertOk();
    expect((float) $response->json('score'))->toBe(50.0);
    expect($response->json('passed'))->toBeFalse();
});

it('time_limit enforced - cannot submit after time expires', function () {
    $quiz = LmsQuiz::create([
        'organization_id' => $this->org->id,
        'title' => 'Timed Quiz',
        'passing_score' => 50,
        'time_limit_minutes' => 5,
    ]);

    $q = LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'Answer this',
        'question_type' => 'true_false',
        'correct_answer' => [true],
        'points' => 1,
        'order' => 0,
    ]);

    // Start attempt and set started_at in the past
    $start = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
    $attemptId = $start->json('id');

    LmsQuizAttempt::where('id', $attemptId)->update([
        'started_at' => now()->subMinutes(10),
    ]);

    $response = $this->postJson("/api/lms/quizzes/{$quiz->id}/submit", [
        'attempt_id' => $attemptId,
        'answers' => [
            ['question_id' => $q->id, 'answer' => [true]],
        ],
    ]);

    $response->assertStatus(422);
});

it('quiz score updates enrollment assessment_score when linked to lesson', function () {
    $course = LmsCourse::create([
        'title' => 'Course with Quiz',
        'organization_id' => $this->org->id,
    ]);

    $module = $course->modules()->create(['title' => 'Module 1', 'order' => 1]);
    $lesson = $module->lessons()->create([
        'title' => 'Quiz Lesson',
        'content_type' => 'quiz',
        'order' => 1,
        'duration_minutes' => 10,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $this->user->id,
        'status' => 'in_progress',
    ]);

    $quiz = LmsQuiz::create([
        'organization_id' => $this->org->id,
        'lms_lesson_id' => $lesson->id,
        'title' => 'Linked Quiz',
        'passing_score' => 50,
    ]);

    $q = LmsQuizQuestion::create([
        'lms_quiz_id' => $quiz->id,
        'question_text' => 'Easy question',
        'question_type' => 'true_false',
        'correct_answer' => [true],
        'points' => 1,
        'order' => 0,
    ]);

    $start = $this->postJson("/api/lms/quizzes/{$quiz->id}/start");
    $attemptId = $start->json('id');

    $this->postJson("/api/lms/quizzes/{$quiz->id}/submit", [
        'attempt_id' => $attemptId,
        'answers' => [
            ['question_id' => $q->id, 'answer' => [true]],
        ],
    ]);

    $enrollment->refresh();
    expect((float) $enrollment->assessment_score)->toBe(100.0);
});

it('cannot access quiz from another organization (403 via not found)', function () {
    $otherOrg = Organization::factory()->create();

    $quiz = LmsQuiz::create([
        'organization_id' => $otherOrg->id,
        'title' => 'Other Org Quiz',
    ]);

    $response = $this->getJson("/api/lms/quizzes/{$quiz->id}");
    // Returns 404 because org scoping filters it out
    $response->assertNotFound();
});

it('unauthenticated user cannot access quizzes', function () {
    // Reset auth
    app('auth')->forgetGuards();

    $response = $this->getJson('/api/lms/quizzes');
    $response->assertUnauthorized();
});
