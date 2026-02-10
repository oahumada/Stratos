<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScenarioGeneration;

class MigrateCompactedFromMetadata extends Command
{
    protected $signature = 'migrate:compacted-from-metadata {--preview : do not write changes, only show what would be migrated}';

    protected $description = 'Move compacted blob from metadata->compacted column for all scenario_generations';

    public function handle()
    {
        $preview = (bool) $this->option('preview');
        $this->info('Scanning scenario_generations for metadata.compacted...');

        $total = 0;
        $migrated = 0;

        ScenarioGeneration::chunkById(200, function ($rows) use (&$total, &$migrated, $preview) {
            foreach ($rows as $g) {
                $total++;
                $meta = is_array($g->metadata) ? $g->metadata : [];
                if (isset($meta['compacted']) && $meta['compacted']) {
                    $migrated++;
                    $this->line("Will migrate generation {$g->id}");
                    if (! $preview) {
                        $g->compacted = $meta['compacted'];
                        unset($meta['compacted']);
                        // ensure chunk_count/compacted_at columns reflect metadata if present
                        if (isset($meta['chunk_count'])) {
                            $g->chunk_count = (int) $meta['chunk_count'];
                        }
                        if (isset($meta['compacted_at'])) {
                            $g->compacted_at = $meta['compacted_at'];
                        }
                        $g->metadata = $meta;
                        $g->save();
                    }
                }
            }
        });

        $this->info("Scanned {$total} generations; found {$migrated} with metadata.compacted.");
        if ($preview) {
            $this->info('Preview mode: no changes were written.');
        }

        return 0;
    }
}
