<?php

namespace App\Services\Talent;

use App\Models\Organizations;
use App\Models\OrganizationSnapshot;
use App\Models\People;
use App\Models\PeopleRoleSkill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StratosIqService
{
    /**
     * Captures a monthly snapshot of the organization's state.
     * Including calculating average skill gaps across the entire company.
     */
    public function captureSnapshot(Organizations $org, array $metadata = []): OrganizationSnapshot
    {
        $snapshotDate = Carbon::now()->startOfMonth();

        $averageGapQuery = DB::table('people_role_skills')
            ->join('people', 'people.id', '=', 'people_role_skills.people_id')
            ->whereIn('people.organization_id', [$org->id])
            ->whereRaw('people_role_skills.required_level > people_role_skills.current_level')
            ->select(DB::raw('AVG(people_role_skills.required_level - people_role_skills.current_level) as total_gap'))
            ->first();

        $averageGap = $averageGapQuery ? $averageGapQuery->total_gap : 0;

        $averageGap = $averageGap ?? 0;
        
        // 2. Total People
        $totalPeople = $org->People()->count();

        // Check if snapshot already exists for this month to overwrite/update or just skip
        $snapshot = OrganizationSnapshot::where('organizations_id', $org->id)
            ->where('snapshot_date', $snapshotDate)
            ->first();

        if (!$snapshot) {
            $snapshot = new OrganizationSnapshot([
                'organizations_id' => $org->id,
                'snapshot_date' => $snapshotDate,
            ]);
        }

        $snapshot->average_gap = $averageGap;
        $snapshot->total_people = $totalPeople;

        // Calculate Learning Velocity based on previously captured (e.g. 1 month ago)
        $previousSnapshot = OrganizationSnapshot::where('organizations_id', $org->id)
            ->where('snapshot_date', '<', $snapshotDate)
            ->orderBy('snapshot_date', 'desc')
            ->first();

        // Si la brecha disminuye, la velocidad de aprendizaje es positiva
        if ($previousSnapshot && $previousSnapshot->average_gap > 0) {
            // Diferencia en puntos porcentuales (ej: bajar de 2 a 1.5 es 0.5 de avance)
            $gapDifference = $previousSnapshot->average_gap - $averageGap;
            // Velocity: porcentaje de avance respecto de la brecha anterior (de manera general)
            $snapshot->learning_velocity = max(0, ($gapDifference / $previousSnapshot->average_gap) * 100);
        } else {
            $snapshot->learning_velocity = 0; // Baseline
        }

        // 3. Status Check: Calculate Stability and Internal Growth
        $last90DaysMovements = \App\Models\PersonMovement::where('organization_id', $org->id)
            ->where('movement_date', '>=', now()->subDays(90))
            ->get();
        
        $exitsCount = $last90DaysMovements->where('type', 'exit')->count();
        $promotionsCount = $last90DaysMovements->where('type', 'promotion')->count();
        $hiresCount = $last90DaysMovements->where('type', 'hire')->count();

        // Stability Index: 1 minus the exit rate (monthly avg in last quarter)
        $monthlyExitRate = $totalPeople > 0 ? ($exitsCount / 3) / $totalPeople : 0;
        $stabilityIndex = max(0, 1 - ($monthlyExitRate * 10)); // Scale 0-1

        // Internal Mobility Efficiency: Promotions vs Hires
        $mobilityEfficiency = ($hiresCount + $promotionsCount) > 0
            ? $promotionsCount / ($hiresCount + $promotionsCount)
            : 0;

        // Calculate Stratos IQ
        // Formula: Baseline 100
        // - (Gap Penalty)
        // + (Learning Velocity bonus)
        // + (Stability Bonus)
        // + (Mobility Efficiency Bonus)
        $baseIq = 100;
        $gapPenalty = $averageGap * 15; // Increased weight for gaps
        $velocityBonus = $snapshot->learning_velocity * 0.8;
        $stabilityImpact = ($stabilityIndex - 0.7) * 40; // 0.7 is average stability, deviation impacts IQ
        $mobilityBonus = $mobilityEfficiency * 20;

        $iq = $baseIq - $gapPenalty + $velocityBonus + $stabilityImpact + $mobilityBonus;
        
        // Final normalization and save
        $snapshot->stratos_iq = max(0, min(200, round($iq, 2)));
        $snapshot->metadata = array_merge($metadata, [
            'stability_index' => round($stabilityIndex, 2),
            'mobility_efficiency' => round($mobilityEfficiency, 2),
            'captured_metrics' => [
                'exits_90d' => $exitsCount,
                'promotions_90d' => $promotionsCount,
                'hires_90d' => $hiresCount
            ]
        ]);
        $snapshot->save();

        return $snapshot;
    }
    
    /**
     * Retrieve the trend of Stratos IQ over the last N months.
     */
    public function getTrends(Organizations $org, int $months = 12): array
    {
        $snapshots = OrganizationSnapshot::where('organizations_id', $org->id)
            ->orderBy('snapshot_date', 'asc')
            ->take($months)
            ->get();
            
        return [
            'trends' => $snapshots,
            'current_iq' => $snapshots->last()->stratos_iq ?? 0,
            'current_velocity' => $snapshots->last()->learning_velocity ?? 0,
            'current_gap' => $snapshots->last()->average_gap ?? 0,
        ];
    }
}
