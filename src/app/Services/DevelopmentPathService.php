<?php

namespace App\Services;

use App\Models\DevelopmentPath;
use App\Models\People;
use App\Models\Roles;

class DevelopmentPathService
{
    /**
     * Genera una ruta de desarrollo personalizada basada en gap analysis.
     *
     * Lógica según especificación:
     * - Gap 1: reading (15-20 días)
     * - Gap 2: course + practice (45-50 días)
     * - Gap 3: course + mentorship + project (75-90 días)
     * - Gap 4+: course + mentorship + project + workshop (100-120 días)
     * - Skills críticas: + certification (15 días extra)
     *
     * Priorización: críticas primero, mayor gap primero
     */
    public function generate(People $people, Roles $targetRole): DevelopmentPath
    {
        $gapService = new GapAnalysisService;
        $analysis = $gapService->calculate($people, $targetRole);

        // Filtrar solo gaps > 0 y ordenar por prioridad
        $gaps = collect($analysis['gaps'] ?? [])
            ->filter(fn ($g) => ($g['gap'] ?? 0) > 0)
            ->sortBy([
                // Orden: críticas desc, gap desc, nombre asc
                fn ($g) => $g['is_critical'] ? 0 : 1,
                fn ($g) => -$g['gap'],
                fn ($g) => $g['skill_name'],
            ]);

        $steps = [];
        $order = 1;
        $totalDays = 0;

        foreach ($gaps as $gap) {
            $gapValue = (int) $gap['gap'];
            $isCritical = (bool) ($gap['is_critical'] ?? false);
            $skillName = $gap['skill_name'];
            $skillId = $gap['skill_id'];

            // Generar pasos según el gap
            $gapSteps = $this->generateStepsForGap($skillId, $skillName, $gapValue, $isCritical);

            foreach ($gapSteps as $step) {
                $steps[] = array_merge($step, ['order' => $order++]);
                $totalDays += $step['estimated_duration_days'];
            }
        }

        // Convertir días a meses (30 días = 1 mes) y asegurar entero para la columna DB
        $estimatedMonths = (int) max(1, round($totalDays / 30));

        // Obtener organization_id de la persona o del usuario autenticado
        $organizationId = $people->organization_id;
        if (! $organizationId && auth()->check()) {
            $organizationId = auth()->user()->organization_id;
        }

        $peopleName = $people->full_name ?? ($people->first_name.' '.$people->last_name);
        $actionTitle = "Ruta automática de aprendizaje para {$peopleName} → {$targetRole->name}";

        return DevelopmentPath::create([
            'action_title' => $actionTitle,
            'organization_id' => $organizationId,
            'people_id' => $people->id,
            'target_role_id' => $targetRole->id,
            'status' => 'draft',
            'estimated_duration_months' => $estimatedMonths,
            'steps' => $steps,
        ]);
    }

    /**
     * Genera pasos específicos para una skill según el tamaño del gap
     */
    private function generateStepsForGap(int $skillId, string $skillName, int $gap, bool $isCritical): array
    {
        $steps = [];

        switch ($gap) {
            case 1:
                // Gap pequeño: solo lectura
                $steps[] = [
                    'action_type' => 'reading',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Estudio individual y documentación de {$skillName}",
                    'estimated_duration_days' => rand(15, 20),
                    'status' => 'draft',
                ];
                break;

            case 2:
                // Gap medio: curso + práctica
                $steps[] = [
                    'action_type' => 'course',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Curso intensivo de {$skillName} con enfoque práctico",
                    'estimated_duration_days' => rand(25, 30),
                    'status' => 'draft',
                ];
                $steps[] = [
                    'action_type' => 'practice',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Ejercicios prácticos y proyectos pequeños de {$skillName}",
                    'estimated_duration_days' => rand(15, 20),
                    'status' => 'draft',
                ];
                break;

            case 3:
                // Gap grande: curso + mentoría + proyecto
                $steps[] = [
                    'action_type' => 'course',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Formación avanzada en {$skillName}",
                    'estimated_duration_days' => rand(30, 35),
                    'status' => 'draft',
                ];
                $steps[] = [
                    'action_type' => 'mentorship',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Mentoría personalizada para dominar {$skillName}",
                    'estimated_duration_days' => rand(25, 30),
                    'status' => 'draft',
                ];
                $steps[] = [
                    'action_type' => 'project',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Proyecto real aplicando {$skillName} en caso de uso empresarial",
                    'estimated_duration_days' => rand(20, 25),
                    'status' => 'draft',
                ];
                break;

            default: // 4+
                // Gap muy grande: curso + mentoría + proyecto + workshop
                $steps[] = [
                    'action_type' => 'course',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Programa completo de formación en {$skillName}",
                    'estimated_duration_days' => rand(40, 45),
                    'status' => 'draft',
                ];
                $steps[] = [
                    'action_type' => 'mentorship',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Mentoría intensiva con experto en {$skillName}",
                    'estimated_duration_days' => rand(30, 35),
                    'status' => 'draft',
                ];
                $steps[] = [
                    'action_type' => 'workshop',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Workshop práctico de {$skillName}",
                    'estimated_duration_days' => rand(10, 15),
                    'status' => 'draft',
                ];
                $steps[] = [
                    'action_type' => 'project',
                    'skill_id' => $skillId,
                    'skill_name' => $skillName,
                    'description' => "Proyecto enterprise aplicando {$skillName}",
                    'estimated_duration_days' => rand(20, 25),
                    'status' => 'draft',
                ];
                break;
        }

        // Si es crítica, agregar certificación
        if ($isCritical) {
            $steps[] = [
                'action_type' => 'certification',
                'skill_id' => $skillId,
                'skill_name' => $skillName,
                'description' => "Certificación oficial en {$skillName}",
                'estimated_duration_days' => 15,
                'status' => 'draft',
            ];
        }

        return $steps;
    }
}
