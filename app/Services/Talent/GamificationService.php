<?php

namespace App\Services\Talent;

use App\Models\Badge;
use App\Models\People;
use App\Models\PersonQuest;
use App\Models\Quest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GamificationService
{
    /**
     * Award points to a person.
     */
    public function awardPoints(int $personId, int $points, string $reason, ?array $meta = null): void
    {
        try {
            DB::table('people_points')->insert([
                'people_id' => $personId,
                'points' => $points,
                'reason' => $reason,
                'meta' => json_encode($meta),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update cached total points in the people table if applicable
            $person = People::find($personId);
            if ($person && \Schema::hasColumn('people', 'current_points')) {
                $person->current_points += $points;
                $person->save();
            }

            Log::info("Awarded $points XP to person $personId. Reason: $reason");
        } catch (\Exception $e) {
            Log::error('Failed to award points', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Award a badge to a person.
     */
    public function awardBadge(int $personId, string $badgeSlug): void
    {
        try {
            $badge = Badge::where('slug', $badgeSlug)->first();
            if (! $badge) {
                Log::warning("Badge slug not found: $badgeSlug");

                return;
            }

            $exists = DB::table('people_badges')
                ->where('people_id', $personId)
                ->where('badge_id', $badge->id)
                ->exists();

            if (! $exists) {
                DB::table('people_badges')->insert([
                    'people_id' => $personId,
                    'badge_id' => $badge->id,
                    'awarded_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("Badge {$badgeSlug} awarded to person $personId");
            }
        } catch (\Exception $e) {
            Log::error('Failed to award badge', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get active and completed quests for a person.
     */
    public function getPersonQuests(int $personId): array
    {
        return PersonQuest::with('quest.badge')
            ->where('people_id', $personId)
            ->get()
            ->toArray();
    }

    /**
     * Start a new quest for a person.
     */
    public function startQuest(int $personId, int $questId): ?PersonQuest
    {
        $exists = PersonQuest::where('people_id', $personId)->where('quest_id', $questId)->first();
        if ($exists) {
            return $exists;
        }

        $quest = Quest::find($questId);
        if (! $quest) {
            return null;
        }

        return PersonQuest::create([
            'people_id' => $personId,
            'quest_id' => $questId,
            'status' => 'active',
            'progress' => [],
        ]);
    }

    /**
     * Progress a quest state.
     */
    public function progressQuest(int $personId, int $questId, array $progressUpdates): ?PersonQuest
    {
        $personQuest = PersonQuest::where('people_id', $personId)->where('quest_id', $questId)->first();
        if (! $personQuest || $personQuest->status !== 'active') {
            return $personQuest;
        }

        $currentProgress = $personQuest->progress ?? [];
        $newProgress = array_merge($currentProgress, $progressUpdates);

        $personQuest->progress = $newProgress;
        $personQuest->save();

        return $personQuest;
    }

    /**
     * Mark a quest as completed, awarding points and potential badges.
     */
    public function completeQuest(int $personId, int $questId): ?PersonQuest
    {
        $personQuest = PersonQuest::with('quest')->where('people_id', $personId)->where('quest_id', $questId)->first();
        if (! $personQuest || $personQuest->status === 'completed') {
            return $personQuest;
        }

        $quest = $personQuest->quest;

        DB::transaction(function () use ($personQuest, $quest, $personId) {
            $personQuest->status = 'completed';
            $personQuest->completed_at = now();
            $personQuest->save();

            // Award points
            if ($quest->points_reward > 0) {
                $this->awardPoints($personId, $quest->points_reward, "Quest Completed: {$quest->title}", ['quest_id' => $quest->id]);
            }

            // Award badge
            if ($quest->badge_id) {
                $badge = Badge::find($quest->badge_id);
                if ($badge) {
                    $this->awardBadge($personId, $badge->slug);
                }
            }
        });

        return $personQuest;
    }
    /**
     * Spend points to redeem a reward.
     */
    public function redeemReward(int $personId, int $rewardId): array
    {
        $person = People::findOrFail($personId);
        $reward = DB::table('rewards')->where('id', $rewardId)->where('is_active', true)->first();

        if (!$reward) {
            return ['success' => false, 'message' => 'Recompensa no encontrada o inactiva.'];
        }

        if ($person->current_points < $reward->points_cost) {
            return [
                'success' => false,
                'message' => "Puntos insuficientes. Necesitas {$reward->points_cost} y tienes {$person->current_points}."
            ];
        }

        return DB::transaction(function () use ($person, $reward) {
            // 1. Deduct points
            $person->current_points -= $reward->points_cost;
            $person->save();

            // 2. Log the transaction
            DB::table('people_points')->insert([
                'people_id' => $person->id,
                'points' => -$reward->points_cost,
                'reason' => "Redemption: {$reward->title}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Create redemption record
            $redemptionId = DB::table('reward_redemptions')->insertGetId([
                'people_id' => $person->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_cost,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Person {$person->id} redeemed reward {$reward->id} for {$reward->points_cost} points.");

            return [
                'success' => true,
                'message' => '¡Canje exitoso! Tu solicitud está siendo procesada.',
                'redemption_id' => $redemptionId
            ];
        });
    }

    /**
     * Get all active rewards from the catalog.
     */
    public function getAvailableRewards(): array
    {
        return DB::table('rewards')
            ->where('is_active', true)
            ->orderBy('points_cost', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Get redemption history for a specific person.
     */
    public function getRedemptionHistory(int $personId): array
    {
        return DB::table('reward_redemptions')
            ->join('rewards', 'reward_redemptions.reward_id', '=', 'rewards.id')
            ->where('reward_redemptions.people_id', $personId)
            ->select('reward_redemptions.*', 'rewards.title as reward_title', 'rewards.category')
            ->orderBy('reward_redemptions.created_at', 'desc')
            ->get()
            ->toArray();
    }
}
