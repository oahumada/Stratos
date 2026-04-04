<?php

use App\Models\LmsScormPackage;
use App\Models\LmsScormTracking;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

function createScormZip(): string
{
    $zip = new \ZipArchive;
    $zipPath = tempnam(sys_get_temp_dir(), 'scorm').'.zip';
    $zip->open($zipPath, \ZipArchive::CREATE);
    $zip->addFromString('imsmanifest.xml', '<?xml version="1.0"?>
<manifest identifier="test-pkg" xmlns="http://www.imsproject.org/xsd/imscp_rootv1p1p2" xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_rootv1p2">
  <organizations default="org1">
    <organization identifier="org1"><title>Test SCORM Course</title>
      <item identifier="item1" identifierref="res1"><title>Lesson 1</title></item>
    </organization>
  </organizations>
  <resources>
    <resource identifier="res1" type="webcontent" adlcp:scormtype="sco" href="index.html">
      <file href="index.html"/>
    </resource>
  </resources>
</manifest>');
    $zip->addFromString('index.html', '<html><body><h1>Test</h1></body></html>');
    $zip->close();

    return $zipPath;
}

function createAuthenticatedUser(?Organization $org = null): array
{
    $org = $org ?? Organization::factory()->create();
    $user = User::factory()->admin()->create([
        'organization_id' => $org->id,
        'current_organization_id' => $org->id,
    ]);
    Sanctum::actingAs($user, ['*']);

    return [$org, $user];
}

beforeEach(function () {
    Storage::fake('local');
});

it('can upload a SCORM package', function () {
    [$org, $user] = createAuthenticatedUser();

    $zipPath = createScormZip();
    $file = new UploadedFile($zipPath, 'test-course.zip', 'application/zip', null, true);

    $response = $this->postJson('/api/lms/scorm/upload', [
        'file' => $file,
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);
    $response->assertJsonPath('data.status', 'ready');
    $response->assertJsonPath('data.entry_point', 'index.html');
    $response->assertJsonPath('data.identifier', 'test-pkg');
    $response->assertJsonPath('data.organization_id', $org->id);

    // Check title was parsed from manifest
    expect($response->json('data.title'))->toBe('Test SCORM Course');

    // Verify DB record
    expect(LmsScormPackage::count())->toBe(1);
});

it('can upload a SCORM package with custom title', function () {
    [$org, $user] = createAuthenticatedUser();

    $zipPath = createScormZip();
    $file = new UploadedFile($zipPath, 'test-course.zip', 'application/zip', null, true);

    $response = $this->postJson('/api/lms/scorm/upload', [
        'file' => $file,
        'title' => 'My Custom Title',
    ]);

    $response->assertStatus(201);
    expect($response->json('data.title'))->toBe('My Custom Title');
});

it('can list packages for organization', function () {
    [$org, $user] = createAuthenticatedUser();

    // Create packages
    LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'Package 1',
        'filename' => 'pkg1.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
    ]);
    LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'Package 2',
        'filename' => 'pkg2.zip',
        'storage_path' => "scorm/{$org->id}/2",
        'status' => 'ready',
        'entry_point' => 'index.html',
    ]);

    $response = $this->getJson('/api/lms/scorm/packages');

    $response->assertOk();
    expect(count($response->json('data')))->toBe(2);
});

it('can get launch data for a package', function () {
    [$org, $user] = createAuthenticatedUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'Test Package',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
        'identifier' => 'test-pkg',
        'version' => '1.2',
    ]);

    $response = $this->getJson("/api/lms/scorm/{$package->id}/launch");

    $response->assertOk();
    $response->assertJsonStructure([
        'data' => [
            'package' => ['id', 'title', 'version', 'entry_point', 'identifier', 'status'],
            'tracking' => ['id', 'lesson_status', 'total_time', 'session_count'],
            'launch_url',
        ],
    ]);
    expect($response->json('data.tracking.lesson_status'))->toBe('not attempted');
    expect($response->json('data.launch_url'))->toContain('/storage/scorm/');

    // Verify tracking was created
    expect(LmsScormTracking::count())->toBe(1);
});

