<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Talent\RoleDesignerService;
use Illuminate\Http\Request;

class RoleDesignerController extends Controller
{
    protected RoleDesignerService $designerService;

    public function __construct(RoleDesignerService $designerService)
    {
        $this->designerService = $designerService;
    }

    /**
     * Diseñar un rol usando la metodología de Cubo de Roles (AI).
     */
    public function design(Request $request, $id)
    {
        $isScenario = $request->boolean('is_scenario', false);
        $result = $this->designerService->designRole($id, $isScenario);
        
        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    /**
     * Realiza un análisis previo de diseño sin necesidad de un ID.
     */
    public function analyzePreview(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        $result = $this->designerService->analyzePreview(
            $request->input('name'),
            $request->input('description')
        );

        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }
}
