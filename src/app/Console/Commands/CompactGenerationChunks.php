<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScenarioGeneration;
use App\Models\GenerationChunk;

class CompactGenerationChunks extends Command
{
    protected $signature = 'generate:compact-chunks {--days=30} {--dry-run}';
    protected $description = 'Compact generation_chunks for completed generations older than N days into scenario_generations.metadata["compacted"] and delete originals';

    public function handle()
    {
        $days = (int) $this->option('days');
        $dry = (bool) $this->option('dry-run');

        $cutoff = now()->subDays($days);
        $gens = ScenarioGeneration::where('status', 'complete')
            ->where('created_at', '<', $cutoff)
            ->get();

        $this->info('Found '.$gens->count().' completed generations older than '.$days.' days');

        foreach ($gens as $gen) {
            $meta = (array) $gen->metadata;
            if (! empty($meta['compacted'])) {
                $this->line('Skipping generation '.$gen->id.' (already compacted)');
                continue;
            }

            $chunks = GenerationChunk::where('scenario_generation_id', $gen->id)->orderBy('sequence')->pluck('chunk')->toArray();
            if (empty($chunks)) {
                $this->line('No chunks for generation '.$gen->id);
                continue;
            }

            $assembled = implode('', $chunks);

            $this->line('Generation '.$gen->id.': chunks='.count($chunks).', bytes='.strlen($assembled));

            if ($dry) continue;

            // store base64 to avoid JSON issues with binary (should be text though)
            $meta['compacted'] = base64_encode($assembled);
            $gen->metadata = $meta;
            $gen->save();

            // delete chunks
            GenerationChunk::where('scenario_generation_id', $gen->id)->delete();
            $this->line('Compacted and deleted chunks for generation '.$gen->id);
        }

        $this->info('Done');
        return 0;
    }
}
