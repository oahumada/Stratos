<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidence;
use App\Models\DevelopmentAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    /**
     * Lista evidencias de una acción.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'development_action_id' => ['required', 'integer', 'exists:development_actions,id'],
        ]);

        $evidences = Evidence::where('development_action_id', $request->development_action_id)
            ->latest()
            ->get();

        return response()->json(['data' => $evidences]);
    }

    /**
     * Sube una nueva evidencia.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'development_action_id' => ['required', 'integer', 'exists:development_actions,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'in:file,link,text'],
            'file' => ['nullable', 'file', 'max:10240'], // Max 10MB
            'external_url' => ['nullable', 'url'],
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('evidences', 'public');
            $data['file_path'] = $path;
        }

        $evidence = Evidence::create($data);

        return response()->json([
            'message' => 'Evidencia guardada correctamente',
            'data' => $evidence
        ], 201);
    }

    /**
     * Elimina una evidencia.
     */
    public function destroy($id): JsonResponse
    {
        $evidence = Evidence::findOrFail($id);

        if ($evidence->file_path) {
            Storage::disk('public')->delete($evidence->file_path);
        }

        $evidence->delete();

        return response()->json(['message' => 'Evidencia eliminada']);
    }
}
