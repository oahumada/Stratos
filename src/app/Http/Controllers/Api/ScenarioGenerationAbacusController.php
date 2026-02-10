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

        // Instead of performing streaming synchronously in the controller,
        // enqueue a background job via ScenarioGenerationService so the UI can
        // poll progress and assemble chunks. Preserve provider metadata so the
        // job uses `AbacusClient` when appropriate.

        // Accept optional provider options from request (e.g., timeouts)
        $providerOptions = $request->only(['timeout', 'stream_idle_timeout', 'expected_total_chunks', 'expected_total_bytes']);
        // Ensure concrete model when not provided
        $model = config('services.abacus.model');
        if (empty($model) || $model === 'abacus-default') {
            $model = 'gpt-5';
        }
        $providerOptions = array_merge(['overrides' => ['model' => $model]], array_filter($providerOptions, function ($v) { return $v !== null; }));

        $generation->metadata = array_merge($generation->metadata ?? [], ['provider' => 'abacus', 'provider_options' => $providerOptions, 'used_instruction' => $instructionMeta]);
        $generation->status = 'queued';
        $generation->save();

        // Dispatch job via service to keep enqueueing behavior consistent
        $svc->enqueueGeneration($prompt, $orgId, $user->id, $generation->metadata ?? []);

        return response()->json(['success' => true, 'data' => ['id' => $generation->id, 'status' => $generation->status]], 202);
    }
}
