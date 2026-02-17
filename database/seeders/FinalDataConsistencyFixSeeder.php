<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinalDataConsistencyFixSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar que las llaves foráneas apuntan a scenario_roles y no a roles
        $this->command->info("Corrigiendo esquemas de llaves foráneas...");
        
        $tables = ['scenario_role_competencies', 'scenario_role_skills'];
        
        foreach ($tables as $table) {
            try {
                // Drop any constraint pointing to 'roles' table for 'role_id' column
                if (DB::getDriverName() === 'pgsql') {
                    // Buscar el nombre de la constraint que apunta a 'roles'
                    $constraint = DB::selectOne("
                        SELECT conname 
                        FROM pg_constraint con
                        JOIN pg_class rel ON rel.oid = con.conrelid
                        JOIN pg_namespace nsp ON nsp.oid = rel.relnamespace
                        WHERE rel.relname = ? AND confrelid = (SELECT oid FROM pg_class WHERE relname = 'roles')
                    ", [$table]);

                    if ($constraint) {
                        $this->command->warn("  Eliminando constraint obsoleta {$constraint->conname} en {$table}");
                        DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$constraint->conname}");
                    }
                }
            } catch (\Exception $e) {
                $this->command->error("  Error al limpiar constraints en {$table}: " . $e->getMessage());
            }
        }

        // 2. Corregir DATA de role_id (Base Role -> Scenario Role)
        $this->command->info("Corregiendo asociaciones de roles (Base -> Escenario)...");
        
        foreach ($tables as $table) {
            $updated = DB::statement("
                UPDATE {$table}
                SET role_id = sr.id
                FROM scenario_roles sr
                WHERE {$table}.role_id = sr.role_id 
                AND {$table}.scenario_id = sr.scenario_id
                AND NOT EXISTS (SELECT 1 FROM scenario_roles sr2 WHERE sr2.id = {$table}.role_id AND sr2.scenario_id = {$table}.scenario_id)
            ");
            $this->command->info("  Tabla {$table} actualizada.");
        }

        // 3. Re-añadir constraints correctas
        $this->command->info("Restaurando llaves foráneas correctas...");
        foreach ($tables as $table) {
            try {
                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$table}_role_id_scenario_roles_foreign FOREIGN KEY (role_id) REFERENCES scenario_roles(id) ON DELETE CASCADE");
                $this->command->info("  Constraint restaurada en {$table}");
            } catch (\Exception $e) {
                $this->command->warn("  La constraint ya existía o no se pudo crear en {$table}");
            }
        }

        // 4. Forzar Rederivación y Población
        $this->command->info("Rederivando skills para todos los escenarios...");
        $scenarios = \App\Models\Scenario::all();
        $derivationSvc = app(\App\Services\RoleSkillDerivationService::class);
        foreach ($scenarios as $s) {
            $derivationSvc->deriveAllSkillsForScenario($s->id);
        }

        $this->command->info("Poblando con datos de prueba...");
        $this->call(PopulateSkillGapsSeeder::class);
    }
}
