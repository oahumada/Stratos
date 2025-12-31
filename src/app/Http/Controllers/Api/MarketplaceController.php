<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\People;
use App\Services\GapAnalysisService;
use Illuminate\Http\JsonResponse;

class MarketplaceController extends Controller
{
    public function opportunities(int $peopleId): JsonResponse
    {
        $people = People::find($peopleId);
        if (! $people) {
            return response()->json(['error' => 'Peoplea no encontrada'], 404);
        }

        $openings = JobOpening::where('organization_id', $people->organization_id)
            ->where('status', 'open')
            ->with('role')
            ->get();

        $gapService = new GapAnalysisService();
        $opportunities = $openings->map(function ($opening) use ($people, $gapService) {
            $analysis = $gapService->calculate($people, $opening->role);

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
            'people' => [
                'id' => $people->id,
                'name' => $people->full_name ?? ($people->first_name . ' ' . $people->last_name),
            ],
            'opportunities' => $opportunities,
        ]);
    }
}
