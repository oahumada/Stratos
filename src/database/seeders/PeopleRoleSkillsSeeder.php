<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\People;
use App\Models\PeopleRoleSkills;
use Carbon\Carbon;

class PeopleRoleSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Migra datos existentes de people_skills a people_role_skills
     * estableciendo todas las skills como activas (is_active=true)
     * y asignando fecha de expiración de 6 meses
     */
    public function run(): void
    {
        if (!Schema::hasTable('people_skills')) {
            $this->command->warn('Tabla people_skills no existe, se omite migración a people_role_skills.');
            return;
        }

        $this->command->info('Migrando datos de people_skills a people_role_skills...');

        // Obtener todas las personas con sus skills actuales
        $peopleSkills = DB::table('people_skills')
            ->join('people', 'people.id', '=', 'people_skills.people_id')
            ->select(
                'people_skills.people_id',
                'people.role_id',
                'people_skills.skill_id',
                'people_skills.level as current_level',
                'people_skills.last_evaluated_at as evaluated_at',
                'people_skills.evaluated_by'
            )
            ->get();

        $created = 0;
        $skipped = 0;

        foreach ($peopleSkills as $peopleSkill) {
            // Verificar si la persona tiene rol asignado
            if (!$peopleSkill->role_id) {
                $this->command->warn("Persona ID {$peopleSkill->people_id} no tiene rol asignado. Se omite.");
                $skipped++;
                continue;
            }

            // Obtener el nivel requerido por el rol para esta skill
            $roleSkill = DB::table('role_skills')
                ->where('role_id', $peopleSkill->role_id)
                ->where('skill_id', $peopleSkill->skill_id)
                ->first();

            $requiredLevel = $roleSkill ? $roleSkill->required_level : 3; // Default 3 si no está en role_skills

            // Calcular fecha de expiración (6 meses desde evaluación o desde ahora)
            $evaluatedAt = $peopleSkill->evaluated_at
                ? Carbon::parse($peopleSkill->evaluated_at)
                : now();

            $expiresAt = (clone $evaluatedAt)->addMonths(6);

            // Crear registro en people_role_skills
            PeopleRoleSkills::create([
                'people_id' => $peopleSkill->people_id,
                'role_id' => $peopleSkill->role_id,
                'skill_id' => $peopleSkill->skill_id,
                'current_level' => $peopleSkill->current_level,
                'required_level' => $requiredLevel,
                'is_active' => true, // Todas las skills actuales se marcan como activas
                'evaluated_at' => $evaluatedAt,
                'expires_at' => $expiresAt,
                'evaluated_by' => $peopleSkill->evaluated_by,
                'notes' => 'Migrado desde people_skills - skill actual del rol'
            ]);

            $created++;
        }

        $this->command->info("✓ Migración completada:");
        $this->command->info("  • {$created} skills migradas a people_role_skills");
        $this->command->info("  • {$skipped} personas sin rol (omitidas)");
        $this->command->info("  • Todas marcadas como is_active=true");
        $this->command->info("  • Fecha de expiración: 6 meses desde última evaluación");
    }
}
