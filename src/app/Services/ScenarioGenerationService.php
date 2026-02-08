<?php

namespace App\Services;

use App\Jobs\GenerateScenarioFromLLMJob;
use App\Models\Organizations;
use App\Models\ScenarioGeneration;
use App\Models\User;
use App\Models\PromptInstruction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ScenarioGenerationService
{
    public function preparePrompt(array $data, User $user, Organizations $org): string
    {
        // Minimal builder: merge template with provided data. Can be extended.
        $template = '';
        // Safely attempt to load template when app basePath is available
        if (function_exists('app') && is_callable([app(), 'basePath'])) {
            $templatePath = app()->basePath('docs/GUIA_GENERACION_ESCENARIOS.md');
            if (file_exists($templatePath)) {
                $template = @file_get_contents($templatePath) ?: '';
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
        $prompt .= "\n\nOPERATOR_INPUT:\n".json_encode($replacements, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Enforce JSON-only output from the LLM: add an explicit instruction
        $prompt .= "\n\nINSTRUCTIONS:\nReturn ONLY a single valid JSON object matching the schema with top-level keys: scenario_metadata, capacities, competencies, skills, suggested_roles, impact_analysis, confidence_score, assumptions. Do not include any prose, explanation or commentary outside the JSON object.\n";

        return $prompt;
    }

    /**
     * Compose prompt and include the operator instruction template.
     * Priority: client-provided instruction in payload > DB latest instruction > file fallback.
     * Returns array: ['prompt' => string, 'instruction' => ['content'=>string|null,'source'=>string,'language'=>string]]
     */
    public function composePromptWithInstruction(array $data, User $user, Organizations $org, string $lang = 'es', ?int $instructionId = null): array
    {
        $basePrompt = $this->preparePrompt($data, $user, $org);

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

        // Append instruction content to the prompt if present
        $prompt = $basePrompt;
        if (!empty($instructionContent)) {
            $prompt .= "\n\nOPERATOR_INSTRUCTION (source: {$instructionSource}, lang: {$language}):\n" . $instructionContent . "\n";
        }

        return ['prompt' => $prompt, 'instruction' => ['content' => $instructionContent, 'source' => $instructionSource, 'language' => $language]];
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
}
