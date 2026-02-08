<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScenarioGeneration;
use App\Services\LLMClient;
use App\Services\RedactionService;

class RetryLatestGeneration extends Command
{
    protected $signature = 'debug:retry-generation {id?}';
    protected $description = 'Retry the latest failed scenario generation (or provide id) by invoking the LLM flow synchronously for debugging.';

    public function handle()
    {
        $id = $this->argument('id');

        if (empty($id)) {
            $generation = ScenarioGeneration::where('status', 'failed')->orderBy('id', 'desc')->first();
        } else {
            $generation = ScenarioGeneration::find($id);
        }

        if (! $generation) {
            $this->error('No failed generation found' . ($id ? " for id {$id}" : ''));
            return 1;
        }

        $this->info('Retrying generation id: '.$generation->id);

        $generation->status = 'processing';
        $generation->save();

        $llm = new LLMClient();

        try {
            $result = $llm->generate($generation->prompt ?? '');
            $rawResponse = $result['response'] ?? $result;

            $parsed = null;
            if (is_string($rawResponse)) {
                $parsed = json_decode($rawResponse, true);
            } elseif (is_array($rawResponse) || is_object($rawResponse)) {
                $parsed = is_array($rawResponse) ? $rawResponse : json_decode(json_encode($rawResponse), true);
            }

            $requiredKeys = ['scenario_metadata', 'capacities', 'competencies', 'skills', 'suggested_roles', 'impact_analysis'];
            $isValid = is_array($parsed);
            if ($isValid) {
                foreach ($requiredKeys as $key) {
                    if (! array_key_exists($key, $parsed)) { $isValid = false; break; }
                }
            }

            if (! $isValid) {
                $generation->status = 'failed';
                $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'invalid_llm_response', 'message' => 'LLM returned invalid or non-JSON response (retry)']);
                $generation->save();
                $this->error('LLM returned invalid/non-JSON response');
                $this->line(var_export($rawResponse, true));
                return 2;
            }

            $generation->llm_response = RedactionService::redactArray($parsed);
            $generation->confidence_score = $result['confidence'] ?? null;
            $generation->model_version = $result['model_version'] ?? null;
            $generation->generated_at = now();
            $generation->status = 'complete';
            $generation->save();

            $this->info('Generation completed successfully.');
            return 0;
        } catch (\Exception $e) {
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => 'exception', 'message' => $e->getMessage()]);
            $generation->save();
            $this->error('Exception during retry: '.$e->getMessage());
            return 3;
        }
    }
}
