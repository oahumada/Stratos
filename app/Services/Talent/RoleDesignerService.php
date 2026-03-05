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
                'analysis' => $analysis,
            ];

        } catch (\Exception $e) {
            Log::error("Error analizando rol {$name}: ".$e->getMessage());

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Orquesta el empaquetado de capacidades genuinamente nuevas.
     * Evalúa si deben enriquecer roles existentes (con un gap alto) o instanciar roles nuevos (Creation).
     */
    public function bundleNewCapabilities(array $newCompetencies, array $candidateRoles): array
    {
        $competenciesJson = json_encode($newCompetencies, JSON_UNESCAPED_UNICODE);
        $rolesJson = json_encode($candidateRoles, JSON_UNESCAPED_UNICODE);

        $prompt = "Actúa como Arquitecto de Diseño Organizacional de Alta Eficiencia en Stratos.
        
        CONTEXTO: 
        El escenario futuro exige incorporar capacidades totalmente nuevas ('Nuevas Competencias') que la empresa hoy no tiene.
        También tienes una lista de 'Roles Actuales' cercanos o afines.
        
        OBJETIVO:
        Debes hacer 'Role Bundling' (Empaquetado de Roles) para minimizar el impacto y evitar fragmentar a la organización en demasiados cargos nuevos innecesarios.
        
        REGLAS:
        1. ENRICHMENT: Si una o varias competencias nuevas encajan lógicamente en uno de los 'Roles Actuales', asígnalas ahí indicando 'enrichment' (esto implicará que dicho rol necesitará un upskilling a futuro).
        2. CREATION: Si una o varias competencias son demasiado disruptivas, alienígenas, o forman un modelo de negocio completo por sí solas, empaquétalas juntas y sugiere la creación de un nuevo gran rol, indicando 'creation'.
        
        DATOS DE ENTRADA:
        - Nuevas Competencias a resolver: {$competenciesJson}
        - Roles Actuales Afines (Candidatos para recibir absorción): {$rolesJson}
        
        Responde ESTRICTAMENTE en formato JSON con la siguiente estructura y sin markdown ni texto extra alrededor:
        {
            \"orchestration\": [
                {
                    \"type\": \"enrichment o creation\",
                    \"target_role_name\": \"Nombre del rol (el actual afín, o el nuevo sugerido)\",
                    \"target_role_id\": \"ID del rol (si es enrichment, usar el original del array. Si es creation usa null)\",
                    \"assigned_competencies\": [\"Nombre de competencia nueva 1 asignada\", \"Nombre 2\"],
                    \"rationale\": \"Justifica brevemente por qué este empaquetado es la opción de máxima eficiencia organizacional.\"
                }
            ]
        }";

        try {
            $result = $this->orchestrator->agentThink('Estratega de Talento', $prompt);

            // Extracción segura del array 'orchestration' de la respuesta JSON
            $parsed = $result['response'] ?? $result;

            return $parsed['orchestration'] ?? [];

        } catch (\Exception $e) {
            Log::error('Error en AI Role Bundling: '.$e->getMessage());

            return [];
        }
    }
}
