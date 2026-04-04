<?php

use App\Models\LmsAdaptiveRule;
use App\Models\LmsCohort;
use App\Models\LmsCohortMember;
use App\Models\LmsCourse;
use App\Models\LmsLearnerProfile;
use App\Models\LmsLesson;
use App\Models\LmsModule;
use App\Models\LmsPeerReview;
use App\Models\LmsUserContent;
use App\Models\Organization;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

// ── Helpers ──────────────────────────────────────────────────────────────────

function createOrgAndAdmin(): array
{
    $org = Organization::factory()->create();
    $user = User::factory()->admin()->create(['current_organization_id' => $org->id]);
    Sanctum::actingAs($user, ['*']);

    return [$org, $user];
}

function createLessonForOrg(int $orgId): LmsLesson
{
    $course = LmsCourse::create(['title' => 'Test Course', 'organization_id' => $orgId, 'is_active' => true]);
    $module = LmsModule::create(['lms_course_id' => $course->id, 'title' => 'Module 1', 'order' => 1]);

    return LmsLesson::create([
        'lms_module_id' => $module->id,
        'title' => 'Test Lesson',
        'content_type' => 'text',
        'order' => 1,
    ]);
}

// ── Peer Review Tests ────────────────────────────────────────────────────────

it('can create peer review assignments', function () {
    [$org, $admin] = createOrgAndAdmin();
    $lesson = createLessonForOrg($org->id);
    $reviewee = User::factory()->create(['current_organization_id' => $org->id]);

    $response = $this->postJson('/api/lms/peer-reviews', [
        'lesson_id' => $lesson->id,
        'reviewer_ids' => [$admin->id],
        'reviewee_ids' => [$reviewee->id],
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_peer_reviews', [
        'assignment_id' => $lesson->id,
        'reviewer_id' => $admin->id,
        'reviewee_id' => $reviewee->id,
        'organization_id' => $org->id,
        'status' => 'pending_submission',
    ]);
});

