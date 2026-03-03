<?php

namespace App\Services\Talent;

use App\Models\People;
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

            Log::info("Awarded $points points to person $personId. Reason: $reason");
        } catch (\Exception $e) {
            Log::error("Failed to award points", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Award a badge to a person.
     */
    public function awardBadge(int $personId, string $badgeSlug): void
    {
        try {
            $badge = DB::table('badges')->where('slug', $badgeSlug)->first();
            if (!$badge) {
                Log::warning("Badge slug not found: $badgeSlug");
                return;
            }

            // Check if already has it
            $exists = DB::table('people_badges')
                ->where('people_id', $personId)
                ->where('badge_id', $badge->id)
                ->exists();

            if (!$exists) {
                DB::table('people_badges')->insert([
                    'people_id' => $personId,
                    'badge_id' => $badge->id,
                    'awarded_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("Badge $badgeSlug awarded to person $personId");
            }
        } catch (\Exception $e) {
            Log::error("Failed to award badge", ['error' => $e->getMessage()]);
        }
    }
}
