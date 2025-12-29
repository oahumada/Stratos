<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    public function index(): JsonResponse
    {
        $Person = Person::with('skills')->get()->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->full_name ?? ($p->first_name . ' ' . $p->last_name),
            'email' => $p->email,
            'department' => $p->department,
            'hire_date' => $p->hire_date,
            'current_role' => $p->currentRole?->name,
            'skills_count' => $p->skills()->count(),
        ]);

        return response()->json(['data' => $Person]);
    }

    public function show(int $id): JsonResponse
    {
        $person = Person::with('skills', 'currentRole')->find($id);
        if (! $person) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        return response()->json([
            'id' => $person->id,
            'name' => $person->full_name ?? ($person->first_name . ' ' . $person->last_name),
            'email' => $person->email,
            'first_name' => $person->first_name,
            'last_name' => $person->last_name,
            'department' => $person->department,
            'hire_date' => $person->hire_date,
            'current_role' => $person->currentRole?->name,
            'skills' => $person->skills->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'category' => $s->category,
                'level' => $s->pivot->level ?? 0,
                'last_evaluated_at' => $s->pivot->last_evaluated_at,
            ]),
        ]);
    }
}
