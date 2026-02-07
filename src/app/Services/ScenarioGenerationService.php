<?php

namespace App\Services;

use App\Jobs\GenerateScenarioFromLLMJob;
use App\Models\ScenarioGeneration;
use App\Models\Organizations;
use App\Models\User;
use App\Services\RedactionService;

class ScenarioGenerationService
{
    public function preparePrompt(array $data, User $user, Organizations $org): string
    {
        // Minimal builder: merge template with provided data. Can be extended.
        $template = file_get_contents(base_path('docs/GUIA_GENERACION_ESCENARIOS.md')) ?: '';

        $replacements = array_merge($data, [
            'company_name' => $org->name ?? $data['company_name'] ?? '',
            'organization_id' => $org->id ?? null,
        ]);

        // Replace simple tokens {{key}} in the template
        $prompt = $template;
        foreach ($replacements as $k => $v) {
            $prompt = str_replace('{{' . $k . '}}', is_array($v) ? json_encode($v) : (string) $v, $prompt);
        }

        // Append operator answers as JSON at the end for clarity
        $prompt .= "\n\nOPERATOR_INPUT:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $prompt;
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
