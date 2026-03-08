<?php

namespace App\Console\Commands;

use App\Models\Organizations;
use App\Services\Intelligence\NudgeOrchestratorService;
use Illuminate\Console\Command;

class ProactiveNudgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stratos:nudge {--org_id= : Ejecutar para una organización específica}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta el orquestador de Nudging Proactivo para generar recomendaciones e intervenciones';

    /**
     * Execute the console command.
     */
    public function handle(NudgeOrchestratorService $orchestrator)
    {
        $orgId = $this->option('org_id');

        $this->info('🚀 Iniciando Stratos Proactive Nudge...');

        $organizations = $orgId 
            ? Organizations::where('id', $orgId)->get() 
            : Organizations::all();

        if ($organizations->isEmpty()) {
            $this->error('No se encontraron organizaciones.');
            return;
        }

        foreach ($organizations as $org) {
            $this->comment("Analizando organización: {$org->name} (ID: {$org->id})");
            
            try {
                $result = $orchestrator->orchestrate($org->id);
                $this->info("✅ Generados {$result['total_nudges_generated']} nudges para {$org->name}.");
            } catch (\Exception $e) {
                $this->error("❌ Error en {$org->name}: " . $e->getMessage());
            }
        }

        $this->info('🎉 Ciclo de Nudging completado.');
    }
}
