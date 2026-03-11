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

        // Calculate Stratos IQ
        // Formula concept: Baseline 100 + (Learning Velocity % * weight) - (Average Gap * weight)
        // Adjust formula according to specific business rules, here's a logical baseline
        $baseIq = 100;
        $gapPenalty = $averageGap * 10; // e.g. a gap of 2 (on scale 0-5) drops IQ by 20 points
        $velocityBonus = $snapshot->learning_velocity * 0.5; // e.g. 10% velocity adds 5 points
        
        $iq = $baseIq - $gapPenalty + $velocityBonus;
        // Normalizado entre 0 y 200
        $snapshot->stratos_iq = max(0, min(200, $iq));
        $snapshot->metadata = $metadata;
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
