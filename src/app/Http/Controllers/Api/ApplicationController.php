<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobOpening;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(): JsonResponse
    {
        $applications = Application::with('jobOpening', 'person')
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'person' => $a->person?->full_name ?? ($a->person?->first_name . ' ' . $a->person?->last_name),
                'job_opening' => $a->jobOpening?->title,
                'status' => $a->status,
                'applied_at' => $a->applied_at,
                'message' => $a->message,
            ]);

        return response()->json(['data' => $applications]);
    }

    public function show(int $id): JsonResponse
    {
        $application = Application::with('jobOpening', 'person')->find($id);
        if (! $application) {
            return response()->json(['error' => 'Postulación no encontrada'], 404);
        }

        return response()->json([
            'id' => $application->id,
            'person' => [
                'id' => $application->person?->id,
                'name' => $application->person?->full_name ?? ($application->person?->first_name . ' ' . $application->person?->last_name),
            ],
            'job_opening' => [
                'id' => $application->jobOpening?->id,
                'title' => $application->jobOpening?->title,
            ],
            'status' => $application->status,
            'applied_at' => $application->applied_at,
            'message' => $application->message,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'person_id' => ['required', 'integer', 'exists:Person,id'],
            'job_opening_id' => ['required', 'integer', 'exists:job_openings,id'],
            'message' => ['nullable', 'string'],
        ]);

        $person = Person::find($data['person_id']);
        $opening = JobOpening::find($data['job_opening_id']);

        // Validar que la persona y vacante estén en la misma organización
        if ($person->organization_id !== $opening->organization_id) {
            return response()->json(['error' => 'Persona y vacante deben estar en la misma organización'], 422);
        }

        // Validar que no exista postulación duplicada
        $existing = Application::where('person_id', $person->id)
            ->where('job_opening_id', $opening->id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Ya existe una postulación para esta combinación'], 422);
        }

        $application = Application::create([
            'person_id' => $person->id,
            'job_opening_id' => $opening->id,
            'status' => 'pending',
            'message' => $data['message'] ?? null,
            'applied_at' => now(),
        ]);

        return response()->json([
            'id' => $application->id,
            'status' => $application->status,
            'applied_at' => $application->applied_at,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $application = Application::find($id);
        if (! $application) {
            return response()->json(['error' => 'Postulación no encontrada'], 404);
        }

        $data = $request->validate([
            'status' => ['required', 'in:pending,under_review,accepted,rejected'],
        ]);

        $application->update(['status' => $data['status']]);

        return response()->json([
            'id' => $application->id,
            'status' => $application->status,
        ]);
    }
}
