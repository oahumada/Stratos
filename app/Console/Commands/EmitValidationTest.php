<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MetadataValidator;

class EmitValidationTest extends Command
{
    protected $signature = 'debug:emit-validation-test {generation_id?}';
    protected $description = 'Emit a metadata validation test entry (writes JSONL and DB record)';

    public function handle()
    {
        $generationId = $this->argument('generation_id') ?: null;
        $validator = new MetadataValidator();

        $meta = [
            'provider' => null,
            'model' => null,
        ];

        $errors = $validator->validate($meta);
        $validator->log($generationId, $meta, $errors);

        $this->info('Metadata validation test emitted; errors: ' . json_encode($errors));
        return 0;
    }
}
