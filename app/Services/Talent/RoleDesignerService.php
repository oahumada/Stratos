<?php

namespace App\Services\Talent;

use App\Models\Roles;
use App\Models\ScenarioRole;
use App\Models\Skill;
use App\Services\AiOrchestratorService;
use Illuminate\Support\Facades\Log;

class RoleDesignerService
{
    protected AiOrchestratorService $orchestrator;
    protected \App\Services\Competency\CompetencyCuratorService $competencyCurator;

    public function __construct(
        AiOrchestratorService $orchestrator,
        \App\Services\Competency\CompetencyCuratorService $competencyCurator
    ) {
        $this->orchestrator = $orchestrator;
        $this->competencyCurator = $competencyCurator;
    }

    /**
     * Transforma las competencias sugeridas por la IA en Skills reales asociadas al rol
     * y las cura (genera BARS, Unidades de Aprendizaje, etc.) usando el Agente de Competencias.
     */
    public function materializeSuggestedSkills(int $roleId): array
    {
        $role = Roles::findOrFail($roleId);
        $config = $role->ai_archetype_config;

        if (! $config || ! isset($config['core_competencies'])) {
            return [
                'status' => 'error',
                'message' => 'No hay sugerencias de competencias para este rol. Ejecute el diseño primero.',
            ];
        }

        $suggested = $config['core_competencies'];
        $materialized = [];

        foreach ($suggested as $comp) {
            $name = $comp['name'];
            $level = $comp['level'] ?? 3;

            // 1. Encontrar o crear la Skill
            $skill = Skill::firstOrCreate(
                ['name' => $name],
                [
                    'organization_id' => $role->organization_id,
                    'category' => 'Transversal', // Por defecto
                    'description' => $comp['rationale'] ?? "Generada automáticamente para el rol {$role->name}",
                ]
            );

            // 2. Asociar al Rol con el nivel sugerido
            if (! $role->skills()->where('skill_id', $skill->id)->exists()) {
                $role->skills()->attach($skill->id, [
                    'required_level' => $level,
                    'is_critical' => false,
                ]);
            } else {
                // Actualizar nivel si ya existe
                $role->skills()->updateExistingPivot($skill->id, [
                    'required_level' => $level,
                ]);
            }

            // 3. Curar la Skill (Generar BARS, etc.)
            $this->competencyCurator->curateSkill($skill->id);

            $materialized[] = $name;
        }

        return [
            'status' => 'success',
            'message' => 'Competencias materializadas y curadas exitosamente.',
            'materialized' => $materialized,
        ];
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
        $prompt = "Actúa como Diseñador de Roles Senior de Stratos, experto en diseño organizacional y SFIA 8. Necesito que apliques la metodología de 'Cubo de Roles' (X, Y, Z) para el siguiente cargo: '{$name}'.
        
        Descripción actual: {$description}
        
        Por favor, define:
        1. Propósito del Rol (Misión estratégica breve).
        2. Descripción Profesional (Un resumen más detallado de responsabilidades clave).
        3. Resultados Esperados (3-5 logros clave cuantificables).
        4. Coordenadas del cubo:
           - Eje X (Arquetipo): ¿Es Estratégico, Táctico u Operativo? Justifica.
           - Eje Y (Nivel de Maestría): Define el nivel de exigencia del 1 al 5.
           - Eje Z (Proceso de Negocio): Identifica el flujo de valor principal al que pertenece.
        
        Además, genera:
        - Una lista de 5 competencias clave justificadas.
        - Sugerencias de mejora para la nitidez organizacional del rol.
        
        Responde estrictamente en formato JSON con esta estructura:
        {
          \"purpose\": \"...\",
          \"description\": \"...\",
          \"expected_results\": \"...\",
          \"cube_coordinates\": {
            \"x_archetype\": \"...\",
            \"y_mastery_level\": 0,
            \"z_business_process\": \"...\",
            \"justification\": \"...\"
          },
          \"core_competencies\": [
            {
              \"name\": \"...\",
              \"level\": 0,
              \"rationale\": \"...\"
            }
          ],
          \"organizational_suggestions\": \"...\"
        }";

        try {
            $result = $this->orchestrator->agentThink('Diseñador de Roles', $prompt);
            $analysis = $result['response'];

            // Si hay un modelo, persistir
            if ($roleModel) {
                if ($isScenario) {
                    $roleModel->update(['ai_suggestions' => $analysis]);
                } else {
                    $roleModel->update([
                        'ai_archetype_config' => $analysis,
                        'purpose' => $analysis['purpose'] ?? $roleModel->purpose,
                        'description' => $analysis['description'] ?? $roleModel->description,
                        'expected_results' => $analysis['expected_results'] ?? $roleModel->expected_results,
                    ]);
                }
            }

            return [
                'status' => 'success',
                'role' => $name,
                'cube' => $analysis['cube_coordinates'] ?? null,
                'purpose' => $analysis['purpose'] ?? null,
                'description' => $analysis['description'] ?? null,
                'expected_results' => $analysis['expected_results'] ?? null,
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
