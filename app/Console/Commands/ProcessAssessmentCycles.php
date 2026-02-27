<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessAssessmentCycles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:process-cycles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa y activa los ciclos de evaluación programados para hoy.';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\Assessment\AssessmentCycleSchedulerService $service)
    {
        $this->info('Iniciando procesamiento de ciclos de evaluación...');
        
        $service->processCycles();
        
        $this->info('Procesamiento completado.');
    }
}
