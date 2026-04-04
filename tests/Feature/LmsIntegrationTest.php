<?php

use App\Models\LmsCalendarEvent;
use App\Models\LmsCourse;
use App\Models\LmsLtiPlatform;
use App\Models\LmsMarketplaceListing;
use App\Models\LmsMarketplacePurchase;
use App\Models\LmsWebhook;
use App\Models\LmsWebhookLog;
use App\Models\Organization;
use App\Models\User;
use App\Services\Lms\CalendarService;
use App\Services\Lms\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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

// ── G8.1 Webhooks ──

it('can list webhooks for organization', function () {
    LmsWebhook::create([
        'organization_id' => $this->org->id,
        'url' => 'https://example.com/hook',
        'secret' => 'secret1234',
        'events' => ['course.completed'],
    ]);

    $response = $this->getJson('/api/lms/webhooks');
    $response->assertOk();
    $response->assertJsonCount(1);
});

it('can create a webhook', function () {
    $response = $this->postJson('/api/lms/webhooks', [
        'url' => 'https://example.com/hook',
        'secret' => 'secret1234',
        'events' => ['enrollment.completed', 'course.completed'],
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_webhooks', [
        'organization_id' => $this->org->id,
        'url' => 'https://example.com/hook',
    ]);
});

it('can update a webhook', function () {
    $webhook = LmsWebhook::create([
        'organization_id' => $this->org->id,
        'url' => 'https://example.com/hook',
        'secret' => 'secret1234',
        'events' => ['course.completed'],
    ]);

    $response = $this->putJson("/api/lms/webhooks/{$webhook->id}", [
        'url' => 'https://example.com/updated',
    ]);

    $response->assertOk();
    $this->assertDatabaseHas('lms_webhooks', ['url' => 'https://example.com/updated']);
});

it('can delete a webhook', function () {
    $webhook = LmsWebhook::create([
        'organization_id' => $this->org->id,
        'url' => 'https://example.com/hook',
        'secret' => 'secret1234',
        'events' => ['course.completed'],
    ]);

    $response = $this->deleteJson("/api/lms/webhooks/{$webhook->id}");
    $response->assertOk();
    $this->assertSoftDeleted('lms_webhooks', ['id' => $webhook->id]);
});

it('can test a webhook', function () {
    Http::fake(['https://example.com/hook' => Http::response('ok', 200)]);

    $webhook = LmsWebhook::create([
        'organization_id' => $this->org->id,
        'url' => 'https://example.com/hook',
        'secret' => 'secret1234',
        'events' => ['test.ping'],
    ]);

    $response = $this->postJson("/api/lms/webhooks/{$webhook->id}/test");
    $response->assertOk();
    $this->assertDatabaseHas('lms_webhook_logs', [
        'webhook_id' => $webhook->id,
        'event' => 'test.ping',
        'status' => 'success',
    ]);
});

it('deactivates webhook after 5 consecutive failures', function () {
    $webhook = LmsWebhook::create([
        'organization_id' => $this->org->id,
        'url' => 'https://example.com/hook',
        'secret' => 'secret1234',
        'events' => ['course.completed'],
        'failure_count' => 4,
    ]);

    $service = app(WebhookService::class);
    $service->deactivateOnFailure($webhook->id);

    $webhook->refresh();
    expect($webhook->is_active)->toBeFalse();
    expect($webhook->failure_count)->toBe(5);
});

// ── G8.2 LTI 1.3 ──

it('can register an LTI platform', function () {
    $response = $this->postJson('/api/lms/lti/platforms', [
        'name' => 'Canvas LMS',
        'client_id' => 'client-abc',
        'deployment_id' => 'deploy-123',
        'platform_url' => 'https://canvas.example.com',
        'auth_url' => 'https://canvas.example.com/auth',
        'token_url' => 'https://canvas.example.com/token',
        'jwks_url' => 'https://canvas.example.com/jwks',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_lti_platforms', [
        'organization_id' => $this->org->id,
        'client_id' => 'client-abc',
    ]);
});

it('can list LTI platforms', function () {
    LmsLtiPlatform::create([
        'organization_id' => $this->org->id,
        'name' => 'Canvas',
        'client_id' => 'client-abc',
        'deployment_id' => 'deploy-123',
        'platform_url' => 'https://canvas.example.com',
        'auth_url' => 'https://canvas.example.com/auth',
        'token_url' => 'https://canvas.example.com/token',
        'jwks_url' => 'https://canvas.example.com/jwks',
    ]);

    $response = $this->getJson('/api/lms/lti/platforms');
    $response->assertOk();
    $response->assertJsonCount(1);
});