it('can save CMI data and updates tracking', function () {
    [$org, $user] = createAuthenticatedUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'Test Package',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
    ]);

    // Get launch data first (creates tracking)
    $this->getJson("/api/lms/scorm/{$package->id}/launch");

    $response = $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.core.lesson_status' => 'incomplete',
            'cmi.core.score.raw' => '75',
            'cmi.core.score.min' => '0',
            'cmi.core.score.max' => '100',
            'cmi.core.session_time' => '00:15:30',
            'cmi.suspend_data' => 'page=3',
            'cmi.core.lesson_location' => 'module2/page3',
        ],
    ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);

    $tracking = LmsScormTracking::first();
    expect($tracking->lesson_status)->toBe('incomplete');
    expect($tracking->score_raw)->toBe(75.0);
    expect($tracking->score_min)->toBe(0.0);
    expect($tracking->score_max)->toBe(100.0);
    expect($tracking->suspend_data)->toBe('page=3');
    expect($tracking->lesson_location)->toBe('module2/page3');
    expect($tracking->session_count)->toBe(1);
    expect($tracking->total_time)->toBe('0000:15:30');
});

it('lesson_status completed updates linked enrollment', function () {
    [$org, $user] = createAuthenticatedUser();

    $course = LmsCourse::create([
        'organization_id' => $org->id,
        'title' => 'Test Course',
        'is_active' => true,
    ]);

    $enrollment = LmsEnrollment::create([
        'lms_course_id' => $course->id,
        'user_id' => $user->id,
        'status' => 'in_progress',
        'progress_percentage' => 50,
    ]);

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'lms_course_id' => $course->id,
        'title' => 'Test Package',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
    ]);

    // Get launch data (creates tracking with enrollment link)
    $this->getJson("/api/lms/scorm/{$package->id}/launch");

    // Save CMI data with completed status
    $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.core.lesson_status' => 'completed',
            'cmi.core.score.raw' => '90',
        ],
    ]);

    $enrollment->refresh();
    expect($enrollment->status)->toBe('completed');
    expect($enrollment->progress_percentage)->toBe(100.0);
    expect($enrollment->assessment_score)->toBe(90.0);
    expect($enrollment->completed_at)->not->toBeNull();
});

it('session time accumulates correctly', function () {
    [$org, $user] = createAuthenticatedUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'Test Package',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
    ]);

    // Get launch data
    $this->getJson("/api/lms/scorm/{$package->id}/launch");

    // First session
    $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.core.lesson_status' => 'incomplete',
            'cmi.core.session_time' => '00:10:00',
        ],
    ]);

    // Second session
    $this->postJson("/api/lms/scorm/{$package->id}/cmi", [
        'cmi_data' => [
            'cmi.core.lesson_status' => 'incomplete',
            'cmi.core.session_time' => '00:25:30',
        ],
    ]);

    $tracking = LmsScormTracking::first();
    expect($tracking->total_time)->toBe('0000:35:30');
    expect($tracking->session_count)->toBe(2);
});

it('cannot access package from another organization', function () {
    [$org1, $user1] = createAuthenticatedUser();

    $org2 = Organization::factory()->create();
    $package = LmsScormPackage::create([
        'organization_id' => $org2->id,
        'title' => 'Other Org Package',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org2->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
    ]);

    $response = $this->getJson("/api/lms/scorm/{$package->id}/launch");
    $response->assertStatus(404);
});

it('unauthenticated returns 401', function () {
    $response = $this->getJson('/api/lms/scorm/packages');
    $response->assertStatus(401);
});

it('can delete a package', function () {
    [$org, $user] = createAuthenticatedUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'Test Package',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
    ]);

    // Create storage directory
    Storage::makeDirectory($package->storage_path);
    Storage::put("{$package->storage_path}/index.html", '<html></html>');

    $response = $this->deleteJson("/api/lms/scorm/{$package->id}");

    $response->assertOk();
    $response->assertJsonPath('success', true);
    expect(LmsScormPackage::count())->toBe(0);
    Storage::assertMissing("{$package->storage_path}/index.html");
});

it('can get current tracking data', function () {
    [$org, $user] = createAuthenticatedUser();

    $package = LmsScormPackage::create([
        'organization_id' => $org->id,
        'title' => 'Test Package',
        'filename' => 'test.zip',
        'storage_path' => "scorm/{$org->id}/1",
        'status' => 'ready',
        'entry_point' => 'index.html',
    ]);

    $response = $this->getJson("/api/lms/scorm/{$package->id}/tracking");

    $response->assertOk();
    $response->assertJsonStructure(['data' => ['id', 'lesson_status', 'total_time']]);
    expect($response->json('data.lesson_status'))->toBe('not attempted');
});
