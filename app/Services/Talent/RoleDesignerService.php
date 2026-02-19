<?php

namespace App\Services\Talent;

use App\Models\Roles;
use App\Models\ScenarioRole;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class RoleDesignerService
{
    protected AiOrchestratorService $orchestrator;

    public function __construct(AiOrchestratorService $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * Analiza o genera la configuración de un rol basado en el Modelo de Cubo (X, Y, Z).
     */
    public function designRole(int $roleId, bool $isScenario = false): array
    {
        $roleModel = $isScenario ? ScenarioRole::with('role')->findOrFail($roleId) : Roles::findOrFail($roleId);
        $roleName = $isScenario ? ($roleModel->role->name ?? 'Rol en Incubación') : $roleModel->name;
        
        $prompt = "Actúa como Diseñador de Roles de Stratos. Necesito que apliques la metodología de 'Cubo de Roles' (X, Y, Z) para el siguiente cargo: '{$roleName}'.
        
        Descripción actual: " . ($isScenario ? $roleModel->rationale : $roleModel->description) . "
        
        Por favor, define las coordenadas del cubo:
        1. Eje X (Arquetipo): ¿Es Estratégico, Táctico u Operativo? Justifica.
        2. Eje Y (Nivel de Maestría): Define el nivel de exigencia del 1 al 5.
        3. Eje Z (Proceso de Negocio): Identifica el flujo de valor principal al que pertenece.
        
        Además, genera:
        - Una lista de 5 competencias clave justificadas.
        - Sugerencias de mejora para la nitidez organizacional del rol.
        
        Responde estrictamente en formato JSON con esta estructura:
        {
          \"cube_coordinates\": {
            \"x_archetype\": \"...\",
            \"y_mastery_level\": 0,
            \"z_business_process\": \"...\",
            \"justification\": \"...\"
          },
          \"core_competencies\": [
            { \"name\": \"...\", \"level\": 0, \"rationale\": \"...\" }
          ],
          \"organizational_suggestions\": \"...\"
        }";

        try {
            $result = $this->orchestrator->agentThink('Diseñador de Roles', $prompt);
            $analysis = $result['response'];

            // Persistir en el modelo correspondiente
            if ($isScenario) {
                $roleModel->update(['ai_suggestions' => $analysis]);
            } else {
                $roleModel->update(['ai_archetype_config' => $analysis]);
            }

            Log::info("Diseño de Cubo de Roles completado para: {$roleName}");

            return [
                'status' => 'success',
                'role' => $roleName,
                'cube' => $analysis['cube_coordinates'] ?? null,
                'analysis' => $analysis
            ];

        } catch (\Exception $e) {
            Log::error("Error diseñando rol {$roleName}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
