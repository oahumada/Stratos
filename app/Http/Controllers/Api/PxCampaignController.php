<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PxCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PxCampaignController extends Controller
{
    /**
     * Listar comandos/campañas PX de la organización.
     */
    public function index(Request $request)
    {
        $orgId = $request->user()->organization_id;

        $campaigns = PxCampaign::where('organization_id', $orgId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $campaigns]);
    }

    /**
     * Configurar una nueva campaña PX o Centinela.
     */
    public function store(Request $request)
    {
        $orgId = $request->user()->organization_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mode' => 'required|in:specific_date,recurring,agent_autonomous',
            'schedule_config' => 'required|array',
            'scope' => 'required|array',
            // Example: "scope.type": "randomized_sample", "scope.target_pct": 20
            'scope.type' => 'required|in:all,department,randomized_sample',
            // Topics: 'clima', 'health', 'stress', 'burnout', 'happiness'
            'topics' => 'required|array|min:1',
            'status' => 'nullable|in:draft,scheduled,active,paused,completed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['organization_id'] = $orgId;
        $data['created_by'] = $request->user()->id;
        
        if (!isset($data['status'])) {
            $data['status'] = 'draft';
        }

        $campaign = PxCampaign::create($data);

        return response()->json(['data' => $campaign], 201);
    }

    /**
     * Mostrar una campaña específica.
     */
    public function show(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;

        $campaign = PxCampaign::where('organization_id', $orgId)->findOrFail($id);

        return response()->json(['data' => $campaign]);
    }

    /**
     * Actualizar configuración de la campaña.
     */
    public function update(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;
        $campaign = PxCampaign::where('organization_id', $orgId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'mode' => 'in:specific_date,recurring,agent_autonomous',
            'schedule_config' => 'array',
            'scope' => 'array',
            'scope.type' => 'in:all,department,randomized_sample',
            'topics' => 'array|min:1',
            'status' => 'in:draft,scheduled,active,paused,completed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $campaign->update($validator->validated());

        return response()->json(['data' => $campaign]);
    }

    /**
     * Eliminar la campaña PX.
     */
    public function destroy(Request $request, int $id)
    {
        $orgId = $request->user()->organization_id;
        $campaign = PxCampaign::where('organization_id', $orgId)->findOrFail($id);

        if ($campaign->status === 'active') {
            return response()->json(['message' => 'Pause la campaña antes de eliminarla.'], 403);
        }

        $campaign->delete();

        return response()->json(null, 204);
    }
}
