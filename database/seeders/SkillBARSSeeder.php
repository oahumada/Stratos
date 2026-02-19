<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\CompetencyLevelBars;
use App\Models\SkillQuestionBank;

class SkillBARSSeeder extends Seeder
{
    public function run()
    {
        $leadership = Skill::where('name', 'like', '%Leadership%')->first();
        
        if ($leadership) {
            // Niveles BARS para Leadership
            $levels = [
                [
                    'level' => 1,
                    'name' => 'Básico',
                    'description' => 'Coordina tareas simples pero requiere guía constante para motivar al equipo.',
                    'behaviors' => ['Asigna tareas sin explicaciones tácticas', 'Mantiene una comunicación unidireccional']
                ],
                [
                    'level' => 2,
                    'name' => 'Intermedio',
                    'description' => 'Logra los objetivos grupales y empieza a delegar basándose en capacidades.',
                    'behaviors' => ['Escucha sugerencias del equipo', 'Define objetivos claros para la semana']
                ],
                [
                    'level' => 3,
                    'name' => 'Avanzado',
                    'description' => 'Inspira al equipo y gestiona conflictos de manera autónoma.',
                    'behaviors' => ['Da feedback constructivo regularmente', 'Alinea el trabajo del equipo con la visión de la empresa']
                ],
                [
                    'level' => 4,
                    'name' => 'Experto',
                    'description' => 'Desarrolla el potencial de los miembros y es un referente de cultura.',
                    'behaviors' => ['Mentorea a futuros líderes', 'Anticipa conflictos estructurales']
                ],
                [
                    'level' => 5,
                    'name' => 'Maestro',
                    'description' => 'Visionario que transforma la organización a través del liderazgo.',
                    'behaviors' => ['Redefine la cultura de liderazgo en toda la empresa', 'Es un imán para el talento de alto nivel']
                ],
            ];

            foreach ($levels as $l) {
                CompetencyLevelBars::create([
                    'skill_id' => $leadership->id,
                    'level' => $l['level'],
                    'name' => $l['name'],
                    'description' => $l['description'],
                    'key_behaviors' => $l['behaviors']
                ]);
            }

            // Banco de Preguntas para Leadership
            SkillQuestionBank::create([
                'skill_id' => $leadership->id,
                'target_relationship' => 'peer',
                'question' => '¿Con qué frecuencia este colaborador fomenta la colaboración y el espíritu de equipo?'
            ]);
            SkillQuestionBank::create([
                'skill_id' => $leadership->id,
                'target_relationship' => 'subordinate',
                'question' => '¿Cómo evalúas la claridad de las metas y el apoyo recibido para alcanzarlas?'
            ]);
            SkillQuestionBank::create([
                'skill_id' => $leadership->id,
                'target_relationship' => 'boss',
                'question' => '¿En qué medida el colaborador demuestra autonomía y toma de decisiones estratégica?'
            ]);
        }
    }
}