it('can validate LTI launch with valid client_id', function () {
    LmsLtiPlatform::create([
        'organization_id' => $this->org->id,
        'name' => 'Canvas',
        'client_id' => 'valid-client',
        'deployment_id' => 'deploy-123',
        'platform_url' => 'https://canvas.example.com',
        'auth_url' => 'https://canvas.example.com/auth',
        'token_url' => 'https://canvas.example.com/token',
        'jwks_url' => 'https://canvas.example.com/jwks',
    ]);

    $response = $this->postJson('/api/lms/lti/launch', [
        'client_id' => 'valid-client',
        'course_id' => 1,
    ]);

    $response->assertOk();
    $response->assertJsonPath('platform', 'Canvas');
});

it('rejects LTI launch with invalid client_id', function () {
    $response = $this->postJson('/api/lms/lti/launch', [
        'client_id' => 'nonexistent-client',
        'course_id' => 1,
    ]);

    $response->assertStatus(401);
});

// ── G8.6 Calendar ──

it('can create a calendar event', function () {
    $response = $this->postJson('/api/lms/calendar', [
        'title' => 'Exam deadline',
        'event_type' => 'quiz_deadline',
        'starts_at' => '2026-05-01 09:00:00',
        'ends_at' => '2026-05-01 10:00:00',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_calendar_events', [
        'user_id' => $this->user->id,
        'title' => 'Exam deadline',
    ]);
});

it('can list calendar events for current user', function () {
    LmsCalendarEvent::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
        'title' => 'My Event',
        'event_type' => 'session',
        'starts_at' => '2026-05-01 09:00:00',
        'ends_at' => '2026-05-01 10:00:00',
    ]);

    $response = $this->getJson('/api/lms/calendar');
    $response->assertOk();
    $response->assertJsonCount(1);
});

it('can delete a calendar event', function () {
    $event = LmsCalendarEvent::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
        'title' => 'My Event',
        'event_type' => 'session',
        'starts_at' => '2026-05-01 09:00:00',
        'ends_at' => '2026-05-01 10:00:00',
    ]);

    $response = $this->deleteJson("/api/lms/calendar/{$event->id}");
    $response->assertOk();
    $this->assertDatabaseMissing('lms_calendar_events', ['id' => $event->id]);
});

it('generates iCal output', function () {
    LmsCalendarEvent::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
        'title' => 'iCal Event',
        'event_type' => 'session',
        'starts_at' => '2026-05-01 09:00:00',
        'ends_at' => '2026-05-01 10:00:00',
    ]);

    $response = $this->get('/api/lms/calendar/ical');
    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    expect($response->getContent())->toContain('BEGIN:VCALENDAR');
    expect($response->getContent())->toContain('iCal Event');
});

// ── G8.7-G8.9 Marketplace ──

