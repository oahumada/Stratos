<?php

namespace Database\Seeders;

use App\Models\Scenario;
use App\Models\ScenarioRole;
use App\Models\ScenarioRoleCompetency;
use App\Models\ScenarioRoleSkill;
use App\Models\PeopleRoleSkills;
use App\Models\Competency;
use App\Services\RoleSkillDerivationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PopulateSkillGapsSeeder extends Seeder
{
    public function run(): void
    {
        $derivationSvc = app(RoleSkillDerivationService::class);
        $scenarios = Scenario::all();

        foreach ($scenarios as $scenario) {
            $this->command->info("Poblando datos para escenario: {$scenario->id} - {$scenario->name}");
            
            $sRoles = ScenarioRole::where('scenario_id', $scenario->id)->get();
            $organizationCompetencies = Competency::where('organization_id', $scenario->organization_id)->limit(5)->get();

            if ($organizationCompetencies->isEmpty()) {
                $this->command->warn("  No hay competencias globales para la organización {$scenario->organization_id}. Saltando.");
                continue;
            }

            foreach ($sRoles as $sRole) {
                // 1. Asegurar que el rol tenga al menos 2 competencias mapeadas
                $mappingCount = ScenarioRoleCompetency::where('scenario_id', $scenario->id)
                    ->where('role_id', $sRole->id)
                    ->count();

                if ($mappingCount < 2) {
                    $this->command->info("  Asignando/Actualizando competencias automáticas para rol {$sRole->id}...");
                    foreach ($organizationCompetencies->take(3) as $comp) {
                        ScenarioRoleCompetency::updateOrCreate([
                            'scenario_id' => $scenario->id,
                            'role_id' => $sRole->id,
                            'competency_id' => $comp->id,
                        ], [
                            'required_level' => rand(3, 5),
                            'change_type' => 'transformation',
                            'is_core' => true,
                        ]);
                    }
                }

                // 2. Ejecutar derivación de skills
                $derivationSvc->deriveSkillsFromCompetencies($scenario->id, $sRole->id);
            }

            // 3. Ahora que tenemos registros en scenario_role_skills, poner niveles realistas
            $gaps = ScenarioRoleSkill::where('scenario_id', $scenario->id)->get();
            foreach ($gaps as $gap) {
                $baseRoleId = DB::table('scenario_roles')->where('id', $gap->role_id)->value('role_id');
                
                // Calcular promedio real si hay datos, si no usar random
                $avg = null;
                if ($baseRoleId) {
                    $avg = PeopleRoleSkills::where('role_id', $baseRoleId)
                        ->where('skill_id', $gap->skill_id)
                        ->avg('current_level');
                }

                if ($avg === null || $avg == 0) {
                    $avg = rand(1, 3);
                }

                // Asegurar que haya una brecha visible y niveles válidos
                $gap->current_level = round($avg);
                if ($gap->required_level <= 0) {
                    $gap->required_level = rand(3, 5);
                }
                
                if ($gap->required_level <= $gap->current_level) {
                    $gap->required_level = min($gap->current_level + rand(1, 2), 5);
                }
                
                $gap->save();
            }
        }

        $this->command->info("Se han actualizado un total de " . ScenarioRoleSkill::count() . " entradas en la matriz de brechas.");
    }
}
