<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Competency\CompetencyCuratorService;
use Illuminate\Http\Request;

class CompetencyCuratorController extends Controller
{
    protected CompetencyCuratorService $curatorService;

    public function __construct(CompetencyCuratorService $curatorService)
    {
        $this->curatorService = $curatorService;
    }

    public function curateCompetency(Request $request, $id)
    {
        $result = $this->curatorService->curateCompetency($id);
        
        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    /**
     * Curar una habilidad: Generar niveles BARS, contenido de aprendizaje e indicadores.
     */
    public function curate(Request $request, $id)
    {
        $result = $this->curatorService->curateSkill($id);
        
        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    /**
     * Generar banco de preguntas para una habilidad.
     */
    public function generateQuestions(Request $request, $id)
    {
        $result = $this->curatorService->generateQuestions($id);
        
        if ($result['status'] === 'success') {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }
}