it('can create a marketplace listing', function () {
    $course = LmsCourse::create([
        'title' => 'Test Course',
        'organization_id' => $this->org->id,
    ]);

    $response = $this->postJson('/api/lms/marketplace', [
        'course_id' => $course->id,
        'title' => 'Amazing Course',
        'description' => 'Learn everything',
        'price' => 29.99,
        'listing_type' => 'paid',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('lms_marketplace_listings', [
        'title' => 'Amazing Course',
        'organization_id' => $this->org->id,
    ]);
});

it('can publish a listing', function () {
    $course = LmsCourse::create([
        'title' => 'Test Course',
        'organization_id' => $this->org->id,
    ]);

    $listing = LmsMarketplaceListing::create([
        'organization_id' => $this->org->id,
        'course_id' => $course->id,
        'title' => 'Draft Course',
        'description' => 'Not yet visible',
        'listing_type' => 'free',
    ]);

    $response = $this->postJson("/api/lms/marketplace/{$listing->id}/publish");
    $response->assertOk();

    $listing->refresh();
    expect($listing->is_published)->toBeTrue();
});

it('can browse published marketplace listings', function () {
    $course = LmsCourse::create([
        'title' => 'Test Course',
        'organization_id' => $this->org->id,
    ]);

    LmsMarketplaceListing::create([
        'organization_id' => $this->org->id,
        'course_id' => $course->id,
        'title' => 'Published Course',
        'description' => 'Visible',
        'listing_type' => 'free',
        'is_published' => true,
    ]);

    LmsMarketplaceListing::create([
        'organization_id' => $this->org->id,
        'course_id' => $course->id,
        'title' => 'Draft Course',
        'description' => 'Hidden',
        'listing_type' => 'free',
        'is_published' => false,
    ]);

    $response = $this->getJson('/api/lms/marketplace');
    $response->assertOk();
    // Only published listings returned
    $data = $response->json('data');
    expect(count($data))->toBe(1);
    expect($data[0]['title'])->toBe('Published Course');
});

it('can purchase a listing and clone course', function () {
    $sellerOrg = Organization::factory()->create();
    $course = LmsCourse::create([
        'title' => 'Seller Course',
        'description' => 'Great content',
        'organization_id' => $sellerOrg->id,
    ]);

    $listing = LmsMarketplaceListing::create([
        'organization_id' => $sellerOrg->id,
        'course_id' => $course->id,
        'title' => 'For Sale',
        'description' => 'Buy me',
        'listing_type' => 'free',
        'is_published' => true,
    ]);

    $response = $this->postJson("/api/lms/marketplace/{$listing->id}/purchase");
    $response->assertStatus(201);

    $this->assertDatabaseHas('lms_marketplace_purchases', [
        'listing_id' => $listing->id,
        'buyer_organization_id' => $this->org->id,
        'status' => 'completed',
    ]);

    // Course cloned to buyer org
    $this->assertDatabaseHas('lms_courses', [
        'organization_id' => $this->org->id,
        'title' => 'Seller Course',
        'is_active' => false,
    ]);
});

it('can list seller listings', function () {
    $course = LmsCourse::create([
        'title' => 'My Course',
        'organization_id' => $this->org->id,
    ]);

    LmsMarketplaceListing::create([
        'organization_id' => $this->org->id,
        'course_id' => $course->id,
        'title' => 'My Listing',
        'description' => 'My description',
        'listing_type' => 'free',
    ]);

    $response = $this->getJson('/api/lms/marketplace/my-listings');
    $response->assertOk();
    $response->assertJsonCount(1);
});

it('can list buyer purchases', function () {
    $sellerOrg = Organization::factory()->create();
    $course = LmsCourse::create(['title' => 'Course', 'organization_id' => $sellerOrg->id]);

    $listing = LmsMarketplaceListing::create([
        'organization_id' => $sellerOrg->id,
        'course_id' => $course->id,
        'title' => 'Listed',
        'description' => 'Listed desc',
        'listing_type' => 'free',
        'is_published' => true,
    ]);

    LmsMarketplacePurchase::create([
        'listing_id' => $listing->id,
        'buyer_organization_id' => $this->org->id,
        'purchased_by' => $this->user->id,
        'price_paid' => 0,
        'status' => 'completed',
    ]);

    $response = $this->getJson('/api/lms/marketplace/purchases');
    $response->assertOk();
    $response->assertJsonCount(1);
});

// ── Multi-tenant isolation ──

it('isolates webhooks by organization', function () {
    $otherOrg = Organization::factory()->create();
    LmsWebhook::create([
        'organization_id' => $otherOrg->id,
        'url' => 'https://other-org.com/hook',
        'secret' => 'secret1234',
        'events' => ['course.completed'],
    ]);

    $response = $this->getJson('/api/lms/webhooks');
    $response->assertOk();
    $response->assertJsonCount(0);
});

it('isolates LTI platforms by organization', function () {
    $otherOrg = Organization::factory()->create();
    LmsLtiPlatform::create([
        'organization_id' => $otherOrg->id,
        'name' => 'Other Canvas',
        'client_id' => 'other-client',
        'deployment_id' => 'other-deploy',
        'platform_url' => 'https://other.example.com',
        'auth_url' => 'https://other.example.com/auth',
        'token_url' => 'https://other.example.com/token',
        'jwks_url' => 'https://other.example.com/jwks',
    ]);

    $response = $this->getJson('/api/lms/lti/platforms');
    $response->assertOk();
    $response->assertJsonCount(0);
});

it('isolates calendar events by organization', function () {
    $otherOrg = Organization::factory()->create();
    $otherUser = User::factory()->create([
        'organization_id' => $otherOrg->id,
        'current_organization_id' => $otherOrg->id,
    ]);

    LmsCalendarEvent::create([
        'organization_id' => $otherOrg->id,
        'user_id' => $otherUser->id,
        'title' => 'Other Org Event',
        'event_type' => 'session',
        'starts_at' => '2026-05-01 09:00:00',
        'ends_at' => '2026-05-01 10:00:00',
    ]);

    $response = $this->getJson('/api/lms/calendar');
    $response->assertOk();
    $response->assertJsonCount(0);
});

it('isolates marketplace seller listings by organization', function () {
    $otherOrg = Organization::factory()->create();
    $course = LmsCourse::create(['title' => 'Other Course', 'organization_id' => $otherOrg->id]);

    LmsMarketplaceListing::create([
        'organization_id' => $otherOrg->id,
        'course_id' => $course->id,
        'title' => 'Other Listing',
        'description' => 'Other description',
        'listing_type' => 'free',
    ]);

    $response = $this->getJson('/api/lms/marketplace/my-listings');
    $response->assertOk();
    $response->assertJsonCount(0);
});
