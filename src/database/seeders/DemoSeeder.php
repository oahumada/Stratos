<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\DevelopmentPath;
use App\Models\JobOpening;
use App\Models\Organizations;
use App\Models\People;
use App\Models\Roles;
use App\Models\Skills;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Este seeder orquesta todos los seeders individuales en el orden correcto.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando DemoSeeder...');
        
        // Limpiar tablas en orden inverso a las foreign keys
        $this->command->info('🧹 Limpiando tablas...');
        
        // SQLite usa PRAGMA en lugar de SET FOREIGN_KEY_CHECKS
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        
        DevelopmentPath::truncate();
        Application::truncate();
        JobOpening::truncate();
        DB::table('people_skills')->truncate();
        DB::table('role_skills')->truncate();
        People::truncate();
        Skills::truncate();
        Roles::truncate();
        User::truncate();
        Organizations::truncate();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
        
        $this->command->info('✅ Tablas limpiadas');
        $this->command->newLine();

        // 1. Organización (debe ser primero)
        $this->command->info('📦 Creando organización...');
        $this->call(OrganizationSeeder::class);

        // 2. Usuario admin
        $this->command->info('👤 Creando usuario admin...');
        $this->call(UserSeeder::class);

        // 3. Skills (antes de Roles para las relaciones)
        $this->command->info('🎯 Creando skills...');
        $this->call(SkillSeeder::class);

        // 4. Roles
        $this->command->info('👔 Creando roles...');
        $this->call(RoleSeeder::class);

        // 5. Relaciones Role-Skill (6 skills por rol)
        $this->command->info('🔗 Asociando skills a roles...');
        $this->call(RoleSkillSeeder::class);

        // 6. People (empleados)
        $this->command->info('👥 Creando people...');
        $this->call(PeopleSeeder::class);

        // 7. Job Openings (vacantes)
        $this->command->info('💼 Creando vacantes...');
        $this->call(JobOpeningSeeder::class);

        // 8. Applications (postulaciones)
        $this->command->info('📝 Creando postulaciones...');
        $this->call(ApplicationSeeder::class);

        // 9. Development Paths (rutas de desarrollo)
        $this->command->info('🛤️  Creando rutas de desarrollo...');
        $this->call(DevelopmentPathSeeder::class);

        $this->command->newLine();
        $this->command->info('✅ Demo seeder completado exitosamente!');
        $this->command->table(
            ['Entidad', 'Cantidad'],
            [
                ['Organizaciones', '1'],
                ['Usuarios', '1'],
                ['Skills', '30'],
                ['Roles', '8'],
                ['Relaciones Role-Skill', '48 (6 por rol)'],
                ['People', '20'],
                ['Vacantes', '5'],
                ['Postulaciones', '10'],
                ['Rutas de desarrollo', '1'],
            ]
        );
    }
}
