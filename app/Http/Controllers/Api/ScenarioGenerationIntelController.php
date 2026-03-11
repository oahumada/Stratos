<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organizations;
use App\Services\ScenarioGenerationService;
use Illuminate\Http\Request;

class ScenarioGenerationIntelController extends Controller
{
    /**
     * Generate a scenario using the Python Intelligence Service (DeepSeek/GPT).
     */
    public function generate(Request $request, ScenarioGenerationService $svc)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $rules = [
            'company_name' => 'sometimes|string|max:255',
            'industry' => 'sometimes|string|max:255',
            'company_size' => 'sometimes|string|max:255', // Wizard sends it
            'current_challenges' => 'sometimes|string',
            'current_capabilities' => 'sometimes|string',
            'current_gaps' => 'sometimes|string',
            'strategic_goal' => 'sometimes|string',
            'key_initiatives' => 'sometimes|string',
            'time_horizon' => 'sometimes|string',
            'milestones' => 'sometimes|string',
            'organization_id' => 'sometimes|integer',
            'instruction' => 'sometimes|string',
            'instruction_id' => 'sometimes|integer',
            'instruction_language' => 'sometimes|string|in:es,en',
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

        $lang = $payload['instruction_language'] ?? 'es';

        // Compose prompt using existing service to preserve instruction handling
        try {
            $composed = $svc->composePromptWithInstruction($payload, $user, $org, $lang, $payload['instruction_id'] ?? null);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid instruction', 'errors' => $e->errors()], 422);
        }

        $prompt = $composed['prompt'] ?? ($payload['instruction'] ?? '');
        $instructionMeta = $composed['instruction'] ?? null;

        // Delegate entire creation and enqueueing to the service
        $generation = $svc->enqueueGeneration($prompt, $orgId, $user->id, [
            'provider' => 'intel',
            'company_name' => $org->name,
            'language' => $lang,
            'used_instruction' => $instructionMeta,
        ]);

        return response()->json(['success' => true, 'data' => ['id' => $generation->id, 'status' => $generation->status]], 202);
    }
}
