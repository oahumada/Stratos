<?php

namespace App\Console\Commands;

use App\Jobs\EmbeddingIndexJob;
use Illuminate\Console\Command;

class ReindexEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stratos:embeddings:reindex
        {resourceType? : Resource type to reindex (people|role|scenario)}
        {--org= : Organization ID to scope the reindex}
        {--delta : Only reindex recently updated records (last 24 hours)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex semantic embeddings into the generic embeddings table, with optional delta/window support.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $resourceType = $this->argument('resourceType');
        $organizationId = $this->option('org') ? (int) $this->option('org') : null;
        $onlyRecent = (bool) $this->option('delta');

        if ($resourceType !== null && ! in_array($resourceType, ['people', 'role', 'scenario'], true)) {
            $this->error('Invalid resourceType. Allowed values: people, role, scenario');

            return 1;
        }

        $this->info('Dispatching EmbeddingIndexJob...');

        EmbeddingIndexJob::dispatch($resourceType, $organizationId, $onlyRecent);

        $this->info('EmbeddingIndexJob dispatched successfully.');

        return 0;
    }
}
