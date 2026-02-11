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
            $prompt = str_replace('{{'.$k.'}}', is_array($v) ? json_encode($v) : (string) $v, $prompt);
        }

        // If template was empty, prepend minimal header with company name
        if (empty(trim($template))) {
            $prompt = 'Company: '.($replacements['company_name'] ?? '')."\n\n".$prompt;
        }

        // Append operator answers (use replacements so org overrides are visible)
        $operatorInputLabel = strtolower($lang) === 'es' ? 'ENTRADAS_OPERADOR' : 'OPERATOR_INPUT';
        $operatorInstructionLabel = strtolower($lang) === 'es' ? 'INSTRUCCION_OPERADOR' : 'OPERATOR_INSTRUCTION';
        $prompt .= "\n\n" . $operatorInputLabel . ":\n" . json_encode($replacements, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Enforce JSON-only output from the LLM: add an explicit instruction (language-specific)
        /* if (strtolower($lang) === 'es') {
            $prompt .= "\n\nINSTRUCCIONES:\nDevuelve SOLO un único objeto JSON válido que cumpla el esquema con claves de nivel superior: scenario_metadata, capabilities, competencies, skills, suggested_roles, impact_analysis, confidence_score, assumptions.\n";
            $prompt .= "El JSON DEBE usar la siguiente estructura anidada: cada elemento en 'capabilities' es un objeto con 'name' y opcional 'description' y un arreglo 'competencies'; cada competency es un objeto con 'name', opcional 'description' y un arreglo 'skills'; cada skill puede ser una cadena (nombre de habilidad) o un objeto con 'name'.\n";
        } else {
            $prompt .= "\n\nINSTRUCTIONS:\nReturn ONLY a single valid JSON object matching the schema with top-level keys: scenario_metadata, capabilities, competencies, skills, suggested_roles, impact_analysis, confidence_score, assumptions.\n";
            $prompt .= "The JSON MUST use the following nested structure: each element in 'capabilities' is an object with a 'name' and optional 'description' and a 'competencies' array; each competency is an object with 'name', optional 'description' and a 'skills' array; each skill may be a string (skill name) or object with 'name'.\n";
        } */

        // Purpose and concise definitions to guide the LLM (language-specific)
        /* if (strtolower($lang) === 'es') {
            $prompt .= "\nPROPÓSITO: Este escenario simula la gestión estratégica de talento para lograr el objetivo principal.\n";
            $prompt .= "DEFINICIÓN (ES):\n- Capacidades: medios/funciones organizacionales que permiten cumplir el objetivo del escenario.\n- Competencias: conocimientos y habilidades necesarias para ejecutar una capability.\n- Habilidades: unidad mínima (habilidades/conocimientos) que compone una competencia; puede ser texto o {\"name\":string}.\n- Roles: puestos propuestos con las competencias asignadas (el analista debe homologar estos roles con la estructura interna).\n\n";
            $prompt .= "NO incluyas ningún texto explicativo o comentario fuera del objeto JSON. Si no puedes producir la estructura anidada completa, devuelve un objeto con las claves y arreglos vacíos.\n\nEjemplo de salida mínima válida:\n";
        } else {
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
        $prompt .= "\n"; */
        // Append a formal JSON Schema to the instructions to help the LLM comply.
        // NOTE: The canonical, improved schema is also stored as a standalone
        // JSON file for operator/reference use at: docs/for_agent/prompt_schema_scenario.json
        // Title: "Stratos Talent Engineering Blueprint"
        // Keeping the embedded schema here for runtime prompt composition; keep in sync with the file above.
        /* $schemaArray = [
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
 */
     //   $prompt .= "\nJSON_SCHEMA:\n" . json_encode($schemaArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

        // Additional instruction: Talent Engineering focus (enforced guidance)
        if (strtolower($lang) === 'es') {
            $prompt .= <<<'EOT'

    EOT;
        }

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
            $instructionContent = (string) $data['instruction'];
            $instructionSource = 'client';
        } else {
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
                            } else {
                                Log::warning("Requested PromptInstruction id {$instructionId} language mismatch: expected {$language}, got {$byId->language}");
                            }
                        } else {
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
            } catch (\Throwable $e) {
                // If DB is not available or has issues, fall back to file
                Log::warning('PromptInstruction DB access failed: '.$e->getMessage());
            }
        }

        // 3) file fallback
        if (empty($instructionContent)) {
            $filePath = base_path('resources/prompt_instructions/default_'.$language.'.md');
            if (!file_exists($filePath)) {
                // try without language suffix
                $filePath = base_path('resources/prompt_instructions/default.md');
            }
            if (file_exists($filePath)) {
                $instructionContent = @file_get_contents($filePath) ?: null;
                $instructionSource = $instructionSource === 'none' ? 'file' : $instructionSource;
                Log::info('Using file fallback for prompt instruction: '.$filePath);
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
            } else {
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

        $generation = ScenarioGeneration::create([
            'organization_id' => $organizationId,
            'created_by' => $createdBy,
            'prompt' => $redactedPrompt,
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
                $existing = is_array($generation->metadata) ? $generation->metadata : (array) ($generation->metadata ?? []);
                $generation->metadata = array_merge($existing, $options['metadata']);
            }

            $generation->save();
        } catch (\Throwable $e) {
            Log::error('Failed to persist LLM response for generation ' . ($generation->id ?? 'unknown') . ': ' . $e->getMessage());
            throw $e;
        }

        return $generation;
    }
}
