<?php

namespace App\Http\Controllers;

use App\Models\Competency;
use App\Models\CompetencyVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompetencyVersionController
{
    public function index(Request $request, $competencyId)
    {
        $user = $request->user();
        $competency = Competency::find($competencyId);
        if (! $competency) {
            return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
        }
        if ($competency->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $versions = CompetencyVersion::where('competency_id', $competency->id)->orderBy('created_at', 'desc')->get();

        return response()->json(['success' => true, 'data' => $versions]);
    }

    public function store(Request $request, $competencyId)
    {
        $user = $request->user();
        $competency = Competency::find($competencyId);
        if (! $competency) {
            return response()->json(['success' => false, 'message' => 'Competency not found'], 404);
        }
        if ($competency->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'effective_from' => 'nullable|date',
            'evolution_state' => 'nullable|in:transformed,obsolescent,new_embryo,stable',
            'metadata' => 'nullable|array',
        ]);

        $cv = CompetencyVersion::create([
            'organization_id' => $competency->organization_id,
            'competency_id' => $competency->id,
            'version_group_id' => (string) Str::uuid(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'effective_from' => $validated['effective_from'] ?? null,
            'evolution_state' => $validated['evolution_state'] ?? 'stable',
            'metadata' => $validated['metadata'] ?? null,
            'created_by' => $user->id ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $cv], 201);
    }

    public function show(Request $request, $competencyId, $id)
    {
        $user = $request->user();
        $cv = CompetencyVersion::find($id);
        if (! $cv || $cv->competency_id != $competencyId) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        if ($cv->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return response()->json(['success' => true, 'data' => $cv]);
    }

    public function destroy(Request $request, $competencyId, $id)
    {
        $user = $request->user();
        $cv = CompetencyVersion::find($id);
        if (! $cv || $cv->competency_id != $competencyId) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        if ($cv->organization_id !== ($user->organization_id ?? null)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        $cv->delete();

        return response()->json(['success' => true]);
    }
}
