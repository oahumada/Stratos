<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use App\Services\Talent\CompetencyMaterializerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompetencyMaterializerController extends Controller
{
    protected CompetencyMaterializerService $materializer;

    public function __construct(CompetencyMaterializerService $materializer)
    {
        $this->materializer = $materializer;
    }

    /**
     * Genera sugerencias de Skills mediante Inteligencia Artificial (Blueprint).
     */
    public function generateBlueprint(Request $request, $id)
    {
        $competency = Competency::findOrFail($id);

        try {
            $blueprint = $this->materializer->generateBlueprint($competency);

            return response()->json([
                'success' => true,
                'blueprint' => $blueprint,
            ]);
        } catch (\Exception $e) {
            Log::error('API Blueprint Error: '.$e->getMessage());

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Persiste el diseño final en la base de datos (Materialización).
     */
    public function materialize(Request $request, $id)
    {
        $competency = Competency::findOrFail($id);
        $blueprintData = $request->input('blueprint');

        if (! $blueprintData) {
            return response()->json(['success' => false, 'message' => 'Blueprint data required'], 400);
        }

        $result = $this->materializer->materialize($competency, $blueprintData);

        if ($result['status'] === 'success') {
            return response()->json(['success' => true, 'message' => $result['message']]);
        }

        return response()->json(['success' => false, 'error' => $result['message']], 500);
    }
}
