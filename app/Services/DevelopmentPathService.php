<?php

namespace App\Services;

use App\Models\DevelopmentPath;
use App\Models\People;
use App\Models\Roles;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class DevelopmentPathService
{
    protected $ai;

    public function __construct(AiOrchestratorService $ai)
    {
        $this->ai = $ai;
    }
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
        
        // Intentar generación con Agente Inteligente
        try {
            Log::info("Iniciando generación de ruta con Agente para {$people->id}");
            $agentSteps = $this->generateStepsWithAgent($people, $targetRole, $gaps);
            if (!empty($agentSteps)) {
                $steps = $agentSteps;
                Log::info("Ruta generada exitosamente por el Agente (" . count($steps) . " pasos)");
            }
        } catch (\Exception $e) {
            Log::warning("Agentic learning path failed, falling back to legacy rules: " . $e->getMessage());
        }

        // Fallback a lógica legacy si el agente falla o no devuelve pasos
        if (empty($steps)) {
            $order = 1;
            foreach ($gaps as $gap) {
                $gapValue = (int) $gap['gap'];
                $isCritical = (bool) ($gap['is_critical'] ?? false);
                $skillName = $gap['skill_name'];
                $skillId = $gap['skill_id'];

                $gapSteps = $this->generateStepsForGap($skillId, $skillName, $gapValue, $isCritical);

                foreach ($gapSteps as $step) {
                    $steps[] = array_merge($step, ['order' => $order++]);
                }
            }
        }

        // Calcular duración total
        $totalDays = collect($steps)->sum('estimated_duration_days');
        $estimatedMonths = (int) max(1, round($totalDays / 30));

        // Obtener organization_id
        $organizationId = $people->organization_id;
        if (! $organizationId && auth()->check()) {
            $organizationId = auth()->user()->organization_id;
        }

        $peopleName = $people->full_name ?? ($people->first_name.' '.$people->last_name);
        $actionTitle = "Ruta personalizada de aprendizaje para {$peopleName} → {$targetRole->name}";

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
     * Usa al Agente "Arquitecto de Aprendizaje" para generar una ruta pedagógica.
     */
    protected function generateStepsWithAgent(People $people, Roles $targetRole, $gaps): array
    {
        $peopleName = $people->full_name ?? ($people->first_name . ' ' . $people->last_name);
        
        $taskPrompt = "Persona: {$peopleName}\n";
        $taskPrompt .= "Rol Objetivo: {$targetRole->name}\n";
        $taskPrompt .= "Descripción del Rol: " . ($targetRole->description ?? 'N/A') . "\n";
        $taskPrompt .= "Brechas detectadas (Gaps a cerrar):\n";
        
        foreach ($gaps as $gap) {
            $critical = $gap['is_critical'] ? '[CRÍTICA]' : '';
            $taskPrompt .= "- Skill: {$gap['skill_name']}, Brecha: {$gap['gap']} niveles {$critical}\n";
        }
        
        $taskPrompt .= "\nINSTRUCCIÓN: Genera una secuencia lógica de pasos de aprendizaje (Learning Path). ";
        $taskPrompt .= "Para cada paso utiliza este formato JSON exacto: { \"order\": int, \"action_type\": \"course|mentorship|project|certification|workshop|reading|practice\", \"skill_name\": \"string\", \"description\": \"descripción pedagógica personalizada y motivadora\", \"estimated_duration_days\": int }.\n";
        $taskPrompt .= "LINEAMIENTOS TÉCNICOS:\n";
        $taskPrompt .= "- Gap 1: ~20 días totales (actividad tipo reading/estudio)\n";
        $taskPrompt .= "- Gap 2: ~45 días (actividades combinadas tipo course + practice)\n";
        $taskPrompt .= "- Gap 3: ~80 días (course + mentorship + project)\n";
        $taskPrompt .= "- Gap 4+: ~110 días (course + mentorship + workshop + project)\n";
        $taskPrompt .= "- Skills críticas: añade siempre un paso final de 'certification' (15 días).\n";
        $taskPrompt .= "IMPORTANTE: Resume o agrupa si hay demasiadas skills para no crear una ruta infinita. Devuelve SOLO un JSON con la clave 'steps' conteniendo el array de objetos.";

        $result = $this->ai->agentThink('Arquitecto de Aprendizaje', $taskPrompt);
        
        // Extraer respuesta (soporta si viene como 'response' o directo)
        $response = $result['response'] ?? $result;
        
        // Si es string (markdown code block), limpiar
        if (is_string($response)) {
            $response = preg_replace('/(^```json\s*)|(```$)/m', '', $response);
            $response = json_decode(trim($response), true);
        }
        
        return $response['steps'] ?? [];
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
