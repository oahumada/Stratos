<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromptInstructionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure immutable default instructions exist for Spanish and English.
        $defaults = [
            'es' => null,
            'en' => null,
        ];

        // Attempt to load from resource files if present
        foreach ($defaults as $lang => $_) {
            $path = resource_path('prompt_instructions/default_' . $lang . '.md');
            if (file_exists($path)) {
                $defaults[$lang] = file_get_contents($path);
            }
        }

        // Fallback hardcoded if files missing
        if (empty($defaults['es'])) {
            $defaults['es'] = "# Instrucción por defecto (Sistema)\n\nGenera un escenario estratégico a partir de los datos proporcionados.\n\n- Respeta el alcance organizacional y no expongas datos de otras organizaciones.\n- Mantén el resultado en español si el parámetro `language` es `es`, en inglés si es `en`.\n- Provee un resumen ejecutivo corto, objetivos estratégicos, iniciativas recomendadas y un plan de hitos.\n- Si hay incertidumbre, indica supuestos y fuentes de datos sugeridas.\n\nAdemás, incluye una sección detallada de: capacidades, competencias, skills y roles.\nPara cada elemento proporciona una breve descripción y ejemplos de cómo se relaciona\ncon las iniciativas recomendadas.\n\nFormato: Markdown.\n";
        }
        if (empty($defaults['en'])) {
            $defaults['en'] = "# Default Instruction (System)\n\nGenerate a strategic scenario from the provided data.\n\n- Respect organizational scope and do not expose other organizations' data.\n- Produce output in English if `language` is `en`, in Spanish if `language` is `es`.\n- Provide a short executive summary, strategic objectives, recommended initiatives and a milestones plan.\n- If uncertain, state assumptions and suggested data sources.\n\nAlso include a detailed section listing capabilities, competencies, skills and roles.\nFor each item provide a short description and examples of how it maps to the recommended initiatives.\n\nFormat: Markdown.\n";
        }

        foreach ($defaults as $lang => $content) {
            DB::table('prompt_instructions')->updateOrInsert(
                ['language' => $lang, 'editable' => false, 'author_name' => 'system:default'],
                ['content' => $content, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
