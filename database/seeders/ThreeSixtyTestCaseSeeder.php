<?php

namespace Database\Seeders;

use App\Models\AssessmentRequest;
use App\Models\Organizations;
use App\Models\People;
use App\Models\SkillQuestionBank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ThreeSixtyTestCaseSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organizations::first();
        if (!$org) {
            $this->command->error('No organization found. Please run DemoSeeder first.');
            return;
        }

        // 1. Seleccionar Sujeto (El evaluado)
        $subject = People::where('email', 'like', 'carlos%')->first() ?? People::first();
        
        // 2. Seleccionar Evaluadores
        $boss = People::where('id', '!=', $subject->id)->first();
        $peer = People::where('id', '!=', $subject->id)
            ->where('id', '!=', $boss->id)
            ->where('department_id', $subject->department_id)
            ->first() ?? People::where('id', '!=', $subject->id)->where('id', '!=', $boss->id)->first();

        $this->command->info("Configurando caso de prueba 360 para: {$subject->full_name}");
        $this->command->info("Jefe: {$boss->full_name}");
        $this->command->info("Par: {$peer->full_name}");

        // 3. Asegurar que existan preguntas para las skills del sujeto
        $this->command->info("Verificando Banco de Preguntas...");
        foreach ($subject->skills as $skill) {
            // Pregunta para el Jefe (manager)
            SkillQuestionBank::firstOrCreate([
                'skill_id' => $skill->id,
                'target_relationship' => 'manager',
            ], [
                'question' => "¿Cómo evalúa la capacidad de {$subject->first_name} para aplicar {$skill->name} en proyectos críticos?",
                'question_type' => 'bars',
                'is_global' => false
            ]);

            // Pregunta para el Par (peer)
            SkillQuestionBank::firstOrCreate([
                'skill_id' => $skill->id,
                'target_relationship' => 'peer',
            ], [
                'question' => "¿Qué tan dispuesto está {$subject->first_name} a colaborar usando su conocimiento en {$skill->name}?",
                'question_type' => 'bars',
                'is_global' => false
            ]);
        }

        // 4. Crear Solicitudes de Assessment (AssessmentRequests)
        $this->command->info("Creando solicitudes de feedback...");

        // Solicitud para el Jefe
        $bossReq = AssessmentRequest::create([
            'organization_id' => $org->id,
            'subject_id' => $subject->id,
            'evaluator_id' => $boss->id,
            'relationship' => 'manager',
            'status' => 'pending',
            'token' => 'TEST-TOKEN-BOSS-' . Str::random(10)
        ]);

        // Poblar feedback para el Jefe
        foreach ($subject->skills as $skill) {
            $q = SkillQuestionBank::where('skill_id', $skill->id)
                ->where('target_relationship', 'manager')
                ->first();
            
            if ($q) {
                $bossReq->feedback()->create([
                    'skill_id' => $skill->id,
                    'question' => $q->question,
                    'answer' => ''
                ]);
            }
        }

        // Solicitud para el Par
        $peerReq = AssessmentRequest::create([
            'organization_id' => $org->id,
            'subject_id' => $subject->id,
            'evaluator_id' => $peer->id,
            'relationship' => 'peer',
            'status' => 'pending',
            'token' => 'TEST-TOKEN-PEER-' . Str::random(10)
        ]);

        // Poblar feedback para el Par
        foreach ($subject->skills as $skill) {
            $q = SkillQuestionBank::where('skill_id', $skill->id)
                ->where('target_relationship', 'peer')
                ->first();
            
            if ($q) {
                $peerReq->feedback()->create([
                    'skill_id' => $skill->id,
                    'question' => $q->question,
                    'answer' => ''
                ]);
            }
        }

        $this->command->info("--- CASO DE PRUEBA LISTO ---");
        $this->command->info("ID Sujeto: {$subject->id}");
        $this->command->info("Token Jefe: {$bossReq->token}");
        $this->command->info("Token Par: {$peerReq->token}");
    }
}
