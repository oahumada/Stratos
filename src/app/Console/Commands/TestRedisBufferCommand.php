<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;
use App\Services\GenerationRedisBuffer;

class TestRedisBufferCommand extends Command
{
    protected $signature = 'test:redis-buffer {generation_id} {--delete : delete redis keys after compacting}';

    protected $description = 'Populate Redis from DB chunks for a generation and run assemble+persist (useful for manual tests)';

    public function handle()
    {
        $genId = (int) $this->argument('generation_id');
        $delete = (bool) $this->option('delete');

        $generation = ScenarioGeneration::find($genId);
        if (! $generation) {
            $this->error("Generation {$genId} not found.");
            return 1;
        }

        $org = (int) $generation->organization_id;

        $this->info("Loading chunks for generation {$genId} from DB...");
        $chunks = GenerationChunk::where('scenario_generation_id', $genId)->orderBy('sequence')->get();
        if ($chunks->isEmpty()) {
            $this->warn('No chunks found in DB for this generation.');
            return 1;
        }

        $svc = new GenerationRedisBuffer();
        foreach ($chunks as $c) {
            $svc->pushChunk($org, $genId, $c->chunk);
        }

        $this->info('Pushed ' . $svc->getChunkCount($org, $genId) . ' chunks to Redis.');

        $this->info('Assembling and persisting compacted payload...');
        $res = $svc->assembleAndPersist($org, $genId, null, $delete);
        if (! $res['ok']) {
            $this->error('Assemble failed: ' . ($res['reason'] ?? 'unknown'));
            return 1;
        }

        $this->info('Saved compacted for gen ' . $res['generation_id']);
        $this->line('chunk_count: ' . $res['chunk_count']);
        $this->line('compacted base64 len: ' . $res['compacted_base64_len']);
        $this->line('decoded len: ' . $res['decoded_len']);

        return 0;
    }
}
