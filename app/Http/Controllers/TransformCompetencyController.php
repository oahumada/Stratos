<?php

namespace App\Http\Controllers;

use App\Models\Competency;
use App\Models\CompetencyVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransformCompetencyController
{
    public function transform(Request $request, $competencyId)
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
            'metadata' => 'nullable|array',
        ]);

        // Create a new version in same version_group if provided, else new group
        $versionGroup = $request->input('version_group_id') ?? (string) Str::uuid();

        $cv = CompetencyVersion::create([
            'organization_id' => $competency->organization_id,
            'competency_id' => $competency->id,
            'version_group_id' => $versionGroup,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'effective_from' => $validated['effective_from'] ?? null,
            'evolution_state' => 'transformed',
            'metadata' => $validated['metadata'] ?? null,
            'created_by' => $user->id ?? null,
        ]);

        $createdSkills = [];
        if ($request->boolean('create_skills_incubated') && isset($validated['metadata']['bars']['skills'])) {
            foreach ($validated['metadata']['bars']['skills'] as $skillData) {
                $skillName = is_array($skillData) ? ($skillData['name'] ?? null) : $skillData;
                if (!empty($skillName)) {
                    $skill = \App\Models\Skill::firstOrCreate([
                        'organization_id' => $competency->organization_id,
                        'name' => $skillName
                    ], [
                        'status' => 'incubation',
                        'category' => 'Skill',
                        'description' => 'Generado por IA en Blueprint de Ingeniería',
                    ]);

                    $competency->skills()->syncWithoutDetaching([$skill->id]);
                    
                    $createdSkills[] = $skill;
                }
            }
        }

        $responseData = $cv->toArray();
        $responseData['created_skills'] = $createdSkills;

        return response()->json(['success' => true, 'data' => $responseData], 201);
    }
}
