<?php

namespace App\Jobs;

use App\Models\ScenarioGeneration;
use App\Services\LLMClient;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateScenarioFromLLMJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $generationId;

    public function __construct(int $generationId)
    {
        $this->generationId = $generationId;
    }

    public function handle(LLMClient $llm)
    {
        $generation = ScenarioGeneration::find($this->generationId);
        if (!$generation) {
            return;
        }

        $generation->status = 'processing';
        $generation->save();

        try {
            $result = $llm->generate($generation->prompt ?? '');

            $generation->llm_response = $result['response'] ?? $result;
            $generation->confidence_score = $result['confidence'] ?? null;
            $generation->model_version = $result['model_version'] ?? null;
            $generation->generated_at = now();
            $generation->status = 'complete';
            $generation->save();
        } catch (Exception $e) {
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => $e->getMessage()]);
            $generation->save();
        }
    }
}
