<?php

namespace Database\Seeders;

use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioRoleSkill;
use App\Services\RoleSkillDerivationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixScenarioDataSeeder extends Seeder
{
    public function run(): void
    {
        $scenarios = Scenario::all();

        foreach ($scenarios as $s) {
            $this->command->info("Procesando Escenario: {$s->id} - {$s->name}");

            // 1. Corregir IDs en scenario_role_competencies
            $mappings = ScenarioRoleCompetency::where('scenario_id', $s->id)->get();
            foreach ($mappings as $m) {
                // Si el role_id NO es un ID de scenario_roles para este escenario
                $sRole = ScenarioRole::where('scenario_id', $s->id)->where('id', $m->role_id)->first();
                
                if (!$sRole) {
                    // Probablemente es un base role_id. Buscar el sRole correspondiente.
                    $correctSRole = ScenarioRole::where('scenario_id', $s->id)->where('role_id', $m->role_id)->first();
                    if ($correctSRole) {
                        $this->command->warn("  Corrigiendo mapping {$m->id}: role_id {$m->role_id} -> {$correctSRole->id}");
                        $m->role_id = $correctSRole->id;
                        $m->save();
                    }
                }
            }

            // 2. Corregir IDs en scenario_role_skills
            $gaps = ScenarioRoleSkill::where('scenario_id', $s->id)->get();
            foreach ($gaps as $g) {
                $sRole = ScenarioRole::where('scenario_id', $s->id)->where('id', $g->role_id)->first();
                if (!$sRole) {
                    $correctSRole = ScenarioRole::where('scenario_id', $s->id)->where('role_id', $g->role_id)->first();
                    if ($correctSRole) {
                        $this->command->warn("  Corrigiendo gap {$g->id}: role_id {$g->role_id} -> {$correctSRole->id}");
                        $g->role_id = $correctSRole->id;
                        $g->save();
                    }
                }
            }

            // 3. Forzar Rederivación para asegurar que todos los skills están presentes
            $this->command->info("  Rederivando skills para escenario...");
            $derivationSvc = app(RoleSkillDerivationService::class);
            $derivationSvc->deriveAllSkillsForScenario($s->id);
        }

        // 4. Poblar con datos aleatorios (usando el seeder previo)
        $this->command->info("Poblando con datos de brechas...");
        $this->call(PopulateSkillGapsSeeder::class);
    }
}
