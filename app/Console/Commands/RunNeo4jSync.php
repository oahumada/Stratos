<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class RunNeo4jSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'neo4j:sync {--via=script : Use "script" or "fastapi"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger Neo4j ETL (local script or FastAPI endpoint)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $via = $this->option('via') ?? 'script';

        if ($via === 'fastapi') {
            $url = config('services.neo4j_etl.url') ?? env('NEO4J_ETL_SERVICE_URL');
            if (! $url) {
                $this->error('NEO4J_ETL_SERVICE_URL not configured (env or services.neo4j_etl.url)');
                return 1;
            }

            $this->info("Triggering FastAPI ETL at {$url}/sync");
            $response = Http::post(rtrim($url, '/') . '/sync');

            if ($response->successful()) {
                $this->info('ETL triggered successfully (FastAPI).');
                return 0;
            }

            $this->error('FastAPI ETL trigger failed: ' . $response->status());
            return 2;
        }

        // Default: run local script using virtualenv
        $this->info('Running local ETL script: python_services/neo4j_etl.py (using venv)');
        $script = base_path('python_services/neo4j_etl.py');
        $pythonPath = base_path('python_services/venv/bin/python3');
        
        $process = new Process([$pythonPath, $script]);
        $process->setTimeout(3600);

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (! $process->isSuccessful()) {
            $this->error('Local ETL failed: ' . $process->getExitCode());
            $this->error($process->getErrorOutput());
            return 2;
        }

        $this->info('Local ETL completed successfully.');
        return 0;
    }
}
