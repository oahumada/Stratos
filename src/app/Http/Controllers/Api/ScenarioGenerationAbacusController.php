<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GenerationChunk;
use App\Models\Organizations;
use App\Models\ScenarioGeneration;
use App\Services\AbacusClient;
use App\Services\RedactionService;
use App\Services\ScenarioGenerationService;
use Illuminate\Http\Request;

class ScenarioGenerationAbacusController extends Controller
{
    public function generate(Request $request, ScenarioGenerationService $svc, AbacusClient $abacus)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $rules = [
            'company_name' => 'sometimes|string|max:255',
            'organization_id' => 'sometimes|integer',
            'instruction' => 'sometimes|string',
            'instruction_id' => 'sometimes|integer',
        ];

        $payload = $request->validate($rules);

        $orgId = $payload['organization_id'] ?? ($user->organization_id ?? null);
        if (! $orgId) {
            return response()->json(['success' => false, 'message' => 'organization_id is required'], 422);
        }

        $org = Organizations::find($orgId);
        if (! $org) {
            return response()->json(['success' => false, 'message' => 'Organization not found'], 404);
        }

        // Compose prompt using existing service to preserve instruction handling
        try {
            $composed = $svc->composePromptWithInstruction($payload, $user, $org, $payload['instruction_language'] ?? 'es', $payload['instruction_id'] ?? null);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid instruction', 'errors' => $e->errors()], 422);
        }

        $prompt = $composed['prompt'] ?? ($payload['instruction'] ?? '');
        $instructionMeta = $composed['instruction'] ?? null;

        // Create generation tracker row
        $generation = ScenarioGeneration::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'prompt' => RedactionService::redactText($prompt),
            'status' => 'processing',
            'metadata' => ['provider' => 'abacus', 'model' => config('services.abacus.model'), 'used_instruction' => $instructionMeta],
        ]);

        // Stream from Abacus and persist chunks incrementally
        $assembled = '';
        $seq = 1;
        $buffer = '';
        $maxBuffer = 128; // bytes
        $flushInterval = 0.20; // seconds
        $lastFlush = microtime(true);

        try {
            $result = $abacus->generateStream($prompt, ['max_tokens' => 1200, 'temperature' => 0.1], function ($delta) use (&$assembled, &$seq, $generation, &$buffer, $maxBuffer, &$lastFlush, $flushInterval) {
                $assembled .= $delta;
                $buffer .= $delta;
                $now = microtime(true);
                $shouldFlushBySize = strlen($buffer) >= $maxBuffer;
                $shouldFlushByTime = ($now - ($lastFlush ?? 0)) >= $flushInterval;
                if ($shouldFlushBySize || $shouldFlushByTime) {
                    try {
                        GenerationChunk::create([
                            'scenario_generation_id' => $generation->id,
                            'sequence' => $seq++,
                            'chunk' => $buffer,
                        ]);
                    } catch (\Throwable $e) {
                        // log but don't interrupt streaming
                        \Log::error('Failed to persist chunk: '.$e->getMessage(), ['generation_id' => $generation->id]);
                    }
                    $buffer = '';
                    $lastFlush = microtime(true);
                }
            });

            // flush remaining buffer
            if (! empty($buffer)) {
                try {
                    GenerationChunk::create([
                        'scenario_generation_id' => $generation->id,
                        'sequence' => $seq++,
                        'chunk' => $buffer,
                    ]);
                } catch (\Throwable $e) {
                    \Log::error('Failed to persist final chunk: '.$e->getMessage(), ['generation_id' => $generation->id]);
                }
            }

            // Interpret result returned by AbacusClient::generateStream()
            if ((is_array($result) && array_key_exists('escenario', $result)) || (is_array($result) && array_key_exists('scenario_metadata', $result))) {
                $resp = $result;
            } else {
                $decoded = json_decode($assembled, true);
                $resp = is_array($decoded) ? $decoded : ['content' => $assembled];
            }

            // Persist final response and mark complete
            $generation->llm_response = $resp;
            $generation->status = 'complete';
            $generation->model_version = config('services.abacus.model');
            // optional: extract confidence if present
            if (is_array($resp) && isset($resp['scenario_metadata']['confidence_score'])) {
                $generation->confidence_score = $resp['scenario_metadata']['confidence_score'];
            }
            $generation->save();

            return response()->json(['success' => true, 'data' => ['id' => $generation->id, 'status' => $generation->status]], 200);
        } catch (\Throwable $e) {
            \Log::error('Abacus generation failed: '.$e->getMessage(), ['generation_id' => $generation->id]);
            $generation->status = 'failed';
            $generation->metadata = array_merge($generation->metadata ?? [], ['error' => $e->getMessage()]);
            $generation->save();
            return response()->json(['success' => false, 'message' => 'LLM generation failed', 'error' => $e->getMessage()], 500);
        }
    }
}
