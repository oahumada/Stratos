<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scenario;
use App\Models\Capability;
use App\Services\ScenarioAnalysisService;
use App\Repository\ScenarioRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ScenarioController extends Controller
{
    protected $analytics;

    public function __construct(
        private ScenarioRepository $scenarioRepo,
        private ScenarioAnalysisService $analysisService
    ) {}

    /**
     * Obtiene el IQ y métricas clave del escenario.
     */
    public function getIQ($id)
    {
        $metrics = $this->analytics->calculateScenarioIQ($id);
        return response()->json($metrics);
    }

    /**
     * Obtiene el árbol jerárquico de capacidades para el escenario.
     */
    public function getCapabilityTree($id)
    {
        $scenario = Scenario::with([
            'scenarioCapabilities.capability.competencies.competencySkills.skill'
        ])->findOrFail($id);

        $tree = $scenario->scenarioCapabilities->map(function ($scap) use ($id) {
            $capability = $scap->capability;
            return [
                'id' => $capability->id,
                'name' => $capability->name,
                'strategic_role' => $scap->strategic_role,
                'is_incubating' => $capability->isIncubating(),
                'readiness' => round($this->analytics->calculateCapabilityReadiness($id, $capability->id) * 100, 1),
                'competencies' => $capability->competencies->map(function ($comp) use ($id) {
                    return [
                        'id' => $comp->id,
                        'name' => $comp->name,
                        'readiness' => round($this->analytics->calculateCompetencyReadiness($id, $comp->id) * 100, 1),
                        'skills' => $comp->competencySkills->map(function ($cs) use ($id) {
                            return [
                                'id' => $cs->skill->id,
                                'name' => $cs->skill->name,
                                'weight' => $cs->weight,
                                'is_incubating' => !is_null($cs->skill->discovered_in_scenario_id),
                                'readiness' => round($this->analytics->calculateSkillReadiness($id, $cs->skill->id) * 100, 1)
                            ];
                        })
                    ];
                })
            ];
        });

        return response()->json($tree);
    }

    /**
     * Aprueba el escenario y "gradúa" las capacidades/skills incubadas.
     */
    public function approve($id)
    {
        return DB::transaction(function () use ($id) {
            $scenario = Scenario::findOrFail($id);

            // 1. Graduar Capabilities
            Capability::where('discovered_in_scenario_id', $id)
                ->update(['status' => 'active']);

            // 2. Graduar Skills (asumiendo tabla skills tiene discovered_in_scenario_id)
            DB::table('skills')
                ->where('discovered_in_scenario_id', $id)
                ->update(['maturity_status' => 'established']);

            // 3. Cambiar estado del escenario
            $scenario->update(['status' => 'approved']);

            return response()->json(['message' => 'Escenario aprobado y capacidades graduadas con éxito.']);
        });
    }

    public function show($id)
    {
       $scenario = $this->scenarioRepo->findWithCapabilities($id);
        $health = $this->analysisService->calculateHealth($scenario);

        $capabilities = $scenario->capabilities->map(function($cap) {
            return [
                'id' => $cap->id,
                'name' => $cap->name,
                'level' => 3, // Aquí vendría el cálculo real
                'required' => $cap->pivot->required_level,
                'importance' => $cap->importance,
                'x' => $cap->position_x,
                'y' => $cap->position_y,
            ];
        });

        return inertia('Stratos/ScenarioView', [
            'scenario' => array_merge($scenario->toArray(), $health),
            'capabilities' => $capabilities
        ]);
    }
}
