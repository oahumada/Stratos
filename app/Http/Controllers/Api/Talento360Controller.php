<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentSession;
use App\Models\People;
use App\Models\PsychometricProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Talento360Controller extends Controller
{
    /**
     * Obtiene métricas globales para el Dashboard Talento 360.
     */
    public function metrics()
    {
        $organizationId = auth()->user()->organization_id;

        // 1. Promedio de potencial global
        // 1. Promedio de potencial global (MySQL JSON extraction)
        $avgPotential = DB::table('assessment_sessions')
            ->where('organization_id', $organizationId)
            ->where('status', 'analyzed')
            ->selectRaw("AVG(CAST(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.overall_potential')) AS DECIMAL(10,2))) as avg_potential")
            ->value('avg_potential');

        // 2. Distribución de rasgos (Promedio por rasgo)
        $traitDistribution = PsychometricProfile::join('people', 'psychometric_profiles.people_id', '=', 'people.id')
            ->where('people.organization_id', $organizationId)
            ->select('trait_name', DB::raw('AVG(score) as average_score'))
            ->groupBy('trait_name')
            ->orderBy('average_score', 'desc')
            ->get();

        // 3. Conteo de High Potentials (Potencial > 0.8)
        $highPotentialQuery = DB::table('assessment_sessions')
            ->where('organization_id', $organizationId)
            ->where('status', 'analyzed')
            ->whereRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.overall_potential')) AS DECIMAL(10,2)) > ?", [0.8]);

        $highPotentialCount = $highPotentialQuery->count();

        // 4. Últimas evaluaciones
        $latestAssessments = AssessmentSession::with('person')
            ->where('organization_id', $organizationId)
            ->where('status', 'analyzed')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($session) {
                return [
                    'id' => $session->id,
                    'person_name' => $session->person->full_name,
                    'type' => $session->type,
                    'potential' => $session->metadata['overall_potential'] ?? 0,
                    'completed_at' => $session->completed_at->diffForHumans()
                ];
            });

        return response()->json([
            'potential_index' => round(($avgPotential ?? 0) * 100, 1),
            'high_potential_count' => $highPotentialCount,
            'trait_distribution' => $traitDistribution,
            'latest_assessments' => $latestAssessments,
            'total_assessed' => AssessmentSession::where('organization_id', $organizationId)->where('status', 'analyzed')->count()
        ]);
    }
}
