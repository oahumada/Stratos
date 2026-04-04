<?php

namespace App\Services\Lms;

use App\Models\LmsCmi5Session;
use App\Models\LmsScormPackage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Cmi5Service
{
    public function uploadCmi5Package(int $orgId, UploadedFile $file): LmsScormPackage
    {
        return DB::transaction(function () use ($orgId, $file) {
            $package = LmsScormPackage::create([
                'organization_id' => $orgId,
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'filename' => $file->getClientOriginalName(),
                'storage_path' => "scorm/{$orgId}/placeholder",
                'version' => 'cmi5',
                'status' => 'processing',
                'file_size_bytes' => $file->getSize(),
            ]);

            $storagePath = "scorm/{$orgId}/{$package->id}";
            $package->update(['storage_path' => $storagePath]);

            $zipPath = $file->storeAs($storagePath, $file->getClientOriginalName());
            $fullZipPath = Storage::path($zipPath);
            $extractPath = Storage::path($storagePath);

            $zip = new \ZipArchive;
            if ($zip->open($fullZipPath) !== true) {
                $package->update(['status' => 'error']);

                return $package;
            }

            $zip->extractTo($extractPath);
            $zip->close();

            // Parse cmi5.xml course structure
            $cmi5XmlPath = $extractPath.'/cmi5.xml';
            if (file_exists($cmi5XmlPath)) {
                $courseData = $this->parseCmi5Xml($cmi5XmlPath);
                $package->update([
                    'title' => $courseData['title'] ?: $package->title,
                    'identifier' => $courseData['id'] ?? null,
                    'entry_point' => $courseData['launch_url'] ?? null,
                    'manifest_data' => $courseData,
                    'status' => 'ready',
                ]);
            } else {
                $package->update(['status' => 'error']);
            }

            return $package->fresh();
        });
    }

    public function createSession(int $packageId, int $userId, int $orgId, string $launchMode = 'Normal'): LmsCmi5Session
    {
        $package = LmsScormPackage::forOrganization($orgId)->findOrFail($packageId);

        $session = LmsCmi5Session::create([
            'organization_id' => $orgId,
            'package_id' => $package->id,
            'registration_id' => (string) Str::uuid(),
            'session_id' => (string) Str::uuid(),
            'user_id' => $userId,
            'actor_json' => [
                'objectType' => 'Agent',
                'account' => [
                    'homePage' => config('app.url'),
                    'name' => (string) $userId,
                ],
            ],
            'launch_mode' => $launchMode,
            'launch_url' => $package->entry_point ?? '',
            'status' => 'launched',
        ]);

        return $session;
    }

    public function buildLaunchUrl(LmsCmi5Session $session): string
    {
        $baseUrl = $session->launch_url;
        $params = http_build_query([
            'fetch' => url("/api/lms/cmi5/sessions/{$session->id}/fetch"),
            'registration' => $session->registration_id,
            'activityId' => $session->package->identifier ?? 'activity-'.$session->package_id,
            'actor' => json_encode($session->actor_json),
            'endpoint' => url('/api/lms/xapi'),
        ]);

        $separator = str_contains($baseUrl, '?') ? '&' : '?';

        return $baseUrl.$separator.$params;
    }

    public function handleStatement(int $sessionId, array $statement, int $orgId): LmsCmi5Session
    {
        $session = LmsCmi5Session::forOrganization($orgId)->findOrFail($sessionId);

        $verb = $statement['verb']['id'] ?? '';
        $verbMap = [
            'http://adlnet.gov/expapi/verbs/initialized' => 'initialized',
            'http://adlnet.gov/expapi/verbs/completed' => 'completed',
            'http://adlnet.gov/expapi/verbs/passed' => 'passed',
            'http://adlnet.gov/expapi/verbs/failed' => 'failed',
            'http://adlnet.gov/expapi/verbs/terminated' => 'terminated',
        ];

        if (isset($verbMap[$verb])) {
            $session->update(['status' => $verbMap[$verb]]);
        }

        // Extract score if present
        if (isset($statement['result']['score']['scaled'])) {
            $session->update(['score_scaled' => (float) $statement['result']['score']['scaled']]);
        }

        if (isset($statement['result']['duration'])) {
            $session->update(['duration' => $statement['result']['duration']]);
        }

        $this->evaluateMoveOn($session->id, $orgId);

        return $session->fresh();
    }

    public function getAuthToken(int $sessionId, int $orgId): string
    {
        $session = LmsCmi5Session::forOrganization($orgId)->findOrFail($sessionId);

        return base64_encode("{$session->session_id}:{$session->registration_id}");
    }

    public function abandonStaleSession(int $sessionId, int $orgId): LmsCmi5Session
    {
        $session = LmsCmi5Session::forOrganization($orgId)->findOrFail($sessionId);

        if (! in_array($session->status, ['terminated', 'completed', 'passed', 'failed', 'abandoned'])) {
            $session->update(['status' => 'abandoned']);
        }

        return $session->fresh();
    }

    public function evaluateMoveOn(int $sessionId, int $orgId): bool
    {
        $session = LmsCmi5Session::forOrganization($orgId)->findOrFail($sessionId);

        $satisfied = match ($session->move_on) {
            'Passed' => $session->status === 'passed',
            'Completed' => $session->status === 'completed',
            'CompletedAndPassed' => in_array($session->status, ['completed', 'passed']),
            'CompletedOrPassed' => in_array($session->status, ['completed', 'passed']),
            'NotApplicable' => true,
            default => false,
        };

        if ($session->move_on === 'CompletedAndPassed') {
            $satisfied = $session->status === 'passed' || $session->status === 'completed';
            // For CompletedAndPassed, require both in session history
            $statuses = LmsCmi5Session::where('registration_id', $session->registration_id)
                ->where('organization_id', $orgId)
                ->pluck('status')
                ->toArray();
            $satisfied = in_array('completed', $statuses) && in_array('passed', $statuses);
        }

        if ($satisfied !== $session->satisfied) {
            $session->update(['satisfied' => $satisfied]);
        }

        return $satisfied;
    }

    private function parseCmi5Xml(string $path): array
    {
        $result = ['title' => '', 'id' => '', 'launch_url' => '', 'blocks' => []];

        try {
            $xml = simplexml_load_file($path);
            if ($xml === false) {
                return $result;
            }

            $result['id'] = (string) ($xml['id'] ?? '');

            // Try to get course title
            foreach ($xml->children() as $child) {
                if ($child->getName() === 'course') {
                    $result['id'] = (string) ($child['id'] ?? $result['id']);
                    foreach ($child->children() as $courseChild) {
                        if ($courseChild->getName() === 'title') {
                            foreach ($courseChild->children() as $langString) {
                                $result['title'] = (string) $langString;
                                break;
                            }
                        }
                        if ($courseChild->getName() === 'au') {
                            $result['launch_url'] = (string) ($courseChild['url'] ?? '');
                        }
                    }
                }
            }

            // Search for AU elements anywhere
            if (! $result['launch_url']) {
                $this->findAu($xml, $result);
            }
        } catch (\Throwable) {
        }

        return $result;
    }

    private function findAu(\SimpleXMLElement $element, array &$result): void
    {
        foreach ($element->children() as $child) {
            if ($child->getName() === 'au') {
                $result['launch_url'] = (string) ($child['url'] ?? '');

                return;
            }
            $this->findAu($child, $result);
        }
    }
}
