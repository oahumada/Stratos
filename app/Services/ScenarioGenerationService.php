<?php

namespace App\Services;

use App\Jobs\GenerateScenarioFromLLMJob;
use App\Models\Organizations;
use App\Models\ScenarioGeneration;
use App\Models\User;
use App\Models\PromptInstruction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Services\EmbeddingService;

class ScenarioGenerationService
{
    public function preparePrompt(array $data, User $user, Organizations $org, string $lang = 'es'): string
    {
        // Minimal builder: merge template with provided data. Can be extended.
        $template = '';
        // Safely attempt to load template when app basePath is available
        if (function_exists('app') && is_callable([app(), 'basePath'])) {
            // Prefer compact prompt templates used by the wizard (language-specific)
            $templateCandidates = [
                app()->basePath('resources/prompt_instructions/default_' . $lang . '.md'),
                //app()->basePath('resources/prompt_templates/abacus_modal_prompt.md'),
                //app()->basePath('docs/GUIA_GENERACION_ESCENARIOS.md'),
            ];
            foreach ($templateCandidates as $templatePath) {
                if ($templatePath && file_exists($templatePath)) {
                    $template = @file_get_contents($templatePath) ?: '';
                    break;
                }
            }
        }

        $replacements = array_merge($data, [
            'company_name' => $org->name ?? $data['company_name'] ?? '',
            'organization_id' => $org->id ?? null,
        ]);

        // Replace simple tokens {{key}} in the template
        $prompt = $template;
        foreach ($replacements as $k => $v) {
            $prompt = str_replace('{{' . $k . '}}', is_array($v) ? json_encode($v) : (string)$v, $prompt);
        }

        // If template was empty, prepend minimal header with company name
        if (empty(trim($template))) {
            $prompt = 'Company: ' . ($replacements['company_name'] ?? '') . "\n\n" . $prompt;
        }

        // Append operator answers (use replacements so org overrides are visible)
        $operatorInputLabel = strtolower($lang) === 'es' ? 'ENTRADAS_OPERADOR' : 'OPERATOR_INPUT';
        $operatorInstructionLabel = strtolower($lang) === 'es' ? 'INSTRUCCION_OPERADOR' : 'OPERATOR_INSTRUCTION';
        $prompt .= "\n\n" . $operatorInputLabel . ":\n" . json_encode($replacements, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Enforce JSON-only output from the LLM: add an explicit instruction (language-specific)
        if (strtolower($lang) === 'es') {
            $prompt .= "\n\nINSTRUCCIONES:\nDevuelve SOLO un único objeto JSON válido que cumpla el esquema con claves de nivel superior: scenario_metadata, capabilities, competencies, skills, suggested_roles, impact_analysis, confidence_score, assumptions.\n";
            $prompt .= "El JSON DEBE usar la siguiente estructura anidada: cada elemento en 'capabilities' es un objeto con 'name' y opcional 'description' y un arreglo 'competencies'; cada competency es un objeto con 'name', opcional 'description' y un arreglo 'skills'; cada skill puede ser una cadena (nombre de habilidad) o un objeto con 'name'.\n";
        }
        else {
            $prompt .= "\n\nINSTRUCTIONS:\nReturn ONLY a single valid JSON object matching the schema with top-level keys: scenario_metadata, capabilities, competencies, skills, suggested_roles, impact_analysis, confidence_score, assumptions.\n";
            $prompt .= "The JSON MUST use the following nested structure: each element in 'capabilities' is an object with a 'name' and optional 'description' and a 'competencies' array; each competency is an object with 'name', optional 'description' and a 'skills' array; each skill may be a string (skill name) or object with 'name'.\n";
        }

        // Purpose and concise definitions to guide the LLM (language-specific)
        if (strtolower($lang) === 'es') {
            $prompt .= "\nPROPÓSITO: Este escenario simula la gestión estratégica de talento para lograr el objetivo principal.\n";
            $prompt .= "DEFINICIÓN (ES):\n- Capacidades: medios/funciones organizacionales que permiten cumplir el objetivo del escenario.\n- Competencias: conocimientos y habilidades necesarias para ejecutar una capability.\n- Habilidades: unidad mínima (habilidades/conocimientos) que compone una competencia; puede ser texto o {\"name\":string}.\n- Roles: puestos propuestos con las competencias asignadas (el analista debe homologar estos roles con la estructura interna).\n\n";
            $prompt .= "NO incluyas ningún texto explicativo o comentario fuera del objeto JSON. Si no puedes producir la estructura anidada completa, devuelve un objeto con las claves y arreglos vacíos.\n\nEjemplo de salida mínima válida:\n";
        }
        else {
            $prompt .= "\nPURPOSE: This scenario simulates strategic talent management to achieve the main objective.\n";
            $prompt .= "DEFINITIONS (EN):\n- Capabilities: organizational means/functions that enable achieving the scenario objective.\n- Competencies: knowledge and abilities required to perform a capability.\n- Skills: the minimal unit (specific skills/knowledge) that composes a competency; may be a string or an object with {\"name\"}.\n- Roles: proposed positions with assigned competencies (analyst must later map/harmonize to internal roles).\n\n";
            $prompt .= "Do NOT include any prose, explanation or commentary outside the JSON object. If you cannot produce the full nested structure, return an object with the keys and empty arrays.\n\nExample minimal valid output:\n";
        }
        $prompt .= json_encode([
            'scenario_metadata' => [
                'name' => 'Example Scenario',
                'generated_at' => date('c'),
                'confidence_score' => 0.9,
            ],
            'capabilities' => [
                [
                    'name' => 'Capability A',
                    'description' => 'Short desc',
                    'competencies' => [
                        [
                            'name' => 'Competency X',
                            'description' => 'Desc',
                            'skills' => ['Skill 1', 'Skill 2']
                        ]
                    ]
                ]
            ],
            'competencies' => [],
            'skills' => [],
            'suggested_roles' => [],
            'impact_analysis' => [],
            'confidence_score' => 0.9,
            'assumptions' => []
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $prompt .= "\n";
        // Append a formal JSON Schema to the instructions to help the LLM comply.
        // NOTE: The canonical, improved schema is also stored as a standalone
        // JSON file for operator/reference use at: docs/for_agent/prompt_schema_scenario.json
        // Title: "Stratos Talent Engineering Blueprint"
        // Keeping the embedded schema here for runtime prompt composition; keep in sync with the file above.
        $schemaArray = [
            '$schema' => 'http://json-schema.org/draft-07/schema#',
            'type' => 'object',
            'required' => ['scenario_metadata'],
            'properties' => [
                'scenario_metadata' => [
                    'type' => 'object',
                    'required' => ['name'],
                    'properties' => [
                        'name' => ['type' => 'string'],
                        'generated_at' => ['type' => 'string', 'format' => 'date-time'],
                        'confidence_score' => ['type' => 'number']
                    ]
                ],
                'capabilities' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'competencies' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => ['name'],
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'description' => ['type' => 'string'],
                                        'skills' => [
                                            'type' => 'array',
                                            'items' => [
                                                'oneOf' => [
                                                    ['type' => 'string'],
                                                    ['type' => 'object', 'required' => ['name'], 'properties' => ['name' => ['type' => 'string']]]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'competencies' => ['type' => 'array'],
                'skills' => ['type' => 'array'],
                'suggested_roles' => ['type' => 'array'],
                'impact_analysis' => ['type' => 'array'],
                'confidence_score' => ['type' => 'number'],
                'assumptions' => ['type' => 'array'],
                'roles' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['name'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'description' => ['type' => 'string'],
                            'competencies' => [
                                'type' => 'array',
                                'items' => [
                                    'oneOf' => [
                                        ['type' => 'string'],
                                        ['type' => 'object', 'required' => ['name'], 'properties' => ['name' => ['type' => 'string']]]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ];

        $prompt .= "\nJSON_SCHEMA:\n" . json_encode($schemaArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";


        // Additional instruction: Talent Engineering focus (enforced guidance)
        $prompt .= <<<'EOT'
        JSON SCHEMA (resumen)
        
        Incluye el siguiente esquema JSON simplificado para validar la estructura anidada requerida
        (`capabilities` → `competencies` → `skills`):
        
        {
            "$schema": "http://json-schema.org/draft-07/schema#",
            "type": "object",
            "required": ["scenario_metadata"],
            "properties": {
                "scenario_metadata": { "type": "object", "required": ["name"] },
                "capabilities": { "type": "array" },
                "competencies": { "type": "array" },
                "skills": { "type": "array" ,},
                "suggested_roles": { "type": "array" }
            }
        }

    EJEMPLO AVANZADO (incluye composición de talento):

    {
        "scenario_metadata": {
            "name": "Transformación Digital 2026",
            "generated_at": "2026-02-10T00:00:00Z",
            "confidence_score": 0.92
        },
        "capabilities": [
            {
                "id": "CAP-03",
                "name": "Tecnología y Datos",
                "description": "Seleccionar y operar plataformas tecnológicas seguras, escalables y orientadas a datos.",
                "competencies": [
                    {
                        "id": "CAP-03-C1",
                        "name": "Arquitectura y Nube",
                        "description": "Diseñar soluciones modulares y operar cargas en la nube",
                        "skills": [
                            {
                                "id": "CAP-03-C1-S1",
                                "name": "Modelado de arquitectura",
                                "description": "Definir componentes e integraciones"
                            }
                        ]
                    }
                ]
            }
        ],
        "suggested_roles": [
            {
                "name": "Ingeniero de Talento",
                "description": "Diseña y optimiza sistemas de capacidades híbridas",
                "estimated_fte": 1.0,
                "key_competencies": [
                    "Modelado de Datos",
                    "Visualización de Insights"
                ],
                "talent_composition": {
                    "human_percentage": 40,
                    "synthetic_percentage": 60,
                    "strategy_suggestion": "Hybrid",
                    "logic_justification": "La IA procesa modelos y genera dashboards; el humano diseña la estrategia y valida hipótesis."
                },
                "suggested_agent_type": "Orquestador de Datos y Analytics"
            }
        ],
        "impact_analysis": [
            {
                "area": "Productividad",
                "impact": "alto",
                "notes": "Reducción de ciclos de planificación en 40%"
            }
        ],
        "confidence_score": 0.92,
        "assumptions": ["Disponibilidad de APIs de datos históricos"]
    }


    INSTRUCCIÓN PARA EL MODELO (ENFOQUE INGENIERÍA DE TALENTO):
    - Actúa como un Ingeniero de Talento Estratégico. Tu objetivo es diseñar un plano (blueprint) de capacidades híbridas.
    - Por cada rol en `suggested_roles`, DEBES incluir obligatoriamente el objeto `talent_composition`:
        - `human_percentage`: % de carga de trabajo que requiere juicio humano, empatía o liderazgo (0-100).
        - `synthetic_percentage`: % de carga de trabajo delegable a agentes IA o automatización (0-100).
        - `strategy_suggestion`: Elige la mejor estrategia de cobertura: ["Buy", "Build", "Borrow", "Synthetic", "Hybrid"].
        - `logic_justification`: Breve explicación de por qué ese mix (ej: "Alta carga de procesamiento de datos permite 70% IA").

    - En `impact_analysis`, evalúa cómo la introducción de "Talento Sintético" (IA) mejora la eficiencia de la capacidad analizada.

    Mantén el resto de los requisitos de formato JSON y las claves: `scenario_metadata`, `capabilities`, `competencies`, `skills`, `suggested_roles`, `impact_analysis`, `confidence_score`, `assumptions`.

    REQUISITOS OBLIGATORIOS POR ROL:

    1. `name`: Nombre del rol estratégico.
    2. `description`: Descripción breve del rol.
    3. `key_competencies`: Array de nombres o IDs de competencias asociadas (strings).
    4. `estimated_fte`: Cantidad de personas equivalentes necesarias (número).
    5. `talent_composition`: Objeto JSON que DEBE contener:
        - `human_percentage`: (0-100) % de carga de trabajo humana.
        - `synthetic_percentage`: (0-100) % de carga delegable a IA.
        - `strategy_suggestion`: ["Buy", "Build", "Borrow", "Synthetic", "Hybrid"].
        - `logic_justification`: Explicación técnica del mix.
    6. `suggested_agent_type`: Tipo de IA necesaria si aplica.

    REGLAS DE NEGOCIO PARA EL MODELO:

    - Si el rol es altamente transaccional o de procesamiento de datos, el
    `synthetic_percentage` debe ser alto (>60%).
    - Si el rol es de alta gestión o cuidado de personas, el `human_percentage`
    debe ser dominante (>80%).
    - Usa "Synthetic" solo si el rol es 100% IA. Usa "Hybrid" para colaboración
    humano-máquina.
    - La suma de `human_percentage` y `synthetic_percentage` debe ser siempre 100.
    - La `strategy_suggestion` debe corresponder a la factibilidad técnica y de
    negocio indicada en `logic_justification`.
    - `Catálogos completos`: Los arrays `competencies` y `skills` de primer nivel
    deben contener TODAS las competencias y skills mencionadas en `capabilities`,
    sin duplicados.

    ADICIONALES / GUÍAS PRÁCTICAS:

    - Prioriza roles prácticos y mapeables a estructuras organizacionales típicas
    (ej. Analista, Líder de Producto, Ingeniero de Datos).
    - Cuando propongas `suggested_roles`, incluye `name`, `estimated_fte` y
    `key_competencies` (lista de nombres o IDs).
    - En `impact_analysis` incluye 2-3 ítems con `area`, `impact` (alto/medio/bajo)
    y `notes` que conecten con las iniciativas recomendadas.


NOTAS DE USO Y SEGURIDAD

- El prompt puede incluir `{{company_name}}` y otros tokens; reemplázalos
  antes de enviar la petición al LLM.
- Si el sistema detecta instrucción conflictiva (p. ej. solicita Markdown cuando
  el prompt exige JSON), devolver un error de validación y pedir corrección.
- Mantener registros de prompts y respuestas para auditoría, preferiblemente
  almacenando la versión original cifrada y una versión redactada para
  operaciones (ver política interna).

---

# FORMATO DE SALIDA (RECORDATORIO FINAL):

Devuelve exclusivamente el objeto JSON siguiendo el esquema validado. No añadas
introducciones ni cierres.
EOT;

        return $prompt;
    }

    /**
     * Compose prompt and include the operator instruction template.
     * Priority: client-provided instruction in payload > DB latest instruction > file fallback.
     * Returns array: ['prompt' => string, 'instruction' => ['content'=>string|null,'source'=>string,'language'=>string]]
     */
    public function composePromptWithInstruction(array $data, User $user, Organizations $org, string $lang = 'es', ?int $instructionId = null): array
    {
        $basePrompt = $this->preparePrompt($data, $user, $org, $lang);

        $instructionContent = null;
        $instructionSource = 'none';
        $language = $lang;

        // 1) client-provided instruction
        if (!empty($data['instruction'])) {
            $instructionContent = (string)$data['instruction'];
            $instructionSource = 'client';
        }
        else {
            // 2) DB lookup if table exists
            try {
                if (Schema::hasTable((new PromptInstruction())->getTable())) {
                    // If a specific instruction id was provided, prefer it (if exists and language matches)
                    if (!empty($instructionId)) {
                        $byId = PromptInstruction::find($instructionId);
                        if ($byId) {
                            // only accept if language matches requested language, otherwise ignore id
                            if (empty($byId->language) || $byId->language === $language) {
                                $instructionContent = $byId->content;
                                $instructionSource = 'db_id';
                            }
                            else {
                                Log::warning("Requested PromptInstruction id {$instructionId} language mismatch: expected {$language}, got {$byId->language}");
                            }
                        }
                        else {
                            Log::warning("Requested PromptInstruction id {$instructionId} not found");
                        }
                    }

                    // If not resolved by id, fallback to latest by language
                    if (empty($instructionContent)) {
                        $row = PromptInstruction::where('language', $language)->orderBy('created_at', 'desc')->first();
                        if ($row) {
                            $instructionContent = $row->content;
                            $instructionSource = 'db';
                        }
                    }
                }
            }
            catch (\Throwable $e) {
                // If DB is not available or has issues, fall back to file
                Log::warning('PromptInstruction DB access failed: ' . $e->getMessage());
            }
        }

        // 3) file fallback
        if (empty($instructionContent)) {
            $filePath = base_path('resources/prompt_instructions/default_' . $language . '.md');
            if (!file_exists($filePath)) {
                // try without language suffix
                $filePath = base_path('resources/prompt_instructions/default.md');
            }
            if (file_exists($filePath)) {
                $instructionContent = @file_get_contents($filePath) ?: null;
                $instructionSource = $instructionSource === 'none' ? 'file' : $instructionSource;
                Log::info('Using file fallback for prompt instruction: ' . $filePath);
            }
        }

        // Validate compatibility between base prompt instructions and operator instruction
        if (!empty($instructionContent)) {
            $conflict = $this->validateInstructionCompatibility($basePrompt, $instructionContent);
            if ($conflict !== null) {
                throw ValidationException::withMessages(['instruction' => [$conflict]]);
            }
        }

        $prompt = $basePrompt;

        // Avoid duplicating instructions: if the base prompt already contains
        // embedded model instructions or an instruction block, do not append
        // the operator-provided instruction to prevent repeated guidance.
        $alreadyHasInstruction = false;
        $instructionMarkers = [
            'INSTRUCCIONES',
            'INSTRUCTIONS',
            'INSTRUCCIÓN PARA EL MODELO',
            'INSTRUCTION FOR THE MODEL',
            'INSTRUCCIÓN PARA EL MODELO (ENFOQUE INGENIERÍA DE TALENTO)',
            'INSTRUCTION FOR THE MODEL (TALENT ENGINEERING FOCUS)',
        ];
        foreach ($instructionMarkers as $m) {
            if (stripos($basePrompt, $m) !== false) {
                $alreadyHasInstruction = true;
                break;
            }
        }

        // language-specific label for operator instruction when appending
        $operatorInstructionLabel = strtolower($language) === 'es' ? 'INSTRUCCION_OPERADOR' : 'OPERATOR_INSTRUCTION';
        if (!empty($instructionContent)) {
            if ($alreadyHasInstruction) {
                // mark source as embedded so callers can see we skipped appending
                $instructionSource = $instructionSource . '_embedded';
                Log::info("Skipping append of operator instruction because base prompt already contains instructions for lang {$language} (source: {$instructionSource})");
            }
            else {
                $prompt .= "\n\n" . $operatorInstructionLabel . " (source: {$instructionSource}, lang: {$language}):\n" . $instructionContent . "\n";
            }
        }

        return ['prompt' => $prompt, 'instruction' => ['content' => $instructionContent, 'source' => $instructionSource, 'language' => $language]];
    }

    /**
     * Validate compatibility between the base prompt's instructions and an operator instruction.
     * Returns null when compatible, or a string message describing the conflict.
     */
    protected function validateInstructionCompatibility(string $basePrompt, string $instructionContent): ?string
    {
        // If base prompt explicitly requires JSON-only output, detect operator requests for Markdown
        $baseRequiresJson = stripos($basePrompt, 'Return ONLY a single valid JSON') !== false || stripos($basePrompt, 'Formato: JSON') !== false || stripos($basePrompt, 'Format: JSON') !== false;

        if ($baseRequiresJson) {
            // operator requests markdown -> conflict
            if (preg_match('/Formato:\s*Markdown/i', $instructionContent) || preg_match('/Format:\s*Markdown/i', $instructionContent) || stripos($instructionContent, 'Formato: Markdown') !== false || stripos($instructionContent, 'Format: Markdown') !== false) {
                return 'Operator instruction requests Markdown output but the system prompt requires JSON-only output. Edit the instruction to request JSON.';
            }
        }

        return null;
    }

    public function enqueueGeneration(string $prompt, int $organizationId, ?int $createdBy = null, array $metadata = []): ScenarioGeneration
    {
        // Redact prompt before persisting to avoid storing secrets/PII
        $redactedPrompt = RedactionService::redactText($prompt);

        $encryptedRaw = null;
        try {
            $encryptedRaw = Crypt::encryptString($prompt);
        }
        catch (\Throwable $e) {
            Log::warning('Failed to encrypt raw prompt before persisting: ' . $e->getMessage());
        }

        $generation = ScenarioGeneration::create([
            'organization_id' => $organizationId,
            'created_by' => $createdBy,
            'prompt' => $redactedPrompt,
            'raw_prompt' => $encryptedRaw,
            'metadata' => $metadata,
            'status' => 'queued',
            'redacted' => true,
        ]);

        GenerateScenarioFromLLMJob::dispatch($generation->id);

        return $generation;
    }

    /**
     * Persist the final LLM response for a ScenarioGeneration and mark it complete.
     * Centralized method so other callers (scripts, jobs, tests) can reuse the logic.
     *
     * @param ScenarioGeneration $generation
     * @param array|string $response
     * @param array $options optional keys: status, metadata
     * @return ScenarioGeneration
     */
    public function persistLLMResponse(ScenarioGeneration $generation, $response, array $options = []): ScenarioGeneration
    {
        try {
            $generation->llm_response = $response;
            $generation->status = $options['status'] ?? 'complete';

            if (!empty($options['metadata']) && is_array($options['metadata'])) {
                $existing = is_array($generation->metadata) ? $generation->metadata : (array)($generation->metadata ?? []);
                $generation->metadata = array_merge($existing, $options['metadata']);
            }

            $generation->save();
        }
        catch (\Throwable $e) {
            Log::error('Failed to persist LLM response for generation ' . ($generation->id ?? 'unknown') . ': ' . $e->getMessage());
            throw $e;
        }

        return $generation;
    }

    /**
     * Finalize the scenario by importing the LLM response into the relational database.
     * This creates/updates Capabilities, Competencies, and Skills, and links them to the Scenario.
     *
     * @param ScenarioGeneration $generation
     * @return array Summary of imported entities
     */
    public function finalizeScenarioImport(ScenarioGeneration $generation): array
    {
        $data = $generation->llm_response;
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (!$data || !is_array($data)) {
            throw new \Exception("Invalid LLM response data for import.");
        }

        return \DB::transaction(function () use ($generation, $data) {
            $orgId = $generation->organization_id;

            // 1. Resolve or Create Scenario
            $scenario = $generation->scenario;
            if (!$scenario) {
                $meta = $data['escenario'] ?? $data['scenario_metadata'] ?? [];
                $scenario = \App\Models\Scenario::create([
                    'organization_id' => $orgId,
                    'name' => $meta['nombre'] ?? $meta['name'] ?? 'Generated Scenario ' . now()->toDateTimeString(),
                    'description' => $meta['descripcion'] ?? $meta['description'] ?? 'Imported from LLM analysis',
                    'status' => 'draft',
                    'horizon_months' => $meta['horizon_months'] ?? ($meta['planning_horizon_months'] ?? 12),
                    'fiscal_year' => $meta['fiscal_year'] ?? (int)date('Y'),
                    'source_generation_id' => $generation->id,
                    'created_by' => $generation->created_by,
                    'owner_user_id' => $generation->created_by,
                ]);
                $generation->update(['scenario_id' => $scenario->id]);

                // Generate embedding for scenario
                if (config('features.generate_embeddings', false)) {
                    try {
                        $embeddingService = app(EmbeddingService::class);
                        $embedding = $embeddingService->forScenario($scenario);

                        if ($embedding) {
                            $vectorStr = $embeddingService->toVectorString($embedding);
                            DB::update(
                                "UPDATE scenarios SET embedding = ?::vector WHERE id = ?",
                                [$vectorStr, $scenario->id]
                            );
                        }
                    } catch (\Exception $e) {
                         Log::warning("Embedding generation failed for scenario {$scenario->id}: " . $e->getMessage());
                    }
                }
            }

            $stats = ['capabilities' => 0, 'competencies' => 0, 'skills' => 0, 'blueprints' => 0];

            // 2. Process Hierarchical Tree: Capabilities -> Competencies -> Skills
            $caps = $data['capabilities'] ?? [];
            foreach ($caps as $capData) {
                $capability = \App\Models\Capability::updateOrCreate(
                    [
                        'organization_id' => $orgId,
                        'llm_id' => $capData['id'] ?? null,
                        'name' => $capData['nombre'] ?? $capData['name'],
                    ],
                    [
                        'description' => $capData['descripcion'] ?? $capData['description'] ?? null,
                        'status' => 'in_incubation',
                        'discovered_in_scenario_id' => $scenario->id,
                    ]
                );

                // Link Capability to Scenario (Pivot)
                $scenario->capabilities()->syncWithoutDetaching([$capability->id => [
                    'strategic_role' => 'target',
                    'strategic_weight' => 10,
                    'priority' => 1,
                    'required_level' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]]);
                $stats['capabilities']++;

                // Generate embedding for capability
                if (config('features.generate_embeddings', false)) {
                    try {
                        $embeddingService = app(EmbeddingService::class);
                        $embedding = $embeddingService->forCapability($capability);
                        
                        if ($embedding) {
                            $vectorStr = $embeddingService->toVectorString($embedding);
                            DB::update(
                                "UPDATE capabilities SET embedding = ?::vector WHERE id = ?",
                                [$vectorStr, $capability->id]
                            );
                            
                            // Find similar capabilities
                            $similar = $embeddingService->findSimilar('capabilities', $embedding, 3, $orgId);
                            if (!empty($similar) && $similar[0]->similarity > 0.90) {
                                Log::info("Similar capability found for '{$capability->name}'", [
                                    'similar_to' => $similar[0]->name,
                                    'similarity' => round($similar[0]->similarity, 3),
                                ]);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning("Embedding generation failed for capability {$capability->id}: " . $e->getMessage());
                    }
                }

                // Process Competencies
                $comps = $capData['competencies'] ?? [];
                foreach ($comps as $compData) {
                    $competency = \App\Models\Competency::updateOrCreate(
                        [
                            'organization_id' => $orgId,
                            'llm_id' => $compData['id'] ?? null,
                            'name' => $compData['nombre'] ?? $compData['name'],
                        ],
                        [
                            'description' => $compData['descripcion'] ?? $compData['description'] ?? null,
                            'status' => 'in_incubation',
                            'discovered_in_scenario_id' => $scenario->id,
                        ]
                    );

                    // Link Competency to Capability specialized for this scenario
                    $capability->competencies()->syncWithoutDetaching([$competency->id => [
                        'scenario_id' => $scenario->id,
                        'required_level' => 3,
                        'weight' => 10,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]]);
                    $stats['competencies']++;

                    // Generate embedding for competency
                    if (config('features.generate_embeddings', false)) {
                        try {
                            $embeddingService = app(EmbeddingService::class);
                            $embedding = $embeddingService->forCompetency($competency);
                            
                            if ($embedding) {
                                $vectorStr = $embeddingService->toVectorString($embedding);
                                DB::update(
                                    "UPDATE competencies SET embedding = ?::vector WHERE id = ?",
                                    [$vectorStr, $competency->id]
                                );
                            }
                        } catch (\Exception $e) {
                            Log::warning("Embedding generation failed for competency {$competency->id}");
                        }
                    }

                    // Process Skills
                    $skills = $compData['skills'] ?? [];
                    foreach ($skills as $skillData) {
                        $skillName = is_array($skillData) ? ($skillData['nombre'] ?? $skillData['name']) : $skillData;
                        $skillLlmId = is_array($skillData) ? ($skillData['id'] ?? null) : null;
                        $skillDesc = is_array($skillData) ? ($skillData['descripcion'] ?? $skillData['description'] ?? null) : null;

                        $skill = \App\Models\Skill::updateOrCreate(
                            [
                                'organization_id' => $orgId,
                                'llm_id' => $skillLlmId,
                                'name' => $skillName,
                            ],
                            [
                                'description' => $skillDesc,
                                'category' => $capability->name,
                                'status' => 'in_incubation',
                                'discovered_in_scenario_id' => $scenario->id,
                                'scope_type' => 'domain',
                            ]
                        );

                        // Link Skill to Competency
                        $competency->skills()->syncWithoutDetaching([$skill->id => [
                            'weight' => 10,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]]);
                        $stats['skills']++;

                        // Generate embedding for skill
                        if (config('features.generate_embeddings', false)) {
                            try {
                                $embeddingService = app(EmbeddingService::class);
                                $embedding = $embeddingService->forSkill($skill);
                                
                                if ($embedding) {
                                    $vectorStr = $embeddingService->toVectorString($embedding);
                                    DB::update(
                                        "UPDATE skills SET embedding = ?::vector WHERE id = ?",
                                        [$vectorStr, $skill->id]
                                    );
                                }
                            } catch (\Exception $e) {
                                Log::warning("Embedding generation failed for skill {$skill->id}");
                            }
                        }
                    }
                }
            }

            // 3. Process Strategic Roles (Roles & Talent Blueprints) if present
            $suggestedRoles = $data['suggested_roles'] ?? [];
            if (!empty($suggestedRoles)) {
                $stats['roles'] = 0;
                foreach ($suggestedRoles as $roleData) {
                    $role = \App\Models\Roles::updateOrCreate(
                        [
                            'organization_id' => $orgId,
                            'name' => $roleData['name'],
                        ],
                        [
                            'description' => $roleData['description'] ?? null,
                            'status' => 'in_incubation',
                            'discovered_in_scenario_id' => $scenario->id,
                        ]
                    );
                    $stats['roles']++;

                    // Generate embedding for role
                    if (config('features.generate_embeddings', false)) {
                        try {
                            $embeddingService = app(EmbeddingService::class);
                            $embedding = $embeddingService->forRole($role);
                            
                            if ($embedding) {
                                $vectorStr = $embeddingService->toVectorString($embedding);
                                DB::update(
                                    "UPDATE roles SET embedding = ?::vector WHERE id = ?",
                                    [$vectorStr, $role->id]
                                );
                                
                                // Find similar roles
                                $similar = $embeddingService->findSimilar('roles', $embedding, 3, $orgId);
                                if (!empty($similar) && $similar[0]->similarity > 0.85) {
                                    Log::info("Similar role found for '{$role->name}'", [
                                        'similar_to' => $similar[0]->name,
                                        'similarity' => round($similar[0]->similarity, 3),
                                    ]);
                                }
                            }
                        } catch (\Exception $e) {
                            Log::warning("Embedding generation failed for role {$role->id}: " . $e->getMessage());
                        }
                    }

                    // Link Role to Scenario (Pivot)
                    DB::table('scenario_roles')->updateOrInsert(
                        ['scenario_id' => $scenario->id, 'role_id' => $role->id],
                        [
                            'fte' => $roleData['estimated_fte'] ?? 1,
                            'rationale' => $roleData['talent_composition']['logic_justification'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    // 3.1 Link Roles to their specified Competencies
                    $roleCompetencies = $roleData['key_competencies'] ?? ($roleData['required_competencies'] ?? []);
                    foreach ($roleCompetencies as $compName) {
                        $competency = \App\Models\Competency::where('organization_id', $orgId)
                            ->where('name', $compName)
                            ->first();
                        
                        if ($competency) {
                            DB::table('scenario_role_competencies')->updateOrInsert(
                                [
                                    'scenario_id' => $scenario->id,
                                    'role_id' => $role->id,
                                    'competency_id' => $competency->id
                                ],
                                [
                                    'required_level' => 4, // Default high required level for strategic roles
                                    'importance_weight' => 10,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );
                        }
                    }
                }

                if (class_exists(\App\Services\TalentBlueprintService::class)) {
                    try {
                        $blueprintSvc = app(\App\Services\TalentBlueprintService::class);
                        $blueprintSvc->createFromLlmResponse($scenario, $suggestedRoles);
                        $stats['blueprints'] = count($suggestedRoles);
                    } catch (\Throwable $e) {
                        \Log::warning('Failed incrementally creating TalentBlueprints: ' . $e->getMessage());
                    }
                }
            }

            \Log::info("Scenario generation finalized and imported for scenario ID: {$scenario->id}", $stats);

            return [
                'success' => true,
                'scenario_id' => $scenario->id,
                'stats' => $stats
            ];
        });
    }
}
