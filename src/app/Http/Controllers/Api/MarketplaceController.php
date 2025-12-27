<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\Person;
use App\Services\GapAnalysisService;
use Illuminate\Http\JsonResponse;

class MarketplaceController extends Controller
{
    public function opportunities(int $personId): JsonResponse
    {
        $person = Person::find($personId);
        if (! $person) {
            return response()->json(['error' => 'Persona no encontrada'], 404);
        }

        $openings = JobOpening::where('organization_id', $person->organization_id)
            ->where('status', 'open')
            ->with('role')
            ->get();

        $gapService = new GapAnalysisService();
        $opportunities = $openings->map(function ($opening) use ($person, $gapService) {
            $analysis = $gapService->calculate($person, $opening->role);

            return [
                'id' => $opening->id,
                'title' => $opening->title,
                'role' => $opening->role?->name,
                'department' => $opening->department,
                'deadline' => $opening->deadline,
                'match_percentage' => $analysis['match_percentage'],
                'category' => $analysis['summary']['category'],
                'missing_skills_count' => collect($analysis['gaps'])->where('gap', '>', 0)->count(),
            ];
        })->sortByDesc('match_percentage')->values();

        return response()->json([
            'person' => [
                'id' => $person->id,
                'name' => $person->full_name ?? ($person->first_name . ' ' . $person->last_name),
            ],
            'opportunities' => $opportunities,
        ]);
    }
}
