<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Services\MatchingService;
use Illuminate\Http\JsonResponse;

class JobOpeningController extends Controller
{
    public function index(): JsonResponse
    {
        $openings = JobOpening::with('role', 'createdBy')
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'title' => $o->title,
                'role' => $o->role?->name,
                'department' => $o->department,
                'status' => $o->status,
                'deadline' => $o->deadline,
                'applications_count' => $o->applications()->count(),
            ]);

        return response()->json(['data' => $openings]);
    }

    public function show(int $id): JsonResponse
    {
        $opening = JobOpening::with('role', 'createdBy', 'applications')->find($id);
        if (! $opening) {
            return response()->json(['error' => 'Vacante no encontrada'], 404);
        }

        return response()->json([
            'id' => $opening->id,
            'title' => $opening->title,
            'role' => [
                'id' => $opening->role?->id,
                'name' => $opening->role?->name,
            ],
            'department' => $opening->department,
            'status' => $opening->status,
            'deadline' => $opening->deadline,
            'created_by' => $opening->createdBy?->name,
            'applications_count' => $opening->applications()->count(),
        ]);
    }

    public function candidates(int $id): JsonResponse
    {
        $opening = JobOpening::find($id);
        if (! $opening) {
            return response()->json(['error' => 'Vacante no encontrada'], 404);
        }

        $service = new MatchingService;
        $candidates = $service->rankCandidatesForOpening($opening);

        return response()->json([
            'job_opening' => [
                'id' => $opening->id,
                'title' => $opening->title,
                'role' => $opening->role?->name,
            ],
            'candidates' => $candidates,
        ]);
    }
}
