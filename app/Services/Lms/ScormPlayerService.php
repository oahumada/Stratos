<?php

namespace App\Services\Lms;

use App\Models\LmsEnrollment;
use App\Models\LmsScormPackage;
use App\Models\LmsScormTracking;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ScormPlayerService
{
    public function uploadPackage(
        UploadedFile $file,
        int $organizationId,
        ?int $courseId = null,
        ?int $lessonId = null,
    ): LmsScormPackage {
        return DB::transaction(function () use ($file, $organizationId, $courseId, $lessonId) {
            $package = LmsScormPackage::create([
                'organization_id' => $organizationId,
                'lms_course_id' => $courseId,
                'lms_lesson_id' => $lessonId,
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'filename' => $file->getClientOriginalName(),
                'storage_path' => "scorm/{$organizationId}/placeholder",
                'status' => 'processing',
                'file_size_bytes' => $file->getSize(),
            ]);

            $storagePath = "scorm/{$organizationId}/{$package->id}";
            $package->update(['storage_path' => $storagePath]);

            // Store the ZIP
            $zipPath = $file->storeAs($storagePath, $file->getClientOriginalName());

            // Extract ZIP
            $zip = new \ZipArchive;
            $fullZipPath = Storage::path($zipPath);
            $extractPath = Storage::path($storagePath);

            if ($zip->open($fullZipPath) !== true) {
                $package->update(['status' => 'error']);

                return $package;
            }

            $zip->extractTo($extractPath);
            $zip->close();

            // Parse manifest
            $manifestPath = $extractPath.'/imsmanifest.xml';
            if (file_exists($manifestPath)) {
                $manifestData = $this->parseManifest($manifestPath);
                $package->update([
                    'title' => $manifestData['title'] ?: $package->title,
                    'identifier' => $manifestData['identifier'],
                    'entry_point' => $manifestData['entry_point'],
                    'manifest_data' => $manifestData,
                    'status' => 'ready',
                ]);
            } else {
                $package->update(['status' => 'error']);
            }

            return $package->fresh();
        });
    }

    public function getOrCreateTracking(int $packageId, int $userId, int $organizationId): LmsScormTracking
    {
        $package = LmsScormPackage::forOrganization($organizationId)->findOrFail($packageId);

        $tracking = LmsScormTracking::firstOrCreate(
            [
                'lms_scorm_package_id' => $package->id,
                'user_id' => $userId,
            ],
            [
                'organization_id' => $organizationId,
                'lesson_status' => 'not attempted',
                'total_time' => '0000:00:00',
                'session_count' => 0,
            ]
        );

        // Link enrollment if available
        if (! $tracking->lms_enrollment_id && $package->lms_course_id) {
            $enrollment = LmsEnrollment::where('lms_course_id', $package->lms_course_id)
                ->where('user_id', $userId)
                ->first();

            if ($enrollment) {
                $tracking->update(['lms_enrollment_id' => $enrollment->id]);
            }
        }

        return $tracking;
    }

    public function saveCmiData(int $trackingId, array $cmiData, int $organizationId): LmsScormTracking
    {
        $tracking = LmsScormTracking::where('id', $trackingId)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $updates = ['cmi_data' => $cmiData];

        if (isset($cmiData['cmi.core.lesson_status'])) {
            $updates['lesson_status'] = $cmiData['cmi.core.lesson_status'];
        }

        if (isset($cmiData['cmi.core.score.raw'])) {
            $updates['score_raw'] = (float) $cmiData['cmi.core.score.raw'];
        }

        if (isset($cmiData['cmi.core.score.min'])) {
            $updates['score_min'] = (float) $cmiData['cmi.core.score.min'];
        }

        if (isset($cmiData['cmi.core.score.max'])) {
            $updates['score_max'] = (float) $cmiData['cmi.core.score.max'];
        }

        if (isset($cmiData['cmi.suspend_data'])) {
            $updates['suspend_data'] = $cmiData['cmi.suspend_data'];
        }

        if (isset($cmiData['cmi.core.lesson_location'])) {
            $updates['lesson_location'] = $cmiData['cmi.core.lesson_location'];
        }

        if (isset($cmiData['cmi.core.session_time'])) {
            $tracking->addSessionTime($cmiData['cmi.core.session_time']);
            $updates['total_time'] = $tracking->total_time;
            $updates['session_count'] = $tracking->session_count + 1;
        }

        $tracking->update($updates);
        $tracking->refresh();

        // Mark completed_at and update linked enrollment
        if ($tracking->isCompleted() && ! $tracking->completed_at) {
            $tracking->update(['completed_at' => now()]);

            if ($tracking->lms_enrollment_id) {
                $enrollment = LmsEnrollment::find($tracking->lms_enrollment_id);
                if ($enrollment) {
                    $enrollmentUpdates = ['status' => 'completed', 'completed_at' => now()];
                    if ($tracking->score_raw !== null) {
                        $enrollmentUpdates['assessment_score'] = $tracking->score_raw;
                    }
                    $enrollmentUpdates['progress_percentage'] = 100;
                    $enrollment->update($enrollmentUpdates);
                }
            }
        }

        return $tracking;
    }

    public function deletePackage(int $packageId, int $organizationId): void
    {
        $package = LmsScormPackage::forOrganization($organizationId)->findOrFail($packageId);

        Storage::deleteDirectory($package->storage_path);

        $package->delete();
    }

    public function getLaunchData(int $packageId, int $userId, int $organizationId): array
    {
        $package = LmsScormPackage::forOrganization($organizationId)->ready()->findOrFail($packageId);
        $tracking = $this->getOrCreateTracking($packageId, $userId, $organizationId);

        return [
            'package' => [
                'id' => $package->id,
                'title' => $package->title,
                'version' => $package->version,
                'entry_point' => $package->entry_point,
                'identifier' => $package->identifier,
                'status' => $package->status,
            ],
            'tracking' => [
                'id' => $tracking->id,
                'lesson_status' => $tracking->lesson_status,
                'score_raw' => $tracking->score_raw,
                'score_min' => $tracking->score_min,
                'score_max' => $tracking->score_max,
                'total_time' => $tracking->total_time,
                'session_count' => $tracking->session_count,
                'suspend_data' => $tracking->suspend_data,
                'lesson_location' => $tracking->lesson_location,
                'cmi_data' => $tracking->cmi_data,
                'completed_at' => $tracking->completed_at?->toIso8601String(),
            ],
            'launch_url' => $package->getLaunchUrl(),
        ];
    }

    private function parseManifest(string $manifestPath): array
    {
        $result = [
            'title' => '',
            'identifier' => '',
            'entry_point' => '',
            'organizations' => [],
            'resources' => [],
        ];

        try {
            $xml = simplexml_load_file($manifestPath);
            if ($xml === false) {
                return $result;
            }

            // Register namespaces
            $namespaces = $xml->getNamespaces(true);
            $ns = $namespaces[''] ?? '';

            // Get identifier from manifest root
            $result['identifier'] = (string) ($xml['identifier'] ?? '');

            // Parse organizations
            if ($ns) {
                $xml->registerXPathNamespace('imscp', $ns);
                $titles = $xml->xpath('//imscp:organization/imscp:title');
                if (! empty($titles)) {
                    $result['title'] = (string) $titles[0];
                }

                // Parse organizations structure
                $orgs = $xml->xpath('//imscp:organization');
                foreach ($orgs as $org) {
                    $result['organizations'][] = [
                        'identifier' => (string) ($org['identifier'] ?? ''),
                        'title' => (string) ($org->children($ns)->title ?? ''),
                    ];
                }

                // Parse resources and find SCO entry point
                if (isset($namespaces['adlcp'])) {
                    $xml->registerXPathNamespace('adlcp', $namespaces['adlcp']);
                }

                $resources = $xml->xpath('//imscp:resource');
                foreach ($resources as $resource) {
                    $attrs = $resource->attributes();
                    $adlcpAttrs = isset($namespaces['adlcp']) ? $resource->attributes($namespaces['adlcp']) : null;
                    $scormType = $adlcpAttrs ? (string) ($adlcpAttrs['scormtype'] ?? '') : '';

                    $res = [
                        'identifier' => (string) ($attrs['identifier'] ?? ''),
                        'type' => (string) ($attrs['type'] ?? ''),
                        'href' => (string) ($attrs['href'] ?? ''),
                        'scormtype' => $scormType,
                    ];

                    $result['resources'][] = $res;

                    if (strtolower($scormType) === 'sco' && ! $result['entry_point'] && ! empty($res['href'])) {
                        $result['entry_point'] = $res['href'];
                    }
                }
            }

            // Fallback: try without namespace
            if (! $result['entry_point']) {
                foreach ($xml->resources->resource ?? [] as $resource) {
                    $href = (string) ($resource['href'] ?? '');
                    if ($href) {
                        $result['entry_point'] = $href;
                        break;
                    }
                }
            }

            if (! $result['title']) {
                foreach ($xml->organizations->organization ?? [] as $org) {
                    $title = (string) ($org->title ?? '');
                    if ($title) {
                        $result['title'] = $title;
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fault-tolerant: return defaults on any parse error
        }

        return $result;
    }
}
