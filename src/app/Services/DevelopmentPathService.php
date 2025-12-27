<?php

namespace App\Services;

use App\Models\DevelopmentPath;
use App\Models\Person;
use App\Models\Role;

class DevelopmentPathService
{
    /**
     * Genera una ruta de desarrollo basada en las brechas del GapAnalysisService.
     */
    public function generate(Person $person, Role $targetRole): DevelopmentPath
    {
        $gapService = new GapAnalysisService();
        $analysis = $gapService->calculate($person, $targetRole);

        $gaps = collect($analysis['gaps'])
            ->filter(fn($g) => ($g['gap'] ?? 0) > 0)
            ->sortByDesc(fn($g) => [
                // Prioridad: críticas primero, luego gap más alto, luego rápidas (gap=1)
                (int) ($g['is_critical'] ? 1 : 0),
                (int) $g['gap'],
                (int) ($g['gap'] === 1 ? 1 : 0),
            ]);

        $steps = [];
        $totalHours = 0;

        foreach ($gaps as $gap) {
            $gapValue = (int) $gap['gap'];
            $critical = (bool) $gap['is_critical'];

            // Heurística simple de acción
            $actionType = $critical
                ? 'mentoring'
                : ($gapValue > 2 ? 'course' : ($gapValue === 1 ? 'project' : 'course'));

            // Estimación de horas según tipo y tamaño de brecha
            $durationHours = match ($actionType) {
                'mentoring' => 12 * $gapValue, // 12h por nivel crítico
                'course' => 20 * $gapValue,    // 20h por nivel
                'project' => 16,               // proyecto corto
                default => 12,
            };

            $totalHours += $durationHours;

            $steps[] = [
                'skill_id' => $gap['skill_id'],
                'skill_name' => $gap['skill_name'],
                'action_type' => $actionType,
                'title' => $this->suggestTitle($gap['skill_name'], $actionType),
                'duration_hours' => $durationHours,
                'notes' => $critical ? 'Skill crítica para el rol objetivo.' : null,
            ];
        }

        // Conversión de horas a meses (160h ~ 1 mes)
        $estimatedMonths = max(1, (int) ceil($totalHours / 160));

        return DevelopmentPath::create([
            'organization_id' => $person->organization_id,
            'person_id' => $person->id,
            'target_role_id' => $targetRole->id,
            'status' => 'draft',
            'estimated_duration_months' => $estimatedMonths,
            'steps' => $steps,
        ]);
    }

    private function suggestTitle(string $skillName, string $actionType): string
    {
        return match ($actionType) {
            'mentoring' => 'Mentoría avanzada en ' . $skillName,
            'course' => 'Curso intensivo de ' . $skillName,
            'project' => 'Proyecto práctico en ' . $skillName,
            'certification' => 'Certificación en ' . $skillName,
            'job_shadowing' => 'Job shadowing en ' . $skillName,
            default => 'Mejora de ' . $skillName,
        };
    }
}
