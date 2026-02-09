<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GenerationChunk;
use App\Models\ScenarioGeneration;

class GenerationChunkController extends Controller
{
    public function index(Request $request, $generationId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $gen = ScenarioGeneration::find($generationId);
        if (! $gen) {
            return response()->json(['success' => false, 'message' => 'Generation not found'], 404);
        }

        // TODO: multi-tenant check: ensure user can view this generation (org match)
        $chunks = GenerationChunk::where('scenario_generation_id', $generationId)
            ->orderBy('sequence')
            ->get(['sequence', 'chunk', 'created_at']);

        return response()->json(['success' => true, 'data' => $chunks]);
    }

    public function compacted(Request $request, $generationId)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $gen = ScenarioGeneration::find($generationId);
        if (! $gen) {
            return response()->json(['success' => false, 'message' => 'Generation not found'], 404);
        }

        if ($gen->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        // If compacted blob exists in metadata, return decoded content
        $meta = $gen->metadata ?? [];
        if (! empty($meta['compacted'])) {
            $decoded = base64_decode($meta['compacted']);
            $json = @json_decode($decoded, true);
            if (is_array($json)) {
                return response()->json(['success' => true, 'data' => $json]);
            }

            // Return as raw string inside data for consistency
            return response()->json(['success' => true, 'data' => $decoded]);
        }

        // Fallback: assemble from existing chunks
        $chunks = GenerationChunk::where('scenario_generation_id', $generationId)
            ->orderBy('sequence')
            ->pluck('chunk')
            ->toArray();

        $assembled = implode('', $chunks);
        $json = @json_decode($assembled, true);
        if (is_array($json)) {
            return response()->json(['success' => true, 'data' => $json]);
        }

        return response()->json(['success' => true, 'data' => $assembled]);
    }
}
