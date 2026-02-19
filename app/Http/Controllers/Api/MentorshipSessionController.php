<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentorshipSession;
use App\Models\DevelopmentAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MentorshipSessionController extends Controller
{
    /**
     * Lista las sesiones de una acción específica.
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'development_action_id' => ['required', 'integer', 'exists:development_actions,id'],
        ]);

        $sessions = MentorshipSession::where('development_action_id', $data['development_action_id'])
            ->orderBy('session_date', 'desc')
            ->get();

        return response()->json(['data' => $sessions]);
    }

    /**
     * Registra una nueva sesión de mentoría.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'development_action_id' => ['required', 'integer', 'exists:development_actions,id'],
            'session_date' => ['required', 'date'],
            'summary' => ['nullable', 'string'],
            'next_steps' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'in:scheduled,completed,cancelled,no_show'],
        ]);

        $session = MentorshipSession::create($data);

        return response()->json([
            'message' => 'Sesión de mentoría registrada',
            'data' => $session
        ], 201);
    }

    /**
     * Actualiza una sesión existente.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $session = MentorshipSession::findOrFail($id);

        $data = $request->validate([
            'session_date' => ['nullable', 'date'],
            'summary' => ['nullable', 'string'],
            'next_steps' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'in:scheduled,completed,cancelled,no_show'],
        ]);

        $session->update($data);

        return response()->json([
            'message' => 'Sesión actualizada',
            'data' => $session
        ]);
    }

    /**
     * Elimina una sesión.
     */
    public function destroy($id): JsonResponse
    {
        $session = MentorshipSession::findOrFail($id);
        $session->delete();

        return response()->json(['message' => 'Sesión eliminada']);
    }
}
