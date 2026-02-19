<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class RunNeo4jSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $via;

    /**
     * Create a new job instance.
     */
    public function __construct(string $via = 'script')
    {
        $this->via = $via;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->via === 'fastapi') {
            $url = config('services.neo4j_etl.url') ?? env('NEO4J_ETL_SERVICE_URL');
            if (! $url) {
                throw new \RuntimeException('NEO4J_ETL_SERVICE_URL not configured');
            }

            $res = Http::post(rtrim($url, '/') . '/sync');
            if (! $res->successful()) {
                throw new \RuntimeException('Remote ETL trigger failed: ' . $res->status());
            }

            return;
        }

        // Local script
        $script = base_path('python_services/neo4j_etl.py');
        $process = new Process(['python3', $script]);
        $process->setTimeout(3600);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('Local ETL failed: ' . $process->getErrorOutput());
        }
    }
}
