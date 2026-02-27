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
        $description = $isScenario ? $roleModel->rationale : $roleModel->description;

        return $this->analyzePreview($roleName, $description, $roleModel, $isScenario);
    }

    /**
     * Realiza un análisis previo (sin persistir necesariamente en un ID existente).
     */
    public function analyzePreview(string $name, ?string $description, $roleModel = null, bool $isScenario = false): array
    {
        $prompt = "Actúa como Ingeniero de Talento de Stratos. Necesito que apliques la metodología de 'Cubo de Roles' (X, Y, Z) para el siguiente cargo: '{$name}'.
        
        Descripción actual: {$description}
        
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
            $result = $this->orchestrator->agentThink('Ingeniero de Talento', $prompt);
            $analysis = $result['response'];

            // Si hay un modelo, persistir
            if ($roleModel) {
                if ($isScenario) {
                    $roleModel->update(['ai_suggestions' => $analysis]);
                } else {
                    $roleModel->update(['ai_archetype_config' => $analysis]);
                }
            }

            return [
                'status' => 'success',
                'role' => $name,
                'cube' => $analysis['cube_coordinates'] ?? null,
                'analysis' => $analysis
            ];

        } catch (\Exception $e) {
            Log::error("Error analizando rol {$name}: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
