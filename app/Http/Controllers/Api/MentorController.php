<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Talent\MentorMatchingService;
use App\Models\Skill;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    /**
     * @var MentorMatchingService
     */
    private $mentorService;

    public function __construct(MentorMatchingService $mentorService)
    {
        $this->mentorService = $mentorService;
    }

    /**
     * GET /api/talent/mentors/suggest?skill_id={id}&level=4
     * Busqueda inteligente de mentores.
     */
    public function suggest(Request $request)
    {
        $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'level' => 'sometimes|integer|min:3|max:5'
        ]);

        $skillId = $request->query('skill_id');
        $minLevel = $request->query('min_level', 4);

        $mentors = $this->mentorService->findMentors($skillId, $minLevel);

        $skill = Skill::find($skillId);

        return response()->json([
            'success' => true,
            'skill' => $skill->name,
            'mentors' => $mentors->map(fn($m) => [
                'id' => $m->id,
                'full_name' => $m->full_name,
                'role' => $m->role_name,
                'expertise_level' => $m->expertise_level,
                'verified' => true,
                'avatar' => $m->avatar_url ?? null,
                'match_score' => rand(85, 99) . '%' // Simulado por ahora
            ])
        ]);
    }
}
