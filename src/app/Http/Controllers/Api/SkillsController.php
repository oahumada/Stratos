<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class SkillsController extends Controller
{
    public function index(): JsonResponse
    {
        $skills = Skill::get()->map(fn($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'category' => $s->category,
        ]);

        return response()->json(['data' => $skills]);
    }

    public function show(int $id): JsonResponse
    {
        $skill = Skill::find($id);
        if (! $skill) {
            return response()->json(['error' => 'Skill no encontrada'], 404);
        }

        return response()->json([
            'id' => $skill->id,
            'name' => $skill->name,
            'category' => $skill->category,
        ]);
    }
}
