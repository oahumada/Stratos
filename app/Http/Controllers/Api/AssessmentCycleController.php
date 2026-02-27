<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssessmentCycleController extends Controller
{
    /**
     * Listar todos los ciclos de evaluación de la organización.
     */
    public function index(Request $request)
    {
        $orgId = $request->user()->organization_id;

        $cycles = AssessmentCycle::where('organization_id', $orgId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $cycles]);
    }

    /**
     * Crear un nuevo ciclo de evaluación configurado.
     */
    public function store(Request $request)
    {
        $orgId = $request->user()->organization_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mode' => 'required|in:specific_date,quarterly,annual,continuous',
            'schedule_config' => 'required|array',
            'scope' => 'required|array',
            'scope.type' => 'required|in:all,department,scenario,hipo',
            'evaluators' => 'required|array',
            'instruments' => 'required|array|min:1',
            'notifications' => 'required|array',
            'status' => 'nullable|in:draft,scheduled,active,completed,cancelled',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['organization_id'] = $orgId;
        $data['created_by'] = $request->user()->id;
        
        // Asignar default 'draft' si no viene status
        if (!isset($data['status'])) {
            $data['status'] = 'draft';
        }

        $cycle = AssessmentCycle::create($data);

        return response()->json(['data' => $cycle], 201);
    }

    /**
     * Mostrar un ciclo específico.
     */
    public function show(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;

        $cycle = AssessmentCycle::where('organization_id', $orgId)->findOrFail($id);

        return response()->json(['data' => $cycle]);
    }

    /**
     * Actualizar configuración de un ciclo.
     */
    public function update(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;
        $cycle = AssessmentCycle::where('organization_id', $orgId)->findOrFail($id);

        // Si ya está completado o cancelado, no debe modificarse su core config.
        if (in_array($cycle->status, ['completed', 'cancelled']) && !$request->has('status')) {
            return response()->json(['message' => 'No se puede editar un ciclo finalizado o cancelado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'mode' => 'in:specific_date,quarterly,annual,continuous',
            'schedule_config' => 'array',
            'scope' => 'array',
            'scope.type' => 'in:all,department,scenario,hipo',
            'evaluators' => 'array',
            'instruments' => 'array|min:1',
            'notifications' => 'array',
            'status' => 'in:draft,scheduled,active,completed,cancelled',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cycle->update($validator->validated());

        return response()->json(['data' => $cycle]);
    }

    /**
     * Eliminar (Cancelar o soft-delete) un ciclo.
     */
    public function destroy(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;
        $cycle = AssessmentCycle::where('organization_id', $orgId)->findOrFail($id);

        if ($cycle->status === 'active') {
            return response()->json(['message' => 'No se puede eliminar un ciclo activo, cancélelo en su lugar.'], 403);
        }

        $cycle->delete();

        return response()->json(null, 204);
    }
}
