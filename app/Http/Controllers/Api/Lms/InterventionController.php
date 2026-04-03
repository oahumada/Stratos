<?php

namespace App\Http\Controllers\Api\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLmsInterventionRequest;
use App\Http\Requests\UpdateLmsPreferencesRequest;
use App\Models\LmsEnrollment;
use App\Models\LmsIntervention;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    private const ORG_RESOLUTION_ERROR = 'No se pudo resolver organization_id.';

    public function index(Request $request): JsonResponse
    {
        $organizationId = (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $started = LmsIntervention::query()
            ->where('organization_id', $organizationId)
            ->where('status', 'started')
            ->pluck('lms_enrollment_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $statuses = LmsIntervention::query()
            ->where('organization_id', $organizationId)
            ->whereIn('status', ['started', 'completed'])
            ->get(['lms_enrollment_id', 'status'])
            ->mapWithKeys(fn (LmsIntervention $intervention) => [
                (string) $intervention->lms_enrollment_id => $intervention->status,
            ])
            ->all();

        return response()->json([
            'data' => [
                'started_enrollment_ids' => $started,
                'statuses_by_enrollment' => $statuses,
            ],
        ]);
    }

    public function store(StoreLmsInterventionRequest $request): JsonResponse
    {
        $organizationId = (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $data = $request->validated();

        $enrollment = LmsEnrollment::query()
            ->whereKey((int) $data['lms_enrollment_id'])
            ->whereHas('course', function ($query) use ($organizationId): void {
                $query->where('organization_id', $organizationId);
            })
            ->first();

        if (! $enrollment) {
            return response()->json(['message' => 'Enrollment no pertenece a la organización actual.'], 403);
        }

        $intervention = LmsIntervention::query()->updateOrCreate(
            [
                'organization_id' => $organizationId,
                'lms_enrollment_id' => (int) $data['lms_enrollment_id'],
            ],
            [
                'lms_course_id' => $data['lms_course_id'] ?? $enrollment->lms_course_id,
                'user_id' => $data['user_id'] ?? $enrollment->user_id,
                'status' => 'started',
                'source' => $data['source'] ?? 'lms_dashboard',
                'metadata' => $data['metadata'] ?? null,
                'started_at' => now(),
                'cleared_at' => null,
            ],
        );

        return response()->json([
            'data' => [
                'id' => $intervention->id,
                'lms_enrollment_id' => (int) $intervention->lms_enrollment_id,
                'status' => $intervention->status,
            ],
        ], 201);
    }

    public function reset(Request $request): JsonResponse
    {
        $organizationId = (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $updated = LmsIntervention::query()
            ->where('organization_id', $organizationId)
            ->whereIn('status', ['started', 'completed'])
            ->update([
                'status' => 'cleared',
                'cleared_at' => now(),
            ]);

        return response()->json([
            'data' => [
                'cleared' => $updated,
            ],
        ]);
    }

    public function complete(Request $request, int $enrollmentId): JsonResponse
    {
        $organizationId = (int) (($request->user()?->current_organization_id ?? $request->user()?->organization_id) ?? 0);

        if ($organizationId <= 0) {
            return response()->json(['message' => self::ORG_RESOLUTION_ERROR], 422);
        }

        $intervention = LmsIntervention::query()
            ->where('organization_id', $organizationId)
            ->where('lms_enrollment_id', $enrollmentId)
            ->whereIn('status', ['started', 'completed'])
            ->first();

        if (! $intervention) {
            return response()->json(['message' => 'Intervención no encontrada para esta organización.'], 404);
        }

        $intervention->status = 'completed';
        $intervention->cleared_at = now();
        $intervention->save();

        return response()->json([
            'data' => [
                'id' => $intervention->id,
                'lms_enrollment_id' => (int) $intervention->lms_enrollment_id,
                'status' => $intervention->status,
            ],
        ]);
    }

    public function preferences(Request $request): JsonResponse
    {
        $preferences = (array) ($request->user()?->ui_preferences ?? []);

        return response()->json([
            'data' => [
                'show_completed_interventions' => (bool) ($preferences['lms']['show_completed_interventions'] ?? false),
            ],
        ]);
    }

    public function updatePreferences(UpdateLmsPreferencesRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $preferences = (array) ($user?->ui_preferences ?? []);

        $preferences['lms'] = array_merge(
            (array) ($preferences['lms'] ?? []),
            [
                'show_completed_interventions' => (bool) $validated['show_completed_interventions'],
            ],
        );

        $user?->update([
            'ui_preferences' => $preferences,
        ]);

        return response()->json([
            'data' => [
                'show_completed_interventions' => (bool) ($preferences['lms']['show_completed_interventions'] ?? false),
            ],
        ]);
    }
}
