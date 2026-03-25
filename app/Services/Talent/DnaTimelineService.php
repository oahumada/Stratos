<?php

namespace App\Services\Talent;

use App\Models\Evaluation;
use App\Models\People;
use App\Models\PersonQuest;
use Illuminate\Support\Facades\DB;

/**
 * DnaTimelineService — Generador del Timeline Evolutivo (D6).
 *
 * Agrega hitos de desarrollo de un colaborador a lo largo del tiempo:
 * - Cambios en niveles de habilidades (Evaluaciones).
 * - Puntos de XP ganados.
 * - Medallas (Badges) obtenidas.
 * - Misiones (Quests) completadas.
 */
class DnaTimelineService
{
    /**
     * Obtiene el historial evolutivo completo de un colaborador.
     */
    public function getTimeline(int $personId): array
    {
        $person = People::findOrFail($personId);
        $events = [];

        // 1. Hitos de Habilidades (Evaluaciones)
        $evaluations = Evaluation::where('user_id', $person->user_id ?? 0) // Asumiendo que user_id enlaza evaluaciones
            ->with('skill')
            ->orderBy('evaluated_at', 'asc')
            ->get();

        foreach ($evaluations as $eval) {
            $events[] = [
                'date' => $eval->evaluated_at ? $eval->evaluated_at->toDateString() : $eval->created_at->toDateString(),
                'timestamp' => $eval->evaluated_at ?? $eval->created_at,
                'type' => 'skill_evolution',
                'title' => "Evolución en {$eval->skill->name}",
                'description' => "Nivel alcanzado: {$eval->current_level} (Requerido: {$eval->required_level})",
                'status' => $eval->gap <= 0 ? 'success' : 'developing',
                'icon' => 'mdi-trending-up',
                'meta' => [
                    'skill_id' => $eval->skill_id,
                    'current_level' => (float) $eval->current_level,
                    'gap' => (float) $eval->gap,
                ],
            ];
        }

        // 2. Hitos de Puntos (XP)
        $pointsHistory = DB::table('people_points')
            ->where('people_id', $personId)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($pointsHistory as $point) {
            $events[] = [
                'date' => date('Y-m-d', strtotime($point->created_at)),
                'timestamp' => $point->created_at,
                'type' => 'xp_gain',
                'title' => "Puntos de Experiencia: +{$point->points} XP",
                'description' => $point->reason,
                'status' => 'info',
                'icon' => 'mdi-flash',
                'meta' => json_decode($point->meta, true),
            ];
        }

        // 3. Hitos de Medallas (Badges)
        $badges = DB::table('people_badges')
            ->join('badges', 'people_badges.badge_id', '=', 'badges.id')
            ->where('people_id', $personId)
            ->select('people_badges.*', 'badges.name', 'badges.icon', 'badges.color')
            ->orderBy('awarded_at', 'asc')
            ->get();

        foreach ($badges as $badge) {
            $events[] = [
                'date' => date('Y-m-d', strtotime($badge->awarded_at)),
                'timestamp' => $badge->awarded_at,
                'type' => 'badge_award',
                'title' => "Medalla Obtenida: {$badge->name}",
                'description' => 'Reconocimiento otorgado por logros destacados.',
                'status' => 'success',
                'icon' => $badge->icon ?? 'mdi-medal',
                'color' => $badge->color,
                'meta' => [
                    'badge_id' => $badge->badge_id,
                ],
            ];
        }

        // 4. Hitos de Misiones (Quests)
        $quests = PersonQuest::where('people_id', $personId)
            ->where('status', 'completed')
            ->with('quest')
            ->orderBy('completed_at', 'asc')
            ->get();

        foreach ($quests as $pq) {
            $events[] = [
                'date' => $pq->completed_at->toDateString(),
                'timestamp' => $pq->completed_at,
                'type' => 'quest_complete',
                'title' => "Misión Cumplida: {$pq->quest->title}",
                'description' => $pq->quest->description,
                'status' => 'completed',
                'icon' => 'mdi-check-decagram',
                'meta' => [
                    'quest_id' => $pq->quest_id,
                    'points' => $pq->quest->points_reward,
                ],
            ];
        }

        // Ordenar todos los eventos por timestamp
        usort($events, function ($a, $b) {
            return strtotime($b['timestamp']) <=> strtotime($a['timestamp']);
        });

        return [
            'person' => [
                'id' => $person->id,
                'full_name' => $person->full_name ?? "{$person->first_name} {$person->last_name}",
                'current_role' => $person->role->name ?? 'N/A',
                'total_xp' => $person->current_points ?? 0,
            ],
            'timeline' => $events,
            'stats' => [
                'skills_analyzed' => $evaluations->unique('skill_id')->count(),
                'badges_count' => $badges->count(),
                'quests_completed' => $quests->count(),
            ],
        ];
    }
}