it('can submit work for peer review', function () {
    [$org, $user] = createOrgAndAdmin();
    $lesson = createLessonForOrg($org->id);
    $reviewer = User::factory()->create(['current_organization_id' => $org->id]);

    $peerReview = LmsPeerReview::create([
        'organization_id' => $org->id,
        'assignment_id' => $lesson->id,
        'reviewer_id' => $reviewer->id,
        'reviewee_id' => $user->id,
        'status' => 'pending_submission',
    ]);

    $response = $this->postJson("/api/lms/peer-reviews/{$peerReview->id}/submit-work", [
        'submission_url' => 'https://example.com/my-work',
        'submission_text' => 'Here is my submission.',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('lms_peer_reviews', [
        'id' => $peerReview->id,
        'status' => 'submitted',
        'submission_url' => 'https://example.com/my-work',
    ]);
});

it('can submit a review with score and feedback', function () {
    [$org, $user] = createOrgAndAdmin();
    $lesson = createLessonForOrg($org->id);
    $reviewee = User::factory()->create(['current_organization_id' => $org->id]);

    $peerReview = LmsPeerReview::create([
        'organization_id' => $org->id,
        'assignment_id' => $lesson->id,
        'reviewer_id' => $user->id,
        'reviewee_id' => $reviewee->id,
        'status' => 'submitted',
        'submitted_at' => now(),
    ]);

    $response = $this->postJson("/api/lms/peer-reviews/{$peerReview->id}/submit-review", [
        'score' => 85.5,
        'feedback' => 'Good work overall.',
        'rubric_scores' => [
            ['criterion' => 'Clarity', 'score' => 8, 'max' => 10, 'comment' => 'Clear writing'],
        ],
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('lms_peer_reviews', [
        'id' => $peerReview->id,
        'status' => 'reviewed',
        'review_score' => 85.5,
    ]);
});

it('can get aggregated peer review scores', function () {
    [$org, $admin] = createOrgAndAdmin();
    $lesson = createLessonForOrg($org->id);
    $reviewee = User::factory()->create(['current_organization_id' => $org->id]);

    LmsPeerReview::create([
        'organization_id' => $org->id,
        'assignment_id' => $lesson->id,
        'reviewer_id' => $admin->id,
        'reviewee_id' => $reviewee->id,
        'status' => 'reviewed',
        'review_score' => 80,
        'reviewed_at' => now(),
    ]);

    $response = $this->getJson("/api/lms/lessons/{$lesson->id}/peer-scores");
    $response->assertOk();
    $response->assertJsonFragment(['reviewee_id' => $reviewee->id]);
});

// ── UGC Tests ────────────────────────────────────────────────────────────────

it('can create UGC content', function () {
    [$org, $user] = createOrgAndAdmin();

    $response = $this->postJson('/api/lms/ugc', [
        'title' => 'My Article',
        'description' => 'A great article',
        'content_type' => 'article',
        'content_body' => 'This is the body.',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_user_contents', [
        'title' => 'My Article',
        'status' => 'draft',
        'author_id' => $user->id,
        'organization_id' => $org->id,
    ]);
});

it('can submit UGC for review', function () {
    [$org, $user] = createOrgAndAdmin();

    $content = LmsUserContent::create([
        'organization_id' => $org->id,
        'author_id' => $user->id,
        'title' => 'Draft Content',
        'content_type' => 'article',
        'status' => 'draft',
    ]);

    $response = $this->postJson("/api/lms/ugc/{$content->id}/submit");
    $response->assertOk();
    $this->assertDatabaseHas('lms_user_contents', ['id' => $content->id, 'status' => 'pending_review']);
});

it('can approve UGC content', function () {
    [$org, $user] = createOrgAndAdmin();

    $content = LmsUserContent::create([
        'organization_id' => $org->id,
        'author_id' => $user->id,
        'title' => 'Pending Content',
        'content_type' => 'article',
        'status' => 'pending_review',
    ]);

    $response = $this->postJson("/api/lms/ugc/{$content->id}/approve");
    $response->assertOk();
    $this->assertDatabaseHas('lms_user_contents', ['id' => $content->id, 'status' => 'published']);
});

it('can reject UGC content', function () {
    [$org, $user] = createOrgAndAdmin();

    $content = LmsUserContent::create([
        'organization_id' => $org->id,
        'author_id' => $user->id,
        'title' => 'Bad Content',
        'content_type' => 'article',
        'status' => 'pending_review',
    ]);

    $response = $this->postJson("/api/lms/ugc/{$content->id}/reject", [
        'reason' => 'Not appropriate.',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('lms_user_contents', ['id' => $content->id, 'status' => 'rejected']);
});

it('can list published UGC', function () {
    [$org, $user] = createOrgAndAdmin();

    LmsUserContent::create([
        'organization_id' => $org->id,
        'author_id' => $user->id,
        'title' => 'Published Article',
        'content_type' => 'article',
        'status' => 'published',
    ]);

    $response = $this->getJson('/api/lms/ugc');
    $response->assertOk();
    $response->assertJsonFragment(['title' => 'Published Article']);
});

it('can toggle UGC like', function () {
    [$org, $user] = createOrgAndAdmin();

    $content = LmsUserContent::create([
        'organization_id' => $org->id,
        'author_id' => $user->id,
        'title' => 'Likeable',
        'content_type' => 'article',
        'status' => 'published',
    ]);

    $response = $this->postJson("/api/lms/ugc/{$content->id}/like");
    $response->assertOk();
    $response->assertJsonFragment(['liked' => true, 'likes_count' => 1]);

    $response = $this->postJson("/api/lms/ugc/{$content->id}/like");
    $response->assertOk();
    $response->assertJsonFragment(['liked' => false, 'likes_count' => 0]);
});

// ── Cohort Tests ─────────────────────────────────────────────────────────────

it('can create a cohort', function () {
    [$org, $user] = createOrgAndAdmin();

    $response = $this->postJson('/api/lms/cohorts', [
        'name' => 'Cohort Alpha',
        'description' => 'First cohort',
        'max_members' => 10,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_cohorts', [
        'name' => 'Cohort Alpha',
        'organization_id' => $org->id,
        'max_members' => 10,
    ]);
});

it('can list cohorts for organization', function () {
    [$org, $user] = createOrgAndAdmin();

    LmsCohort::create(['organization_id' => $org->id, 'name' => 'Test Cohort']);

    $response = $this->getJson('/api/lms/cohorts');
    $response->assertOk();
    $response->assertJsonFragment(['name' => 'Test Cohort']);
});

it('can add members to a cohort', function () {
    [$org, $user] = createOrgAndAdmin();
    $member = User::factory()->create(['current_organization_id' => $org->id]);

    $cohort = LmsCohort::create(['organization_id' => $org->id, 'name' => 'Member Cohort']);

    $response = $this->postJson("/api/lms/cohorts/{$cohort->id}/members", [
        'user_ids' => [$member->id],
        'role' => 'member',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_cohort_members', [
        'cohort_id' => $cohort->id,
        'user_id' => $member->id,
        'role' => 'member',
    ]);
});

it('can remove a member from a cohort', function () {
    [$org, $user] = createOrgAndAdmin();
    $member = User::factory()->create(['current_organization_id' => $org->id]);

    $cohort = LmsCohort::create(['organization_id' => $org->id, 'name' => 'Remove Cohort']);
    LmsCohortMember::create([
        'cohort_id' => $cohort->id,
        'user_id' => $member->id,
        'role' => 'member',
        'joined_at' => now(),
    ]);

    $response = $this->postJson("/api/lms/cohorts/{$cohort->id}/remove-member", [
        'user_id' => $member->id,
    ]);

    $response->assertOk();
    $this->assertDatabaseMissing('lms_cohort_members', [
        'cohort_id' => $cohort->id,
        'user_id' => $member->id,
    ]);
});

it('enforces max_members on cohort', function () {
    [$org, $user] = createOrgAndAdmin();

    $cohort = LmsCohort::create([
        'organization_id' => $org->id,
        'name' => 'Full Cohort',
        'max_members' => 1,
    ]);

    $m1 = User::factory()->create(['current_organization_id' => $org->id]);
    $m2 = User::factory()->create(['current_organization_id' => $org->id]);

    // First member should succeed
    $response = $this->postJson("/api/lms/cohorts/{$cohort->id}/members", [
        'user_ids' => [$m1->id],
    ]);
    $response->assertStatus(201);

    // Second member should fail (max_members = 1)
    $response = $this->postJson("/api/lms/cohorts/{$cohort->id}/members", [
        'user_ids' => [$m2->id],
    ]);
    $response->assertStatus(422);
});

it('can get cohort progress', function () {
    [$org, $user] = createOrgAndAdmin();

    $cohort = LmsCohort::create(['organization_id' => $org->id, 'name' => 'Progress Cohort']);
    LmsCohortMember::create([
        'cohort_id' => $cohort->id,
        'user_id' => $user->id,
        'role' => 'member',
        'joined_at' => now(),
        'completed_at' => now(),
    ]);

    $response = $this->getJson("/api/lms/cohorts/{$cohort->id}/progress");
    $response->assertOk();
    $response->assertJsonFragment(['total_members' => 1, 'completed' => 1, 'completion_rate' => 100.0]);
});

// ── Adaptive Learning Tests ──────────────────────────────────────────────────

it('can get or create a learner profile', function () {
    [$org, $user] = createOrgAndAdmin();

    $response = $this->getJson('/api/lms/adaptive/profile');
    $response->assertOk();
    $response->assertJsonFragment([
        'user_id' => $user->id,
        'proficiency_level' => 'beginner',
    ]);

    $this->assertDatabaseHas('lms_learner_profiles', [
        'user_id' => $user->id,
        'organization_id' => $org->id,
    ]);
});

it('can calibrate learner profile', function () {
    [$org, $user] = createOrgAndAdmin();

    $response = $this->postJson('/api/lms/adaptive/calibrate');
    $response->assertOk();
    $response->assertJsonFragment(['user_id' => $user->id]);

    $this->assertDatabaseHas('lms_learner_profiles', [
        'user_id' => $user->id,
        'organization_id' => $org->id,
    ]);
});

it('can create an adaptive rule', function () {
    [$org, $user] = createOrgAndAdmin();
    $course = LmsCourse::create(['title' => 'Adaptive Course', 'organization_id' => $org->id, 'is_active' => true]);

    $response = $this->postJson('/api/lms/adaptive/rules', [
        'course_id' => $course->id,
        'rule_name' => 'Low Score Remediation',
        'condition_type' => 'score_below',
        'condition_value' => '50',
        'action_type' => 'add_remedial',
        'action_config' => ['message' => 'Please review the material.'],
        'priority' => 10,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_adaptive_rules', [
        'rule_name' => 'Low Score Remediation',
        'course_id' => $course->id,
        'organization_id' => $org->id,
    ]);
});

it('can update an adaptive rule', function () {
    [$org, $user] = createOrgAndAdmin();
    $course = LmsCourse::create(['title' => 'Rule Course', 'organization_id' => $org->id, 'is_active' => true]);

    $rule = LmsAdaptiveRule::create([
        'organization_id' => $org->id,
        'course_id' => $course->id,
        'rule_name' => 'Old Rule',
        'condition_type' => 'score_below',
        'condition_value' => '40',
        'action_type' => 'add_remedial',
    ]);

    $response = $this->putJson("/api/lms/adaptive/rules/{$rule->id}", [
        'rule_name' => 'Updated Rule',
        'priority' => 5,
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('lms_adaptive_rules', ['id' => $rule->id, 'rule_name' => 'Updated Rule', 'priority' => 5]);
});

it('can get recommendations for a course', function () {
    [$org, $user] = createOrgAndAdmin();
    $course = LmsCourse::create(['title' => 'Rec Course', 'organization_id' => $org->id, 'is_active' => true]);

    $response = $this->getJson("/api/lms/adaptive/courses/{$course->id}/recommendations");
    $response->assertOk();
    $response->assertJsonStructure(['profile', 'recommended_actions', 'suggested_pace', 'proficiency']);
});

// ── Multi-tenant Isolation ───────────────────────────────────────────────────

it('enforces multi-tenant isolation for peer reviews', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    $user1 = User::factory()->admin()->create(['current_organization_id' => $org1->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org2->id]);
    $lesson = createLessonForOrg($org1->id);

    LmsPeerReview::create([
        'organization_id' => $org1->id,
        'assignment_id' => $lesson->id,
        'reviewer_id' => $user1->id,
        'reviewee_id' => $user1->id,
        'status' => 'pending_submission',
    ]);

    Sanctum::actingAs($user2, ['*']);
    $response = $this->getJson('/api/lms/peer-reviews');
    $response->assertOk();

    $ids = collect($response->json())->pluck('id')->all();
    expect($ids)->toBeEmpty();
});

it('enforces multi-tenant isolation for UGC', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    $user1 = User::factory()->admin()->create(['current_organization_id' => $org1->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org2->id]);

    LmsUserContent::create([
        'organization_id' => $org1->id,
        'author_id' => $user1->id,
        'title' => 'Org1 Content',
        'content_type' => 'article',
        'status' => 'published',
    ]);

    Sanctum::actingAs($user2, ['*']);
    $response = $this->getJson('/api/lms/ugc');
    $response->assertOk();

    $titles = collect($response->json('data'))->pluck('title')->all();
    expect($titles)->not->toContain('Org1 Content');
});

it('enforces multi-tenant isolation for cohorts', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    $user1 = User::factory()->admin()->create(['current_organization_id' => $org1->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org2->id]);

    LmsCohort::create(['organization_id' => $org1->id, 'name' => 'Org1 Cohort']);

    Sanctum::actingAs($user2, ['*']);
    $response = $this->getJson('/api/lms/cohorts');
    $response->assertOk();

    $names = collect($response->json())->pluck('name')->all();
    expect($names)->not->toContain('Org1 Cohort');
});

it('enforces multi-tenant isolation for adaptive profiles', function () {
    $org1 = Organization::factory()->create();
    $org2 = Organization::factory()->create();
    $user1 = User::factory()->admin()->create(['current_organization_id' => $org1->id]);
    $user2 = User::factory()->admin()->create(['current_organization_id' => $org2->id]);

    LmsLearnerProfile::create([
        'organization_id' => $org1->id,
        'user_id' => $user1->id,
        'proficiency_level' => 'expert',
    ]);

    Sanctum::actingAs($user2, ['*']);
    $response = $this->getJson('/api/lms/adaptive/profile');
    $response->assertOk();

    // User2 gets their own profile, not user1's
    $response->assertJsonFragment(['user_id' => $user2->id, 'proficiency_level' => 'beginner']);
});
