<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use App\Services\Talent\GamificationService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    use ApiResponses;

    public function __construct(
        protected GamificationService $gamificationService
    ) {}

    /**
     * Get active and completed quests for a given person.
     */
    public function getPersonQuests(int $peopleId): JsonResponse
    {
        try {
            $quests = $this->gamificationService->getPersonQuests($peopleId);

            return $this->success($quests, 'Quests fetched successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to fetch quests: '.$e->getMessage(), 500);
        }
    }

    /**
     * Get all available quests.
     */
    public function getAvailableQuests(): JsonResponse
    {
        try {
            $quests = Quest::with('badge')->where('status', 'active')->get();

            return $this->success($quests, 'Available quests fetched.');
        } catch (\Exception $e) {
            return $this->error('Failed to fetch available quests: '.$e->getMessage(), 500);
        }
    }

    /**
     * Start a quest for a person.
     */
    public function startQuest(Request $request, int $peopleId, int $questId): JsonResponse
    {
        try {
            $personQuest = $this->gamificationService->startQuest($peopleId, $questId);

            if (! $personQuest) {
                return $this->error('Quest not found or could not be started.', 404);
            }

            return $this->success($personQuest, 'Quest started successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to start quest: '.$e->getMessage(), 500);
        }
    }

    /**
     * Progress a quest.
     */
    public function progressQuest(Request $request, int $peopleId, int $questId): JsonResponse
    {
        $validated = $request->validate([
            'progress' => 'required|array',
            'complete' => 'boolean',
        ]);

        try {
            $personQuest = $this->gamificationService->progressQuest($peopleId, $questId, $validated['progress']);

            if (! $personQuest) {
                return $this->error('Quest not found or not active.', 404);
            }

            if ($request->boolean('complete')) {
                $personQuest = $this->gamificationService->completeQuest($peopleId, $questId);
            }

            return $this->success($personQuest->load('quest.badge'), 'Quest progressed successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to progress quest: '.$e->getMessage(), 500);
        }
    }
}
