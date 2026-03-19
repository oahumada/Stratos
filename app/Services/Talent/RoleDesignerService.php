<?php

namespace App\Services\Talent;

use App\Models\ApprovalRequest;
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

        // Asegurar que existe una competencia para agrupar estas sugerencias
        $groupComp = \App\Models\Competency::firstOrCreate(
            ['name' => 'Sugerencias de Diseño IA', 'organization_id' => $role->organization_id],
            [
                'description' => 'Competencias sugeridas automáticamente por el Diseñador de Roles IA.',
                'status' => 'proposed',
            ]
        );

        foreach ($suggested as $comp) {
            $name = $comp['name'];
            $level = $comp['level'] ?? 3;

            // 1. Encontrar o crear la Skill
            $skill = Skill::firstOrCreate(
                ['name' => $name, 'organization_id' => $role->organization_id],
                [
                    'category' => 'incubation',
                    'status' => 'proposed',
                    'description' => $comp['rationale'] ?? "Generada automáticamente para el rol {$role->name}",
                ]
            );

            // Asegurar que la skill esté vinculada a la competencia de grupo para que aparezca en el catálogo
            if (! $groupComp->skills()->where('skill_id', $skill->id)->exists()) {
                $groupComp->skills()->attach($skill->id, ['weight' => 10]);
            }

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

            // 3. Curar la Skill (Generar BARS, etc. si fuera necesario en el futuro)
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

    /**
     * Crea un requerimiento de aprobación para un responsable.
     */
    public function requestApproval(int $roleId, int $approverId): array
    {
        Roles::findOrFail($roleId);

        // Crear la solicitud de aprobación
        $approval = ApprovalRequest::create([
            'approvable_type' => Roles::class,
            'approvable_id' => $roleId,
            'approver_id' => $approverId,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // En el futuro, enviar email con magic link aquí.
        // URL format: /approve/role/{token}
        
        return [
            'status' => 'success',
            'message' => 'Solicitud de aprobación enviada exitosamente.',
            'token' => $approval->token,
        ];
    }

    /**
     * Finalize the approval of a role or competency.
     */
    public function finalizeApproval($token, array $data)
    {
        $request = ApprovalRequest::where('token', $token)
            ->where('status', 'pending')
            ->firstOrFail();

        $approvable = $request->approvable;

        if ($approvable instanceof \App\Models\Roles) {
            return $this->finalizeRoleApproval($request, $data);
        } elseif ($approvable instanceof \App\Models\Competency) {
            return $this->finalizeCompetencyApproval($request, $data);
        }

        throw new \Exception('Unknown approvable type');
    }

    protected function finalizeRoleApproval(ApprovalRequest $request, array $data): array
    {
        $role = $request->approvable;

        // Update role with finalized data
        if (isset($data['mission'])) {
            $role->mission = $data['mission'];
        }
        if (isset($data['purpose'])) {
            $role->purpose = $data['purpose'];
        }
        if (isset($data['expected_results'])) {
            $role->expected_results = $data['expected_results'];
        }

        $role->status = 'active';
        $role->save();

        // Mark request as approved
        $request->status = 'approved';
        $request->signature_data = array_merge($request->signature_data ?? [], [
            'approved_at' => now(),
            'final_data' => $data
        ]);
        $request->seal();
        $request->save();

        // Materialize competencies and skills
        $materializeResult = $this->materializeSuggestedSkills($role->id);

        // Sign the role and its skills
        $role->seal();
        $role->save();
        
        foreach ($role->skills as $skill) {
            $skill->status = 'active';
            $skill->seal();
            $skill->save();
        }

        // Crear versión inicial del rol
        $roleVersion = \App\Models\RoleVersion::create([
            'organization_id' => $role->organization_id,
            'role_id' => $role->id,
            'version_group_id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Aprobación Oficial V1.0',
            'description' => 'Versión inicial materializada tras aprobación digital.',
            'effective_from' => now(),
            'evolution_state' => 'baseline',
            'metadata' => [
                'digital_signature' => $role->digital_signature,
                'signed_at' => $role->signed_at,
                'approver_id' => $request->approver_id,
                'role_snapshot' => $role->toArray()
            ],
            'created_by' => $request->approver_id
        ]);

        // Registrar Log de Auditoría (Evento de Dominio) para ISO
        \App\Models\EventStore::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'event_name' => 'role.approved',
            'aggregate_type' => 'roles',
            'aggregate_id' => $role->id,
            'organization_id' => $role->organization_id,
            'actor_id' => $request->approver_id,
            'payload' => [
                'digital_signature' => $role->digital_signature,
                'signed_at' => $role->signed_at,
                'version_id' => $roleVersion->id,
                'audit_standard' => 'ISO/IEC-9001:2015-Traceability'
            ],
            'occurred_at' => now(),
        ]);

        return [
            'status' => 'success',
            'message' => 'Rol aprobado, firmado y materializado exitosamente.',
            'role' => $role->name,
            'materialized' => $materializeResult['materialized'] ?? []
        ];
    }

    public function requestCompetencyApproval($competencyId, $approverId)
    {
        $competency = \App\Models\Competency::findOrFail($competencyId);

        return ApprovalRequest::create([
            'approvable_type' => \App\Models\Competency::class,
            'approvable_id' => $competency->id,
            'approver_id' => $approverId,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);
    }

    protected function finalizeCompetencyApproval(ApprovalRequest $request, array $data): array
    {
        $competency = $request->approvable;

        // Update competency with finalized data
        if (isset($data['name'])) {
            $competency->name = $data['name'];
        }
        if (isset($data['description'])) {
            $competency->description = $data['description'];
        }

        $competency->status = 'active';
        $competency->save();

        // Mark request as approved
        $request->status = 'approved';
        $request->seal();
        $request->save();

        // Seal the competency
        $competency->seal();
        $competency->save();

        // Also seal its skills
        foreach ($competency->skills as $skill) {
            $skill->status = 'active';
            $skill->seal();
            $skill->save();
        }

        // Crear versión inicial de la competencia
        $compVersion = \App\Models\CompetencyVersion::create([
            'organization_id' => $competency->organization_id,
            'competency_id' => $competency->id,
            'version_group_id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Versión Oficial V1.0',
            'description' => 'Versión inicial aprobada digitalmente por el responsable.',
            'effective_from' => now(),
            'evolution_state' => 'active',
            'metadata' => [
                'digital_signature' => $competency->digital_signature,
                'signed_at' => $competency->signed_at,
                'approver_id' => $request->approver_id,
                'competency_snapshot' => $competency->toArray()
            ],
            'created_by' => $request->approver_id
        ]);

        // Registrar Log de Auditoría (Evento de Dominio) para ISO
        \App\Models\EventStore::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'event_name' => 'competency.approved',
            'aggregate_type' => 'competencies',
            'aggregate_id' => $competency->id,
            'organization_id' => $competency->organization_id,
            'actor_id' => $request->approver_id,
            'payload' => [
                'digital_signature' => $competency->digital_signature,
                'signed_at' => $competency->signed_at,
                'version_id' => $compVersion->id,
                'audit_standard' => 'ISO/IEC-9001:2015-Traceability'
            ],
            'occurred_at' => now(),
        ]);

        return [
            'status' => 'success',
            'message' => 'Competencia aprobada, firmada y materializada exitosamente.',
            'competency' => $competency->name,
        ];
    }
}
