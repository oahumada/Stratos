<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GenerationRedisBuffer;

class CompactGenerationFromRedis extends Command
{
    protected $signature = 'compact:generation {organization_id} {generation_id} {--delete : Delete Redis key after compacting}';

    protected $description = 'Assemble chunks from Redis for a generation and persist compacted metadata';

    public function handle()
    {
        $org = (int) $this->argument('organization_id');
        $gen = (int) $this->argument('generation_id');
        $delete = (bool) $this->option('delete');

        $svc = new GenerationRedisBuffer();
        $this->info("Assembling generation {$gen} for org {$org}...");
        // assembleAndPersist signature: (orgId, generationId, ?scenarioId = null, bool $deleteAfter = false)
        $res = $svc->assembleAndPersist($org, $gen, null, $delete);
        if (! $res['ok']) {
            $this->error('Failed: ' . ($res['reason'] ?? 'unknown'));
            return 1;
        }

        $this->info('Saved compacted for gen ' . $res['generation_id']);
        $this->line('chunk_count: ' . $res['chunk_count']);
        $this->line('compacted base64 len: ' . $res['compacted_base64_len']);
        $this->line('decoded len: ' . $res['decoded_len']);

        if ($delete) {
            $this->info('Redis key deleted.');
        }

        return 0;
    }
}
