<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Talent\Lms\LmsService;
use App\Models\DevelopmentAction;
use Illuminate\Http\Request;

class LmsController extends Controller
{
    protected $lmsService;

    public function __construct(LmsService $lmsService)
    {
        $this->lmsService = $lmsService;
    }

    /**
     * Search courses across all providers.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $courses = $this->lmsService->searchCourses($query);

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Launch a course for a development action.
     */
    public function launch(DevelopmentAction $action)
    {
        try {
            $url = $this->lmsService->launchAction($action);

            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Synchronize progress for an action.
     */
    public function sync(DevelopmentAction $action)
    {
        $updated = $this->lmsService->syncProgress($action);

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'status' => $action->status
        ]);
    }

    /**
     * Get gamification stats for the current user.
     */
    public function getGamificationStats(Request $request)
    {
        $user = auth()->user();
        $person = \App\Models\People::where('user_id', $user->id)->first();

        if (!$person) {
            return response()->json(['success' => false, 'message' => 'Colaborador no encontrado'], 404);
        }

        $gamification = \App\Models\UserGamification::firstOrCreate(
            ['user_id' => $person->id],
            ['total_xp' => 0, 'level' => 1, 'current_points' => 0]
        );

        return response()->json([
            'success' => true,
            'stats' => [
                'xp' => $gamification->total_xp,
                'level' => $gamification->level,
                'points' => $gamification->current_points,
                'next_level_xp' => $gamification->level * 1000,
                'progress_percentage' => round(($gamification->total_xp % 1000) / 10, 2),
                'rank_name' => $this->getRankName($gamification->level)
            ]
        ]);
    }

    /**
     * Get top learners leaderboard.
     */
    public function getLeaderboard()
    {
        $top = \App\Models\UserGamification::join('people', 'user_gamification.user_id', '=', 'people.id')
            ->select('user_gamification.*', 'people.first_name', 'people.last_name', 'people.photo_url')
            ->orderBy('total_xp', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'leaderboard' => $top
        ]);
    }

    private function getRankName(int $level): string
    {
        if ($level >= 20) return 'Maestro Arquitecto';
        if ($level >= 15) return 'Estratega Senior';
        if ($level >= 10) return 'Líder en Desarrollo';
        if ($level >= 5) return 'Analista Avanzado';
        return 'Aprendiz de Talento';
    }
}
