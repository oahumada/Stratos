<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ScenarioGenerationService;
use App\Models\User;
use App\Models\Organizations;
use App\Jobs\GenerateScenarioFromLLMJob;
use App\Services\LLMClient;

class CreateAndProcessTestGeneration extends Command
{
    protected $signature = 'debug:create-generation {--payload=} {--file=} {--lang=es}';
    protected $description = 'Create a test scenario generation from payload and process it synchronously.';

    public function handle()
    {
        $payloadOption = $this->option('payload');
        $fileOption = $this->option('file');
        $lang = $this->option('lang') ?: 'es';

        if (empty($payloadOption) && empty($fileOption)) {
            $this->error('Provide --payload or --file with JSON payload.');
            return 1;
        }

        if (!empty($fileOption)) {
            if (!file_exists($fileOption)) {
                $this->error('File not found: '.$fileOption);
                return 2;
            }
            $payloadJson = @file_get_contents($fileOption);
        } else {
            $payloadJson = $payloadOption;
        }

        $data = @json_decode($payloadJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON payload: '.json_last_error_msg());
            return 3;
        }

        $user = User::first();
        $org = Organizations::first();

        $svc = new ScenarioGenerationService();
        $compose = $svc->composePromptWithInstruction($data, $user, $org, $lang, $data['instruction_id'] ?? null);

        $this->line('Composed prompt preview:');
        $this->line(substr($compose['prompt'], 0, 1000));

        // Enqueue and process synchronously: create record and run job.handle
        $generation = $svc->enqueueGeneration($compose['prompt'], $org ? $org->id : 1, $user ? $user->id : null, ['initiator' => $user ? $user->id : null, 'used_instruction' => $compose['instruction']]);

        $this->info('Created generation id: '.$generation->id.' - processing now...');

        $job = new GenerateScenarioFromLLMJob($generation->id);
        try {
            $job->handle(new LLMClient());
            $this->info('Processing finished.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Processing failed: '.$e->getMessage());
            return 4;
        }
    }
}
