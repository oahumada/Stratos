<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scenario;
use App\Services\ImpactReportService;
use App\Services\Intelligence\SmartAlertService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateImpactReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stratos:generate-impact-reports {--scenario= : Escenario a generar (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera reportes de impacto y ROI de forma automática.';

    /**
     * Execute the console command.
     */
    public function handle(ImpactReportService $reportService, SmartAlertService $alertService)
    {
        $this->info('Iniciando generación automática de reportes de impacto...');

        // 1. Organizacional ROI Report
        $roiReport = $reportService->generateOrganizationalRoiReport();
        $fileName = 'roi_report_' . date('Y_m_d_H_i_s') . '.json';
        Storage::disk('local')->put('reports/' . $fileName, json_encode($roiReport, JSON_PRETTY_PRINT));
        $this->info("Reporte ROI generado y guardado en $fileName.");

        // Notificar reporte ROI (usamos organization_id = 1 por defecto si no tenemos contexto de multi-tenant completo)
        $alertService->notify(
            1,
            'Reporte ROI Mensual Disponible',
            'El reporte consolidado de ROI organizacional ha sido generado con éxito.',
            'success',
            'analytics',
            ['url' => '/dashboard', 'label' => 'Ver Dashboard']
        );

        // 2. Reportes por cada Escenario Activo
        $scenarioId = $this->option('scenario');
        $query = Scenario::whereIn('status', ['active', 'published']);
        
        if ($scenarioId) {
            $query->where('id', $scenarioId);
        }

        $scenarios = $query->get();

        foreach ($scenarios as $scenario) {
            try {
                $report = $reportService->generateScenarioImpactReport($scenario->id);
                $hash = Str::random(8); // Generar un hash único
                $scenarioFileName = "scenario_{$scenario->id}_impact_{$hash}.json";
                Storage::disk('local')->put('reports/' . $scenarioFileName, json_encode($report, JSON_PRETTY_PRINT));
                
                $alertService->notify(
                    $scenario->organization_id ?? 1,
                    "Reporte de Impacto: {$scenario->name}",
                    "El análisis de impacto y estrategias 4B para el escenario ha sido actualizado.",
                    'info',
                    'automation',
                    ['url' => "/scenario/{$scenario->id}", 'label' => 'Ver Escenario']
                );

                $this->info("Reporte de Escenario #{$scenario->id} guardado.");
            } catch (\Exception $e) {
                $this->error("Error al generar reporte para el escenario #{$scenario->id}: " . $e->getMessage());
            }
        }

        $this->info('Todos los reportes han sido generados automáticamente.');
        return 0;
    }
}
